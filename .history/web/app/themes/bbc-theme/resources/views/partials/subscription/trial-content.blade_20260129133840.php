<section class="relative bg-slate-950">
  <div class="absolute inset-0">
    <div class="bg-gradient-to-b from-slate-950/80 via-slate-950/60 to-slate-950/90 h-full w-full"></div>
  </div>

  <div class="relative container-content pb-32">
    <div class="grid grid-cols-1 items-stretch gap-12 lg:grid-cols-2">
      @include('partials.subscription.plan-card')
      @include('partials.auth.signup-form')
    </div>
  </div>
</section>
