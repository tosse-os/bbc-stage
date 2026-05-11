{{--
Template Name: Conversion – Trial
--}}

@extends('layouts.conversion')

@section('content')

{{-- Plan Configuration - Zentrale Paketdaten für Kacheln, Summary und CTA --}}
@php
$trialPlans = [
'trial' => [
'badge' => pll__('Zum Kennenlernen'),
'title' => pll__('Testzugang'),
'price' => '4,99 €',
'interval' => pll__('14 Tage Premium-Zugang'),
'headline' => pll__('Teste, bevor du Geld verlierst.'),
'description' => pll__('Prüfen Sie unsere Analysen, Setups und Einschätzungen, bevor Sie ein volles Abo abschließen.'),
'button' => pll__('14 Tage testen'),
'summary_title' => pll__('Ihr Premium-Testzugang'),
'summary_label' => pll__('Testphase'),
'summary_interval' => pll__('einmalig'),
'summary_description' => pll__('14 Tage Zugriff auf alle Premium-Analysen – inklusive sofortiger Freischaltung.'),
'after_title' => pll__('Nach 14 Tagen'),
'after_price' => '49,99 € / ' . pll__('Monat'),
'after_suffix' => pll__('jederzeit kündbar'),
'after_note' => pll__('Das reguläre Abo beginnt automatisch nach Ablauf des Testzeitraums, sofern Sie nicht vorher kündigen.'),
'cta' => pll__('Jetzt Testzugang freischalten'),
'cta_subline' => pll__('14 Tage Premium-Zugang für einmalig 4,99 €'),
'features' => [
pll__('Aktuelle Marktanalysen'),
pll__('Einblick in Denkweise & Setups'),
pll__('Konkrete Setups & Entscheidungs-Zonen'),
pll__('Zugriff auf das Dashboard'),
],
],
'basis' => [
'badge' => pll__('Empfohlen'),
'title' => pll__('Basis'),
'price' => '49,99 €',
'interval' => pll__('pro Monat'),
'headline' => pll__('Treffen Sie bessere Entscheidungen am Markt.'),
'description' => pll__('Klare Analysen, nachvollziehbare Szenarien und konkrete Zonen, an denen Entscheidungen getroffen werden.'),
'button' => pll__('Basis starten'),
'summary_title' => pll__('Ihr Basis-Zugang'),
'summary_label' => pll__('Basis'),
'summary_interval' => pll__('pro Monat'),
'summary_description' => pll__('Monatlicher Zugriff auf Premium-Analysen, Marktupdates und das Dashboard.'),
'after_title' => pll__('Monatliches Abo'),
'after_price' => '49,99 € / ' . pll__('Monat'),
'after_suffix' => pll__('jederzeit kündbar'),
'after_note' => pll__('Das Basis-Abo läuft monatlich und ist jederzeit kündbar.'),
'cta' => pll__('Basis starten'),
'cta_subline' => pll__('49,99 € / Monat · jederzeit kündbar'),
'features' => [
pll__('Tägliche Marktanalysen'),
pll__('Krypto, Aktien & weitere Märkte'),
pll__('Klare Szenarien'),
pll__('Konkrete Preis- und Entscheidungszonen'),
pll__('Zugriff auf das Dashboard'),
],
],
'pro' => [
'badge' => pll__('Maximaler Vorsprung'),
'title' => pll__('Pro'),
'price' => '79,99 €',
'interval' => pll__('pro Monat'),
'headline' => pll__('Seien Sie früher dran als der Markt.'),
'description' => pll__('Alle Basis-Inhalte plus priorisierte Updates, exklusive Setups und zusätzliche Informationen über Märkte.'),
'button' => pll__('Pro freischalten'),
'summary_title' => pll__('Ihr Pro-Zugang'),
'summary_label' => pll__('Pro'),
'summary_interval' => pll__('pro Monat'),
'summary_description' => pll__('Erweiterter Premium-Zugang mit priorisierten Updates, exklusiven Setups und Pro-Inhalten.'),
'after_title' => pll__('Monatliches Pro-Abo'),
'after_price' => '79,99 € / ' . pll__('Monat'),
'after_suffix' => pll__('jederzeit kündbar'),
'after_note' => pll__('Das Pro-Abo läuft monatlich und ist jederzeit kündbar.'),
'cta' => pll__('Pro freischalten'),
'cta_subline' => pll__('79,99 € / Monat · jederzeit kündbar'),
'features' => [
pll__('Alles aus dem Basis-Paket'),
pll__('Schnellere & priorisierte Updates'),
pll__('Exklusive Setups & Marktlevels'),
pll__('Zusätzliche Pro-Informationen'),
pll__('Zugriff auf das Dashboard'),
],
],
];

