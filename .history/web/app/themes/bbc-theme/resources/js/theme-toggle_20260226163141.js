document.addEventListener('DOMContentLoaded', () => {

  const html = document.documentElement
  const buttons = document.querySelectorAll('[data-theme-toggle]')

  if (!buttons.length) return

  buttons.forEach(btn => {

    btn.addEventListener('click', async () => {

      const current = html.dataset.theme === 'dark' ? 'dark' : 'light'
      const next = current === 'dark' ? 'light' : 'dark'

      html.dataset.theme = next

      const mobilePanel = document.querySelector('[data-mobile-menu-panel]')

      if (mobilePanel) {
        if (next === 'dark') {
          mobilePanel.classList.remove('bg-white/10')
          mobilePanel.classList.add('bg-brand-primary/10')
        } else {
          mobilePanel.classList.remove('bg-brand-primary/10')
          mobilePanel.classList.add('bg-white/10')
        }
      }

      const thumb = btn.querySelector('span')
      const label = btn.closest('div')?.querySelector('[data-theme-label]')

      if (next === 'dark') {
        btn.classList.remove('bg-white/20')
        btn.classList.add('bg-brand-primary')
        if (thumb) {
          thumb.classList.remove('left-1')
          thumb.classList.add('left-6')
        }
        if (label) label.textContent = 'Light Mode'
      } else {
        btn.classList.remove('bg-brand-primary')
        btn.classList.add('bg-white/20')
        if (thumb) {
          thumb.classList.remove('left-6')
          thumb.classList.add('left-1')
        }
        if (label) label.textContent = 'Dark Mode'
      }

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
