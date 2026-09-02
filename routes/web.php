<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerImportController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\TrainingCategoryController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {

    Route::get(
        '/login',
        [AuthController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/login',
        [AuthController::class, 'login']
    )->name('login.store');

    Route::get(
        '/register',
        [AuthController::class, 'showRegister']
    )->name('register');

    Route::post(
        '/register',
        [AuthController::class, 'register']
    )->name('register.store');

});

Route::middleware('auth')->group(function () {

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    Route::get(
        '/reports',
        [ReportController::class, 'index']
    )->name('reports.index');

    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    )->name('logout');


    /*
    |--------------------------------------------------------------------------
    | Institution
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'institutions',
        InstitutionController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'customers',
        CustomerController::class
    );

    Route::post(
        '/customers/{customer}/activate',
        [CustomerController::class, 'activate']
    )->name('customers.activate');

    Route::post(
        '/customers/{customer}/deactivate',
        [CustomerController::class, 'deactivate']
    )->name('customers.deactivate');


    Route::get(
        '/activities',
        [ActivityController::class, 'index']
    )->name('activities.index');

    Route::post(
        '/customers/{customer}/activities',
        [ActivityController::class, 'store']
    )->name('customers.activities.store');

    Route::resource(
        'follow-ups',
        FollowUpController::class
    )->except(['show']);

    Route::post(
        '/follow-ups/{followUp}/complete',
        [FollowUpController::class, 'complete']
    )->name('follow-ups.complete');

    Route::post(
        '/follow-ups/{followUp}/cancel',
        [FollowUpController::class, 'cancel']
    )->name('follow-ups.cancel');


    /*
    |--------------------------------------------------------------------------
    | Customer Import
    |--------------------------------------------------------------------------
    */

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

    Route::post(
        '/customer-imports/{importBatch}/validate',
        [CustomerImportController::class, 'validateChunk']
    )->name('customer-imports.validate');

    Route::put(
        '/customer-imports/{importBatch}/rows/{importRow}',
        [CustomerImportController::class, 'updateRow']
    )->name('customer-imports.rows.update');

    Route::delete(
        '/customer-imports/{importBatch}',
        [CustomerImportController::class, 'destroy']
    )->name('customer-imports.destroy');


    /*
    |--------------------------------------------------------------------------
    | Training Categories
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'training-categories',
        TrainingCategoryController::class
    )
        ->except([
            'show',
        ])
        ->parameters([
            'training-categories' =>
                'trainingCategory',
        ]);


    /*
    |--------------------------------------------------------------------------
    | Trainings
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'trainings',
        TrainingController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Registration / Pendaftaran Training
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'registrations',
        RegistrationController::class
    )->except(['show']);

});
