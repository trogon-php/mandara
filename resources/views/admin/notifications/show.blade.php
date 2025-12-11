@extends('admin.layouts.app')
@section('content')

{{ show_window_title($page_title) }}

<h1>Review View</h1>
<a href="{{ url('admin/notifications') }}" class="trogon-link btn btn-primary">Back</a>

@endsection