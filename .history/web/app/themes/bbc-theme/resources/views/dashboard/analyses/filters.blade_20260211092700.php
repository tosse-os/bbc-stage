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

<div class="md:hidden mb-4">
  <button
    type="button"
    data-filter-toggle
    class="w-full flex items-center justify-between
           px-4 py-3 rounded-xl
           bg-white/80 backdrop-blur
           border border-brand-primary/40
           text-sm font-medium text-brand-primary">

    <span data-filter-label>Filter anzeigen</span>

    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      class="w-4 h-4 transition-transform duration-300"
      data-filter-icon
      fill="none"
      stroke="currentColor"
      stroke-width="2">
      <path d="M6 9l6 6 6-6" />
    </svg>

  </button>
</div>

<form
  method="get"
  id="analysis-filters"
  data-filter-panel
  class="relative z-10 mb-8
         bg-white/80 backdrop-blur-xl
         border border-2 border-brand-primary/70
         rounded-2xl
         shadow-[0_12px_40px_rgba(0,0,0,0.08)]
         overflow-hidden
         transition-[max-height,opacity] duration-300 ease-in-out
         md:overflow-visible md:transition-none"
  style="max-height:0; opacity:0;">


  <div class="grid grid-cols-1 gap-4
            md:flex md:flex-wrap md:items-end
            px-4 py-4 md:px-6 md:py-5">

    <div class="relative w-full md:flex-1 md:min-w-[320px]">
      <label class="block text-[11px] font-medium tracking-wide text-slate-500 mb-1 uppercase">
        Asset
      </label>

      <input
        type="text"
        id="asset-search"
        autocomplete="off"
        placeholder="Asset suchen …"
        class="w-full rounded-xl
             border border-2 border-brand-primary/35
             bg-white/70
             px-4 py-3 text-sm
             focus:outline-none focus:ring-2 focus:ring-brand-primary/50 focus:border-brand-primary"
        value="{{ $currentAsset && isset($assetNameBySlug[$currentAsset]) ? $assetNameBySlug[$currentAsset] : '' }}">

      <input
        type="hidden"
        name="asset"
        id="asset-slug"
        value="{{ $currentAsset }}">

      <div
        id="asset-suggestions"
        class="absolute z-30 mt-2 w-full
             bg-white/95 backdrop-blur
             border border-2 border-brand-primary/25
             rounded-xl shadow-lg
             max-h-64 overflow-y-auto hidden">
      </div>
    </div>

    <div class="w-full md:min-w-[220px] md:w-auto">
      <label class="block text-[11px] font-medium tracking-wide text-slate-500 mb-1 uppercase">
        Kategorie
      </label>

      <select
        name="market"
        class="w-full rounded-xl
             border border-2 border-brand-primary/30
             bg-white/70
             px-4 py-3 text-sm
             focus:outline-none focus:ring-2 focus:ring-brand-primary/30"
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

    <div class="w-full md:min-w-[280px] md:w-auto">
      <label class="block text-[11px] font-medium tracking-wide text-slate-500 mb-1 uppercase">
        Datum
      </label>

      <div class="flex items-center rounded-xl
                border border-2 border-brand-primary/30
                bg-white/70
                overflow-hidden
                focus-within:ring-2 focus-within:ring-brand-primary/30">

        <input
          type="date"
          name="date_from"
          value="{{ $dateFrom }}"
          class="w-full bg-transparent px-4 py-3 text-sm focus:outline-none">

        <div class="h-6 w-px bg-brand-primary/20"></div>

        <input
          type="date"
          name="date_to"
          value="{{ $dateTo }}"
          class="w-full bg-transparent px-4 py-3 text-sm focus:outline-none">
      </div>
    </div>

    <div class="w-full md:w-auto md:ml-auto flex justify-end">
      <a
        href="/dashboard"
        class="inline-flex items-center gap-2
             px-3 py-3 rounded-xl
             text-sm font-medium
             text-brand-primary
             bg-brand-primary/10
             hover:bg-brand-primary/20 transition">

        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          stroke-width="1.8"
          stroke-linecap="round"
          stroke-linejoin="round"
          aria-hidden="true">

          <path d="M3 12a9 9 0 0 1 15-6" />
          <polyline points="18 3 18 9 12 9" />
          <path d="M21 12a9 9 0 0 1-15 6" />
          <polyline points="6 21 6 15 12 15" />

        </svg>

      </a>
    </div>

  </div>

</form>
