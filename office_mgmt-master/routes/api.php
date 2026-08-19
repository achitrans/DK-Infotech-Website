<?php

use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\InquiryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('rfid/scan', [\App\Http\Controllers\RfidController::class, 'scan']);

Route::post('login',[AuthController::class,'login'])->middleware(['throttle:10,10']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('getUser', [AuthController::class, 'getAuthUser']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logoutAll', [AuthController::class, 'logoutAll']);
    Route::post('geo-location/log', [\App\Http\Controllers\API\UserGeoLocationLogController::class, 'store']);
    
    // Attendance Routes
    Route::post('attendance/mark-in', [AttendanceController::class, 'markIn']);
    Route::post('attendance/mark-out', [AttendanceController::class, 'markOut']);
    Route::get('attendance/status', [AttendanceController::class, 'status']);
});

Route::post('inquiry/int/', [InquiryController::class, 'create']);

Route::post('/attendance/push', [AttendanceController::class, 'store']);

