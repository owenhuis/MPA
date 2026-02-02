<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header>
        <h1>Welcome Page</h1>

        <button onclick="window.location='{{ route('inlog') }}'"> Inloggen</button>
    </header>
    <div class="container">
        <h1>Welcome to my multi game page</h1>
    </div>
    <div class="favoriteFilters">
        <h2>Choose your game:</h2>
    </div>
    <div class="toGames">
        <a href="{{ route('wordle') }}"><button>wordle</button></a>
        <button>⭐</button>
    </div>
    <div class="toGames">
        <a href="{{ route('muziek') }}"><button>muziek</button></a>
        <button>⭐</button>
    </div>
</body>
</html>