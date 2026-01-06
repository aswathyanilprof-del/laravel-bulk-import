<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChunkedImageUploadController;

Route::get('/test-image-upload', function () {
    return view('test-image-upload');
});

Route::post('/test-image-upload', [ChunkedImageUploadController::class, 'upload']);

Route::get('/upload', function () {
    return <<<HTML
<!doctype html>
<html>
<body>
  <h2>CSV Upload Test</h2>
  <form method="POST" action="/api/import-products" enctype="multipart/form-data">
    <input type="file" name="file" />
    <button type="submit">Upload</button>
  </form>
</body>
</html>
HTML;
});