$requestedPlan = request()->get('plan');
$defaultPlanKey = isset($trialPlans[$requestedPlan]) ? $requestedPlan : 'trial';
$defaultPlan = $trialPlans[$defaultPlanKey];

$trialError = request()->get('error');

$trialMessages = [
'invalid_request' => pll__('Die Anfrage war ungültig. Bitte versuchen Sie es erneut.'),
'email' => pll__('Bitte geben Sie eine gültige E-Mail-Adresse ein.'),
'weak_password' => pll__('Das Passwort muss mindestens 8 Zeichen lang sein.'),
'exists' => pll__('Diese E-Mail ist bereits registriert. Bitte melden Sie sich an. Danach können Sie Ihr Abo im Billing-Bereich starten.'),
'account_exists' => pll__('Diese E-Mail ist bereits registriert. Bitte melden Sie sich an. Danach können Sie Ihr Abo im Billing-Bereich starten.'),
'terms' => pll__('Bitte akzeptieren Sie die AGB und die Datenschutzerklärung.'),
'create_failed' => pll__('Der Account konnte nicht erstellt werden. Bitte versuchen Sie es erneut.'),
'stripe_sdk_missing' => pll__('Stripe ist technisch noch nicht vollständig eingerichtet.'),
'stripe_not_configured' => pll__('Stripe ist noch nicht vollständig konfiguriert.'),
'stripe_secret_missing' => pll__('Der Stripe Secret Key fehlt.'),
'stripe_price_basis_missing' => pll__('Die Stripe Price ID für das Basis-Abo fehlt.'),
'stripe_price_pro_missing' => pll__('Die Stripe Price ID für das Pro-Abo fehlt.'),
'stripe_trial_fee_missing' => pll__('Die Stripe Price ID für die Trial-Gebühr fehlt.'),
'stripe_checkout_failed' => pll__('Stripe Checkout konnte nicht gestartet werden. Bitte versuchen Sie es erneut.'),
'user_missing' => pll__('Der Benutzer konnte nicht ermittelt werden. Bitte melden Sie sich erneut an.'),
];
@endphp

