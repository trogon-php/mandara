@extends('admin.layouts.app')
@section('content')

{{ show_window_title($page_title) }}

<h1>Reel Category View</h1>
<a href="{{ url('admin/reel-categories') }}" class="trogon-link btn btn-primary">Back</a>

@endsection

