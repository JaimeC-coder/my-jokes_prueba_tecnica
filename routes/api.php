<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CardController;
use App\Http\Controllers\API\HomeController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth.token'])->group(function () {
    Route::get('/home', [HomeController::class, 'index']);

    // Card routes
    Route::post('/register-card', [CardController::class, 'register']);
    Route::get('/list-cards', [CardController::class, 'listCards']);
    Route::post('/charge-card', [CardController::class, 'chargeCard']);
});
