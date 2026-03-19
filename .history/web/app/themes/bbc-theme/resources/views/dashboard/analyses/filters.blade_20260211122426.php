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

// Prüfen, ob irgendein Filter aktiv ist
$hasActiveFilters = $currentAsset || $currentMarket || $dateFrom || $dateTo;
@endphp

<script>
  window.AnalysisFilters = @json([
    'assets' => $assets
  ]);
</script>

<form
  method="get"
  id="analysis-filters"
  data-filter-panel
  class="relative z-10 mb-8
         bg-white/80 backdrop-blur-xl
         border border-2 border-brand-primary/70
         rounded-2xl
         shadow-[0_12px_40px_rgba(0,0,0,0.08)]
         overflow-hidden md:overflow-visible">

  {{-- Header-Bereich: Mobil deutlich flacher (py-2.5 statt py-3) --}}
  <div class="flex items-center justify-between
            px-4 py-2.5 md:px-6 md:py-5
            cursor-pointer md:cursor-default"
    data-filter-toggle>

    <div class="flex items-center gap-2">
      <span class="text-xs uppercase tracking-wider font-bold text-brand-primary/60 md:text-sm md:normal-case md:font-medium md:text-brand-primary">
        Filter
      </span>

      {{-- Kleiner Indikator-Punkt, falls Filter aktiv sind --}}
      @if($hasActiveFilters)
      <span class="flex h-1.5 w-1.5 rounded-full bg-brand-primary animate-pulse"></span>
      @endif
    </div>

    <button
      type="button"
      class="md:hidden flex items-center gap-1.5
           text-xs font-bold uppercase tracking-tight text-brand-primary">

      <span data-filter-label>anzeigen</span>

      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        class="w-4 h-4 transition-transform duration-300"
        data-filter-icon
        fill="none"
        stroke="currentColor"
        stroke-width="3">
        <path d="M6 9l6 6 6-6" />
      </svg>
    </button>
  </div>

  <div
    data-filter-content
    class="grid grid-cols-1 gap-4
           md:flex md:flex-wrap md:items-end
           px-4 pb-4 md:px-6 md:pb-5
           overflow-hidden transition-all duration-300 ease-in-out"
    style="max-height:0; opacity:0;">

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

    <div class="w-full md:w-auto md:ml-auto flex justify-end gap-2">
      {{-- Reset Button --}}
      <a
        href="/dashboard"
        class="inline-flex items-center gap-2
               px-4 py-3 rounded-xl
               text-sm font-medium
               text-brand-primary
               bg-brand-primary/10
               hover:bg-brand-primary/20 transition"
        title="Filter zurücksetzen">

        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          stroke-width="1.8"
          stroke-linecap="round"
          stroke-linejoin="round">
          <path d="M3 12a9 9 0 0 1 15-6" />
          <polyline points="18 3 18 9 12 9" />
          <path d="M21 12a9 9 0 0 1-15 6" />
          <polyline points="6 21 6 15 12 15" />
        </svg>
      </a>

      {{-- Absenden Button für Mobile, falls automatisches Submit nicht erwünscht --}}
      <button type="submit" class="md:hidden bg-brand-primary text-white px-6 py-3 rounded-xl text-sm font-bold shadow-sm">
        Anwenden
      </button>
    </div>

  </div>

</form>
