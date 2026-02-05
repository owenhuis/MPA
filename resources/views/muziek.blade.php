<?php
session_start();
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

        if ($row) {
            $_SESSION['songToGuess'] = strtolower($row['titel']);
            $songToGuess = $_SESSION['songToGuess'];
        } else {
            $songToGuess = "abcde"; // als geen song in database
        }


    } catch (PDOException $e) {
        echo "Fout bij verbinden: " . $e->getMessage();
        $songToGuess = "abcde"; // als er geen db verbinding is
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
    <!-- reset button -->
    <div>
        <form method="GET">
            <button type="submit">Reset</button>
        </form>
    </div>

</body>



<?php
$result = "niets ingevuld";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userGuess = strtoupper(trim($_POST['userGuess'] ?? ''));
} else {
    $userGuess = '';
}


if (!isset($_SESSION['gameOver'])) {
    $_SESSION['gameOver'] = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['gameOver'] === false) {
    $songToGuess = $_SESSION['songToGuess'];
    $maxAttempts = 6;
    $_SESSION['attempts'] = $_SESSION['attempts'] ?? 0;
    $attempts = $_SESSION['attempts'];

    $attempts++;
    $_SESSION['attempts'] = $attempts;

    if ($userGuess === $songToGuess) {
        $result = "🎉 Goed gedaan! Je hebt het woord geraden in $attempts pogingen.";
        $_SESSION['gameOver'] = true;
        // points for this round
        $points = max(0, (7 - $attempts) * 1000);
        if (!empty($_SESSION['user_id'])) {
            // persist to leaderboard
            $stmtIns = $pdo->prepare("INSERT INTO leaderboard_scores (user_id, name, game, score, created_at, updated_at) VALUES (:user_id, :name, 'muziek', :score, NOW(), NOW())");
            $stmtIns->bindParam(':user_id', $_SESSION['user_id']);
            $stmtIns->bindParam(':name', $_SESSION['username']);
            $stmtIns->bindParam(':score', $points);
            $stmtIns->execute();
            // keep session user score
            $_SESSION['muziek_score'] = ($_SESSION['muziek_score'] ?? 0) + $points;
        } else {
            $_SESSION['guest_scores'][$_SESSION['songToGuess']] = $points;
        }

    } elseif ($attempts >= $maxAttempts) {
        $result = "💀 Game over! Het woord was: <b>$songToGuess</b>";
        $_SESSION['gameOver'] = true;

    } else {
        $remaining = $maxAttempts - $attempts;
        $result = "❌ Fout! Nog $remaining pogingen over.";
    }        
}
echo "<p>" . $result . "</p>";