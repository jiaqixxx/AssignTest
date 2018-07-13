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
Route::get('/users', 'AssignTestController@getAgents');
Route::post('/assignTests', 'AssignTestController@assignTests');
Route::get('/comments/{assignmentId}', 'AssignTestController@getComments');

Route::get('/assignments', 'AssignmentController@getWorkload');
Route::post('/assignments', 'AssignmentController@approveOrNotAssignment');
Route::get('/assignments/{status}', 'AssignmentController@getAssignmentsWithStatus');
Route::get('/search', 'AssignmentController@searchAssignment');