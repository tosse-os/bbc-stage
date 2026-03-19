import domReady from '@wordpress/dom-ready';

domReady(() => {
  //
});

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('advanced-sortables')
  if (!container || typeof postboxes === 'undefined') return

  const boxes = container.querySelectorAll('.postbox')

  // 1. Beim Laden: alles zu
  boxes.forEach(box => {
    if (!box.classList.contains('closed')) {
      postboxes.handle_click(box)
    }
  })

  // 2. Accordion-Verhalten
  boxes.forEach(box => {
    const header = box.querySelector('.postbox-header')
    if (!header) return

    header.addEventListener('click', () => {
      boxes.forEach(other => {
        if (other !== box && !other.classList.contains('closed')) {
          postboxes.handle_click(other)
        }
      })
    })
  })
})
