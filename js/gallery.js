let map;
let markersLayer;

async function fetchImages() {
    const gallerySlug = window.location.pathname.split('/').pop();

    if (!gallerySlug) {
        console.error('No gallery slug provided in the URL.');
        return;
    }

    result = await fetch(`${APP_ROOT}/fetch-images?slug=${gallerySlug}`);
    if (!result.ok) {
        console.error('Failed to fetch images:', result.statusText);
        return [];
    }
    const data = await result.json();
    if (!data || !Array.isArray(data)) {
        console.error('Invalid data format:', data);
        return [];
    }
    return data.map(image => ({
        id: image.id,
        url: image.url,
        filename: image.url.split('/').pop().split('-').pop(),
        thumbnail_url: image.thumbnail_url,
        latitude: image.latitude || null,
        longitude: image.longitude || null,
        device_maker: image.device_maker || 'Unknown',
        device_model: image.device_model || 'Unknown',
        taken_at: image.taken_at || 'Unknown',
        uploaded_at: image.uploaded_at
    }));
}

async function initMap(images) {
    const validImages = images.filter(img => typeof img.latitude === 'number' && typeof img.longitude === 'number');

    if (!map) {
        map = L.map('map2').setView([42.6743, 23.3304], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    }

    if (markersLayer) {
        markersLayer.clearLayers();
    }

    markersLayer = L.layerGroup().addTo(map);

    if (validImages.length === 0) {
        console.warn("No geotagged images found.");
        return;
    }

    map.fitBounds(L.latLngBounds(validImages.map(img => [img.latitude, img.longitude])), {
        padding: [20, 20],
    });

    validImages.forEach(image => {
        const thumbnailIcon = L.divIcon({
            html: `<div class="thumbnail-marker" style="background-image: url('${image.thumbnail_url}')"></div>`,
            iconSize: [60, 60],
            className: ''
        });

        const marker = L.marker([image.latitude, image.longitude], { icon: thumbnailIcon }).addTo(markersLayer);

        marker.bindPopup(`
            <strong><a href=${image.url}>${image.filename}</a></strong><br>
            <small>Device Brand: ${image.device_maker}</small><br>
            <small>Device Model: ${image.device_model}</small><br>
            <small>Taken at: ${image.taken_at || 'Unknown'}</small><br>
            <small>Uploaded at: ${image.uploaded_at}</small><br>
            <small>Coordinates: ${image.latitude}, ${image.longitude}</small><br>
        `);

        marker.on('click', () => {
            const target = document.getElementById(`image-${image.id}`);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                const listItem = target.closest('.list-group-item');
                listItem?.classList.add('highlight');
                setTimeout(() => listItem?.classList.remove('highlight'), 2000);
            }
        });
    });
}


async function updateGallery() {
    const images = await fetchImages();

    initMap(images);
    const galleryList= document.getElementById('gallery-list');
    if (galleryList) {
        galleryList.innerHTML = '';

        images.forEach(image => {
            console.log(image.filename);
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex gap-3 align-items-start m-1';

            const img = document.createElement('img');
            img.id = `image-${image.id}`;
            img.src = image.thumbnail_url;
            img.alt = 'Thumbnail';
            img.className = 'thumbnail';
            img.onclick = () => {
                const link = document.createElement('a');
                link.href = image.url;
                link.download = `image-${image.id}.jpg`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
            img.style.cursor = 'pointer';

            const contentDiv = document.createElement('div');
            contentDiv.className = 'd-flex flex-column gap-1';
            contentDiv.style.maxWidth = 'calc(100% - 120px)';

            const filename = document.createElement('h5');
            filename.className = 'mb-0 text-truncate w-100';
            filename.textContent = image.filename;
            filename.title = image.filename;

            const coordinates = document.createElement('p');
            coordinates.className = 'mb-0';
            coordinates.innerHTML = `<strong>Coordinates:</strong> ${image.latitude || 'N/A'} lat, ${image.longitude || 'N/A'} lon`;

            const takenAt = document.createElement('p');
            takenAt.className = 'mb-0';
            takenAt.innerHTML = `<strong>Taken:</strong> ${image.taken_at}`;

            const deviceInfo = document.createElement('p');
            deviceInfo.className = 'mb-0';
            deviceInfo.innerHTML = `<strong>Device:</strong> ${image.device_maker} ${image.device_model}`;

            const uploadedAt = document.createElement('p');
            uploadedAt.className = 'mb-0';
            uploadedAt.innerHTML = `<strong>Uploaded:</strong> ${image.uploaded_at}`;

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'btn btn-danger btn-sm';
            deleteBtn.textContent = 'X';
            deleteBtn.onclick = async () => {
                if (confirm('Are you sure you want to delete this image?')) {
                    const response = await fetch(`${APP_ROOT}/delete-photo.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: image.id })
                    });
                    if (response.ok) {
                        const result = await response.json();
                        console.log('Delete result:', result); // <--- Add this line
                        if (result.galleryDeleted) {
                            window.location.href = `${APP_ROOT}/index.php`;
                        } else {
                            updateGallery();
                        }
                    } else {
                        alert('Failed to delete image.');
                    }
                }
            };
            
            const insideDiv = document.createElement('div');
            insideDiv.className = 'd-flex justify-content-between align-items-center gap-1 mb-2';
            insideDiv.appendChild(filename);
            insideDiv.appendChild(deleteBtn);
            
            contentDiv.appendChild(insideDiv);
            contentDiv.appendChild(coordinates);
            contentDiv.appendChild(takenAt);
            contentDiv.appendChild(deviceInfo);
            contentDiv.appendChild(uploadedAt);
          
            listItem.appendChild(img);
            listItem.appendChild(contentDiv);

            galleryList.appendChild(listItem);
        });
    } else {
        console.error('Gallery grid element not found.');
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    updateGallery();
});

