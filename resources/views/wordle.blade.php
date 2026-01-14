<!DOCTYPE html>
<html>
<body>

<h1>Wordle</h1>

<form method="POST">
    @csrf
    <input type="text" name="guess"  maxlength="1" class="char" required>
    <input type="text" name="guess2" maxlength="1" class="char" required>
    <input type="text" name="guess3" maxlength="1" class="char" required>
    <input type="text" name="guess4" maxlength="1" class="char" required>
    <input type="text" name="guess5" maxlength="1" class="char" required>

    <button type="submit">Submit</button>
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

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laravel";

// echo "<p>Het woord heeft " . strlen($wordToGuess) . " letters.</p>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userGuess = strtolower($_POST['guess'] . $_POST['guess2'] . $_POST['guess3'] . $_POST['guess4'] . $_POST['guess5']);
    if (strlen($userGuess) !== 5) {
        $result = "Please enter exactly 5 letters.";
    } elseif ($userGuess === $wordToGuess) {
        $result = "Congratulations! You've guessed the word!";
    } else {
        $result = "Incorrect guess. Try again!";
    }
    
}
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
            $wordToGuess = strtolower($row['woord']);
        } else {
            $wordToGuess = "abcde"; // als geen woord in database
        }
    } catch (PDOException $e) {
        echo "Fout bij verbinden: " . $e->getMessage();
        $wordToGuess = "abcde"; // als er geen db verbinding is
    }
    echo "<p>Debug: Het te raden woord is '" . htmlspecialchars($wordToGuess) . "'</p>";
    $result = "niks aan gegeven";


echo "<p>" . $result . "</p>";
?>

</html>
