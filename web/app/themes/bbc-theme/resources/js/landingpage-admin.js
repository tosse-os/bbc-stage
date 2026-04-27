document.addEventListener('DOMContentLoaded', () => {

  const selector = '.acf-postbox.postbox';

  function closeAllInitially() {
    document.querySelectorAll(selector).forEach(box => {
      if (!box.classList.contains('closed')) {
        window.jQuery(box).addClass('closed').find('.inside').slideUp(150);
      }
    });
  }

  requestAnimationFrame(closeAllInitially);

  document.addEventListener('click', (e) => {

    const toggle = e.target.closest('.acf-postbox .handlediv, .acf-postbox .hndle');
    if (!toggle) return;

    const box = toggle.closest('.acf-postbox.postbox');
    if (!box) return;

    setTimeout(() => {

      document.querySelectorAll(selector).forEach(other => {

        if (other !== box && !other.classList.contains('closed')) {
          window.jQuery(other).addClass('closed').find('.inside').slideUp(150);
        }

      });

    }, 10);

  });

});
