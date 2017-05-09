<?php

use Illuminate\Http\Request;

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
/* Post Section */
Route::post("signUp", "AuthController@signUp");
Route::post("signIn", "AuthController@signIn");
Route::post("updateProduct/{id}", "ProductsController@updateProduct");
Route::post("storeProduct", "ProductsController@storeProduct");
Route::post("destroyProduct/{id}", "ProductsController@destroyProduct");
Route::post("updateOrder/{id}", "OrdersController@updateOrder");
Route::post("storeOrder", "OrdersController@storeOrder");
Route::post("destroyOrder/{id}", "OrdersController@destroyOrder");
Route::post("storeCategory", "CategoryController@storeCategory");
Route::post("destroyCategory/{id}", "OrdersController@destroyCategory");

/* Get Section */
Route::get("showProduct/{id}", "ProductsController@showProduct");
Route::get("showCategory/{id}", "CategoryController@showCategory");
Route::get("showOrder/{id}", "OrdersController@showProduct");
Route::get("getProduct", "ProductsController@index");
Route::get("getOrder", "ProductsController@index");
Route::get("getCategory", "ProductsController@index");
Route::get("getUser", "AuthController@getUser");
