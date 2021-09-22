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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login','Api\AuthController@login');
Route::post('/register','Api\AuthController@register');
Route::get('/user/api_operations', 'Api\UserApiController@apiOperations');
Route::get('/profile','Api\UserApiController@getProfile');
Route::get('/services','Api\UserApiController@serviceList');
Route::get('/technology','Api\UserApiController@technologyList');
Route::get('/resources','Api\UserApiController@resourceList');
Route::get('/sub_resource/{id}','Api\UserApiController@subresourceList');
Route::get('/value_journals','Api\UserApiController@journalList');
Route::get('/sub_journals/{id}','Api\UserApiController@subJournals');
Route::get('/value_stories','Api\UserApiController@valuestories');
Route::get('/brands','Api\UserApiController@brands');
Route::get('/regions','Api\UserApiController@regions');
Route::get('/salesconnect','Api\UserApiController@salesconnectList');
Route::get('/presetQuestions','Api\UserApiController@presetQuestions');
Route::get('/products','Api\UserApiController@products');