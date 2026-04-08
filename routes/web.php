<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotePdfController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/quotes/{quote}/pdf', QuotePdfController::class)
         ->name('quotes.pdf');
});
