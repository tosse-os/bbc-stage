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

      <a
        href="/dashboard-settings?tab=billing"
        class="pb-3 text-sm font-medium
  {{ $tab === 'billing'
    ? 'text-brand-primary border-b-2 border-brand-primary'
    : 'text-slate-400 hover:text-slate-600' }}">
        Billing
      </a>

    </div>

    {{-- ACCOUNT TAB --}}
    @if ($tab === 'account')

    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="grid grid-cols-1 md:grid-cols-[1fr_260px] gap-10">
      <input type="hidden" name="action" value="dashboard_update_account">
      @php wp_nonce_field('dashboard_update_account', '_wpnonce'); @endphp

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

    {{-- BILLING TAB --}}
    @if ($tab === 'billing')

    @php
    $subscriptionState = get_user_meta($user->ID, USER_META_SUB_STATUS, true) ?: 'payment_required';
    $stripeCustomerId = get_user_meta($user->ID, 'stripe_customer_id', true);
    $stripeSubscriptionId = get_user_meta($user->ID, 'stripe_subscription_id', true);
    @endphp

    <section class="max-w-3xl space-y-6">

      @if(request()->get('stripe') === 'success')
      <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        Checkout completed. Your subscription status will update automatically in a moment.
      </div>
      @endif

      @if(request()->get('stripe') === 'cancel')
      <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
        Checkout was cancelled.
      </div>
      @endif

      @if(request()->get('error'))
      <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        Stripe checkout could not be started.
      </div>
      @endif

      <div class="rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-sm">
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-slate-900">Subscription</h2>
          <p class="mt-1 text-sm text-slate-500">
            Current dashboard access status: <span class="font-medium text-slate-700">{{ $subscriptionState }}</span>
          </p>
        </div>

        @if($subscriptionState !== 'active')
        <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}">
          <input type="hidden" name="action" value="dashboard_start_checkout">
          @php wp_nonce_field('dashboard_start_checkout', '_wpnonce'); @endphp

          <button
            type="submit"
            class="inline-flex items-center px-6 py-3 rounded-xl bg-brand-primary text-white text-sm font-semibold hover:bg-brand-primaryHover transition">
            Start subscription
          </button>
        </form>
        @else
        <div class="text-sm text-slate-600">
          Your subscription is already active.
        </div>
        @endif
      </div>

      @if($stripeCustomerId || $stripeSubscriptionId)
      <div class="rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Stripe</h3>

        <div class="space-y-3 text-sm text-slate-600">
          @if($stripeCustomerId)
          <div>
            <span class="font-medium text-slate-700">Customer ID:</span>
            <span>{{ $stripeCustomerId }}</span>
          </div>
          @endif

          @if($stripeSubscriptionId)
          <div>
            <span class="font-medium text-slate-700">Subscription ID:</span>
            <span>{{ $stripeSubscriptionId }}</span>
          </div>
          @endif
        </div>
      </div>
      @endif

    </section>

    @endif

    {{-- SECURITY TAB --}}
    @if ($tab === 'security')

    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="max-w-md space-y-6">
      @csrf
      <input type="hidden" name="action" value="dashboard_update_password">
      @php wp_nonce_field('dashboard_update_password', '_wpnonce'); @endphp

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
