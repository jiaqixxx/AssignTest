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


Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/assign_test', 'SupervisorController@index')->middleware('auth')->name('assign_test');


//API
Route::get('/agents', 'AssignTestController@getAgents');
Route::post('/assignTests', 'AssignTestController@assignTests');
Route::get('/workload', 'AssignTestController@getWorkload');
Route::get('/getInProgressAssignments', 'AssignTestController@getInProgressAssignments');
Route::get('/getComments/{assignmentId}', 'AssignTestController@getComments');
Route::post('/approveAssignment/{assignmentId}', 'AssignTestController@approveAssignment');

Route::get('/getApprovedAssignments', 'AssignTestController@getApprovedAssignments');
Route::post('/unSetAssignment/{assignmentId}', 'AssignTestController@unSetAssignment');
Route::get('/search', 'AssignTestController@search');