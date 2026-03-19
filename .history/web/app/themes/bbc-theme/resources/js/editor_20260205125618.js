import domReady from '@wordpress/dom-ready';

domReady(() => {
  //
});

document.addEventListener('click', function (event) {
  // Prüfen, ob auf den Header oder den Toggle-Button einer Postbox geklickt wurde
  const toggleButton = event.target.closest('.handlediv') || event.target.closest('.hndle');

  if (toggleButton) {
    const currentPostbox = toggleButton.closest('.postbox');

    // Wir warten einen Moment, bis WordPress seine eigenen Klassen-Änderungen abgeschlossen hat
    setTimeout(() => {
      // Wenn die aktuell geklickte Box offen ist (also NICHT die Klasse 'closed' hat)
      if (!currentPostbox.classList.contains('closed')) {

        // Alle anderen Postboxen im gleichen Container finden
        const container = currentPostbox.closest('.meta-box-sortables');
        const allPostboxes = container.querySelectorAll('.postbox');

        allPostboxes.forEach(postbox => {
          // Alle anderen Boxen schließen
          if (postbox !== currentPostbox) {
            postbox.classList.add('closed');
            // Aria-Expanded Attribut für Barrierefreiheit anpassen (optional)
            const btn = postbox.querySelector('.handlediv');
            if (btn) btn.setAttribute('aria-expanded', 'false');
          }
        });
      }
    }, 50); // Kleiner Timeout, damit WordPress-Standard-Events zuerst feuern
  }
});

document.addEventListener('click', function (event) {
  const header = event.target.closest('.handlediv') || event.target.closest('.hndle');
  if (!header) return;

  const currentPostbox = header.closest('.postbox');
  const container = currentPostbox.closest('.meta-box-sortables');

  // Verzögerung, damit wir NACH dem WordPress-Toggle agieren
  setTimeout(() => {
    if (!currentPostbox.classList.contains('closed')) {
      const allPostboxes = container.querySelectorAll('.postbox');

      allPostboxes.forEach(postbox => {
        if (postbox !== currentPostbox) {
          postbox.classList.add('closed');
        }
      });
    }
  }, 10);
});
