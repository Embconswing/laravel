<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Api\TvAppointmentController;
use App\Http\Controllers\AnnouncementController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/ping', function () {
    return response()->json(['api' => 'ok']);
});

//Route::get('/announcements', [AppointmentController::class, 'getAnnouncements']);

Route::get('/tv/appointments', [TvAppointmentController::class, 'index']);


Route::get('/announcements', [AnnouncementController::class, 'api']);
