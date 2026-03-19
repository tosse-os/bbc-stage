@extends('layouts.dashboard')

@section('content')

@php
    $tab = request()->get('tab', 'account');
    $user = wp_get_current_user();
    $avatar_id = get_user_meta($user->ID, 'profile_avatar_id', true);
    // Falls ein Fehler oder Erfolg zurückgegeben wird
    $success = request()->get('success');
    $error = request()->get('error');
@endphp

<section class="max-w-5xl mx-auto">

    <header class="mb-8 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-800">Settings</h1>

        @if($success)
            <div class="text-sm bg-emerald-50 text-emerald-600 px-4 py-2 rounded-lg border border-emerald-100">
                Änderungen erfolgreich gespeichert.
            </div>
        @endif

        @if($error)
            <div class="text-sm bg-rose-50 text-rose-600 px-4 py-2 rounded-lg border border-rose-100">
                {{ $error === 'mismatch' ? 'Passwörter stimmen nicht überein.' : 'Ein Fehler ist aufgetreten.' }}
            </div>
        @endif
    </header>

    <div class="bg-white/85 backdrop-blur rounded-2xl shadow-sm px-8 py-8 border border-slate-100">

        <div class="mb-8 flex gap-8 border-b border-slate-100">
            <a href="/dashboard-settings?tab=account"
                class="pb-3 text-sm font-medium transition-all {{ $tab === 'account' ? 'text-brand-primary border-b-2 border-brand-primary' : 'text-slate-400 hover:text-slate-600' }}">
                Account
            </a>
            <a href="/dashboard-settings?tab=security"
                class="pb-3 text-sm font-medium transition-all {{ $tab === 'security' ? 'text-brand-primary border-b-2 border-brand-primary' : 'text-slate-400 hover:text-slate-600' }}">
                Security
            </a>
        </div>

        @if ($tab === 'account')
        <form
            method="post"
            action="{{ esc_url(admin_url('admin-post.php')) }}"
            enctype="multipart/form-data"
            class="grid grid-cols-1 md:grid-cols-[1fr_260px] gap-12"
            x-data="{ photoPreview: null }">

            @csrf
            <input type="hidden" name="action" value="dashboard_update_account">

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">
                        Full name
                    </label>
                    <input
                        type="text"
                        name="display_name"
                        value="{{ $user->display_name }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">
                            Email address
                        </label>
                        <input
                            type="email"
                            value="{{ $user->user_email }}"
                            disabled
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">
                            Username
                        </label>
                        <input
                            type="text"
                            value="{{ $user->user_login }}"
                            disabled
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">
                        Phone Number
                    </label>
                    <input
                        type="text"
                        name="phone"
                        placeholder="+49..."
                        value="{{ get_user_meta($user->ID, 'phone_number', true) }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                </div>

                <div class="pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center px-8 py-3 rounded-xl bg-brand-primary text-white text-sm font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-primary/90 transition-all transform hover:-translate-y-0.5">
                        Save Changes
                    </button>
                </div>
            </div>

            <div class="flex flex-col items-center md:items-start space-y-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-400">
                    Profile Picture
                </label>

                <div class="relative group">
                    <div class="h-44 w-44 rounded-2xl overflow-hidden bg-slate-100 flex items-center justify-center border-2 border-slate-200 shadow-inner">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-full h-full object-cover">
                        </template>

                        <template x-if="!photoPreview">
                            @if ($avatar_id && wp_attachment_is_image($avatar_id))
                                {!! wp_get_attachment_image($avatar_id, 'medium', false, ['class' => 'w-full h-full object-cover']) !!}
                            @else
                                <div class="text-center p-4">
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-[10px] text-slate-400 mt-2 block italic">No image</span>
                                </div>
                            @endif
                        </template>
                    </div>
                </div>

                <label class="w-full">
                    <span class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold cursor-pointer hover:bg-slate-200 transition-all border border-slate-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Choose Photo
                    </span>
                    <input
                        type="file"
                        name="profile_avatar"
                        class="hidden"
                        accept="image/jpeg,image/png,image/webp"
                        @change="const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL($event.target.files[0]);">
                </label>
                <p class="text-[10px] text-slate-400 text-center md:text-left leading-relaxed">
                    Allowed: JPG, PNG or WebP.<br>Max size: 2MB.
                </p>
            </div>

        </form>
        @endif

        @if ($tab === 'security')
        <form
            method="post"
            action="{{ esc_url(admin_url('admin-post.php')) }}"
            class="max-w-md space-y-6">

            @csrf
            <input type="hidden" name="action" value="dashboard_update_password">

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">
                        Current Password
                    </label>
                    <input type="password" name="current_password" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                </div>

                <hr class="border-slate-100">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">
                        New Password
                    </label>
                    <input type="password" name="new_password" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">
                        Confirm New Password
                    </label>
                    <input type="password" name="new_password_confirm" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                </div>
            </div>

            <div class="pt-4">
                <button
                    type="submit"
                    class="px-8 py-3 rounded-xl bg-brand-primary text-white text-sm font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-primary/90 transition-all transform hover:-translate-y-0.5">
                    Update Password
                </button>
            </div>

        </form>
        @endif

    </div>

</section>

@endsection
