<section id="team" class="relative py-20 lg:py-28">
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-[#d7e9ee] to-[#b9d6df]"></div>

  <div class="container-content">
    {{-- Headline --}}
    <div class="mb-14 text-center">
      <h2 class="text-3xl lg:text-4xl font-semibold tracking-tight text-brand-primaryFontDark">
        Dedicated <span class="text-brand-primary">Expert</span> Consultants
      </h2>
      <p class="mt-4 mx-auto max-w-xl text-sm lg:text-base text-slate-600">
        Meet our team of experienced professionals ready to support your financial journey
      </p>
    </div>

    {{-- Team Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10">
      @php
      $team = [
      ['name' => 'Jennifer', 'role' => 'Manager', 'image' => 'team/jennifer.jpg'],
      ['name' => 'Sarah', 'role' => 'Manager', 'image' => 'team/sarah.jpg'],
      ['name' => 'Markus', 'role' => 'Manager', 'image' => 'team/markus.jpg'],
      ['name' => 'Thomas', 'role' => 'Manager', 'image' => 'team/thomas.jpg'],
      ];
      @endphp

      @foreach ($team as $member)
      <div
        class="group rounded-2xl bg-white/90 p-5 text-center shadow-[0_18px_45px_rgba(15,70,85,0.18)]
                 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_26px_65px_rgba(15,70,85,0.28)]">

        <div class="relative mx-auto mb-4 aspect-square w-full max-w-[220px] overflow-hidden rounded-xl bg-slate-100">
          <img
            src="@asset('images/' . $member['image'])"
            alt="{{ $member['name'] }}"
            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
        </div>

        <div class="mt-2">
          <p class="text-base font-semibold text-brand-primaryFontDark">
            {{ $member['name'] }}
          </p>
          <p class="mt-0.5 text-sm text-slate-500">
            {{ $member['role'] }}
          </p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
