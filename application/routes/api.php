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

use App\Http\Controllers\EventRecordController;
use App\Http\Controllers\EventRecordsControllerOld;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('/event-records-old')
    ->controller(EventRecordsControllerOld::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('audit.event_records_old.index');
        Route::get('/export', 'export')->name('audit.event_records_old.export');
    });

Route::prefix('/event-records')
    ->controller(EventRecordController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('audit.event_records.index');
        Route::get('/export', 'export')->name('audit.event_records.export');
        Route::get('/actions', 'indexActions')->name('audit.event_records.indexActions');
    });

Route::prefix('/institutions/{institution_id}/settings')
    ->controller(SettingController::class)
    ->whereUuid('institution_id')
    ->group(function (): void {
        Route::get('/', 'show')->name('audit.settings.show');
        Route::put('/', 'update')->name('audit.settings.update');
    });
