document.addEventListener('DOMContentLoaded', () => {

  const html = document.documentElement
  const buttons = document.querySelectorAll('[data-theme-toggle]')
  const mobilePanel = document.querySelector('[data-mobile-menu-panel]')

  if (!buttons.length) return

  function syncMobilePanel(theme) {
    if (!mobilePanel) return

    mobilePanel.classList.remove('bg-white/10', 'bg-brand-primary/10')

    if (theme === 'dark') {
      mobilePanel.classList.add('bg-brand-primary/10')
    } else {
      mobilePanel.classList.add('bg-white/10')
    }
  }

  function syncToggleUI(btn, theme) {
    const thumb = btn.querySelector('span')
    const label = btn.closest('div')?.querySelector('[data-theme-label]')

    btn.classList.remove('bg-white/20', 'bg-brand-primary')

    if (theme === 'dark') {
      btn.classList.add('bg-brand-primary')
      if (thumb) {
        thumb.classList.remove('left-1')
        thumb.classList.add('left-6')
      }
      if (label) label.textContent = 'Light Mode'
    } else {
      btn.classList.add('bg-white/20')
      if (thumb) {
        thumb.classList.remove('left-6')
        thumb.classList.add('left-1')
      }
      if (label) label.textContent = 'Dark Mode'
    }
  }

  const initialTheme = html.dataset.theme === 'dark' ? 'dark' : 'light'

  syncMobilePanel(initialTheme)
  buttons.forEach(btn => syncToggleUI(btn, initialTheme))

  buttons.forEach(btn => {

    btn.addEventListener('click', async () => {

      const current = html.dataset.theme === 'dark' ? 'dark' : 'light'
      const next = current === 'dark' ? 'light' : 'dark'

      html.dataset.theme = next

      syncMobilePanel(next)
      buttons.forEach(b => syncToggleUI(b, next))

      const form = new FormData()
      form.append('action', 'set_dashboard_theme')
      form.append('theme', next)
      form.append('_wpnonce', btn.dataset.nonce)

      await fetch(btn.dataset.ajax, {
        method: 'POST',
        body: form
      })

    })

  })

})
