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
//Route::get('/test', function(){
//
//
//    $k1 = \App\Services\ApiClient\RestApiClientFactory::kibana();
//
//    return($k1->getTestData(\Carbon\Carbon::parse('2018-07-01 00:00:00'),\Carbon\Carbon::now()));
//    return [$k1->getTestData(\Carbon\Carbon::parse('2018-07-06 00:00:00'),\Carbon\Carbon::parse('2018-07-13 00:00:00'))];
//    $t = \App\Http\ApiClient\TestRestApiClient::instance();
////    return [$k1->test(),$k3->test(),$t->test()];
//    return [spl_object_hash($k1),spl_object_hash($k3)];
//    $kibanaRest = new \App\Http\ApiClient\KibanaRestApiClient();
//    $testRest = new \App\Http\ApiClient\TestRestApiClient();
//    return [$kibanaRest->test(),$testRest->test()];
//})->name('home');

Auth::routes();

//todo
Route::get('/', 'HomeController@index')->name('home');
Route::get('/your_assignments', 'AgentViewController@index')->name('your_assignments');
Route::get('/assign_test', 'SupervisorViewController@index')->name('assign_test');

Route::get('/users', 'AssignTestController@getAgents');
Route::post('/assignTests', 'AssignTestController@assignTests');

Route::get('/assignments', 'AssignmentController@getWorkload');
Route::put('/assignments', 'AssignmentController@approveOrNotAssignment');
Route::get('/assignments/status', 'AssignmentController@getAssignmentsWithStatus');
Route::get('/assignments/search', 'AssignmentController@searchAssignment');

Route::get('/agents/assignments', 'AssignmentController@getAgentAssignments');
Route::put('/agents/assignments', 'AssignmentController@setAllGood');

Route::post('/comments', 'CommentController@saveComments');
Route::post('/comments/images', 'CommentController@saveImages');
Route::get('/comments/{assignmentId}', 'CommentController@getComments');