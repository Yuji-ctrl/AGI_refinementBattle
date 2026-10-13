<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HaikuController;

Route::get('/', [HaikuController::class, 'index'])->name('haiku.index');
Route::post('/battle', [HaikuController::class, 'battle'])->name('haiku.battle');