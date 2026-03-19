import.meta.glob([
  '../images/**',
  '../fonts/**',
]);



//import Alpine from 'alpinejs/dist/module.esm.js';
import Alpine from "alpinejs"
import collapse from "@alpinejs/collapse"

Alpine.plugin(collapse)


// import Swiper, { Autoplay, Navigation, Pagination } from 'swiper';
// import 'swiper/css';
// import 'swiper/css/navigation';
// import 'swiper/css/pagination';

function contactForm() {
  console.log('Alpine init 00');
  return {
    data: { company: '', contact: '', phone: '', email: '', message: '', privacy: false },
    errors: {},
    touch(f) { this.validateField(f) },
    validateField(f) {
      const v = this.data[f]; let m = '';
      if (f === 'email') { m = v ? (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? '' : 'Bitte eine gültige E-Mail-Adresse eingeben.') : 'Bitte E-Mail angeben.'; }
      if (f === 'privacy') { m = this.data.privacy ? '' : 'Bitte Datenschutzerklärung akzeptieren.'; }
      if (['company', 'contact', 'message'].includes(f)) { m = v?.trim() ? '' : 'Dieses Feld darf nicht leer sein.'; }
      if (m) this.errors[f] = m; else delete this.errors[f];
    },
    inputClass(f) { return this.errors[f] ? 'input border-red-500' : 'input border-gray-300' },
    onSubmit() {
      ['company', 'contact', 'email', 'message', 'privacy'].forEach(f => this.validateField(f));
      if (Object.values(this.errors).some(e => e)) return;
      this.$refs.submitBtn.disabled = true;
      this.$refs.form.submit();
    }
  }
}
document.addEventListener('alpine:init', () => {
  Alpine.data('contactForm', contactForm)
})

function initAlpine() {
  window.Alpine = Alpine;
  Alpine.start();
}

function initStyledListLede() {
  document.querySelectorAll('.styled-list-lede li').forEach(li => {
    if (li.dataset.processed) return;
    const lead = li.querySelector('strong');
    if (!lead) return;
    const desc = document.createElement('span');
    desc.className = 'desc';
    let n = lead.nextSibling;
    if (n && n.nodeType === 3) n.textContent = n.textContent.replace(/^\s*[:\-–]\s*/, ' ');
    while (n) {
      const next = n.nextSibling;
      desc.appendChild(n);
      n = next;
    }
    lead.classList.add('lede');
    li.appendChild(desc);
    li.dataset.processed = '1';
  });
}

function initParallax() {
  const speed = 0.35;
  const offset = 0.2;
  const overscanScale = 1.33;
  const runParallax = () => {
    document.querySelectorAll('.parallax-cover').forEach(el => {
      const bg = el.querySelector('.wp-block-cover__image-background');
      if (!bg) return;
      const rect = el.getBoundingClientRect();
      const vh = window.innerHeight || 0;
      if (rect.bottom < 0 || rect.top > vh * 1.2) return;
      const y = (-rect.top) * speed + rect.height * offset;
      bg.style.transform = `translateY(${y}px) scale(${overscanScale})`;
      bg.style.willChange = 'transform';
    });
  };
  ['scroll', 'resize', 'load'].forEach(e => window.addEventListener(e, runParallax, { passive: true }));
  runParallax();
}

function initRefSwiper() {
  const refEl = document.querySelector('.refSwiper');
  if (!refEl) return;
  new Swiper(refEl, {
    modules: [Navigation, Pagination],
    observer: true,
    observeParents: true,
    slidesPerView: 1,
    spaceBetween: 26,
    navigation: {
      nextEl: refEl.querySelector('.ref-button-next'),
      prevEl: refEl.querySelector('.ref-button-prev'),
    },
    pagination: {
      el: refEl.querySelector('.swiper-pagination'),
      clickable: true,
    },
    breakpoints: {
      640: { slidesPerView: 1 },
      1024: { slidesPerView: 3 },
    },
  });
}

function initLogoSwiper() {
  const logoEl = document.querySelector('.logoSwiper');
  if (!logoEl) return;
  const slidesDesktop = parseInt(logoEl.dataset.slidesDesktop) || 5;
  new Swiper(logoEl, {
    modules: [Navigation, Pagination, Autoplay],
    loop: true,
    slidesPerView: 2,
    slidesPerGroup: 2,
    spaceBetween: 60,
    breakpoints: {
      640: { slidesPerView: 3, slidesPerGroup: 3 },
      768: { slidesPerView: 4, slidesPerGroup: 4 },
      1024: { slidesPerView: slidesDesktop, slidesPerGroup: slidesDesktop },
    },
    navigation: {
      nextEl: logoEl.querySelector('.swiper-button-next'),
      prevEl: logoEl.querySelector('.swiper-button-prev'),
    },
    pagination: {
      el: logoEl.querySelector('.swiper-pagination'),
      clickable: true,
    },
    autoplay: {
      delay: 4000,
      disableOnInteraction: false
    },
    speed: 1200,
    loop: true
  });
}

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
  initAlpine();
  initStyledListLede();
  initParallax();
  initRefSwiper();
  initLogoSwiper();
  initSmoothScroll(900);
  initScrollToTop();
}

document.addEventListener('DOMContentLoaded', initApp);


/* document.querySelectorAll('a[href="#service-block"]').forEach(a => {
  a.addEventListener('click', e => {
    e.preventDefault();
    const target = document.querySelector('#service-block');
    const offset = 110;
console.log('dodoclick');
    const top = target.getBoundingClientRect().top + window.pageYOffset - offset;

    window.scrollTo({
      top: top,
      behavior: 'smooth'
    });
  });
});
 */
