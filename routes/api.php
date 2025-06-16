<?php

use App\Collection\UserCollection;
use App\Http\Controllers\Api\Auth\ApiAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

require __DIR__ . '/api/auth.php';
require __DIR__ . '/api/admin.php';