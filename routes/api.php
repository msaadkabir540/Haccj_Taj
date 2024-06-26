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
use App\Http\Controllers\ProductsCatergoryController;
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

Route::middleware('auth:sanctum')->get('/api', function (Request $request) {
    // return $request->user();
    // Route::post('/logout', [AuthController::class, 'logout']);

});



// Route::middleware('auth:sanctum')->group(function () {
//     // Route::post('/get-temperature-data', [TemperatureCatergoryController::class, 'getAllTemperatureData']);

//     // Validate Employee Code    
//     Route::get('validate-employee/{employeecode}', [EmployeeProfileController::class, 'validateEmployee']);
//     Route::post('get-all-employee', [EmployeeProfileController::class, 'getAllEmployeeData']);
//     // Create Employee
//     Route::post('/add-employee', [EmployeeProfileController::class, 'createEmployee']);


//         // Equipments
//     Route::post('/add-equipment', [TemperatureCatergoryController::class, 'addEquipmentData']);
//     Route::get('/get-equipment-data', [TemperatureCatergoryController::class, 'getAllEquipmentData']);

//     // Temperature APIs
//     Route::post('/add-temperature', [TemperatureCatergoryController::class, 'addTemperatureData']);
//     Route::post('/get-temperature-data', [TemperatureCatergoryController::class, 'getAllTemperatureData']);
//     // Route::post('/get-temperature-data', [TemperatureCatergoryController::class, 'getAllTemperatureData']);
//     Route::get('/get-temperature-byid/{id}', [TemperatureCatergoryController::class, 'getTempById']);


//     Route::post('/update-employee', [EmployeeProfileController::class, 'updateEmployeeData']);
//     Route::delete('/delete-employee/{id}', [EmployeeProfileController::class, 'deleteEmployee']);
//     Route::post('/update-temperature', [TemperatureCatergoryController::class, 'updateTemperatureData']);
//     Route::delete('/delete-temperature/{id}', [TemperatureCatergoryController::class, 'deleteTemperature']);
//     Route::post('/update-cleaning', [CleaningCatergoryController::class, 'updateCleaningData']);
//     Route::delete('/delete-cleaning/{id}', [CleaningCatergoryController::class, 'deleteCleaning']);
//     Route::post('/update-oil-temperature', [OilTemperatureCatergoryController::class, 'updateOilTemperatureData']);
//     Route::delete('/delete-oil-temperature/{id}', [OilTemperatureCatergoryController::class, 'deleteOilTemperature']);
//     Route::post('/update-checklist', [ChecklistCatergoryController::class, 'updateChecklistData']);
//     Route::delete('/delete-checklist/{id}', [ChecklistCatergoryController::class, 'deleteChecklist']);
//     Route::post('/update-trasability', [TrasabilityCatergoryController::class, 'updateTrasabilityData']);
//     Route::delete('/delete-trasability/{id}', [TrasabilityCatergoryController::class, 'deleteTrasability']);
//     Route::delete('/delete-trasability-product/{id}', [TrasabilityCatergoryController::class, 'deleteTrasabilityProd']);
//     Route::delete('/delete-temperature-equipment/{id}', [TemperatureCatergoryController::class, 'deleteEquipment']);
//     Route::delete('/delete-oil-temperature-machine/{id}', [OilTemperatureCatergoryController::class, 'deleteOilTemperatureMachine']);
//     Route::get('get-all-employee-get', [EmployeeProfileController::class, 'getAllEmployeeDataGet']);

//     // Trasabilty
//     Route::post('/add-trasability', [TrasabilityCatergoryController::class, 'addTrasabilityData']);
//     Route::post('/get-trasability-data', [TrasabilityCatergoryController::class, 'getAllTrasabilityData']);
//     Route::post('/add-trasability-productType', [TrasabilityCatergoryController::class, 'addTrasabilityProdType']);
//     Route::get('/get-trasability-productType', [TrasabilityCatergoryController::class, 'getAllTrasabilityProd']);
//     Route::get('/get-trasability-byid/{id}', [TrasabilityCatergoryController::class, 'getTrasabilityById']);


//     // Checklist
//     Route::post('/add-checklist', [ChecklistCatergoryController::class, 'addChecklistData']);
//     Route::post('get-checklist-data', [ChecklistCatergoryController::class, 'getAllChecklistData']);

//     // Oil Temperature
//     Route::post('/add-oil-temperature', [OilTemperatureCatergoryController::class, 'addOilTemperatureData']);
//     Route::post('/get-oil-temperature-data', [OilTemperatureCatergoryController::class, 'getAllOilTemperatureData']);
//     Route::post('/add-oil-temperature-machines', [OilTemperatureCatergoryController::class, 'addOilTempMachines']);

