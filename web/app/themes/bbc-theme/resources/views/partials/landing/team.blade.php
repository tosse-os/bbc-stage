<section id="team" class="relative overflow-hidden py-20 lg:py-28 scroll-mt-15">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-team"></div>
    <div class="absolute inset-0 team-pattern pattern-section"></div>
  </div>

  <div class="relative container-content">

    @php
    $headline = $team['headline'];
    $subheadline = $team['subheadline'];
    $bottomline = $team['bottomline'];
    $members = $team['members'];
    @endphp

    <div class="mb-16 text-center">
      @if($headline)
      <h2 class="reveal-text text-3xl lg:text-4xl font-semibold tracking-tight text-brand-primaryFontDark">
        {!! $headline !!}
      </h2>
      @endif

      @if($subheadline)
      <p class="reveal-text mt-4 mx-auto max-w-xl text-sm lg:text-base text-slate-600">
        {!! $subheadline !!}
      </p>
      @endif
    </div>

    @if(!empty($members))

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-14 mb-14">
      @foreach (array_slice($members, 0, 3) as $member)
      @if(!empty($member))
      <div class="reveal-media text-center group">
        <div class="team-photo-wrapper">
          @if(!empty($member['image']))
          <img
            src="{{ $member['image']['url'] }}"
            alt="{{ $member['image']['alt'] ?? $member['name'] }}"
            class="team-photo">
          @endif
        </div>

        @if(!empty($member['name']))
        <p class="mt-4 text-lg font-semibold text-brand-primaryFontDark">
          {{ $member['name'] }}
        </p>
        @endif

        @if(!empty($member['role']))
        <p class="mt-0.5 text-sm text-slate-500">
          {{ $member['role'] }}
        </p>
        @endif
      </div>
      @endif
      @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 max-w-3xl mx-auto">
      @foreach (array_slice($members, 3, 2) as $member)
      @if(!empty($member))
      <div class="reveal-media text-center group">
        <div class="team-photo-wrapper">
          @if(!empty($member['image']))
          <img
            src="{{ $member['image']['url'] }}"
            alt="{{ $member['image']['alt'] ?? $member['name'] }}"
            class="team-photo">
          @endif
        </div>

        @if(!empty($member['name']))
        <p class="mt-4 text-lg font-semibold text-brand-primaryFontDark">
          {{ $member['name'] }}
        </p>
        @endif

        @if(!empty($member['role']))
        <p class="mt-0.5 text-sm text-slate-500">
          {{ $member['role'] }}
        </p>
        @endif
      </div>
      @endif
      @endforeach
    </div>

    @endif

    @if($bottomline)
    <!-- <div class="mt-12 mb-4 text-center">
      <p class="reveal-text mx-auto max-w-xl text-sm lg:text-base text-slate-600">
        {!! $bottomline !!}
      </p>
    </div> -->
    @endif


  </div>
</section>
