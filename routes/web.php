<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/report', [ReportController::class, 'index'])->name('report');

Route::get('/events', [EventController::class, 'index'])->name('events.index');