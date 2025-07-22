<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});


Route::get('/dashboard', function () {
    return view('jocos/dashboard');
});