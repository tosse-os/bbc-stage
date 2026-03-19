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

      document.documentElement.classList.toggle('sidebar-collapsed', next === 'collapsed')

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
