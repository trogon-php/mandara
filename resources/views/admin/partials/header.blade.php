<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="dark" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">
<head>
    <meta charset="utf-8" />
    <title>{{ $page_title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="Trogon" name="author" />
    @include('admin.partials.header_includes')

</head>

<body>

<!-- Begin page -->
<div id="layout-wrapper">

    @include('admin.partials.header_topbar')


    <!-- ========== App Menu ========== -->
    @include('admin.partials.navigation')
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
