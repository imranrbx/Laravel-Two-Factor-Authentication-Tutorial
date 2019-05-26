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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/2fa', 'PasswordSecurityController@show2faform');
Route::post('/generate2fasecret', 'PasswordSecurityController@generate2fasecret')->name('generate2fasecret');
Route::post('/2fa', 'PasswordSecurityController@enable2fa')->name('enable2fa');
Route::post('/disable2fa', 'PasswordSecurityController@disable2fa')->name('disable2fa');
Route::post('/2faverify', function(){
	return redirect(URL()->previous());
})->name('2faverify')->middleware('2fa');