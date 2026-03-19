<section id="contact" class="relative overflow-hidden py-28 scroll-mt-20 bg-gradient-to-b from-[#9fc6d1] to-[#cfe1e6]">
  <div class="absolute inset-0 pointer-events-none"
    style="background-image:
         linear-gradient(rgba(255,255,255,0.25) 1px, transparent 1px),
         linear-gradient(90deg, rgba(255,255,255,0.25) 1px, transparent 1px);
         background-size:48px 48px;">
  </div>

  <div class="relative container-content">
    <div class="grid grid-cols-1 gap-20 lg:grid-cols-[1.2fr_0.8fr] items-start">

      <div>
        <h2 class="text-4xl font-semibold tracking-tight text-[#0b3a4a]">
          {{ get_field('contact_headline') ?: 'Need help or have questions?' }}
        </h2>

        <p class="mt-4 max-w-xl text-lg leading-relaxed text-[#4f6f7b]">
          {{ get_field('contact_intro') ?: 'Our platform is designed to be self-explanatory. If you need support, have technical questions or want to discuss enterprise access, we’re here to help.' }}
        </p>

        <div class="mt-14 space-y-10">
          @for($i = 1; $i <= 3; $i++)
            @php($label=get_field("contact_item_{$i}_label"))
            @if($label)
            <div class="flex items-center gap-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-[0_8px_24px_rgba(0,0,0,0.12)] text-brand-primary">
              @include('icons.message')
            </div>
            <div class="text-lg font-medium text-[#0b3a4a]">
              {!! $label !!}
            </div>
        </div>
        @endif
        @endfor
      </div>
    </div>

    <div class="relative">
      <div class="rounded-2xl bg-white p-8 shadow-[0_25px_70px_rgba(0,0,0,0.22)]">
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
            class="w-full resize-none rounded-md border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-primary focus:outline-none"></textarea>

          <button
            type="submit"
            class="mt-2 w-full rounded-md bg-brand-primary py-3 text-sm font-medium text-white transition hover:bg-brand-primaryHover">
            Send message
          </button>
        </form>
      </div>
    </div>

  </div>
  </div>
</section>
