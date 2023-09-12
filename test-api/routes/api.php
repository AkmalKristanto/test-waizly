<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*General No Middleware*/
Route::group([], function () {

   Route::post('register', 'UserController@register');
   Route::post('login', [ 'as' => 'login', 'uses' => 'UserController@login']);

});

/*General Middleware*/
Route::group(['middleware' => 'jwt.verify'], function () {
   Route::post('logout', 'UserController@logout');
   Route::get('me', 'UserController@me');

   Route::group([
      'prefix' => 'cashier',
      'as' => 'cashier'
      ], function () {
          /*Product*/
         Route::get('product/list', 'ProductController@list_product');
         Route::get('product/detail', 'ProductController@detail_product');
         Route::post('product/update', 'ProductController@update_product');
         Route::post('product/create', 'ProductController@create_product');
         Route::post('product/delete', 'ProductController@delete_product');

         /*Order*/
         Route::get('order/list', 'OrderController@list_order');
         Route::get('order/list-draft', 'OrderController@list_draft_order');
         Route::post('order/draft', 'OrderController@draft_order');
         Route::get('order/detail', 'OrderController@detail_order');
         Route::post('order/create', 'OrderController@create_order');
   });
});