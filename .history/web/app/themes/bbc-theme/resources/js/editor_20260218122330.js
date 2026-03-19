import domReady from '@wordpress/dom-ready';

domReady(() => {

  document.addEventListener('click', (event) => {

    const toggle = event.target.closest('.handlediv, .hndle');
    if (!toggle) return;

    const currentBox = toggle.closest('.postbox');
    if (!currentBox) return;

    const container = currentBox.closest('.meta-box-sortables');
    if (!container) return;

    requestAnimationFrame(() => {

      if (!currentBox.classList.contains('closed')) {

        container.querySelectorAll('.postbox').forEach((box) => {

          if (box !== currentBox) {

            box.classList.add('closed');

            const btn = box.querySelector('.handlediv');
            if (btn) {
              btn.setAttribute('aria-expanded', 'false');
            }

          }

        });

      }

    });

  });

  /*
  |--------------------------------------------------------------------------
  | Media Auto-Title
  |--------------------------------------------------------------------------
  | Generiert im WordPress-Backend automatisch einen sauberen Titel
  | basierend auf dem Dateinamen der ausgewählten Media-Datei.
  |--------------------------------------------------------------------------
  */

  function initMediaAutoTitle() {
    if (typeof acf === 'undefined') {
      console.warn('Media Auto-Title: ACF ist nicht definiert. Skript wird abgebrochen.');
      return;
    }

    console.log('Media Auto-Title: System bereit und wartet auf Datei-Auswahl...');

    // Wir hören auf das ACF-Event 'select_data'
    acf.addAction('select_data', function (data, $el) {

      // Debug: Welches Feld wurde getriggert?
      const $field = $el.closest('.acf-field');
      const fieldName = $field.data('name');
      console.log('Media Auto-Title: Event "select_data" gefeuert für Feld: ' + fieldName);

      // Wir reagieren nur auf das Audio-Feld (oder Video, falls du es erweiterst)
      if (fieldName !== 'audio_file') {
        console.log('Media Auto-Title: Ignoriere Feld "' + fieldName + '", da es nicht "audio_file" ist.');
        return;
      }

      const $titleField = document.querySelector('#title');
      if (!$titleField) {
        console.error('Media Auto-Title: Titelfeld (#title) wurde im DOM nicht gefunden!');
        return;
      }

      const currentTitle = $titleField.value.trim();
      const isPlaceholder = currentTitle === '' || currentTitle === 'Auto Draft' || currentTitle === 'Titel hinzufügen';

      console.log('Media Auto-Title: Aktueller Titel ist: "' + currentTitle + '" (Platzhalter: ' + isPlaceholder + ')');

      if (isPlaceholder) {
        if (data && data.filename) {
          console.log('Media Auto-Title: Verarbeite Datei: ' + data.filename);

          // Transformation
          let cleanTitle = data.filename.split('.').slice(0, -1).join('.')
            .replace(/[-_]/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase());

          // Titel setzen
          $titleField.value = cleanTitle;
          console.log('Media Auto-Title: Neuer Titel gesetzt -> ' + cleanTitle);

          // Visuelles Feedback
          $titleField.style.backgroundColor = 'rgba(var(--brand-primary-rgb), 0.1)';
          setTimeout(() => $titleField.style.backgroundColor = '', 800);
        } else {
          console.warn('Media Auto-Title: Keine Dateidaten (filename) in der Auswahl gefunden.');
        }
      } else {
        console.log('Media Auto-Title: Abbruch. Das Titelfeld wurde bereits manuell befüllt.');
      }
    });
  }

  /*
  |--------------------------------------------------------------------------
  | Dashboard Init
  |--------------------------------------------------------------------------
  | Zentrale Initialisierung aller Dashboard-Module.
  |--------------------------------------------------------------------------
  */
  function initDashboard() {
    console.log('initDSBackend')
    initMediaAutoTitle();

  }

  document.addEventListener('DOMContentLoaded', initDashboard);


});
