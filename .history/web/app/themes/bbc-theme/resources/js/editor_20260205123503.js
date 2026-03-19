import domReady from '@wordpress/dom-ready';

domReady(() => {
  //
});

/**
 * Erzwingt echtes Accordion-Verhalten für ACF-Postboxen:
 * – beim Laden alle geschlossen
 * – immer nur eine offen
 */
function initAcfAccordion() {
  const boxes = document.querySelectorAll('.acf-postbox')
  if (!boxes.length) return

  boxes.forEach(box => {
    box.classList.add('closed')
  })

  boxes.forEach(box => {
    const header = box.querySelector('.postbox-header')
    if (!header) return

    header.addEventListener('click', e => {
      e.preventDefault()

      const isClosed = box.classList.contains('closed')

      boxes.forEach(other => {
        other.classList.add('closed')
      })

      if (isClosed) {
        box.classList.remove('closed')
      }
    })
  })
}

document.addEventListener('DOMContentLoaded', initAcfAccordion)
