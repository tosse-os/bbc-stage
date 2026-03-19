{{-- Dashboard Login Seite --}}
@extends('layouts.dashboard-auth')

@section('content')
<section class="max-w-md w-full bg-dashboard-card p-8 rounded-xl">
  <h1 class="text-2xl font-semibold mb-6">Login</h1>

  {{-- WordPress Core Login, Redirect nach erfolgreichem Login ins Dashboard --}}
  <form method="post" action="{{ wp_login_url('/dashboard') }}" class="space-y-4">
    @csrf

    <input type="email" name="log" required placeholder="E-Mail"
      class="w-full rounded-lg px-4 py-2 border">

    <input type="password" name="pwd" required placeholder="Passwort"
      class="w-full rounded-lg px-4 py-2 border">

    <button type="submit"
      class="w-full py-2 rounded-lg bg-primary text-white">
      Einloggen
    </button>
  </form>

  <p class="text-sm text-center mt-4 text-dashboard-muted">
    Noch kein Zugang?
    <a href="/dashboard-register" class="underline">Registrieren</a>
  </p>
</section>
@endsection
