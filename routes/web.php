<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();


Route::get('/login','GeneralController@adminLogin')->name('login');
Route::get('/home', 'HomeController@index')->name('home');
Route::group(['prefix' => 'admin'], function () {
    Route::get('/login','GeneralController@adminLogin')->name('Admin-Login');
    Route::get('/home','AdminController@index')->name('Admin-Home');
    Route::get('/edit/profile','AdminController@editprofile');
    Route::post('/update/profile','AdminController@updateprofile');
    Route::get('/edit/company/profile','AdminController@editCompanyDetails');
    Route::post('/update/company/profile','AdminController@updateCompanyDetails');
    Route::get('/list/user/{type}','AdminController@listUser');
    Route::get('/create/user/{type}','AdminController@createUser');
    Route::post('/save/user','AdminController@saveUser');
    Route::get('/edit/user/{id}','AdminController@editUser');
    Route::post('/update/user','AdminController@updateUser');
    Route::get('/active/user/{id}','AdminController@activeUser');
    Route::get('list/Redington/{type}','AdminController@ListRedingtonFeatures');
    Route::post('add/new/Redington/{type}','AdminController@AddRedingtonFeatures');
    Route::post('/edit/Redington/technology/{id}','AdminController@editRedingtonTechnology');
    Route::post('/edit/Redington/service/{id}','AdminController@editRedingtonService');
    Route::get('/active/service/{id}','AdminController@activeService');
    Route::get('/active/technology/{id}','AdminController@activeTechnology');
    Route::get('/list/rewards','AdminController@ListRewards');
    Route::get('create/partner/reward','AdminController@createReward');
    Route::post('save/reward','AdminController@Savereward');
    Route::get('active/reward/{id}','AdminController@activeReward');
    Route::get('redeem/history/{id}','AdminController@RedeemHistory');
    Route::get('create/partner/redeem/{id}','AdminController@CreateRedeem');
    Route::post('save/redeem','AdminController@SaveRedeem');
    Route::get('reward/point','AdminController@getRewardPoint')->name('getRewardPoint');
    Route::get('reward/history/{id}','AdminController@RewardHistory');
    Route::post('Redington/Apply/Rewards','AdminController@SaveRewardforPartner');
    Route::get('resource/list','AdminController@resources');
    Route::post('add/resource','AdminController@addResource');
    Route::post('edit/resource/{id}','AdminController@editResource');
    Route::get('active/resource/{id}','AdminController@activeResource');
    Route::get('subresource/list/{id}','AdminController@subresources');
    Route::post('add/subresource','AdminController@addsubResource');
    Route::post('edit/subresource/{id}','AdminController@editsubResource');
    Route::get('/downloadfile/{file}','AdminController@downloadfile');
    Route::get('active/subresource/{id}','AdminController@activesubResource');
    Route::get('journals','AdminController@journals');
    Route::post('add/journal','AdminController@addJournal');
    Route::post('edit/journal/{id}','AdminController@editJournal');
    Route::get('active/main_journals/{id}','Admincontroller@activemainjournals');
    Route::get('value_journals/list/{id}','AdminController@ValueJournalList');
    Route::post('add/value_journal','AdminController@storevalueJournal');
    Route::post('edit/value_journals/{id}','AdminController@editvaluejournals');
    Route::get('active/journals/{id}','Admincontroller@activejournals');
    Route::get('value_stories/list','AdminController@ValuestoriesList');
    Route::post('add/value_stories','AdminController@storevaluestories');
    Route::post('edit/value_stories/{id}','AdminController@editvaluestories');
    Route::get('active/stories/{id}','Admincontroller@activestories');
    Route::get('/logout', 'AdminController@logout')->name('Admin-Logout');
});