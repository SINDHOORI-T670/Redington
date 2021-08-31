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
    Route::get('/delete/user/{id}','AdminController@deleteUser');
    Route::get('list/Redington/{type}','AdminController@ListRedingtonFeatures');
    Route::post('add/new/Redington/{type}','AdminController@AddRedingtonFeatures');
    Route::post('/edit/Redington/technology/{id}','AdminController@editRedingtonTechnology');
    Route::post('/edit/Redington/service/{id}','AdminController@editRedingtonService');
    Route::get('/logout', 'AdminController@logout')->name('Admin-Logout');
});