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
    return null;
});

Route::group(['prefix' => 'mock', 'namespace' => '\App\Http\Controllers\Mock'], function () {
    Route::get('books', ['uses' => 'FakeDataController@books']);
    Route::get('authors', ['uses' => 'FakeDataController@authors']);
    Route::get('types', ['uses' => 'FakeDataController@types']);
    Route::get('metas', ['uses' => 'FakeDataController@metas']);
    Route::get('sections', ['uses' => 'FakeDataController@sections']);
    Route::get('summary', ['uses' => 'FakeDataController@summary']);
});
