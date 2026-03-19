<section id="contact" class="relative overflow-hidden py-16 lg:py-28">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-contact"></div>
    <div class="absolute inset-0 contact-pattern  pattern-section"></div>
  </div>

  <div class="relative container-content">
    <div class="grid grid-cols-1 lg:grid-cols-[1.6fr_1.1fr] gap-12 lg:gap-x-20 lg:gap-y-24 items-start">

      {{-- LEFT --}}
      <div class="text-center lg:text-left">
        <h2 class="reveal-text text-3xl lg:text-4xl font-semibold tracking-tight text-brand-primaryFontDark" data-reveal-delay="0">
          Need help or have questions?
        </h2>

        <p class="reveal-text mt-5 mx-auto lg:mx-0 max-w-[440px] text-base lg:text-lg leading-relaxed text-slate-500" data-reveal-delay="120">
          Our platform is designed to be self-explanatory.
          If you need support, have technical questions or want to discuss enterprise access,
          we’re here to help.
        </p>

        <div class="mt-10 lg:mt-14 space-y-6 lg:space-y-9">
          <div class="reveal-text group flex flex-col lg:flex-row items-center lg:items-center gap-4 lg:gap-8" data-reveal-delay="150">
            <div class="flex h-12 w-12 lg:h-13 lg:w-13 items-center justify-center rounded-xl bg-white/90 shadow-[0_10px_30px_rgba(15,70,85,0.16)] text-brand-primary transition-all duration-300 group-hover:scale-105 group-hover:bg-white group-hover:shadow-xl">
              @include('icons.message')
            </div>
            <span class="text-lg lg:text-xl font-medium text-brand-primaryFontDark">
              Technical questions
            </span>
          </div>

          <div class="reveal-text group flex flex-col lg:flex-row items-center lg:items-center gap-4 lg:gap-8" data-reveal-delay="300">
            <div class="flex h-12 w-12 lg:h-13 lg:w-13 items-center justify-center rounded-xl bg-white/90 shadow-[0_10px_30px_rgba(15,70,85,0.16)] text-brand-primary transition-all duration-300 group-hover:scale-105 group-hover:bg-white group-hover:shadow-xl">
              @include('icons.enterprise')
            </div>
            <span class="text-lg lg:text-xl font-medium text-brand-primaryFontDark">
              Enterprise / institutional access
            </span>
          </div>

          <div class="reveal-text group flex flex-col lg:flex-row items-center lg:items-center gap-4 lg:gap-8" data-reveal-delay="450">
            <div class="flex h-12 w-12 lg:h-13 lg:w-13 items-center justify-center rounded-xl bg-white/90 shadow-[0_10px_30px_rgba(15,70,85,0.16)] text-brand-primary transition-all duration-300 group-hover:scale-105 group-hover:bg-white group-hover:shadow-xl">
              @include('icons.api')
            </div>
            <span class="text-lg lg:text-xl font-medium text-brand-primaryFontDark">
              Data & API inquiries
            </span>
          </div>
        </div>
      </div>

      {{-- RIGHT --}}
      <div class="mt-4 lg:mt-16">
        <div class="reveal-media contact-card-soft rounded-[22px] lg:rounded-[26px] bg-white/85 px-6 py-8 lg:px-9 shadow-[0_22px_55px_rgba(15,70,85,0.28)]" data-reveal-delay="0">
          <h3 class="mb-6 text-[17px] font-semibold text-brand-primaryFontDark text-center lg:text-left">
            Get in touch
          </h3>

          <form
            id="contactForm"
            data-ajax-url="{{ admin_url('admin-ajax.php') }}"
            class="space-y-4"
            novalidate>

            <div
              id="contactSuccess"
              class="overflow-hidden max-h-0 opacity-0 transition-all duration-600 ease-out text-base text-green-700">
              Your message has been sent successfully.
            </div>

            <div class="space-y-1 transition-all duration-300">
              <input
                type="email"
                name="email"
                required
                placeholder="Your e-mail"
                class="w-full rounded-md border border-[#c9dbe2] bg-white px-4 py-2.5 text-[14px] text-[#3f5f6b] placeholder-[#8fa7b1] input-focus-premium">

              <p
                class="error-email overflow-hidden max-h-0 opacity-0 transition-all duration-300 text-sm text-red-600"></p>
            </div>

            <div class="space-y-1 transition-all duration-300">
              <textarea
                name="message"
                rows="5"
                required
                placeholder="How can we assist you?"
                class="w-full resize-none rounded-md border border-[#c9dbe2] bg-white px-4 py-3 text-[14px] text-[#3f5f6b] placeholder-[#8fa7b1] input-focus-premium"></textarea>

              <p
                class="error-message overflow-hidden max-h-0 opacity-0 transition-all duration-300 text-sm text-red-600"></p>
            </div>

            <button
              type="submit"
              class="mt-4 w-full rounded-md bg-gradient-to-b from-[#4a97ad] to-[#3f879c] py-3 text-[14px] font-medium text-white shadow-[0_8px_22px_rgba(63,135,156,0.45)] transition-all duration-300 hover:from-[#3f879c] hover:to-[#35788b] hover:shadow-[0_12px_28px_rgba(63,135,156,0.45)] active:scale-[0.98] cursor-pointer">
              Send message
            </button>

          </form>

        </div>
      </div>

    </div>
  </div>
</section>
