<header class="site-header">
  <div class="container">
    <div class="brand">
      <a href="{{ route('web.home') }}" class="brand-link">
        <img src="{{ $siteSettings['logo_url'] ?? asset('images/logo.svg') }}"
             alt="{{ $siteSettings['brand'] ?? config('app.name') }}" height="32">
      </a>
    </div>

    <nav class="main-nav">
      @foreach($primaryNav as $item)
        <a href="{{ $item['url'] }}" class="nav-link">{{ $item['label'] }}</a>
      @endforeach
    </nav>

    <div class="auth-slot">
      @if($websiteUser)
        <span>Hello, {{ $websiteUser->name }}</span>
      @else
        <a href="{{ route('login') }}" class="btn btn-sm">Login</a>
      @endif
    </div>
  </div>
</header>

{{-- Minimal inline styles (replace with your CSS framework) --}}
<style>
  .site-header { border-bottom:1px solid #eee; padding:12px 0; }
  .site-header .container { display:flex; align-items:center; justify-content:space-between; gap:24px; }
  .main-nav .nav-link { margin:0 8px; text-decoration:none; color:#111; }
  .btn.btn-sm { padding:6px 10px; border:1px solid #111; text-decoration:none; color:#111; border-radius:6px; }
</style>
