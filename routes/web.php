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
    Route::get('/edit/company/profile','AdminController@editprofile');
    Route::get('/logout', 'AdminController@logout')->name('Admin-Logout');
});