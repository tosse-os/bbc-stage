{{--
Template Name: Dashboard Register
--}}
@extends('layouts.dashboard-auth')

@section('content')
<section class="relative w-full max-w-md mx-auto px-4">

  <div class="backdrop-blur-xl bg-white/[0.03] border border-white/10 rounded-2xl px-8 py-10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] ring-1 ring-white/10 overflow-hidden relative">

    <div class="flex flex-col items-center mb-10">
      <img src="{{ get_theme_file_uri('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}" class="h-13 mb-5 drop-shadow-md" alt="Bloombridge Capital">
      <p class="text-slate-400 text-sm mt-1 uppercase uppercase-tracking-[-0.1]">Account erstellen</p>
    </div>

    <div class="space-y-5 text-center">
      <h1 class="text-xl font-semibold text-white tracking-tight">
        Registrierung über Abo-Seite
      </h1>

      <p class="text-sm leading-relaxed text-slate-400">
        Neue Accounts werden zusammen mit der Abo-Auswahl und der sicheren Zahlung über Stripe erstellt.
      </p>

      <a href="{{ home_url('/subscribe-trial/') }}"
        class="inline-flex w-full items-center justify-center py-3.5 rounded-lg bg-brand-primary hover:bg-brand-primaryHover transition-all shadow-lg shadow-brand-primary/20 font-semibold text-white uppercase tracking-wide">
        Zur Abo-Seite
      </a>
    </div>

    <p class="mt-10 text-sm text-center text-slate-400">
      Bereits registriert?
      <a href="/dashboard-login" class="text-brand-primary font-semibold hover:underline">
        Zum Login
      </a>
    </p>

  </div>

</section>
@endsection
