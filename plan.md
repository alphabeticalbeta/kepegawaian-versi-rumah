# Plan to Fix Route Naming Issue

## Current Issue

The error message "Route [backend.admin-univ-usulan.unitkerja.store] not defined" indicates a mismatch between the route names used in the code and the actual route definitions in the routes file.

### Current Route References in Controller and Views

In the UnitKerjaController and blade templates, routes are referenced with names like:
- `backend.admin-univ-usulan.unitkerja.index`
- `backend.admin-univ-usulan.unitkerja.store`
- `backend.admin-univ-usulan.unitkerja.update`
- `backend.admin-univ-usulan.unitkerja.edit`
- `backend.admin-univ-usulan.unitkerja.destroy`

### Current Route Definitions in routes/backend.php

However, in the routes/backend.php file, the routes are defined with different names:
- `admin-universitas-usulan.dashboard-universitas-usulan-unitkerja`
- `admin-universitas-usulan.dashboard-universitas-usulan-unitkerja-create`
- `admin-universitas-usulan.dashboard-universitas-usulan-unitkerja-store`
- `admin-universitas-usulan.dashboard-universitas-usulan-unitkerja-edit`
- `admin-universitas-usulan.dashboard-universitas-usulan-unitkerja-destroy`

## Solution: Update Route Definitions

The simplest solution is to update the routes in backend.php to match the naming convention used in the controller and views. This requires fewer changes than updating all references in the controller and views.

### Changes Needed in routes/backend.php

1. Update the route group prefix and name:
   ```php
   Route::prefix('admin-univ-usulan')->name('backend.admin-univ-usulan.')->group(function () {
       // Routes here
   });
   ```

2. Update the individual route names to follow the RESTful convention:
   ```php
   Route::get('/dashboard', [AdminUnivUsulanDashboardController::class, 'index'])->name('dashboard');
   Route::get('/unitkerja', [UnitKerjaController::class, 'index'])->name('unitkerja.index');
   Route::get('/unitkerja/create', [UnitKerjaController::class, 'create'])->name('unitkerja.create');
   Route::post('/unitkerja', [UnitKerjaController::class, 'store'])->name('unitkerja.store');
   Route::get('/unitkerja/{unitKerja}/edit', [UnitKerjaController::class, 'edit'])->name('unitkerja.edit');
   Route::put('/unitkerja/{unitKerja}', [UnitKerjaController::class, 'update'])->name('unitkerja.update');
   Route::delete('/unitkerja/{unitKerja}', [UnitKerjaController::class, 'destroy'])->name('unitkerja.destroy');
   ```

This approach follows Laravel's RESTful routing conventions and will match the route names used in the controller and views.

# Plan to Update UnitKerja Model

## Current Issue

The UnitKerja model is currently missing the `$fillable` property, which is required for mass assignment in Laravel. Without this property, the `create()` and `update()` methods in the controller won't work properly.

## Current UnitKerja Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    //
}
```

## Database Structure (from migration)

The unit_kerjas table has the following columns:
- id (auto-incrementing primary key)
- nama (string, unique)
- keterangan (text, nullable)
- timestamps (created_at, updated_at)

## Solution: Update UnitKerja Model

Add the `$fillable` property to the UnitKerja model to specify which attributes can be mass-assigned:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'keterangan',
    ];
}
```

This change will allow the `create()` and `update()` methods in the controller to work properly with mass assignment.
