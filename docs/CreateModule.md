# Laravel Admin Panel CRUD Development Guidelines

This comprehensive guide provides step-by-step instructions for creating new CRUD modules in the admin panel following the established architecture patterns.

## 📋 **Prerequisites**

- Understanding of Laravel MVC architecture
- Familiarity with the existing codebase structure
- Access to the admin panel
- Knowledge of existing CRUD patterns in the codebase

## 🏗️ **Architecture Overview**

The admin panel follows a simplified layered architecture:

```
Controller → Service → Model
     ↓         ↓        ↓
   Views    Business  Database
           Logic
```

## 📁 **Folder Structure & Naming Conventions**

### **Controllers**
- **Location**: `app/Http/Controllers/Admin/`
- **Naming**: `{ModuleName}Controller.php` (PascalCase)
- **Example**: `StudentController.php`, `CategoryController.php`
- **Base Class**: Must extend `AdminBaseController`

### **Services**
- **Location**: `app/Services/{ModuleGroup}/`
- **Naming**: `{ModuleName}Service.php` (PascalCase)
- **Example**: `app/Services/Users/StudentService.php`
- **Base Class**: Must extend `BaseService`

### **Models**
- **Location**: `app/Models/`
- **Naming**: `{ModuleName}.php` (PascalCase)
- **Base Class**: Must extend `BaseModel` or `BaseAuthModel`

### **Requests**
- **Location**: `app/Http/Requests/{ModuleName}s/`
- **Naming**: `Store{ModuleName}Request.php`, `Update{ModuleName}Request.php`
- **Example**: `app/Http/Requests/Students/StoreStudentRequest.php`
- **Base Class**: Must extend `BaseRequest`

### **Views**
- **Location**: `resources/views/admin/{module-name}/`
- **Naming**: `index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`
- **Example**: `resources/views/admin/students/index.blade.php`

## 🚀 **Step-by-Step Module Creation**

### **Step 1: Create Model**

**File:** `app/Models/{ModuleName}.php`

```php
<?php

namespace App\Models;

use App\Models\BaseModel;

class {ModuleName} extends BaseModel
{
    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'status' => 'string',
        'sort_order' => 'integer',
    ];

    protected $fileFields = [
        'image' => [
            'single' => true,
            'folder' => '{module-name}s',
            'preset' => 'default',
        ]
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo({ModuleName}::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany({ModuleName}::class, 'parent_id');
    }
}
```

### **Step 2: Create Service**

**File:** `app/Services/{ModuleGroup}/{ModuleName}Service.php`

```php
<?php

namespace App\Services\{ModuleGroup};

use App\Models\{ModuleName};
use App\Services\Core\BaseService;

class {ModuleName}Service extends BaseService
{
    protected string $modelClass = {ModuleName}::class;

    public function __construct()
    {
        parent::__construct();
    }

    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ],
            ]
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['name', 'email'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    // Override methods if needed for specific business logic
    public function store(array $data): Model
    {
        // Add any specific logic before storing
        return parent::store($data);
    }

    public function update(int $id, array $data): ?Model
    {
        // Add any specific logic before updating
        return parent::update($id, $data);
    }
}
```

### **Step 3: Create Request Validation Classes**

**File:** `app/Http/Requests/{ModuleName}s/Store{ModuleName}Request.php`

```php
<?php

namespace App\Http\Requests\{ModuleName}s;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class Store{ModuleName}Request extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:{table_name},email',
            'phone' => 'required|string|unique:{table_name},phone',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email already exists.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'Phone number already exists.',
            'status.required' => 'Status is required.',
            'image.image' => 'File must be an image.',
        ];
    }
}
```

**File:** `app/Http/Requests/{ModuleName}s/Update{ModuleName}Request.php`

```php
<?php

namespace App\Http\Requests\{ModuleName}s;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class Update{ModuleName}Request extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('{module-name}');
        
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('{table_name}', 'email')->ignore($id)
            ],
            'phone' => [
                'required',
                'string',
                Rule::unique('{table_name}', 'phone')->ignore($id)
            ],
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email already exists.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'Phone number already exists.',
            'status.required' => 'Status is required.',
            'image.image' => 'File must be an image.',
        ];
    }
}
```

