<?php

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

Route::get('/', function () {
    return Redirect::route('home');
});

Auth::routes();

//Management
Route::get('/logout', 'HomeController@logout');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/algorithm-roster', 'RmsAlgorithmController@index');
Route::post('/algorithm-roster', 'RmsAlgorithmController@getDataAlgorithm');
Route::get('/previous-roster/{Id}', 'RMSController@previous_roster');
//User manager
Route::get('/user-manage', 'ManagementController@users');
Route::get('/view-user/{ID}', 'ManagementController@user_info');
Route::post('/update-user', 'ManagementController@update_user_info');
Route::post('/save-user', 'ManagementController@save_user');

//RosterController
Route::get('/add-roster', 'RMSController@add_roster');
// Route::post('/check-roster', 'RMSController@index');

Route::get('/roster', 'RMSController@index');
Route::post('/roster', 'RMSController@create');

Route::put('/roster/{ID}', 'RMSController@update');
Route::get('/roster/{ID}', 'RMSController@edit');
Route::get('/pdf-roster/{ID}', 'RMSController@pdfWeekRoster');
Route::get('/month-xl-roster/{ID}', 'RMSController@excel_monthly_roster');


Route::get('/pdf-roster-report', 'RMSController@index');
Route::get('/get-employee', 'RMSController@get_all_employee');
Route::get('/get-employee-roster/{ID}', 'RMSController@get_all_employee_edit_roster');


////engineers
Route::get('/engineers', 'EngineerController@index');
Route::post('/engineers', 'EngineerController@create');
Route::get('/engineers/{Id}', 'EngineerController@edit');
Route::put('/engineers/{Id}', 'EngineerController@update');

Route::get('/settings', 'ManagementController@settings');
Route::get('/settings/{ID}/{OP}', 'ManagementController@settingsRule');
