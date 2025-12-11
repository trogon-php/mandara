# Referral Reports Implementation

## Overview
This document describes the implementation of two referral reports for the admin panel:
1. **Referral Report** - Detailed listing of all referrals
2. **Top Referrers** - Summary report showing top referrers by total referrals and reward points

## Files Created

### Backend Services
- **`app/Services/Referrals/ReferralService.php`**
  - Handles business logic for referral reports
  - Provides filtered data with search capabilities
  - Implements filter configuration for referrer and date range
  - Includes method to get top referrers with statistics

### Controllers
- **`app/Http/Controllers/Admin/ReferralReportController.php`**
  - Manages referral report listing
  - Provides edit and delete functionality for referrals
  - Supports filtering by referrer and date range

- **`app/Http/Controllers/Admin/TopReferrersController.php`**
  - Displays top referrers with statistics
  - Shows number of referred users and total reward points
  - Provides link to view referred users (redirects to referral report with filter)

### Request Validation
- **`app/Http/Requests/Referrals/UpdateReferralRequest.php`**
  - Validates referral update requests
  - Validates status (pending, completed, rewarded, cancelled)
  - Validates reward_coins (integer, min: 0)

### Views

#### Referral Report Views
- **`resources/views/admin/reports/referrals/index.blade.php`**
  - Main referral report page
  - Includes filters for referrer and date range
  - Displays link to Top Referrers report

- **`resources/views/admin/reports/referrals/index-table.blade.php`**
  - Table body for referral report
  - Displays: Referrer, Referred User, Referral Code, Reward Points, Status, Date Time
  - Provides Edit and Delete actions

- **`resources/views/admin/reports/referrals/edit.blade.php`**
  - Edit form for referral
  - Allows updating status and reward coins
  - Shows read-only information for referrer, referred user, and referral code

#### Top Referrers Views
- **`resources/views/admin/reports/top-referrers/index.blade.php`**
  - Main top referrers report page
  - Includes date range filters
  - Displays link to Referral Report

- **`resources/views/admin/reports/top-referrers/index-table.blade.php`**
  - Table body for top referrers report
  - Displays: Referrer Name, Contact Info, No. of Referred Users, Total Reward Points
  - Provides "View Referred Users" button that redirects to referral report with filter

- **`resources/views/admin/reports/top-referrers/filters.blade.php`**
  - Custom filter form for top referrers
  - Includes search and date range filters

## Configuration Updates

### Routes (`routes/admin.php`)
Added two new route groups:

```php
// Referral Report routes
Route::prefix('reports/referrals')->name('reports.referrals.')->controller(ReferralReportController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{referral}/edit', 'edit')->name('edit');
    Route::put('/{referral}', 'update')->name('update');
    Route::delete('/{referral}', 'destroy')->name('destroy');
});

// Top Referrers Report routes
Route::prefix('reports/top-referrers')->name('reports.top-referrers.')->controller(TopReferrersController::class)->group(function () {
    Route::get('/', 'index')->name('index');
});
```

### Admin Menu (`config/admin_menu.php`)
Added two new menu items under Reports section:
- Referral Report
- Top Referrers

### Permissions (`config/permissions.php`)
Added permissions for both reports:
```php
'reports' => [
    'index' => [Role::ADMIN],
    'referrals' => [Role::ADMIN],
    'top-referrers' => [Role::ADMIN],
    // ... other report permissions
],
```

## Features Implemented

### Referral Report
✅ Displays all referrals with detailed information
✅ Shows Referrer details (name, email, phone)
✅ Shows Referred User details (name, email) or "Not Used Yet"
✅ Displays Referral Code
✅ Shows Reward Points with badge
✅ Displays Status with color-coded badges (pending, completed, rewarded, cancelled)
✅ Shows Date and Time of referral creation
✅ Edit functionality (update status and reward coins)
✅ Delete functionality
✅ Filter by Referrer (dropdown)
✅ Filter by Date Range (from and to)
✅ Search functionality (referrer name, email, phone, referred user name, email, referral code)
✅ Link to Top Referrers report

### Top Referrers Report
✅ Displays top referrers ranked by number of referrals
✅ Shows Referrer Name
✅ Shows Contact Information (email, phone)
✅ Displays No. of Referred Users (badge)
✅ Displays Total Reward Points (badge)
✅ "View Referred Users" button that redirects to Referral Report with referrer filter applied
✅ Search functionality (name, email, phone)
✅ Filter by Date Range (from and to)
✅ Link to Referral Report

## Database Schema Reference

The reports use the existing `referrals` table with the following structure:
```sql
CREATE TABLE `referrals` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `referrer_id` int unsigned NOT NULL COMMENT 'User who referred (FK → users.id)',
  `referred_id` int unsigned DEFAULT NULL COMMENT 'User who joined via referral (FK → users.id)',
  `referral_code` varchar(50) NOT NULL COMMENT 'Unique code used for referral',
  `status` enum('pending','completed','rewarded','cancelled') NOT NULL DEFAULT 'pending',
  `reward_coins` int DEFAULT '0' COMMENT 'Coins assigned for this referral',
  `reward_transaction_id` int unsigned DEFAULT NULL COMMENT 'FK → wallet_transactions.id',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_referrer` (`referrer_id`),
  KEY `idx_referred` (`referred_id`)
);
```

## Access URLs

After implementation, the reports can be accessed at:
- **Referral Report**: `/admin/reports/referrals`
- **Top Referrers**: `/admin/reports/top-referrers`

## Navigation

Both reports are accessible from:
1. Admin sidebar menu under **Reports** section
2. Cross-linking buttons on each report page
3. "View Referred Users" button on Top Referrers report (redirects to Referral Report with filter)

## Best Practices Followed

✅ Used existing BaseService pattern for business logic
✅ Controllers use services instead of models directly (as per project rules)
✅ No database migrations or schema changes (as per project rules)
✅ Followed existing naming conventions and code structure
✅ Used existing CRUD layout patterns
✅ Implemented proper validation
✅ Used existing filter and search configurations
✅ Followed Laravel best practices
✅ No linting errors

## Notes

- The implementation follows the existing codebase patterns and structure
- All business logic is contained in the service layer
- Controllers are kept thin and only handle request/response
- Views use existing blade components and layouts
- Permissions are configured to allow only Admin access
- The reports integrate seamlessly with the existing admin panel



