<?php

use Illuminate\Support\Facades\Route;

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

Route::get('Contacts/List',['App\Http\Controllers\ContactController','index']);
Route::post('Contacts/Store',['App\Http\Controllers\ContactController','store']);
Route::post('Contacts/Update/{id}',['App\Http\Controllers\ContactController','update']);
Route::get('Contacts/Delete/{id}',['App\Http\Controllers\ContactController','destroy']);

Route::get('/', function () {
    return view('welcome');
});
