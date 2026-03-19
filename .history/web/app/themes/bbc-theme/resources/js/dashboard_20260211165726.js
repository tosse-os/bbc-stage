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
  const content = document.querySelector('[data-filter-content]')
  const icon = document.querySelector('[data-filter-icon]')
  const label = document.querySelector('[data-filter-label]')

  if (!toggle || !content) return

  const isMobile = () => window.innerWidth < 768

  function closePanel() {
    content.style.maxHeight = '0px'
    content.style.opacity = '0'
    icon.classList.remove('rotate-180')
    label.textContent = 'anzeigen'
  }

  function openPanel() {
    content.style.maxHeight = content.scrollHeight + 'px'
    content.style.opacity = '1'
    icon.classList.add('rotate-180')
    label.textContent = 'ausblenden'
  }

  content.style.transition = 'max-height 300ms ease, opacity 300ms ease'
  content.style.overflow = 'hidden'

  if (isMobile()) {
    closePanel()
  } else {
    content.style.maxHeight = 'none'
    content.style.opacity = '1'
  }

  toggle.addEventListener('click', () => {
    if (content.style.maxHeight === '0px') {
      openPanel()
    } else {
      closePanel()
    }
  })

  window.addEventListener('resize', () => {
    if (!isMobile()) {
      content.style.maxHeight = 'none'
      content.style.opacity = '1'
    } else {
      closePanel()
    }
  })
})


/**
 * Init Copy Button
 */
function initCopyButtons() {
  const buttons = document.querySelectorAll('.copy-btn')
  if (!buttons.length) return

  buttons.forEach(btn => {
    const tooltip = btn.querySelector('.tooltip')
    const copyIcon = btn.querySelector('[data-icon="copy"]')
    const checkIcon = btn.querySelector('[data-icon="check"]')

    btn.addEventListener('mouseenter', () => {
      tooltip.style.opacity = '1'
    })

    btn.addEventListener('mouseleave', () => {
      tooltip.style.opacity = '0'
    })

    btn.addEventListener('click', () => {
      const value = btn.dataset.copy
      if (!value) return

      navigator.clipboard.writeText(value)

      tooltip.textContent = 'Kopiert!'
      copyIcon.classList.add('hidden')
      checkIcon.classList.remove('hidden')
      btn.classList.add('text-green-600')

      setTimeout(() => {
        tooltip.textContent = 'Kopieren'
        copyIcon.classList.remove('hidden')
        checkIcon.classList.add('hidden')
        btn.classList.remove('text-green-600')
      }, 1000)
    })
  })
}

document.addEventListener('DOMContentLoaded', initCopyButtons)


/**
 * Appearence profile functions
 */

document.addEventListener('DOMContentLoaded', () => {

  const html = document.documentElement

  const themeBtn = document.querySelector('[data-appearance-theme]')
  const sidebarBtn = document.querySelector('[data-appearance-sidebar]')

  if (themeBtn) {

    themeBtn.addEventListener('click', async () => {

      const current = themeBtn.dataset.current === 'dark' ? 'dark' : 'light'
      const next = current === 'dark' ? 'light' : 'dark'

      html.dataset.theme = next
      themeBtn.dataset.current = next
      themeBtn.textContent = next.charAt(0).toUpperCase() + next.slice(1)

      const form = new FormData()
      form.append('action', 'set_dashboard_theme')
      form.append('theme', next)
      form.append('_wpnonce', themeBtn.dataset.nonce)

      await fetch(themeBtn.dataset.ajax, {
        method: 'POST',
        body: form
      })

    })

  }

  if (sidebarBtn) {

    sidebarBtn.addEventListener('click', async () => {

      const isCollapsed = sidebarBtn.dataset.current === 'collapsed'
      const next = isCollapsed ? 'expanded' : 'collapsed'

      sidebarBtn.dataset.current = next
      sidebarBtn.textContent = next === 'collapsed' ? 'Collapsed' : 'Expanded'

      document.documentElement.classList.toggle(
        'sidebar-collapsed',
        next === 'collapsed'
      )

      const form = new FormData()
      form.append('action', 'set_dashboard_sidebar')
      form.append('collapsed', next === 'collapsed' ? '1' : '0')
      form.append('_wpnonce', sidebarBtn.dataset.nonce)

      await fetch(sidebarBtn.dataset.ajax, {
        method: 'POST',
        body: form
      })

    })

  }

})
