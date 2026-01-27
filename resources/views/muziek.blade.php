<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laravel";
try {
        // Maak verbinding met de database
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // haal het hoogste gebruikte id op
        $stmt = $pdo->prepare("SELECT * FROM muziek ORDER BY id desc LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // kies een random id tussen 1 en het hoogste id
        $rndm = random_int(1,$row['id']);
        // haal het woord met dat id op
        $stmt = $pdo->prepare("SELECT * FROM muziek WHERE id = $rndm LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Fout bij verbinden: " . $e->getMessage();
        $wordToGuess = "abcde"; // als er geen db verbinding is
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muziek Page</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container">
        <h1>Welkom op de Muziek pagina</h1>
        <p>Hier raad je verschillende nummers</p>
    </div>
    <div class="toGames">
        <button onclick="window.location='{{ route('welcome') }}'"> Terug naar welkom</button>
    </div>

    <div>
        <audio controls>
            <source src="{{ asset($row['song_path']) }}" type="audio/mpeg">
        </audio>
    </div>
    <div>
        <form method="POST">
            @csrf
            <label for="userGuess">Raad het nummer:</label>
            <input type="text" id="userGuess" name="userGuess" required>
            <button type="submit">Submit</button>
        </form>
    </div>

</body>



<?php
$result = "";   
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userGuess = strtoupper(trim($_POST['userGuess'] ?? ''));
} else {
    $userGuess = '';
}


if (!isset($_SESSION['gameOver'])) {
    $_SESSION['gameOver'] = false;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$_SESSION['gameOver']) {
    $wordToGuess = "MUSIC";
    $maxAttempts = 6;
    $_SESSION['attempts'] = $_SESSION['attempts'] ?? 0;
    $attempts = $_SESSION['attempts'];


    $attempts++;
    if ($userGuess === $wordToGuess) {
        $result = "🎉 Goed gedaan! Je hebt het woord geraden in $attempts pogingen.";
        $_SESSION['gameOver'] = true;

    } elseif ($attempts >= $maxAttempts) {
        $result = "💀 Game over! Het woord was: <b>$wordToGuess</b>";
        $_SESSION['gameOver'] = true;

    } else {
        $remaining = $maxAttempts - $attempts;
        $result = "❌ Fout! Nog $remaining pogingen over.";
    }        
}
echo "<p>" . $result . "</p>";