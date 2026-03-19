import.meta.glob([
  '../images/**',
  '../fonts/**',
])

/**
 * Globales Smooth Scroll Utility
 */
function smoothScrollTo(targetY, duration = 700) {
  const startY = window.scrollY
  const diff = targetY - startY
  let startTime = null

  function step(timestamp) {
    if (!startTime) startTime = timestamp
    const time = timestamp - startTime
    const percent = Math.min(time / duration, 1)

    window.scrollTo(0, startY + diff * ease(percent))

    if (percent < 1) requestAnimationFrame(step)
  }

  function ease(t) {
    return t < 0.5
      ? 2 * t * t
      : -1 + (4 - 2 * t) * t
  }

  requestAnimationFrame(step)
}

/**
 * Initialisiert weiches Scrollen mit stabilem Offset pro Zielsektion.
 * Unterstützt data-scroll-offset auf dem Ziel-Element.
 * initSmoothScroll/initScrollToTop
 */
function initSmoothScroll(duration = 700) {
  const isHome = window.location.pathname === '/' || window.location.pathname === ''

  document.querySelectorAll('a[href*="#"]').forEach(link => {
    link.addEventListener('click', e => {
      if (!isHome) return

      const url = new URL(link.href)
      if (url.pathname !== '/' || !url.hash) return

      const id = url.hash.replace('#', '')
      if (!id) return

      const target = document.getElementById(id)
      if (!target) return

      e.preventDefault()

      const header = document.getElementById('siteHeader')
      const headerHeight = header ? header.offsetHeight : 0

      const customOffset = target.dataset.scrollOffset
        ? parseInt(target.dataset.scrollOffset, 10)
        : 0

      const targetY =
        target.getBoundingClientRect().top +
        window.pageYOffset -
        headerHeight -
        customOffset

      smoothScrollTo(targetY, duration)
    })
  })
}

function initScrollToTop() {
  const scrollBtn = document.getElementById('scrollToTopBtn')
  if (!scrollBtn) return

  window.addEventListener('scroll', () => {
    const isVisible = window.scrollY > 200
    scrollBtn.style.opacity = isVisible ? '1' : '0'
    scrollBtn.style.pointerEvents = isVisible ? 'auto' : 'none'
  }, { passive: true })

  scrollBtn.addEventListener('click', () => {
    smoothScrollTo(0, 900)
  })
}

/* mobile menu */
function initMobileMenu() {
  const toggle = document.getElementById('mobileMenuToggle')
  const close = document.getElementById('mobileMenuClose')
  const menu = document.getElementById('mobileMenu')
  const links = menu?.querySelectorAll('.mobile-link')

  if (!toggle || !menu) return

  function openMenu() {
    menu.classList.remove('translate-x-full')
    document.body.classList.add('overflow-hidden')
  }

  function closeMenu() {
    menu.classList.add('translate-x-full')
    document.body.classList.remove('overflow-hidden')
  }

  toggle.addEventListener('click', openMenu)
  close?.addEventListener('click', closeMenu)

  links?.forEach(link => {
    link.addEventListener('click', closeMenu)
  })

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeMenu()
  })
}

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

/**
 * Initialisiert Validierung und AJAX-Submit für das Kontaktformular.
 * Zeigt mehrsprachige Inline-Fehlermeldungen und sendet gültige Daten per AJAX.
 */
function initContactForm() {
  const form = document.getElementById('contactForm')
  if (!form) return

  const successBox = document.getElementById('contactSuccess')
  const lang = document.documentElement.lang?.startsWith('de') ? 'de' : 'en'

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
    if (!el) return
    el.textContent = message
    el.style.maxHeight = '40px'
    el.style.opacity = '1'
    field.setAttribute('aria-invalid', 'true')
  }

  function clearError(field) {
    const el = form.querySelector('.error-' + field.name)
    if (!el) return
    el.style.maxHeight = '0'
    el.style.opacity = '0'
    field.removeAttribute('aria-invalid')

    el.addEventListener('transitionend', () => {
      el.textContent = ''
    }, { once: true })
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

    try {
      const response = await fetch(ajaxUrl, {
        method: 'POST',
        body: data
      })

      if (!response.ok) throw new Error()

      const json = await response.json()

      if (!json.success && json.data?.field) {
        const field = json.data.field === 'email' ? emailField : messageField
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
    } catch {
      console.error('Contact form submission failed')
    }
  })
}

