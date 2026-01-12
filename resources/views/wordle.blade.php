<!DOCTYPE html>
<html>
<body>

<h1>Wordle</h1>

<form method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="text" name="guess" maxlength="1" required>
    <input type="text" name="guess2" maxlength="1" required>
    <input type="text" name="guess3" maxlength="1" required>
    <input type="text" name="guess4" maxlength="1" required>
    <input type="text" name="guess5" maxlength="1" required>
    <button type="submit">Submit</button>
</form>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laravel";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT * FROM wordle ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $wordToGuess = strtolower($row['woord']);
    } else {
        $wordToGuess = "abcde"; // fallback als geen woord in database
    }
} catch (PDOException $e) {
    echo "Fout bij verbinden: " . $e->getMessage();
    $wordToGuess = "abcde"; // fallback
}
echo "<p>Debug: Het te raden woord is '" . htmlspecialchars($wordToGuess) . "'</p>";
$result = "niks aan gegeven";

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
echo "<p>" . $result . "</p>";
?>

</html>
