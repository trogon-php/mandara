<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class makeFullcrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-fullcrud {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Model, Service, Controller, Requests, and admin views for a given name';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name')); // LiveClassAccount
        $plural = Str::pluralStudly($name);           // LiveClassAccounts
        $snakePlural = Str::snake($plural);    // for view folder: live_class_accounts
        $kebabPlural = Str::kebab($plural);    // for routes: live-class-accounts

        // 1. Create Model
        // $this->createFile(app_path("Models/{$name}.php"), $this->getModelStub($name));
        // $this->info("Model created: Models/{$name}.php");

        // 2. Create Service
        $this->createFile(app_path("Services/{$plural}/{$name}Service.php"), $this->getServiceStub($name));
        $this->info("Service created: Services/{$plural}/{$name}Service.php");

        // // 3. Create Controller
        // $this->createFile(app_path("Http/Controllers/Admin/{$name}Controller.php"), $this->getControllerStub($name, $plural, $snakePlural));
        // $this->info("Controller created: Http/Controllers/Admin/{$name}Controller.php");

        // // 4. Create Requests
        // $this->createFile(app_path("Http/Requests/{$plural}/Store{$name}Request.php"), $this->getRequestStub($name, 'Store', $plural));
        // $this->createFile(app_path("Http/Requests/{$plural}/Update{$name}Request.php"), $this->getRequestStub($name, 'Update', $plural));
        // $this->info("Requests created: Store{$name}Request, Update{$name}Request");

        // 5. Create Views
        // $viewsPath = resource_path("views/admin/{$snakePlural}");
        // $this->createFile("{$viewsPath}/create.blade.php", $this->getCreateViewStub($snakePlural, $plural, $kebabPlural));
        // $this->createFile("{$viewsPath}/edit.blade.php", $this->getEditViewStub($snakePlural, $plural, $kebabPlural));
        // $this->createFile("{$viewsPath}/index.blade.php", $this->getIndexViewStub($snakePlural, $plural, $kebabPlural));
        // $this->createFile("{$viewsPath}/index-table.blade.php", $this->getIndexTableViewStub($snakePlural, $kebabPlural));
        // $this->createFile("{$viewsPath}/show.blade.php", $this->getShowViewStub($snakePlural, $plural, $kebabPlural));
        // $this->createFile("{$viewsPath}/sort.blade.php", $this->getSortViewStub($snakePlural, $plural, $kebabPlural));

        // $this->info("Views created in: resources/views/admin/{$snakePlural}");
    }

    protected function createFile($path, $content)
    {
        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }
        File::put($path, $content);
    }

    // --- Stubs ---
    protected function getModelStub($name)
    {
        return <<<PHP
<?php

namespace App\Models;

use App\Models\BaseModel;

class {$name} extends BaseModel
{
    // Model properties and relationships
}
PHP;
    }

    protected function getServiceStub($name)
{
    $plural = Str::pluralStudly($name);

    return <<<PHP
<?php

namespace App\Services\\{$plural};

use App\Models\\{$name};
use App\Services\Core\BaseService;

class {$name}Service extends BaseService
{
    protected string \$modelClass = {$name}::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration - used for CRUD filters
     */
    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    '1' => 'Active',
                    '0' => 'Inactive',
                ],
            ],
        ];
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'provider' => 'Provider',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'provider'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    public function store(array \$data): {$name}
    {
        \$maxSortOrder = \$this->model->max('sort_order') ?? 0;
        \$data['sort_order'] = \$maxSortOrder + 1;

        return parent::store(\$data);
    }
}
PHP;
}


    protected function getControllerStub($name, $plural, $snakePlural)
    {
        return <<<PHP
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\\{$plural}\\Store{$name}Request as StoreRequest;
use App\Http\Requests\\{$plural}\\Update{$name}Request as UpdateRequest;
use App\Services\\{$plural}\\{$name}Service;
use Illuminate\Http\Request;

class {$name}Controller extends AdminBaseController
{
    public function __construct(private {$name}Service \$service) {}

    public function index(Request \$request)
    {
        \$filters = array_filter(\$request->only(['status','date_from','date_to']));
        \$searchParams = ['search' => \$request->get('search')];

        \$list_items = \$this->service->getFilteredData(['search' => \$searchParams['search'], 'filters' => \$filters]);

        return view('admin.{$snakePlural}.index', [
            'page_title' => '{$name} List',
            'list_items' => \$list_items,
            'filters' => \$filters,
            'search_params' => \$searchParams,
            'filterConfig' => \$this->service->getFilterConfig(),
            'searchConfig' => \$this->service->getSearchConfig(),
        ]);
    }

    public function create() { return view('admin.{$snakePlural}.create'); }

    public function store(StoreRequest \$request)
    {
        \$this->service->store(\$request->validated());
        return \$this->successResponse('Item created successfully');
    }

    public function edit(string \$id)
    {
        \$edit_data = \$this->service->find(\$id);
        return view('admin.{$snakePlural}.edit', compact('edit_data'));
    }

    public function update(UpdateRequest \$request, string \$id)
    {
        \$this->service->update(\$id, \$request->validated());
        return \$this->successResponse('Item updated successfully');
    }

    public function destroy(string \$id)
    {
        if (!\$this->service->delete(\$id)) {
            return \$this->errorResponse('Failed to delete item');
        }
        return \$this->successResponse('Item deleted successfully');
    }

    public function bulkDelete(Request \$request)
    {
        if (!\$this->service->bulkDelete(\$request->ids)) {
            return \$this->errorResponse('Failed to delete items');
        }
        return \$this->successResponse('Selected items deleted successfully');
    }

    public function sortUpdate(Request \$request)
    {
        \$result = \$this->service->sortUpdate(\$request->order);
        if (!\$result) return \$this->errorResponse('Failed to update sort order');
        return \$this->successResponse('Sort order updated successfully');
    }

    public function sortView()
    {
        \$list_items = \$this->service->getAll();
        return view('admin.{$snakePlural}.sort', ['list_items'=>\$list_items]);
    }

    public function cloneItem(\$id)
    {
        \$item = \$this->service->find(\$id);
        \$cloned = \$this->service->clone(\$item);
        if (!\$cloned) return \$this->errorResponse('Failed to clone item.');
        return \$this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.{$snakePlural}.edit', \$cloned->id)
        ]);
    }
}
PHP;
    }

    protected function getRequestStub($name, $type, $plural)
    {
        return <<<PHP
<?php

namespace App\Http\Requests\\{$plural};

use App\Http\Requests\BaseRequest;

class {$type}{$name}Request extends BaseRequest
{
    public function rules(): array
    {
        return [
            // validation rules
        ];
    }
}
PHP;
    }

    protected function getCreateViewStub($snakePlural, $plural, $kebabPlural)
    {
        return <<<PHP
@include('admin.crud.form', [
    'action' => route('admin.{$kebabPlural}.store'),
    'formId' => 'add-{$snakePlural}-form',
    'submitText' => 'Save {$plural}',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.{$kebabPlural}.index'),
    'fields' => [
        ['type'=>'text','name'=>'name','label'=>'Name','col'=>6],
        ['type'=>'select','name'=>'status','label'=>'Status','options'=>[1=>'Published',0=>'Draft'],'col'=>6]
    ]
])
PHP;
    }

    protected function getEditViewStub($snakePlural, $plural, $kebabPlural)
    {
        return <<<PHP
@include('admin.crud.form', [
    'action' => route('admin.{$kebabPlural}.update', \$edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-{$kebabPlural}-form',
    'submitText' => 'Update {$plural}',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.{$kebabPlural}.index'),
    'fields' => [
        ['type'=>'text','name'=>'name','label'=>'Name','value'=>old('name',\$edit_data->name),'col'=>6],
        ['type'=>'select','name'=>'status','label'=>'Status','options'=>[1=>'Published',0=>'Draft'],'value'=>old('status',\$edit_data->status),'col'=>6]
    ]
])
PHP;
    }

    protected function getIndexViewStub($snakePlural, $plural, $kebabPlural)
    {
        return <<<PHP
@include('admin.crud.crud-index-layout', [
    'page_title' => '{$plural}',
    'createUrl' => url('admin/{$kebabPlural}/create'),
    'sortUrl' => url('admin/{$kebabPlural}/sort'),
    'bulkDeleteUrl' => url('admin/{$kebabPlural}/bulk-delete'),
    'redirectUrl' => url('admin/{$kebabPlural}'),
    'tableId' => '{$kebabPlural}-table',
    'list_items' => \$list_items,
    'breadcrumbs' => ['Dashboard'=>url('admin/dashboard'), '{$plural}'=>null],
    'filters' => view('admin.partials.universal-filters', ['filterConfig'=>\$filterConfig,'searchConfig'=>\$searchConfig]),
    'tableHead' => '<tr>
                        <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
                        <th>#</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th style="width: 120px;">Provider</th>
                        <th>Action</th>
                    </tr>',
    'tableBody' => view('admin.{$snakePlural}.index-table', compact('list_items'))
])
PHP;
    }

    protected function getIndexTableViewStub($snakePlural, $kebabPlural)
    {
        return <<<PHP
@if(\$list_items)
    @foreach(\$list_items as \$list_item)
        <tr>
            <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ \$list_item->id }}"></td>
            <td>{{ \$loop->iteration }}</td>
            <td>{{ \$list_item->name }}</td>
            <td>{{ \$list_item->status ? 'Active' : 'Inactive' }}</td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'cloneUrl'    => url('admin/{$kebabPlural}/'.\$list_item->id.'/clone'),
                    'cloneTitle'  => 'Clone Live Class',
                    'editUrl'=>url('admin/{$kebabPlural}/'.\$list_item->id.'/edit'),
                    'deleteUrl'=>route('admin.{$kebabPlural}.destroy', \$list_item->id),
                    'redirectUrl'=>route('admin.{$kebabPlural}.index')
                ])
            </td>
        </tr>
    @endforeach
@endif
PHP;
    }

    protected function getShowViewStub($snakePlural, $plural, $kebabPlural)
    {
        return <<<PHP
@extends('admin.layouts.app')
@section('content')
{{ show_window_title('View {$plural}') }}
<a href="{{ url('admin/{$kebabPlural}') }}" class="btn btn-primary">Back</a>
@endsection
PHP;
    }

    protected function getSortViewStub($snakePlural, $plural, $kebabPlural)
    {
        return <<<PHP
@include('admin.crud.sort', [
    'formId' => 'sort-{$kebabPlural}-form',
    'saveUrl' => route('admin.{$kebabPlural}.sort.update'),
    'redirectUrl' => route('admin.{$kebabPlural}.index'),
    'items' => \$list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'status']
])
PHP;
    }
}
