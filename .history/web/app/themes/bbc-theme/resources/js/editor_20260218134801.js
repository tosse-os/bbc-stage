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
/*
|--------------------------------------------------------------------------
| Media Auto-Title (ACF Direct Link)
|--------------------------------------------------------------------------
*/
function initMediaAutoTitle() {
  console.log('🚀 Media Auto-Title: Aktive Feld-Überwachung gestartet.');

  if (typeof acf === 'undefined') return;

  /**
   * Dieser Hook wird von ACF genau dann gefeuert, wenn ein Anhang (Attachment)
   * ausgewählt und dem Feld zugewiesen wird.
   */
  acf.add_filter('select_attachment', function (attachment, field) {

    // Debug-Log: Welches Feld empfängt gerade Daten?
    console.log('📡 ACF-Datenempfang für Feld:', field.data.name);

    if (field.data.name === 'audio_file') {
      const $titleField = document.querySelector('#title');

      if ($titleField) {
        const currentVal = $titleField.value.trim();

        // Wir prüfen auf die Platzhalter aus deinem Screenshot
        const isPlaceholder = !currentVal ||
          currentVal === 'Auto Draft' ||
          currentVal === 'Titel hinzufügen';

        if (isPlaceholder && attachment.filename) {
          console.log('📂 Verarbeite Datei:', attachment.filename);

          // Titel-Formatierung
          let cleanTitle = attachment.filename.split('.').slice(0, -1).join('.')
            .replace(/[-_]/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase());

          // Den Titel SOFORT in das Feld schreiben
          $titleField.value = cleanTitle;
          console.log('✨ Titel im Editor gesetzt auf:', cleanTitle);

          // Visuelles Feedback (Feld leuchtet kurz auf)
          $titleField.style.transition = 'background-color 0.5s';
          $titleField.style.backgroundColor = 'rgba(0, 124, 186, 0.1)';
          setTimeout(() => $titleField.style.backgroundColor = '', 800);
        }
      }
    }

    return attachment;
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
