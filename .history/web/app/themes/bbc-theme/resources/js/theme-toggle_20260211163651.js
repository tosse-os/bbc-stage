document.addEventListener('DOMContentLoaded', () => {

  const html = document.documentElement
  const buttons = document.querySelectorAll('[data-theme-toggle]')

  if (!buttons.length) return

  buttons.forEach(btn => {

    btn.addEventListener('click', async () => {

      const current = html.dataset.theme === 'dark' ? 'dark' : 'light'
      const next = current === 'dark' ? 'light' : 'dark'

      html.dataset.theme = next

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
