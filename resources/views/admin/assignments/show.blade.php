@extends('admin.layouts.app')
@section('content')
{{ show_window_title('View Assignments') }}
<a href="{{ url('admin/assignments') }}" class="btn btn-primary">Back</a>
@endsection