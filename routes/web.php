<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()
        ->view('app')
        ->header('X-Frame-Options', 'ALLOWALL')
        ->header('Content-Security-Policy', "frame-ancestors *");
});

Route::get('/widget', function () {
    return response()
        ->view('app')
        ->header('X-Frame-Options', 'ALLOWALL')
        ->header('Content-Security-Policy', "frame-ancestors *");
});
