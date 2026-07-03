{{--
Template Name: Dashboard Login
--}}
@extends('layouts.dashboard-auth')

@section('content')

@php
$checkoutPlan = request()->get('plan');
$checkoutPlan = in_array($checkoutPlan, ['trial', 'basis', 'pro'], true) ? $checkoutPlan : '';
$loginError = request()->get('error');

$loginMessages = [
'account_exists' => 'Diese E-Mail ist bereits registriert. Bitte melden Sie sich an. Danach können Sie Ihr Abo im Billing-Bereich starten.',
'too_many_attempts' => 'Zu viele Login-Versuche. Bitte versuchen Sie es später erneut.',
'invalid_request' => 'Die Anfrage war ungültig. Bitte versuchen Sie es erneut.',
'1' => 'Login fehlgeschlagen. Bitte prüfen Sie Ihre Daten.',
];
@endphp

<section class="relative w-full max-w-md mx-auto px-4">
  <div class="backdrop-blur-xl bg-white/[0.03] border border-white/10 rounded-2xl px-8 py-10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] ring-1 ring-white/10 overflow-hidden relative">

    {{-- 1. Logo & Welcome Message --}}
    <div class="flex flex-col items-center mb-10">
      <a href="{{ home_url('/') }}" aria-label="Zurück zur Startseite" class="inline-flex items-center justify-center transition duration-300 hover:opacity-80">
        <img src="{{ get_theme_file_uri('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}" class="h-13 mb-5 drop-shadow-md">
      </a>
      {{--
    <h1 class="text-xl font-semibold text-white tracking-tight">Willkommen zurück</h1>
    --}}
      <p class="text-slate-400 text-sm mt-1 uppercase uppercase-tracking-[-0.1]">Sicherer Zugriff auf Ihren Account</p>
    </div>

    {{-- 2. Social Logins (Neu für maximale Conversion) --}}
    <div class="grid grid-cols-2 gap-3 mb-8">
      <button class="flex items-center justify-center gap-2 py-2.5 border border-white/20 rounded-lg bg-white/5 duration-300 hover:bg-white/10 transition text-sm font-medium">
        @include('dashboard.icons.google') Google
      </button>
      <button class="flex items-center justify-center gap-2 py-2.5 border border-white/20 rounded-lg bg-white/5 duration-300 hover:bg-white/10 transition text-sm font-medium">
        @include('dashboard.icons.apple') Apple
      </button>
    </div>

    <div class="relative mb-8 flex items-center gap-4">
      {{-- Linker Strich: Fadet von transparent zu weiß/10 --}}
      <div class="flex-grow h-[1px] bg-gradient-to-r from-transparent to-white/10"></div>

      {{-- Text: Ohne Hintergrund, perfekt zentriert --}}
      <div class="text-[10px] uppercase tracking-[0.2em] font-medium text-slate-400 whitespace-nowrap">
        Oder mit E-Mail
      </div>

      {{-- Rechter Strich: Fadet von weiß/10 zu transparent --}}
      <div class="flex-grow h-[1px] bg-gradient-to-l from-transparent to-white/10"></div>
    </div>

    {{-- 3. Login Form (Optimiert nach Bild 15) --}}
    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="space-y-6">
      @php wp_nonce_field('dashboard_login', '_wpnonce'); @endphp
      @csrf
      <input type="hidden" name="action" value="dashboard_login">

      @if($checkoutPlan !== '')
      <input type="hidden" name="checkout_plan" value="{{ $checkoutPlan }}">
      @endif

      <div>
        <label class="block text-xs font-semibold mb-1.5 text-slate-300 uppercase tracking-wider">E-Mail Adresse</label>
        <input type="email" name="email" required placeholder="name@beispiel.de"
          class="dashboard-input w-full">
      </div>

      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Passwort</label>
          <a href="/dashboard-password" class="text-xs text-brand-primary hover:text-brand-primaryHover transition">Passwort vergessen?</a>
        </div>

        <div class="relative">
          <input type="password" name="password" required
            class="dashboard-input password-input w-full">
          <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
            @include('dashboard.icons.eye')
          </button>
        </div>
      </div>

      <button type="submit"
        class="w-full py-3.5 rounded-lg bg-brand-primary hover:bg-brand-primaryHover duration-300 transition-all shadow-lg shadow-brand-primary/20 font-semibold text-white uppercase tracking-wide">
        Anmelden
      </button>
    </form>

    @if($loginError)
    <div class="mt-4 p-3 rounded bg-red-500/10 border border-red-500/20">
      <p class="text-xs text-red-400 text-center">
        {{ $loginMessages[$loginError] ?? 'Login fehlgeschlagen. Bitte prüfen Sie Ihre Daten.' }}
      </p>
    </div>
    @endif

    <div class="mt-10 text-sm text-center text-slate-400 space-y-3">
      <p>
        Noch kein Konto?
        <a href="/subscribe-trial{{ $checkoutPlan ? '?plan=' . $checkoutPlan : '' }}" class="text-brand-primary font-semibold hover:underline">Zur Abo-Seite</a>
      </p>

      <a href="{{ home_url('/') }}" class="inline-flex items-center justify-center text-xs uppercase tracking-[0.18em] text-slate-500 hover:text-brand-primary transition duration-300">
        Zurück zur Startseite
      </a>
    </div>

  </div>
</section>

@endsection
