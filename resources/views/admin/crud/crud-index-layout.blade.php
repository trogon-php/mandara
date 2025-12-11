@extends('admin.layouts.app')
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $page_title }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @foreach($breadcrumbs ?? [] as $label => $url)
                        @if($loop->last || empty($url))
                            <li class="breadcrumb-item active">{{ $label }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $url }}" class="trogon-link">{{ $label }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </div>
            
        </div>
    </div>
</div>

<!-- Card -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <a class="btn btn-md btn-outline-dark rounded-pill float-start trogon-link me-2 mt-2"
                           href="{{ $backUrl ?? url('admin/dashboard') }}">
                            <i class="mdi mdi-arrow-left"></i>
                            {{ $backText ?? 'Back to Dashboard' }}
                        </a>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        @isset($createPage)
                            <a class="btn btn-md btn-primary rounded-pill float-end mt-2"
                                href="{{ $createPage }}">
                                <i class="mdi mdi-plus"></i>
                                Add {{ $page_title }}
                            </a>
                        @endisset
                        @isset($createUrl)
                            <button class="btn btn-md btn-primary rounded-pill float-end mt-2"
                                    onclick="showAjaxModal('{{ $createUrl }}', 'Add New {{ $page_title }}')">
                                <i class="mdi mdi-plus"></i>
                                Add {{ $page_title }}
                            </button>
                        @endisset

                        @isset($sortUrl)
                            <button class="btn btn-md btn-outline-warning rounded-pill float-end me-2 mt-2"
                                    onclick="showAjaxModal('{{ $sortUrl }}', 'Sort {{ $page_title }}')">
                                <i class="mdi mdi-sort"></i>
                                Sort {{ $page_title }}
                            </button>
                        @endisset

                        {{-- Custom buttons section --}}
                        @isset($customButtons)
                            @foreach($customButtons as $button)
                                @if($button['type'] === 'link')
                                    <a href="{{ $button['url'] }}" 
                                       class="btn btn-md {{ $button['class'] ?? 'btn-outline-secondary' }} rounded-pill float-end me-2 mt-2">
                                        <i class="{{ $button['icon'] ?? '' }}"></i>
                                        {{ $button['text'] }}
                                    </a>
                                @elseif($button['type'] === 'modal')
                                    <button class="btn btn-md {{ $button['class'] ?? 'btn-outline-secondary' }} rounded-pill float-end me-2 mt-2"
                                            onclick="showAjaxModal('{{ $button['url'] }}', '{{ $button['title'] }}')">
                                        <i class="{{ $button['icon'] ?? '' }}"></i>
                                        {{ $button['text'] }}
                                    </button>
                                @elseif($button['type'] === 'button')
                                    <button class="btn btn-md {{ $button['class'] ?? 'btn-outline-secondary' }} rounded-pill float-end me-2 mt-2"
                                            onclick="{{ $button['onclick'] ?? '' }}">
                                        <i class="{{ $button['icon'] ?? '' }}"></i>
                                        {{ $button['text'] }}
                                    </button>
                                @endif
                            @endforeach
                        @endisset
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- stat widgets --}}
                @isset($statWidgets)
                    <div class="row mb-4">
                        @foreach ($statWidgets as $statWidget)
                            
                            @include('admin.crud.cards.stat', [
                                'label' => $statWidget['label'] ?? null,
                                'value' =>  $statWidget['value'] ?? null,
                                'description' =>  $statWidget['description'] ?? null,
                                'col'   =>  $statWidget['col'] ?? null,
                                'iconClass' => $statWidget['iconClass'] ?? null,
                                'iconColor' => $statWidget['iconColor'] ?? null
                            ])

                        @endforeach
                    </div>
                @endisset
                {{-- Include filters if provided --}}
                @isset($filters)
                    @if(is_string($filters))
                        {!! $filters !!}
                    @else
                        {!! $filters->render() !!}
                    @endif
                @endisset

                @isset($list_items)
                    <div class="mb-3">
                        <strong>Total Items: </strong> 
                        @if(method_exists($list_items, 'total'))
                            {{ $list_items->total() }} (Showing {{ $list_items->count() }} of {{ $list_items->total() }})
                        @else
                            {{ $list_items->count() }}
                        @endif
                    </div>
                @endisset

                <table id="{{ $tableId ?? 'crud-table' }}"
                class="{{ $tableClass ?? 'data_table_basic' }} table table-bordered table-striped align-middle"
                data-ajax-url="{{ $tableDataAjaxUrl ?? '' }}"
                data-export-btn={{ $tableExportBtn ?? 'false' }} style="width:100%">
                    <thead>
                        @if(is_string($tableHead))
                            {!! $tableHead !!}
                        @else
                            {!! $tableHead->render() !!}
                        @endif
                    </thead>
                    <tbody>
                        @if(is_string($tableBody))
                            {!! $tableBody !!}
                        @else
                            {!! $tableBody->render() !!}
                        @endif
                    </tbody>
                </table>

                @include('admin.crud.bulk-actions', [
                    'bulkDeleteUrl' => $bulkDeleteUrl ?? null,
                    'redirectUrl'   => $redirectUrl ?? null
                ])

                {{-- Pagination --}}
                @isset($pagination)
                    <div class="row mt-2" style="margin-top: 30px!important;">
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-center">
                                <div class="pagination-wrap">
                                    @if(is_string($pagination))
                                        {!! $pagination !!}
                                    @else
                                        {!! $pagination->render() !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>

@endsection