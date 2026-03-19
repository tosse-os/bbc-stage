import './dashboard-filters.js';
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
 * Sidebar-Collapse mit synchroner Layout-Anpassung.
 * Setzt den Zustand am Layout, Sidebar-Hintergrund wird physisch schmal,
 * Icons bleiben sichtbar, Texte werden ausgeblendet.
 */

document.addEventListener('DOMContentLoaded', () => {
  const layout = document.querySelector('.dashboard-layout');
  const toggle = document.querySelector('[data-sidebar-toggle]');
  if (!layout || !toggle) return;

  function setCollapsed(collapsed) {
    layout.classList.toggle('is-collapsed', collapsed);
    localStorage.setItem('dashboard_sidebar', collapsed ? '1' : '0');
  }

  setCollapsed(localStorage.getItem('dashboard_sidebar') === '1');

  toggle.addEventListener('click', () => {
    setCollapsed(!layout.classList.contains('is-collapsed'));
  });
});
