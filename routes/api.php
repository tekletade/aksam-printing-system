<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\AttendanceController;

// Protected API routes with API key
Route::prefix('v1')->middleware('api.key')->group(function () {

    // Printer Monitoring Endpoints
    Route::post('/printer/update', [AgentController::class, 'updatePrinter']);
    Route::post('/printer/bulk-update', [AgentController::class, 'bulkUpdate']);
    Route::get('/printer/config/{printer_id}', [AgentController::class, 'getPrinterConfig']);

    // Attendance Endpoints
    Route::post('/attendance/check-in', [AttendanceController::class, 'apiCheckIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'apiCheckOut']);
    Route::post('/attendance/biometric', [AttendanceController::class, 'biometricCheckIn']);
    Route::get('/attendance/qr/{employee_id}', [AttendanceController::class, 'generateQRCode']);

    // Sync endpoints
    Route::post('/sync/upload', [AgentController::class, 'uploadCache']);
    Route::get('/sync/download', [AgentController::class, 'downloadConfig']);
});
