<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

 Route::get('/teams-logo/{filename}', function ($filename) {
        $path = storage_path('app/public/teams/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path);
    });