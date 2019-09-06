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
    return view('welcome');
});

Route::get('posts/{post}', 'PostController@show');

Route::post('accept-terms', 'AcceptTermsController@accept');

Route::middleware('auth')->namespace('Admin\\')->prefix('admin/')->group(function () {
    Route::get('posts', 'PostController@index');

    Route::get('posts/create', 'PostController@create');

    Route::post('posts', 'PostController@store');

    Route::get('posts/{post}/edit', 'PostController@edit')->name('posts.edit');

    Route::put('posts/{post}', 'PostController@update')->where('post', '\d+');

    Route::delete('posts/{post}', 'PostController@destroy')->where('post', '\d+');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
