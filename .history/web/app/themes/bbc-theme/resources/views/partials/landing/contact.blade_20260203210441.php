{{-- resources/views/partials/landing/contact.blade.php --}}

<section id="contact" class="relative overflow-hidden py-32 scroll-mt-24">

  {{-- Background --}}
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

  <div class="container-content">
    <div class="grid grid-cols-1 gap-x-24 gap-y-20 lg:grid-cols-[2fr_1fr] lg:items-start">

      {{-- LEFT --}}
      <div>
        <h2 class="text-4xl font-semibold tracking-tight text-[#0b3a4a]">
          Need help or have questions?
        </h2>

        <p class="mt-6 max-w-xl text-lg leading-relaxed text-[#4f6f7b]">
          Our platform is designed to be self-explanatory.
          If you need support, have technical questions or want to discuss enterprise access, we’re here to help.
        </p>

        <div class="mt-16 space-y-10">

          <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-brand-primary shadow-[0_10px_30px_rgba(0,0,0,0.08)]">
              @include('icons.message')
            </div>
            <div class="text-lg font-medium text-[#0b3a4a]">
              Technical questions
            </div>
          </div>

          <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-brand-primary shadow-[0_10px_30px_rgba(0,0,0,0.08)]">
              @include('icons.message')
            </div>
            <div class="text-lg font-medium text-[#0b3a4a]">
              Enterprise / institutional access
            </div>
          </div>

          <div class="flex items-center gap-5">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-brand-primary shadow-[0_10px_30px_rgba(0,0,0,0.08)]">
              @include('icons.message')
            </div>
            <div class="text-lg font-medium text-[#0b3a4a]">
              Data & API inquiries
            </div>
          </div>

        </div>
      </div>

      {{-- RIGHT --}}
      <div class="relative">
        <div class="rounded-2xl bg-white p-10 shadow-[0_25px_70px_rgba(0,0,0,0.18)]">

          <h3 class="text-lg font-semibold text-slate-900">
            Get in touch
          </h3>

          <form class="mt-6 space-y-4">

            <div class="relative">
              <input
                type="email"
                placeholder="Your e-mail"
                class="w-full rounded-md border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-primary focus:outline-none">
            </div>

            <textarea
              rows="5"
              placeholder="How can we assist you?"
              class="w-full resize-none rounded-md border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-primary focus:outline-none"></textarea>

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
