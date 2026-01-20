<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('wordle');
});

Route::post('/', function (Request $request) {

    return view('wordle');
});