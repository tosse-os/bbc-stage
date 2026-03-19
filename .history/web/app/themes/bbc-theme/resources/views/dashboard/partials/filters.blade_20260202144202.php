@php
$currentAsset = request()->get('asset');
$currentMarket = request()->get('market');
$dateFrom = request()->get('date_from');
$dateTo = request()->get('date_to');

$topMarkets = get_terms([
'taxonomy' => 'analysis_market',
'parent' => 0,
'hide_empty' => false,
]);

$assetNameBySlug = [];

$assets = get_terms([
'taxonomy' => 'analysis_market',
'hide_empty' => false,
]);

foreach ($assets as $asset) {
if ($asset->parent !== 0) {
$assetNameBySlug[$asset->slug] = html_entity_decode($asset->name);
}
}
@endphp

<form
  method="get"
  id="analysis-filters"
  class="flex flex-wrap gap-4 items-end bg-white rounded-2xl p-5 shadow-sm">

  <div class="relative flex-1 min-w-[280px]">
    <label class="block text-xs font-medium text-slate-500 mb-1">
      Asset
    </label>

    <input
      type="text"
      id="asset-search"
      autocomplete="off"
      placeholder="Asset suchen …"
      class="w-full rounded-xl border px-4 py-2.5 text-sm focus:ring-2 focus:ring-dashboard-accent"
      value="{{ $currentAsset && isset($assetNameBySlug[$currentAsset]) ? $assetNameBySlug[$currentAsset] : '' }}">

    <input
      type="hidden"
      name="asset"
      id="asset-slug"
      value="{{ $currentAsset }}">

    <div
      id="asset-suggestions"
      class="absolute z-30 mt-1 w-full bg-white border rounded-xl shadow-lg max-h-64 overflow-y-auto hidden">
    </div>
  </div>

  <div class="min-w-[200px]">
    <label class="block text-xs font-medium text-slate-500 mb-1">
      Kategorie
    </label>

    <select
      name="market"
      class="w-full rounded-xl border px-3 py-2.5 text-sm"
      @disabled($currentAsset)>
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

  <div>
    <label class="block text-xs font-medium text-slate-500 mb-1">
      Von
    </label>
    <input
      type="date"
      name="date_from"
      value="{{ $dateFrom }}"
      class="rounded-xl border px-3 py-2.5 text-sm">
  </div>

  <div>
    <label class="block text-xs font-medium text-slate-500 mb-1">
      Bis
    </label>
    <input
      type="date"
      name="date_to"
      value="{{ $dateTo }}"
      class="rounded-xl border px-3 py-2.5 text-sm">
  </div>

  <div>
    <a
      href="/dashboard"
      class="inline-flex items-center px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-sm hover:bg-slate-200">
      Reset
    </a>
  </div>

</form>
