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
  const layout = document.querySelector('.dashboard-layout')
  const toggle = document.querySelector('[data-sidebar-toggle]')
  if (!layout || !toggle) return

  const body = document.body
  const ajax = toggle.dataset.ajax
  const nonce = toggle.dataset.nonce

  function setCollapsed(collapsed, persist = false) {
    body.classList.toggle('sidebar-collapsed', collapsed)
    layout.classList.toggle('is-collapsed', collapsed)

    if (persist) {
      const form = new FormData()
      form.append('action', 'set_dashboard_sidebar_state')
      form.append('state', collapsed ? 'collapsed' : 'expanded')
      form.append('_wpnonce', nonce)

      fetch(ajax, { method: 'POST', body: form })
    }
  }

  setCollapsed(body.classList.contains('sidebar-collapsed'), false)

  toggle.addEventListener('click', () => {
    const collapsed = !body.classList.contains('sidebar-collapsed')
    setCollapsed(collapsed, true)
  })
})

/**
 * Avatar dynamic functions
 */

document.addEventListener('DOMContentLoaded', () => {
  const input = document.querySelector('[data-avatar-input]')
  const preview = document.querySelector('[data-avatar-preview]')
  const sidebar = document.querySelector('[data-sidebar-avatar]')
  if (!input || !preview) return

  input.addEventListener('change', async () => {
    if (!input.files.length) return

    const form = new FormData()
    form.append('action', 'dashboard_upload_avatar')
    form.append('avatar', input.files[0])
    form.append('_wpnonce', input.dataset.nonce)

    const res = await fetch(input.dataset.ajax, {
      method: 'POST',
      body: form
    })

    const json = await res.json()
    if (!json.success) return

    preview.src = json.data.url
    if (sidebar) sidebar.src = json.data.url
  })
})


/**
 * mobile customizing filter bar
 *
 */

document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('[data-filter-toggle]')
  const panel = document.querySelector('[data-filter-panel]')
  const icon = document.querySelector('[data-filter-icon]')

  if (!toggle || !panel) return

  toggle.addEventListener('click', () => {
    panel.classList.toggle('hidden')
    icon.classList.toggle('rotate-180')
  })
})
