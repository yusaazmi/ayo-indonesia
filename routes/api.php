<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\MatchScheduleController;
use App\Http\Controllers\Api\MatchResultController;

// Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum', 'role:admin'], 'prefix' => 'admin'], function () {
    // user management
    Route::apiResource('users', UserController::class);
    Route::put('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
    // role management
    Route::apiResource('roles', RoleController::class);
    // team management
    Route::apiResource('teams', TeamController::class);
    Route::post('teams/{id}/upload-logo', [TeamController::class, 'uploadLogo'])->name('teams.uploadLogo');
    Route::put('teams/{id}/restore', [TeamController::class, 'restore'])->name('teams.restore');
    Route::put('teams/{id}/force-delete', [TeamController::class, 'forceDelete'])->name('teams.forceDelete');
    // player management
    Route::apiResource('players', PlayerController::class);
    Route::put('players/{id}/restore', [PlayerController::class, 'restore'])->name('players.restore');
    Route::delete('players/{id}/force-delete', [PlayerController::class, 'forceDelete'])->name('players.forceDelete');
    // match schedule management
    Route::apiResource('match-schedules', MatchScheduleController::class);
    Route::put('match-schedules/{id}/restore', [MatchScheduleController::class, 'restore'])->name('match-schedules.restore');
    Route::delete('match-schedules/{id}/force-delete', [MatchScheduleController::class, 'forceDelete'])->name('match-schedules.forceDelete');
    // match result management
    Route::apiResource('match-results', MatchResultController::class);
    Route::put('match-results/{id}/restore', [MatchResultController::class, 'restore'])->name('match-results.restore');
    Route::delete('match-results/{id}/force-delete', [MatchResultController::class, 'forceDelete'])->name('match-results.forceDelete');
});
