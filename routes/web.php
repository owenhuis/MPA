<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/wordle', function () {
    return view('wordle');
})->name('wordle');

Route::post('/wordle', function (Request $request) {
    return view('wordle');
})->name('wordle.post');