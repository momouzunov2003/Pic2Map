function showToast(message, type = 'success') {
    const toastEl = document.getElementById('myToast');
    const toastBody = toastEl.querySelector('.toast-body');
    const toast = new bootstrap.Toast(toastEl, {
        delay: 3000, // 3 seconds
    });

    toastBody.textContent = message;
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-info');
    toastEl.classList.add(`bg-${type}`);
        
    toast.show();
}