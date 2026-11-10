<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/report', [ReportController::class, 'index'])->name('report');

Route::get('/events', [EventController::class, 'index'])->name('events.index');

Route::get('/events/filter', [EventController::class, 'filtered'])->name('events.filter');  

Route::get('/events/search', [EventController::class, 'search'])->name('events.search');