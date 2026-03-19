<section id="contact" class="relative overflow-hidden py-28 scroll-mt-20">
  {{-- Background --}}
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-contact"></div>
    <div class="absolute inset-0 contact-pattern"></div>
  </div>

  {{-- CONTENT --}}
  <div class="relative container-content">
    <div
      class="grid gap-24 items-start"
      style="grid-template-columns: 2fr 1.1fr">

      {{-- LEFT --}}
      <div>
        {{-- INTRO --}}
        <div>
          <h2 class="text-4xl font-semibold tracking-tight text-brand-primaryFontDark">
            Need help or have questions?
          </h2>

          <p class="mt-5 max-w-md text-lg leading-relaxed text-slate-500">
            Our platform is designed to be self-explanatory.
            If you need support, have technical questions or want to discuss enterprise access,
            we’re here to help.
          </p>
        </div>

        {{-- BULLETS --}}
        <div class="mt-14 space-y-9">
          <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/90 shadow-[0_8px_24px_rgba(15,70,85,0.12)] text-brand-primary">
              @include('icons.message')
            </div>
            <span class="text-lg font-medium text-brand-primaryFontDark">
              Technical questions
            </span>
          </div>

          <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/90 shadow-[0_8px_24px_rgba(15,70,85,0.12)] text-brand-primary">
              @include('icons.message')
            </div>
            <span class="text-lg font-medium text-brand-primaryFontDark">
              Enterprise / institutional access
            </span>
          </div>

          <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/90 shadow-[0_8px_24px_rgba(15,70,85,0.12)] text-brand-primary">
              @include('icons.message')
            </div>
            <span class="text-lg font-medium text-brand-primaryFontDark">
              Data & API inquiries
            </span>
          </div>
        </div>
      </div>

      {{-- RIGHT --}}
      <div class="relative row-start-2">
        <div class="contact-card-soft rounded-[22px] px-8 py-7 shadow-[0_18px_45px_rgba(15,70,85,0.22)]">
          <h3 class="mb-5 text-[17px] font-semibold text-[#0b3a4a]">
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
              class="mt-3 w-full rounded-md bg-[#3f879c] py-3 text-[14px] font-medium text-white shadow-[0_6px_18px_rgba(63,135,156,0.35)] transition hover:bg-[#35788b]">
              Send message
            </button>
          </form>
        </div>
      </div>


    </div>
  </div>
</section>
