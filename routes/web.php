<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractPdfController;
use App\Http\Controllers\QuotePdfController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/quotes/{quote}/pdf', QuotePdfController::class)
         ->name('quotes.pdf');

    Route::get('/admin/projects/{project}/contract/pdf', ContractPdfController::class)
         ->name('projects.contract.pdf');
});
