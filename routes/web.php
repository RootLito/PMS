<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});


Route::get('/dashboard', function () {
    return view('jocos/dashboard');
});

Route::get('/employee', function () {
    return view('jocos/employee');
});

Route::get('/adjustment', function () {
    return view('jocos/adjustment');
});

Route::get('/contribution', function () {
    return view('jocos/contribution');
});
