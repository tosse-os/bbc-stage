{{--
Template Name: Dashboard Login
--}}
<!doctype html>
<html {!! get_language_attributes() !!}>

<head>
  <meta charset="{{ get_bloginfo('charset') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {!! wp_head() !!}
  @vite('resources/css/app-dashboard.css')
</head>

<body class="min-h-screen flex items-center justify-center text-slate-100"
  style="background-image:url('{{ Vite::asset('resources/images/dashboard/forms-bg.jpg') }}');background-size:cover;background-position:center;">

  <section class="relative w-full max-w-md mx-auto">

    <div class="backdrop-blur-md bg-white/5 border border-white/20 rounded-2xl px-8 py-10 shadow-2xl">

      <div class="flex flex-col items-center mb-8">
        <img
          src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}"
          alt="Bloombridge Capital"
          class="h-10 mb-4">
      </div>

      <form method="post"
        action="{{ esc_url(admin_url('admin-post.php')) }}"
        class="space-y-5">
        @csrf
        <input type="hidden" name="action" value="dashboard_login">

        <div>
          <label class="block text-sm mb-1 text-slate-200">Email Address</label>
          <input
            type="email"
            name="email"
            required
            placeholder="johndoe@email.com"
            class="w-full rounded-lg bg-transparent border border-white/30 px-4 py-3 text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-primary">
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="text-sm text-slate-200">Password</label>
            <a href="#"
              class="text-xs text-brand-primary hover:underline">
              Forgot Password?
            </a>
          </div>

          <div class="relative">
            <input
              type="password"
              name="password"
              required
              class="w-full rounded-lg bg-transparent border border-white/30 px-4 py-3 pr-10 text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary">
              <span class="absolute right-3 top-1/2 -translate-y-1/2
              w-5 h-5 text-slate-400 flex items-center justify-center">
                <span class="w-5 h-5">
                  @include('dashboard.icons.eye')
                </span>
              </span>
          </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-300">
          <input type="checkbox" class="accent-brand-primary">
          Keep me signed in
        </label>

        <button
          type="submit"
          class="w-full mt-4 py-3 rounded-lg bg-brand-primary hover:bg-brand-primaryHover transition font-medium text-white">
          Login
        </button>
      </form>

      @if(request()->get('error'))
      <p class="mt-4 text-sm text-red-400 text-center">
        Login fehlgeschlagen
      </p>
      @endif

      <p class="mt-6 text-sm text-center text-slate-300">
        Create an account
      </p>

    </div>

  </section>

  {!! wp_footer() !!}
</body>

</html>
