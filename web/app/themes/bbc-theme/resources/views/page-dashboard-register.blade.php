{{--
Template Name: Dashboard Register
--}}
@extends('layouts.dashboard-auth')

@section('content')
<section class="relative w-full max-w-md mx-auto px-4">

  <div class="backdrop-blur-xl bg-white/[0.03] border border-white/10 rounded-2xl px-8 py-10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] ring-1 ring-white/10 overflow-hidden relative">

    {{-- 1. Logo & Welcome Message --}}
    <div class="flex flex-col items-center mb-10">
      <img src="{{ get_theme_file_uri('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}" class="h-13 mb-5 drop-shadow-md">
      {{--
        <h1 class="text-xl font-semibold text-white tracking-tight">Willkommen zurück</h1>
        --}}
      <p class="text-slate-400 text-sm mt-1 uppercase uppercase-tracking-[-0.1]">Erstellen Ihren Account</p>
    </div>

    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="space-y-6">
      @csrf
      <input type="hidden" name="action" value="dashboard_register">

      {{-- Name --}}
      <div>
        <label class="block text-xs font-semibold mb-1.5 text-slate-300 uppercase tracking-wider">
          Name
        </label>
        <input
          type="text"
          name="name"
          required
          placeholder="Ihr Name"
          class="dashboard-input w-full rounded-lg bg-white/10 border border-white/10 px-4 py-3 text-white placeholder-slate-500 transition-all">
      </div>

      {{-- E-Mail --}}
      <div>
        <label class="block text-xs font-semibold mb-1.5 text-slate-300 uppercase tracking-wider">
          E-Mail Adresse
        </label>
        <input
          type="email"
          name="email"
          required
          placeholder="name@beispiel.de"
          class="dashboard-input w-full">
      </div>

      {{-- Passwort --}}
      <div>
        <label class="block text-xs font-semibold mb-1.5 text-slate-300 uppercase tracking-wider">
          Passwort
        </label>

        <div class="relative">
          <input
            type="password"
            name="password"
            required
            placeholder="Passwort"
            class="dashboard-input password-input w-full ">

          <button
            type="button"
            class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
            @include('dashboard.icons.eye')
          </button>
        </div>
      </div>

      <button
        type="submit"
        class="w-full py-3.5 rounded-lg bg-brand-primary hover:bg-brand-primaryHover transition-all shadow-lg shadow-brand-primary/20 font-semibold text-white uppercase tracking-wide">
        Account erstellen
      </button>
    </form>

    <p class="mt-10 text-sm text-center text-slate-400">
      Bereits registriert?
      <a href="/dashboard-login" class="text-brand-primary font-semibold hover:underline">
        Zum Login
      </a>
    </p>

  </div>

</section>
@endsection
