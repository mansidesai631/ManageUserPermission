<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::post('login', [AuthController::class,'login'])->middleware(['web']);

Route::middleware([EnsureFrontendRequestsAreStateful::class, 'auth:sanctum'])->group(function(){
  Route::get('user', [AuthController::class,'currentUser']);

  Route::apiResource('users', UserController::class);
  Route::apiResource('permissions', PermissionController::class);

  Route::get('/roles-permissions', [PermissionController::class, 'rolesWithPermissions']);
  Route::post('/roles/{role}/permissions', [PermissionController::class, 'updateRolePermissions']);

  Route::post('logout', [AuthController::class,'logout']);
});


