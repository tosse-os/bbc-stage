@if (is_user_logged_in())
@php
$user = wp_get_current_user();
$avatar_id = get_user_meta($user->ID, 'dashboard_avatar_id', true);
$avatar_url = $avatar_id
? wp_get_attachment_image_url($avatar_id, 'thumbnail')
: get_avatar_url($user->ID);
$langs = function_exists('pll_the_languages') ? pll_the_languages(['raw' => 1]) : [];
@endphp

{{-- MOBILE BOTTOM BAR --}}
<div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-brand-primary/95 border-t border-white/10">

  <div class="flex items-center justify-around min-h-[68px]">

    <a href="/dashboard" class="flex flex-col items-center justify-center text-white text-xs">
      @include('dashboard.icons.overview')
    </a>

    <a href="/dashboard-reports" class="flex flex-col items-center justify-center text-white text-xs">
      @include('dashboard.icons.report')
    </a>

    <a href="/dashboard-media" class="flex flex-col items-center justify-center text-white text-xs">
      @include('dashboard.icons.media')
    </a>

    <button type="button"
      data-mobile-menu-toggle
      class="flex flex-col items-center justify-center text-white text-xs">
      @include('dashboard.icons.menu')
    </button>

  </div>
</div>


{{-- MOBILE FLOATING PANEL --}}
<div data-mobile-menu class="md:hidden fixed inset-0 z-50 hidden">

  <div class="absolute inset-0 bg-black/45" data-mobile-menu-overlay></div>

  <div data-mobile-menu-panel
    class="absolute bottom-20 left-1/2 -translate-x-1/2
              w-[92%] max-w-sm
              rounded-2xl
              bg-white/10
              backdrop-blur-xl
              border border-white/15
              shadow-[0_25px_60px_rgba(0,0,0,0.55)]
              p-5
              space-y-4
              transform translate-y-8 opacity-0
              transition-all duration-300">

    <div class="flex items-center gap-3">
      <img src="{{ $avatar_url }}"
        class="w-12 h-12 rounded-full object-cover">
      <div>
        <div class="text-white font-medium">
          {{ $user->display_name }}
        </div>
        <a href="/dashboard-settings" class="text-sm text-slate-300">
          View profile
        </a>
      </div>
    </div>

    <div class="border-t border-white/10"></div>

    @if(!empty($langs))
    <div class="flex items-center justify-between py-2">
      <div class="flex items-center gap-3 text-white">
        @include('dashboard.icons.language')
        <span>DE/EN</span>
      </div>
      <div class="flex items-center gap-2">
        @foreach($langs as $lang)
        <a href="{{ $lang['url'] }}"
          class="px-3 py-1 rounded-lg text-sm
           {{ $lang['current_lang']
             ? 'bg-white/20 text-white'
             : 'text-slate-300 hover:bg-white/10' }}">
          {{ strtoupper($lang['slug']) }}
        </a>
        @endforeach
      </div>
    </div>
    <div class="border-t border-white/10"></div>
    @endif

    <div class="flex items-center justify-between py-2">
      <div class="flex items-center gap-3 text-white">
        @include('dashboard.icons.theme')
        <span>Dark Mode</span>
      </div>

      <button type="button"
        data-theme-toggle
        data-ajax="{{ admin_url('admin-ajax.php') }}"
        data-nonce="{{ wp_create_nonce('dashboard_theme_toggle') }}"
        class="relative w-11 h-6 bg-white/20 rounded-full">
        <span class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full"></span>
      </button>
    </div>

    <div class="border-t border-white/10"></div>

    <a href="/dashboard-settings"
      class="flex items-center gap-3 py-2 text-white hover:text-white/80 transition">
      @include('dashboard.icons.settings')
      <span>Account Settings</span>
    </a>

    <a href="/?dashboard_logout=1"
      class="flex items-center gap-3 py-2 text-red-400 hover:text-red-300 transition">
      @include('dashboard.icons.logout')
      <span>Logout</span>
    </a>

    <div class="border-t border-white/10"></div>

    <button type="button"
      data-mobile-menu-close
      class="flex items-center justify-center w-full pt-2 text-white/70 hover:text-white transition">
      @include('dashboard.icons.chevron-down')
    </button>

  </div>
</div>
@endif
