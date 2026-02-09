<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <h1>Welcome Page</h1>

        <button onclick="window.location='{{ route('inlog') }}'"> Inloggen</button>
    </header>
    <div class="container">
        <h1>Welcome to my multi game page</h1>
        @if(!empty($message))
            <div class="flash">{{ $message }}</div>
        @endif
        @if(count($games) === 0)
            <div class="flash">Geen games beschikbaar. Voer <code>php artisan migrate --seed</code> uit om games toe te voegen.</div>
        @endif
    </div>
    <div class="favoriteFilters">
        <h2>Choose your game:</h2>
        <a href="{{ route('favorites') }}"><button>Mijn Favorieten</button></a>
    </div>

    @foreach($games as $game)
    <div class="toGames">
        @if(isset($game->working) && !$game->working)
        <a disabled><button>{{ $game->name }}</button></a>
        <a disabled style="margin-left:8px;"><button>Leaderboard</button></a>
        
            <span style="color: red; margin-left: 8px;">(under construction)</span>
            <form style="display:inline; margin-left:8px;">
                @csrf
                <button type="submit" disabled>{{ in_array($game->id, $favoriteIds) ? '★' : '☆' }}</button>
            </form>
        @else(isset($game->working) && $game->working)
        <a href="{{ url($game->route) }}"><button>{{ $game->name }}</button></a>
        <a href="{{ route('leaderboard', $game->slug) }}" style="margin-left:8px;"><button>Leaderboard</button></a>
            <form method="POST" action="{{ route('games.favorite', $game->id) }}" style="display:inline; margin-left:8px;">
                @csrf
                <button type="submit">{{ in_array($game->id, $favoriteIds) ? '★' : '☆' }}</button>
            </form>
        @endif
    </div>
    @endforeach
</body>
</html>