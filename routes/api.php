<?php

use App\Http\Controllers\API\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventCategoryController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ExportController;
// Route::get('/unauthenticated', function() {
//     return response()->json(['message' => 'Unauthenticated.'], 401);
// })->name('login');
// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});

// event categories
Route::get('/categories', [EventCategoryController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/categories', [EventCategoryController::class, 'store']);
    Route::put('/categories/{eventCategory}', [EventCategoryController::class, 'update']);
    Route::delete('/categories/{eventCategory}', [EventCategoryController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/events', [EventController::class, 'adminIndex']);
    Route::get('/dashboard', [DashboardController::class, 'admin']);
});


Route::middleware(['auth:sanctum', 'role:organizer'])->prefix('organizer')->group(function () {
    Route::get('/events', [EventController::class, 'myEvents']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
    Route::post('/events/{event}/check-in', [AttendanceController::class, 'checkIn']);
    Route::get('/events/{event}/attendances', [AttendanceController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'organizer']);
    Route::get('/events/{event}/analytics', [DashboardController::class, 'eventAnalytics']);
    Route::get('/events/{event}/registrations', [RegistrationController::class, 'index']);
    Route::get('/events/{event}/export/registrations/pdf',[ExportController::class, 'registrationsPdf']);
    Route::get('/events/{event}/export/registrations/xlsx',[ExportController::class, 'registrationsXlsx']);
    Route::get('/events/{event}/export/payments/pdf',[ExportController::class, 'paymentsPdf']);
    Route::get('/events/{event}/export/payments/xlsx',[ExportController::class, 'paymentsXlsx']);
});

Route::get('/events/{slug}', [EventController::class, 'show']);
Route::post('/events/{slug}/register', [RegistrationController::class, 'store']);
Route::get('/payments/{transactionId}', [PaymentController::class, 'show']);
Route::post('/payments/{transactionId}/simulate-pay', [PaymentController::class, 'simulatePay']);
Route::get('/tickets/{ticketCode}/download', [ExportController::class, 'downloadTicket']);
