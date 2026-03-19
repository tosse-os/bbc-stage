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
| Media Auto-Title (ACF Field Change Hook + Logging)
|--------------------------------------------------------------------------
| Überwacht das ACF File-Feld "audio_file" und setzt automatisch
| den Post-Titel basierend auf dem Dateinamen.
|--------------------------------------------------------------------------
*/
function initMediaAutoTitle() {

  console.log('🚀 Media Auto-Title: Initialisierung gestartet');

  if (typeof acf === 'undefined') {
    console.error('❌ ACF nicht gefunden.');
    return;
  }

  // Direkte Überwachung des Media-Upload-Events
  acf.addAction('ready append', function () {

    // Alle ACF-Felder durchsuchen
    acf.getFields().forEach(function (field) {

      // Prüfen ob es das Audio-Feld ist
      if (field.get('key') === 'field_audio_file' || field.get('name') === 'audio_file') {
        console.log('🎯 Audio-Feld gefunden:', field);

        // Mehrere Event-Listener für verschiedene Szenarien
        setupFieldListeners(field);

        // Auch das Input-Element direkt überwachen
        const $input = field.$el.find('input[type="hidden"]');
        setupInputListeners($input, field);

        // ACF-spezifische Events nutzen
        field.on('change', function () {
          console.log('🔄 ACF change Event');
          handleFileUpload(field);
        });
      }
    });
  });

  function setupFieldListeners(field) {
    if (field.$el.data('autoTitleBound')) {
      return;
    }

    field.$el.data('autoTitleBound', true);

    // Auf Änderungen im gesamten Feld reagieren
    field.$el.on('click', '.acf-file-uploader .file-wrap', function () {
      console.log('📁 Datei-Wrapper geklickt');
    });

    console.log('✅ Listener für Feld eingerichtet');
  }

  function setupInputListeners($input, field) {
    // MutationObserver für das Hidden-Input
    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
          console.log('👀 MutationObserver: Wert geändert');
          handleFileUpload(field);
        }
      });
    });

    observer.observe($input[0], {
      attributes: true,
      attributeFilter: ['value']
    });

    // Zusätzlich auf jQuery change Event
    $input.on('change', function () {
      console.log('👂 Input change Event');
      handleFileUpload(field);
    });

    console.log('✅ Input-Listener eingerichtet');
  }

  function handleFileUpload(field) {
    // Kurze Verzögerung für ACF-internes Update
    setTimeout(() => {

      // Verschiedene Wege, um an die Datei-Info zu kommen
      let fileData = null;

      // Methode 1: Über field.val()
      const fieldValue = field.val();
      console.log('📦 field.val():', fieldValue);

      if (fieldValue && (fieldValue.filename || fieldValue.url)) {
        fileData = fieldValue;
      }

      // Methode 2: Über das Hidden-Input
      if (!fileData) {
        const $input = field.$el.find('input[type="hidden"]');
        const inputValue = $input.val();

        if (inputValue && inputValue !== '0') {
          try {
            // Versuche als JSON zu parsen
            if (inputValue.startsWith('{')) {
              fileData = JSON.parse(inputValue);
            } else if (inputValue.match(/^\d+$/)) {
              // Falls nur ID: lade Attachment
              fetchAttachmentData(inputValue, function (data) {
                if (data && data.filename) {
                  processFileData(data, field);
                }
              });
              return;
            }
          } catch (e) {
            console.log('❌ Kein JSON, behandle als ID:', inputValue);
          }
        }
      }

      if (fileData) {
        processFileData(fileData, field);
      } else {
        console.warn('⚠️ Keine Dateidaten gefunden');
      }

    }, 150); // Längere Verzögerung für ACF-Updates
  }

  function fetchAttachmentData(attachmentId, callback) {
    if (wp && wp.media) {
      wp.media.attachment(attachmentId).fetch().then(function (data) {
        callback(data);
      });
    }
  }

  function processFileData(fileData, field) {
    // Dateiname aus verschiedenen Quellen extrahieren
    let filename = fileData.filename ||
      fileData.url ||
      fileData.name ||
      fileData.title;

    if (!filename) {
      console.warn('⚠️ Kein Dateiname gefunden');
      return;
    }

    console.log('📂 Datei erkannt:', filename);

    // Nur den Dateinamen ohne Pfad extrahieren
    if (filename.includes('/')) {
      filename = filename.split('/').pop();
    }

    const titleField = document.getElementById('title');

    if (!titleField) {
      console.error('❌ Titelfeld nicht gefunden');
      return;
    }

    const currentTitle = titleField.value.trim();
    console.log('📝 Aktueller Titel:', currentTitle);

    // Prüfen ob Titel bereits gesetzt
    if (currentTitle &&
      currentTitle !== 'Auto Draft' &&
      currentTitle !== 'Titel hinzufügen' &&
      currentTitle !== '') {
      console.log('⏭️ Titel bereits gesetzt. Kein Override.');
      return;
    }

    // Dateiname bereinigen
    let cleanTitle = filename
      .split('.')
      .slice(0, -1)
      .join('.')
      .replace(/[-_]/g, ' ')
      .replace(/\s+/g, ' ')
      .replace(/\b\w/g, l => l.toUpperCase())
      .trim();

    // Fallback falls keine Extension entfernt wurde
    if (!cleanTitle && filename) {
      cleanTitle = filename
        .replace(/[-_]/g, ' ')
        .replace(/\.[^/.]+$/, '')
        .replace(/\b\w/g, l => l.toUpperCase());
    }

    console.log('✨ Generierter Titel:', cleanTitle);

    if (cleanTitle) {
      titleField.value = cleanTitle;

      // Event auslösen für andere Plugins
      titleField.dispatchEvent(new Event('change', { bubbles: true }));
      titleField.dispatchEvent(new Event('input', { bubbles: true }));

      titleField.focus();
      titleField.select();

      console.log('✅ Titel gesetzt & Fokus aktiviert');
    }
  }
}

// Zusätzlich: WordPress Media Library Events abfangen
jQuery(document).ready(function ($) {
  if (typeof wp !== 'undefined' && wp.media) {
    // Vor dem Einfügen in ACF
    $(document).on('acf/upload', function (e, data) {
      console.log('📤 ACF Upload Event', data);
    });

    // Nachdem Medien eingefügt wurden
    $(document).on('acf/remove', function (e, data) {
      console.log('🗑️ ACF Remove Event', data);
    });
  }
});

// Initialisierung
initMediaAutoTitle();



/*
|--------------------------------------------------------------------------
| Backend Init
|--------------------------------------------------------------------------
| Zentrale Initialisierung aller Backend-Module.
|--------------------------------------------------------------------------
*/
function initBackend() {
  initCollapsibleMetaBoxes();
  initMediaAutoTitle();

}

domReady(() => {
  initBackend();
});
