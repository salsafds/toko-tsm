<?php

use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return view('welcome');
});

// Halaman login
Route::get('/login', function () {
    return view('Signin');
})->name('login');

Route::get('/dashboardmaster', function () {
    return view('dashboardmaster'); // nanti kita bikin view dengan nama ini
})->name('dashboardmaster');
