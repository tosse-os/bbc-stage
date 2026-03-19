{{-- Template Name: Dashboard Login --}}
<!doctype html>
<html {!! get_language_attributes() !!} class="h-full">

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {!! wp_head() !!}
  @vite(['resources/css/app-dashboard.css','resources/js/dashboard.js'])
</head>

<body class="dashboard-login min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 text-slate-100 bg-[#060a13]">

  <div class="sm:mx-auto sm:w-full sm:max-w-[440px]">
    <div class="backdrop-blur-xl bg-white/[0.03] border border-white/10 rounded-2xl shadow-2xl ring-1 ring-white/5 overflow-hidden">

      <div class="px-8 py-10">
        {{-- 1. Logo & Welcome --}}
        <div class="text-center mb-8">
          <img src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}" class="h-10 mx-auto mb-4 w-auto">
          <h1 class="text-xl font-bold text-white tracking-tight">Willkommen zurück</h1>
          <p class="text-slate-400 text-sm mt-2">Loggen Sie sich in Ihr Dashboard ein</p>
        </div>

        {{-- 2. Social Logins --}}
        <div class="grid grid-cols-2 gap-4 mb-8">
          <button type="button" class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-white/5 px-4 py-2.5 text-sm font-semibold text-white border border-white/10 hover:bg-white/10 transition">
            @include('dashboard.icons.google') <span>Google</span>
          </button>
          <button type="button" class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-white/5 px-4 py-2.5 text-sm font-semibold text-white border border-white/10 hover:bg-white/10 transition">
            @include('dashboard.icons.apple') <span>Apple</span>
          </button>
        </div>

        <div class="relative mb-8">
          <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-white/10"></div>
          </div>
          <div class="relative flex justify-center text-xs uppercase">
            <span class="bg-[#0b101c] px-2 text-slate-500 font-medium">Oder mit E-Mail</span>
          </div>
        </div>

        {{-- 3. Form --}}
        <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="space-y-5">
          @csrf
          <input type="hidden" name="action" value="dashboard_login">

          <div>
            <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">E-Mail Adresse</label>
            <input id="email" name="email" type="email" autocomplete="email" required
              class="block w-full rounded-lg border-0 bg-white/5 py-3 text-white shadow-sm ring-1 ring-inset ring-white/10 placeholder:text-slate-600 focus:ring-2 focus:ring-inset focus:ring-brand-primary sm:text-sm">
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Passwort</label>
              <a href="/forgot-password" class="text-xs font-semibold text-brand-primary hover:text-brand-primaryHover">Vergessen?</a>
            </div>
            <div class="relative">
              <input id="password" name="password" type="password" required
                class="password-input block w-full rounded-lg border-0 bg-white/5 py-3 pr-10 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-brand-primary sm:text-sm">
              <button type="button" class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-white">
                @include('dashboard.icons.eye')
              </button>
            </div>
          </div>

          <div class="flex items-center">
            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-white/10 bg-white/5 text-brand-primary focus:ring-brand-primary">
            <label for="remember-me" class="ml-2 block text-sm text-slate-400">Angemeldet bleiben</label>
          </div>

          <div>
            <button type="submit" class="flex w-full justify-center rounded-lg bg-brand-primary px-3 py-3 text-sm font-bold text-white shadow-sm hover:bg-brand-primaryHover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-primary uppercase tracking-widest transition-all">
              Anmelden
            </button>
          </div>
        </form>

        <p class="mt-8 text-center text-sm text-slate-400">
          Noch kein Konto? <a href="/register" class="font-bold text-brand-primary hover:underline">Kostenlos registrieren</a>
        </p>

      </div>
    </div>
    </section>
    {!! wp_footer() !!}
</body>

</html>
