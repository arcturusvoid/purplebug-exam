<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user', function (Request $request) {
        return User::with('role')->find($request->user()->id);
    });

    Route::get('/dashboard', DashboardController::class);

    Route::middleware(['admin'])->group(function () {
        Route::apiResource('roles', RoleController::class)->except(['create', 'edit']);
        Route::apiResource('users', UserController::class)->except(['create', 'edit', 'show']);
       
    });
    
    Route::apiResource('expense-categories', ExpenseCategoryController::class)->except(['create', 'edit', 'show']);
    Route::apiResource('expense', ExpenseController::class)->except(['create', 'edit', 'show']);

});