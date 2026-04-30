{{--
Template Name: Conversion – Trial
--}}

@extends('layouts.conversion')

@section('content')

<section class="relative min-h-screen overflow-hidden">

  <div class="absolute inset-0">
    <img
      src="{{ Vite::asset('resources/images/landingpage/hero-bg.jpg') }}"
      alt=""
      class="h-full w-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/40 via-slate-950/60 to-slate-950/70"></div>
  </div>

  <div class="relative">
    <div class="container-content pt-24 pb-32">

      <div class="mx-auto max-w-5xl text-center">
        <h1 class="text-3xl font-semibold tracking-tight text-white lg:text-4xl">
          {!! pll__('Premium Analysen 14 Tage testen') !!}
        </h1>

        <p class="mt-4 text-lg text-slate-300">
          <span class="font-semibold text-brand-primary">4,99 €</span>
          {!! pll__('für 14 Tage Zugang') !!}
          · {!! pll__('danach 49,99 € / Monat') !!}
          · {!! pll__('jederzeit kündbar') !!}
        </p>

        <div class="mt-10 flex items-center justify-center gap-4 text-sm text-slate-400">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-primary text-white font-semibold">1</span>
          <span>{!! pll__('Konto erstellen') !!}</span>
          <span class="h-px w-16 bg-white/20"></span>
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/30 text-white font-semibold">2</span>
          <span>{!! pll__('Zahlungsdaten') !!}</span>
          <span class="h-px w-16 bg-white/20"></span>
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/30 text-white font-semibold">3</span>
          <span>{!! pll__('Fertig') !!}</span>
        </div>
      </div>

      <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)] lg:items-start">

        {{-- LEFT · FORM --}}
        <div class="rounded-3xl border border-white/10 bg-slate-950/45 backdrop-blur-xl p-6 md:p-8 shadow-2xl">

          <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M20 21a8 8 0 0 0-16 0"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>

            <div>
              <h2 class="text-xl font-semibold text-white">
                {!! pll__('Konto erstellen') !!}
              </h2>
              <p class="mt-1 text-sm text-slate-400">
                {!! pll__('Erstellen Sie Ihr Konto und starten Sie direkt den sicheren Stripe Checkout.') !!}
              </p>
            </div>
          </div>

          <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="mt-8 flex flex-col gap-4">
            <input type="hidden" name="action" value="dashboard_register_and_checkout">
            @php wp_nonce_field('dashboard_register_checkout', '_wpnonce'); @endphp

            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1">
                {!! pll__('E-Mail-Adresse') !!}
              </label>
              <input
                type="email"
                name="email"
                required
                autocomplete="email"
                placeholder="name@beispiel.de"
                class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1">
                {!! pll__('Passwort') !!}
              </label>
              <input
                type="password"
                name="password"
                required
                minlength="8"
                autocomplete="new-password"
                placeholder="{!! pll__('Mindestens 8 Zeichen') !!}"
                class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20">
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                  {!! pll__('Vorname') !!}
                </label>
                <input
                  type="text"
                  name="first_name"
                  autocomplete="given-name"
                  placeholder="Max"
                  class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                  {!! pll__('Nachname') !!}
                </label>
                <input
                  type="text"
                  name="last_name"
                  autocomplete="family-name"
                  placeholder="Mustermann"
                  class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20">
              </div>
            </div>

            <div class="grid grid-cols-1 gap-4 pt-2 md:grid-cols-3">
              <div class="flex gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3.5 19 6.6v5.1c0 4.5-2.9 7.7-7 9-4.1-1.3-7-4.5-7-9V6.6l7-3.1Z" />
                    <path d="m9.3 12 1.8 1.8 3.9-4.1" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-white">{!! pll__('Sicher') !!}</div>
                  <div class="text-xs text-slate-400">{!! pll__('DSGVO-konform') !!}</div>
                </div>
              </div>

              <div class="flex gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="6" width="14" height="14" rx="3" />
                    <path d="M8 4v4" />
                    <path d="M16 4v4" />
                    <path d="M8.5 12.8 11 15.3l4.7-5" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-white">{!! pll__('Kündbar') !!}</div>
                  <div class="text-xs text-slate-400">{!! pll__('Jederzeit möglich') !!}</div>
                </div>
              </div>

              <div class="flex gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="6" width="16" height="12" rx="3" />
                    <path d="M4 10h16" />
                    <path d="M8 15h3" />
                    <path d="M15.5 15h.01" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-white">{!! pll__('Stripe') !!}</div>
                  <div class="text-xs text-slate-400">{!! pll__('Sichere Zahlung') !!}</div>
                </div>
              </div>
            </div>

            <div class="border-t border-white/10 pt-6">
              <label class="flex items-start gap-3 text-sm text-slate-400">
                <input
                  type="checkbox"
                  name="terms"
                  value="1"
                  required
                  class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-brand-primary focus:ring-brand-primary">
                <span>
                  {!! pll__('Ich stimme den') !!}
                  <a href="/agb/" class="text-brand-primary hover:text-white">{!! pll__('AGB') !!}</a>
                  {!! pll__('und der') !!}
                  <a href="/datenschutz/" class="text-brand-primary hover:text-white">{!! pll__('Datenschutzerklärung') !!}</a>
                  {!! pll__('zu.') !!}
                </span>
              </label>
            </div>

            <button
              type="submit"
              class="w-full rounded-xl bg-brand-primary px-6 py-4 text-base font-semibold text-white shadow-lg shadow-brand-primary/20 transition hover:bg-brand-primaryHover">
              {!! pll__('Jetzt Testzugang freischalten') !!}
              <span class="block pt-1 text-xs font-normal text-white/80">
                {!! pll__('14 Tage für einmalig 4,99 € testen') !!}
              </span>
            </button>

            <p class="text-center text-xs text-slate-500">
              {!! pll__('Sofortige Freischaltung · jederzeit kündbar · sichere Zahlung über Stripe') !!}
            </p>
          </form>
        </div>

        {{-- RIGHT · PLAN CARD --}}
        <aside class="rounded-3xl border border-white/10 bg-slate-950/45 backdrop-blur-xl p-6 md:p-8 shadow-2xl">
          <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
              <path d="M5 16L3 6l5.5 4L12 4l3.5 6L21 6l-2 10H5zm0 2h14v2H5v-2z"></path>
            </svg>
          </div>

          <h2 class="mt-8 text-2xl font-semibold text-white">
            {!! pll__('Premium Analysen Zugang') !!}
          </h2>

          <div class="mt-6">
            <div class="text-brand-primary font-semibold">
              {!! pll__('Testphase') !!}
            </div>

            <div class="mt-2 flex items-end gap-3">
              <span class="text-3xl font-semibold text-brand-primary">4,99 €</span>
              <span class="pb-2 text-slate-300">{!! pll__('einmalig') !!}</span>
            </div>

            <p class="mt-2 text-sm text-slate-400">
              {!! pll__('Für 14 Tage Premium-Zugang inklusive sofortiger Freischaltung.') !!}
            </p>
          </div>

          <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="flex items-start gap-4">
              <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M8 7h8" />
                  <path d="M8 12h8" />
                  <path d="M8 17h5" />
                  <rect x="4" y="3" width="16" height="18" rx="3" />
                </svg>
              </div>

              <div>
                <div class="text-sm font-semibold text-white">
                  {!! pll__('Nach 14 Tagen') !!}
                </div>
                <div class="mt-1 text-sm text-slate-300">
                  <span class="font-semibold text-white">49,99 € / {!! pll__('Monat') !!}</span>
                  · {!! pll__('jederzeit kündbar') !!}
                </div>
                <p class="mt-2 text-xs leading-relaxed text-slate-500">
                  {!! pll__('Das reguläre Abo beginnt automatisch nach Ablauf des Testzeitraums, sofern Sie nicht vorher kündigen.') !!}
                </p>
              </div>
            </div>
          </div>

          <ul class="mt-8 space-y-4 text-sm text-slate-300">
            <li class="flex gap-3">
              <span class="text-brand-primary">✓</span>
              <span>{!! pll__('Alle Premium Analysen') !!}</span>
            </li>
            <li class="flex gap-3">
              <span class="text-brand-primary">✓</span>
              <span>{!! pll__('Tägliche Marktupdates') !!}</span>
            </li>
            <li class="flex gap-3">
              <span class="text-brand-primary">✓</span>
              <span>{!! pll__('Audio- und Video-Inhalte') !!}</span>
            </li>
            <li class="flex gap-3">
              <span class="text-brand-primary">✓</span>
              <span>{!! pll__('Historische Analysen') !!}</span>
            </li>
            <li class="flex gap-3">
              <span class="text-brand-primary">✓</span>
              <span>{!! pll__('Zugriff auf das Dashboard') !!}</span>
            </li>
          </ul>

          <div class="mt-8 border-t border-white/10 pt-6">
            <div class="flex gap-3">
              <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                  <path d="M9.5 12l2 2 3-4" />
                </svg>
              </div>
              <div>
                <div class="font-semibold text-white">
                  {!! pll__('Sichere Zahlung & sofortiger Zugang') !!}
                </div>
                <p class="mt-1 text-sm text-slate-400">
                  {!! pll__('Zahlungsdaten werden ausschließlich über Stripe verarbeitet. Ihr Zugang wird nach erfolgreicher Zahlung automatisch freigeschaltet.') !!}
                </p>
              </div>
            </div>
          </div>
        </aside>

      </div>

    </div>
  </div>

  <div class="relative">
    @include('sections.conversion-footer')
  </div>

</section>

@endsection
