<!DOCTYPE html>
<html>
<body>

<h1>Wordle</h1>

<form method="POST">
    @csrf
    <input type="text" name="guess" maxlength="1" required>
    <input type="text" name="guess2" maxlength="1" required>
    <input type="text" name="guess3" maxlength="1" required>
    <input type="text" name="guess4" maxlength="1" required>
    <input type="text" name="guess5" maxlength="1" required>
    <button type="submit">Submit</button>
</form>

<?php
include 'resources/pdo/pdo.php';
$stmt = $pdo->prepare("SELECT * FROM wordle ORDER BY id");
$stmt->execute();
$wordToGuess = "abcde";
$result = "niks aan gegeven";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "post werkt";
    if (strlen($userGuess) !== 5) {
        $result = "Please enter exactly 5 letters.";
    } elseif (strtolower($userGuess) === $wordToGuess) {
        $result = "Congratulations! You've guessed the word!";
    } else {
        $result = "Incorrect guess. Try again!";
    }
    
}
echo "<p>" . $result . "</p>";
?>

</html>
