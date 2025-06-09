<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\EventRecordsController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('/event-records')
    ->controller(EventRecordsController::class)
    ->group(function (): void {
        Route::get('/', 'index');
        Route::get('/export', 'export');
    });

Route::prefix('/institutions/{institution_id}/settings')
    ->controller(SettingController::class)
    ->whereUuid('institution_id')
    ->group(function (): void {
        Route::get('/', 'show');
        Route::put('/', 'update');
    });
