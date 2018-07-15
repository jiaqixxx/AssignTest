<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
    //return $request->user();
//});

Route::get('/users', 'AssignTestController@getAgents');
Route::post('/assignTests', 'AssignTestController@assignTests');
Route::get('/comments/{assignmentId}', 'AssignTestController@getComments');

Route::get('/assignments', 'AssignmentController@getWorkload');
Route::post('/assignments', 'AssignmentController@approveOrNotAssignment');
Route::get('/assignments/{status}', 'AssignmentController@getAssignmentsWithStatus');
Route::get('/search', 'AssignmentController@searchAssignment');

Route::get('/agents/assignments', 'AssignmentController@getAgentAssignments');