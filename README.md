# Laravel Trogon - Educational Platform

A comprehensive Laravel-based educational platform with multi-role authentication, course management, and advanced learning features.

## 🚀 Project Overview

Laravel Trogon is a modern educational platform built with Laravel 12, featuring a clean service-oriented architecture with multi-role authentication, comprehensive course management, and advanced learning tools. The platform supports administrators, tutors, and students with role-based access control.

### ✨ Key Features

- **Multi-Role Authentication System** - Admin, Tutor, and Student roles with JWT API authentication
- **Comprehensive Course Management** - Programs, Courses, Course Units, and Course Materials
- **Advanced Learning Features** - Nested course structures, file uploads, and content management
- **Service-Oriented Architecture** - Clean service layer with standardized business logic
- **File Management** - Automatic file uploads, image processing, and file URL generation
- **Activity Logging** - Comprehensive audit trail for all user actions
- **Modern UI** - Tailwind CSS with responsive design and AJAX modals
- **API-First Design** - RESTful API with JWT authentication for mobile/web integration

## 🏗️ Architecture

### Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Authentication**: JWT (tymon/jwt-auth)
- **Database**: MySQL/PostgreSQL with SQLite for development
- **Frontend**: Tailwind CSS 4.0, Vite, Alpine.js
- **File Storage**: Local/AWS S3 with Intervention Image processing
- **Testing**: PHPUnit with Laravel testing features

### Project Structure

```
app/
├── Enums/                           # Application enums
│   └── Role.php                     # User role definitions
├── Helpers/                         # Global helper functions
│   ├── feature_helper.php
│   ├── file_helper.php
│   ├── permission_helper.php
│   ├── ui_helper.php
│   └── user_helper.php
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                   # Admin panel controllers
│   │   ├── Api/                     # API controllers
│   │   ├── Auth/                    # Authentication controllers
│   │   ├── Base/                    # Base controller classes
│   │   ├── Student/                 # Student panel controllers
│   │   ├── Tutor/                   # Tutor panel controllers
│   │   └── Website/                 # Public website controllers
│   ├── Middleware/                  # Custom middleware
│   ├── Requests/                    # Form request validation
│   └── Resources/                   # API resources
├── Models/
│   ├── BaseModel.php                # Base model with common functionality
│   ├── BaseAuthModel.php            # Base model for authenticated users
│   ├── User.php                     # Main user model
│   ├── ActivityLog.php              # Activity logging
│   ├── Category.php                  # Course categories
│   ├── Course.php                 # Courses
│   ├── CourseMaterial.php            # Course materials
│   ├── CourseUnit.php               # Course units (nested structure)
│   ├── Feed.php                     # News feeds
│   ├── FeedCategory.php             # Feed categories
│   ├── LoginAttempt.php             # Login attempt tracking
│   ├── Notification.php             # User notifications
│   ├── Otp.php                      # OTP management
│   ├── Program.php                  # Educational programs
│   ├── Review.php                   # User reviews
│   ├── Role.php                     # User roles
│   ├── Testimonial.php              # User testimonials
│   └── Traits/                      # Model traits
│       ├── HasFileUrls.php          # File URL generation
│       ├── HasNestedChildren.php     # Nested relationships
│       └── HasRoles.php             # Role management
├── Providers/                       # Service providers
├── Services/                        # Business logic layer
│   ├── Core/                        # Core services
│   │   ├── BaseService.php          # Base service class
│   │   ├── FileUploadService.php    # File upload handling
│   │   └── CacheService.php         # Caching service
│   ├── Auth/                        # Authentication services
│   ├── App/                         # Application services
│   ├── [Module]s/                   # Module-specific services
│   └── Traits/                      # Service traits
│       ├── CacheableService.php     # Caching functionality
│       └── HasNestedService.php     # Nested data handling
└── Support/                         # Support classes
    └── ApiExceptionFormatter.php     # API exception formatting
```

## 🔐 Authentication System

### Multi-Guard Authentication

The platform uses Laravel's multi-guard authentication system:

```php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
    'admin' => ['driver' => 'session', 'provider' => 'users'],
    'tutor' => ['driver' => 'session', 'provider' => 'users'],
    'student' => ['driver' => 'session', 'provider' => 'users'],
    'api' => ['driver' => 'jwt', 'provider' => 'users'],
],
```

### User Roles

