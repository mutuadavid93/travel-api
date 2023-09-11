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
Route::prefix("admin")->middleware(["forceJson", "auth:sanctum", "role:admin"])->group(function () {
    // e.g. http://localhost:8000/api/v1/admin/travels
    Route::post("travels", [Admin\TravelController::class, "store"]);
});

// Login Endpoint
Route::post("login", LoginController::class)->name("login");
