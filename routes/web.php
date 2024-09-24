<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmokeController;

Route::get('/', function () {
    return view('smoke');
});

Route::post('/remove-smoke', [SmokeController::class, 'removeSmoke']);
