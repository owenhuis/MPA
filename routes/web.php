<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('wordle');
});

Route::post('/', function (Request $request) {
    $userGuess =
    request('guess') .
    request('guess2') .
    request('guess3') .
    request('guess4') .
    request('guess5');

    return view('wordle') -> with('userGuess', $userGuess);
});