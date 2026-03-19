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
function initMediaAutoTitle() {
  console.log('🚀 Media Auto-Title: WP-Core Überwachung gestartet.');

  // Wir nutzen das WordPress Core Media Event
  if (typeof wp !== 'undefined' && wp.media) {

    // Wir hören global auf alle Media-Auswahlen
    wp.media.view.Modal.prototype.on('close', function () {
      console.log('📡 Media Modal geschlossen - prüfe Auswahl...');

      // Wir geben WordPress einen Moment Zeit, die Daten zu verarbeiten
      setTimeout(() => {
        const state = wp.media.frame.state();
        const selection = state.get('selection');

        if (selection && selection.length > 0) {
          const attachment = selection.first().toJSON();
          console.log('📂 Datei erkannt:', attachment.filename);

          const $titleField = document.querySelector('#title');

          if ($titleField) {
            const val = $titleField.value.trim();
            // Prüfen auf deine spezifischen Platzhalter laut Screenshot
            if (!val || val === 'Auto Draft' || val === 'Titel hinzufügen') {

              let cleanTitle = attachment.filename.split('.').slice(0, -1).join('.')
                .replace(/[-_]/g, ' ')
                .replace(/\b\w/g, l => l.toUpperCase());

              $titleField.value = cleanTitle;
              console.log('✨ Titel erfolgreich gesetzt auf:', cleanTitle);

              // Visuelles Feedback
              $titleField.style.backgroundColor = 'rgba(0, 124, 186, 0.1)';
              setTimeout(() => $titleField.style.backgroundColor = '', 800);
            } else {
              console.log('⚠️ Abbruch: Feld nicht leer (Inhalt: "' + val + '")');
            }
          }
        }
      }, 100);
    });
  } else {
    console.error('❌ WordPress Media API nicht gefunden!');
  }
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
