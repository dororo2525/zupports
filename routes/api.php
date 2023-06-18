<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/get-nearby-restaurants',[App\Http\Controllers\GoogleMapController::class, 'getNearbyRestaurants']);
Route::get('/search-places/{keyword}',[App\Http\Controllers\GoogleMapController::class, 'searchPlaces']);
Route::get('/search-cache',[App\Http\Controllers\GoogleMapController::class, 'searchCache']);
Route::get('/nearbySearch',[App\Http\Controllers\GoogleMapController::class, 'nearbySearch']);
