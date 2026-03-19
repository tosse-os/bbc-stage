document.addEventListener('DOMContentLoaded', () => {
  const html = document.documentElement
  const stored = localStorage.getItem('dashboard-theme')

  if (stored === 'dark' || stored === 'light') {
    html.dataset.theme = stored
  } else {
    html.dataset.theme = 'light'
  }

  document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
    btn.addEventListener('click', () => {
      const current = html.dataset.theme === 'dark' ? 'dark' : 'light'
      const next = current === 'dark' ? 'light' : 'dark'

      html.dataset.theme = next
      localStorage.setItem('dashboard-theme', next)
    })
  })
})
