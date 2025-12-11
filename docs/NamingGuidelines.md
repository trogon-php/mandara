## Global Naming Guidelines

This document defines naming conventions for tables, models, controllers, repositories, services, and related files.
Following these rules ensures consistency, clarity, and Laravel best practices across all projects.

### Database

- Tables → plural, snake_case
- Columns → snake_case
- Pivot tables → singular_singular (alphabetical order)

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
-----
### Models

- Models → singular, PascalCase
- Corresponds to one table.
- Uses singular form, even if the table is plural.

**- Examples:**

```
User
Category
Review
Testimonial
FeedCategory
CourseUnit
```
---
### Controllers

- Controllers → singular + Controller suffix
- Each controller manages a resource type (e.g., CategoryController manages all categories).

**- Examples:**

```
UserController
CategoryController
ReviewController
TestimonialController
FeedCategoryController
CourseUnitController
```
---
### Repositories

- Repositories → singular + Repository suffix
- Interfaces follow same pattern with Interface suffix.
- Folder name → plural.

**- Examples:**
```
app/Repositories/Users/UserRepository.php
app/Repositories/Users/UserRepositoryInterface.php

app/Repositories/Categories/CategoryRepository.php
app/Repositories/Categories/CategoryRepositoryInterface.php
```
---
### Services

- Services → singular + Service suffix
- Business logic layer.
- Folder name → plural.

**- Examples:**

```
app/Services/Users/UserService.php
app/Services/Categories/CategoryService.php
app/Services/Reviews/ReviewService.php
```
---
### Views

- Views → plural, snake_case folder names
- Blade files → snake_case.blade.php

**- Examples:**

```
resources/views/admin/users/index.blade.php
resources/views/admin/categories/form.blade.php
resources/views/admin/reviews/show.blade.php
```
---
### Routes

- Web routes → plural (collection-based URIs)
- API routes → plural, versioned if needed.

**- Examples:**
```php
Route::resource('users', UserController::class);
Route::resource('categories', CategoryController::class);
Route::resource('reviews', ReviewController::class);

Route::prefix('v1')->group(function () {
    Route::apiResource('feed-categories', FeedCategoryController::class);
});
```
---
### Migrations

- Migrations → use plural table names
- Use create_ or add_ prefixes.

**- Examples:**
```
2025_09_11_000000_create_users_table.php
2025_09_11_010000_create_categories_table.php
2025_09_11_020000_add_status_to_reviews_table.php
```
---
### Summary:

- Plural → Tables, Views, Routes.
- Singular → Models, Controllers, Repositories, Services.
- CamelCase → PHP class names.
- snake_case → DB, Views, Files.