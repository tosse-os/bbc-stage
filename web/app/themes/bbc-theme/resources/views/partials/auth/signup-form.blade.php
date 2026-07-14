<div class="rounded-2xl bg-white p-10 shadow-[0_20px_60px_rgba(0,0,0,0.25)]">

  <div class="mb-6 grid grid-cols-2 gap-4">
    <button type="button" class="flex items-center justify-center gap-2 rounded-md border border-slate-200 bg-white py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
      <img src="{{ Vite::asset('resources/images/landingpage/google.svg') }}" alt="" class="h-4 w-4">
      {!! pll__('Mit Google anmelden') !!}
    </button>

    <button type="button" class="flex items-center justify-center gap-2 rounded-md border border-slate-200 bg-white py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
      <img src="{{ Vite::asset('resources/images/landingpage/apple.svg') }}" alt="" class="h-4 w-4">
      {!! pll__('Mit Apple anmelden') !!}
    </button>
  </div>

  <h2 class="text-xl font-semibold text-slate-900">
    {!! pll__('Ihr') !!}
    <span class="text-brand-primary">
      Bloombridge Capital
    </span>
    {!! pll__('Konto erstellen') !!}
  </h2>

  <form class="mt-6 space-y-5">

    <div>
      <input type="text" placeholder="{{ pll__('Vollständiger Name') }}" class="w-full rounded-md border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-primary focus:outline-none">
    </div>

    <div>
      <input type="email" placeholder="{{ pll__('E-Mail-Adresse') }}" class="w-full rounded-md border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-primary focus:outline-none">
    </div>

    <div>
      <input type="password" placeholder="{{ pll__('Passwort') }}" class="w-full rounded-md border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-primary focus:outline-none">
    </div>

    <button type="submit" class="mt-4 w-full rounded-md bg-brand-primary py-3 text-base font-medium text-white transition hover:bg-brand-primaryHover">
      {!! pll__('Jetzt gratis starten') !!}
    </button>

    <ul class="mt-6 space-y-3 text-base font-base text-slate-700">
      <li class="flex items-start gap-3">
        @include('icons.check')
        <span>{!! pll__('7 Tage kostenlos testen') !!}</span>
      </li>

      <li class="flex items-start gap-3">
        @include('icons.check')
        <span>{!! pll__('Keine Zahlungsdaten beim Start') !!}</span>
      </li>

      <li class="flex items-start gap-3">
        @include('icons.check')
        <span>{!! pll__('Kündigung jederzeit mit einem Klick') !!}</span>
      </li>
    </ul>

    {{--
    <div class="mt-6 text-xs text-slate-400">
      Zahlungsabwicklung später über Stripe.
    </div>
    --}}

    {{--
    <div class="mt-3 flex items-center gap-4 text-xs text-slate-400">
      <span>stripe</span>
      <span>VISA</span>
      <span>Mastercard</span>
      <span>SEPA</span>
    </div>
    --}}

    {{--
    <div class="mt-6 border-t border-slate-200 pt-4">
      <div class="flex items-center justify-center gap-5 opacity-60">
        <img src="{{ asset('images/payments/stripe.svg') }}" alt="Stripe" class="h-4">
    <img src="{{ asset('images/payments/visa.svg') }}" alt="Visa" class="h-4">
    <img src="{{ asset('images/payments/mastercard.svg') }}" alt="Mastercard" class="h-4">
    <img src="{{ asset('images/payments/sepa.svg') }}" alt="SEPA" class="h-4">
</div>
</div>
--}}

<p class="mt-5 text-center text-xs text-slate-400">
  {!! pll__('Mit Klick auf „Jetzt gratis starten“ stimmst du unseren AGB und der Datenschutzerklärung zu.') !!}
</p>

<div class="mt-2 pt-2 flex justify-center">
  <img
    src="{{ asset('resources/images/landingpage/trustline.png') }}"
    alt="Zahlungsmethoden"
    class="h-7 opacity-70">
</div>

{{--
    <div class="mt-6 pt-4 flex justify-center">
      <img
        src="{{ asset('resources/images/trustline-2.png') }}"
alt="Payment methods"
class="h-7 opacity-60">
</div>
--}}

</form>
</div>