{{-- Conversion - Hauptbereich mit Background, Inhalt und Footer --}}
<section class="relative min-h-screen overflow-hidden">

  {{-- Background - Hero-Bild mit dunklem Overlay --}}
  <div class="absolute inset-0">
    <img
      src="{{ Vite::asset('resources/images/landingpage/hero-bg.jpg') }}"
      alt=""
      class="h-full w-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/40 via-slate-950/60 to-slate-950/70"></div>
  </div>

  <div class="relative">
    <div class="container-content pt-24 pb-32">

      {{-- Hero - Headline, Preisanker und Trust-Hinweise --}}
      <div class="mx-auto max-w-5xl text-center">
        <h1 class="text-3xl font-semibold tracking-tight text-white lg:text-4xl">
          {{ pll__('Premium-Analysen 14 Tage testen') }}
        </h1>

        <!-- <p class="mt-4 text-lg text-slate-300">
          <span class="font-semibold text-brand-primary">4,99 €</span>
          {{ pll__('für 14 Tage Zugang') }}
          · {{ pll__('danach 49,99 € / Monat') }}
          · {{ pll__('jederzeit kündbar') }}
        </p> -->

        <div class="mt-4 flex items-center justify-center gap-4 text-sm text-slate-400">
          <!-- <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-primary text-white font-semibold">1</span> -->
          <span>{{ pll__('In 2 Minuten freigeschaltet') }}</span>
          <span class="h-px w-16 bg-white/20 opacity-80"></span>
          <!-- <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/30 text-white font-semibold">2</span> -->
          <span>{{ pll__('Sichere Zahlung über Stripe') }}</span>
          <span class="h-px w-16 bg-white/20 opacity-80"></span>
          <!-- <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/30 text-white font-semibold">3</span> -->
          <span>{{ pll__('Sofortiger Zugriff') }}</span>
        </div>

        @if($trialError)
        <div class="mx-auto mt-8 max-w-3xl rounded-2xl border border-red-400/20 bg-red-500/10 px-5 py-4 text-center text-sm text-red-200">
          {{ $trialMessages[$trialError] ?? pll__('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.') }}

          @if(in_array($trialError, ['exists', 'account_exists'], true))
          <div class="mt-3">
            <a href="/dashboard-login?plan={{ $defaultPlanKey }}" class="font-semibold text-brand-primary hover:text-white">
              {{ pll__('Jetzt einloggen') }}
            </a>
          </div>
          @endif
        </div>
        @endif
      </div>

      {{-- Plan Selector - Drei auswählbare Paket-Kacheln --}}
      <div class="mt-14 grid grid-cols-1 gap-6 md:grid-cols-3" data-plan-selector>
        @foreach($trialPlans as $planKey => $plan)
        @php
        $isActivePlan = $planKey === $defaultPlanKey;
        @endphp

        <button
          type="button"
          data-plan-card="{{ $planKey }}"
          style="{{ $isActivePlan ? 'box-shadow: 0 0 0 1px rgba(34,211,238,.55), 0 0 42px rgba(34,211,238,.36);' : '' }}"
          class="group relative flex h-full cursor-pointer flex-col rounded-3xl border bg-slate-950/45 p-5 text-left backdrop-blur-xl transition duration-300 {{ $isActivePlan ? 'scale-[1.015] border-brand-primary bg-brand-primary/10 ring-2 ring-brand-primary/50' : 'border-white/10 shadow-2xl hover:-translate-y-1 hover:border-brand-primary/60 hover:bg-white/5' }}">

          @if($planKey === 'basis')
          <div class="absolute -top-4 left-1/2 -translate-x-1/2 rounded-full bg-brand-primary px-4 py-1 text-[10px] font-bold uppercase tracking-widest text-slate-950">
            {{ pll__('Empfohlen') }}
          </div>
          @endif

          <div class="flex items-center justify-between gap-4">
            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[11px] text-slate-300">
              {{ $plan['badge'] }}
            </span>

            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-brand-primary/15 text-brand-primary">
              @if($planKey === 'trial')
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2 15 8l6 .9-4.5 4.4 1.1 6.2L12 16.6 6.4 19.5l1.1-6.2L3 8.9 9 8l3-6Z"></path>
              </svg>
              @elseif($planKey === 'basis')
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 6h16v4H4V6Zm0 6h10v6H4v-6Zm12 0h4v6h-4v-6Z"></path>
              </svg>
              @else
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M5 16 3 6l5.5 4L12 4l3.5 6L21 6l-2 10H5Zm0 2h14v2H5v-2Z"></path>
              </svg>
              @endif
            </span>
          </div>

          <div class="mt-4">
            <h3 class="text-2xl font-semibold text-white">
              {{ $plan['title'] }}
            </h3>

            <div class="mt-2">
              <span class="text-2xl font-bold tracking-tight text-white">
                {{ $plan['price'] }}
              </span>
              <div class="mt-1 text-sm text-slate-400">
                {{ $plan['interval'] }}
              </div>
            </div>

            <p class="mt-4 text-xl font-medium leading-tight text-white" style="min-height: 3.5rem;">
              {{ $plan['headline'] }}
            </p>

            <p class="mt-2 text-sm leading-relaxed text-slate-400">
              {{ $plan['description'] }}
            </p>
          </div>

          <div class="mt-5 flex flex-1 flex-col">
            <ul class="space-y-2 text-sm text-slate-300">
              @foreach(array_slice($plan['features'], 0, 4) as $feature)
              <li class="flex gap-2">
                <span class="text-brand-primary">✓</span>
                <span>{{ $feature }}</span>
              </li>
              @endforeach
            </ul>

            <span
              data-plan-card-button
              class="mt-6 inline-flex w-full items-center justify-center rounded-full px-5 py-3 text-sm font-semibold transition {{ $isActivePlan ? 'bg-brand-primary text-slate-950 shadow-lg shadow-brand-primary/30' : 'bg-white text-slate-950 group-hover:bg-brand-primary' }}">
              {{ $plan['button'] }}
            </span>
          </div>
        </button>
        @endforeach
      </div>

      {{-- Checkout Area - Formular links, Summary rechts --}}
      <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)] lg:items-start" data-checkout-area>

        {{-- Registration Form - Accountdaten und Planübergabe --}}
        <div class="rounded-3xl border border-white/10 bg-slate-950/45 backdrop-blur-xl p-6 md:p-8 shadow-2xl">

          <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
                <path d="M5 16L3 6l5.5 4L12 4l3.5 6L21 6l-2 10H5zm0 2h14v2H5v-2z"></path>
              </svg>
            </div>

            <div>
              <h2 class="text-xl font-semibold text-white">
                {{ pll__('Premium-Zugang starten') }}
              </h2>
              <p class="mt-1 text-sm text-slate-400">
                {{ pll__('Zugangsdaten anlegen. Sicher bezahlen. Direkt starten.') }}
              </p>
            </div>
          </div>

          <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="mt-8 flex flex-col gap-4" data-trial-form>
            <input type="hidden" name="action" value="dashboard_register_and_checkout">
            <input type="hidden" name="plan" value="{{ $defaultPlanKey }}" data-selected-plan>
            <input type="hidden" name="name" value="" data-full-name>
            @php wp_nonce_field('dashboard_register_checkout', '_wpnonce'); @endphp

            {{-- Account Fields - E-Mail, Passwort und Name --}}
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1">
                {{ pll__('E-Mail-Adresse') }}
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
                {{ pll__('Passwort') }}
              </label>
              <input
                type="password"
                name="password"
                required
                minlength="8"
                autocomplete="new-password"
                placeholder="{{ pll__('Mindestens 8 Zeichen') }}"
                class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20">
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                  {{ pll__('Vorname') }}
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
                  {{ pll__('Nachname') }}
                </label>
                <input
                  type="text"
                  name="last_name"
                  autocomplete="family-name"
                  placeholder="Mustermann"
                  class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20">
              </div>
            </div>

            {{-- Trust Points - Sicherheit, Kündbarkeit und Stripe --}}
            <div class="grid grid-cols-1 gap-4 pt-2 md:grid-cols-3">
              <div class="flex gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3.5 19 6.6v5.1c0 4.5-2.9 7.7-7 9-4.1-1.3-7-4.5-7-9V6.6l7-3.1Z" />
                    <path d="m9.3 12 1.8 1.8 3.9-4.1" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-white">{{ pll__('Sicher') }}</div>
                  <div class="text-xs text-slate-400">{{ pll__('DSGVO-konform') }}</div>
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
                  <div class="text-sm font-semibold text-white">{{ pll__('Kündbar') }}</div>
                  <div class="text-xs text-slate-400">{{ pll__('Jederzeit möglich') }}</div>
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
                  <div class="text-sm font-semibold text-white">{{ pll__('Stripe') }}</div>
                  <div class="text-xs text-slate-400">{{ pll__('Sichere Zahlung') }}</div>
                </div>
              </div>
            </div>

            {{-- Legal Consent - Zustimmung zu AGB und Datenschutz --}}
            <div class="border-t border-white/10 pt-6">
              <label class="flex items-start gap-3 text-sm text-slate-400">
                <input
                  type="checkbox"
                  name="terms"
                  value="1"
                  required
                  class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-brand-primary focus:ring-brand-primary">
                <span>
                  {{ pll__('Ich stimme den') }}
                  <a href="/agb/" class="text-brand-primary hover:text-white">{{ pll__('AGB') }}</a>
                  {{ pll__('und der') }}
                  <a href="/datenschutz/" class="text-brand-primary hover:text-white">{{ pll__('Datenschutzerklärung') }}</a>
                  {{ pll__('zu.') }}
                </span>
              </label>
            </div>

            {{-- Form CTA - Dynamischer Button je Paket --}}
            <button
              type="submit"
              class="w-full rounded-xl bg-brand-primary px-6 py-4 text-base font-semibold text-white shadow-lg shadow-brand-primary/20 transition hover:bg-brand-primaryHover">
              <span data-plan-submit-label>{{ $defaultPlan['cta'] }}</span>
              <span class="block pt-1 text-xs font-normal text-white/80" data-plan-submit-subline>
                {{ $defaultPlan['cta_subline'] }}
              </span>
            </button>

            <p class="text-center text-xs text-slate-500">
              {{ pll__('Sofortige Freischaltung · jederzeit kündbar · DSGVO-konform') }}
            </p>
          </form>
        </div>

        {{-- Plan Summary - Rechte Kachel mit gewähltem Paket --}}
        <aside class="rounded-3xl border border-white/10 bg-slate-950/45 backdrop-blur-xl p-6 md:p-8 shadow-2xl">
          <!-- <div class="flex h-7 w-7 items-center justify-center rounded-2xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
              <path d="M5 16L3 6l5.5 4L12 4l3.5 6L21 6l-2 10H5zm0 2h14v2H5v-2z"></path>
            </svg>
          </div> -->

          <div class="flex items-start gap-4">
            <!-- <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
                <path d="M5 16L3 6l5.5 4L12 4l3.5 6L21 6l-2 10H5zm0 2h14v2H5v-2z"></path>
              </svg>
            </div> -->

            <div>
              <h2 class="text-xl font-semibold text-white" data-plan-summary-title>
                {{ $defaultPlan['summary_title'] }}
              </h2>
            </div>
          </div>

          {{-- Selected Plan Summary - Preis, Laufzeit und Beschreibung --}}
          <div class="mt-6">
            <div class="text-brand-primary font-semibold" data-plan-summary-label>
              {{ $defaultPlan['summary_label'] }}
            </div>

            <div class="mt-2 flex items-end gap-3">
              <span class="text-3xl font-semibold text-brand-primary" data-plan-summary-price>{{ $defaultPlan['price'] }}</span>
              <span class="pb-2 text-slate-300" data-plan-summary-interval>{{ $defaultPlan['summary_interval'] }}</span>
            </div>

            <p class="mt-2 text-sm text-slate-400" data-plan-summary-description>
              {{ $defaultPlan['summary_description'] }}
            </p>
          </div>

          {{-- Billing Info - Folgepreis und Kündigungshinweis --}}
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
                <div class="text-sm font-semibold text-white" data-plan-after-title>
                  {{ $defaultPlan['after_title'] }}
                </div>
                <div class="mt-1 text-sm text-slate-300">
                  <span class="font-semibold text-white" data-plan-after-price>{{ $defaultPlan['after_price'] }}</span>
                  · <span data-plan-after-suffix>{{ $defaultPlan['after_suffix'] }}</span>
                </div>
                <p class="mt-2 text-xs leading-relaxed text-slate-500" data-plan-after-note>
                  {{ $defaultPlan['after_note'] }}
                </p>
              </div>
            </div>
          </div>

          {{-- Plan Features - Leistungsumfang des gewählten Pakets --}}
          <ul class="mt-8 space-y-4 text-sm text-slate-300" data-plan-summary-features>
            @foreach($defaultPlan['features'] as $feature)
            <li class="flex gap-3">
              <span class="text-brand-primary">✓</span>
              <span>{{ $feature }}</span>
            </li>
            @endforeach
          </ul>

          <!-- <div class="mt-8 border-t border-white/10 pt-6">
            <div class="flex gap-3">
              <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-primary/15 text-brand-primary shadow-[0_0_24px_rgba(34,211,238,0.18)]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                  <path d="M9.5 12l2 2 3-4" />
                </svg>
              </div>
              <div>
                <div class="font-semibold text-white">
                  {{ pll__('Sichere Zahlung & sofortiger Zugang') }}
                </div>
                <p class="mt-1 text-sm text-slate-400">
                  {{ pll__('Zahlungsdaten werden ausschließlich über Stripe verarbeitet. Ihr Zugang wird nach erfolgreicher Zahlung automatisch freigeschaltet.') }}
                </p>
              </div>
            </div>
          </div> -->
        </aside>

      </div>


    </div>
  </div>

  {{-- Footer - Conversion Footer --}}
  <div class="relative">
    @include('sections.conversion-footer')
  </div>

