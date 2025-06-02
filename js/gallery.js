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
    const map = L.map('map2').setView([42.674349403151, 23.330461580825677], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    console.log("Fetched images:", images);

    if (images.length === 0) {
        console.warn("No images found for this gallery.");
        return;
    }

    images.forEach(image => {
        if (image.latitude && image.longitude) {
            const thumbnailIcon = L.divIcon({
                html: `<div class="thumbnail-marker" style="background-image: url('${image.thumbnail_url}')"></div>`,
                iconSize: [60, 60],
                className: ''
            });
            const marker = L.marker([image.latitude, image.longitude], { icon: thumbnailIcon }).addTo(map);
            marker.bindPopup(`
                <strong><a href=${image.url}>Link</a></strong><br>
                <small>Device Brand: ${image.device_maker}</small><br>
                <small>Device Model: ${image.device_model}</small><br>
                <small>Taken at: ${image.taken_at || 'Unknown'}</small><br>
                <small>Uploaded at: ${image.uploaded_at}</small><br>
                <small>Coordinates: ${image.latitude}, ${image.longitude}</small><br>
            `);
        } else {
            console.warn("Skipping image without valid coordinates:", image);
        }
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    const images = await fetchImages();

    initMap(images);
    const galleryList= document.getElementById('gallery-list');
    if (galleryList) {
        images.forEach(image => {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex align-items-start';

            const img = document.createElement('img');
            img.src = image.thumbnail_url;
            img.alt = 'Thumbnail';
            img.className = 'thumbnail me-3';
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

            const filename = document.createElement('h5');
            filename.className = 'mb-1';
            filename.textContent = `ID: ${image.id}`;

            const coordinates = document.createElement('p');
            coordinates.className = 'mb-1';
            coordinates.innerHTML = `<strong>Coordinates:</strong> ${image.latitude || 'N/A'} lat, ${image.longitude || 'N/A'} lon`;

            const takenAt = document.createElement('p');
            takenAt.className = 'mb-1';
            takenAt.innerHTML = `<strong>Taken:</strong> ${image.taken_at}`;

            const deviceInfo = document.createElement('p');
            deviceInfo.className = 'mb-1';
            deviceInfo.innerHTML = `<strong>Device:</strong> ${image.device_maker} ${image.device_model}`;

            const uploadedAt = document.createElement('p');
            uploadedAt.className = 'mb-0';
            uploadedAt.innerHTML = `<strong>Uploaded:</strong> ${image.uploaded_at}`;

            contentDiv.appendChild(filename);
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
});

