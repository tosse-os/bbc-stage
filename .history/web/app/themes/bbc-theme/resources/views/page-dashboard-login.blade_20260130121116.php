{{-- Template Name: Dashboard Login --}}
<!doctype html>
<html {!! get_language_attributes() !!}>

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {!! wp_head() !!}
  @vite(['resources/css/app-dashboard.css','resources/js/dashboard.js'])
</head>

<body class="dashboard-login min-h-screen flex items-center justify-center text-slate-100 bg-[#060a13]">

  <section class="relative w-full max-w-[400px] mx-auto px-4">
    <div class="backdrop-blur-xl bg-white/[0.03] border border-white/10 rounded-2xl px-8 py-10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] ring-1 ring-white/10 overflow-hidden relative">

      <div class="flex flex-col items-center mb-6">
        <img src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}" class="h-17 mb-4">
        <h1 class="text-lg font-semibold text-white tracking-tight">Willkommen zurück</h1>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-8">
        <button class="flex items-center justify-center gap-2
                 px-5 py-2
                 border border-white/10 rounded-lg
                 bg-white/[0.04] hover:bg-white/[0.08]
                 transition text-[11px] font-bold uppercase tracking-wider">
          @include('dashboard.icons.google') Google
        </button>

        <button class="flex items-center justify-center gap-2
                 px-5 py-2
                 border border-white/10 rounded-lg
                 bg-white/[0.04] hover:bg-white/[0.08]
                 transition text-[11px] font-bold uppercase tracking-wider">
          @include('dashboard.icons.apple') Apple
        </button>
      </div>


      <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
          <span class="w-full border-t border-white/5"></span>
        </div>
        <div class="relative flex justify-center text-[10px] uppercase tracking-widest">
          <span class="bg-[#0a0f1d] px-2 text-slate-500 font-medium">Oder E-Mail</span>
        </div>
      </div>

      <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="space-y-4">
        @csrf
        <input type="hidden" name="action" value="dashboard_login">

        {{-- E-Mail --}}
        <div>
          <label class="block text-[11px] font-bold mb-1.5 text-slate-400 uppercase tracking-wider">
            E-Mail
          </label>

          <input
            type="email"
            name="email"
            required
            placeholder="name@beispiel.de"
            class="dashboard-input">
        </div>

        {{-- Passwort --}}
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
              Passwort
            </label>
            <a href="/forgot-password" class="text-[11px] text-brand-primary hover:text-brand-primaryHover transition">
              Vergessen?
            </a>
          </div>

          <div class="relative">
            <input
              type="password"
              name="password"
              required
              class="dashboard-input password-input pr-10">

            <button
              type="button"
              class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition">
              @include('dashboard.icons.eye')
            </button>
          </div>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <input
            type="checkbox"
            name="remember"
            id="remember"
            class="w-3.5 h-3.5 rounded border-white/30 bg-white/5 text-brand-primary
            hover:border-white/60 focus:ring-2 focus:ring-brand-primary/40">
          <label for="remember" class="text-xs text-slate-400 cursor-pointer select-none">
            Angemeldet bleiben
          </label>
        </div>

        <button
          type="submit"
          class="w-full py-3 rounded-lg bg-brand-primary hover:bg-brand-primaryHover transition-all shadow-lg shadow-brand-primary/10 font-bold text-sm text-white uppercase tracking-widest mt-2">
          Anmelden
        </button>
      </form>

      @if(request()->get('error'))
      <div class="mt-4 p-2 rounded bg-red-500/10 border border-red-500/20">
        <p class="text-[11px] text-red-400 text-center">Login fehlgeschlagen.</p>
      </div>
      @endif

      <p class="mt-8 text-xs text-center text-slate-500">
        Noch kein Konto?
        <a href="/register" class="text-brand-primary font-bold hover:underline">Registrieren</a>
      </p>

    </div>
  </section>

  {!! wp_footer() !!}
</body>

</html>
