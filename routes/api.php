<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ItemController;
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

Route::group(['middleware'=>'api'],function (){
    Route::get('auth/show',[AuthController::class,'show']);
});
Route::group(['prefix' => LaravelLocalization::setLocale()],function (){
   Route::resource('categories',CategoryController::class);
   Route::resource('items',ItemController::class);
   Route::resource('cart',CartController::class);
   Route::post('auth/register',[AuthController::class,'store']);
   Route::post('auth/login',[AuthController::class,'login']);

});
