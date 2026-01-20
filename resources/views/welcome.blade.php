<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container">
        <h1>Welcome to my multi game page</h1>
    </div>
    <div class="toGames">
        <button onclick="window.location='{{ route('wordle') }}'"> wordle</button>
    </div>
</body>
</html>