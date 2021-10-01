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


// Route::get('/login','GeneralController@adminLogin')->name('login');
Route::get('admin/login','GeneralController@adminLogin')->name('Admin-Login');
Route::get('/home', 'HomeController@index')->name('home');

// user protected routes
Route::group(['middleware' => ['auth', 'CheckUser'], 'prefix' => 'customer'], function () {
    Route::get('/', 'HomeController@index')->name('Customer-Home');
    Route::get('/home','CustomerController@index')->name('Customer_Dashboard');
    Route::get('/edit/profile','CustomerController@editprofile');
    Route::post('/update/profile','CustomerController@updateprofile');
    Route::get('/logout', 'CustomerController@logout')->name('customer-Logout');
});

// admin protected routes
Route::group(['middleware' => ['auth', 'CheckAdmin'], 'prefix' => 'admin'], function () {
    Route::get('/', 'HomeController@index')->name('Admin-Home');
    Route::get('/home','AdminController@index')->name('Admin_Dashboard');
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
    Route::get('/edit/reward/{id}','AdminController@editreward');
    Route::post('/update/reward/{id}','AdminController@updatereward');
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
    Route::get('edit/subresource/{id}','AdminController@updatesubres');
    Route::post('edit/subresource/{id}','AdminController@editsubResource');
    Route::get('/downloadfile/{file}','AdminController@downloadfile');
    Route::get('active/subresource/{id}','AdminController@activesubResource');
    Route::get('delete/sub_resource_file','AdminController@deletesubresourcefile');
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
    Route::get('list/brands','AdminController@BrandList');
    Route::post('add/brand','AdminController@addBrand');
    Route::post('edit/brand/{id}','AdminController@editBrand');
    Route::get('list/regions','AdminController@RegionList');
    Route::post('add/region','AdminController@addRegion');
    Route::post('edit/region/{id}','AdminController@editRegion');
    Route::post('assign/region','AdminController@AssignRegion');
    Route::get('/sales_connects/{modal?}','AdminController@SalesConnects')->name('Sales_Connect');
    Route::post('/Reschedule/{id}','AdminController@Reschedule');
    Route::get('/preset_questions/{techid}/{brandid}/{from}','AdminController@PresetQuestions');
    Route::get('/prest_questions','AdminController@allqueries');
    Route::post('add/new/query','AdminController@addsalesquery');
    Route::post('edit/query/{id}','AdminController@editsalesquery');
    Route::get('query_request/{id}','AdminController@QueryRequest');
    Route::post('query/reply/{id}','AdminController@replyquery');
    Route::get('list/products','AdminController@ProductList');
    Route::post('add/product','AdminController@addProduct');
    Route::post('edit/product/{id}','AdminController@editProduct');
    Route::get('/list/new/events','AdminController@latestevents');
    Route::get('/list/past/events','AdminController@pastevents');
    Route::post('add/new/event','AdminController@addevent');
    Route::post('update/event/{id}','AdminController@updateevent');
    Route::get('/active/event/{id}','AdminController@activeEvent');
    Route::post('copy/event/{id}','AdminController@copyEvent');
    Route::get('main_services/list','AdminController@mainservices');
    Route::post('add/main_service','AdminController@addMainService');
    Route::post('edit/main_service/{id}','AdminController@editMainService');
    Route::get('active/main_service/{id}','AdminController@activeMainService');
    Route::get('subservices/list/{id}','AdminController@subMainService');
    Route::post('add/subservice','AdminController@addsubMainService');
    Route::post('edit/subservice/{id}','AdminController@editsubMainService');
    Route::get('active/subservice/{id}','AdminController@activesubMainService');
    Route::get('/business_solutions/','AdminController@businessSolutions');
    Route::post('/add/business_solution','AdminController@addbusinesssolution');
    Route::post('/edit/business_solution/{id}','AdminController@editbusinesssolution');
    Route::get('/logout', 'AdminController@logout')->name('Admin-Logout');
    Route::get('/page/settings','AdminController@pagesetiing');
    Route::post('add/page/details','AdminController@addpagedetails');
    Route::post('edit/page/{id}','AdminController@editpage');
    Route::get('/feedbacks','AdminController@feedbacks');
    Route::post('sales_connect/schedule/accept/{id}','AdminController@scheduleacceptforsalesconnect');
    Route::get('request_for/{type}/{id}/{modal?}','AdminController@requestslist')->name('Request_Call');
    Route::post('respond/request/{id}','AdminController@RequestRespond');
    Route::get('read/notification/{id}/{type}','AdminController@readNotification');
});
// Route::group(['prefix' => 'admin'], function () {
//     Route::get('admin/login','GeneralController@adminLogin')->name('Admin-Login');
// });

// Route::group(['prefix' => 'customer'], function () {
    

// });