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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// App v1 API
Route::group([
    'middleware' => ['api.version:1'],
    'namespace'  => 'App\Http\Controllers\Api\V1',
    'prefix'     => 'v1',
], function ($router) {
    require base_path('routes/api/v1.php');
});

// App v2 API
// Route::group([
//     'middleware' => ['app', 'api.version:2'],
//     'namespace'  => 'App\Http\Controllers\App',
//     'prefix'     => 'api/v2',
// ], function ($router) {
//     require base_path('routes/api/v2.php');
// });