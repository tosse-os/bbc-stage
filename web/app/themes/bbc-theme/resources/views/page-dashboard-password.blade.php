{{--
Template Name: Dashboard Password Reset
--}}
@extends('layouts.dashboard-auth')

@section('content')
<section class="relative w-full max-w-md mx-auto px-4">
  <div class="backdrop-blur-xl bg-white/[0.03] border border-white/10 rounded-2xl px-8 py-10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] ring-1 ring-white/10 overflow-hidden relative">

    <div class="flex flex-col items-center mb-10">
      <img src="{{ get_theme_file_uri('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}" class="h-14 mb-6">
      <h1 class="text-xl font-semibold text-white tracking-tight">{{ dashboard_t('auth.reset_password_title') }}</h1>
      <p class="text-slate-400 text-sm mt-1 text-center">
        {{ dashboard_t('auth.reset_password_intro') }}
      </p>
    </div>

    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="space-y-6">
      @csrf
      <input type="hidden" name="action" value="dashboard_password_reset">
      <input type="hidden" name="lang" value="{{ dashboard_lang() }}">

      <div>
        <label class="block text-xs font-semibold mb-1.5 text-slate-300 uppercase tracking-wider">{{ dashboard_t('auth.email_address') }}</label>
        <input type="email" name="email" required placeholder="{{ dashboard_t('auth.email_placeholder') }}"
          class="dashboard-input w-full rounded-lg bg-white/10 border border-white/10 px-4 py-3 text-white placeholder-slate-500 transition-all">
      </div>

      <button type="submit"
        class="w-full py-3.5 rounded-lg bg-brand-primary hover:bg-brand-primaryHover transition-all shadow-lg shadow-brand-primary/20 font-bold text-white uppercase tracking-wide">
        {{ dashboard_t('auth.reset_password_button') }}
      </button>
    </form>

    @if(request()->get('success'))
    <div class="mt-6 p-3 rounded bg-emerald-500/10 border border-emerald-500/20">
      <p class="text-xs text-emerald-400 text-center">
        {{ dashboard_t('auth.reset_success') }}
      </p>
    </div>
    @endif

    @if(request()->get('error'))
    <div class="mt-6 p-3 rounded bg-red-500/10 border border-red-500/20">
      <p class="text-xs text-red-400 text-center">
        {{ dashboard_error_text((string) request()->get('error')) }}
      </p>
    </div>
    @endif

    <p class="mt-10 text-sm text-center text-slate-400">
      <a href="{{ dashboard_login_url() }}" class="text-brand-primary font-semibold hover:underline">{{ dashboard_t('auth.back_to_login') }}</a>
    </p>

  </div>
</section>
@endsection