- **Admin** (Role ID: 1) - Full system access
- **Student** (Role ID: 2) - Learning platform access
- **Tutor** (Role ID: 3) - Teaching and content management

### Authentication Methods

- **Email/Password Login**
- **Username/Password Login**
- **Phone OTP Login** (with multiple providers: AWS SNS, MSG91, Twilio)

## 📚 Course Management System

### Hierarchical Structure

```
Programs
└── Courses
    └── Course Units (Nested)
        ├── Subjects
        ├── Lessons
        └── Inner Lessons
            └── Course Materials
```

### Key Models

- **Program** - Educational programs/degrees
- **Course** - Individual courses within programs
- **CourseUnit** - Hierarchical course structure (subjects, lessons)
- **CourseMaterial** - Learning materials (videos, documents, etc.)
- **Category** - Course categorization

### Features

- **Nested Course Structure** - Unlimited depth with parent-child relationships
- **Content Types** - Videos, documents, SCORM packages, live classes
- **Access Control** - Free, paid, and restricted content
- **File Management** - Automatic file uploads and processing
- **Sorting** - Drag-and-drop course organization

## 🛠️ Development Features

### Service-Oriented Architecture

The platform uses a clean service-oriented architecture with:

- **Service Layer** - Business logic separation from controllers
- **Base Services** - Common functionality for all services
- **Service Traits** - Reusable functionality (caching, nested data)
- **File Upload Handling** - Automatic file processing and URL generation
- **Activity Logging** - Automatic audit trail
- **Soft Deletes** - Safe record deletion
- **AJAX Modals** - Modern UI interactions

### Module Creation

Follow the comprehensive guide in `docs/CreateModule.md` to create new modules with:

1. **Model** - Database structure and relationships
2. **Service** - Business logic layer
3. **Controller** - HTTP request handling
4. **Requests** - Form validation
5. **Resources** - API response formatting
6. **Views** - User interface
7. **Routes** - URL routing

### Implemented Modules

✅ **Completed Modules:**
- Users
- Reviews
- Testimonials
- Categories
- Programs
- Courses
- Course Units
- Course Materials
- Feed Categories
- Feeds
- Notifications

🚧 **Planned Modules:**
- Videos, Exams, Quizzes
- Live Classes, Documents, Notes
- SCORM packages, Homeworks, Assignments
- Batches/Cohorts, Question Bank, Library
- Payments, Packages, Subscriptions
- Certificates, Gamification, Chat
- Surveys, Support, Reports, Analytics
- CRM, CMS, Integrations, AI Module

## 🔧 Installation & Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Laravel 12

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel.trogon.info
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start development server**
   ```bash
   composer run dev
   ```

### Development Commands

```bash
# Start all development services
composer run dev

# Run tests
composer run test

# Code formatting
./vendor/bin/pint
```

## 📁 File Structure

### Routes

- `routes/web.php` - Main web routes
- `routes/api.php` - API routes with JWT authentication
- `routes/admin.php` - Admin panel routes
- `routes/tutor.php` - Tutor panel routes
- `routes/student.php` - Student panel routes
- `routes/website.php` - Public website routes

### Configuration

- `config/auth.php` - Authentication configuration
- `config/jwt.php` - JWT configuration
- `config/otp.php` - OTP provider settings
- `config/images.php` - Image processing presets
- `config/permissions.php` - Role-based permissions

## 🎨 Frontend Features

### UI Components

- **Responsive Design** - Mobile-first approach with Tailwind CSS
- **AJAX Modals** - Modern form interactions
- **Drag-and-Drop** - Visual content organization
- **File Upload** - Drag-and-drop file handling
- **Data Tables** - Sortable, filterable data display
- **Breadcrumbs** - Navigation context
- **Notifications** - Real-time user feedback

### Styling

- **Tailwind CSS 4.0** - Utility-first CSS framework
- **Vite** - Modern build tool
- **Alpine.js** - Lightweight JavaScript framework
- **Custom Components** - Reusable UI elements

## 🔒 Security Features

- **JWT Authentication** - Secure API authentication
- **Role-Based Access Control** - Granular permissions
- **Activity Logging** - Comprehensive audit trail
- **Login Attempt Tracking** - Brute force protection
- **File Upload Validation** - Secure file handling
- **CSRF Protection** - Cross-site request forgery prevention
- **SQL Injection Prevention** - Eloquent ORM protection

