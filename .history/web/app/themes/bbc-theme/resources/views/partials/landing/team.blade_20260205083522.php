<section id="team" class="relative overflow-hidden py-20 lg:py-28 scroll-mt-15">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-team"></div>
    <div class="absolute inset-0 team-pattern"></div>
  </div>

  <div class="relative container-content">

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
    ['name' => 'Jennifer', 'role' => 'Manager', 'image' => 'team/markus.jpg'],
    ['name' => 'Sarah', 'role' => 'Manager', 'image' => 'team/markus.jpg'],
    ['name' => 'Markus', 'role' => 'Manager', 'image' => 'team/markus.jpg'],
    ['name' => 'Thomas', 'role' => 'Manager', 'image' => 'team/markus.jpg'],
    ['name' => 'Michael', 'role' => 'Senior Consultant', 'image' => 'team/markus.jpg'],
    ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 lg:gap-14 mb-14">
      @foreach (array_slice($team, 0, 3) as $member)
      <div class="text-center group">
        <div class="team-photo-wrapper">
          <img
            src="{{ Vite::asset('resources/images/' . $member['image']) }}"
            alt="{{ $member['name'] }}"
            class="team-photo">
        </div>

        <p class="mt-4 text-base font-semibold text-brand-primaryFontDark">
          {{ $member['name'] }}
        </p>
        <p class="mt-0.5 text-sm text-slate-500">
          {{ $member['role'] }}
        </p>
      </div>
      @endforeach
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 lg:gap-14 max-w-3xl mx-auto">
      @foreach (array_slice($team, 3, 2) as $member)
      <div class="text-center group">
        <div class="team-photo-wrapper">
          <img
            src="{{ Vite::asset('resources/images/' . $member['image']) }}"
            alt="{{ $member['name'] }}"
            class="team-photo">
        </div>

        <p class="mt-5 text-lg font-semibold text-brand-primaryFontDark">
          {{ $member['name'] }}
        </p>
        <p class="mt-1 text-sm font-medium text-brand-primary/70">
          {{ $member['role'] }}
        </p>
      </div>
      @endforeach
    </div>

  </div>
</section>
