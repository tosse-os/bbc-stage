{{-- Template Name: Dashboard Login --}}
@extends('layouts.dashboard-auth')

@section('content')
<section class="relative w-full max-w-md mx-auto px-4">
  {{-- Veredelte Kachel mit extremem Glow und Schattentiefe --}}
  <div class="backdrop-blur-xl bg-white/[0.03] border border-white/10 rounded-2xl px-8 py-10 shadow-[0_20px_50px_rgba(0,0,0,0.6)] ring-1 ring-white/10 overflow-hidden relative">

    {{-- Subtiler Lichteffekt in der Ecke für mehr Tiefe --}}
    <div class="absolute -top-20 -left-20 w-40 h-40 bg-brand-primary/10 blur-[60px] pointer-events-none"></div>

    {{-- 1. Logo & Welcome Message --}}
    <div class="flex flex-col items-center mb-10 relative z-10">
      <img src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}" class="h-12 mb-5 drop-shadow-md">
      <p class="text-slate-400 text-[11px] uppercase tracking-[0.2em] font-medium opacity-80">Sicherer Zugriff auf Ihr Konto</p>
    </div>

    {{-- 2. Social Logins (Veredelte Buttons) --}}
    <div class="grid grid-cols-2 gap-3 mb-8 relative z-10">
      <button class="flex items-center justify-center gap-2 py-2.5 border border-white/10 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] hover:border-white/20 transition-all duration-300 text-xs font-bold uppercase tracking-wider group">
        <span class="opacity-70 group-hover:opacity-100 transition-opacity">@include('dashboard.icons.google')</span> Google
      </button>
      <button class="flex items-center justify-center gap-2 py-2.5 border border-white/10 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] hover:border-white/20 transition-all duration-300 text-xs font-bold uppercase tracking-wider group">
        <span class="opacity-70 group-hover:opacity-100 transition-opacity">@include('dashboard.icons.apple')</span> Apple
      </button>
    </div>

    <div class="relative mb-8 z-10">
      <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-white/5"></span></div>
      <div class="relative flex justify-center text-[10px] uppercase tracking-[0.2em] font-bold"><span class="bg-[#0b101c] px-3 text-slate-500">Oder mit E-Mail</span></div>
    </div>

    {{-- 3. Login Form (Optimierte Typografie & Abstände) --}}
    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="space-y-5 relative z-10">
      @csrf
      <input type="hidden" name="action" value="dashboard_login">

      <div>
        <label class="block text-[10px] font-black mb-2 text-slate-400 uppercase tracking-[0.15em]">E-Mail Adresse</label>
        <input type="email" name="email" required placeholder="name@beispiel.de"
          class="dashboard-input w-full rounded-lg bg-white/[0.05] border border-white/10 px-5 py-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-brand-primary/50 focus:bg-white/[0.08] transition-all duration-300">
      </div>

      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Passwort</label>
          <a href="/dashboard-password" class="text-[10px] font-bold text-brand-primary hover:text-white transition-colors uppercase tracking-widest">Vergessen?</a>
        </div>

        <div class="relative group">
          <input type="password" name="password" required
            class="dashboard-input password-input w-full rounded-lg bg-white/[0.05] border border-white/10 px-5 py-3 pr-12 text-sm text-white focus:outline-none focus:ring-1 focus:ring-brand-primary/50 focus:bg-white/[0.08] transition-all duration-300">
          <button type="button" class="password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-brand-primary transition-colors">
            @include('dashboard.icons.eye')
          </button>
        </div>
      </div>

      <button type="submit"
        class="w-full py-4 rounded-lg bg-brand-primary hover:bg-brand-primaryHover active:scale-[0.98] transition-all duration-300 shadow-[0_10px_25px_rgba(var(--brand-primary-rgb,0,0,0),0.3)] font-black text-xs text-white uppercase tracking-[0.2em] mt-2">
        Anmelden
      </button>
    </form>

    @if(request()->get('error'))
    <div class="mt-6 p-3 rounded-lg bg-red-500/10 border border-red-500/20">
      <p class="text-[10px] font-bold text-red-400 text-center uppercase tracking-widest">Login fehlgeschlagen</p>
    </div>
    @endif

    <p class="mt-10 text-[11px] text-center text-slate-500 font-medium tracking-wide">
      Noch kein Konto? <a href="/dashboard-register" class="text-brand-primary font-black hover:text-white transition-colors uppercase tracking-widest ml-1">Registrieren</a>
    </p>

  </div>
</section>
@endsection
