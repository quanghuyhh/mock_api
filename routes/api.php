<?php

use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\UserResource;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return UserResource::make($request->user());
// });

Route::group(['prefix' => 'v1/auth', 'namespace' => '\App\Http\Controllers\Api\Auth'], function () {
    Route::post('login', ['uses' => 'AuthController@login']);
    Route::post('signup', ['uses' => 'AuthController@signup']);
  
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', ['uses' => 'AuthController@logout']);
        Route::get('user', ['uses' => 'AuthController@user']);
    });
});

Route::group(['prefix' => 'v1/password', 'namespace' => '\App\Http\Controllers\Api\Auth', 'middleware' => 'api'], function () {    
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token?}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});

// App v1 API
Route::group([
    'middleware' => ['api.version:1', 'auth:api'],
    'namespace'  => 'App\Http\Controllers\Api\V1',
    'prefix'     => 'v1',
], function ($router) {
    require base_path('routes/api/v1.php');
});