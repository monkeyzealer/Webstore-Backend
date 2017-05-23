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
/* POST SECTION */
/* Auth Controller */
Route::post("signUp", "AuthController@signUp");
Route::post("signIn", "AuthController@signIn");
/* Roles Controller */
Route::post("updateRole/{id}", "RolesController@updateRole");
Route::post("storeRole", "RolesController@storeRole");
Route::post("destroyRole/{id}", "RolesController@destroyRole");
/* Products Controller */
Route::post("updateProduct/{id}", "ProductsController@updateProduct");
Route::post("storeProduct", "ProductsController@storeProduct");
Route::post("destroyProduct/{id}", "ProductsController@destroyProduct");
/* Comments Controller */
Route::post("storeComment", "CommentsController@storeComment");
Route::post("deleteComment/{id}", "CommentsController@deleteComment");
/* Orders Controller */
Route::post("updateOrder/{id}", "OrdersController@updateOrder");
Route::post("storeOrder", "OrdersController@storeOrder");
Route::post("destroyOrder/{id}", "OrdersController@destroyOrder");
/* Category Controller */
Route::post("storeCategory", "CategoryController@storeCategory");
Route::post("destroyCategory/{id}", "CategoryController@destroyCategory");


/* GET SECTION */
/* Auth Controller */
Route::get("getUser", "AuthController@getUser");
/* Roles Controller */
Route::get("showRole/{id}", "RolesController@showRole");
Route::get("getRole", "RolesController@index");
/* Products Controller */
Route::get("showProduct/{id}", "ProductsController@showProduct");
Route::get("getProducts", "ProductsController@index");
/* Products Controller */
Route::get("getComments/{id}", "CommentsController@index");
/* Orders Controller */
Route::get("showOrder/{id}", "OrdersController@showOrder");
Route::get("getOrders", "OrdersController@index");
Route::get("showUserOrders", "OrdersController@showUserOrders");
/* Category Controller */
Route::get("showCategory/{id}", "CategoryController@showCategory");
Route::get("getCategories", "CategoryController@index");
