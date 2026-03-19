import domReady from '@wordpress/dom-ready';

domReady(() => {
  //
});

/**
 * Erzwingt Accordion-Verhalten für ACF-Metaboxen,
 * sodass immer nur ein Block gleichzeitig geöffnet ist.
 */
function initAcfSingleOpen() {
  const boxes = document.querySelectorAll('.acf-postbox')
  if (!boxes.length) return

  boxes.forEach(box => {
    const handle = box.querySelector('.acf-postbox-header')
    if (!handle) return

    handle.addEventListener('click', () => {
      boxes.forEach(other => {
        if (other !== box) {
          other.classList.add('closed')
        }
      })
    })
  })
}

document.addEventListener('DOMContentLoaded', initAcfSingleOpen)
