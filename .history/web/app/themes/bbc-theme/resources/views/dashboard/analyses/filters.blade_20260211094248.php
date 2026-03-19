@php
/**
* Analyses Filter Component
* Optimized for minimal mobile height and maximum desktop usability.
*/
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

// Status für UI-Indikatoren
$hasActiveFilters = $currentAsset || $currentMarket || $dateFrom || $dateTo;
@endphp

<script>
  window.AnalysisFilters = @json([
    'assets' => $assets
  ]);
</script>

{{--
    Container-Logik:
    Mobil: Flach, rahmenlos (nur unten), volle Breite (-mx-4)
    Desktop: Rounded-Box mit Schatten und kräftigem Rahmen
--}}
<form
  method="get"
  id="analysis-filters"
  data-filter-panel
  class="relative z-10 mb-6 md:mb-8
         bg-white/80 backdrop-blur-xl
         border-b border-brand-primary/20 md:border-2 md:border-brand-primary/70
         md:rounded-2xl
         md:shadow-[0_12px_40px_rgba(0,0,0,0.08)]
         overflow-hidden md:overflow-visible
         -mx-4 px-4 md:mx-0">

  {{--
      FILTER TRIGGER (Header)
      Mobil: Kompakt-Leiste (Höhe ca. 44px)
      Desktop: Großzügiger Header
  --}}
  <div class="flex items-center justify-between
            py-2 md:py-5
            cursor-pointer md:cursor-default"
    data-filter-toggle>

    <div class="flex items-center gap-2.5">
      {{-- Icon nur mobil für bessere Intuition --}}
      <div class="md:hidden p-1 bg-brand-primary/10 rounded text-brand-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
      </div>

      <span class="text-[11px] font-bold uppercase tracking-widest text-brand-primary/80 md:text-sm md:normal-case md:font-medium md:text-brand-primary">
        Filter
      </span>

      {{-- Aktiver Status-Badge --}}
      @if($hasActiveFilters)
      <span class="bg-brand-primary text-white text-[9px] px-1.5 py-0.5 rounded-full font-bold uppercase tracking-tighter animate-pulse">
        Aktiv
      </span>
      @endif
    </div>

    <button
      type="button"
      class="md:hidden flex items-center gap-1
           text-[11px] font-bold uppercase text-brand-primary/60">
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

  {{--
      FILTER CONTENT
      Einklappbar per JS (max-height Logik)
  --}}
  <div
    data-filter-content
    class="grid grid-cols-1 gap-4
           md:flex md:flex-wrap md:items-end
           pb-4 md:px-6 md:pb-5
           overflow-hidden transition-all duration-300 ease-in-out"
    style="max-height:0; opacity:0;">

    {{-- Asset Suche --}}
    <div class="relative w-full md:flex-1 md:min-w-[320px]">
      <label class="block text-[10px] font-bold tracking-wider text-slate-400 mb-1 uppercase">
        Asset
      </label>

      <input
        type="text"
        id="asset-search"
        autocomplete="off"
        placeholder="Name eingeben..."
        class="w-full rounded-xl
               border border-2 border-brand-primary/30
               bg-white/70
               px-4 py-2.5 text-sm
               focus:outline-none focus:ring-2 focus:ring-brand-primary/40 focus:border-brand-primary"
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
               max-h-64 overflow-y-auto hidden text-sm">
      </div>
    </div>

    {{-- Kategorie Select --}}
    <div class="w-full md:min-w-[220px] md:w-auto">
      <label class="block text-[10px] font-bold tracking-wider text-slate-400 mb-1 uppercase">
        Kategorie
      </label>

      <select
        name="market"
        class="w-full rounded-xl
               border border-2 border-brand-primary/30
               bg-white/70
               px-4 py-2.5 text-sm
               focus:outline-none focus:ring-2 focus:ring-brand-primary/30"
        @disabled($currentAsset)>

        <option value="">Alle Märkte</option>

        @foreach ($topMarkets as $market)
        <option
          value="{{ $market->slug }}"
          @selected($currentMarket===$market->slug)>
          {!! html_entity_decode($market->name) !!}
        </option>
        @endforeach

      </select>
    </div>

    {{-- Datums-Range --}}
    <div class="w-full md:min-w-[280px] md:w-auto">
      <label class="block text-[10px] font-bold tracking-wider text-slate-400 mb-1 uppercase">
        Zeitraum
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
          class="w-full bg-transparent px-3 py-2.5 text-xs focus:outline-none">

        <div class="h-4 w-px bg-brand-primary/20"></div>

        <input
          type="date"
          name="date_to"
          value="{{ $dateTo }}"
          class="w-full bg-transparent px-3 py-2.5 text-xs focus:outline-none">
      </div>
    </div>

    {{-- Actions --}}
    <div class="w-full md:w-auto md:ml-auto flex items-center justify-between gap-3 pt-2 md:pt-0">
      {{-- Reset --}}
      <a
        href="/dashboard"
        class="flex items-center justify-center
               h-10 w-10 rounded-xl
               text-brand-primary
               bg-brand-primary/10
               hover:bg-brand-primary/20 transition"
        title="Zurücksetzen">

        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          stroke-width="2.5"
          stroke-linecap="round"
          stroke-linejoin="round">
          <path d="M3 12a9 9 0 0 1 15-6" />
          <polyline points="18 3 18 9 12 9" />
          <path d="M21 12a9 9 0 0 1-15 6" />
          <polyline points="6 21 6 15 12 15" />
        </svg>
      </a>

      {{-- Submit mobil --}}
      <button type="submit"
        class="flex-1 md:hidden bg-brand-primary text-white py-2.5 rounded-xl text-xs font-bold shadow-md active:scale-95 transition">
        Filter anwenden
      </button>
    </div>

  </div>
</form>