## 📊 API Documentation

### Authentication Endpoints

```bash
# Request OTP
POST /api/v1/auth/otp/request
{
    "country_code": "+1",
    "phone": "1234567890"
}

# Verify OTP
POST /api/v1/auth/otp/verify
{
    "country_code": "+1",
    "phone": "1234567890",
    "otp": "123456"
}

# Login
POST /api/v1/auth/login
{
    "country_code": "+1",
    "phone": "1234567890"
}
```

### Protected Endpoints

All API endpoints require JWT authentication:

```bash
# Get user profile
GET /api/v1/user/profile
Authorization: Bearer <jwt_token>

# Get courses
GET /api/v1/courses
Authorization: Bearer <jwt_token>
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

## 📈 Performance

### Optimizations

- **Eager Loading** - Prevents N+1 query problems
- **Database Indexing** - Optimized database queries
- **File Caching** - Efficient file processing
- **Service Caching** - Business logic caching
- **Asset Optimization** - Minified CSS/JS

### Monitoring

- **Activity Logs** - User action tracking
- **Error Logging** - Comprehensive error reporting
- **Performance Metrics** - Query optimization

## 📝 Naming Guidelines

### Database

- **Tables** → plural, snake_case
- **Columns** → snake_case
- **Pivot tables** → singular_singular (alphabetical order)

**Examples:**
```
users
categories
reviews
testimonials
feed_categories
course_units
user_roles (pivot: user + role)
```

### Models

- **Models** → singular, PascalCase
- Corresponds to one table
- Uses singular form, even if the table is plural

**Examples:**
```
User
Category
Review
Testimonial
FeedCategory
CourseUnit
```

### Controllers

- **Controllers** → singular + Controller suffix
- Each controller manages a resource type

**Examples:**
```
UserController
CategoryController
ReviewController
TestimonialController
FeedCategoryController
CourseUnitController
```

### Services

- **Services** → singular + Service suffix
- **Folder name** → plural

**Examples:**
```
app/Services/Users/UserService.php
app/Services/Categories/CategoryService.php
app/Services/Reviews/ReviewService.php
```

### Views

- **Views** → plural, snake_case folder names
- **Blade files** → snake_case.blade.php

**Examples:**
```
resources/views/admin/users/index.blade.php
resources/views/admin/categories/form.blade.php
resources/views/admin/reviews/show.blade.php
```

### Routes

- **Web routes** → plural (collection-based URIs)
- **API routes** → plural, versioned if needed

**Examples:**
```php
Route::resource('users', UserController::class);
Route::resource('categories', CategoryController::class);
Route::resource('reviews', ReviewController::class);

Route::prefix('v1')->group(function () {
    Route::apiResource('feed-categories', FeedCategoryController::class);
});
```

### Migrations

- **Migrations** → use plural table names
- Use create_ or add_ prefixes

**Examples:**
```
2025_09_11_000000_create_users_table.php
2025_09_11_010000_create_categories_table.php
2025_09_11_020000_add_status_to_reviews_table.php
```

### Summary

- **Plural** → Tables, Views, Routes
- **Singular** → Models, Controllers, Services
- **CamelCase** → PHP class names
- **snake_case** → DB, Views, Files

## 🏗️ Architecture Patterns

### Service Layer Pattern

The platform uses a service-oriented architecture where:

1. **Controllers** handle HTTP requests and responses
2. **Services** contain business logic
3. **Models** handle data and relationships
4. **Requests** handle validation

## 📋 Base Classes & Examples

### Base Service Class

The `BaseService` provides comprehensive CRUD operations and advanced features:

```php
<?php

namespace App\Services\Core;

abstract class BaseService
{
    protected $model;

    public function __construct()
    {
        if (isset($this->modelClass)) {
            $this->model = new $this->modelClass();
        }
    }

    // Common CRUD operations
    public function getAll(): Collection
    {
        return $this->model->sorted()->get();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->sorted()->paginate($perPage);
    }