/**
 * Aktiviert Reveal-Animationen für Text- und Medien-Elemente beim Eintritt in den Viewport.
 * Staggering erfolgt lokal pro Eintrittsgruppe, mit einmaliger Zusatzbewegung.
 */
function initRevealOnScroll() {
  const elements = document.querySelectorAll('.reveal-text, .reveal-media');
  if (!elements.length) return;

  const observer = new IntersectionObserver(
    (entries) => {
      // Wir gruppieren die Einträge nach dem Moment des Sichtbarwerdens
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          const el = entry.target;

          // PRIORITÄT 1: Manuelles Delay aus Blade (für exakte Reihenfolge)
          // PRIORITÄT 2: Automatisches lokales Staggering (falls im Blade nichts steht)
          const delay = el.dataset.revealDelay
            ? Number(el.dataset.revealDelay)
            : index * 150; // Index basiert hier auf den aktuell sichtbar werdenden Elementen

          setTimeout(() => {
            el.classList.add('is-visible');
            if (el.classList.contains('reveal-media')) {
              el.classList.add('reveal-float-once');
            }
          }, delay);

          observer.unobserve(el);
        }
      });
    },
    {
      threshold: 0.15,
      rootMargin: '0px 0px -50px 0px' // Startet kurz bevor es ganz da ist
    }
  );

  elements.forEach(el => observer.observe(el));
}

/**
 *
 * @returns Hero image - and other elements - effect / moving
 */
function initHeroFloat() {
  const elements = document.querySelectorAll('.element-float');
  if (!elements.length) return;

  function update() {
    elements.forEach(el => {
      // Holt die Position des Elements relativ zum sichtbaren Fenster
      const rect = el.getBoundingClientRect();
      const windowHeight = window.innerHeight;

      // Prüfen, ob das Element überhaupt im oder nah am Viewport ist
      if (rect.top < windowHeight && rect.bottom > 0) {
        // Berechnet, wie weit das Element durch den Viewport gewandert ist (0 bis 1)
        // 0 = Element kommt gerade unten rein, 1 = Element verschwindet oben
        const distanceTriggered = (windowHeight - rect.top) / (windowHeight + rect.height);

        // Wir erzeugen einen sanften Versatz von z.B. -15px bis +15px
        // Du kannst die 30 (Range) und 15 (Offset) nach Belieben anpassen
        const y = (distanceTriggered * 30) - 15;

        el.style.transform = `translateY(${y}px)`;
      }
    });
  }

  // Initialer Aufruf und Event Listener
  update();
  window.addEventListener('scroll', update, { passive: true });
}

/* classes/colors for navigation links */
function initScrollSpy() {
  const navLinks = Array.from(document.querySelectorAll('.nav-link'))
  if (!navLinks.length) return

  const sections = navLinks
    .map(link => {
      const href = link.getAttribute('href')
      if (!href || !href.includes('#')) return null
      const id = href.split('#')[1]
      if (!id) return null
      return document.getElementById(id)
    })
    .filter(Boolean)

  let ticking = false

  function updateActiveNav() {
    const triggerPoint = window.innerHeight * 0.5
    let activeSection = null

    for (const section of sections) {
      const rect = section.getBoundingClientRect()
      if (rect.top <= triggerPoint && rect.bottom >= triggerPoint) {
        activeSection = section
        break
      }
    }

    navLinks.forEach(link => {
      const href = link.getAttribute('href')
      if (!href || !href.includes('#')) return
      const id = href.split('#')[1]
      const isActive = activeSection && id === activeSection.id
      link.classList.toggle('is-active', isActive)
    })

    ticking = false
  }

  function onScroll() {
    if (!ticking) {
      requestAnimationFrame(updateActiveNav)
      ticking = true
    }
  }

  updateActiveNav()
  window.addEventListener('scroll', onScroll, { passive: true })
}

document.addEventListener('DOMContentLoaded', () => {
  initSmoothScroll(900)
  initScrollToTop()
  initMobileMenu()
  initStickyHeader()
  initContactForm()
  initRevealOnScroll()
  initHeroFloat()
  initScrollSpy()
})
