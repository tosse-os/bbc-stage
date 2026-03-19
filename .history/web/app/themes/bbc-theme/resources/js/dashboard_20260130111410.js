document.querySelectorAll('[data-chart-zoom]').forEach(img => {
  img.addEventListener('click', () => {
    const overlay = document.createElement('div')
    overlay.className = 'fixed inset-0 bg-black/90 flex items-center justify-center z-50'
    overlay.innerHTML = `<img src="${img.src}" class="max-h-[90vh] max-w-[90vw]">`
    overlay.addEventListener('click', () => overlay.remove())
    document.body.appendChild(overlay)
  })
})

/* login */

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.password-toggle').forEach(toggle => {
    const input = toggle.closest('div').querySelector('.password-input')
    toggle.addEventListener('click', () => {
      input.type = input.type === 'password' ? 'text' : 'password'
      toggle.classList.toggle('text-brand-primary')
    })
  })
})
