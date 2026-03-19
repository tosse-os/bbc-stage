@php
/**
* Aktuelle Filterwerte aus der URL
*/
$currentAsset = request()->get('asset');
$currentMarket = request()->get('market');
$dateFrom = request()->get('date_from');
$dateTo = request()->get('date_to');

/**
* Alle Hauptrubriken (Top-Level-Märkte)
* z. B. S&P 500, Edelmetalle, ETF-Unternehmen
*/
$topMarkets = get_terms([
'taxonomy' => 'analysis_market',
'parent' => 0,
'hide_empty' => false,
]);

/**
* Alle Assets (Leaf-Terme)
* werden für das Autosuggest benötigt
*/
$assets = get_terms([
'taxonomy' => 'analysis_market',
'hide_empty' => false,
]);

$assetIndex = [];
foreach ($assets as $asset) {
if ($asset->parent !== 0) {
$assetIndex[] = [
'name' => html_entity_decode($asset->name),
'slug' => $asset->slug,
];
}
}
@endphp

<form method="get" class="flex flex-wrap items-end gap-4 bg-white rounded-xl p-4 shadow-sm">

  {{-- Asset Autosuggest --}}
  <div class="relative w-64">
    <label class="block text-xs font-medium text-slate-500 mb-1">
      Asset
    </label>

    <input
      type="text"
      id="asset-search"
      placeholder="Asset suchen …"
      autocomplete="off"
      class="w-full rounded-lg border px-3 py-2 text-sm"
      value="{{ $currentAsset ? '' : '' }}">

    <input type="hidden" name="asset" id="asset-slug" value="{{ $currentAsset }}">

    <div
      id="asset-suggestions"
      class="absolute z-20 mt-1 w-full bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
    </div>
  </div>

  {{-- Hauptrubrik Dropdown --}}
  <div>
    <label class="block text-xs font-medium text-slate-500 mb-1">
      Kategorie
    </label>

    <select name="market" class="rounded-lg border px-3 py-2 text-sm min-w-[180px]">
      <option value="">Alle Kategorien</option>
      @foreach ($topMarkets as $market)
      <option
        value="{{ $market->slug }}"
        @selected($currentMarket===$market->slug)>
        {!! html_entity_decode($market->name) !!}
      </option>
      @endforeach
    </select>
  </div>

  {{-- Datum von --}}
  <div>
    <label class="block text-xs font-medium text-slate-500 mb-1">
      Von
    </label>
    <input
      type="date"
      name="date_from"
      value="{{ $dateFrom }}"
      class="rounded-lg border px-3 py-2 text-sm">
  </div>

  {{-- Datum bis --}}
  <div>
    <label class="block text-xs font-medium text-slate-500 mb-1">
      Bis
    </label>
    <input
      type="date"
      name="date_to"
      value="{{ $dateTo }}"
      class="rounded-lg border px-3 py-2 text-sm">
  </div>

  {{-- Aktionen --}}
  <div class="flex gap-2">
    <button
      type="submit"
      class="px-5 py-2 rounded-lg bg-dashboard-accent text-white text-sm">
      Filtern
    </button>

    <a
      href="/dashboard"
      class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 text-sm">
      Reset
    </a>
  </div>

</form>

<script>
  /**
   * Asset Autosuggest Logik
   * - durchsucht alle Leaf-Assets
   * - setzt beim Klick den Slug ins Hidden-Feld
   * - kein Reload beim Tippen
   */
  (function() {
    const assets = @json($assetIndex);
    const input = document.getElementById('asset-search');
    const hidden = document.getElementById('asset-slug');
    const box = document.getElementById('asset-suggestions');

    if (!input || !box) return;

    input.addEventListener('input', function() {
      const q = this.value.toLowerCase();
      box.innerHTML = '';

      if (q.length < 2) {
        box.classList.add('hidden');
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
        item.className = 'px-3 py-2 text-sm cursor-pointer hover:bg-slate-100';

        item.addEventListener('click', function() {
          input.value = asset.name;
          hidden.value = asset.slug;
          box.classList.add('hidden');
        });

        box.appendChild(item);
      });

      box.classList.remove('hidden');
    });

    document.addEventListener('click', function(e) {
      if (!e.target.closest('#asset-search')) {
        box.classList.add('hidden');
      }
    });
  })();
</script>
