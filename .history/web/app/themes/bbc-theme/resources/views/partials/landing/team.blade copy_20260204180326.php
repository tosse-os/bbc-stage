<section id="team" class="relative overflow-hidden py-20 lg:py-28 scroll-mt-15">
  {{-- Background wie andere Sections --}}
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-contact"></div>
    <div class="absolute inset-0 contact-pattern"></div>
  </div>

  <div class="relative container-content">

    {{-- Headline --}}
    <div class="mb-16 text-center">
      <h2 class="text-3xl lg:text-4xl font-semibold tracking-tight text-brand-primaryFontDark">
        Dedicated <span class="text-brand-primary">Expert</span> Consultants
      </h2>
      <p class="mt-4 mx-auto max-w-xl text-sm lg:text-base text-slate-600">
        Meet our team of experienced professionals ready to support your financial journey
      </p>
    </div>

    @php
      $team = [
        ['name' => 'Jennifer', 'role' => 'Manager', 'image' => 'team/jennifer.jpg'],
        ['name' => 'Sarah', 'role' => 'Manager', 'image' => 'team/sarah.jpg'],
        ['name' => 'Markus', 'role' => 'Manager', 'image' => 'team/markus.jpg'],
        ['name' => 'Thomas', 'role' => 'Manager', 'image' => 'team/thomas.jpg'],
        ['name' => 'Michael', 'role' => 'Senior Consultant', 'image' => 'team/michael.jpg'],
      ];
    @endphp

    {{-- Row 1: 3 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 lg:gap-14 mb-14">
      @foreach (array_slice($team, 0, 3) as $member)
        <div class="text-center">
          <div
            class="mx-auto mb-4 w-[220px] overflow-hidden rounded-2xl bg-white/90
                   shadow-[0_18px_45px_rgba(15,70,85,0.22)]">

            <img
              src="@asset('images/' . $member['image'])"
              alt="{{ $member['name'] }}"
              class="h-full w-full object-cover">
          </div>

          <p class="text-base font-semibold text-brand-primaryFontDark">
            {{ $member['name'] }}
          </p>
          <p class="mt-1 text-sm text-slate-500">
            {{ $member['role'] }}
          </p>
        </div>
      @endforeach
    </div>

    {{-- Row 2: 2 zentriert --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 lg:gap-14 max-w-3xl mx-auto">
      @foreach (array_slice($team, 3, 2) as $member)
        <div class="text-center">
          <div
            class="mx-auto mb-4 w-[220px] overflow-hidden rounded-2xl bg-white/90
                   shadow-[0_18px_45px_rgba(15,70,85,0.22)]">

            <img
              src="@asset('images/' . $member['image'])"
              alt="{{ $member['name'] }}"
              class="h-full w-full object-cover">
          </div>

          <p class="text-base font-semibold text-brand-primaryFontDark">
            {{ $member['name'] }}
          </p>
          <p class="mt-1 text-sm text-slate-500">
            {{ $member['role'] }}
          </p>
        </div>
      @endforeach
    </div>

  </div>
</section>
