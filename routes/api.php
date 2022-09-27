<?php

use App\Http\Controllers\Artist\PortfolioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Artist\ServiceController;

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

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'userProfile']);
    Route::post('/profile', [AuthController::class, 'ProfileUpdate']);


    Route::group(['prefix' => 'artist','middleware' => 'artist'], function() {
        Route::resource('/service', ServiceController::class);
        Route::resource('/portfolio', PortfolioController::class);
        // Route::post('/service', [ServiceController::class, 'create']);
        // Route::post('/service/{id}', [ServiceController::class, 'update']);
        // Route::get('/service', [ServiceController::class, 'index']);
        // Route::delete('/service', [ServiceController::class, 'destroy']);
    });
    
});
