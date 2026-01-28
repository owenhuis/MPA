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


Route::get('/muziek', function () {
    return view('muziek');
})->name('muziek');

Route::post('/muziek', function (Request $request) {
    return view('muziek');
})->name('muziek.post');

Route::get('/inlog', function () {
    return view('inlog');
})->name('inlog');

Route::post('/inlog', function (Request $request) {
    return view('inlog');
})->name('inlog.post');