### **Step 4: Create Controller**

**File:** `app/Http/Controllers/Admin/{ModuleName}Controller.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\{ModuleGroup}\{ModuleName}Service;
use App\Http\Requests\{ModuleName}s\Store{ModuleName}Request as StoreRequest;
use App\Http\Requests\{ModuleName}s\Update{ModuleName}Request as UpdateRequest;

class {ModuleName}Controller extends AdminBaseController
{
    protected {ModuleName}Service $service;

    public function __construct({ModuleName}Service $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'parent_id']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        $list_items = $this->service->getFilteredData($params);

        return view('admin.{module-name}.index', [
            'page_title' => '{ModuleName}s',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        return view('admin.{module-name}.create', [
            'page_title' => 'Add {ModuleName}',
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('{ModuleName} added successfully');
    }

    public function edit($id)
    {
        ${module-name} = $this->service->find($id);
        return view('admin.{module-name}.edit', [
            'page_title' => 'Edit {ModuleName}',
            'edit_data' => ${module-name},
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('{ModuleName} updated successfully');
    }

    public function show($id)
    {
        return view('admin.{module-name}.show', [
            'page_title' => '{ModuleName} Details',
            '{module-name}' => $this->service->find($id),
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse('{ModuleName} deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $this->service->bulkDelete($ids);
        return $this->successResponse('Selected items deleted successfully');
    }
}
```

### **Step 5: Create Views**

**File:** `resources/views/admin/{module-name}/index.blade.php`

```php
@include('admin.crud.crud-index-layout', [
    'page_title'    => '{ModuleName}s',
    'createUrl'     => url('admin/{module-name}s/create'),
    'bulkDeleteUrl' => url('admin/{module-name}s/bulk-delete'),
    'redirectUrl'   => url('admin/{module-name}s'),
    'tableId'       => '{module-name}s-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        '{ModuleName}s' => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.{module-name}s.index-table', compact('list_items'))
])
```

**File:** `resources/views/admin/{module-name}/create.blade.php`

```php
@include('admin.crud.form', [
    'action' => route('admin.{module-name}s.store'),
    'formId' => 'add-{module-name}-form',
    'submitText' => 'Save {ModuleName}',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.{module-name}s.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'name',
            'id' => 'name',
            'label' => 'Name',
            'placeholder' => 'Enter name',
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'email',
            'name' => 'email',
            'id' => 'email',
            'label' => 'Email',
            'placeholder' => 'Enter email',
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [
                'active' => 'Active',
                'inactive' => 'Inactive'
            ],
            'col' => 6
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'id' => 'image',
            'label' => 'Image',
            'accept' => 'image/*',
            'presetKey' => 'default',
            'col' => 6
        ]
    ]
])
```

**File:** `resources/views/admin/{module-name}/edit.blade.php`

```php
@include('admin.crud.form', [
    'action' => route('admin.{module-name}s.update', $edit_data->id),
    'formId' => 'edit-{module-name}-form',
    'submitText' => 'Update {ModuleName}',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.{module-name}s.index'),
    'method' => 'PUT',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'name',
            'id' => 'name',
            'label' => 'Name',
            'placeholder' => 'Enter name',
            'required' => true,
            'value' => $edit_data->name,
            'col' => 6
        ],
        [
            'type' => 'email',
            'name' => 'email',
            'id' => 'email',
            'label' => 'Email',
            'placeholder' => 'Enter email',
            'required' => true,
            'value' => $edit_data->email,
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [
                'active' => 'Active',
                'inactive' => 'Inactive'
            ],
            'value' => $edit_data->status,
            'col' => 6
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'id' => 'image',
            'label' => 'Image',
            'accept' => 'image/*',
            'presetKey' => 'default',
            'value' => $edit_data->image,
            'col' => 6
        ]
    ]
])
```

### **Step 6: Add Routes**

**File:** `routes/admin.php`

```php
// Add these routes in the admin middleware group
Route::prefix('{module-name}s')->name('{module-name}s.')->controller({ModuleName}Controller::class)->group(function () {
    Route::get('sort', 'sortView')->name('sort.view');
    Route::post('sort', 'sortUpdate')->name('sort.update');
    Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
    Route::post('/{{module-name}}/clone', 'cloneItem')->name('clone');
});

Route::resource('{module-name}s', {ModuleName}Controller::class);
```

