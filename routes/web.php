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

Route::get('/employee/update/{id}', function ($id) {
    return view('jocos.employee.update-emp', ['id' => $id]);
})->name('employee.update');


//PAYROLL ROUTE
Route::get('/payroll', function () {
    return view('jocos/payroll');
});
Route::get('/payroll/voucher', function () {
    return view('jocos.raw.voucher'); 
})->name('payroll.voucher');



//COMPUTATION ROUTE
Route::get('computation', function () {
    return view('jocos/computation');
})->name('computation');

Route::get('/contribution/new', function () {
    return view('jocos.contribution.new-cont');
})->name('contribution.new');


Route::get('/computation/voucher', function () {
    return view('jocos.raw.voucher'); 
})->name('computation.voucher');




//SIGNATORY ROUTE
Route::get('/signatory', function () {
    return view('jocos.signatory'); 
})->name('signatory');



//SALARY ROUTE
Route::get('/salary', function () {
    return view('jocos.salary');
})->name('salary');



//CONTRIBUTION ROUTE
Route::get('/contribution', function () {
    return view('jocos.contribution');
})->name('contribution');


//CONTRIBUTION ROUTE
Route::get('/designation', function () {
    return view('jocos.designation');
})->name('designation');