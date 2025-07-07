<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\IncomeController;

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

Route::group(['middleware' => 'api.key'], function () {
    Route::get('/stocks', [StockController::class, 'index']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/sales', [SaleController::class, 'index']);
    Route::get('/incomes', [IncomeController::class, 'index']);
});
