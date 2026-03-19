@extends('layouts.dashboard')

@section('content')

@php
$tab = request()->get('tab', 'account');
$user = wp_get_current_user();
$avatar_id = get_user_meta($user->ID, 'profile_avatar_id', true);
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

    </div>

    @if ($tab === 'account')

    <form
      method="post"
      action="{{ esc_url(admin_url('admin-post.php')) }}"
      enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="action" value="dashboard_update_account">

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

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
          Your Profile Picture
        </label>

        <div class="border-2 border-dashed border-slate-300 rounded-xl h-48 flex items-center justify-center text-slate-400 text-sm">
          Upload your photo
        </div>
      </div>

    </form>

    @endif

    @if ($tab === 'security')

    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="max-w-md space-y-6">
      @csrf
      <input type="hidden" name="action" value="dashboard_change_password">

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          Old Password
        </label>
        <input
          type="password"
          name="current_password"
          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          New Password
        </label>
        <input
          type="password"
          name="new_password"
          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          Retype Password
        </label>
        <input
          type="password"
          name="new_password_confirm"
          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div class="pt-4">
        <button
          type="submit"
          class="px-6 py-3 rounded-xl bg-brand-primary text-white text-sm font-semibold hover:bg-brand-primaryHover transition">
          Update Password
        </button>
      </div>

    </form>

    @endif

  </div>

</section>

@endsection
