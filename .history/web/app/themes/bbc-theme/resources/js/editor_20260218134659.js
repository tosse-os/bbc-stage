import domReady from '@wordpress/dom-ready';

/*
|--------------------------------------------------------------------------
| Collapsible Meta Boxes
|--------------------------------------------------------------------------
| Sorgt dafür, dass beim Öffnen einer Metabox alle anderen automatisch
| geschlossen werden.
|--------------------------------------------------------------------------
*/
function initCollapsibleMetaBoxes() {
console.log('aaa');
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

}

/*
|--------------------------------------------------------------------------
| Media Auto-Title
|--------------------------------------------------------------------------
| Generiert im WordPress-Backend automatisch einen sauberen Titel
| basierend auf dem Dateinamen der ausgewählten Media-Datei.
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Media Auto-Title (mit Force-Log & Ready-Check)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Media Auto-Title (Final Force Version)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Media Auto-Title (WP-Core Level Sync)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Media Auto-Title (ACF Native Sync)
|--------------------------------------------------------------------------
*/
function initMediaAutoTitle() {
  console.log('🚀 Media Auto-Title: ACF-Feld Überwachung aktiv.');

  if (typeof acf === 'undefined') return;

  // Wir nutzen den internen ACF-Hook, der feuert, wenn ein Feld aktualisiert wird
  acf.addAction('change', function ($el) {

    // Wir prüfen, ob es unser Audio-Feld ist
    if ($el.data('name') === 'audio_file') {
      console.log('📡 Audio-Feld Änderung erkannt.');

      // Wir geben ACF 200ms Zeit, die Datei-Daten intern zu verarbeiten
      setTimeout(() => {
        const selection = acf.getSelection($el);

        if (selection && selection.length > 0) {
          const fileData = selection[0];
          console.log('📂 Datei-Daten empfangen:', fileData.filename);

          const $titleField = document.querySelector('#title');

          if ($titleField) {
            const currentVal = $titleField.value.trim();
            // Platzhalter-Check basierend auf deinem Screenshot
            const isPlaceholder = !currentVal || currentVal === 'Auto Draft' || currentVal === 'Titel hinzufügen';

            if (isPlaceholder) {
              let cleanTitle = fileData.filename.split('.').slice(0, -1).join('.')
                .replace(/[-_]/g, ' ')
                .replace(/\b\w/g, l => l.toUpperCase());

              $titleField.value = cleanTitle;
              console.log('✨ Titel gesetzt auf:', cleanTitle);

              // Visueller Fokus-Effekt
              $titleField.focus();
              $titleField.style.backgroundColor = 'rgba(0, 124, 186, 0.1)';
              setTimeout(() => $titleField.style.backgroundColor = '', 1000);
            } else {
              console.log('⚠️ Abbruch: Titel-Feld ist nicht leer.');
            }
          }
        } else {
          console.log('❓ Keine Datei-Selection gefunden.');
        }
      }, 200);
    }
  });
}

/*
|--------------------------------------------------------------------------
| Backend Init
|--------------------------------------------------------------------------
| Zentrale Initialisierung aller Backend-Module.
|--------------------------------------------------------------------------
*/
function initBackend() {
  console.log('ccc');
  initCollapsibleMetaBoxes();
  initMediaAutoTitle();

}

domReady(() => {
  console.log('bbb');
  initBackend();
});
