(function () {
  function init() {
    if (!window.AnalysisFilters) return false;

    const assets = window.AnalysisFilters.assets || [];

    const form = document.getElementById('analysis-filters');
    const input = document.getElementById('asset-search');
    const hidden = document.getElementById('asset-slug');
    const box = document.getElementById('asset-suggestions');
    const category = form?.querySelector('select[name="market"]');
    const clearBtn = document.getElementById('asset-clear');

    if (!form || !input || !hidden || !box || !category) return false;
    if (form.dataset.filtersInit === '1') return true;
    form.dataset.filtersInit = '1';

    document.body.appendChild(box);

    function positionBox() {
      const rect = input.getBoundingClientRect();
      box.style.position = 'fixed';
      box.style.top = rect.bottom + 'px';
      box.style.left = rect.left + 'px';
      box.style.width = rect.width + 'px';
      box.style.zIndex = '9999';
    }

    function syncState() {
      const hasAsset = !!hidden.value || !!input.value;
      category.disabled = hasAsset;
    }

    function clearAsset(submit) {
      input.value = '';
      hidden.value = '';
      box.innerHTML = '';
      box.classList.add('hidden');
      syncState();
      if (submit) form.submit();
    }

    function render(matches) {
      box.innerHTML = '';
      matches.forEach(asset => {
        const item = document.createElement('div');
        item.textContent = asset.name;
        item.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-slate-100';
        item.addEventListener('click', () => {
          input.value = asset.name;
          hidden.value = asset.slug;
          category.value = '';
          box.classList.add('hidden');
          syncState();
          form.submit();
        });
        box.appendChild(item);
      });
      positionBox();
      box.classList.remove('hidden');
    }

    input.addEventListener('input', function () {
      const q = this.value.trim().toLowerCase();
      box.innerHTML = '';

      if (q.length < 2) {
        hidden.value = '';
        box.classList.add('hidden');
        syncState();
        return;
      }

      const matches = assets.filter(a => a.name.toLowerCase().includes(q));
      if (!matches.length) {
        hidden.value = '';
        box.classList.add('hidden');
        syncState();
        return;
      }

      hidden.value = '';
      render(matches);
      syncState();
    });

    document.addEventListener('click', e => {
      if (!e.target.closest('#asset-search') && !e.target.closest('#asset-suggestions')) {
        box.classList.add('hidden');
      }
    });

    window.addEventListener('resize', () => {
      if (!box.classList.contains('hidden')) positionBox();
    });

    window.addEventListener('scroll', () => {
      if (!box.classList.contains('hidden')) positionBox();
    }, true);

    category.addEventListener('change', () => {
      if (hidden.value || input.value) {
        clearAsset(false);
      }
      form.submit();
    });

    form.querySelectorAll('input[type="date"]').forEach(el => {
      el.addEventListener('change', () => form.submit());
    });

    syncState();
    return true;
  }

  function boot() {
    if (init()) return;

    let tries = 0;
    const t = setInterval(() => {
      tries += 1;
      if (init() || tries >= 50) clearInterval(t);
    }, 100);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
