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
 * Initialisiert das AJAX-Kontaktformular
 * und verarbeitet Submit, Fehler und Success-State.
 */

function initContactForm() {
  const form = document.getElementById('contactForm')
  if (!form) return

  form.addEventListener('submit', async e => {
    e.preventDefault()

    const data = new FormData(form)
    data.append('action', 'contact_form_submit')

    const btn = form.querySelector('button')
    btn.disabled = true

    const res = await fetch('/wp-admin/admin-ajax.php', {
      method: 'POST',
      body: data
    })

    const json = await res.json()

    btn.disabled = false

    form.querySelectorAll('input, textarea').forEach(el => {
      el.classList.remove('border-red-400')
    })

    if (!json.success) {
      if (json.data?.field) {
        form.querySelector(`[name="${json.data.field}"]`)?.classList.add('border-red-400')
      }
      return
    }

    form.reset()
    btn.textContent = 'Message sent'
    btn.classList.add('bg-emerald-500')
  })
}

document.addEventListener('DOMContentLoaded', initContactForm)

/**
 * Clientseitige Validierung für das Kontaktformular
 * ohne Änderungen am bestehenden Markup.
 */
function initContactFormValidation() {
  const form = document.getElementById('contactForm')
  if (!form) return

  const lang = document.documentElement.lang === 'de' ? 'de' : 'en'

  const messages = {
    en: {
      email_required: 'Please enter your e-mail address.',
      email_invalid: 'Please enter a valid e-mail address.',
      message_required: 'Please enter a message.'
    },
    de: {
      email_required: 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
      email_invalid: 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
      message_required: 'Bitte geben Sie eine Nachricht ein.'
    }
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

  function t(key) {
    return messages[lang]?.[key] || messages.en[key]
  }

  function getErrorEl(field) {
    let el = field.nextElementSibling
    if (el && el.dataset?.error === field.name) return el

    el = document.createElement('p')
    el.className = 'mt-1 text-sm text-red-500'
    el.dataset.error = field.name
    el.id = `${field.name}-error`

    field.after(el)
    return el
  }

  function showError(field, key) {
    const el = getErrorEl(field)
    el.textContent = t(key)
    el.hidden = false
    field.setAttribute('aria-invalid', 'true')
    field.setAttribute('aria-describedby', el.id)
  }

  function clearError(field) {
    const el = field.nextElementSibling
    if (el?.dataset?.error === field.name) {
      el.textContent = ''
      el.hidden = true
    }
    field.removeAttribute('aria-invalid')
    field.removeAttribute('aria-describedby')
  }

  function validateEmail(field) {
    if (!field.value) {
      showError(field, 'email_required')
      return false
    }

    if (!emailRegex.test(field.value)) {
      showError(field, 'email_invalid')
      return false
    }

    clearError(field)
    return true
  }

  function validateMessage(field) {
    if (!field.value.trim()) {
      showError(field, 'message_required')
      return false
    }

    clearError(field)
    return true
  }

  const email = form.querySelector('[name="email"]')
  const message = form.querySelector('[name="message"]')

  email.addEventListener('input', () => {
    if (email.value) validateEmail(email)
  })

  message.addEventListener('input', () => {
    if (message.value.trim()) clearError(message)
  })

  form.addEventListener('submit', e => {
    e.preventDefault()

    const emailValid = validateEmail(email)
    const messageValid = validateMessage(message)

    if (!emailValid) {
      email.focus()
      return
    }

    if (!messageValid) {
      message.focus()
      return
    }

    form.dispatchEvent(new Event('validSubmit'))
  })
}

document.addEventListener('DOMContentLoaded', initContactFormValidation)

