@extends('layouts.app')

@section('content')
<section class="relative bg-slate-950/30 overflow-hidden">
  @include('partials.subscription.trial-hero')
  @include('partials.subscription.trial-content')
</section>
@endsection
