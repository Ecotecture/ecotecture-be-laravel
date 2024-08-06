<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\RenewableController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/welcome/', [ApiController::class, 'welcome']);
Route::get('/user/', [ApiController::class, 'user']);
Route::post('/renewable/', [RenewableController::class, 'calculateRenewables']);
Route::post('/calculate/', [ApiController::class, 'calculate']);
Route::post('/calculate/cost/', [ApiController::class, 'calculateCost']);