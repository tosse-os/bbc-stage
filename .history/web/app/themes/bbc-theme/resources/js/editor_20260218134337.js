import domReady from '@wordpress/dom-ready';

/*
|--------------------------------------------------------------------------
| Collapsible Meta Boxes
|--------------------------------------------------------------------------
| Sorgt dafür, dass beim Öffnen einer Metabox alle anderen automatisch
| geschlossen werden.
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
function initMediaAutoTitle() {
  // Dieser Log MUSS erscheinen, sobald die Seite lädt
  console.log('🚀 Media Auto-Title Modul geladen');

  // Wir warten explizit auf das ACF 'ready' Event
  if (typeof acf !== 'undefined') {
    setupAcfListeners();
  } else {
    console.log('⏳ ACF noch nicht bereit, warte auf Initialisierung...');
    $(document).on('acf/setup_fields', function () {
      console.log('✅ ACF Setup erkannt, starte Listener...');
      setupAcfListeners();
    });
  }

  function setupAcfListeners() {
    console.log('🎧 Media Auto-Title: Listener für Datei-Auswahl aktiv.');

    // Falls 'select_data' nicht greift, nutzen wir zusätzlich 'change' als Backup
    acf.addAction('select_data', function (data, $el) {
      console.log('📂 Datei im Modal ausgewählt:', data.filename);
      handleTitleUpdate(data, $el);
    });
  }

  function handleTitleUpdate(data, $el) {
    const $field = $el.closest('.acf-field');
    const fieldName = $field.data('name');

    console.log('🔍 Feld-Check: Erkannt wurde "' + fieldName + '"');

    if (fieldName === 'audio_file') {
      const $titleField = document.querySelector('#title');
      if (!$titleField) return;

      const currentTitle = $titleField.value.trim();
      const isPlaceholder = currentTitle === '' || currentTitle === 'Auto Draft' || currentTitle === 'Titel hinzufügen';

      if (isPlaceholder && data.filename) {
        let cleanTitle = data.filename.split('.').slice(0, -1).join('.')
          .replace(/[-_]/g, ' ')
          .replace(/\b\w/g, l => l.toUpperCase());

        $titleField.value = cleanTitle;
        console.log('✨ Titel gesetzt auf: ' + cleanTitle);

        // Kurzer visueller Effekt
        $titleField.style.backgroundColor = 'rgba(0, 124, 186, 0.1)';
        setTimeout(() => $titleField.style.backgroundColor = '', 800);
      }
    }
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
