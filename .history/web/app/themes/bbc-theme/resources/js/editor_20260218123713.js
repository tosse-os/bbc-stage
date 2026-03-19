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
| Media Auto-Title
|--------------------------------------------------------------------------
| Generiert im WordPress-Backend automatisch einen sauberen Titel
| basierend auf dem Dateinamen der ausgewählten Media-Datei.
|--------------------------------------------------------------------------
*/
function initMediaAutoTitle() {

  if (typeof acf === 'undefined') {
    return;
  }

  acf.addAction('select_data', function (data, $el) {

    const $field = $el.closest('.acf-field');
    if (!$field) return;

    const fieldName = $field.data('name');

    if (fieldName !== 'audio_file') {
      return;
    }

    const $titleField = document.querySelector('#title');
    if (!$titleField) {
      return;
    }

    const currentTitle = $titleField.value.trim();
    const isPlaceholder =
      currentTitle === '' ||
      currentTitle === 'Auto Draft' ||
      currentTitle === 'Titel hinzufügen';

    if (!isPlaceholder) {
      return;
    }

    if (!data || !data.filename) {
      return;
    }

    let cleanTitle = data.filename
      .split('.')
      .slice(0, -1)
      .join('.')
      .replace(/[-_]/g, ' ')
      .replace(/\b\w/g, l => l.toUpperCase());

    $titleField.value = cleanTitle;

    $titleField.style.backgroundColor = 'rgba(var(--brand-primary-rgb), 0.1)';
    setTimeout(() => {
      $titleField.style.backgroundColor = '';
    }, 800);

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
