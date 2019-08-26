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
    
    Route::group(['prefix' => 'books'], function() {
        Route::get('/', ['uses' => 'BookController@index', 'as' => 'book.index']);
        Route::get('/highlights', ['uses' => 'BookController@allHighlight', 'as' => 'book.allHighlight']);
        Route::get('/library', ['uses' => 'LibraryController@getBooksByStatus', 'as' => 'books.getBookByStatus']);
        Route::get('/recent', ['uses' => 'BookController@recent', 'as' => 'books.recent']);
        Route::post('/{bookId}/progress', ['uses' => 'BookController@progress', 'as' => 'books.progress']);
    });

    Route::group(['prefix' => 'book'], function() {
        Route::get('/{id}/highlights', ['uses' => 'BookController@highlights', 'as' => 'book.highlights']);
        Route::post('/{id}/library', ['uses' => 'LibraryController@addBookToLibrary', 'as' => 'book.addBookToLibrary']);
        Route::put('/{id}/library', ['uses' => 'LibraryController@updateBookInLibrary', 'as' => 'book.updateBookInLibrary']);
        Route::delete('/{id}/library', ['uses' => 'LibraryController@deleteBookFromLibrary', 'as' => 'book.deleteBookFromLibrary']);
        Route::post('/{id}/recent', ['uses' => 'BookController@addBookToRecent', 'as' => 'book.addBookToRecent']);

        Route::post('/highlight', ['uses' => 'BookController@createBookHighlight', 'as' => 'book.createBookHighlight']);
        Route::put('/highlight/{id}', ['uses' => 'BookController@updateHighlight', 'as' => 'book.updateHighlight']);
        Route::delete('/highlight/{id}', ['uses' => 'BookController@deleteHighlight', 'as' => 'book.deleteHighlight']);

        Route::get('/{id}/{field?}', ['uses' => 'BookController@show', 'as' => 'book.show']);
    });

    Route::group(['prefix' => 'categories'], function() {
        Route::get('/', ['uses' => 'CategoryController@index', 'as' => 'categories.index']);
        Route::get('/{id}', ['uses' => 'CategoryController@show', 'as' => 'categories.show']);
        Route::get('/{id}/books', ['uses' => 'CategoryController@books', 'as' => 'categories.books']);
    });
    
    Route::group(['prefix' => 'sections'], function() {
        Route::get('/', ['uses' => 'SectionController@index', 'as' => 'section.index']);
        Route::get('/browse', ['uses' => 'SectionController@browse', 'as' => 'section.browse']);
        Route::get('/{sectionId}', ['uses' => 'SectionController@show', 'as' => 'section.show']);
        Route::get('/{sectionId}/sectionsItems', ['uses' => 'SectionController@items', 'as' => 'section.items']);
    });

    Route::group(['prefix' => 'sectionItems'], function() {
        Route::get('/{sectionItemId}', ['uses' => 'SectionItemController@show', 'as' => 'section.show']);
    });

    Route::group(['prefix' => 'notes'], function() {
        Route::get('/book/{id}', ['uses' => 'NoteController@listOfBook', 'as' => 'notes.listOfBook']);
        Route::post('/create', ['uses' => 'NoteController@create', 'as' => 'notes.create']);
        Route::get('/{id}', ['uses' => 'NoteController@show', 'as' => 'notes.show']);
        Route::put('/{id}', ['uses' => 'NoteController@edit', 'as' => 'notes.create']);
        Route::delete('/{id}', ['uses' => 'NoteController@delete', 'as' => 'notes.delete']);
    });

    Route::group(['prefix' => 'library'], function() {
        Route::get('/', ['uses' => 'LibraryController@list', 'as' => 'library.list']);
        Route::post('/add', ['uses' => 'LibraryController@add', 'as' => 'library.add']);
        Route::get('/{id}', ['uses' => 'LibraryController@show', 'as' => 'library.show']);
        Route::put('/{id}', ['uses' => 'LibraryController@edit', 'as' => 'library.create']);
        Route::delete('/{id}', ['uses' => 'LibraryController@delete', 'as' => 'library.delete']);
    });
});


