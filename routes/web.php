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
                (object)['id' => 3, 'name' => 'Rock Paper Scissors', 'slug' => 'rock-paper-scissors', 'route' => 'rps', 'working' => TRUE],
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
            (object)['id' => 3, 'name' => 'Rock Paper Scissors', 'slug' => 'rock-paper-scissors', 'route' => 'rps', 'working' => TRUE],
        ]);
        $message = 'Database niet beschikbaar of tabellen ontbreken. Games worden tijdelijk lokaal geladen.';
        $favoriteIds = $_SESSION['guest_favorites'] ?? [];
        if (!is_array($favoriteIds)) $favoriteIds = [];
    }

    return view('welcome', ['games' => $games, 'favoriteIds' => $favoriteIds, 'message' => $message ?? null]);
})->name('welcome');

// Favorite toggle (werkt ook voor gasten)
Route::post('/games/{id}/favorite', function (Request $request, $id) {
    session_start();
    $userId = $_SESSION['user_id'] ?? null;
    $id = (int)$id;
    
    // If a session user_id is set but the user record no longer exists,
    // clear the session user and treat the request as a guest to avoid FK errors.
    if ($userId) {
        $userExists = DB::table('users')->where('id', $userId)->exists();
        if (!$userExists) {
            unset($_SESSION['user_id']);
            unset($_SESSION['username']);
            $userId = null;
        }
    }

    // Check if game exists
    $gameExists = DB::table('games')->where('id', $id)->exists();
    if (!$gameExists) {
        return redirect()->back()->with('error', 'Game niet gevonden.');
    }
    
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

// tijdelijke user score opslag
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

// favorieten pagina
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

// Game info (i)
Route::get('/games/{id}/info', function ($id) {
    $game = DB::table('games')->where('id', $id)->first();
    if (!$game) {
        return response()->json(['error' => 'Game not found'], 404);
    }
    return response()->json([
        'name' => $game->name,
        'description' => $game->description ?? 'Geen beschrijving beschikbaar.'
    ]);
})->name('games.info');

// Leaderboard info 
Route::get('/leaderboard/{game?}', function ($game = 'wordle') {
    try {
        $gameKey = strtolower(trim($game));
        // If a games table entry exists for this slug, use its route/key (e.g., rock-paper-scissors -> rps)
        $gameRow = DB::table('games')->whereRaw('LOWER(slug) = ?', [$gameKey])->first();
        if ($gameRow && !empty($gameRow->route)) {
            $gameKey = strtolower($gameRow->route);
        }

        $scores = DB::table('leaderboard_scores')
            ->whereRaw('LOWER(game) = ?', [$gameKey])
            ->orderByDesc('score')
            ->limit(10)
            ->get();

        // If no scores found, try fallback where game contains the key (handles partial matches)
        if ($scores->isEmpty()) {
            $scores = DB::table('leaderboard_scores')
                ->where('game', 'like', "%{$gameKey}%")
                ->orderByDesc('score')
                ->limit(10)
                ->get();
        }
    } catch (\Throwable $e) {
        // Table missing or DB error: fallback to seeded in-memory values
        $scores = collect([]);
    }

    return view('leaderboard', ['scores' => $scores, 'game' => $game]);
})->name('leaderboard');

// new woord
Route::get('/wordle', function () {
    return view('wordle');
})->name('wordle');

// woord gegokt
Route::post('/wordle', function (Request $request) {
    return view('wordle');
})->name('wordle.post');

//new nummer
Route::get('/muziek', function () {
    return view('muziek');
})->name('muziek');

// nummer gegokt
Route::post('/muziek', function (Request $request) {
    return view('muziek');
})->name('muziek.post');

//new rps
Route::get('/rps', function () {
    return view('rps');
})->name('rps');

// rps gegokt
Route::post('/rps', function (Request $request) {
    return view('rps');
})->name('rps.post');

// rps score save
Route::post('/rps/save', function (Request $request) {
    session_start();
    $userId = $_SESSION['user_id'] ?? null;
    $score = $request->input('score', 0);

    if ($userId) {
        $_SESSION['rps_score'] = ($userId ? (DB::table('leaderboard_scores')->where('user_id', $userId)->where('game', 'rps')->sum('score') ?? 0) : 0) + $score;
        DB::table('leaderboard_scores')->insert([
            'user_id' => $userId,
            'name' => $_SESSION['username'] ?? 'User',
            'game' => 'rps',
            'score' => $score,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Update (or create) aggregate score in `scores` table for this user
        $userName = $_SESSION['username'] ?? null;
        if ($userName) {
            $existing = DB::table('scores')->where('naam', $userName)->first();
            if ($existing) {
                DB::table('scores')->where('naam', $userName)->update([
                    'rps_score' => ($existing->rps_score ?? 0) + $score,
                ]);
            } else {
                DB::table('scores')->insert([
                    'naam' => $userName,
                    'wordle_score' => 0,
                    'muziek_score' => 0,
                    'rps_score' => $score,
                ]);
            }
        }
    } else {
        // Store guest score in session
        $_SESSION['guest_scores']['rps'] = ($_SESSION['guest_scores']['rps'] ?? 0) + $score;
        // Also persist anonymous entry so leaderboard shows guest submissions
        DB::table('leaderboard_scores')->insert([
            'user_id' => null,
            'name' => $_SESSION['username'] ?? 'Guest',
            'game' => 'rps',
            'score' => $score,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    return response()->json(['success' => true]);
})->name('rps.save');

//inlog
Route::get('/inlog', function () {
    return view('inlog');
})->name('inlog');

// registratie
Route::post('/inlog', function (Request $request) {
    return view('inlog');
})->name('inlog.post');