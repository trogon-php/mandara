@extends('admin.layouts.app')
@section('content')
{{ show_window_title('View DietPlans') }}
<a href="{{ url('admin/diet-plans') }}" class="btn btn-primary">Back</a>
@endsection