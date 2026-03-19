import './filters.js';
document.addEventListener('DOMContentLoaded', () => {
  const images = document.querySelectorAll('[data-chart-zoom]');

  images.forEach(img => {
    img.addEventListener('click', () => {
      const overlay = document.createElement('div');
      overlay.className = 'fixed inset-0 bg-black/90 flex items-center justify-center z-50 cursor-zoom-out';

      const clone = img.cloneNode();
      clone.className = 'max-w-full max-h-full rounded-xl';

      overlay.appendChild(clone);
      document.body.appendChild(overlay);

      overlay.addEventListener('click', () => overlay.remove());
      document.addEventListener('keydown', e => {
        if (e.key === 'Escape') overlay.remove();
      }, { once: true });
    });
  });
});

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.password-toggle').forEach(toggle => {
    const input = toggle.closest('div').querySelector('.password-input');
    toggle.addEventListener('click', () => {
      input.type = input.type === 'password' ? 'text' : 'password';
      toggle.classList.toggle('text-brand-primary');
    });
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('[data-sidebar]');
  const toggle = document.querySelector('[data-sidebar-toggle]');

  if (!sidebar || !toggle) return;

  const labels = sidebar.querySelectorAll('.sidebar-label');
  const logo = sidebar.querySelector('.sidebar-logo');

  function setState(collapsed) {
    sidebar.classList.toggle('w-72', !collapsed);
    sidebar.classList.toggle('w-20', collapsed);

    labels.forEach(el => el.classList.toggle('hidden', collapsed));

    logo.classList.toggle('h-10', !collapsed);
    logo.classList.toggle('h-6', collapsed);

    localStorage.setItem('dashboard_sidebar', collapsed ? 'collapsed' : 'open');
  }

  const saved = localStorage.getItem('dashboard_sidebar');
  if (saved === 'collapsed') {
    setState(true);
  }

  toggle.addEventListener('click', () => {
    const collapsed = sidebar.classList.contains('w-72');
    setState(collapsed);
  });
});
