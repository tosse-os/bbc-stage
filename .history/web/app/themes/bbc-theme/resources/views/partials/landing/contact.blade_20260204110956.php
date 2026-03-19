<section id="contact" class="relative overflow-hidden py-28 scroll-mt-20">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-contact"></div>
    <div class="absolute inset-0 contact-pattern"></div>
  </div>

  <div class="relative container-content">
    <div class="grid grid-cols-[2fr_1.1fr] gap-24 items-start">

      {{-- LEFT --}}
      <div>
        <h2 class="text-4xl font-semibold tracking-tight text-brand-primaryFontDark">
          Need help or have questions?
        </h2>

        <p class="mt-5 max-w-[450] text-lg leading-relaxed text-slate-500">
          Our platform is designed to be self-explanatory.
          If you need support, have technical questions or want to discuss enterprise access,
          we’re here to help.
        </p>

        <div class="mt-14 space-y-9">
          <div class="flex items-center gap-5">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/90 shadow-[0_10px_30px_rgba(15,70,85,0.16)] text-brand-primary">
              @include('icons.message')
            </div>
            <span class="text-xl font-medium text-brand-primaryFontDark">
              Technical questions
            </span>
          </div>

          <div class="flex items-center gap-5">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/90 shadow-[0_10px_30px_rgba(15,70,85,0.16)] text-brand-primary">
              @include('icons.message')
            </div>
            <span class="text-xl font-medium text-brand-primaryFontDark">
              Enterprise / institutional access
            </span>
          </div>

          <div class="flex items-center gap-5">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/90 shadow-[0_10px_30px_rgba(15,70,85,0.16)] text-brand-primary">
              @include('icons.message')
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

          <form class="space-y-4">
            <input
              type="email"
              placeholder="Your e-mail"
              class="w-full rounded-md border border-[#c9dbe2] bg-white px-4 py-2.5 text-[14px] text-[#3f5f6b] placeholder-[#8fa7b1] focus:border-[#5fa6bb] focus:outline-none">

            <textarea
              rows="5"
              placeholder="How can we assist you?"
              class="w-full resize-none rounded-md border border-[#c9dbe2] bg-white px-4 py-3 text-[14px] text-[#3f5f6b] placeholder-[#8fa7b1] focus:border-[#5fa6bb] focus:outline-none"></textarea>

            <button
              type="submit"
              class="mt-4 w-full rounded-md bg-[#3f879c] py-3 text-[14px] font-medium text-white shadow-[0_8px_22px_rgba(63,135,156,0.45)] transition hover:bg-[#35788b]">
              Send message
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>
