@foreach(($seo->schema ?? []) as $block)
<script type="application/ld+json">{!! json_encode($block, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach
