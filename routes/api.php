<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ChunkedImageUploadController;

Route::post('/import-products', [ProductImportController::class, 'import']);
Route::post('/upload-image-chunk', [ChunkedImageUploadController::class, 'upload']);