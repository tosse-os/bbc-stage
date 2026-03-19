import domReady from '@wordpress/dom-ready';

domReady(() => {
  //
});

/**
 * Erzwingt Accordion-Verhalten für ACF-Metaboxen,
 * sodass immer nur ein Block gleichzeitig geöffnet ist.
 */

document.addEventListener('DOMContentLoaded', () => {
  if (typeof acf === 'undefined') return

  acf.addAction('show_postbox', postbox => {
    document.querySelectorAll('.acf-postbox').forEach(box => {
      if (box !== postbox[0]) {
        box.classList.add('closed')
      }
    })
  })
})
