<div class="flex h-full flex-col rounded-2xl bg-brand-dark p-8 shadow-[0_20px_60px_rgba(0,0,0,0.45)]">
  <p class="text-xs font-semibold uppercase tracking-wide text-brand-primary/80">
    Free 7-Day Trial
  </p>

  <h3 class="mt-2 text-xl font-semibold text-white">
    Full Access Plan
  </h3>

  <div class="mt-4 flex items-end gap-2">
    <span class="text-4xl font-semibold text-brand-primary">€59</span>
    <span class="pb-1 text-sm text-slate-400">/ Monat</span>
  </div>

  <p class="mt-1 text-sm text-slate-400">
    danach €59 pro Monat
  </p>

  <ul class="mt-6 space-y-3 text-sm text-slate-200">
    @foreach([
    'Echtzeit Marktanalysen',
    'Strukturierte Handelssignale',
    'Aktien, Krypto & Forex',
    '45 Börsenplätze weltweit',
    'Tägliche Updates & Alerts'
    ] as $item)
    <li class="flex items-start gap-3">
      <svg class="mt-1 h-4 w-4 shrink-0 text-brand-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
      </svg>
      <span>{{ $item }}</span>
    </li>
    @endforeach
  </ul>

  <p class="mt-auto pt-6 text-xs text-slate-400">
    Jederzeit kündbar. Keine Gebühren während des Testzeitraums.
  </p>
</div>
