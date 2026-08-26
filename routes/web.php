<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\CustomerImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::resource(
        'institutions',
        InstitutionController::class
    );

    Route::resource(
        'customers',
        CustomerController::class
    );

    Route::get(
        '/customer-imports',
        [CustomerImportController::class, 'index']
    )->name('customer-imports.index');

    Route::get(
        '/customer-imports/create',
        [CustomerImportController::class, 'create']
    )->name('customer-imports.create');

    Route::get(
        '/customer-imports/template',
        [CustomerImportController::class, 'downloadTemplate']
    )->name('customer-imports.template');

    Route::post(
        '/customer-imports',
        [CustomerImportController::class, 'store']
    )->name('customer-imports.store');

    Route::get(
        '/customer-imports/{importBatch}',
        [CustomerImportController::class, 'show']
    )->name('customer-imports.show');

    Route::post(
        '/customer-imports/{importBatch}/execute',
        [CustomerImportController::class, 'execute']
    )->name('customer-imports.execute');

    Route::delete(
        '/customer-imports/{importBatch}',
        [CustomerImportController::class, 'destroy']
    )->name('customer-imports.destroy');
});