</section>

{{-- JS Data - Paketdaten für das Script --}}
<script>
  window.trialPlansData = @json($trialPlans);
</script>

{{-- Inline Script - Paketwechsel und Namens-Sync --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var plans = window.trialPlansData || {}
    var cards = document.querySelectorAll('[data-plan-card]')
    var checkoutArea = document.querySelector('[data-checkout-area]')
    var selectedPlanInput = document.querySelector('[data-selected-plan]')
    var summaryTitle = document.querySelector('[data-plan-summary-title]')
    var summaryLabel = document.querySelector('[data-plan-summary-label]')
    var summaryPrice = document.querySelector('[data-plan-summary-price]')
    var summaryInterval = document.querySelector('[data-plan-summary-interval]')
    var summaryDescription = document.querySelector('[data-plan-summary-description]')
    var afterTitle = document.querySelector('[data-plan-after-title]')
    var afterPrice = document.querySelector('[data-plan-after-price]')
    var afterSuffix = document.querySelector('[data-plan-after-suffix]')
    var afterNote = document.querySelector('[data-plan-after-note]')
    var summaryFeatures = document.querySelector('[data-plan-summary-features]')
    var submitLabel = document.querySelector('[data-plan-submit-label]')
    var submitSubline = document.querySelector('[data-plan-submit-subline]')
    var form = document.querySelector('[data-trial-form]')
    var firstName = form ? form.querySelector('[name="first_name"]') : null
    var lastName = form ? form.querySelector('[name="last_name"]') : null
    var fullName = form ? form.querySelector('[data-full-name]') : null

    var idleCardClasses = ['border-white/10', 'hover:border-brand-primary/60', 'hover:-translate-y-1', 'hover:bg-white/5', 'hover:shadow-brand-primary/20']
    var selectedCardClasses = ['scale-[1.015]', 'border-brand-primary', 'bg-brand-primary/10', 'shadow-brand-primary/30', 'ring-2', 'ring-brand-primary/50']
    var selectedButtonClasses = ['bg-brand-primary', 'text-slate-950', 'shadow-lg', 'shadow-brand-primary/30']
    var idleButtonClasses = ['bg-white', 'text-slate-950', 'group-hover:bg-brand-primary']

    function escapeHtml(value) {
      var div = document.createElement('div')
      div.textContent = value || ''
      return div.innerHTML
    }

    function renderFeatures(features) {
      if (!summaryFeatures) return

      summaryFeatures.innerHTML = features.map(function(feature) {
        return '<li class="flex gap-3"><span class="text-brand-primary">✓</span><span>' + escapeHtml(feature) + '</span></li>'
      }).join('')
    }

    function setActiveCard(planKey) {
      cards.forEach(function(card) {
        var isActive = card.dataset.planCard === planKey
        var button = card.querySelector('[data-plan-card-button]')

        selectedCardClasses.forEach(function(className) {
          card.classList.toggle(className, isActive)
        })

        idleCardClasses.forEach(function(className) {
          card.classList.toggle(className, !isActive)
        })

        card.style.boxShadow = isActive ?
          '0 0 0 1px rgba(34,211,238,.55), 0 0 42px rgba(34,211,238,.36)' :
          ''

        if (button) {
          selectedButtonClasses.forEach(function(className) {
            button.classList.toggle(className, isActive)
          })

          idleButtonClasses.forEach(function(className) {
            button.classList.toggle(className, !isActive)
          })
        }
      })
    }

    function updatePlan(planKey) {
      var plan = plans[planKey]
      if (!plan) return

      if (selectedPlanInput) selectedPlanInput.value = planKey
      if (summaryTitle) summaryTitle.textContent = plan.summary_title
      if (summaryLabel) summaryLabel.textContent = plan.summary_label
      if (summaryPrice) summaryPrice.textContent = plan.price
      if (summaryInterval) summaryInterval.textContent = plan.summary_interval
      if (summaryDescription) summaryDescription.textContent = plan.summary_description
      if (afterTitle) afterTitle.textContent = plan.after_title
      if (afterPrice) afterPrice.textContent = plan.after_price
      if (afterSuffix) afterSuffix.textContent = plan.after_suffix
      if (afterNote) afterNote.textContent = plan.after_note
      if (submitLabel) submitLabel.textContent = plan.cta
      if (submitSubline) submitSubline.textContent = plan.cta_subline

      renderFeatures(plan.features || [])
      setActiveCard(planKey)
    }

    function scrollToCheckout() {
      if (!checkoutArea) return

      var offset = 140
      var target = checkoutArea.getBoundingClientRect().top + window.pageYOffset - offset
      var start = window.pageYOffset
      var distance = target - start
      var duration = 900
      var startTime = null

      function ease(t) {
        return t < 0.5 ?
          2 * t * t :
          1 - Math.pow(-2 * t + 2, 2) / 2
      }

      function animateScroll(currentTime) {
        if (!startTime) startTime = currentTime

        var elapsed = currentTime - startTime
        var progress = Math.min(elapsed / duration, 1)

        window.scrollTo(0, start + distance * ease(progress))

        if (progress < 1) {
          requestAnimationFrame(animateScroll)
        }
      }

      requestAnimationFrame(animateScroll)
    }

    function syncFullName() {
      if (!fullName) return

      var nameParts = []

      if (firstName && firstName.value.trim()) {
        nameParts.push(firstName.value.trim())
      }

      if (lastName && lastName.value.trim()) {
        nameParts.push(lastName.value.trim())
      }

      fullName.value = nameParts.join(' ')
    }

    cards.forEach(function(card) {
      card.addEventListener('click', function(event) {
        updatePlan(card.dataset.planCard)

        if (event.target.closest('[data-plan-card-button]')) {
          scrollToCheckout()
        }
      })
    })

    if (firstName) {
      firstName.addEventListener('input', syncFullName)
    }

    if (lastName) {
      lastName.addEventListener('input', syncFullName)
    }

    if (form) {
      form.addEventListener('submit', syncFullName)
    }

    syncFullName()
  })
</script>

@endsection
