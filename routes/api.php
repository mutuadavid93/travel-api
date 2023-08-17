<?php

use App\Http\Controllers\Api\V1\{TravelController, TourController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get("travels/{travel:slug}/tours", [TourController::class, "index"]);
