<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

function user_ins()
{
	return new App\User();
}

function question_ins()
{
	return new App\Question();
}

function answer_ins()
{
	return new App\Answer();
}

function comment_ins()
{
	return new App\Comment();
}

function rq($key = null, $default = null)
{
	if(!$key) {
		return Request::all();
	}
	return Request::get($key, $default);
}


Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=>'api'], function() {
	Route::group(['prefix'=>'user'],function() {
		Route::any('signup', function() {
			return user_ins()->signUp();
		});

		Route::any('login', function() {
			return user_ins()->login();
		});

		Route::any('logout', function() {
			return user_ins()->logout();
		});
	});

	Route::group(['prefix'=>'question'], function() {
		Route::any('add', function() {
			return question_ins()->add();
		});
		Route::any('change', function() {
			return question_ins()->change();
		});
		Route::any('read', function() {
			return question_ins()->read();
		});
		Route::any('remove', function() {
			return question_ins()->remove();
		});
	});

	Route::group(['prefix'=>'answer'], function() {
		Route::any('add', function() {
			return answer_ins()->add();
		});
		Route::any('change', function() {
			return answer_ins()->change();
		});
		Route::any('read', function() {
			return answer_ins()->read();
		});
	});

	Route::group(['prefix'=>'comment'], function() {
		Route::any('add', function() {
			return comment_ins()->add();
		});
		Route::any('read', function() {
			return comment_ins()->read();
		});
		Route::any('remove', function() {
			return comment_ins()->remove();
		});
	});
});