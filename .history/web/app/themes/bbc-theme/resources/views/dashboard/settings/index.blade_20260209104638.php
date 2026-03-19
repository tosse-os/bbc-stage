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
      <a href="/dashboard-settings"
        class="pb-3 text-sm font-medium {{ $tab === 'account' ? 'text-brand-primary border-b-2 border-brand-primary' : 'text-slate-400' }}">
        Account
      </a>
      <a href="/dashboard-settings?tab=security"
        class="pb-3 text-sm font-medium {{ $tab === 'security' ? 'text-brand-primary border-b-2 border-brand-primary' : 'text-slate-400' }}">
        Security
      </a>
    </div>

    @if ($tab === 'account')

    <form
      method="post"
      action="{{ esc_url(admin_url('admin-post.php')) }}"
      enctype="multipart/form-data"
      class="grid grid-cols-1 md:grid-cols-[1fr_260px] gap-10">

      @csrf
      <input type="hidden" name="action" value="dashboard_update_account">

      <div class="space-y-6">

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            Full name
          </label>
          <input
            type="text"
            name="display_name"
            value="{{ $user->display_name }}"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            Email
          </label>
          <input
            type="email"
            value="{{ $user->user_email }}"
            disabled
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-slate-50">
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            Username
          </label>
          <input
            type="text"
            value="{{ $user->user_login }}"
            disabled
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-slate-50">
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            Phone Number
          </label>
          <input
            type="text"
            name="phone"
            value="{{ get_user_meta($user->ID, 'phone_number', true) }}"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
        </div>

        <button
          type="submit"
          class="inline-flex items-center px-6 py-3 rounded-xl bg-brand-primary text-white text-sm font-semibold">
          Update Profile
        </button>

      </div>

      <div class="space-y-4">

        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">
          Profile Picture
        </label>

        <div class="h-40 w-40 rounded-xl overflow-hidden bg-slate-100 flex items-center justify-center">
          @if ($avatar_id && wp_attachment_is_image($avatar_id))
          {!! wp_get_attachment_image($avatar_id, 'medium', false, ['class' => 'w-full h-full object-cover']) !!}
          @else
          <span class="text-sm text-slate-400">No image uploaded</span>
          @endif
        </div>

        <label class="inline-block px-4 py-2 rounded-lg bg-slate-200 text-sm cursor-pointer">
          Select image
          <input type="file" name="profile_avatar" class="hidden" accept="image/jpeg,image/png,image/webp">
        </label>

      </div>

    </form>

    @endif

    @if ($tab === 'security')

    <form
      method="post"
      action="{{ esc_url(admin_url('admin-post.php')) }}"
      class="max-w-md space-y-6">

      @csrf
      <input type="hidden" name="action" value="dashboard_update_password">

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          Old Password
        </label>
        <input type="password" name="current_password" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          New Password
        </label>
        <input type="password" name="new_password" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          Retype Password
        </label>
        <input type="password" name="new_password_confirm" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <button
        type="submit"
        class="px-6 py-3 rounded-xl bg-brand-primary text-white text-sm font-semibold">
        Update Password
      </button>

    </form>

    @endif

  </div>

</section>

@endsection
