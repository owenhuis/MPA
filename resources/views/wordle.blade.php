<?php
session_start();
?>
<html>
<body>

<h1>Wordle</h1>

<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laravel";

// echo "<p>Het woord heeft " . strlen($wordToGuess) . " letters.</p>";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // reset kansen
    $_SESSION['attempts'] = 0;
    $_SESSION['maxAttempts'] = 6;
    $_SESSION['gameOver'] = false;

    try {
        // Maak verbinding met de database
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
    $result = array_fill(0, 5, 'gray');

    $wordLetters = str_split($word);
    $guessLetters = str_split($guess);

    // 1️⃣ Eerst: groen
    for ($i = 0; $i < 5; $i++) {
        if ($guessLetters[$i] === $wordLetters[$i]) {
            $result[$i] = 'green';
            $wordLetters[$i] = null; // letter opgebruikt
            $guessLetters[$i] = null;
        }
    }

    // 2️⃣ Daarna: geel
    for ($i = 0; $i < 5; $i++) {
        if ($guessLetters[$i] !== null) {
            $pos = array_search($guessLetters[$i], $wordLetters);
            if ($pos !== false) {
                $result[$i] = 'yellow';
                $wordLetters[$pos] = null; // letter opgebruikt
            }
        }
    }

    return $result;
}


    echo "<p>Debug: Het te raden woord is '" . $_SESSION['wordToGuess'] . "'</p>";
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

    for ($i = 0; $i < 5; $i++) {
        echo "<span style='display:inline-block;width:30px;height:30px;line-height:30px;
            text-align:center;margin:2px;background-color:{$kleuren[$i]};
            color:white;font-weight:bold;'>"
            . strtoupper($userGuess[$i]) .
            "</span>";
    }
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
?>

<?php $gameOver = $_SESSION['gameOver'] ?? false; ?>

<form method="POST">
    @csrf
    <input type="text" name="guess" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>
    <input type="text" name="guess2" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>
    <input type="text" name="guess3" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>
    <input type="text" name="guess4" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>
    <input type="text" name="guess5" maxlength="1" class="char" required <?= $gameOver ? 'disabled' : '' ?>>

    <button type="submit" <?= $gameOver ? 'disabled' : '' ?>>Submit</button>
</form>

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

</html>
