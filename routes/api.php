<?php

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


// USERAPICRUD starts
Route::get('user/index',[\App\Http\Controllers\Api\UserController::class,'index']);
Route::get('user/filter',[\App\Http\Controllers\Api\UserController::class,'filterUser']);
Route::post('user/store',[\App\Http\Controllers\Api\UserController::class,'storeUser']);
Route::put('user/update/{id?}',[\App\Http\Controllers\Api\UserController::class,'updateUser']);
Route::get('user/show/{id?}',[\App\Http\Controllers\Api\UserController::class,'showUser']);
Route::delete('user/delete/{id?}',[\App\Http\Controllers\Api\UserController::class,'deleteUser']);
// USERAPICRUD ends
