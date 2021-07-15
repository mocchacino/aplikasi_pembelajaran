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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::post('/tambahAdmin', 'Admin@tambahAdmin');
Route::post('/loginAdmin', 'Admin@loginAdmin');
Route::post('/hapusAdmin', 'Admin@hapusAdmin');
Route::post('/listAdmin', 'Admin@listAdmin');


Route::post('/tambahKonten', 'Konten@tambahKonten');
Route::post('/ubahKonten', 'Konten@ubahKonten');
Route::post('/hapusKonten', 'Konten@hapusKonten');
Route::post('/listKonten', 'Konten@listKonten');




