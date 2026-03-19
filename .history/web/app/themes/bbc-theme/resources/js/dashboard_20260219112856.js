import './dashboard-filters.js';

/*
|--------------------------------------------------------------------------
| Image Zoom
|--------------------------------------------------------------------------
| Ermöglicht das Zoomen von Charts per Overlay mit ESC-Support.
|--------------------------------------------------------------------------
*/
function initImageZoom() {

  const images = document.querySelectorAll('[data-chart-zoom]');

  images.forEach(img => {
    img.addEventListener('click', () => {

      const overlay = document.createElement('div');
      overlay.className = 'fixed inset-0 bg-black/90 flex items-center justify-center z-50 cursor-zoom-out';

      const clone = img.cloneNode();
      clone.className = 'max-w-full max-h-full rounded-xl';

      overlay.appendChild(clone);
      document.body.appendChild(overlay);

      overlay.addEventListener('click', () => overlay.remove());

      document.addEventListener('keydown', e => {
        if (e.key === 'Escape') overlay.remove();
      }, { once: true });

    });
  });

}

/*
|--------------------------------------------------------------------------
| Password Toggle
|--------------------------------------------------------------------------
| Schaltet Passwortfelder zwischen sichtbar und verborgen um.
|--------------------------------------------------------------------------
*/
function initPasswordToggle() {

  document.querySelectorAll('.password-toggle').forEach(toggle => {

    const input = toggle.closest('div').querySelector('.password-input');

    toggle.addEventListener('click', () => {
      input.type = input.type === 'password' ? 'text' : 'password';
      toggle.classList.toggle('text-brand-primary');
    });

  });

}

/*
|--------------------------------------------------------------------------
| Sidebar Logic
|--------------------------------------------------------------------------
| Steuert Collapse/Expand der Sidebar inkl. Persistierung via AJAX.
|--------------------------------------------------------------------------
*/
function initSidebarLogic() {

  const body = document.body;
  const sidebarIconToggle = document.querySelector('[data-sidebar-toggle]');
  const appearanceSidebarBtn = document.querySelector('[data-appearance-sidebar]');

  async function persistSidebar(collapsed, ajaxUrl, nonce) {

    const form = new FormData();
    form.append('action', 'set_dashboard_sidebar');
    form.append('collapsed', collapsed ? '1' : '0');
    form.append('_wpnonce', nonce);

    await fetch(ajaxUrl, {
      method: 'POST',
      body: form
    });

  }

  function applySidebarState(collapsed) {

    body.classList.toggle('sidebar-collapsed', collapsed);

    if (appearanceSidebarBtn) {
      appearanceSidebarBtn.dataset.current = collapsed ? 'collapsed' : 'expanded';
      appearanceSidebarBtn.textContent = collapsed ? 'Collapsed' : 'Expanded';
    }

  }

  if (sidebarIconToggle) {

    sidebarIconToggle.addEventListener('click', async () => {

      const collapsed = !body.classList.contains('sidebar-collapsed');
      applySidebarState(collapsed);

      await persistSidebar(
        collapsed,
        sidebarIconToggle.dataset.ajax,
        sidebarIconToggle.dataset.nonce
      );

    });

  }

  if (appearanceSidebarBtn) {

    appearanceSidebarBtn.addEventListener('click', async () => {

      const collapsed = !body.classList.contains('sidebar-collapsed');
      applySidebarState(collapsed);

      await persistSidebar(
        collapsed,
        appearanceSidebarBtn.dataset.ajax,
        appearanceSidebarBtn.dataset.nonce
      );

    });

  }

}

/*
|--------------------------------------------------------------------------
| Theme Toggle
|--------------------------------------------------------------------------
| Wechselt zwischen Light- und Dark-Theme inkl. Persistierung.
|--------------------------------------------------------------------------
*/
function initThemeToggle() {

  const html = document.documentElement;
  const themeBtn = document.querySelector('[data-appearance-theme]');

  if (!themeBtn) return;

  themeBtn.addEventListener('click', async () => {

    const current = themeBtn.dataset.current === 'dark' ? 'dark' : 'light';
    const next = current === 'dark' ? 'light' : 'dark';

    html.dataset.theme = next;
    themeBtn.dataset.current = next;
    themeBtn.textContent = next.charAt(0).toUpperCase() + next.slice(1);

    const form = new FormData();
    form.append('action', 'set_dashboard_theme');
    form.append('theme', next);
    form.append('_wpnonce', themeBtn.dataset.nonce);

    await fetch(themeBtn.dataset.ajax, {
      method: 'POST',
      body: form
    });

  });

}