//     // Cleaning
//     Route::post('/add-cleaning', [CleaningCatergoryController::class, 'addCleaningData']);
//     Route::post('/get-cleaning-data', [CleaningCatergoryController::class, 'getAllCleaningData']);


// });




// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// // Validate Employee Code    
Route::get('validate-employee/{employeecode}', [EmployeeProfileController::class, 'validateEmployee']);
Route::post('get-all-employee', [EmployeeProfileController::class, 'getAllEmployeeData']);
// Create Employee
Route::post('/add-employee', [EmployeeProfileController::class, 'createEmployee']);

// forgot and reset password
Route::post('/forgot-password', [SubscriberController::class, 'forgotPassword']);
Route::post('/reset-password', [SubscriberController::class, 'resetPassword']);

// // Equipments
Route::post('/add-equipment', [TemperatureCatergoryController::class, 'addEquipmentData']);
Route::get('/get-equipment-data', [TemperatureCatergoryController::class, 'getAllEquipmentData']);

// // Temperature APIs
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
Route::delete('/delete-trasability-product/{id}', [TrasabilityCatergoryController::class, 'deleteTrasabilityProd']);
Route::delete('/delete-temperature-equipment/{id}', [TemperatureCatergoryController::class, 'deleteEquipment']);
Route::delete('/delete-oil-temperature-machine/{id}', [OilTemperatureCatergoryController::class, 'deleteOilTemperatureMachine']);
Route::get('get-all-employee-get', [EmployeeProfileController::class, 'getAllEmployeeDataGet']);

// // Trasabilty
Route::post('/add-trasability', [TrasabilityCatergoryController::class, 'addTrasabilityData']);
Route::post('/get-trasability-data', [TrasabilityCatergoryController::class, 'getAllTrasabilityData']);
Route::post('/add-trasability-productType', [TrasabilityCatergoryController::class, 'addTrasabilityProdType']);
Route::get('/get-trasability-productType', [TrasabilityCatergoryController::class, 'getAllTrasabilityProd']);
Route::post('/add-trasability-productName', [TrasabilityCatergoryController::class, 'addTrasabilityProdName']);
Route::get('/get-trasability-productName', [TrasabilityCatergoryController::class, 'getAllTrasabilityProdName']);
Route::delete('/delete-trasability-productName/{id}', [TrasabilityCatergoryController::class, 'deleteTrasabilityProdName']);
Route::get('/get-trasability-byid/{id}', [TrasabilityCatergoryController::class, 'getTrasabilityById']);

// Product Category
Route::post('/add-ProductsData', [ProductsCatergoryController::class, 'addProductsData']);
Route::post('/get-productsData', [ProductsCatergoryController::class, 'getAllProductsData']);
Route::post('/update-productsData', [ProductsCatergoryController::class, 'updateProductData']);
Route::delete('/delete-productsData/{id}', [ProductsCatergoryController::class, 'deleteProduct']);
Route::post('/add-products-productType', [ProductsCatergoryController::class, 'addProductsProdType']);
Route::get('/get-products-productType', [ProductsCatergoryController::class, 'getAllProductsProdType']);
Route::delete('/delete-products-product/{id}', [ProductsCatergoryController::class, 'deleteProductsProdType']);
Route::post('/add-products-productName', [ProductsCatergoryController::class, 'addProductProdName']);
Route::get('/get-products-productName', [ProductsCatergoryController::class, 'getAllProductProdName']);
Route::delete('/delete-products-productName/{id}', [ProductsCatergoryController::class, 'deleteProductProdName']);

// // Checklist
Route::post('/add-checklist', [ChecklistCatergoryController::class, 'addChecklistData']);
Route::post('get-checklist-data', [ChecklistCatergoryController::class, 'getAllChecklistData']);

// // Oil Temperature
Route::post('/add-oil-temperature', [OilTemperatureCatergoryController::class, 'addOilTemperatureData']);
Route::post('/get-oil-temperature-data', [OilTemperatureCatergoryController::class, 'getAllOilTemperatureData']);
Route::post('/add-oil-temperature-machines', [OilTemperatureCatergoryController::class, 'addOilTempMachines']);

// // Cleaning
Route::post('/add-cleaning', [CleaningCatergoryController::class, 'addCleaningData']);
Route::post('/get-cleaning-data', [CleaningCatergoryController::class, 'getAllCleaningData']);
