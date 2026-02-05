<?php
session_start();
?>
<html class="wordle">
<head>
    <title>Wordle</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>


<h1>Wordle</h1>

<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laravel";

 // Maak verbinding met de database
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// echo "<p>Het woord heeft " . strlen($wordToGuess) . " letters.</p>";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // reset kansen
    $_SESSION['attempts'] = 0;
    $_SESSION['maxAttempts'] = 6;
    $_SESSION['gameOver'] = false;
    $_SESSION['guesses'] = [];


    try {
        
        // haal het hoogste gebruikte id op
        $stmt = $pdo->prepare("SELECT * FROM wordle ORDER BY id desc LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // kies een random id tussen 1 en het hoogste id
        $rndm = random_int(1,$row['id']);
        // haal het woord met dat id op
        $stmt = $pdo->prepare("SELECT * FROM wordle WHERE id = $rndm LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $_SESSION['wordToGuess'] = strtolower($row['woord']);
            $wordToGuess = $_SESSION['wordToGuess'];
        } else {
            $wordToGuess = "abcde"; // als geen woord in database
        }
    } catch (PDOException $e) {
        echo "Fout bij verbinden: " . $e->getMessage();
        $wordToGuess = "abcde"; // als er geen db verbinding is
    }
}
?>


<?php
    function bepaalKleuren($guess, $word) {
    // begin alles met grijs
    $result = array_fill(0, 5, 'gray');
    // alle letters in arrays
    $wordLetters = str_split($word);
    $guessLetters = str_split($guess);

    // groen
    for ($i = 0; $i < 5; $i++) {
        // zit de letter op de juiste plek
        if ($guessLetters[$i] === $wordLetters[$i]) {
            $result[$i] = 'green';
            $wordLetters[$i] = null; // letter kan niet herhaald worden
            $guessLetters[$i] = null; // letter al verwerkt
        }
    }

    //geel
    for ($i = 0; $i < 5; $i++) {
        // moet de letter nog gecheckt worden
        if ($guessLetters[$i] !== null) {
            // checkt de positie van de letter in het woord
            $pos = array_search($guessLetters[$i], $wordLetters);
            // bestaat de letter in het woord( ja / True , nee / False)
            if ($pos !== false) {
                $result[$i] = 'yellow';
                $wordLetters[$pos] = null; // letter kan niet herhaald worden
            }
        }
    }

    return $result;
}


    // echo "<p>Debug: Het te raden woord is '" . $_SESSION['wordToGuess'] . "'</p>";
    $result = "niks aan gegeven";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
    $wordToGuess = $_SESSION['wordToGuess'];
    $_SESSION['attempts']++;

    $attempts = $_SESSION['attempts'];
    $maxAttempts = $_SESSION['maxAttempts'];

    $userGuess = strtolower(
        $_POST['guess'] .
        $_POST['guess2'] .
        $_POST['guess3'] .
        $_POST['guess4'] .
        $_POST['guess5']
    );
    $kleuren = bepaalKleuren($userGuess, $wordToGuess);

    $_SESSION['guesses'][] = [
        'word' => $userGuess,
        'colors' => $kleuren
    ];

    if (!empty($_SESSION['guesses'])) {
                ?>
                <div class="wordle-board">
                <?php
                for ($row = 0; $row < 6; $row++) {

                    echo '<div class="row">';

                    if (isset($_SESSION['guesses'][$row])) {
                        $guess = $_SESSION['guesses'][$row];

                        for ($i = 0; $i < 5; $i++) {
                            echo "<div class='tile {$guess['colors'][$i]}'>"
                                . strtoupper($guess['word'][$i]) .
                                "</div>";
                        }
                    } else {
                        for ($i = 0; $i < 5; $i++) {
                            echo "<div class='tile'></div>";
                        }
                    }

                    echo '</div>';
                }
                ?>
                </div>
                <?php
                ;
            }
            echo "<br>";
    if ($userGuess === $wordToGuess and $_SESSION['gameOver'] === False and $attempts >= 1) {
        $stmt = $pdo->prepare("SELECT wordle_score FROM scores WHERE naam = :naam LIMIT 1");
        $stmt->bindParam(':naam', $_SESSION['username']);
        $stmt->execute();
        $old_score = $stmt->fetch(PDO::FETCH_ASSOC);
        $result = "🎉 Goed gedaan! Je hebt het woord geraden in $attempts pogingen.";
        $_SESSION['gameOver'] = true;

        // points for this round (higher is better)
        $points = max(0, (7 - $attempts) * 1000);

        // update cumulative score table if user logged in
        if (!empty($_SESSION['username'])) {
            $_SESSION['wordle_score'] = ($old_score['wordle_score'] ?? 0) + $points;
            $stmt = $pdo->prepare("UPDATE scores SET wordle_score = :wordle_score WHERE naam = :naam");
            $stmt->bindParam(':wordle_score', $_SESSION['wordle_score']);
            $stmt->bindParam(':naam', $_SESSION['username']);
            $stmt->execute();

            // insert into leaderboard for logged-in user
            if (!empty($_SESSION['user_id'])) {
                $ins = $pdo->prepare("INSERT INTO leaderboard_scores (user_id, name, game, score, created_at, updated_at) VALUES (:user_id, :name, 'wordle', :score, NOW(), NOW())");
                $ins->bindParam(':user_id', $_SESSION['user_id']);
                $ins->bindParam(':name', $_SESSION['username']);
                $ins->bindParam(':score', $points);
                $ins->execute();
            }
        } else {
            // guest: store in session only
            $_SESSION['guest_scores'][$_SESSION['wordToGuess']] = $points;
        }

    } elseif ($attempts >= $maxAttempts) {
        $result = "💀 Game over! Het woord was: <b>$wordToGuess</b>";
        $_SESSION['gameOver'] = true;

    } else {
        $remaining = $maxAttempts - $attempts;
        $result = "❌ Fout! Nog $remaining pogingen over.";
    }        
}
?>

<?php $gameOver = $_SESSION['gameOver'] ?? false; ?>
<div class="guess-inputs">
    <form method="POST">
        @csrf
        <input type="text" name="guess" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>
        <input type="text" name="guess2" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>
        <input type="text" name="guess3" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>
        <input type="text" name="guess4" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>
        <input type="text" name="guess5" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>

        <button type="submit" <?= $gameOver ? 'disabled' : '' ?>>Submit</button>
    </form>
</div>
<form method="GET">
    <button type="submit">🔄 Nieuw spel</button>
</form>



<!--  JavaScript om automatisch naar het volgende invoerveld te gaan -->
<script>
    const inputs = document.querySelectorAll('.char');

    inputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && inputs[index + 1]) {
                inputs[index + 1].focus();
            }
        });

        // Optioneel: backspace → terug naar vorige input
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value === '' && inputs[index - 1]) {
                inputs[index - 1].focus();
            }
        });
    });
</script>
<?php
echo "<p>" . $result . "</p>";
?>
<div class="toGames">
    <button onclick="window.location='{{ route('welcome') }}'"> Terug naar welkom</button>
</div>
</html>
