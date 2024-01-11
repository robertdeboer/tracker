<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProjectLog;
use App\Http\Controllers\ProjectSummary;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get(
    '/',
    [AdminController::class, 'welcome']
)->name('welcome');

Route::middleware(
    [
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ]
)->prefix(config('fortify.prefix'))->group(
    function () {
        Route::get(
            '/dashboard',
            [AdminController::class, 'dashboard']
        )->name('dashboard');
        Route::middleware(['can:View Orders'])
             ->get(
                 '/orders',
                 [AdminController::class, 'orders']
             )
             ->name('orders');
        Route::middleware(['can:View Project'])
             ->get(
                 '/project/{id}',
                 [AdminController::class, 'project']
             )
             ->where(['id' => '[0-9]+'])
             ->name('project');
        Route::get(
            '/project/{id}/log',
            [ProjectLog::class, 'get']
        )->where(['id' => '[0-9]+'])
             ->name('project.log');
        Route::get(
            '/project/{id}/summary/{start}/{end}',
            [ProjectSummary::class, 'get']
        )->name('project.summary');
        Route::post(
            '/project/summary',
            [ProjectSummary::class, 'send']
        )->name('project.summary.send');
        Route::middleware(['can:Manage System'])
             ->get(
                 '/users',
                 [AdminController::class, 'users']
             )->name('users');
    }
);
