<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::controller(ProjectController::class)->group(function () {
    Route::post('/projects', 'store');
    Route::put('/projects', 'update');
    Route::get('/projects', 'index');
    Route::post('/projects/pinned', 'pinnedProject');
    Route::get('/projects/{slug}', 'getProject');
    Route::get('/count/projects', 'countProject');
});


Route::controller(MemberController::class)->group(function () {
    Route::post('/members', 'store');
    Route::put('/members', 'update');
    Route::get('/members', 'index');
});


Route::controller(TaskController::class)->group(function () {
    Route::post('/tasks', 'createTask');
    Route::post('/tasks/not_started_to_pending', 'TaskToNotStartedToPending');
    Route::post('/tasks/not_started_to_complated', 'TaskToNotStartedToComplated');
    Route::post('/tasks/pending_to_complated', 'TaskToPendingToComplated');
    Route::post('/tasks/pending_to_not_started', 'TaskToPendingToNotStarted');
    Route::post('/tasks/complated_to_pending', 'TaskToComplatedToPending');
    Route::post('/tasks/complated_to_not_started', 'TaskToComplatedToNotStarted');
});
