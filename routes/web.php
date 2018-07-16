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
Route::get('/your_assignments', 'AgentViewController@index')->name('your_assignments');
Route::get('/assign_test', 'SupervisorViewController@index')->name('assign_test');

Route::get('/users', 'AssignTestController@getAgents');
Route::post('/assignTests', 'AssignTestController@assignTests');

Route::get('/assignments', 'AssignmentController@getWorkload');
Route::put('/assignments', 'AssignmentController@approveOrNotAssignment');
Route::get('/assignments/{status}', 'AssignmentController@getAssignmentsWithStatus');
Route::get('/search', 'AssignmentController@searchAssignment');

Route::get('/agents/assignments', 'AssignmentController@getAgentAssignments');
Route::put('/agents/assignments', 'AssignmentController@setAllGood');

Route::post('/comments', 'CommentController@saveComments');
Route::post('/comments/images', 'CommentController@saveImages');
Route::get('/comments/{assignmentId}', 'CommentController@getComments');