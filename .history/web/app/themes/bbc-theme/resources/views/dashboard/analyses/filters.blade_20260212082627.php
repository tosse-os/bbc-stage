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
$terms = get_terms(['taxonomy' => 'analysis_market', 'hide_empty' => false]);

foreach ($terms as $term) {
if ($term->parent !== 0) {
$assets[] = ['name' => html_entity_decode($term->name), 'slug' => $term->slug];
$assetNameBySlug[$term->slug] = html_entity_decode($term->name);
}
}

$hasActiveFilters = $currentAsset || $currentMarket || $dateFrom || $dateTo;
@endphp

<script>
  window.AnalysisFilters = @json(['assets' => $assets]);
</script>

{{-- Sticky Wrapper: Mobil sticky, Desktop statisch --}}
<div class="dashboard-filter-bar sticky top-0 z-40 -mx-4 px-4 md:relative md:mx-0 md:px-0 md:z-10 mb-6" id="filter-main-container">
  <form
    method="get"
    id="analysis-filters"
    data-filter-panel
    class="bg-white/90 backdrop-blur-xl border border-brand-primary/30 rounded-b-2xl md:rounded-2xl shadow-sm overflow-visible md:overflow-visible transition-all">

    {{-- HEADER: Nur mobil sichtbar, extrem flach --}}
    <div class="flex items-center justify-between px-3 py-1.5 md:hidden cursor-pointer" data-filter-toggle>
      <div class="flex items-center gap-2">
        <div class="p-1 bg-brand-primary/10 rounded text-brand-primary">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 00.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
        </div>
        <span class="text-[10px] font-bold uppercase tracking-widest text-brand-primary/80">Filter</span>
        @if($hasActiveFilters)<span class="h-1.5 w-1.5 rounded-full bg-brand-primary animate-pulse"></span>@endif
      </div>
      <div class="flex items-center gap-1 text-[10px] font-bold uppercase text-brand-primary/60">
        <span data-filter-label>anzeigen</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transition-transform" data-filter-icon fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
          <path d="M6 9l6 6 6-6" />
        </svg>
      </div>
    </div>

    {{-- CONTENT BEREICH --}}
    <div
      data-filter-content
      class="grid grid-cols-1 gap-2 px-3 pb-3 md:p-4
               md:flex md:flex-row md:items-end md:gap-4
               overflow-hidden transition-all duration-300">

      {{-- Asset Suche --}}
      <div class="relative flex-1 md:min-w-[250px]">
        <label class="block text-[9px] font-bold text-slate-400 mb-0.5 uppercase tracking-wider">Asset</label>
        <input type="text" id="asset-search" autocomplete="off" placeholder="Suchen..."
          class="w-full rounded-lg border border-brand-primary/20 bg-white/50 px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-primary/30 outline-none"
          value="{{ $currentAsset && isset($assetNameBySlug[$currentAsset]) ? $assetNameBySlug[$currentAsset] : '' }}">
        <input type="hidden" name="asset" id="asset-slug" value="{{ $currentAsset }}">
        <div id="asset-suggestions" class="absolute z-30 mt-1 w-full bg-white border border-brand-primary/20 rounded-lg shadow-xl max-h-48 overflow-y-auto hidden"></div>
      </div>

      {{-- Kategorie --}}
      <div class="w-full md:w-[200px]">
        <label class="block text-[9px] font-bold text-slate-400 mb-0.5 uppercase tracking-wider">Kategorie</label>
        <select name="market" class="w-full rounded-lg border border-brand-primary/20 bg-white/50 px-3 py-1.5 text-sm outline-none" @disabled($currentAsset)>
          <option value="">Alle Kategorien</option>
          @foreach ($topMarkets as $market)
          <option value="{{ $market->slug }}" @selected($currentMarket===$market->slug)>{!! html_entity_decode($market->name) !!}</option>
          @endforeach
        </select>
      </div>

      {{-- Zeitraum --}}
      <div class="w-full md:w-[260px]">
        <label class="block text-[9px] font-bold text-slate-400 mb-0.5 uppercase tracking-wider">Zeitraum</label>
        <div class="flex items-center rounded-lg border border-brand-primary/20 bg-white/50 overflow-hidden">
          <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full bg-transparent px-3 py-1.5 text-sm outline-none">
          <div class="h-4 w-px bg-brand-primary/10"></div>
          <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full bg-transparent px-3 py-1.5 text-sm outline-none">
        </div>
      </div>

      {{-- Buttons --}}
      <div class="flex items-center gap-2 pt-1 md:pt-0">
        <a href="/dashboard" class="flex items-center justify-center h-8 w-8 rounded-lg bg-brand-primary/10 text-brand-primary hover:bg-brand-primary/20 transition" title="Reset">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </a>
        <button type="submit" class="flex-1 md:hidden bg-brand-primary text-white py-1.5 rounded-lg text-[10px] font-bold uppercase">Anwenden</button>
      </div>
    </div>
  </form>
</div>
