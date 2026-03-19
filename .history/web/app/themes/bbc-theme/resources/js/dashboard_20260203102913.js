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

  const labels = sidebar.querySelectorAll('[data-sidebar-label]');
  const logo = sidebar.querySelector('[data-sidebar-logo]');

  function apply(collapsed) {
    sidebar.dataset.collapsed = collapsed ? '1' : '0';
    sidebar.classList.toggle('w-72', !collapsed);
    sidebar.classList.toggle('w-20', collapsed);

    labels.forEach(el => el.classList.toggle('hidden', collapsed));

    if (logo) {
      logo.classList.toggle('h-18', !collapsed);
      logo.classList.toggle('h-10', collapsed);
    }

    localStorage.setItem('dashboard_sidebar_collapsed', collapsed ? '1' : '0');
  }

  const saved = localStorage.getItem('dashboard_sidebar_collapsed') === '1';
  apply(saved);

  toggle.addEventListener('click', () => {
    apply(sidebar.dataset.collapsed !== '1');
  });
});
