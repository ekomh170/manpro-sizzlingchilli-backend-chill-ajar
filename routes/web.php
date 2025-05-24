<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Pentest UI per controller
Route::get('/pentest', function () {
    return view('pentest.index');
});
Route::get('/pentest/auth', function () {
    return view('pentest.auth');
});
Route::get('/pentest/mentor', function () {
    return view('pentest.mentor');
});
Route::get('/pentest/pelanggan', function () {
    return view('pentest.pelanggan');
});
Route::get('/pentest/kursus', function () {
    return view('pentest.kursus');
});
Route::get('/pentest/sesi', function () {
    return view('pentest.sesi');
});
Route::get('/pentest/transaksi', function () {
    return view('pentest.transaksi');
});
Route::get('/pentest/testimoni', function () {
    return view('pentest.testimoni');
});
