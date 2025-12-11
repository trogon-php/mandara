@php
  $titleMain = $seo->title ?? config('app.name');
  $fullTitle = trim(($seo->title ?? '') . ($seo->site_suffix ?? '')) ?: config('app.name');
  $canonical = $seo->canonical ?? url()->current();

  // Optional prev/next rel for pagination (pass $paginator from controller/view if needed)
  $prevUrl = isset($paginator) && method_exists($paginator, 'previousPageUrl') ? $paginator->previousPageUrl() : null;
  $nextUrl = isset($paginator) && method_exists($paginator, 'nextPageUrl') ? $paginator->nextPageUrl() : null;
@endphp

<title>{{ $fullTitle }}</title>

@if(!empty($seo->description)) <meta name="description" content="{{ $seo->description }}"> @endif
@if(!empty($seo->keywords))    <meta name="keywords" content="{{ $seo->keywords }}"> @endif
@if(!empty($canonical))        <link rel="canonical" href="{{ $canonical }}"> @endif
@if(!empty($seo->robots))      <meta name="robots" content="{{ $seo->robots }}"> @endif

{{-- Open Graph --}}
<meta property="og:type" content="{{ $seo->og_type ?? 'website' }}">
<meta property="og:title" content="{{ $fullTitle }}">
@if(!empty($seo->description)) <meta property="og:description" content="{{ $seo->description }}"> @endif
<meta property="og:url" content="{{ $canonical }}">
@if(!empty($seo->og_image))    <meta property="og:image" content="{{ $seo->og_image }}"> @endif

{{-- Twitter --}}
<meta name="twitter:card" content="{{ $seo->twitter_card ?? 'summary_large_image' }}">
<meta name="twitter:title" content="{{ $fullTitle }}">
@if(!empty($seo->description)) <meta name="twitter:description" content="{{ $seo->description }}"> @endif
@if(!empty($seo->og_image))    <meta name="twitter:image" content="{{ $seo->og_image }}"> @endif

{{-- hreflang alternates --}}
@foreach(($seo->hreflangs ?? []) as $lang => $href)
  <link rel="alternate" hreflang="{{ $lang }}" href="{{ $href }}">
@endforeach

{{-- extra custom metas --}}
@foreach(($seo->meta_extra ?? []) as $m)
  @if(!empty($m['name']) && !empty($m['content']))
    <meta name="{{ $m['name'] }}" content="{{ $m['content'] }}">
  @endif
@endforeach

{{-- pagination rel (optional) --}}
@if(!empty($prevUrl)) <link rel="prev" href="{{ $prevUrl }}"> @endif
@if(!empty($nextUrl)) <link rel="next" href="{{ $nextUrl }}"> @endif
