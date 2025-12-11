@extends('admin.layouts.app')
@section('content')
{{ show_window_title('View Cottages') }}
<a href="{{ url('admin/cottages') }}" class="btn btn-primary">Back</a>
@endsection