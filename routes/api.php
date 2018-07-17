<?php
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('recover', 'AuthController@recover');
Route::get('/', 'PublicController@getMainPage');

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::get('logout', 'AuthController@logout');
    Route::get('test', function(){
        return response()->json(['foo'=>'bar']);
    });

    Route::post('chain/{id}', 'ChainController@updateChain');
    Route::resource('calendar', 'CalendarController');
    Route::resource('chain', 'ChainController');
    Route::get('chain/do-default/{id}', 'ChainController@doDefault');
});