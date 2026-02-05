<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Favorieten</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <h1>Mijn Favorieten</h1>
        <button onclick="window.location='{{ route('welcome') }}'">Terug</button>
    </header>

    <div class="container">
        @if(count($games) > 0)
            @foreach($games as $game)
                <div class="toGames">
                    <a href="{{ url($game->route) }}"><button>{{ $game->name }}</button></a>
                    <form method="POST" action="{{ route('games.favorite', $game->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit">Verwijder</button>
                    </form>
                </div>
            @endforeach
        @else
            <p>Je hebt nog geen favorieten.</p>
        @endif
    </div>
</body>
</html>