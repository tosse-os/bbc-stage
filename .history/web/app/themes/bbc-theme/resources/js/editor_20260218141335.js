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

  acf.addAction('ready append', function () {

    console.log('✅ ACF ready/append ausgelöst');

    const field = acf.getField('field_audio_file');

    if (!field) {
      console.warn('⚠️ audio_file Feld nicht gefunden.');
      return;
    }

    console.log('🎯 audio_file Feld erkannt.');

    if (field.$el.data('autoTitleBound')) {
      console.log('ℹ️ Event bereits gebunden.');
      return;
    }

    field.$el.data('autoTitleBound', true);

    field.$el.on('change', 'input[type="hidden"]', function () {

      console.log('📡 Hidden Input change erkannt');

      setTimeout(() => {

        const val = field.val();
        console.log('📦 field.val():', val);

        if (!val || !val.filename) {
          console.warn('⚠️ Keine gültigen Dateidaten vorhanden.');
          return;
        }

        console.log('📂 Datei erkannt:', val.filename);

        const titleField = document.querySelector('#title');

        if (!titleField) {
          console.error('❌ Titelfeld nicht gefunden.');
          return;
        }

        const currentTitle = titleField.value.trim();
        console.log('📝 Aktueller Titel:', currentTitle);

        if (
          currentTitle &&
          currentTitle !== 'Auto Draft' &&
          currentTitle !== 'Titel hinzufügen'
        ) {
          console.log('⏭️ Titel bereits gesetzt. Kein Override.');
          return;
        }

        let cleanTitle = val.filename
          .split('.')
          .slice(0, -1)
          .join('.')
          .replace(/[-_]/g, ' ')
          .replace(/\b\w/g, l => l.toUpperCase());

        console.log('✨ Generierter Titel:', cleanTitle);

        titleField.value = cleanTitle;

        titleField.focus();
        titleField.select();

        console.log('✅ Titel gesetzt & Fokus aktiviert');

      }, 80);

    });

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
  initCollapsibleMetaBoxes();
  initMediaAutoTitle();

}

domReady(() => {
  initBackend();
});
