document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('open-sidebar');
    const closeBtn = document.querySelector('.close-btn');
    const sidebar = document.getElementById('sidebar-nav');

    if (openBtn && sidebar) {
        openBtn.addEventListener('click', () => {
            sidebar.classList.add('open'); // Usa 'open' come in timer.js
        });
    }

    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('open');
        });
    }
});