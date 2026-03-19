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
 * Sidebar-Collapse-Logik
 */
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('[data-sidebar]');
  const toggle = document.querySelector('[data-sidebar-toggle]');
  if (!sidebar || !toggle) return;

  const labels = sidebar.querySelectorAll('[data-sidebar-label]');
  const logoContainer = sidebar.querySelector('[data-sidebar-logo]');

  function applyState(isCollapsed) {
    // Klassen für Breite
    if (isCollapsed) {
      sidebar.classList.replace('w-72', 'w-20');
      labels.forEach(el => {
        el.style.opacity = '0';
        setTimeout(() => el.classList.add('hidden'), 200);
      });
      if (logoContainer) logoContainer.classList.add('opacity-0', 'invisible');
    } else {
      sidebar.classList.replace('w-20', 'w-72');
      labels.forEach(el => {
        el.classList.remove('hidden');
        setTimeout(() => el.style.opacity = '1', 50);
      });
      if (logoContainer) logoContainer.classList.remove('opacity-0', 'invisible');
    }

    sidebar.dataset.collapsed = isCollapsed ? '1' : '0';
    localStorage.setItem('dashboard_sidebar_collapsed', isCollapsed ? '1' : '0');
  }

  // 1. Initialer Check (LocalStorage oder Mobile)
  const isMobile = window.innerWidth < 1024;
  const savedState = localStorage.getItem('dashboard_sidebar_collapsed') === '1';

  applyState(isMobile || savedState);

  // 2. Toggle Click
  toggle.addEventListener('click', () => {
    const currentlyCollapsed = sidebar.dataset.collapsed === '1';
    applyState(!currentlyCollapsed);
  });

  // 3. Optional: Auto-Collapse bei Resize
  window.addEventListener('resize', () => {
    if (window.innerWidth < 1024 && sidebar.dataset.collapsed !== '1') {
      applyState(true);
    }
  });
});
