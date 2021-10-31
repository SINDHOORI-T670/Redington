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
Route::get('/profile/{user_id}','Api\UserApiController@getProfile');
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
Route::get('/main/services','Api\UserApiController@mainserviceList');
Route::get('new/events','Api\UserApiController@neweventsList');
Route::get('past/events','Api\UserApiController@pasteventsList');
Route::post('/new/sales_connect','Api\UserApiController@connectnow');
Route::post('/preset_question/send/reply/{id}','Api\UserApiController@sendReply');
Route::post('/new/schedule/meeting/{id}','Api\UserApiController@scheduleMeeting');
Route::get('/requests/{userid}','Api\UserApiController@getRequests');
Route::get('/meeting/schedules/{userid}','Api\UserApiController@getschedules');
Route::get('/myevents/{userid}','Api\UserApiController@myevents');
Route::get('/myhistory/{userid}','Api\UserController@myhistory');
Route::post('/new/request/{userid}/{id}','Api\UserController@newrequest');
Route::get('/salesconnect/{id}','Api\UserController@Salesconnect');