<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::view('/','app');

// Login (guest access)
Route::post('/login', [AuthController::class, 'login'])->middleware('web');

// Protected routes with Sanctum & session auth
Route::middleware(['web', EnsureFrontendRequestsAreStateful::class, 'auth:sanctum'])->group(function () {
    // Auth-related
    Route::get('/user', [AuthController::class, 'currentUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Resources
    Route::resource('/users', UserController::class);
    Route::resource('/permissions', PermissionController::class);

    // Permissions-specific
    Route::get('/roles-permissions', [PermissionController::class, 'rolesWithPermissions']);
    Route::post('/roles/{role}/permissions', [PermissionController::class, 'updateRolePermissions']);
});