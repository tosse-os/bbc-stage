<section
  id="contact"
  class="relative overflow-hidden py-28 scroll-mt-20">
  <div class="absolute inset-0 -z-10">
    <div class="h-full w-full bg-gradient-to-b from-[#9ec4cf] to-[#cfe1e6]"></div>
    <div class="absolute inset-0 opacity-40" style="background-image:linear-gradient(rgba(255,255,255,.25) 1px, transparent 1px),linear-gradient(90deg, rgba(255,255,255,.25) 1px, transparent 1px);background-size:48px 48px;"></div>
  </div>

  <div class="container-content">
    <div class="grid grid-cols-1 items-start gap-20 lg:grid-cols-[2fr_1fr]">

      <div>
        <h2 class="text-4xl font-semibold tracking-tight text-slate-900">
          {{ get_field('contact_headline') ?: 'Need help or have questions?' }}
        </h2>

        <p class="mt-4 max-w-xl text-lg text-slate-600">
          {{ get_field('contact_intro') ?: 'Our platform is designed to be self-explanatory. If you need support or have questions, we are here to help.' }}
        </p>

        <div class="mt-12 space-y-8">
          @for($i = 1; $i <= 3; $i++)
            @php
            $label=get_field("contact_item_{$i}_label");
            @endphp

            @if($label)
            <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/70 text-brand-primary shadow-[0_6px_20px_rgba(0,0,0,0.08)]">
              @include('icons.message')
            </div>
            <div class="text-lg font-medium text-slate-800">
              {!! $label !!}
            </div>
        </div>
        @endif
        @endfor
      </div>
    </div>

    <div>
      <div class="rounded-2xl bg-white p-8 shadow-[0_20px_60px_rgba(0,0,0,0.15)]">
        <h3 class="text-lg font-semibold text-slate-900">
          {{ get_field('contact_form_headline') ?: 'Get in touch' }}
        </h3>

        <form class="mt-6 space-y-4">
          <input
            type="email"
            placeholder="Your e-mail"
            class="w-full rounded-md border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-primary focus:outline-none">

          <textarea
            rows="5"
            placeholder="How can we assist you?"
            class="w-full rounded-md border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-primary focus:outline-none"></textarea>

          <button
            type="submit"
            class="w-full rounded-md bg-brand-primary py-3 text-sm font-medium text-white transition hover:bg-brand-primaryHover">
            Send message
          </button>
        </form>
      </div>
    </div>

  </div>
  </div>
</section>
