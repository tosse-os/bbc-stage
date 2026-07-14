@extends('layouts.dashboard')

@section('content')

<section class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-semibold mb-6">{{ dashboard_t('settings.tabs.billing') }}</h1>

  <div class="bg-dashboard-card p-6 rounded-xl">
    <p class="text-dashboard-muted">
      {{ dashboard_t('billing.integration_pending') }}
    </p>
  </div>
</section>

@endsection
