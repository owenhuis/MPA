<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Route::get('/', function (Request $request) {
    session_start();
    $userId = $_SESSION['user_id'] ?? null;
    $favoriteIds = [];
    try {
        $games = DB::table('games')->get();

        // If table exists but is empty, fallback to default list and show a helpful message
        if ($games->isEmpty()) {
            $games = collect([
                (object)['id' => 1, 'name' => 'Wordle', 'slug' => 'wordle', 'route' => 'wordle', 'working' => TRUE],
                (object)['id' => 2, 'name' => 'Muziek', 'slug' => 'muziek', 'route' => 'muziek', 'working' => FALSE],
            ]);
            $message = 'Geen favoriete games gevonden.';
        } else {
            $message = null;
        }

        if ($userId) {
            $favoriteIds = DB::table('favorites')->where('user_id', $userId)->pluck('game_id')->toArray();
        } else {
            $favoriteIds = $_SESSION['guest_favorites'] ?? [];
            if (!is_array($favoriteIds)) $favoriteIds = [];
        }
    } catch (\Throwable $e) {
        // DB or tables missing — fall back to a minimal in-memory list so the page still works
        $games = collect([
            (object)['id' => 1, 'name' => 'Wordle', 'slug' => 'wordle', 'route' => 'wordle', 'working' => TRUE],
            (object)['id' => 2, 'name' => 'Muziek', 'slug' => 'muziek', 'route' => 'muziek', 'working' => FALSE],
        ]);
        $message = 'Database niet beschikbaar of tabellen ontbreken. Games worden tijdelijk lokaal geladen.';
        $favoriteIds = $_SESSION['guest_favorites'] ?? [];
        if (!is_array($favoriteIds)) $favoriteIds = [];
    }

    return view('welcome', ['games' => $games, 'favoriteIds' => $favoriteIds, 'message' => $message ?? null]);
})->name('welcome');

// Favorite toggle (supports guests via session)
Route::post('/games/{id}/favorite', function (Request $request, $id) {
    session_start();
    $userId = $_SESSION['user_id'] ?? null;
    $id = (int)$id;
    if ($userId) {
        $exists = DB::table('favorites')->where('user_id', $userId)->where('game_id', $id)->exists();
        if ($exists) {
            DB::table('favorites')->where('user_id', $userId)->where('game_id', $id)->delete();
        } else {
            DB::table('favorites')->insert(['user_id' => $userId, 'game_id' => $id, 'created_at' => now(), 'updated_at' => now()]);
        }
    } else {
        // Guest: store in PHP session
        $guestFav = $_SESSION['guest_favorites'] ?? [];
        if (!is_array($guestFav)) $guestFav = [];
        if (in_array($id, $guestFav)) {
            $guestFav = array_values(array_diff($guestFav, [$id]));
        } else {
            $guestFav[] = $id;
        }
        $_SESSION['guest_favorites'] = $guestFav;
    }

    return redirect()->back();
})->name('games.favorite');

// Store temporary score (guest or session-based for logged in users) and persist for logged in users to leaderboard
Route::post('/games/{id}/score', function (Request $request, $id) {
    session_start();
    $score = $request->input('score');
    $userId = $_SESSION['user_id'] ?? null;
    $id = (int)$id;

    // determine game slug/name
    $game = DB::table('games')->where('id', $id)->first();
    $gameSlug = $game->slug ?? 'unknown';

    if ($userId) {
        $_SESSION['user_scores'][$id] = $score;
        // persist to leaderboard
        DB::table('leaderboard_scores')->insert([
            'user_id' => $userId,
            'name' => $_SESSION['username'] ?? 'User',
            'game' => $gameSlug,
            'score' => $score,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } else {
        $_SESSION['guest_scores'][$id] = $score;
    }
    return redirect()->back();
})->name('games.score');

Route::get('/favorites', function () {
    session_start();
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        $games = DB::table('games')->join('favorites', 'games.id', '=', 'favorites.game_id')->where('favorites.user_id', $userId)->select('games.*')->get();
    } else {
        $ids = $_SESSION['guest_favorites'] ?? [];
        if (!is_array($ids)) $ids = [];
        if (count($ids) > 0) {
            $games = DB::table('games')->whereIn('id', $ids)->get();
        } else {
            $games = collect([]);
        }
    }
    return view('favorites', ['games' => $games]);
})->name('favorites');

// Leaderboard
Route::get('/leaderboard/{game?}', function ($game = 'wordle') {
    $scores = DB::table('leaderboard_scores')->where('game', $game)->orderByDesc('score')->limit(10)->get();
    return view('leaderboard', ['scores' => $scores, 'game' => $game]);
})->name('leaderboard');

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