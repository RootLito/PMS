<?php

use Illuminate\Support\Facades\Route;


//MAIN PAGE ROUTE
Route::get('/', function () {
    return view('main');
});

//DASHBOARD ROUTE
Route::get('/dashboard', function () {
    return view('jocos/dashboard');
});


//EMPLOYEE ROUTE 
Route::get('/employee', function () {
    return view('jocos.employee'); 
})->name('employee');

Route::get('/employee/new', function () {
    return view('jocos.employee.new-emp');
})->name('employee.new');

Route::get('/employee/update', function () {
    return view('jocos.employee.update-emp');
})->name('employee.update');



//ADJUSTMENT ROUTE
Route::get('/adjustment', function () {
    return view('jocos/adjustment');
});



//CONTRIBUTION ROUTE
Route::get('contribution', function (){
    return view('jocos/contribution');
})->name('contribution');

Route::get('/contribution/new', function () {
    return view('jocos.contribution.new-cont');
})->name('contribution.new');






