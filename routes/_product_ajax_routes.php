<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Apps\ProductController;

// Add this to your existing web.php or api.php routes

// Products Ajax API endpoint
Route::get('/catalog/products/ajax', [ProductController::class, 'index'])->name('catalog.products.ajax');
