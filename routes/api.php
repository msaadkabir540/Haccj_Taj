<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TemperatureCatergoryController;
use App\Http\Controllers\TrasabilityCatergoryController;
use App\Http\Controllers\ChecklistCatergoryController;
use App\Models\Equipments;

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
Route::get('get-all-employee', [EmployeeProfileController::class, 'getAllEmployeeData']);
// Create Employee
Route::post('/add-employee', [EmployeeProfileController::class, 'createEmployee']);

// forgot and reset password
Route::post('/forgot-password', [SubscriberController::class, 'forgotPassword']);
Route::post('/reset-password', [SubscriberController::class, 'resetPassword']);

// Equipments
Route::post('/add-equipment', [TemperatureCatergoryController::class, 'addEquipmentData']);
Route::get('/get-equipment-data', [TemperatureCatergoryController::class, 'getAllEquipmentData']);

// Temperature APIs
Route::post('/add-temperature', [TemperatureCatergoryController::class, 'addTemperatureData']);
Route::get('/get-temperature-data/{employeecode}', [TemperatureCatergoryController::class, 'getAllTemperatureData']);

// Trasabilty
Route::post('/add-trasability', [TrasabilityCatergoryController::class, 'addTrasabilityData']);
Route::get('/get-trasability-data/{employeecode}', [TrasabilityCatergoryController::class, 'getAllTrasabilityData']);
Route::post('/add-trasability-productType', [TrasabilityCatergoryController::class, 'addTrasabilityProdType']);
Route::get('/get-trasability-productType', [TrasabilityCatergoryController::class, 'getAllTrasabilityProd']);

// Checklist
Route::post('/add-checklist', [ChecklistCatergoryController::class, 'addChecklistData']);
Route::get('get-checklist-data/{employeecode}', [ChecklistCatergoryController::class, 'getAllChecklistData']);