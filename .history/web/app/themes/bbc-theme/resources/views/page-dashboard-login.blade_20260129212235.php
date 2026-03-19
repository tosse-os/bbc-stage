{{-- Dashboard Login --}}
@extends('layouts.dashboard-auth')

@section('content')
<section class="max-w-md w-full bg-dashboard-card p-8 rounded-xl">
  <h1 class="text-2xl font-semibold mb-6">Login</h1>

  <form method="post" action="{{ esc_url(admin_url('admin-post.php')) }}" class="space-y-4">
    @csrf
    <input type="hidden" name="action" value="dashboard_login">

    <input
      type="email"
      name="email"
      required
      placeholder="E-Mail"
      class="w-full rounded-lg px-4 py-2 border">

    <input
      type="password"
      name="password"
      required
      placeholder="Passwort"
      class="w-full rounded-lg px-4 py-2 border">

    <button
      type="submit"
      class="w-full py-2 rounded-lg bg-primary text-white">
      Einloggen
    </button>
  </form>

  @if(request()->get('error'))
  <p class="mt-4 text-sm text-red-500">
    Login fehlgeschlagen
  </p>
  @endif

  <p class="text-sm text-center mt-4 text-dashboard-muted">
    Noch kein Zugang?
    <a href="/dashboard-register" class="underline">Registrieren</a>
  </p>
</section>
@endsection
