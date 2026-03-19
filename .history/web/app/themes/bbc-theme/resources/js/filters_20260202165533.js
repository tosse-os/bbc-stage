(function () {
  if (!window.AnalysisFilters) return;

  const assets = window.AnalysisFilters.assets || [];

  const form = document.getElementById('analysis-filters');
  const input = document.getElementById('asset-search');
  const hidden = document.getElementById('asset-slug');
  const box = document.getElementById('asset-suggestions');
  const category = form?.querySelector('select[name="market"]');
  const clearBtn = document.getElementById('asset-clear');

  if (!form || !input || !hidden || !box) return;

  function updateClearButton() {
    if (!clearBtn) return;
    clearBtn.classList.toggle('hidden', !hidden.value);
  }

  function clearAsset(submit = true) {
    input.value = '';
    hidden.value = '';
    box.classList.add('hidden');
    box.innerHTML = '';
    updateClearButton();
    if (submit) form.submit();
  }

  updateClearButton();

  // ✕ Button
  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      clearAsset(true);
    });
  }

  // Asset Autosuggest
  input.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    box.innerHTML = '';

    if (q.length < 2) {
      box.classList.add('hidden');
      hidden.value = '';
      updateClearButton();
      return;
    }

    const matches = assets.filter(a =>
      a.name.toLowerCase().includes(q)
    );

    if (!matches.length) {
      box.classList.add('hidden');
      updateClearButton();
      return;
    }

    matches.forEach(asset => {
      const item = document.createElement('div');
      item.textContent = asset.name;
      item.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-slate-100';

      item.addEventListener('click', () => {
        input.value = asset.name;
        hidden.value = asset.slug;

        if (category) category.value = '';

        box.classList.add('hidden');
        updateClearButton();
        form.submit();
      });

      box.appendChild(item);
    });

    box.classList.remove('hidden');
  });

  document.addEventListener('click', e => {
    if (!e.target.closest('#asset-search')) {
      box.classList.add('hidden');
    }
  });

  // Kategorie-Wechsel → Asset löschen
  if (category) {
    category.addEventListener('change', () => {
      if (hidden.value) {
        clearAsset(false);
      }
      form.submit();
    });
  }
})();

(function () {
  const form = document.getElementById('analysis-filters');
  if (!form) return;

  form.querySelectorAll('input[type="date"]').forEach(el => {
    el.addEventListener('change', () => form.submit());
  });
})();
