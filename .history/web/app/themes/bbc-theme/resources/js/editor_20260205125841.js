import domReady from '@wordpress/dom-ready';

domReady(() => {
  //
});

document.addEventListener('click', function (event) {
  const toggle = event.target.closest('.handlediv') || event.target.closest('.hndle');
  if (!toggle) return;

  const current = toggle.closest('.postbox');
  const container = current.closest('.meta-box-sortables');

  setTimeout(() => {
    // Nur wenn die geklickte Box jetzt offen ist, schließen wir die anderen
    if (!current.classList.contains('closed')) {
      const siblings = container.querySelectorAll('.postbox');
      siblings.forEach(box => {
        if (box !== current) {
          box.classList.add('closed');
          // Aria-Status für Screenreader korrigieren
          const btn = box.querySelector('.handlediv');
          if (btn) btn.setAttribute('aria-expanded', 'false');
        }
      });
    }
  }, 50);
});
