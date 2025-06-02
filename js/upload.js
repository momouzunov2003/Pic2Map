function uploadPhoto() {
    const fileInput = document.getElementById('photoInput');
    const gallerySlug = window.location.pathname.split('/').pop();
    
    fileInput.addEventListener('change', function (event) {
        const files = Array.from(event.target.files);

        formData = new FormData();

        files.forEach(file => {
            formData.append('slug', gallerySlug);
            formData.append('images[]', file);
        });

        fetch(`${APP_ROOT}/upload-image`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data || data.status !== 'done') {
                throw new Error('Upload failed: ' + (data.message || 'Unknown error'));
            }
            const statusMessages = data.results.map(result => ({
                filename: result.file || null,
                status: result.status || 'Unknown',
            }));
            let successCount = 0;
            statusMessages.forEach(message => {
                if (message.status === 'Uploaded') {
                    successCount++;
                }
                if (message.status !== 'Uploaded') {
                    console.error(`Error uploading ${message.filename}: ${message.status}`);
                    showToast(`Failed to upload ${message.filename}: ${message.status}`, 'warning');
                }
            });

            showToast(`${successCount} image(s) uploaded successfully!`, 'success');
        })
        .catch(error => {
            console.error('Error during upload:', error);
        });
    })
}

document.addEventListener('DOMContentLoaded', uploadPhoto);
