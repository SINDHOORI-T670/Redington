<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::get('/home','AdminController@index')->name('AdminDashboard');
Route::get('admin/logout', 'AdminController@logout')->name('Admin-Logout');