/*
|--------------------------------------------------------------------------
| Mobile Filter Panel
|--------------------------------------------------------------------------
| Steuert das Ein- und Ausklappen des Filterbereichs auf Mobile.
|--------------------------------------------------------------------------
*/
function initMobileFilterPanel() {

  const toggle = document.querySelector('[data-filter-toggle]');
  const content = document.querySelector('[data-filter-content]');
  const icon = document.querySelector('[data-filter-icon]');
  const label = document.querySelector('[data-filter-label]');

  if (!toggle || !content) return;

  const isMobile = () => window.innerWidth < 768;

  function closePanel() {
    content.style.maxHeight = '0px';
    content.style.opacity = '0';
    content.style.overflow = 'hidden';
    if (icon) icon.classList.remove('rotate-180');
    if (label) label.textContent = 'anzeigen';
  }

  function openPanel() {
    content.style.maxHeight = content.scrollHeight + 'px';
    content.style.opacity = '1';
    content.style.overflow = 'hidden';
    if (icon) icon.classList.add('rotate-180');
    if (label) label.textContent = 'ausblenden';
  }

  content.style.transition = 'max-height 300ms ease, opacity 300ms ease';

  if (isMobile()) {
    closePanel();
  } else {
    content.style.maxHeight = 'none';
    content.style.opacity = '1';
    content.style.overflow = 'visible';
  }

  toggle.addEventListener('click', () => {
    content.style.maxHeight === '0px' ? openPanel() : closePanel();
  });

  window.addEventListener('resize', () => {
    if (!isMobile()) {
      content.style.maxHeight = 'none';
      content.style.opacity = '1';
      content.style.overflow = 'visible';
    } else {
      closePanel();
    }
  });

}

/*
|--------------------------------------------------------------------------
| Copy Buttons
|--------------------------------------------------------------------------
| Kopiert definierte Werte in die Zwischenablage mit visuellem Feedback.
|--------------------------------------------------------------------------
*/
function initCopyButtons() {

  const buttons = document.querySelectorAll('.copy-btn');

  buttons.forEach(btn => {

    const tooltip = btn.querySelector('.tooltip');
    const copyIcon = btn.querySelector('[data-icon="copy"]');
    const checkIcon = btn.querySelector('[data-icon="check"]');

    btn.addEventListener('mouseenter', () => {
      if (tooltip) tooltip.style.opacity = '1';
    });

    btn.addEventListener('mouseleave', () => {
      if (tooltip) tooltip.style.opacity = '0';
    });

    btn.addEventListener('click', () => {

      const value = btn.dataset.copy;
      if (!value) return;

      const labelCopy = btn.dataset.labelCopy;
      const labelSuccess = btn.dataset.labelSuccess;

      navigator.clipboard.writeText(value);

      if (tooltip) tooltip.textContent = labelSuccess;
      if (copyIcon) copyIcon.classList.add('hidden');
      if (checkIcon) checkIcon.classList.remove('hidden');

      btn.classList.add('copy-success');

      setTimeout(() => {

        if (tooltip) tooltip.textContent = labelCopy;
        if (copyIcon) copyIcon.classList.remove('hidden');
        if (checkIcon) checkIcon.classList.add('hidden');

        btn.classList.remove('copy-success');

      }, 1000);

    });

  });

}

/*
|--------------------------------------------------------------------------
| Custom Audio Players
|--------------------------------------------------------------------------
| Steuert individuelle Audio-Player mit Play/Pause & Progressbar.
|--------------------------------------------------------------------------
*/
function initCustomAudioPlayers() {

  document.querySelectorAll('.custom-audio-wrapper').forEach(wrapper => {
    const audio = wrapper.querySelector('.hidden-audio');
    const playBtn = wrapper.querySelector('.play-btn');
    const seekBar = wrapper.querySelector('.seek-bar');
    const muteBtn = wrapper.querySelector('.mute-btn');
    const currentTimeEl = wrapper.querySelector('.current-time');
    const durationEl = wrapper.querySelector('.duration');

    // 1. Spulen (Scrubbing)
    seekBar.oninput = () => {
      const seekTo = audio.duration * (seekBar.value / 100);
      audio.currentTime = seekTo;
    };

    // 2. Mute / Unmute
    muteBtn.onclick = () => {
      audio.muted = !audio.muted;
      muteBtn.classList.toggle('text-red-500', audio.muted);
      // Optional: Icon-Wechsel bei Mute
    };

    // 3. Play / Pause
    playBtn.onclick = () => {
      if (audio.paused) {
        audio.play();
        playBtn.innerHTML = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
      } else {
        audio.pause();
        playBtn.innerHTML = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
      }
    };

    // 4. Fortschritt synchronisieren
    audio.ontimeupdate = () => {
      if (!isNaN(audio.duration)) {
        const percent = (audio.currentTime / audio.duration) * 100;
        seekBar.value = percent;
        currentTimeEl.innerText = formatTime(audio.currentTime);
      }
    };

    audio.onloadedmetadata = () => {
      durationEl.innerText = formatTime(audio.duration);
    };

    function formatTime(time) {
      const min = Math.floor(time / 60);
      const sec = Math.floor(time % 60);
      return `${min}:${sec < 10 ? '0' + sec : sec}`;
    }
  });


}