### **Step 7: Add Menu Configuration**

**File:** `config/admin_menu.php`

```php
[
    'title'    => '{ModuleName}s',
    'icon'     => 'ri-{icon}-line',
    'route'    => 'admin/{module-name}s',
    'can'      => '{module-name}s/index',
],
```

### **Step 8: Create Database Migration**

**Command:** `php artisan make:migration create_{module_name}s_table`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('{module_name}s', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('status')->default('active');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{module_name}s');
    }
};
```

## 📋 **Essential Rules**

### **DO's:**
1. ✅ Follow exact naming conventions (PascalCase for classes, snake_case for files/folders)
2. ✅ Always extend base classes (`AdminBaseController`, `BaseService`, `BaseModel`)
3. ✅ Use existing view layouts (`admin.crud.crud-index-layout`, `admin.crud.form`)
4. ✅ Implement required methods in controllers and services
5. ✅ Follow existing route patterns and middleware
6. ✅ Use consistent response methods (`successResponse`, `errorResponse`)
7. ✅ Follow existing filter and search configurations
8. ✅ Use existing table structure patterns

### **DON'Ts:**
1. ❌ Don't create unnecessary comments or documentation
2. ❌ Don't add extra methods in services unless absolutely required
3. ❌ Don't create custom view layouts when existing ones work
4. ❌ Don't deviate from existing naming patterns
5. ❌ Don't add unnecessary relationships in models
6. ❌ Don't create custom middleware unless required
7. ❌ Don't add extra validation rules beyond what's needed
8. ❌ Don't create separate files for simple functionality

## 🗂️ **File Generation Order**

1. Model
2. Service
3. Controller
4. Request classes (Store & Update)
5. Views (index, create, edit, show)
6. Routes
7. Menu configuration
8. Migration

## ✅ **Testing Checklist**

- [ ] CRUD operations work correctly
- [ ] Validation rules are enforced
- [ ] File uploads work (if applicable)
- [ ] Search and filters work
- [ ] Bulk operations work
- [ ] Permissions are properly configured
- [ ] Menu item appears correctly
- [ ] Breadcrumbs work
- [ ] AJAX forms work properly

## 📝 **Quick Reference**

### **Naming Conventions**
- **Module Name**: PascalCase (e.g., `Product`, `Category`)
- **Module Name (plural)**: PascalCase + 's' (e.g., `Products`, `Categories`)
- **File Names**: snake_case (e.g., `products`, `categories`)
- **Database Table**: snake_case + 's' (e.g., `products`, `categories`)

### **Required Files Checklist**
- [ ] Model (`app/Models/{ModuleName}.php`)
- [ ] Service (`app/Services/{ModuleGroup}/{ModuleName}Service.php`)
- [ ] Store Request (`app/Http/Requests/{ModuleName}s/Store{ModuleName}Request.php`)
- [ ] Update Request (`app/Http/Requests/{ModuleName}s/Update{ModuleName}Request.php`)
- [ ] Controller (`app/Http/Controllers/Admin/{ModuleName}Controller.php`)
- [ ] Migration (`database/migrations/create_{module_name}s_table.php`)
- [ ] View Files (`resources/views/admin/{module_name}s/`)
- [ ] Routes (in `routes/admin.php`)
- [ ] Menu Configuration (in `config/admin_menu.php`)

### **Features Included**
- ✅ Full CRUD operations
- ✅ File upload handling
- ✅ Drag-and-drop sorting
- ✅ Bulk delete operations
- ✅ Record cloning
- ✅ Activity logging
- ✅ Soft deletes
- ✅ Audit fields
- ✅ AJAX modals
- ✅ Form validation

## 🎯 **Example: Creating a "Products" Module**

Replace `{ModuleName}` with `Product` and `{module_name}` with `product`:

- Model: `Product.php`
- Controller: `ProductController.php`
- Service: `ProductService.php`
- Routes: `products`
- Views: `admin/products/`
- Table: `products`

This structure ensures consistency across all admin modules and provides a solid foundation for scalable CRUD operations.