@php
$tab = request()->get('tab', 'account');
$user = wp_get_current_user();
$avatar_id = get_user_meta($user->ID, 'profile_avatar_id', true);
$avatar_url = $avatar_id ? wp_get_attachment_image_url($avatar_id, 'medium') : null;
@endphp

{{-- ... Header & Tabs wie gehabt ... --}}

@if ($tab === 'account')
<form
  method="post"
  action="{{ esc_url(admin_url('admin-post.php')) }}"
  enctype="multipart/form-data" {{-- WICHTIG --}}
  class="grid grid-cols-1 md:grid-cols-[1fr_260px] gap-10"
  x-data="{ photoPreview: null }"> {{-- Optional: Alpine.js für Preview --}}

  @csrf
  <input type="hidden" name="action" value="dashboard_update_account">

  <div class="space-y-6">
    {{-- Deine Inputs (Name, Email, Phone) --}}
    {{-- ... --}}
    <button type="submit" class="...">Update Profile</button>
  </div>

  <div class="space-y-4">
    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">
      Profile Picture
    </label>

    {{-- Preview Area --}}
    <div class="h-40 w-40 rounded-xl overflow-hidden bg-slate-100 flex items-center justify-center border-2 border-dashed border-slate-200">
      <template x-if="photoPreview">
        <img :src="photoPreview" class="w-full h-full object-cover">
      </template>

      <template x-if="!photoPreview">
        @if ($avatar_id && wp_attachment_is_image($avatar_id))
        {!! wp_get_attachment_image($avatar_id, 'medium', false, ['class' => 'w-full h-full object-cover']) !!}
        @else
        <span class="text-sm text-slate-400">No image uploaded</span>
        @endif
      </template>
    </div>

    <label class="inline-block px-4 py-2 rounded-lg bg-slate-200 text-sm font-medium cursor-pointer hover:bg-slate-300 transition-colors">
      <span>Select image</span>
      <input
        type="file"
        name="profile_avatar"
        class="hidden"
        accept="image/jpeg,image/png,image/webp"
        @change="const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL($event.target.files[0]);">
    </label>
    <p class="text-[10px] text-slate-400">JPG, PNG oder WebP. Max. 2MB empfohlen.</p>
  </div>
</form>
@endif
