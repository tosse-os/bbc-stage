import.meta.glob([
  '../images/dashboard/**',
  '../images/landingpage/**',
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
  function normalizePath(path) {
    const normalized = path.replace(/\/+$/, '')
    return normalized === '' ? '/' : normalized
  }

  const currentPath = normalizePath(window.location.pathname)

  document.querySelectorAll('a[href*="#"]').forEach(link => {
    link.addEventListener('click', e => {
      const url = new URL(link.href, window.location.href)

      if (url.origin !== window.location.origin || !url.hash) return
      if (normalizePath(url.pathname) !== currentPath) return

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

/**
 * Zeigt die Fehlermeldung eines Formularfelds an.
 * Nutzt den vorhandenen Fehlercontainer ohne DOM-Neuerzeugung.
 */
function contactFormShowFieldError(form, field, message) {
  const box = form.querySelector('.error-' + field.name)
  if (!box) return

  box.textContent = message
  box.style.maxHeight = '60px'
  box.style.opacity = '1'
  field.setAttribute('aria-invalid', 'true')
}

/**
 * Entfernt die Fehlermeldung eines Formularfelds.
 * Löscht den Text erst nach Ende der Transition.
 */
function contactFormClearFieldError(form, field) {
  const box = form.querySelector('.error-' + field.name)
  if (!box) return

  box.style.maxHeight = '0'
  box.style.opacity = '0'
  field.removeAttribute('aria-invalid')

  box.addEventListener('transitionend', () => {
    box.textContent = ''
  }, { once: true })
}

/**
 * Blendet die Erfolgsmeldung des Kontaktformulars ein.
 * Nutzt den bereits vorhandenen Container im Markup.
 */
function contactFormShowSuccess(box) {
  if (!box) return

  box.style.maxHeight = '60px'
  box.style.opacity = '1'
}

/**
 * Blendet die Erfolgsmeldung des Kontaktformulars aus.
 * Der vorhandene Text bleibt dabei unverändert erhalten.
 */
function contactFormHideSuccess(box) {
  if (!box) return

  box.style.maxHeight = '0'
  box.style.opacity = '0'
}

/**
 * Initialisiert Validierung und AJAX-Submit für das Kontaktformular.
 * Verarbeitet serverseitige Formularfehler, Honeypot und Throttling sauber im bestehenden UI.
 */
function initContactForm() {
  const form = document.getElementById('contactForm')
  if (!form) return

  const successBox = document.getElementById('contactSuccess')
  const emailField = form.querySelector('[name="email"]')
  const messageField = form.querySelector('[name="message"]')
  const lang = document.documentElement.lang?.startsWith('de') ? 'de' : 'en'
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  let successTimeout = null

  const messages = {
    en: {
      email_required: 'Please enter your email address.',
      email_invalid: 'Please enter a valid email address.',
      message_required: 'Please enter a message.',
      message_too_short: 'Your message must contain at least 5 characters.',
      too_fast: 'Please wait a moment before sending another message.',
      rate_limited: 'Too many requests. Please try again in a few minutes.',
      request_invalid: 'Your request could not be processed.',
      mail_failed: 'Your message could not be sent. Please try again later.',
      submit_failed: 'Submission failed. Please try again later.'
    },
    de: {
      email_required: 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
      email_invalid: 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
      message_required: 'Bitte geben Sie eine Nachricht ein.',
      message_too_short: 'Die Nachricht muss mindestens 5 Zeichen enthalten.',
      too_fast: 'Bitte warten Sie kurz, bevor Sie eine weitere Nachricht senden.',
      rate_limited: 'Zu viele Anfragen. Bitte versuchen Sie es in einigen Minuten erneut.',
      request_invalid: 'Ihre Anfrage konnte nicht verarbeitet werden.',
      mail_failed: 'Ihre Nachricht konnte nicht gesendet werden. Bitte versuchen Sie es später erneut.',
      submit_failed: 'Der Versand ist fehlgeschlagen. Bitte versuchen Sie es später erneut.'
    }
  }

  const t = key => messages[lang][key] || messages.en[key] || messages.en.submit_failed

  /**
   * Prüft das E-Mail-Feld clientseitig.
   * Nutzt die vorhandene Inline-Fehlerausgabe im Formular.
   */
  function validateEmail() {
    const value = emailField.value.trim()

    if (!value) {
      contactFormShowFieldError(form, emailField, t('email_required'))
      return false
    }

    if (!emailRegex.test(value)) {
      contactFormShowFieldError(form, emailField, t('email_invalid'))
      return false
    }

    contactFormClearFieldError(form, emailField)
    return true
  }

  /**
   * Prüft das Nachrichtenfeld clientseitig.
   * Nutzt die vorhandene Inline-Fehlerausgabe im Formular.
   */
  function validateMessage() {
    const value = messageField.value.trim()

    if (!value) {
      contactFormShowFieldError(form, messageField, t('message_required'))
      return false
    }

    if (value.length < 5) {
      contactFormShowFieldError(form, messageField, t('message_too_short'))
      return false
    }

    contactFormClearFieldError(form, messageField)
    return true
  }

  emailField.addEventListener('input', validateEmail)
  messageField.addEventListener('input', validateMessage)

  form.addEventListener('submit', async e => {
    e.preventDefault()

    if (form.dataset.submitting === '1') return

    clearTimeout(successTimeout)

    const emailValid = validateEmail()
    const messageValid = validateMessage()

    if (!emailValid || !messageValid) return

    form.dataset.submitting = '1'

    const data = new FormData(form)
    data.append('action', 'contact_form_submit')
    data.append('_wpnonce', form.dataset.nonce)

    try {
      const response = await fetch(form.dataset.ajaxUrl, {
        method: 'POST',
        body: data
      })

      const json = await response.json().catch(() => null)

      if (!response.ok || !json?.success) {
        const code = json?.data?.code || 'submit_failed'
        const field = json?.data?.field || ''

        contactFormHideSuccess(successBox)

        if (field === 'email') {
          contactFormShowFieldError(form, emailField, t(code))
          return
        }

        if (field === 'message') {
          contactFormShowFieldError(form, messageField, t(code))
          return
        }

        contactFormShowFieldError(form, messageField, t(code))
        return
      }

      contactFormClearFieldError(form, emailField)
      contactFormClearFieldError(form, messageField)
      form.reset()
      contactFormShowSuccess(successBox)

      successTimeout = window.setTimeout(() => {
        contactFormHideSuccess(successBox)
      }, 5000)
    } catch {
      contactFormHideSuccess(successBox)
      contactFormShowFieldError(form, messageField, t('submit_failed'))
    } finally {
      form.dataset.submitting = '0'
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
      // Prüfen, wo das Element gerade im Fenster steht
      const rect = el.getBoundingClientRect();
      const windowHeight = window.innerHeight;

      // Nur berechnen, wenn das Element im Sichtfeld ist
      if (rect.top < windowHeight && rect.bottom > 0) {
        // Wir berechnen den Fortschritt des Elements durch den Viewport (0 bis 1)
        // 0 = Element kommt unten gerade rein, 1 = Element verschwindet oben
        const scrollPercent = (windowHeight - rect.top) / (windowHeight + rect.height);

        // Sanfter Schwung: von -15px bis +15px
        const movementRange = 30;
        const offset = 15;
        const y = (scrollPercent * movementRange) - offset;

        el.style.transform = `translateY(${-y}px)`;
      }
    });
  }

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

function initReviewsSlider() {
  var sliders = document.querySelectorAll('[data-reviews-slider]');

  if (!sliders.length) {
    return;
  }

  sliders.forEach(function (slider) {
    var viewport = slider.querySelector('.reviews-slider__viewport');
    var track = slider.querySelector('.reviews-slider__track');
    var slides = Array.prototype.slice.call(slider.querySelectorAll('.reviews-slider__slide'));
    var cards = Array.prototype.slice.call(slider.querySelectorAll('.review-card'));
    var previous = slider.querySelector('[data-reviews-prev]');
    var next = slider.querySelector('[data-reviews-next]');
    var dotsContainer = slider.querySelector('[data-reviews-dots]');
    var toggles = Array.prototype.slice.call(slider.querySelectorAll('[data-review-toggle]'));

    if (!viewport || !track || !slides.length) {
      return;
    }

    var index = 0;
    var slideWidth = 0;
    var gap = 0;
    var timer = null;
    var resizeFrame = null;
    var paused = false;
    var autoplay = slider.getAttribute('data-autoplay') === '1';
    var speed = Math.max(parseInt(slider.getAttribute('data-speed') || '6500', 10) || 6500, 2500);
    var perStep = Math.max(parseInt(slider.getAttribute('data-per-step') || '1', 10) || 1, 1);
    var equalHeight = slider.getAttribute('data-equal-height') === '1';
    var dots = [];
    var pages = [];

    var slidesPerView = function () {
      if (window.innerWidth < 768) {
        return 1;
      }

      if (window.innerWidth < 1024) {
        return 2;
      }

      return 3;
    };

    var viewportWidth = function () {
      var style = window.getComputedStyle(viewport);
      var left = parseFloat(style.paddingLeft) || 0;
      var right = parseFloat(style.paddingRight) || 0;

      return viewport.clientWidth - left - right;
    };

    var clearCardHeights = function () {
      cards.forEach(function (card) {
        card.style.height = '';
      });
    };

    var applyEqualHeight = function () {
      clearCardHeights();

      if (!equalHeight) {
        return;
      }

      var height = cards.reduce(function (max, card) {
        return Math.max(max, card.offsetHeight);
      }, 0);

      cards.forEach(function (card) {
        card.style.height = height + 'px';
      });
    };

    var getMaxIndex = function () {
      return Math.max(slides.length - slidesPerView(), 0);
    };

    var buildPages = function () {
      var maxIndex = getMaxIndex();
      var result = [];
      var step = Math.max(perStep, 1);
      var page = 0;

      while (page < maxIndex) {
        result.push(page);
        page += step;
      }

      if (!result.length || result[result.length - 1] !== maxIndex) {
        result.push(maxIndex);
      }

      return result;
    };

    var buildDots = function () {
      if (!dotsContainer) {
        return;
      }

      pages = buildPages();

      while (dotsContainer.firstChild) {
        dotsContainer.removeChild(dotsContainer.firstChild);
      }

      dots = pages.map(function (page, dotIndex) {
        var dot = document.createElement('button');

        dot.type = 'button';
        dot.className = 'reviews-slider__dot';
        dot.setAttribute('aria-label', 'Review slide ' + (dotIndex + 1));
        dot.addEventListener('click', function () {
          index = page;
          update();
          start();
        });

        dotsContainer.appendChild(dot);

        return dot;
      });
    };

    var updateVisibleSlides = function () {
      var visible = slidesPerView();

      slides.forEach(function (slide, slideIndex) {
        slide.classList.toggle('is-visible', slideIndex >= index && slideIndex < index + visible);
      });
    };

    var updateDots = function () {
      if (!dots.length) {
        return;
      }

      var activePage = pages.reduce(function (closest, page) {
        return Math.abs(page - index) < Math.abs(closest - index) ? page : closest;
      }, pages[0]);

      dots.forEach(function (dot, dotIndex) {
        var isActive = pages[dotIndex] === activePage;

        dot.classList.toggle('is-active', isActive);
        dot.setAttribute('aria-current', isActive ? 'true' : 'false');
      });
    };

    var updateButtons = function () {
      var disabled = slides.length <= slidesPerView();

      if (previous) {
        previous.disabled = disabled;
      }

      if (next) {
        next.disabled = disabled;
      }
    };

    var update = function () {
      updateVisibleSlides();
      updateDots();
      track.style.transform = 'translateX(-' + (index * (slideWidth + gap)) + 'px)';
      updateButtons();
    };

    var layout = function () {
      var visible = slidesPerView();
      var trackStyle = window.getComputedStyle(track);
      var width = viewportWidth();

      gap = parseFloat(trackStyle.columnGap || trackStyle.gap) || 0;
      slideWidth = Math.floor((width - gap * (visible - 1)) / visible);

      slides.forEach(function (slide) {
        slide.style.width = slideWidth + 'px';
        slide.style.flexBasis = slideWidth + 'px';
      });

      index = Math.min(index, getMaxIndex());
      buildDots();
      applyEqualHeight();
      update();
    };

    var go = function (direction) {
      var maxIndex = getMaxIndex();

      if (!maxIndex) {
        index = 0;
        update();
        return;
      }

      index += direction * perStep;

      if (index > maxIndex) {
        index = 0;
      }

      if (index < 0) {
        index = maxIndex;
      }

      update();
    };

    var stop = function () {
      if (timer) {
        window.clearInterval(timer);
        timer = null;
      }
    };

    var start = function () {
      stop();

      if (!autoplay || paused || slides.length <= slidesPerView()) {
        return;
      }

      timer = window.setInterval(function () {
        go(1);
      }, speed);
    };

    if (previous) {
      previous.addEventListener('click', function () {
        go(-1);
        start();
      });
    }

    if (next) {
      next.addEventListener('click', function () {
        go(1);
        start();
      });
    }

    toggles.forEach(function (button) {
      button.addEventListener('click', function () {
        var card = button.closest('.review-card');

        if (!card) {
          return;
        }

        var expanded = !card.classList.contains('is-expanded');

        card.classList.toggle('is-expanded', expanded);
        button.setAttribute('aria-expanded', expanded ? 'true' : 'false');

        clearCardHeights();
        layout();
        start();
      });
    });

    slider.addEventListener('mouseenter', function () {
      paused = true;
      stop();
    });

    slider.addEventListener('mouseleave', function () {
      paused = false;
      start();
    });

    slider.addEventListener('focusin', function () {
      paused = true;
      stop();
    });

    slider.addEventListener('focusout', function () {
      if (!slider.contains(document.activeElement)) {
        paused = false;
        start();
      }
    });

    window.addEventListener('resize', function () {
      if (resizeFrame) {
        window.cancelAnimationFrame(resizeFrame);
      }

      resizeFrame = window.requestAnimationFrame(function () {
        clearCardHeights();
        layout();
        start();
      });
    });

    cards.forEach(function (card) {
      var image = card.querySelector('img');

      if (image && !image.complete) {
        image.addEventListener('load', function () {
          layout();
        }, { once: true });
      }
    });

    layout();
    start();
  });
}

document.addEventListener('DOMContentLoaded', initReviewsSlider);
