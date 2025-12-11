@extends('admin.layouts.app')
@section('content')
{{ show_window_title('View Blogs') }}
<a href="{{ url('admin/blogs') }}" class="btn btn-primary">Back</a>
@endsection