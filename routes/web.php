<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\LoginController;


//MAIN PAGE ROUTE
Route::get('/', function () {
    return view('main');
});

//AUTH
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protect all routes below using auth middleware
// Route::middleware('auth')->group(function () {

//     Route::get('/dashboard', function () {
//         return view('jocos/dashboard');
//     });

//     Route::get('/employee', function () {
//         return view('jocos.employee');
//     })->name('employee');

//     Route::get('/employee/new', function () {
//         return view('jocos.employee.new-emp');
//     })->name('employee.new');

//     Route::get('/employee/update/{id}', function ($id) {
//         return view('jocos.employee.update-emp', ['id' => $id]);
//     })->name('employee.update');

//     Route::get('/payroll', function () {
//         return view('jocos/payroll');
//     });

//     Route::get('/payroll/voucher', function () {
//         return view('jocos.raw.voucher');
//     })->name('payroll.voucher');

//     Route::get('computation', function () {
//         return view('jocos/computation');
//     })->name('computation');

//     Route::get('/contribution/new', function () {
//         return view('jocos.contribution.new-cont');
//     })->name('contribution.new');

//     Route::get('/computation/voucher', function () {
//         return view('jocos.raw.voucher');
//     })->name('computation.voucher');

//     Route::get('/configuration/signatory', function () {
//         return view('jocos.signatory');
//     })->name('signatory');

//     Route::get('/configuration/salary', function () {
//         return view('jocos.salary');
//     })->name('salary');

//     Route::get('/contribution', function () {
//         return view('jocos.contribution');
//     })->name('contribution');

//     Route::get('/configuration/designation', function () {
//         return view('jocos.designation');
//     })->name('designation');

//     Route::get('/archive', function () {
//         return view('jocos.show-archive');
//     })->name('archive');

//     Route::get('/files/download/{id}', [DownloadController::class, 'download'])->name('files.download');

//     Route::get('/attendance', function () {
//         return view('jocos.attendance');
//     })->name('attendance');

//     Route::get('/configuration/position', function () {
//         return view('jocos.position');
//     })->name('position');

//     Route::get('/configuration', function () {
//         return view('jocos.config');
//     })->name('configuration');

//     Route::get('/employee/payslip/{employeeId}', [PayslipController::class, 'printPayslip'])->name('employee.payslip');

//     Route::get('/configuration/account', function () {
//         return view('jocos.account');
//     })->name('account');
// });






//REGULAR DASHBOARD
Route::get('/regular-dashboard', function () {
    return view('regular/regular-dashboard');
});


//REGULAR EMPLOYEE
Route::get('/regular-employee', function () {
    return view('regular.regular-employee');
})->name('regular-employee');

Route::get('/regular-employee/new-regular', function () {
    return view('regular.employee.new-regular-emp');
})->name('regular-employee.new-regular');

Route::get('/employee/update/{id}', function ($id) {
    return view('regular.employee.update-emp', ['id' => $id]);
})->name('employee.update');

Route::get('/employee/payslip/{employeeId}', [
    PayslipController::class,
    'printPayslip'
])->name('employee.payslip');



//REGULAR CONTRIBUTION

//     Route::get('/contribution', function () {
//         return view('jocos.contribution');
//     })->name('contribution');

//REGULAR COMPUTATION






//REGULAR PAYROLL

//     Route::get('/payroll', function () {
//         return view('jocos/payroll');
//     });


Route::get('/configuration/regular-designation', function () {
    return view('regular.regular-designation');
})->name('designation');

Route::get('/configuration/regular-position', function () {
    return view('regular.regular-position');
})->name('position');

Route::get('/configuration/regular-salary', function () {
    return view('regular.regular-salary');
})->name('salary');