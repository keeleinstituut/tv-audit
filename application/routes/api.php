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
use App\Http\Controllers\EventRecord2Controller;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('/event-records')
    ->controller(EventRecordsController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('audit.event_records.index');
        Route::get('/export', 'export')->name('audit.event_records.export');
    });

Route::prefix('/event-records2')
    ->controller(EventRecord2Controller::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('audit.event_records2.index');
    });

Route::prefix('/institutions/{institution_id}/settings')
    ->controller(SettingController::class)
    ->whereUuid('institution_id')
    ->group(function (): void {
        Route::get('/', 'show')->name('audit.settings.show');
        Route::put('/', 'update')->name('audit.settings.update');
    });
