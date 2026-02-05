<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - {{ ucfirst($game) }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header>
        <h1>Leaderboard - {{ ucfirst($game) }}</h1>
        <button onclick="window.location='{{ route('welcome') }}'">Terug</button>
    </header>

    <div class="container">
        @if(count($scores) > 0)
            <ol>
            @foreach($scores as $s)
                <li>{{ $s->name }} — {{ $s->score }} @if($s->user_id) <small>(ingelogd)</small> @endif</li>
            @endforeach
            </ol>
        @else
            <p>Geen scores gevonden voor deze game.</p>
        @endif
    </div>
</body>
</html>