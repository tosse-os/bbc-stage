(function () {
  if (!window.AnalysisFilters) return;

  const assets = window.AnalysisFilters.assets || [];

  const form = document.getElementById('analysis-filters');
  const input = document.getElementById('asset-search');
  const hidden = document.getElementById('asset-slug');
  const box = document.getElementById('asset-suggestions');
  const clearBtn = document.getElementById('asset-clear');
  const marketSelect = document.getElementById('market-select');

  if (!form || !input || !hidden || !box || !clearBtn || !marketSelect) return;

  function updateClearButton() {
    clearBtn.classList.toggle('hidden', !hidden.value && !input.value);
  }

  function clearAsset(submit = true) {
    input.value = '';
    hidden.value = '';
    box.classList.add('hidden');
    box.innerHTML = '';
    updateClearButton();
    if (submit) form.submit();
  }

  clearBtn.addEventListener('click', () => {
    clearAsset(true);
  });

  input.addEventListener('input', () => {
    const q = input.value.toLowerCase();
    box.innerHTML = '';

    if (q.length < 2) {
      hidden.value = '';
      box.classList.add('hidden');
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
      const el = document.createElement('div');
      el.textContent = asset.name;
      el.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-slate-100';

      el.addEventListener('click', () => {
        input.value = asset.name;
        hidden.value = asset.slug;
        marketSelect.value = '';
        box.classList.add('hidden');
        updateClearButton();
        form.submit();
      });

      box.appendChild(el);
    });

    box.classList.remove('hidden');
    updateClearButton();
  });

  marketSelect.addEventListener('change', () => {
    if (hidden.value) {
      clearAsset(false);
    }
    form.submit();
  });

  form.querySelectorAll('input[type="date"]').forEach(el => {
    el.addEventListener('change', () => form.submit());
  });

  updateClearButton();
})();
