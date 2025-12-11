# CRUD Customization Guide

This guide covers how to customize CRUD (Create, Read, Update, Delete) components in the Laravel admin panel, including custom buttons, forms, and filter sections.

## Table of Contents

1. [Custom Buttons in CRUD Index Pages](#custom-buttons-in-crud-index-pages)
2. [Creating CRUD Forms](#creating-crud-forms)
3. [Creating Filter Sections](#creating-filter-sections)
4. [Available Field Types](#available-field-types)
5. [Examples and Best Practices](#examples-and-best-practices)

---

## Custom Buttons in CRUD Index Pages

### Overview

The CRUD index layout supports custom buttons that can be added alongside the standard "Add" and "Sort" buttons. Custom buttons support three types: `link`, `modal`, and `button`.

### Implementation

#### 1. Using the Layout System (Recommended)

The `crud-index-layout.blade.php` supports a `customButtons` parameter that accepts an array of button configurations.

**Example in your index view:**

```php
@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Courses',
    'createUrl'     => url('admin/courses/create'),
    'sortUrl'       => url('admin/courses/sort'),
    'bulkDeleteUrl' => url('admin/courses/bulk-delete'),
    'redirectUrl'   => url('admin/courses'),
    'tableId'       => 'courses-table',
    'list_items'    => $list_items,
    'customButtons' => [
        [
            'type'  => 'link',
            'text'  => 'Categories',
            'url'   => url('admin/categories'),
            'class' => 'btn-outline-success',
            'icon'  => 'mdi mdi-tag-multiple',
            'enabled' => has_feature('categories')
        ],
        [
            'type'  => 'modal',
            'text'  => 'Import Data',
            'url'   => url('admin/courses/import'),
            'class' => 'btn-outline-info',
            'icon'  => 'mdi mdi-upload',
            'title' => 'Import Courses',
            'enabled' => true
        ],
        [
            'type'  => 'button',
            'text'  => 'Export',
            'class' => 'btn-outline-secondary',
            'icon'  => 'mdi mdi-download',
            'onclick' => 'exportCourses()',
            'enabled' => true
        ]
    ],
    // ... other parameters
])
```

#### 2. Button Configuration Options

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `type` | string | Yes | Button type: `link`, `modal`, or `button` |
| `text` | string | Yes | Button text/label |
| `class` | string | No | CSS classes (default: `btn-outline-info`) |
| `icon` | string | No | Icon class (e.g., `mdi mdi-plus`) |
| `enabled` | boolean | No | Whether button is enabled (default: `true`) |

**Type-specific parameters:**

- **Link buttons:**
  - `url` (required): Target URL

- **Modal buttons:**
  - `url` (required): Modal content URL
  - `title` (required): Modal title

- **Custom buttons:**
  - `onclick` (required): JavaScript function to execute

#### 3. Available Button Classes

```css
/* Primary actions */
btn-primary
btn-success
btn-warning
btn-danger

/* Outline variants */
btn-outline-primary
btn-outline-success
btn-outline-warning
btn-outline-danger
btn-outline-info
btn-outline-secondary
btn-outline-dark
```

#### 4. Common Icons

```css
/* Material Design Icons */
mdi mdi-plus          /* Add */
mdi mdi-sort          /* Sort */
mdi mdi-tag-multiple  /* Categories */
mdi mdi-chart-box     /* Programs */
mdi mdi-upload        /* Import */
mdi mdi-download      /* Export */
mdi mdi-cog           /* Settings */
mdi mdi-eye           /* View */
mdi mdi-pencil        /* Edit */
mdi mdi-delete        /* Delete */
```

---

## Creating CRUD Forms

### Overview

CRUD forms use a flexible field-based system that supports various input types and configurations.

### Implementation

#### 1. Basic Form Structure

```php
@include('admin.crud.form', [
    'action' => url('admin/courses'),
    'method' => 'POST',
    'redirect' => url('admin/courses'),
    'submitText' => 'Save Course',
    'fields' => [
        // Field configurations here
    ]
])
```

#### 2. Field Configuration

Each field is an array with the following structure:

```php
[
    'type' => 'text',           // Field type
    'name' => 'title',          // Field name
    'id' => 'title',            // Field ID
    'label' => 'Course Title',  // Field label
    'value' => $course->title,  // Default value
    'required' => true,         // Required field
    'placeholder' => 'Enter title',
    'col' => 6,                 // Bootstrap column size
    'enabled' => true,          // Field enabled/disabled
    // Type-specific options
]
```

### Available Field Types

#### Text Fields

```php
[
    'type' => 'text',
    'name' => 'title',
    'label' => 'Title',
    'required' => true,
    'placeholder' => 'Enter title'
]
```

#### Number Fields

```php
[
    'type' => 'number',
    'name' => 'price',
    'label' => 'Price',
    'min' => 0,
    'max' => 9999,
    'step' => 0.01
]
```

#### Textarea Fields

```php
[
    'type' => 'textarea',
    'name' => 'description',
    'label' => 'Description',
    'rows' => 5,
    'placeholder' => 'Enter description'
]
```

#### Select Fields

```php
[
    'type' => 'select',
    'name' => 'category_id',
    'label' => 'Category',
    'options' => [
        1 => 'Web Development',
        2 => 'Mobile Development',
        3 => 'Data Science'
    ],
    'required' => true
]
```

#### Select2 Fields (Enhanced Dropdown)

```php
[
    'type' => 'select2',
    'name' => 'program_id',
    'label' => 'Program',
    'options' => $programs, // Array of options
    'placeholder' => 'Select a program',
    'required' => true
]
```

#### Multiple Select Fields

```php
[
    'type' => 'select-multiple',
    'name' => 'tags[]',
    'label' => 'Tags',
    'options' => $tags,
    'value' => $selectedTags // Array of selected values
]
```

#### Date Fields

```php
[
    'type' => 'date',
    'name' => 'start_date',
    'label' => 'Start Date',
    'required' => true
]
```

#### File Upload Fields

```php
[
    'type' => 'file',
    'name' => 'thumbnail',
    'label' => 'Thumbnail',
    'accept' => 'image/*',
    'required' => false
]
```

#### Multiple File Upload

```php
[
    'type' => 'files',
    'name' => 'attachments[]',
    'label' => 'Attachments',
    'accept' => '.pdf,.doc,.docx',
    'multiple' => true
]
```

#### Image Upload Fields

```php
[
    'type' => 'image',
    'name' => 'featured_image',
    'label' => 'Featured Image',
    'accept' => 'image/*',
    'preview' => true // Show image preview
]
```

#### Separator Fields

```php
[
    'type' => 'seperator',
    'label' => 'Additional Information',
    'col' => 12
]
```

### Form Examples

#### Complete Course Form

```php
@include('admin.crud.form', [
    'action' => url('admin/courses'),
    'method' => 'POST',
    'redirect' => url('admin/courses'),
    'submitText' => 'Save Course',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'label' => 'Course Title',
            'required' => true,
            'placeholder' => 'Enter course title',
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'slug',
            'label' => 'Slug',
            'placeholder' => 'course-slug',
            'col' => 6
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'label' => 'Description',
            'rows' => 4,
            'col' => 12
        ],
        [
            'type' => 'select2',
            'name' => 'category_id',
            'label' => 'Category',
            'options' => $categories,
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'select2',
            'name' => 'program_id',
            'label' => 'Program',
            'options' => $programs,
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'price',
            'label' => 'Price',
            'min' => 0,
            'step' => 0.01,
            'col' => 4
        ],
        [
            'type' => 'date',
            'name' => 'start_date',
            'label' => 'Start Date',
            'col' => 4
        ],
        [
            'type' => 'date',
            'name' => 'end_date',
            'label' => 'End Date',
            'col' => 4
        ],
        [
            'type' => 'image',
            'name' => 'thumbnail',
            'label' => 'Thumbnail',
            'accept' => 'image/*',
            'preview' => true,
            'col' => 6
        ],
        [
            'type' => 'files',
            'name' => 'attachments[]',
            'label' => 'Attachments',
            'accept' => '.pdf,.doc,.docx',
            'multiple' => true,
            'col' => 6
        ]
    ]
])
```

---

## Creating Filter Sections

### Overview

Filter sections provide search and filtering capabilities for CRUD index pages using the universal filter system.

### Implementation

#### 1. Service Configuration

In your service class, define filter and search configurations:

```php
// In your Service class (e.g., CourseService.php)

public function getFilterConfig()
{
    return [
        'status' => [
            'type' => 'select',
            'label' => 'Status',
            'options' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'draft' => 'Draft'
            ],
            'enabled' => true
        ],
        'category_id' => [
            'type' => 'select2',
            'label' => 'Category',
            'options' => $this->categoryService->getIdTitle(),
            'enabled' => has_feature('categories')
        ],
        'program_id' => [
            'type' => 'select2',
            'label' => 'Program',
            'options' => $this->programService->getIdTitle(),
            'enabled' => has_feature('programs')
        ],
        'price_range' => [
            'type' => 'number',
            'label' => 'Min Price',
            'min' => 0,
            'enabled' => true
        ],
        'date_range' => [
            'type' => 'date-range',
            'label' => 'Date Range',
            'enabled' => true
        ]
    ];
}

public function getSearchConfig()
{
    return [
        'search_fields' => [
            'title' => 'Title',
            'description' => 'Description',
            'slug' => 'Slug'
        ]
    ];
}
```

#### 2. Controller Implementation

```php
// In your Controller

public function index()
{
    $filterConfig = $this->service->getFilterConfig();
    $searchConfig = $this->service->getSearchConfig();

    return view('admin.courses.index', [
        'page_title' => 'Courses',
        'list_items' => $this->service->getAll(),
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig,
    ]);
}
```

#### 3. View Implementation

```php
@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Courses',
    'createUrl'     => url('admin/courses/create'),
    'sortUrl'       => url('admin/courses/sort'),
    'bulkDeleteUrl' => url('admin/courses/bulk-delete'),
    'redirectUrl'   => url('admin/courses'),
    'tableId'       => 'courses-table',
    'list_items'    => $list_items,
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    // ... other parameters
])
```

### Available Filter Field Types

#### Select Filters

```php
'status' => [
    'type' => 'select',
    'label' => 'Status',
    'options' => [
        'active' => 'Active',
        'inactive' => 'Inactive'
    ],
    'col' => 3 // Bootstrap column size
]
```

#### Select2 Filters

```php
'category_id' => [
    'type' => 'select2',
    'label' => 'Category',
    'options' => $categories,
    'col' => 3
]
```

#### Multiple Select Filters

```php
'tags' => [
    'type' => 'select-multiple',
    'label' => 'Tags',
    'options' => $tags,
    'col' => 3
]
```

#### Text Filters

```php
'search_text' => [
    'type' => 'text',
    'label' => 'Search Text',
    'placeholder' => 'Enter search text',
    'col' => 3
]
```

#### Number Filters

```php
'min_price' => [
    'type' => 'number',
    'label' => 'Min Price',
    'min' => 0,
    'step' => 0.01,
    'col' => 3
]
```

#### Date Range Filters

```php
'date_range' => [
    'type' => 'date-range',
    'label' => 'Date Range',
    'col' => 4
]
```

### Filter Processing in Service

```php
// In your Service class

public function getAll($filters = [])
{
    $query = $this->model->newQuery();

    // Search functionality
    if (request('search')) {
        $searchTerm = request('search');
        $searchFields = $this->getSearchConfig()['search_fields'];
        
        $query->where(function($q) use ($searchTerm, $searchFields) {
            foreach ($searchFields as $field => $label) {
                $q->orWhere($field, 'like', "%{$searchTerm}%");
            }
        });
    }

    // Filter processing
    foreach ($filters as $field => $value) {
        if (empty($value)) continue;

        switch ($field) {
            case 'status':
                $query->where('status', $value);
                break;
            case 'category_id':
                $query->where('category_id', $value);
                break;
            case 'min_price':
                $query->where('price', '>=', $value);
                break;
            case 'date_range':
                if (isset($value['from'])) {
                    $query->whereDate('created_at', '>=', $value['from']);
                }
                if (isset($value['to'])) {
                    $query->whereDate('created_at', '<=', $value['to']);
                }
                break;
        }
    }

    return $query->orderBy('sort_order')->get();
}
```

---

## Examples and Best Practices

### 1. Feature-Based Button Enabling

```php
'customButtons' => [
    [
        'type' => 'link',
        'text' => 'Categories',
        'url' => url('admin/categories'),
        'class' => 'btn-outline-success',
        'icon' => 'mdi mdi-tag-multiple',
        'enabled' => has_feature('categories')
    ],
    [
        'type' => 'link',
        'text' => 'Programs',
        'url' => url('admin/programs'),
        'class' => 'btn-outline-info',
        'icon' => 'mdi mdi-chart-box',
        'enabled' => has_feature('programs')
    ]
]
```

### 2. Conditional Field Display

```php
[
    'type' => 'select2',
    'name' => 'program_id',
    'label' => 'Program',
    'options' => $programs,
    'enabled' => has_feature('programs'),
    'col' => 6
]
```

### 3. Dynamic Options Loading

```php
// In your controller
public function create()
{
    $categories = $this->categoryService->getFlatCategoriesOptions();
    $programs = $this->programService->getIdTitle();

    return view('admin.courses.create', [
        'page_title' => 'Add Course',
        'categories' => $categories,
        'programs' => $programs,
    ]);
}
```

### 4. Form Validation Integration

```php
// In your form fields
[
    'type' => 'text',
    'name' => 'title',
    'label' => 'Title',
    'required' => true,
    'placeholder' => 'Enter title'
]
```

The form system automatically handles validation errors and displays them below each field.

### 5. Custom Styling

You can customize button and field styling by:

1. **Adding custom CSS classes:**
```php
'class' => 'btn-outline-success custom-button'
```

2. **Using Bootstrap utility classes:**
```php
'class' => 'btn-outline-info me-2 mt-1'
```

3. **Custom field styling:**
```php
[
    'type' => 'text',
    'name' => 'title',
    'label' => 'Title',
    'class' => 'form-control custom-input' // This will be applied to the input
]
```

### 6. JavaScript Integration

For custom button actions:

```javascript
function exportCourses() {
    // Custom export logic
    window.location.href = '/admin/courses/export';
}

function importCourses() {
    // Custom import logic
    showAjaxModal('/admin/courses/import', 'Import Courses');
}
```

---

## Troubleshooting

### Common Issues

1. **Buttons not appearing:** Check if `enabled` is set to `true` or not set at all
2. **Form fields not rendering:** Verify field type is supported and configuration is correct
3. **Filters not working:** Ensure filter configuration matches the field names in your database
4. **Validation errors:** Check that field names match your validation rules

### Debug Tips

1. Use `dd($filterConfig)` to debug filter configurations
2. Check browser console for JavaScript errors
3. Verify that all required parameters are provided
4. Ensure proper route definitions for custom buttons

---

This guide provides comprehensive coverage of CRUD customization in the Laravel admin panel. For additional help, refer to the existing module examples in the codebase or consult the Laravel documentation for form handling and validation.
