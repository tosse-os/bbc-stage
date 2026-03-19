document.querySelectorAll('[data-chart-zoom]').forEach(img => {
  img.addEventListener('click', () => {
    const overlay = document.createElement('div')
    overlay.className = 'fixed inset-0 bg-black/90 flex items-center justify-center z-50'
    overlay.innerHTML = `<img src="${img.src}" class="max-h-[90vh] max-w-[90vw]">`
    overlay.addEventListener('click', () => overlay.remove())
    document.body.appendChild(overlay)
  })
})
