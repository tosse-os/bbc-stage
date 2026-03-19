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

  function showClear() {
    if (!clearBtn) return;
    clearBtn.classList.toggle('hidden', !(input.value || hidden.value));
  }

  function renderSuggestions(list) {
    box.innerHTML = '';
    list.forEach(asset => {
      const item = document.createElement('div');
      item.textContent = asset.name;
      item.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-slate-100';
      item.addEventListener('click', () => {
        input.value = asset.name;
        hidden.value = asset.slug;
        if (category) category.value = '';
        box.classList.add('hidden');
        showClear();
        form.submit();
      });
      box.appendChild(item);
    });
    box.classList.remove('hidden');
  }

  function clearAsset(submit) {
    input.value = '';
    hidden.value = '';
    box.innerHTML = '';
    box.classList.add('hidden');
    showClear();
    if (submit) form.submit();
  }

  // Autosuggest
  input.addEventListener('input', () => {
    const q = input.value.trim().toLowerCase();
    hidden.value = '';
    showClear();

    if (q.length < 2) {
      box.classList.add('hidden');
      return;
    }

    const matches = assets.filter(a => a.name.toLowerCase().includes(q));
    if (!matches.length) {
      box.classList.add('hidden');
      return;
    }

    renderSuggestions(matches);
  });

  // ✕ löschen
  if (clearBtn) {
    clearBtn.addEventListener('click', () => clearAsset(true));
  }

  // Kategorie-Wechsel → Asset löschen, dann submit
  if (category) {
    category.addEventListener('change', () => {
      if (hidden.value || input.value) clearAsset(false);
      form.submit();
    });
  }

  // Klick außerhalb → Suggestions schließen
  document.addEventListener('click', e => {
    if (!e.target.closest('#asset-search')) box.classList.add('hidden');
  });

  // Datum → submit
  form.querySelectorAll('input[type="date"]').forEach(el => {
    el.addEventListener('change', () => form.submit());
  });

  showClear();
})();
