<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api-docs/admin.yml', function () {
    return response()->file(resource_path('views/docs-api/admin.yml'));
});

// Route untuk serve seluruh file YAML di folder docs-api (langsung dari root project)
Route::get('/api-docs/{filename}', function ($filename) {
    $path = base_path('docs-api/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
})->where('filename', '.*\\.ya?ml');
