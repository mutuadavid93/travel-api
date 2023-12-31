<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{TravelController, TourController};

// Import all admin resources and only specify what to use. 
// e.g. Admin\TravelController
use App\Http\Controllers\Api\V1\Admin;

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



// Travels Endpoint
Route::get("travels", [TravelController::class, "index"]);

// Tours Endpoint
// TIP: use a different field e.g. slug instead of the default `id` column
Route::get("travels/{travel:slug}/tours", [TourController::class, "index"])->middleware('forceJson');


// Prefix Admin Routes 
Route::prefix("admin")->middleware(["forceJson", "auth:sanctum"])->group(function () {

    Route::middleware("role:admin")->group(function () {
        // e.g. http://localhost:8000/api/v1/admin/travels
        Route::post("travels", [Admin\TravelController::class, "store"]);

        // Default id used instead of slug. In this case is uuid.
        // e.g. http://localhost:8000/api/v1/admin/travels/9a1f3923-1eec-4fb5-a3f5-d86a8fe38ca7/tours
        Route::post("travels/{travel}/tours", [Admin\TourController::class, "store"]);
    });

    // Any logged in user can update a travel
    Route::put("travels/{travel}", [Admin\TravelController::class, "update"]);
});

// Login Endpoint
Route::post("login", LoginController::class)->name("login");
