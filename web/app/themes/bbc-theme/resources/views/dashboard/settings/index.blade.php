@extends('layouts.dashboard')

@section('content')

@php
$tab = request()->get('tab', 'account');
$user = wp_get_current_user();

$currentTheme = get_user_meta($user->ID, 'dashboard_theme', true) === 'dark' ? 'dark' : 'light';
$sidebarCollapsed = get_user_meta($user->ID, 'dashboard_sidebar_collapsed', true) === '1';
@endphp

<section class="max-w-5xl mx-auto">

  <header class="mb-8">
    <h1 class="text-2xl font-semibold">Settings</h1>
  </header>

  <div class="bg-white/85 backdrop-blur rounded-2xl shadow-sm px-8 py-8">

    <div class="mb-8 flex gap-8 border-b">

      <a
        href="/dashboard-settings"
        class="pb-3 text-sm font-medium
        {{ $tab === 'account'
          ? 'text-brand-primary border-b-2 border-brand-primary'
          : 'text-slate-400 hover:text-slate-600' }}">
        Account
      </a>

      <a
        href="/dashboard-settings?tab=security"
        class="pb-3 text-sm font-medium
        {{ $tab === 'security'
          ? 'text-brand-primary border-b-2 border-brand-primary'
          : 'text-slate-400 hover:text-slate-600' }}">
        Security
      </a>

      <a
        href="/dashboard-settings?tab=appearance"
        class="pb-3 text-sm font-medium
        {{ $tab === 'appearance'
          ? 'text-brand-primary border-b-2 border-brand-primary'
          : 'text-slate-400 hover:text-slate-600' }}">
        Appearance
      </a>

    </div>

    {{-- ACCOUNT TAB --}}
    @if ($tab === 'account')

    <form method="post" class="grid grid-cols-1 md:grid-cols-[1fr_260px] gap-10">

      <div class="space-y-6">

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            Full name
          </label>
          <input
            type="text"
            value="{{ $user->display_name }}"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary/40">
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            Email
          </label>
          <input
            type="email"
            value="{{ $user->user_email }}"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-slate-50 cursor-not-allowed"
            disabled>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            Username
          </label>
          <input
            type="text"
            value="{{ $user->user_login }}"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-slate-50 cursor-not-allowed"
            disabled>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            Phone Number
          </label>
          <input
            type="text"
            placeholder="+49 …"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary/40">
        </div>

        <div class="pt-4">
          <button
            type="submit"
            class="inline-flex items-center px-6 py-3 rounded-xl bg-brand-primary text-white text-sm font-semibold hover:bg-brand-primaryHover transition">
            Update Profile
          </button>
        </div>

      </div>

      @php
      $avatar_id = get_user_meta($user->ID, 'dashboard_avatar_id', true);
      $avatar_url = $avatar_id
      ? wp_get_attachment_image_url($avatar_id, 'thumbnail')
      : get_avatar_url($user->ID);
      @endphp

      <div class="space-y-3">
        <img
          src="{{ $avatar_url }}"
          class="w-40 h-40 rounded-xl object-cover bg-slate-100"
          data-avatar-preview>

        <label class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 cursor-pointer text-sm">
          Choose Photo
          <input
            type="file"
            accept="image/jpeg,image/png,image/webp"
            class="hidden"
            data-avatar-input
            data-ajax="{{ admin_url('admin-ajax.php') }}"
            data-nonce="{{ wp_create_nonce('dashboard_avatar_upload') }}">
        </label>

        <p class="text-xs text-slate-400">
          JPG, PNG oder WebP · max. 2 MB
        </p>
      </div>

    </form>

    @endif


    {{-- SECURITY TAB --}}
    @if ($tab === 'security')

    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="max-w-md space-y-6">
      @csrf
      <input type="hidden" name="action" value="dashboard_change_password">

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          Old Password
        </label>
        <input type="password" name="current_password"
          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          New Password
        </label>
        <input type="password" name="new_password"
          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          Retype Password
        </label>
        <input type="password" name="new_password_confirm"
          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div class="pt-4">
        <button type="submit"
          class="px-6 py-3 rounded-xl bg-brand-primary text-white text-sm font-semibold hover:bg-brand-primaryHover transition">
          Update Password
        </button>
      </div>

    </form>

    @endif


    {{-- APPEARANCE TAB --}}
    @if ($tab === 'appearance')

    <div class="max-w-lg space-y-10">

      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-semibold">Theme</h3>
          <p class="text-xs text-slate-500">Light oder Dark Mode</p>
        </div>

        <button
          type="button"
          data-appearance-theme
          data-current="{{ $currentTheme }}"
          data-ajax="{{ admin_url('admin-ajax.php') }}"
          data-nonce="{{ wp_create_nonce('dashboard_theme_toggle') }}"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm">
          {{ ucfirst($currentTheme) }}
        </button>

      </div>

      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-semibold">Sidebar</h3>
          <p class="text-xs text-slate-500">Collapsed oder Expanded</p>
        </div>

        <button
          type="button"
          data-appearance-sidebar
          data-current="{{ $sidebarCollapsed ? 'collapsed' : 'expanded' }}"
          data-ajax="{{ admin_url('admin-ajax.php') }}"
          data-nonce="{{ wp_create_nonce('dashboard_sidebar_toggle') }}"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm">
          {{ $sidebarCollapsed ? 'Collapsed' : 'Expanded' }}
        </button>

      </div>

    </div>

    @endif

  </div>

</section>

@endsection
