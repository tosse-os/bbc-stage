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


/**
 * Sidebar-Collapse: steuert Breite, Min-/Max-Width und Layout-Zentrierung.
 * Der Sidebar-Hintergrund wird automatisch korrekt skaliert,
 * da die Breite ausschließlich am <aside> gesetzt wird.
 */

document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('.dashboard-sidebar');
  const toggle = document.querySelector('[data-sidebar-toggle]');
  if (!sidebar || !toggle) return;

  const labels = sidebar.querySelectorAll('[data-sidebar-label]');
  const logo = sidebar.querySelector('[data-sidebar-logo]');

  function apply(collapsed) {
    sidebar.classList.toggle('sidebar-collapsed', collapsed);
    sidebar.classList.toggle('sidebar-expanded', !collapsed);
    labels.forEach(el => el.classList.toggle('hidden', collapsed));
    if (logo) logo.classList.toggle('sidebar-logo-collapsed', collapsed);
    localStorage.setItem('dashboard_sidebar', collapsed ? '1' : '0');
  }

  apply(localStorage.getItem('dashboard_sidebar') === '1');

  toggle.addEventListener('click', () => {
    apply(!sidebar.classList.contains('sidebar-collapsed'));
  });
});