    public function find(int $id, array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    public function store(array $data): Model
    {
        $this->processFileUploads($data);
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Model
    {
        $record = $this->model->find($id);
        if (!$record) return null;

        $this->processFileUploads($data, $record);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);
        if (!$record) return false;

        $this->deleteAttachedFiles($record);
        return $record->delete();
    }

    public function bulkDelete(array $ids): int
    {
        $records = $this->model->whereIn('id', $ids)->get();
        foreach ($records as $record) {
            if ($record) {
                $this->deleteAttachedFiles($record);
            }
        }
        return $this->model->whereIn('id', $ids)->delete();
    }

    // Advanced features
    public function sortUpdate(array $order, string $column = 'sort_order'): bool
    {
        foreach ($order as $position => $id) {
            $record = $this->model->find($id);
            if ($record) {
                $record->update([$column => $position + 1]);
            }
        }
        return true;
    }

    public function clone(Model $model, array $overrides = []): ?Model
    {
        // Comprehensive cloning with file handling and unique field management
        // See full implementation in BaseService
    }

    // Abstract methods that each service must implement
    abstract public function getFilterConfig(): array;
    abstract public function getSearchFieldsConfig(): array;
    abstract public function getDefaultSearchFields(): array;
    abstract public function getDefaultSorting(): array;
}
```

### Base Model Class

```php
<?php

namespace App\Models;

abstract class BaseModel extends Model
{
    use SoftDeletes, HasFileUrls, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    protected static function booted()
    {
        // Set audit fields automatically
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = $model->created_by ?? Auth::id();
                $model->updated_by = $model->updated_by ?? Auth::id();
            }

            if (Schema::hasColumn($model->getTable(), 'sort_order') && empty($model->sort_order)) {
                $model->sort_order = (static::max('sort_order') ?? 0) + 1;
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && !$model->isForceDeleting()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });

        // Activity logging
        static::created(fn ($model) => static::logActivity($model, 'created'));
        static::updated(fn ($model) => static::logActivity($model, 'updated'));
        static::deleted(fn ($model) => !$model->isForceDeleting() && static::logActivity($model, 'deleted'));
        static::restored(fn ($model) => static::logActivity($model, 'restored'));
    }

    // Common query scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1)->orWhere('status', 'active');
    }

    public function scopeSorted($query)
    {
        $model = $query->getModel();
        $table = $model->getTable();

        if (Schema::hasColumn($table, 'sort_order')) {
            return $query->orderBy('sort_order', 'asc')
                        ->orderBy('created_at', 'desc');
        }

        return $query->orderBy('created_at', 'desc');
    }

    // Audit relationships
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }
}
```

### Base API Controller

```php
<?php

namespace App\Http\Controllers\Api;

abstract class BaseApiController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = authUser();
    }

    protected function respondSuccess($data = [], string $message = 'Success', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status'       => true,
            'http_code'    => $status,
            'message'      => $message,
            'data'         => $data,
            'errors'       => (object) [],
            'meta'         => (object) [],
        ], $status);
    }

    protected function respondError(string $message = 'Error', int $status = Response::HTTP_BAD_REQUEST, $errors = []): JsonResponse
    {
        return response()->json([
            'status'       => false,
            'http_code'    => $status,
            'message'      => $message,
            'data'         => (object) [],
            'errors'       => $errors,
            'meta'         => (object) [],
        ], $status);
    }

    protected function respondValidationError(string $message = 'Validation failed', $errors = []): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    protected function serviceResponse(array $result, string $successMessage = 'Success'): JsonResponse
    {
        $httpCode = $result['status']
            ? Response::HTTP_OK
            : ($result['http_code'] ?? Response::HTTP_BAD_REQUEST);

        return response()->json([
            'status'       => $result['status'],
            'http_code'    => $httpCode,
            'message'      => $result['message'] ?? $successMessage,
            'data'         => $result['data'] ?? (object) [],
            'errors'       => $result['errors'] ?? (object) [],
            'meta'         => $result['meta'] ?? (object) [],
        ], $httpCode);
    }
}
```

### Admin Base Controller

```php
<?php

namespace App\Http\Controllers\Admin;

abstract class AdminBaseController extends RoleBaseController
{
    protected string $guard = 'admin';
    protected ?int $requiredRoleId = 1;

    use AdminControllerHelpers;

    protected function redirectToDashboard()
    {
        return redirect()->route('admin.dashboard');
    }
}
```

## 🔄 Complete CRUD Example

### Example Model

```php
<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Traits\HasNestedChildren;

