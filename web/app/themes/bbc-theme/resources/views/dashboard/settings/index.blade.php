@extends('layouts.dashboard')

@section('content')

@php
$tab = request()->get('tab', 'account');
$user = wp_get_current_user();

$storedTheme = get_user_meta($user->ID, 'dashboard_theme', true);
$currentTheme = $storedTheme === 'light' ? 'light' : 'dark';
$sidebarCollapsed = get_user_meta($user->ID, 'dashboard_sidebar_collapsed', true) === '1';
@endphp

<section class="max-w-5xl mx-auto">

  <header class="mb-8">
    <h1 class="text-2xl font-semibold">{{ dashboard_t('settings.title') }}</h1>
  </header>

  <div class="bg-white/85 backdrop-blur rounded-2xl shadow-sm px-8 py-8">

    <div class="mb-8 flex gap-8 border-b">

      <a
        href="{{ dashboard_settings_url() }}"
        class="pb-3 text-sm font-medium
        {{ $tab === 'account'
          ? 'text-brand-primary border-b-2 border-brand-primary'
          : 'text-slate-400 hover:text-slate-600' }}">
        {{ dashboard_t('settings.tabs.account') }}
      </a>

      <a
        href="{{ dashboard_settings_url(['tab' => 'security']) }}"
        class="pb-3 text-sm font-medium
        {{ $tab === 'security'
          ? 'text-brand-primary border-b-2 border-brand-primary'
          : 'text-slate-400 hover:text-slate-600' }}">
        {{ dashboard_t('settings.tabs.security') }}
      </a>

      <a
        href="{{ dashboard_settings_url(['tab' => 'appearance']) }}"
        class="pb-3 text-sm font-medium
        {{ $tab === 'appearance'
          ? 'text-brand-primary border-b-2 border-brand-primary'
          : 'text-slate-400 hover:text-slate-600' }}">
        {{ dashboard_t('settings.tabs.appearance') }}
      </a>

      <a
        href="{{ dashboard_settings_billing_url() }}"
        class="pb-3 text-sm font-medium
  {{ $tab === 'billing'
    ? 'text-brand-primary border-b-2 border-brand-primary'
    : 'text-slate-400 hover:text-slate-600' }}">
        {{ dashboard_t('settings.tabs.billing') }}
      </a>

    </div>

    @if($tab !== 'billing' && request()->get('success'))
    <div class="mb-6 rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700">
      {{ $tab === 'security' ? dashboard_t('messages.password_updated') : dashboard_t('messages.account_updated') }}
    </div>
    @endif

    @if($tab !== 'billing' && request()->get('error'))
    <div class="mb-6 rounded-xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-700">
      {{ dashboard_error_text((string) request()->get('error')) }}
    </div>
    @endif

    {{-- ACCOUNT TAB --}}
    @if ($tab === 'account')

    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="grid grid-cols-1 md:grid-cols-[1fr_260px] gap-10">
      <input type="hidden" name="action" value="dashboard_update_account">
      <input type="hidden" name="lang" value="{{ dashboard_lang() }}">
      @php wp_nonce_field('dashboard_update_account', '_wpnonce'); @endphp

      <div class="space-y-6">

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            {{ dashboard_t('settings.account.full_name') }}
          </label>
          <input
            type="text"
            name="display_name"
            value="{{ $user->display_name }}"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary/40">
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            {{ dashboard_t('settings.account.email') }}
          </label>
          <input
            type="email"
            value="{{ $user->user_email }}"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-slate-50 cursor-not-allowed"
            disabled>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            {{ dashboard_t('settings.account.username') }}
          </label>
          <input
            type="text"
            value="{{ $user->user_login }}"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-slate-50 cursor-not-allowed"
            disabled>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
            {{ dashboard_t('settings.account.phone_number') }}
          </label>
          <input
            type="text"
            name="phone"
            value="{{ get_user_meta($user->ID, 'phone_number', true) }}"
            placeholder="+49 …"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary/40">
        </div>

        <div class="pt-4">
          <button
            type="submit"
            class="inline-flex items-center px-6 py-3 rounded-xl bg-brand-primary text-white text-sm font-semibold hover:bg-brand-primaryHover transition">
            {{ dashboard_t('settings.account.update_profile') }}
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
          {{ dashboard_t('settings.account.choose_photo') }}
          <input
            type="file"
            accept="image/jpeg,image/png,image/webp"
            class="hidden"
            data-avatar-input
            data-ajax="{{ admin_url('admin-ajax.php') }}"
            data-nonce="{{ wp_create_nonce('dashboard_avatar_upload') }}">
        </label>

        <p class="text-xs text-slate-400">
          {{ dashboard_t('settings.account.upload_hint') }}
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
    $stripeRawStatus = get_user_meta($user->ID, 'stripe_subscription_status', true);
    $currentPeriodEnd = (int) get_user_meta($user->ID, 'stripe_current_period_end', true);
    $cancelAtPeriodEnd = get_user_meta($user->ID, 'stripe_cancel_at_period_end', true) === '1';
    $billingErrorCode = trim((string) request()->get('error', ''));
    $billingErrorMessage = $billingErrorCode !== '' ? dashboard_stripe_billing_error_message($billingErrorCode) : '';
    $subscriptionPriceLabel = dashboard_stripe_user_price_label($user->ID);

    $isActive = $subscriptionState === 'active';
    $isTrial = $subscriptionState === 'trial';

    $stateText = dashboard_stripe_subscription_state_label($subscriptionState);

    $badgeClass = $isActive || $isTrial
    ? 'bg-emerald-400/20 text-emerald-200 border-emerald-300/20'
    : 'bg-amber-400/20 text-amber-200 border-amber-300/20';
    @endphp

    <section class="max-w-6xl text-white">

      @if(request()->get('stripe') === 'success')
      <div class="mb-6 rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
        {{ dashboard_t('billing.checkout_success') }}
      </div>
      @endif

      @if(request()->get('stripe') === 'cancel')
      <div class="mb-6 rounded-xl border border-amber-400/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">
        {{ dashboard_t('billing.checkout_cancel') }}
      </div>
      @endif

      @if($billingErrorMessage !== '')
      <div class="mb-6 rounded-xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-200">
        {{ $billingErrorMessage }}
      </div>
      @endif

      <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,680px)_minmax(260px,1fr)] gap-8 items-start">

        <div class="space-y-6">

          <div class="rounded-2xl border border-white/10 bg-slate-950/35 backdrop-blur-xl px-6 py-6 shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_260px] gap-8 items-end">

              <div>
                <h2 class="text-lg font-semibold uppercase tracking-wide text-white">
                  {{ dashboard_t('billing.current_subscription') }}
                </h2>

                <span class="mt-3 inline-flex items-center rounded-lg border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                  {{ $stateText }}
                </span>

                <div class="mt-5">
                  <div class="text-lg font-semibold text-white">
                    {{ dashboard_t('billing.premium_access') }}
                  </div>

                  <div class="text-slate-300">
                    {{ $subscriptionPriceLabel !== '' ? $subscriptionPriceLabel : dashboard_t('billing.price_monthly') }}
                  </div>

                  @if($currentPeriodEnd)
                  <div class="text-sm text-slate-400">
                    {{ dashboard_t('billing.next_invoice', '', ['date' => date_i18n('d.m.Y', $currentPeriodEnd)]) }}
                  </div>
                  @endif

                  @if($cancelAtPeriodEnd)
                  <div class="text-sm text-amber-300">
                    {{ dashboard_t('billing.cancel_at_period_end') }}
                  </div>
                  @elseif($isActive)
                  <div class="text-sm text-slate-400">
                    {{ dashboard_t('billing.subscription_active') }}
                  </div>
                  @elseif($isTrial)
                  <div class="text-sm text-slate-400">
                    {{ dashboard_t('billing.trial_active') }}
                  </div>
                  @else
                  <div class="text-sm text-slate-400">
                    {{ dashboard_t('billing.no_active_subscription') }}
                  </div>
                  @endif
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                  @if(!$isActive)
                  <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}">
                    <input type="hidden" name="action" value="dashboard_start_checkout">
                    <input type="hidden" name="lang" value="{{ dashboard_lang() }}">
                    @php wp_nonce_field('dashboard_start_checkout', '_wpnonce'); @endphp

                    <button
                      type="submit"
                      class="inline-flex items-center px-5 py-2.5 rounded-xl bg-brand-primary text-white text-sm font-semibold hover:bg-brand-primaryHover transition">
                      {{ dashboard_t('billing.start_subscription') }}
                    </button>
                  </form>
                  @endif

                  @if($stripeCustomerId)
                  <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" target="_blank">
                    <input type="hidden" name="action" value="dashboard_open_billing_portal">
                    <input type="hidden" name="lang" value="{{ dashboard_lang() }}">
                    @php wp_nonce_field('dashboard_open_billing_portal', '_wpnonce'); @endphp

                    <button
                      type="submit"
                      class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl border border-white/10 bg-white/5 text-white text-sm font-semibold hover:bg-white/10 transition leading-none whitespace-nowrap">
                      <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-4 h-4 flex-shrink-0 text-brand-primary">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 0 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 0 1-2.8-2.8l.1-.1A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.6-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 0 1 2.8-2.8l.1.1A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.6V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 0 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1a2 2 0 0 1 0 4H21a1.7 1.7 0 0 0-1.6 1z"></path>
                      </svg>
                      {{ dashboard_t('billing.manage_subscription') }}
                    </button>
                  </form>
                  @endif
                </div>
              </div>


            </div>
          </div>

          <div class="rounded-2xl border border-white/10 bg-slate-950/30 backdrop-blur-xl px-6 py-6 shadow-2xl">
            <div class="flex items-center justify-between gap-6">
              <div>
                <h2 class="text-lg font-semibold uppercase tracking-wide text-white">
                  {{ dashboard_t('billing.payment_method') }}
                </h2>

                <p class="mt-4 text-slate-300">
                  @if($stripeCustomerId)
                  {{ dashboard_t('billing.payment_method_stripe') }}
                  @else
                  {{ dashboard_t('billing.payment_method_missing') }}
                  @endif
                </p>
              </div>

              @if($stripeCustomerId)
              <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" target="_blank">
                <input type="hidden" name="action" value="dashboard_open_billing_portal">
                    <input type="hidden" name="lang" value="{{ dashboard_lang() }}">
                @php wp_nonce_field('dashboard_open_billing_portal', '_wpnonce'); @endphp

                <button
                  type="submit"
                  class="text-sm font-semibold text-brand-primary hover:text-white transition whitespace-nowrap">
                  {{ dashboard_t('billing.manage_subscription') }}
                </button>
              </form>
              @endif
            </div>
          </div>

        </div>

        <aside class="lg:pt-8 px-2">
          <div class="max-w-sm text-white">
            <p class="text-base font-semibold leading-relaxed">
              {{ dashboard_t('billing.portal_intro') }}
            </p>

            <p class="mt-4 text-sm leading-relaxed text-slate-400">
              {{ dashboard_t('billing.portal_description') }}
            </p>

          </div>
        </aside>

      </div>

    </section>

    @endif

    {{-- SECURITY TAB --}}
    @if ($tab === 'security')

    <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="max-w-md space-y-6">
      @csrf
      <input type="hidden" name="action" value="dashboard_update_password">
      <input type="hidden" name="lang" value="{{ dashboard_lang() }}">
      @php wp_nonce_field('dashboard_update_password', '_wpnonce'); @endphp

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          {{ dashboard_t('settings.password.old_password') }}
        </label>
        <input type="password" name="current_password"
          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          {{ dashboard_t('settings.password.new_password') }}
        </label>
        <input type="password" name="new_password"
          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
          {{ dashboard_t('settings.password.retype_password') }}
        </label>
        <input type="password" name="new_password_confirm"
          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
      </div>

      <div class="pt-4">
        <button type="submit"
          class="px-6 py-3 rounded-xl bg-brand-primary text-white text-sm font-semibold hover:bg-brand-primaryHover transition">
          {{ dashboard_t('settings.password.update_password') }}
        </button>
      </div>

    </form>

    @endif


    {{-- APPEARANCE TAB --}}
    @if ($tab === 'appearance')

    <div class="max-w-lg space-y-10">

      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-semibold">{{ dashboard_t('settings.appearance.theme') }}</h3>
          <p class="text-xs text-slate-500">{{ dashboard_t('settings.appearance.theme_description') }}</p>
        </div>

        <button
          type="button"
          data-appearance-theme
          data-current="{{ $currentTheme }}"
          data-label-light="{{ dashboard_t('settings.appearance.light') }}"
          data-label-dark="{{ dashboard_t('settings.appearance.dark') }}"
          data-ajax="{{ admin_url('admin-ajax.php') }}"
          data-nonce="{{ wp_create_nonce('dashboard_theme_toggle') }}"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm">
          {{ $currentTheme === 'dark' ? dashboard_t('settings.appearance.dark') : dashboard_t('settings.appearance.light') }}
        </button>

      </div>

      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-semibold">{{ dashboard_t('settings.appearance.sidebar') }}</h3>
          <p class="text-xs text-slate-500">{{ dashboard_t('settings.appearance.sidebar_description') }}</p>
        </div>

        <button
          type="button"
          data-appearance-sidebar
          data-current="{{ $sidebarCollapsed ? 'collapsed' : 'expanded' }}"
          data-label-collapsed="{{ dashboard_t('settings.appearance.collapsed') }}"
          data-label-expanded="{{ dashboard_t('settings.appearance.expanded') }}"
          data-ajax="{{ admin_url('admin-ajax.php') }}"
          data-nonce="{{ wp_create_nonce('dashboard_sidebar_toggle') }}"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm">
          {{ $sidebarCollapsed ? dashboard_t('settings.appearance.collapsed') : dashboard_t('settings.appearance.expanded') }}
        </button>

      </div>

    </div>

    @endif

  </div>

</section>

@endsection
