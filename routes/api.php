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

Route::get("/list-banks", [App\Http\Controllers\API\BankController::class, 'listBanks']);
Route::post("/verify-account-number", [App\Http\Controllers\API\BankController::class, 'fetchAccountDetails']);
Route::post("/make-transfer", [App\Http\Controllers\API\PaymentController::class, "makeTransfer"]);
Route::get("/list-transfers", [App\Http\Controllers\API\PaymentController::class, "listTransfers"]);
Route::get("/fetch-transfer/{id_or_code}", [App\Http\Controllers\API\PaymentController::class, "fetchTransfer"]);