class Category extends BaseModel
{
    use HasNestedChildren;

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $fileFields = [
        'image' => [
            'folder' => 'categories',
            'type' => 'category_image',
            'single' => true,
        ],
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->sorted();
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
```

### Example Service

```php
<?php

namespace App\Services\Categories;

use App\Models\Category;
use App\Services\Core\BaseService;
use App\Services\Traits\CacheableService;
use App\Services\Traits\HasNestedService;

class CategoryService extends BaseService
{
    use CacheableService, HasNestedService;
    
    protected string $modelClass = Category::class;
    protected string $cachePrefix = 'categories';
    protected int $cacheTtl = 1800;

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
            'parent_id' => [
                'type' => 'select',
                'label' => 'Parent Category',
                'col' => 3,
                'options' => $this->getIdTitle(),
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['title', 'description'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    public function getFlatList()
    {
        return $this->remember('flat_list', function() {
            return $this->model->with('parent')->get()->map(function($item) {
                $item->indented_title = str_repeat('— ', $item->depth) . $item->title;
                return $item;
            });
        });
    }
}
```

### Example Admin Controller

```php
<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Categories\CategoryService;
use App\Http\Requests\Categories\StoreCategoryRequest as StoreRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest as UpdateRequest;

class CategoryController extends AdminBaseController
{
    protected CategoryService $service;

    public function __construct(CategoryService $service)
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

        if($filters){
            $list_items = $this->service->getFilteredData($params);
        }else{
            $list_items = $this->service->getFlatList();
        }

        return view('admin.categories.index', [
            'page_title' => 'Categories',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        $parentCategories = $this->service->getFlatList()->pluck('indented_title', 'id');
        
        return view('admin.categories.create', [
            'parentCategories' => $parentCategories,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Category added successfully');
    }

    public function edit($id)
    {
        $edit_data = $this->service->find($id);
        $parentCategories = $this->service->getFlatList()->pluck('indented_title', 'id');

        return view('admin.categories.edit', [
            'edit_data' => $edit_data,
            'parentCategories' => $parentCategories,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Category updated successfully');
    }

    public function show($id)
    {
        return view('admin.categories.show', [
            'item' => $this->service->find($id),
        ]);
    }

    public function sortView(Request $request)
    {
        return view('admin.categories.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        return $this->successResponse('Sort order updated successfully');
    }

    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete item');
        }
        return $this->successResponse('Item deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete items');
        }
        return $this->successResponse('Selected items deleted successfully');
    }

    public function cloneItem($id)
    {
        $item = $this->service->find($id);
        $cloned = $this->service->clone($item);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone item.');
        }

        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.categories.edit', $cloned->id),
        ]);
    }
}
```

### Example Request Validation

```php
<?php

namespace App\Http\Requests\Categories;

use App\Http\Requests\BaseRequest;

class StoreCategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'status.required' => 'Status is required.',
            'image.image' => 'File must be an image.',
        ];
    }
}
```

## 🌐 API Implementation Examples

### Example API Controller

```php
<?php

namespace App\Http\Controllers\Api;

use App\Services\Categories\CategoryService;
use App\Http\Resources\Categories\CategoryResource;
use Illuminate\Http\Request;

class CategoryApiController extends BaseApiController
{
    protected CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $categories = $this->service->paginate($perPage);
        
        return $this->respondPaginated($categories, 'Categories fetched successfully');
    }

    public function show($id)
    {
        $category = $this->service->find($id);
        
        if (!$category) {
            return $this->respondError('Category not found', 404);
        }

        return $this->respondSuccess(
            new CategoryResource($category),
            'Category fetched successfully'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:0,1',
        ]);

        $category = $this->service->store($data);

        return $this->respondSuccess(
            new CategoryResource($category),
            'Category created successfully',
            201
        );
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:0,1',
        ]);

        $category = $this->service->update($id, $data);

        if (!$category) {
            return $this->respondError('Category not found', 404);
        }

        return $this->respondSuccess(
            new CategoryResource($category),
            'Category updated successfully'
        );
    }

    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->respondError('Category not found', 404);
        }

        return $this->respondSuccess([], 'Category deleted successfully');
    }
}
```

### Example API Resource

```php
<?php

