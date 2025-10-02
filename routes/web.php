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