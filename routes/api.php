<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\CustomerController;

Route::apiResource('v1/admin', AdminController::class)->only(['store']);
Route::apiResource('v1/customer', CustomerController::class)->only(['store']);

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
