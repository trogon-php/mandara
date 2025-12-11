<footer class="site-footer">
  <div class="container">
    <div class="footer-left">
      {!! $siteSettings['footer_html'] ?? ('© '.date('Y').' '.config('app.name')) !!}
    </div>
    <div class="footer-right">
      <a href="{{ route('web.courses') }}">Courses</a>
      <a href="{{ route('web.about') }}">About</a>
      <a href="{{ route('web.contact') }}">Contact</a>
      <a href="{{ route('web.sitemap') ?? '#' }}">Sitemap</a>
    </div>
  </div>
</footer>

<style>
  .site-footer { border-top:1px solid #eee; padding:16px 0; margin-top:48px; color:#555; }
  .site-footer .container { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
  .site-footer a { margin-left:12px; color:#555; text-decoration:none; }
</style>
