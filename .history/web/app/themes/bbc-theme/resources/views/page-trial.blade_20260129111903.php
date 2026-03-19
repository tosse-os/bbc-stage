{{--
Template Name: Free Trial
--}}
@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-slate-950">
  <div class="absolute inset-0">
    <img
      src="{{ Vite::asset('resources/images/hero-bg.jpg') }}"
      alt=""
      class="h-full w-full object-cover opacity-80">
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/60 via-slate-950/30 to-slate-950/10"></div>
  </div>

  <div class="relative">
    <div class="container-content pt-32 pb-32">
      <div class="grid grid-cols-1 items-start gap-16 lg:grid-cols-[1.1fr_0.9fr]">

        <div>
          <h1 class="text-4xl font-semibold tracking-tight text-white lg:text-5xl">
            Start Your <span class="text-brand-primary">Free Trial</span>
          </h1>

          <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-300">
            Sign up to access advanced trading signals and structured market
            analyses today. Cancel anytime during your free trial.
          </p>

          <div class="mt-10 max-w-md rounded-2xl border border-white/10 bg-white/5 p-8 backdrop-blur">
            <div class="text-sm font-medium uppercase tracking-wide text-slate-300">
              Free 7-Day Trial
            </div>

            <div class="mt-3 text-2xl font-semibold text-white">
              Full Access Plan
            </div>

            <div class="mt-4 flex items-end gap-2">
              <div class="text-4xl font-semibold text-white">
                €59
              </div>
              <div class="pb-1 text-sm text-slate-400">
                / Monat
              </div>
            </div>

            <div class="mt-1 text-sm text-slate-400">
              danach €59 pro Monat
            </div>

            <ul class="mt-6 space-y-3 text-sm text-slate-300">
              <li>✓ Echtzeit Marktanalysen</li>
              <li>✓ Strukturierte Handelssignale</li>
              <li>✓ Aktien, Krypto & Forex</li>
              <li>✓ 45 Börsenplätze weltweit</li>
              <li>✓ Tägliche Updates & Alerts</li>
            </ul>

            <div class="mt-6 text-xs text-slate-400">
              Jederzeit kündbar. Keine Gebühren während des Testzeitraums.
            </div>
          </div>
        </div>

        <div class="rounded-2xl bg-white p-8 shadow-[0_20px_60px_rgba(0,0,0,0.25)]">
          <h2 class="text-xl font-semibold text-slate-900">
            Create Your <span class="text-brand-primary">Bloombridge Capital</span> Account
          </h2>

          <form class="mt-8 space-y-5">
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Your Name
              </label>
              <input
                type="text"
                class="w-full rounded-md border border-slate-300 px-4 py-2 text-sm focus:border-brand-primary focus:outline-none">
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Your Email
              </label>
              <input
                type="email"
                class="w-full rounded-md border border-slate-300 px-4 py-2 text-sm focus:border-brand-primary focus:outline-none">
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Password
              </label>
              <input
                type="password"
                class="w-full rounded-md border border-slate-300 px-4 py-2 text-sm focus:border-brand-primary focus:outline-none">
            </div>

            <button
              type="submit"
              class="mt-4 w-full rounded-md bg-brand-primary py-3 text-base font-medium text-white transition hover:bg-brand-primaryHover">
              Start 7-Day Free Trial
            </button>

            <div class="mt-4 text-center text-xs text-slate-500">
              🔒 100% secure payment
            </div>

            <div class="mt-2 flex justify-center gap-3 text-xs text-slate-400">
              <span>Stripe</span>
              <span>VISA</span>
              <span>Mastercard</span>
              <span>SEPA</span>
            </div>

            <div class="mt-3 text-center text-xs text-slate-400">
              No charges during free trial. Cancel anytime.
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
