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
            name="display_name"
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
            name="phone"
            value="{{ get_user_meta($user->ID, 'phone_number', true) }}"
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
    $stripeRawStatus = get_user_meta($user->ID, 'stripe_subscription_status', true);
    $currentPeriodEnd = (int) get_user_meta($user->ID, 'stripe_current_period_end', true);
    $cancelAtPeriodEnd = get_user_meta($user->ID, 'stripe_cancel_at_period_end', true) === '1';
    $billingErrorCode = trim((string) request()->get('error', ''));
    $billingErrorMessage = $billingErrorCode !== '' ? dashboard_stripe_billing_error_message($billingErrorCode) : '';

    $isActive = $subscriptionState === 'active';
    $isTrial = $subscriptionState === 'trial';

    $stateText = match ($subscriptionState) {
    'active' => 'Aktiv',
    'trial' => 'Testphase',
    'past_due' => 'Zahlung überfällig',
    'canceled' => 'Gekündigt',
    default => 'Zahlung erforderlich',
    };

    $badgeClass = $isActive || $isTrial
    ? 'bg-emerald-400/20 text-emerald-200 border-emerald-300/20'
    : 'bg-amber-400/20 text-amber-200 border-amber-300/20';
    @endphp

    <section class="max-w-6xl text-white">

      @if(request()->get('stripe') === 'success')
      <div class="mb-6 rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
        Checkout abgeschlossen. Dein Abo-Status wird automatisch aktualisiert.
      </div>
      @endif

      @if(request()->get('stripe') === 'cancel')
      <div class="mb-6 rounded-xl border border-amber-400/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">
        Checkout wurde abgebrochen.
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
                  Aktuelles Abonnement
                </h2>

                <span class="mt-3 inline-flex items-center rounded-lg border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                  {{ $stateText }}
                </span>

                <div class="mt-5">
                  <div class="text-lg font-semibold text-white">
                    Premium Analysen Zugang
                  </div>

                  <div class="text-slate-300">
                    €29,99 / Monat
                  </div>

                  @if($currentPeriodEnd)
                  <div class="text-sm text-slate-400">
                    Nächste Rechnung: {{ date_i18n('d.m.Y', $currentPeriodEnd) }}
                  </div>
                  @endif

                  @if($cancelAtPeriodEnd)
                  <div class="text-sm text-amber-300">
                    Kündigung zum Periodenende vorgemerkt.
                  </div>
                  @elseif($isActive)
                  <div class="text-sm text-slate-400">
                    Abonnement aktiv.
                  </div>
                  @elseif($isTrial)
                  <div class="text-sm text-slate-400">
                    Testphase aktiv.
                  </div>
                  @else
                  <div class="text-sm text-slate-400">
                    Kein aktives Abonnement.
                  </div>
                  @endif
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                  @if(!$isActive)
                  <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}">
                    <input type="hidden" name="action" value="dashboard_start_checkout">
                    @php wp_nonce_field('dashboard_start_checkout', '_wpnonce'); @endphp

                    <button
                      type="submit"
                      class="inline-flex items-center px-5 py-2.5 rounded-xl bg-brand-primary text-white text-sm font-semibold hover:bg-brand-primaryHover transition">
                      Abo starten
                    </button>
                  </form>
                  @endif

                  @if($stripeCustomerId)
                  <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}">
                    <input type="hidden" name="action" value="dashboard_open_billing_portal">
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
                      Im Stripe Portal verwalten
                    </button>
                  </form>
                  @endif
                </div>
              </div>

              <div class="text-xs text-slate-400 space-y-1 md:text-right">
                @if($stripeCustomerId)
                <div>
                  <span class="text-slate-500">Customer ID:</span>
                  <span class="break-all">{{ $stripeCustomerId }}</span>
                </div>
                @endif

                @if($stripeSubscriptionId)
                <div>
                  <span class="text-slate-500">Subscription ID:</span>
                  <span class="break-all">{{ $stripeSubscriptionId }}</span>
                </div>
                @endif
              </div>

            </div>
          </div>

          <div class="rounded-2xl border border-white/10 bg-slate-950/30 backdrop-blur-xl px-6 py-6 shadow-2xl">
            <div class="flex items-center justify-between gap-6">
              <div>
                <h2 class="text-lg font-semibold uppercase tracking-wide text-white">
                  Zahlungsmethode
                </h2>

                <p class="mt-4 text-slate-300">
                  @if($stripeCustomerId)
                  Zahlungsdaten werden sicher über Stripe verwaltet.
                  @else
                  Noch keine Zahlungsmethode hinterlegt.
                  @endif
                </p>
              </div>

              @if($stripeCustomerId)
              <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}">
                <input type="hidden" name="action" value="dashboard_open_billing_portal">
                @php wp_nonce_field('dashboard_open_billing_portal', '_wpnonce'); @endphp

                <button
                  type="submit"
                  class="text-sm font-semibold text-brand-primary hover:text-white transition whitespace-nowrap">
                  Im Stripe Portal verwalten
                </button>
              </form>
              @endif
            </div>
          </div>

          <div class="rounded-2xl border border-white/10 bg-slate-950/30 backdrop-blur-xl px-6 py-6 shadow-2xl">
            <h2 class="text-lg font-semibold uppercase tracking-wide text-white">
              Rechnungsverlauf
            </h2>

            <div class="mt-5 overflow-hidden rounded-xl border border-white/10">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-white/10 text-slate-300">
                    <th class="px-4 py-3 text-left font-semibold">Datum</th>
                    <th class="px-4 py-3 text-left font-semibold">Betrag</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-right font-semibold">PDF</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="text-slate-400">
                    <td class="px-4 py-4">—</td>
                    <td class="px-4 py-4">—</td>
                    <td class="px-4 py-4">Noch keine Rechnungen</td>
                    <td class="px-4 py-4 text-right">—</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <p class="mt-4 text-xs text-slate-500">
              Rechnungen und Zahlungsbelege sind im Stripe Portal verfügbar.
            </p>
          </div>

        </div>

        <aside class="lg:pt-8 px-2">
          <div class="max-w-sm text-white">
            <p class="text-base font-semibold leading-relaxed">
              Um Abonnement, Zahlungsdaten oder Rechnungsdetails zu ändern, nutze bitte das sichere Stripe Portal.
            </p>

            <p class="mt-4 text-sm leading-relaxed text-slate-400">
              Dort kannst du Zahlungsmethoden aktualisieren, Rechnungen einsehen und dein Abo verwalten.
            </p>

            @if($stripeRawStatus)
            <div class="mt-8 rounded-xl border border-white/10 bg-white/5 px-4 py-3">
              <div class="text-xs uppercase tracking-wide text-slate-500">
                Stripe Status
              </div>
              <div class="mt-1 text-sm font-medium text-slate-300">
                {{ $stripeRawStatus }}
              </div>
            </div>
            @endif
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
