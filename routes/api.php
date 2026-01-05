<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductImportController;

Route::post('/import-products', [ProductImportController::class, 'import']);