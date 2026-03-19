(function () {
  console.log('0.0');
  if (!window.AnalysisFilters) return;
  console.log('0.1');
  const assets = window.AnalysisFilters.assets || [];

  const form = document.getElementById('analysis-filters');
  const input = document.getElementById('asset-search');
  const hidden = document.getElementById('asset-slug');
  const box = document.getElementById('asset-suggestions');
  const category = form?.querySelector('select[name="market"]');

  if (!form || !input || !hidden || !box) return;

  input.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    box.innerHTML = '';

    if (q.length < 2) {
      box.classList.add('hidden');
      hidden.value = '';
      return;
    }

    const matches = assets.filter(a =>
      a.name.toLowerCase().includes(q)
    );

    if (!matches.length) {
      box.classList.add('hidden');
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
})();

(function () {
  const form = document.getElementById('analysis-filters');
  if (!form) return;

  form.querySelectorAll('select, input[type="date"]').forEach(el => {
    el.addEventListener('change', () => form.submit());
  });
})();
