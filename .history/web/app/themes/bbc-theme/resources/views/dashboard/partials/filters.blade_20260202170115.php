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

$assets = [];
$assetNameBySlug = [];

$terms = get_terms([
'taxonomy' => 'analysis_market',
'hide_empty' => false,
]);

foreach ($terms as $term) {
if ($term->parent !== 0) {
$assets[] = [
'name' => html_entity_decode($term->name),
'slug' => $term->slug,
];
$assetNameBySlug[$term->slug] = html_entity_decode($term->name);
}
}
@endphp

<script>
  window.AnalysisFilters = @json([
    'assets' => $assets
  ]);
</script>

<form
  method="get"
  id="analysis-filters"
  class="relative z-10 mb-6 flex flex-wrap gap-4 items-end
         bg-white/80 backdrop-blur-xl
         border border-2 border-brand-primary/80
         rounded-2xl px-6 py-5
         shadow-[0_12px_40px_rgba(0,0,0,0.08)]">

  <div class="relative flex-1 min-w-[320px]">
    <label class="block text-[11px] font-medium tracking-wide text-slate-500 mb-1 uppercase">
      Asset
    </label>

    <div class="relative">
      <input
        type="text"
        id="asset-search"
        autocomplete="off"
        placeholder="Asset suchen …"
        class="w-full rounded-xl
               border border-2 border-brand-primary/40
               bg-white/70
               pl-4 pr-12 py-3 text-sm
               focus:outline-none focus:ring-2 focus:ring-brand-primary/60 focus:border-brand-primary"
        value="{{ $currentAsset && isset($assetNameBySlug[$currentAsset]) ? $assetNameBySlug[$currentAsset] : '' }}">

      <button
        type="button"
        id="asset-clear"
        class="absolute right-3 top-1/2 -translate-y-1/2 hidden
               w-7 h-7 rounded-lg
               text-brand-primary
               bg-brand-primary/15
               hover:bg-brand-primary/25 transition">
        ×
      </button>
    </div>

    <input
      type="hidden"
      name="asset"
      id="asset-slug"
      value="{{ $currentAsset }}">

    <div
      id="asset-suggestions"
      class="absolute z-50 mt-2 w-full
             bg-white/95 backdrop-blur
             border border-2 border-brand-primary/30
             rounded-xl shadow-lg
             max-h-64 overflow-y-auto hidden">
    </div>
  </div>

  <div class="min-w-[200px]">
    <label class="block text-[11px] font-medium tracking-wide text-slate-500 mb-1 uppercase">
      Kategorie
    </label>

    <select
      name="market"
      id="market-select"
      class="w-full rounded-xl
             border border-2 border-brand-primary/30
             bg-white/70
             px-4 py-3 text-sm
             focus:outline-none focus:ring-2 focus:ring-brand-primary/30">
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
    <label class="block text-[11px] font-medium tracking-wide text-slate-500 mb-1 uppercase">
      Von
    </label>
    <input
      type="date"
      name="date_from"
      value="{{ $dateFrom }}"
      class="rounded-xl
             border border-2 border-brand-primary/30
             bg-white/70
             px-4 py-3 text-sm
             focus:outline-none focus:ring-2 focus:ring-brand-primary/30">
  </div>

  <div>
    <label class="block text-[11px] font-medium tracking-wide text-slate-500 mb-1 uppercase">
      Bis
    </label>
    <input
      type="date"
      name="date_to"
      value="{{ $dateTo }}"
      class="rounded-xl
             border border-2 border-brand-primary/30
             bg-white/70
             px-4 py-3 text-sm
             focus:outline-none focus:ring-2 focus:ring-brand-primary/30">
  </div>

  <div class="ml-auto">
    <a
      href="/dashboard"
      class="inline-flex items-center
             px-4 py-3 rounded-xl
             text-sm font-medium
             text-brand-primary
             bg-brand-primary/10
             hover:bg-brand-primary/20 transition">
      Reset
    </a>
  </div>

</form>
