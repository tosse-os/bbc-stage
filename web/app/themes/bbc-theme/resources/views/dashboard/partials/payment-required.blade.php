<section class="max-w-3xl mx-auto">

  <div class="bg-dashboard-card rounded-xl p-8 shadow-lg">
    <h1 class="text-2xl font-semibold mb-4">
      {{ dashboard_t('payment_required.title') }}
    </h1>

    <p class="text-dashboard-muted mb-6">
      {{ dashboard_t('payment_required.text') }}
    </p>

    <a href="{{ dashboard_settings_billing_url() }}"
      class="inline-flex items-center px-6 py-3 rounded-lg bg-primary text-white hover:bg-primary-dark transition">
      {{ dashboard_t('payment_required.button') }}
    </a>
  </div>

</section>