namespace App\Http\Resources\Categories;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class CategoryResource extends BaseResource
{
    protected function resourceFields(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'image' => $this->image_url,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

## 🔄 API Flow Examples

### Authentication Flow

```bash
# 1. Request OTP
POST /api/v1/auth/otp/request
{
    "country_code": "+1",
    "phone": "1234567890"
}

# Response
{
    "status": true,
    "http_code": 200,
    "message": "OTP sent successfully",
    "data": {},
    "errors": {},
    "meta": {}
}

# 2. Verify OTP
POST /api/v1/auth/otp/verify
{
    "country_code": "+1",
    "phone": "1234567890",
    "otp": "123456"
}

# Response
{
    "status": true,
    "http_code": 200,
    "message": "OTP verified successfully",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+11234567890"
        }
    },
    "errors": {},
    "meta": {}
}
```

### CRUD API Flow

```bash
# 1. Get Categories
GET /api/v1/categories
Authorization: Bearer <jwt_token>

# Response
{
    "status": true,
    "http_code": 200,
    "message": "Categories fetched successfully",
    "data": [
        {
            "id": 1,
            "title": "Programming",
            "description": "Programming courses",
            "status": 1,
            "image": "https://example.com/images/category1.jpg",
            "parent_id": null,
            "sort_order": 1
        }
    ],
    "errors": {},
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1
    }
}

# 2. Create Category
POST /api/v1/categories
Authorization: Bearer <jwt_token>
{
    "title": "Web Development",
    "description": "Web development courses",
    "parent_id": 1,
    "status": 1
}

# Response
{
    "status": true,
    "http_code": 201,
    "message": "Category created successfully",
    "data": {
        "id": 2,
        "title": "Web Development",
        "description": "Web development courses",
        "status": 1,
        "image": null,
        "parent_id": 1,
        "sort_order": 2
    },
    "errors": {},
    "meta": {}
}

# 3. Update Category
PUT /api/v1/categories/2
Authorization: Bearer <jwt_token>
{
    "title": "Advanced Web Development",
    "description": "Advanced web development courses",
    "status": 1
}

# Response
{
    "status": true,
    "http_code": 200,
    "message": "Category updated successfully",
    "data": {
        "id": 2,
        "title": "Advanced Web Development",
        "description": "Advanced web development courses",
        "status": 1,
        "image": null,
        "parent_id": 1,
        "sort_order": 2
    },
    "errors": {},
    "meta": {}
}

# 4. Delete Category
DELETE /api/v1/categories/2
Authorization: Bearer <jwt_token>

# Response
{
    "status": true,
    "http_code": 200,
    "message": "Category deleted successfully",
    "data": {},
    "errors": {},
    "meta": {}
}
```

## 🤖 AI Development Guide

### For AI Assistants

When working with this codebase, follow these patterns:

#### 1. Service-First Approach
- Always use services for business logic
- Controllers should be thin and delegate to services
- Use dependency injection for service dependencies

#### 2. Model Relationships
- Use Eloquent relationships for data access
- Leverage model traits for common functionality
- Follow the established naming conventions

#### 3. File Handling
- Use the FileUploadService for file operations
- Define file fields in models using $fileFields property
- Use image presets from config/images.php

#### 4. Validation
- Create dedicated Request classes for validation
- Use BaseRequest for common functionality
- Follow Laravel validation rules

#### 5. API Responses
- Use BaseApiController for consistent API responses
- Create Resource classes for data formatting
- Follow the established response structure

### Common Patterns

#### Creating a New Module

1. **Model** - Extend BaseModel or BaseAuthModel
2. **Service** - Extend BaseService with business logic
3. **Controller** - Extend appropriate base controller
4. **Requests** - Create validation classes
5. **Resources** - Create API response classes
6. **Views** - Create Blade templates
7. **Routes** - Add to appropriate route file

#### File Upload Pattern

```php
// In Model
protected $fileFields = [
    'image' => [
        'folder' => 'module_name',
        'type' => 'module_image',
        'single' => true,
    ],
];

// In Service
public function store(array $data)
{
    $this->processFileUploads($data);
    return parent::store($data);
}
```

#### Caching Pattern

```php
// In Service
use App\Services\Traits\CacheableService;

class ModuleService extends BaseService
{
    use CacheableService;
    
    protected string $cachePrefix = 'module';
    protected int $cacheTtl = 1800;
    
    public function getCachedData()
    {
        return $this->remember('key', function() {
            return $this->model->get();
        });
    }
}
```

## 🛠️ Service Traits

### CacheableService Trait

```php
<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheableService
{
    protected function isCacheEnabled(): bool
    {
        return !app()->environment('local', 'testing');
    }

    protected function getCacheTtl(): int
    {
        return $this->cacheTtl ?? 300;
    }

    protected function getCachePrefix(): string
    {
        return $this->cachePrefix ?? strtolower(class_basename(static::class));
    }

    protected function cacheKey(string $suffix): string
    {
        return "{$this->getCachePrefix()}:{$suffix}";
    }

    protected function remember(string $key, \Closure $callback, ?int $ttl = null)
    {
        if (!$this->isCacheEnabled()) {
            return $callback();
        }

        $ttl = $ttl ?? $this->getCacheTtl();
        return Cache::remember($this->cacheKey($key), $ttl, $callback);
    }

    public function clearCache(): void
    {
        if (!$this->isCacheEnabled()) {
            return;
        }

        $pattern = $this->getCachePrefix() . ':*';
        Cache::forget($pattern);
    }
}
```

### HasNestedService Trait

```php
<?php

namespace App\Services\Traits;

use Illuminate\Support\Collection;

trait HasNestedService
{
    public function getRootItems(): Collection
    {
        return $this->model->whereNull('parent_id')->sorted()->get();
    }

    public function getLeafItems(): Collection
    {
        return $this->model->whereDoesntHave('children')->sorted()->get();
    }

    public function getTree($parentId = null): Collection
    {
        $query = $this->model->where('parent_id', $parentId);
        
        $items = $query->with(['children' => function ($query) {
            $query->sorted();
        }])->sorted()->get();
        
        return $items;
    }

    public function getDescendants(int $id): Collection
    {
        $item = $this->model->find($id);
        if (!$item) {
            return collect();
        }
        
        return $item->descendants()->sorted()->get();
    }

    public function getAncestors(int $id): Collection
    {
        $item = $this->model->find($id);
        if (!$item) {
            return collect();
        }
        
        return $item->ancestors()->sorted()->get();
    }
}
```

## 📝 Request Validation Examples

```php
<?php

namespace App\Http\Requests\Categories;

use App\Http\Requests\BaseRequest;

class StoreCategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'status.required' => 'Status is required.',
            'image.image' => 'File must be an image.',
        ];
    }
}
```

### Model Traits

- **HasFileUrls** - File URL generation
- **HasNestedChildren** - Parent-child relationships
- **HasRoles** - Role management

### Controller Traits

- **AdminControllerHelpers** - Common admin functionality

#### 3. File Handling
- Use the FileUploadService for file operations
- Define file fields in models using $fileFields property
- Use image presets from config/images.php

#### 4. Validation
- Create dedicated Request classes for validation
- Use BaseRequest for common functionality
- Follow Laravel validation rules

#### 5. API Responses
- Use BaseApiController for consistent API responses
- Create Resource classes for data formatting
- Follow the established response structure

### Common Patterns

#### Creating a New Module

1. **Model** - Extend BaseModel or BaseAuthModel
2. **Service** - Extend BaseService with business logic
3. **Controller** - Extend appropriate base controller
4. **Requests** - Create validation classes
5. **Resources** - Create API response classes
6. **Views** - Create Blade templates
7. **Routes** - Add to appropriate route file

#### File Upload Pattern

```php
// In Model
protected $fileFields = [
    'image' => [
        'folder' => 'module_name',
        'type' => 'module_image',
        'single' => true,
    ],
];

// In Service
public function store(array $data)
{
    $this->processFileUploads($data);
    return parent::store($data);
}
```

#### Caching Pattern

```php
// In Service
use App\Services\Traits\CacheableService;

class ModuleService extends BaseService
{
    use CacheableService;
    
    protected string $cachePrefix = 'module';
    protected int $cacheTtl = 1800;
    
    public function getCachedData()
    {
        return $this->remember('key', function() {
            return $this->model->get();
        });
    }
}
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Follow the coding standards
4. Write tests for new features
5. Submit a pull request

### Coding Standards

- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Write comprehensive tests
- Document new features
- Follow the established architecture patterns
- **Follow the naming guidelines above**

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:

- Check the documentation in the `docs/` directory
- Review the module creation guide
- Examine existing modules for patterns
- Create an issue for bugs or feature requests

---

**Laravel Trogon** - Building the future of education with modern web technologies.