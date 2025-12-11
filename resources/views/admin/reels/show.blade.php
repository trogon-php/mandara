@extends('admin.layouts.app')
@section('content')

{{ show_window_title($page_title) }}

<h1>Reel View</h1>
<a href="{{ url('admin/reels') }}" class="trogon-link btn btn-primary">Back</a>

@endsection

