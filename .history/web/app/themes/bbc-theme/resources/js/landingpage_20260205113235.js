import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

function initSmoothScroll(duration = 700) {
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', function (e) {
      const id = this.getAttribute('href');
      if (!id || id === '#') return;

      const target = document.querySelector(id);
      if (!target) return;

      e.preventDefault();

      const rectTop = target.getBoundingClientRect().top;
      const absoluteY = window.scrollY + rectTop;

      const styles = window.getComputedStyle(target);
      const smt = parseInt(styles.scrollMarginTop) || 0;

      const targetY = absoluteY - smt;

      smoothScrollTo(targetY, duration);
    });
  });

  function smoothScrollTo(targetY, duration) {
    const startY = window.scrollY;
    const diff = targetY - startY;
    let startTime = null;

    function step(ts) {
      if (!startTime) startTime = ts;

      const time = ts - startTime;
      const percent = Math.min(time / duration, 1);

      window.scrollTo(0, startY + diff * ease(percent));

      if (percent < 1) requestAnimationFrame(step);
    }

    function ease(t) {
      return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
    }

    requestAnimationFrame(step);
  }
}


function initScrollToTop() {
  const scrollBtn = document.getElementById('scrollToTopBtn');
  if (!scrollBtn) return;
  window.addEventListener('scroll', () => {
    const isVisible = window.scrollY > 200;
    scrollBtn.style.opacity = isVisible ? '1' : '0';
    scrollBtn.style.pointerEvents = isVisible ? 'auto' : 'none';
  });
  scrollBtn.addEventListener('click', () => {
    smoothScrollTo(0, 900);
  });
  function smoothScrollTo(targetY, duration) {
    const startY = window.scrollY;
    const diff = targetY - startY;
    let startTime;
    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      const time = timestamp - startTime;
      const percent = Math.min(time / duration, 1);
      window.scrollTo(0, startY + diff * easeInOutQuad(percent));
      if (time < duration) requestAnimationFrame(step);
    }
    function easeInOutQuad(t) {
      return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
    }
    requestAnimationFrame(step);
  }
}

function initApp() {
  initSmoothScroll(900);
  initScrollToTop();
}

document.addEventListener('DOMContentLoaded', initApp);

/* mobile menu */
function initMobileMenu() {
  const toggle = document.getElementById('mobileMenuToggle');
  const close = document.getElementById('mobileMenuClose');
  const menu = document.getElementById('mobileMenu');
  const links = menu?.querySelectorAll('.mobile-link');

  if (!toggle || !menu) return;

  function openMenu() {
    menu.classList.remove('translate-x-full');
    document.body.classList.add('overflow-hidden');
  }

  function closeMenu() {
    menu.classList.add('translate-x-full');
    document.body.classList.remove('overflow-hidden');
  }

  toggle.addEventListener('click', openMenu);
  close?.addEventListener('click', closeMenu);

  links?.forEach(link => {
    link.addEventListener('click', closeMenu);
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeMenu();
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
});





function initStickyHeader() {
  const header = document.getElementById('siteHeader')
  if (!header) return

  function update() {
    if (window.scrollY > 40) {
      header.classList.add('is-scrolled')
    } else {
      header.classList.remove('is-scrolled')
    }
  }

  update()
  window.addEventListener('scroll', update, { passive: true })
}

document.addEventListener('DOMContentLoaded', initStickyHeader)




/**
 * Initialisiert Validierung und AJAX-Submit für das Kontaktformular.
 * Zeigt mehrsprachige Inline-Fehlermeldungen und sendet gültige Daten per AJAX.
 */

function initContactForm() {
  const form = document.getElementById('contactForm')
  if (!form) return
  const successBox = document.getElementById('contactSuccess')
  const lang =
    document.documentElement.lang?.startsWith('de') ? 'de' : 'en'

  const messages = {
    en: {
      email_required: 'Please enter your email address.',
      email_invalid: 'Please enter a valid email address.',
      message_required: 'Please enter a message.',
      message_too_short: 'Your message must contain at least 5 characters.'
    },
    de: {
      email_required: 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
      email_invalid: 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
      message_required: 'Bitte geben Sie eine Nachricht ein.',
      message_too_short: 'Die Nachricht muss mindestens 5 Zeichen enthalten.'
    }
  }

  const t = key => messages[lang][key] || messages.en[key]

  const emailField = form.querySelector('[name="email"]')
  const messageField = form.querySelector('[name="message"]')

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

  function showError(field, message) {
    const el = form.querySelector('.error-' + field.name)
    el.textContent = message
    el.style.maxHeight = '40px'
    el.style.opacity = '1'
    field.setAttribute('aria-invalid', 'true')
  }

  function clearError(field) {
    const el = form.querySelector('.error-' + field.name)

    el.style.maxHeight = '0'
    el.style.opacity = '0'
    field.removeAttribute('aria-invalid')

    el.addEventListener(
      'transitionend',
      () => {
        el.textContent = ''
      },
      { once: true }
    )
  }

  function validateEmail() {
    const value = emailField.value.trim()

    if (!value) {
      showError(emailField, t('email_required'))
      return false
    }

    if (!emailRegex.test(value)) {
      showError(emailField, t('email_invalid'))
      return false
    }

    clearError(emailField)
    return true
  }

  function validateMessage() {
    const value = messageField.value.trim()

    if (!value) {
      showError(messageField, t('message_required'))
      return false
    }

    if (value.length < 5) {
      showError(messageField, t('message_too_short'))
      return false
    }

    clearError(messageField)
    return true
  }

  emailField.addEventListener('input', validateEmail)
  messageField.addEventListener('input', validateMessage)

  form.addEventListener('submit', async e => {
    e.preventDefault()

    const emailValid = validateEmail()
    const messageValid = validateMessage()

    if (!emailValid || !messageValid) return

    const data = new FormData(form)
    data.append('action', 'contact_form_submit')

    const ajaxUrl = form.dataset.ajaxUrl

    const response = await fetch(ajaxUrl, {
      method: 'POST',
      body: data
    })

    const json = await response.json()

    if (!json.success && json.data?.field) {
      const field =
        json.data.field === 'email' ? emailField : messageField
      showError(field, '')
      return
    }

    clearError(emailField)
    clearError(messageField)

    successBox.style.maxHeight = '40px'
    successBox.style.opacity = '1'

    setTimeout(() => {
      successBox.style.maxHeight = '0'
      successBox.style.opacity = '0'
    }, 5000)

    form.reset()
  })
}

document.addEventListener('DOMContentLoaded', initContactForm)


/**
 * Aktiviert Reveal-Animationen für Text- und Medien-Elemente beim Eintritt in den Viewport.
 * Unterstützt automatisches Staggering anhand der DOM-Reihenfolge.
 */
function initRevealOnScroll() {
  const elements = document.querySelectorAll('.reveal-text, .reveal-media')
  if (!elements.length) return

  const observer = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return

        const el = entry.target
        const delay = Number(el.dataset.revealDelay || 0)

        setTimeout(() => {
          el.classList.add('is-visible')
        }, delay)

        observer.unobserve(el)
      })
    },
    {
      threshold: 0.15
    }
  )

  elements.forEach((el, index) => {
    if (!el.dataset.revealDelay) {
      el.dataset.revealDelay = index * 100
    }
    observer.observe(el)
  })
}

document.addEventListener('DOMContentLoaded', initRevealOnScroll)
