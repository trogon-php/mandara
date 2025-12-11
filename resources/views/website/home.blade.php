@extends('website.layouts.app')

@section('content')
<section class="hero">
  <div class="container">
    <h1>Welcome to {{ $siteSettings['brand'] ?? config('app.name') }}</h1>
    <p>Learn with high-quality courses and live sessions.</p>
    <p><a href="{{ route('web.courses') }}">Browse Courses →</a></p>
  </div>
</section>

<style>
  .hero { padding:48px 0; }
  .container { max-width:1100px; margin:0 auto; padding:0 16px; }
</style>
@endsection
