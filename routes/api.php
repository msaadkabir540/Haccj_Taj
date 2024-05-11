<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TemperatureCatergoryController;
use App\Http\Controllers\TrasabilityCatergoryController;
use App\Http\Controllers\ChecklistCatergoryController;
use App\Http\Controllers\OilTemperatureCatergoryController;
use App\Http\Controllers\CleaningCatergoryController;
use App\Models\CleaningCategory;
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
    // Route::post('/logout', [AuthController::class, 'logout']);

});



// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

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
Route::post('/get-temperature-data', [TemperatureCatergoryController::class, 'getAllTemperatureData']);
Route::get('/get-temperature-byid/{id}', [TemperatureCatergoryController::class, 'getTempById']);


Route::post('/update-employee', [EmployeeProfileController::class, 'updateEmployeeData']);
Route::delete('/delete-employee/{id}', [EmployeeProfileController::class, 'deleteEmployee']);
Route::post('/update-temperature', [TemperatureCatergoryController::class, 'updateTemperatureData']);
Route::delete('/delete-temperature/{id}', [TemperatureCatergoryController::class, 'deleteTemperature']);
Route::post('/update-cleaning', [CleaningCatergoryController::class, 'updateCleaningData']);
Route::delete('/delete-cleaning/{id}', [CleaningCatergoryController::class, 'deleteCleaning']);
Route::post('/update-oil-temperature', [OilTemperatureCatergoryController::class, 'updateOilTemperatureData']);
Route::delete('/delete-oil-temperature/{id}', [OilTemperatureCatergoryController::class, 'deleteOilTemperature']);
Route::post('/update-checklist', [ChecklistCatergoryController::class, 'updateChecklistData']);
Route::delete('/delete-checklist/{id}', [ChecklistCatergoryController::class, 'deleteChecklist']);
Route::post('/update-trasability', [TrasabilityCatergoryController::class, 'updateTrasabilityData']);
Route::delete('/delete-trasability/{id}', [TrasabilityCatergoryController::class, 'deleteTrasability']);

// Trasabilty
Route::post('/add-trasability', [TrasabilityCatergoryController::class, 'addTrasabilityData']);
Route::get('/get-trasability-data/{employeecode}', [TrasabilityCatergoryController::class, 'getAllTrasabilityData']);
Route::post('/add-trasability-productType', [TrasabilityCatergoryController::class, 'addTrasabilityProdType']);
Route::get('/get-trasability-productType', [TrasabilityCatergoryController::class, 'getAllTrasabilityProd']);
Route::get('/get-trasability-byid/{id}', [TrasabilityCatergoryController::class, 'getTrasabilityById']);


// Checklist
Route::post('/add-checklist', [ChecklistCatergoryController::class, 'addChecklistData']);
Route::get('get-checklist-data/{employeecode}', [ChecklistCatergoryController::class, 'getAllChecklistData']);

// Oil Temperature
Route::post('/add-oil-temperature', [OilTemperatureCatergoryController::class, 'addOilTemperatureData']);
Route::get('/get-oil-temperature-data/{employeecode}', [OilTemperatureCatergoryController::class, 'getAllOilTemperatureData']);
Route::post('/add-oil-temperature-machines', [OilTemperatureCatergoryController::class, 'addOilTempMachines']);

// Cleaning
Route::post('/add-cleaning', [CleaningCatergoryController::class, 'addCleaningData']);
Route::get('/get-cleaning-data/{employeecode}/{area}', [CleaningCatergoryController::class, 'getAllCleaningData']);
