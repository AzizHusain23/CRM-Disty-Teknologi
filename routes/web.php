<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadImportController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard & CRM Control Panel
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::patch('/leads/{lead}/phone', [DashboardController::class, 'updatePhone'])->name('leads.update-phone');

// Rute Import Leads
Route::get('/leads/import', [LeadImportController::class, 'showImportForm'])->name('leads.import');
Route::post('/leads/import', [LeadImportController::class, 'processImport'])->name('leads.import.process');

// Tambah Data Leads Manual
Route::get('/leads/create', [DashboardController::class, 'create'])->name('leads.create');
Route::post('/leads', [DashboardController::class, 'store'])->name('leads.store');
    
// Rute Campaign Email Blast
Route::get('/campaigns/create   ', [CampaignController::class, 'create'])->name('campaigns.create');
Route::post('/campaigns/send', [CampaignController::class, 'storeAndSend'])->name('campaigns.send');