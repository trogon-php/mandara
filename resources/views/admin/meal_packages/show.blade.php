@extends('admin.layouts.app')
@section('content')
{{ show_window_title('View MealPackages') }}
<a href="{{ url('admin/meal-packages') }}" class="btn btn-primary">Back</a>
@endsection