/*
|--------------------------------------------------------------------------
| Dashboard Init
|--------------------------------------------------------------------------
| Zentrale Initialisierung aller Dashboard-Module.
|--------------------------------------------------------------------------
*/
function initDashboard() {

  initImageZoom();
  initPasswordToggle();
  initSidebarLogic();
  initThemeToggle();
  initMobileFilterPanel();
  initCopyButtons();
  initCustomAudioPlayers();

}

document.addEventListener('DOMContentLoaded', initDashboard);



document.addEventListener('click', function (e) {

  const trigger = e.target.closest('.video-trigger');
  if (!trigger) return;

  const url = trigger.dataset.video;
  if (!url) return;

  const modal = document.getElementById('videoModal');
  const frame = document.getElementById('videoFrame');

  let embedUrl = url;

  if (url.includes('youtube.com/watch')) {
    const videoId = new URL(url).searchParams.get('v');
    embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
  }

  frame.src = embedUrl;
  modal.classList.remove('hidden');
  modal.classList.add('flex');
});

document.getElementById('videoClose')?.addEventListener('click', function () {
  const modal = document.getElementById('videoModal');
  const frame = document.getElementById('videoFrame');
  frame.src = '';
  modal.classList.add('hidden');
});

document.addEventListener('DOMContentLoaded', function () {

  const modal = document.getElementById('video-modal');
  if (!modal) return;

  const overlay = modal.querySelector('[data-video-overlay]');
  const dialog = modal.querySelector('[data-video-dialog]');
  const content = document.getElementById('video-modal-content');
  const closeBtn = modal.querySelector('[data-video-close]');

  let activeIframe = null;

  function buildEmbed(url) {

    if (url.includes('youtube.com') || url.includes('youtu.be')) {
      return `
        <iframe
          src="https://www.youtube.com/embed/${extractYouTubeId(url)}?autoplay=1&rel=0"
          class="w-full h-full"
          frameborder="0"
          allow="autoplay; encrypted-media"
          allowfullscreen>
        </iframe>
      `;
    }

    if (url.includes('vimeo.com')) {
      return `
        <iframe
          src="https://player.vimeo.com/video/${extractVimeoId(url)}?autoplay=1"
          class="w-full h-full"
          frameborder="0"
          allow="autoplay; fullscreen"
          allowfullscreen>
        </iframe>
      `;
    }

    return '';
  }

  function extractYouTubeId(url) {
    const regExp = /(?:youtube\.com.*(?:\\?|&)v=|youtu\.be\\/)([^&#] +) /;
const match = url.match(regExp);
return match && match[1] ? match[1] : '';
  }

function extractVimeoId(url) {
  const regExp = /vimeo\.com\\/(\\d +) /;
  const match = url.match(regExp);
  return match && match[1] ? match[1] : '';
}

function openModal(url) {

  pauseAll();

  content.innerHTML = buildEmbed(url);
  activeIframe = content.querySelector('iframe');

  modal.classList.remove('hidden');
  modal.classList.add('flex');

  requestAnimationFrame(() => {
    overlay.classList.remove('opacity-0');
    overlay.classList.add('opacity-100');

    dialog.classList.remove('opacity-0', 'scale-95');
    dialog.classList.add('opacity-100', 'scale-100');
  });
}

function closeModal() {

  overlay.classList.remove('opacity-100');
  overlay.classList.add('opacity-0');

  dialog.classList.remove('opacity-100', 'scale-100');
  dialog.classList.add('opacity-0', 'scale-95');

  setTimeout(() => {
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    content.innerHTML = '';
    activeIframe = null;
  }, 300);
}

function pauseAll() {
  document.querySelectorAll('iframe').forEach(frame => {
    frame.src = frame.src;
  });
}

document.querySelectorAll('.video-trigger').forEach(btn => {
  btn.addEventListener('click', function () {
    const url = this.dataset.videoUrl;
    if (url) openModal(url);
  });
});

overlay.addEventListener('click', closeModal);
closeBtn.addEventListener('click', closeModal);

document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    closeModal();
  }
});

});

