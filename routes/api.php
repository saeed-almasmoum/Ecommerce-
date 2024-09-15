<?php

use App\Http\Controllers\ProdutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/index', [ProdutController::class,'index']);

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::get('/index', [ProdutController::class, 'index']);

 
});