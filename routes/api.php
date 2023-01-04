<?php

// use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::any('users', 'Api\UserController@register');

Route::group([
    'middleware' => 'api',
    'prefix'     => 'auth'
], function () {
    Route::post('login', 'Api\AuthController@login');
    Route::post('logout', 'Api\AuthController@logout');
    Route::post('refresh', 'Api\AuthController@refresh');
    Route::post('me', 'Api\AuthController@me');
});

Route::group([
    'prefix'     => 'category'
], function () {
    Route::get('/', 'Api\CategoryController@index');
    Route::get('{id}', 'Api\CategoryController@getById');

    Route::group(
        [
            'middleware' => 'auth'
        ],
        function () {
            Route::post('/', 'Api\CategoryController@store');
            Route::put('{id}', 'Api\CategoryController@update');
            Route::delete('{id}', 'Api\CategoryController@destroy');
        }
    );
});

Route::group([
    'prefix'     => 'subcategory'
], function () {
    Route::get('/', 'Api\SubCategoryController@index');
    Route::get('{id}', 'Api\SubCategoryController@getById');

    Route::group(
        [
            'middleware' => 'auth'
        ],
        function () {
            Route::post('/', 'Api\SubCategoryController@store');
            Route::put('{id}', 'Api\SubCategoryController@update');
            Route::delete('{id}', 'Api\SubCategoryController@destroy');
        }
    );
});

Route::group([
    'prefix'     => 'tag'
], function () {
    Route::get('/', 'Api\TagController@index');
    Route::get('{id}', 'Api\TagController@getById');
    Route::group(
        [
            'middleware' => 'auth'
        ],
        function () {
            Route::post('/', 'Api\TagController@store');
            Route::put('{id}', 'Api\TagController@update');
            Route::delete('{id}', 'Api\TagController@destroy');
        }
    );
});

Route::group([
    'prefix'     => 'news'
], function () {
    Route::get('/', 'Api\NewsController@index');
    Route::get('{id}/id', 'Api\NewsController@getById');
    Route::get('{slg}/slug', 'Api\NewsController@getBySlug');

    Route::group(
        [
            'middleware' => 'auth'
        ],
        function () {
            Route::post('/', 'Api\NewsController@store');
            Route::post('{id}/id', 'Api\NewsController@update');
            Route::post('{slug}/slug', 'Api\NewsController@updateBySlug');
            Route::delete('{id}/id', 'Api\NewsController@destroy');
            Route::delete('{id}/slug', 'Api\NewsController@destroyBySlug');
        }
    );
});
