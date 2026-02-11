<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Info Modal -->
    <div id="infoModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div style="background:white; margin:50px auto; padding:30px; width:80%; max-width:500px; border-radius:10px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2 id="infoTitle" style="margin:0;"></h2>
                <button onclick="closeInfoModal()" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
            </div>
            <p id="infoDescription" style="margin-top:15px; line-height:1.6;"></p>
            <button onclick="closeInfoModal()" style="margin-top:20px; padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">Sluiten</button>
        </div>
    </div>
    
    <script>
        function openInfoModal(gameId) {
            fetch('/games/' + gameId + '/info')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    document.getElementById('infoTitle').textContent = data.name;
                    document.getElementById('infoDescription').textContent = data.description;
                    document.getElementById('infoModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching game info:', error);
                    document.getElementById('infoTitle').textContent = 'Info';
                    document.getElementById('infoDescription').textContent = 'Geen beschrijving beschikbaar.';
                    document.getElementById('infoModal').style.display = 'block';
                });
        }

        function openInfoModalFromData(el) {
            const name = el.dataset.gameName;
            const desc = el.dataset.gameDesc;
            const id = el.dataset.gameId;
            if (name || desc) {
                document.getElementById('infoTitle').textContent = name || 'Info';
                document.getElementById('infoDescription').textContent = desc || 'Geen beschrijving beschikbaar.';
                document.getElementById('infoModal').style.display = 'block';
            } else if (id) {
                openInfoModal(id);
            } else {
                document.getElementById('infoTitle').textContent = 'Info';
                document.getElementById('infoDescription').textContent = 'Geen beschrijving beschikbaar.';
                document.getElementById('infoModal').style.display = 'block';
            }
        }
        
        function closeInfoModal() {
            document.getElementById('infoModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('infoModal');
            if (event.target === modal) {
                closeInfoModal();
            }
        }
    </script>
    
    <header>
        <h1>Welcome Page</h1>
        @auth
            <span>Welkom, {{ auth()->users()->username }}</span>
        @else
            <button onclick="window.location='{{ route('inlog') }}'">Inloggen</button>
        @endauth
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
        <button onclick="openInfoModalFromData(this)" data-game-id="{{ $game->id }}" data-game-name="{{ $game->name }}" data-game-desc="{{ isset($game->description) ? e($game->description) : 'Geen beschrijving beschikbaar.' }}" style="margin-left:8px; background:#17a2b8; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;">ℹ️</button>
            <span style="color: red; margin-left: 8px;">(under construction)</span>
            <form method="POST" action="{{ route('games.favorite', $game->id) }}" style="display:inline; margin-left:8px;">
                @csrf
                <button type="submit">{{ in_array($game->id, $favoriteIds) ? '★' : '☆' }}</button>
            </form>
        @else(isset($game->working) && $game->working)
        <a href="{{ url($game->route) }}"><button>{{ $game->name }}</button></a>
        <a href="{{ route('leaderboard', $game->slug) }}" style="margin-left:8px;"><button>Leaderboard</button></a>
        <button onclick="openInfoModalFromData(this)" data-game-id="{{ $game->id }}" data-game-name="{{ $game->name }}" data-game-desc="{{ isset($game->description) ? e($game->description) : 'Geen beschrijving beschikbaar.' }}" style="margin-left:8px; background:#17a2b8; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;">ℹ️</button>
            <form method="POST" action="{{ route('games.favorite', $game->id) }}" style="display:inline; margin-left:8px;">
                @csrf
                <button type="submit">{{ in_array($game->id, $favoriteIds) ? '★' : '☆' }}</button>
            </form>
        @endif
    </div>
    @endforeach
</body>
</html>