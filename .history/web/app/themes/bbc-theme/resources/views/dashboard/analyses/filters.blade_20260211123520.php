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

$hasActiveFilters = $currentAsset || $currentMarket || $dateFrom || $dateTo;
@endphp

<script>
  window.AnalysisFilters = @json(['assets' => $assets]);
</script>

<form
  method="get"
  id="analysis-filters"
  data-filter-panel
  class="relative z-10 mb-6
         bg-white/80 backdrop-blur-xl
         border border-brand-primary/40 {{-- Dünnerer Rahmen für weniger optische Höhe --}}
         rounded-xl md:rounded-2xl
         shadow-[0_8px_30px_rgba(0,0,0,0.04)]
         overflow-hidden md:overflow-visible">

  {{--
      HEADER: Maximale Höhenreduzierung
      Mobil: py-1.5 (nur ca. 6px Padding) -> Extrem flach eingeklappt
  --}}
  <div class="flex items-center justify-between
            px-3 py-1.5 md:px-6 md:py-4
            cursor-pointer md:cursor-default"
    data-filter-toggle>

    <div class="flex items-center gap-2.5">
      {{-- Icon aus Variante 3 für bessere Intuition --}}
      <div class="flex p-1 bg-brand-primary/10 rounded text-brand-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
      </div>

      <span class="text-[10px] md:text-sm font-bold uppercase tracking-widest text-brand-primary/80">
        Filter
      </span>

      @if($hasActiveFilters)
      <span class="flex h-1.5 w-1.5 rounded-full bg-brand-primary animate-pulse"></span>
      @endif
    </div>

    <button
      type="button"
      class="md:hidden flex items-center gap-1
             text-[10px] font-bold uppercase tracking-tight text-brand-primary/60">
      <span data-filter-label>anzeigen</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        class="w-3.5 h-3.5 transition-transform duration-300"
        data-filter-icon
        fill="none"
        stroke="currentColor"
        stroke-width="3">
        <path d="M6 9l6 6 6-6" />
      </svg>
    </button>
  </div>

  {{--
      CONTENT: Nur sichtbar wenn ausgeklappt
  --}}
  <div
    data-filter-content
    class="grid grid-cols-1 gap-3
           md:flex md:flex-wrap md:items-end
           px-3 pb-3 md:px-6 md:pb-5
           overflow-hidden transition-all duration-300 ease-in-out"
    style="max-height:0; opacity:0;">

    {{-- Asset Suche --}}
    <div class="relative w-full md:flex-1 md:min-w-[300px]">
      <label class="block text-[9px] font-bold text-slate-400 mb-1 uppercase tracking-wider">
        Asset
      </label>
      <input
        type="text"
        id="asset-search"
        autocomplete="off"
        placeholder="Asset suchen..."
        class="w-full rounded-lg border border-brand-primary/20 bg-white/50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary/30"
        value="{{ $currentAsset && isset($assetNameBySlug[$currentAsset]) ? $assetNameBySlug[$currentAsset] : '' }}">

      <input type="hidden" name="asset" id="asset-slug" value="{{ $currentAsset }}">

      <div id="asset-suggestions" class="absolute z-30 mt-1 w-full bg-white border border-brand-primary/20 rounded-lg shadow-xl max-h-48 overflow-y-auto hidden text-sm"></div>
    </div>

    {{-- Kategorie --}}
    <div class="w-full md:w-auto md:min-w-[200px]">
      <label class="block text-[9px] font-bold text-slate-400 mb-1 uppercase tracking-wider">
        Kategorie
      </label>
      <select
        name="market"
        class="w-full rounded-lg border border-brand-primary/20 bg-white/50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary/30"
        @disabled($currentAsset)>
        <option value="">Alle Kategorien</option>
        @foreach ($topMarkets as $market)
        <option value="{{ $market->slug }}" @selected($currentMarket===$market->slug)>
          {!! html_entity_decode($market->name) !!}
        </option>
        @endforeach
      </select>
    </div>

    {{-- Datum --}}
    <div class="w-full md:w-auto">
      <label class="block text-[9px] font-bold text-slate-400 mb-1 uppercase tracking-wider">
        Zeitraum
      </label>
      <div class="flex items-center rounded-lg border border-brand-primary/20 bg-white/50 overflow-hidden">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full bg-transparent px-2 py-2 text-xs focus:outline-none">
        <div class="h-4 w-px bg-brand-primary/10"></div>
        <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full bg-transparent px-2 py-2 text-xs focus:outline-none">
      </div>
    </div>

    {{-- Buttons --}}
    <div class="flex items-center gap-2 pt-2 md:pt-0 md:ml-auto">
      <a href="/dashboard" class="flex items-center justify-center h-9 w-9 rounded-lg bg-brand-primary/10 text-brand-primary hover:bg-brand-primary/20 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </a>
      <button type="submit" class="flex-1 md:hidden bg-brand-primary text-white py-2 rounded-lg text-xs font-bold uppercase tracking-wide shadow-md">
        Anwenden
      </button>
    </div>
  </div>
</form>
