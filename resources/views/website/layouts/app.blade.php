<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- SEO meta & JSON-LD --}}
  @include('website.partials.meta')
  @include('website.partials.jsonld')

  {{-- Favicon (update paths) --}}
  <link rel="icon" type="image/png" href="{{ url('favicon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

  {{-- Extra head hooks (per-page) --}}
  @stack('head')
</head>
<body>
  {{-- Header --}}
  @include('website.partials.header')

  <main id="app" class="site-main">
    @yield('content')
  </main>

  {{-- Footer --}}
  @include('website.partials.footer')

  {{-- Scripts --}}
  @stack('scripts')
</body>
</html>
