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

/**
 * UPGRADE NOTE:
 * anton: route has closure and can't be cached
 * @see https://github.com/laravel/framework/issues/31821
 */
Route::resources([
    'orders' => 'OrderController',
    'items' => 'ItemController',
]);

Route::get('/context_service', 'ContextController@getContext');
