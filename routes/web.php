<?php

use App\Http\Controllers\Apps\PermissionManagementController;
use App\Http\Controllers\Apps\RoleManagementController;
use App\Http\Controllers\Apps\UserManagementController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentReturnController;
use App\Http\Controllers\WrappingAreaController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('user-management')->name('user-management.')->group(function () {
        // Users
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::match(['put', 'patch'], 'users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        // Roles
        Route::get('roles', [RoleManagementController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleManagementController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleManagementController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}', [RoleManagementController::class, 'show'])->name('roles.show');
        Route::get('roles/{role}/edit', [RoleManagementController::class, 'edit'])->name('roles.edit');
        Route::match(['put', 'patch'], 'roles/{role}', [RoleManagementController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleManagementController::class, 'destroy'])->name('roles.destroy');

        // Permissions
        Route::get('permissions', [PermissionManagementController::class, 'index'])->name('permissions.index');
        Route::get('permissions/create', [PermissionManagementController::class, 'create'])->name('permissions.create');
        Route::post('permissions', [PermissionManagementController::class, 'store'])->name('permissions.store');
        Route::get('permissions/{permission}', [PermissionManagementController::class, 'show'])->name('permissions.show');
        Route::get('permissions/{permission}/edit', [PermissionManagementController::class, 'edit'])->name('permissions.edit');
        Route::match(['put', 'patch'], 'permissions/{permission}', [PermissionManagementController::class, 'update'])->name('permissions.update');
        Route::delete('permissions/{permission}', [PermissionManagementController::class, 'destroy'])->name('permissions.destroy');
    });

    Route::prefix('catalog')->name('catalog.')->group(function () {
        // Catalogs
        Route::get('categories', [App\Http\Controllers\Apps\CatalogController::class, 'index'])->name('catalogs.index');
        Route::get('categories/create', [App\Http\Controllers\Apps\CatalogController::class, 'create'])->name('catalogs.create');
        Route::post('categories', [App\Http\Controllers\Apps\CatalogController::class, 'store'])->name('catalogs.store');
        Route::get('categories/{catalog}', [App\Http\Controllers\Apps\CatalogController::class, 'show'])->name('catalogs.show');
        Route::get('categories/{catalog}/edit', [App\Http\Controllers\Apps\CatalogController::class, 'edit'])->name('catalogs.edit');
        Route::match(['put', 'patch'], 'categories/{catalog}', [App\Http\Controllers\Apps\CatalogController::class, 'update'])->name('catalogs.update');
        Route::delete('categories/{catalog}', [App\Http\Controllers\Apps\CatalogController::class, 'destroy'])->name('catalogs.destroy');

        // sub_categories
        Route::get('sub_categories', [App\Http\Controllers\Apps\CategoryController::class, 'index'])->name('categories.index');
        Route::get('sub_categories/create', [App\Http\Controllers\Apps\CategoryController::class, 'create'])->name('categories.create');
        Route::post('sub_categories', [App\Http\Controllers\Apps\CategoryController::class, 'store'])->name('categories.store');
        Route::get('sub_categories/{category}', [App\Http\Controllers\Apps\CategoryController::class, 'show'])->name('categories.show');
        Route::get('sub_categories/{category}/edit', [App\Http\Controllers\Apps\CategoryController::class, 'edit'])->name('categories.edit');
        Route::match(['put', 'patch'], 'sub_categories/{category}', [App\Http\Controllers\Apps\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('sub_categories/{category}', [App\Http\Controllers\Apps\CategoryController::class, 'destroy'])->name('categories.destroy');

        // Products
        Route::get('products', [App\Http\Controllers\Apps\ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [App\Http\Controllers\Apps\ProductController::class, 'create'])->name('products.create');
        Route::post('products', [App\Http\Controllers\Apps\ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}', [App\Http\Controllers\Apps\ProductController::class, 'show'])->name('products.show');
        Route::get('products/{product}/edit', [App\Http\Controllers\Apps\ProductController::class, 'edit'])->name('products.edit');
        Route::match(['put', 'patch'], 'products/{product}', [App\Http\Controllers\Apps\ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [App\Http\Controllers\Apps\ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Customers Management
    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/verify-email', [CustomerController::class, 'verifyEmail'])->name('customers.verify-email');
    Route::post('customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
    Route::delete('customers/{id}/force-delete', [CustomerController::class, 'forceDelete'])->name('customers.force-delete');
    Route::get('customers-export', [CustomerController::class, 'export'])->name('customers.export');
    Route::post('customers-bulk-update-status', [CustomerController::class, 'bulkUpdateStatus'])->name('customers.bulk-update-status');
    Route::delete('customers-bulk-delete', [CustomerController::class, 'bulkDelete'])->name('customers.bulk-delete');

    // Orders Management
    Route::get('orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::post('orders', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
    Route::put('orders/{order}/status', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::put('orders/{order}/payment-status', [App\Http\Controllers\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::put('orders/{order}/tracking', [App\Http\Controllers\OrderController::class, 'addTrackingNumber'])->name('orders.add-tracking');
    Route::get('orders-export', [App\Http\Controllers\OrderController::class, 'export'])->name('orders.export');


    Route::prefix('blogs')->name('blogs.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('create', [BlogController::class, 'create'])->name('create');
        Route::post('/', [BlogController::class, 'store'])->name('store');
        Route::get('{blog}', [BlogController::class, 'show'])->name('show');
        Route::get('{blog}/edit', [BlogController::class, 'edit'])->name('edit');
        Route::put('{blog}', [BlogController::class, 'update'])->name('update');
        Route::delete('{blog}', [BlogController::class, 'destroy'])->name('destroy');
    });

    // Contact Messages Management
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('{contact}', [ContactController::class, 'show'])->name('show');
        Route::put('{contact}/status', [ContactController::class, 'updateStatus'])->name('update-status');
        Route::delete('{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });

    // Wrapping Areas Management
    Route::prefix('wrapping-areas')->name('wrapping-areas.')->group(function () {
        Route::get('/', [WrappingAreaController::class, 'index'])->name('index');
        Route::get('create', [WrappingAreaController::class, 'create'])->name('create');
        Route::post('/', [WrappingAreaController::class, 'store'])->name('store');
        Route::get('{wrappingArea}', [WrappingAreaController::class, 'show'])->name('show');
        Route::get('{wrappingArea}/edit', [WrappingAreaController::class, 'edit'])->name('edit');
        Route::put('{wrappingArea}', [WrappingAreaController::class, 'update'])->name('update');
        Route::delete('{wrappingArea}', [WrappingAreaController::class, 'destroy'])->name('destroy');
        Route::post('{wrappingArea}/toggle-active', [WrappingAreaController::class, 'toggleActive'])->name('toggle-active');
    });

    // Pages Management
    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/', [PagesController::class, 'index'])->name('index');
        Route::post('/', [PagesController::class, 'store'])->name('store');
        Route::get('{page}', [PagesController::class, 'show'])->name('show');
        Route::put('{page}', [PagesController::class, 'update'])->name('update');
        Route::delete('{page}', [PagesController::class, 'destroy'])->name('destroy');

        // SEO Management
        Route::get('{page}/seo', [PagesController::class, 'seoEdit'])->name('seo-edit');
        Route::put('{page}/seo', [PagesController::class, 'seoUpdate'])->name('seo-update');

        // Content Management
        Route::get('{page}/content', [PagesController::class, 'contentEdit'])->name('content-edit');
        Route::put('{page}/content', [PagesController::class, 'contentUpdate'])->name('content-update');
    });
});

// Payment return routes (no auth middleware - accessible for guests)
Route::get('/payment/ngenius/return', [PaymentReturnController::class, 'ngeniusReturn'])->name('payment.ngenius.return');


Route::get('/error', function () {
    abort(500);
});

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);

require __DIR__ . '/auth.php';
