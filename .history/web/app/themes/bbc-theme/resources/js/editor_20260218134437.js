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
function initMediaAutoTitle() {
  console.log('🚀 Media Auto-Title: Start der Überwachung...');

  // Wir nutzen das globale ACF-Objekt, um auf das Media-Modell zu hören
  if (typeof acf !== 'undefined') {

    // Dieser Hook feuert, sobald das Media-Modal mit einer Auswahl geschlossen wird
    acf.add_filter('select_attachment', function (attachment, field) {
      console.log('📂 Datei ausgewählt:', attachment.filename);
      console.log('🔍 In Feld:', field.data.name);

      // Nur reagieren, wenn es das Audio-Feld ist
      if (field.data.name === 'audio_file') {
        const $titleField = document.querySelector('#title');

        if ($titleField) {
          const currentTitle = $titleField.value.trim();
          const isPlaceholder = !currentTitle ||
            currentTitle === 'Auto Draft' ||
            currentTitle === 'Titel hinzufügen';

          if (isPlaceholder) {
            // Titel-Formatierung (wie gewünscht)
            let cleanTitle = attachment.filename.split('.').slice(0, -1).join('.')
              .replace(/[-_]/g, ' ')
              .replace(/\b\w/g, l => l.toUpperCase());

            $titleField.value = cleanTitle;
            console.log('✨ Titel gesetzt auf:', cleanTitle);

            // Visueller Effekt zur Bestätigung
            $titleField.style.backgroundColor = 'rgba(0, 124, 186, 0.1)';
            setTimeout(() => $titleField.style.backgroundColor = '', 800);
          } else {
            console.log('⚠️ Abbruch: Titel-Feld ist bereits manuell befüllt.');
          }
        }
      }
      return attachment;
    });
  } else {
    console.error('❌ ACF wurde nicht gefunden!');
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
