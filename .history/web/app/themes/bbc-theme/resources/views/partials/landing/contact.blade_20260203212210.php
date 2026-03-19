<section
  id="contact"
  class="relative overflow-hidden py-28 scroll-mt-20 full-bleed">

  {{-- BACKGROUND (IMMER SICHTBAR, FULL BLEED) --}}
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-b from-[#9fc6d1] to-[#cfe1e6]"></div>

    <div
      class="absolute inset-0 opacity-40"
      style="
        background-image:
          linear-gradient(rgba(255,255,255,0.25) 1px, transparent 1px),
          linear-gradient(90deg, rgba(255,255,255,0.25) 1px, transparent 1px);
        background-size:48px 48px;
      ">
    </div>
  </div>

  {{-- CONTENT --}}
  <div class="relative container-content">
    <div
      class="grid gap-24 items-start"
      style="grid-template-columns: 2fr 1.1fr">

      {{-- LEFT --}}
      <div>
        <h2 class="text-4xl font-semibold tracking-tight text-[#0b3a4a]">
          Need help or have questions?
        </h2>

        <p class="mt-6 max-w-xl text-lg leading-relaxed text-[#4f6f7b]">
          Our platform is designed to be self-explanatory.
          If you need support, have technical questions or want to discuss enterprise access,
          we’re here to help.
        </p>

        <div class="mt-16 space-y-10">
          <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-[0_10px_30px_rgba(0,0,0,0.08)]">
              @include('icons.message')
            </div>
            <span class="text-lg font-medium text-[#0b3a4a]">
              Technical questions
            </span>
          </div>

          <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-[0_10px_30px_rgba(0,0,0,0.08)]">
              @include('icons.message')
            </div>
            <span class="text-lg font-medium text-[#0b3a4a]">
              Enterprise / institutional access
            </span>
          </div>

          <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-[0_10px_30px_rgba(0,0,0,0.08)]">
              @include('icons.message')
            </div>
            <span class="text-lg font-medium text-[#0b3a4a]">
              Data & API inquiries
            </span>
          </div>
        </div>
      </div>

      {{-- RIGHT --}}
      <div class="relative">
        <div class="rounded-2xl bg-[#e6f2f6] p-6 shadow-[0_20px_50px_rgba(0,0,0,0.18)]">
          <h3 class="mb-4 text-base font-semibold text-slate-900">
            Get in touch
          </h3>

          <form class="space-y-4">
            <div class="relative">
              <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#5fa6bb]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                  <path d="M21 15a4 4 0 0 1-4 4H7l-4 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />
                </svg>
              </span>
              <input
                type="email"
                placeholder="Your e-mail"
                class="w-full rounded-md border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 focus:border-[#5fa6bb] focus:outline-none">
            </div>

            <textarea
              rows="5"
              placeholder="Your message&#10;How can we assist you?"
              class="w-full resize-none rounded-md border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-[#5fa6bb] focus:outline-none"></textarea>

            <button
              type="submit"
              class="mt-2 w-full rounded-md bg-[#3f879c] py-3 text-sm font-medium text-white transition hover:bg-[#35788b]">
              Send message
            </button>
          </form>
        </div>

      </div>

    </div>
  </div>
</section>
