<?php

use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/', function () {
        return view('wordle');
    })->name('wordle');

Route::post('/', function (Request $request) {
        return view('wordle');
    })->name('wordle');


