<section id="contact" class="relative overflow-hidden py-28 scroll-mt-15">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-contact"></div>
    <div class="absolute inset-0 contact-pattern"></div>
  </div>

  <div class="relative container-content">
    <div class="grid grid-cols-[1.6fr_1.1fr] gap-x-20 gap-y-24 items-start">

      {{-- LEFT --}}
      <div>
        <h2 class="text-4xl font-semibold tracking-tight text-brand-primaryFontDark">
          Need help or have questions?
        </h2>

        <p class="mt-5 max-w-[440px] text-lg leading-relaxed text-slate-500">
          Our platform is designed to be self-explanatory.
          If you need support, have technical questions or want to discuss enterprise access,
          we’re here to help.
        </p>

        <div class="mt-14 space-y-9">
          <div class="group flex items-center gap-8">
            <div class="flex h-13 w-13 items-center justify-center rounded-xl bg-white/90 shadow-[0_10px_30px_rgba(15,70,85,0.16)] text-brand-primary transition-all duration-300 group-hover:scale-105 group-hover:bg-white group-hover:shadow-xl">
              @include('icons.message')
            </div>
            <span class="text-xl font-medium text-brand-primaryFontDark">
              Technical questions
            </span>
          </div>

          <div class="group flex items-center gap-8">
            <div class="flex h-13 w-13 items-center justify-center rounded-xl bg-white/90 shadow-[0_10px_30px_rgba(15,70,85,0.16)] text-brand-primary transition-all duration-300 group-hover:scale-105 group-hover:bg-white group-hover:shadow-xl">
              @include('icons.enterprise')
            </div>
            <span class="text-xl font-medium text-brand-primaryFontDark">
              Enterprise / institutional access
            </span>
          </div>

          <div class="group flex items-center gap-8">
            <div class="flex h-13 w-13 items-center justify-center rounded-xl bg-white/90 shadow-[0_10px_30px_rgba(15,70,85,0.16)] text-brand-primary transition-all duration-300 group-hover:scale-105 group-hover:bg-white group-hover:shadow-xl">
              @include('icons.api')
            </div>
            <span class="text-xl font-medium text-brand-primaryFontDark">
              Data & API inquiries
            </span>
          </div>
        </div>
      </div>

      {{-- RIGHT --}}
      <div class="mt-16">
        <div class="contact-card-soft rounded-[26px] bg-white/85 px-9 py-8 shadow-[0_22px_55px_rgba(15,70,85,0.28)]">
          <h3 class="mb-6 text-[17px] font-semibold text-brand-primaryFontDark">
            Get in touch
          </h3>

          <form id="contactForm" novalidate class="space-y-4">
            <div>
              <input type="email" name="email" class="input-focus-premium w-full rounded-md border px-4 py-2.5 text-[14px]">
              <p class="mt-1 text-sm text-red-500 hidden" data-error-for="email"></p>
            </div>

            <div>
              <textarea name="message" rows="5" class="input-focus-premium w-full rounded-md border px-4 py-3 text-[14px]"></textarea>
              <p class="mt-1 text-sm text-red-500 hidden" data-error-for="message"></p>
            </div>

            <button type="submit" class="w-full rounded-md bg-brand-primary py-3 text-white">
              Send message
            </button>
          </form>


        </div>
      </div>

    </div>
  </div>
</section>
