function showToast(message, type = 'success', duration = 3000) {
  const container = document.getElementById('toast-container');

  // Generate unique ID
  const toastId = 'toast-' + Date.now() + Math.floor(Math.random() * 1000);

  // Create the toast element
  const toastHtml = document.createElement('div');
  toastHtml.className = `toast align-items-center text-white bg-${type} border-0 mb-2`;
  toastHtml.id = toastId;
  toastHtml.setAttribute('role', 'alert');
  toastHtml.setAttribute('aria-live', 'assertive');
  toastHtml.setAttribute('aria-atomic', 'true');

  toastHtml.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;

  container.appendChild(toastHtml);

  // Defer toast activation to next tick so DOM can catch up
  setTimeout(() => {
    const toast = new bootstrap.Toast(toastHtml, { delay: duration });
    toast.show();

    toastHtml.addEventListener('hidden.bs.toast', () => {
      toastHtml.remove();
    });
  }, 0); // micro-delay ensures styles apply
}
