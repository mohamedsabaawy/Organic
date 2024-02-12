<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Admin\HintController;
use App\Http\Controllers\Api\Admin\OfferController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\PostController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\InvoiceController;
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

Route::group(['middleware'=>'auth:api'],function (){
    Route::get('auth/show',[AuthController::class,'show']);
    Route::post('auth/update',[AuthController::class,'update']);
    Route::post('auth/logout',[AuthController::class,'logout']);
    Route::resource('cart',CartController::class);
    Route::post('orders/cart',[InvoiceController::class,'fromCart']);
    //offer routes //
    Route::get('addresses',[AddressController::class,'index']);
    Route::post('addresses',[AddressController::class,'store']);
    Route::post('addresses/{id}',[AddressController::class,'update']);
    Route::get('addresses/{id}/edit',[AddressController::class,'edit']);
    Route::get('addresses/{id}',[AddressController::class,'show']);
    Route::delete('addresses/{id}',[AddressController::class,'destroy']);
    //offer routes //
    Route::resource('favorites',FavoriteController::class);
    Route::resource('orders',InvoiceController::class);
    Route::post('orders/cart',[InvoiceController::class,'fromCart']);

    Route::group(['prefix'=>'admin','middleware'=>'CheckAdmin'],function (){
        Route::get('/',[\App\Http\Controllers\Api\Admin\AuthController::class,'index']);
        Route::get('/show/{id}',[\App\Http\Controllers\Api\Admin\AuthController::class,'show']);
        Route::post('/delete/{id}',[\App\Http\Controllers\Api\Admin\AuthController::class,'delete']);
        Route::post('/update/{id}',[\App\Http\Controllers\Api\Admin\AuthController::class,'update']);
        Route::post('/store',[\App\Http\Controllers\Api\Admin\AuthController::class,'store']);
        Route::post('/swap/{id}',[\App\Http\Controllers\Api\Admin\AuthController::class,'swap']);
        Route::post('photos/{id}',[ItemController::class,'photoDelete']);
        Route::post('item/photos/{id}',[ItemController::class,'addPhoto']);
        Route::post('items/special/{id}',[ItemController::class,'makeSpecial']);
        Route::post('offers/{id}',[OfferController::class,'update']);

        Route::get('orders',[OrderController::class,'index']);
        Route::post('orders/{id}',[OrderController::class,'update']);
        Route::post('orders/rollback/{id}',[OrderController::class,'rollback']);
    });
});
Route::group([],function (){
   //category routes //
   Route::get('categories',[CategoryController::class,'index']);
   Route::post('categories',[CategoryController::class,'store']);
   Route::post('categories/{id}',[CategoryController::class,'update']);
   Route::get('categories/{id}/edit',[CategoryController::class,'edit']);
   Route::get('categories/{id}',[CategoryController::class,'show']);
   Route::delete('categories/{id}',[CategoryController::class,'destroy']);
    //category routes //
   //offer routes //
   Route::get('offers',[OfferController::class,'index']);
   Route::post('offers',[OfferController::class,'store']);
   Route::post('offers/{id}',[OfferController::class,'update']);
   Route::get('offers/{id}/edit',[OfferController::class,'edit']);
   Route::get('offers/{id}',[OfferController::class,'show']);
   Route::delete('offers/{id}',[OfferController::class,'destroy']);
    //offer routes //
   //item routes //
   Route::get('items',[ItemController::class,'index']);
   Route::post('items',[ItemController::class,'store']);
   Route::post('items/{id}',[ItemController::class,'update']);
   Route::get('items/{id}/edit',[ItemController::class,'edit']);
   Route::get('items/{id}',[ItemController::class,'show']);
   Route::delete('items/{id}',[ItemController::class,'destroy']);
    //item routes //
   //sliders routes //
   Route::get('sliders',[SliderController::class,'index']);
   Route::post('sliders',[SliderController::class,'store']);
   Route::post('sliders/{id}',[SliderController::class,'update']);
   Route::get('sliders/{id}/edit',[SliderController::class,'edit']);
   Route::get('sliders/{id}',[SliderController::class,'show']);
   Route::delete('sliders/{id}',[SliderController::class,'destroy']);
    //sliders routes //
    //posts routes//
   Route::get('posts',[PostController::class,'index']);
   Route::post('posts',[PostController::class,'store']);
   Route::post('posts/{id}',[PostController::class,'update']);
   Route::get('posts/{id}/edit',[PostController::class,'edit']);
   Route::get('posts/{id}',[PostController::class,'show']);
   Route::delete('posts/{id}',[PostController::class,'destroy']);
    //posts routes//
    //posts routes//
   Route::get('hints',[HintController::class,'index']);
   Route::post('hints',[HintController::class,'store']);
   Route::post('hints/{id}',[HintController::class,'update']);
   Route::get('hints/{id}/edit',[HintController::class,'edit']);
   Route::get('hints/{id}',[HintController::class,'show']);
   Route::delete('hints/{id}',[HintController::class,'destroy']);
    //posts routes//
   Route::post('auth/register',[AuthController::class,'register']);
   Route::post('auth/login',[AuthController::class,'login']);

});
