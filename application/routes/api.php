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
use Illuminate\Support\Facades\Route;

Route::prefix('/event-records')
    ->controller(EventRecordsController::class)
    ->group(function (): void {
        Route::get('/', 'index');
        Route::get('/export', 'export');
    });
