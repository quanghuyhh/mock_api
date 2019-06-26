<?php

use Illuminate\Http\Request;
use App\Models\Section;
use App\Http\Resources\Api\V1\SectionCollection;
use App\Http\Resources\Api\V1\SectionItemCollection;

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

Route::group(['namespace' => '\App\Http\Controllers\Api\V1'], function() {
    Route::group(['prefix' => 'book'], function() {
        Route::get('/{id}', ['uses' => 'BookController@show', 'as' => 'book.show']);
    });
    
    Route::group(['prefix' => 'sections'], function() {
        Route::get('/browse', ['uses' => 'SectionController@browse', 'as' => 'section.browse']);
    
        Route::get('/{sectionId}/sectionsItems', ['uses' => 'SectionController@items', 'as' => 'section.items']);
    });
});


