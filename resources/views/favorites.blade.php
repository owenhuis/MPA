<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Favorieten</title>
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
                .then(response => response.json())
                .then(data => {
                    document.getElementById('infoTitle').textContent = data.name;
                    document.getElementById('infoDescription').textContent = data.description;
                    document.getElementById('infoModal').style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
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
        <h1>Mijn Favorieten</h1>
        <button onclick="window.location='{{ route('welcome') }}'">Terug</button>
    </header>

    <div class="container">
        @if(count($games) > 0)
            @foreach($games as $game)
                <div class="toGames">
                    @if(isset($game->working) && !$game->working)
                        <a disabled><button>{{ $game->name }}</button></a>
                        <button onclick="openInfoModal({{ $game->id }})" style="margin-left:8px; background:#17a2b8; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;">ℹ️</button>
                        <span style="color: red; margin-left: 8px;">(under construction)</span>
                    @else
                    <a href="{{ url($game->route) }}"><button>{{ $game->name }}</button></a>
                    <button onclick="openInfoModal({{ $game->id }})" style="margin-left:8px; background:#17a2b8; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;">ℹ️</button>
                    @endif
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