<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'Controller@connect');

// Authentication
Route::get('/check-install', 'authController@checkInstall');
Route::get('/callback', 'authController@access_token');

Route::get('/no', function () {
    dd('no');
});
Route::get('/yes', function () {
    dd('yes');
});


//This is where the app actually begins
Route::get('/index', function () {
  dd('index');
});




// For error on installation
Route::get('/redirect-to-install', function () {
    return view('redirect', [
      'url' => env('SHOPIFY_API_APP_STORE')
    ]);
});
