<?php

use App\Http\Controllers\EmployeeController;
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


//ADJUSTMENT ROUTE
Route::get('/payroll', function () {
    return view('jocos/payroll');
});



//CONTRIBUTION ROUTE
Route::get('computation', function () {
    return view('jocos/computation');
})->name('computation');

Route::get('/contribution/new', function () {
    return view('jocos.contribution.new-cont');
})->name('contribution.new');


Route::get('/computation/voucher', function () {
    return view('jocos.raw.voucher'); 
})->name('computation.voucher');
