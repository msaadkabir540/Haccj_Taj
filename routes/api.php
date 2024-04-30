<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TemperatureCatergoryController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Validate Employee Code    
Route::get('validate-employee/{employeecode}', [EmployeeProfileController::class, 'validateEmployee']);
// Create Employee
Route::post('/add-employee', [EmployeeProfileController::class, 'createEmployee']);

// forgot and reset password
Route::post('/forgot-password', [SubscriberController::class, 'forgotPassword']);
Route::post('/reset-password', [SubscriberController::class, 'resetPassword']);

// Temperature APIs
Route::post('/add-temperature', [TemperatureCatergoryController::class, 'addTemperatureData']);
Route::get('/get-temperature-data', [TemperatureCatergoryController::class, 'getAllTemperatureData']);
Route::post('/add-equipment', [TemperatureCatergoryController::class, 'addEquipmentData']);
Route::get('/get-equipment-data', [TemperatureCatergoryController::class, 'getAllEquipmentData']);

