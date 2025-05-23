function uploadPhoto() {
    const fileInput = document.getElementById('photoInput');
    fileInput.addEventListener('change', function (event) {
        const files = Array.from(event.target.files);

        fetch('create-gallery.php')
            .then(response => response.json())
            .then(data => {
                const slug = data.slug;
                const formData = new FormData();
                formData.append('slug', slug);

                files.forEach(file => {
                    if (file.size > 5 * 1024 * 1024) {
                        alert(`${file.name} size exceeds 5MB`);
                        return;
                    }

                    if (!["image/tiff", "image/jpeg", "image/png", "image/webp"].includes(file.type)) {
                        alert(`Invalid file type: ${file.name}`);
                        return;
                    }

                    formData.append('images[]', file);
                });

                return fetch('upload-image.php', {
                    method: 'POST',
                    body: formData
                });
            })
            .then(response => response.json())
            .then(data => {
                console.log('Upload successful:', data);
            })
            .catch(error => {
                console.error('Error during upload:', error);
            });
    });
}

document.addEventListener('DOMContentLoaded', uploadPhoto);
