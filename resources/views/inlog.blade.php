<?php
session_start();

// Bepaal de pagina van afkomst
$referrer = $_POST['referrer'] ?? $_SERVER['HTTP_REFERER'] ?? '/';
// Valideer de referrer om security issues te voorkomen
if (filter_var($referrer, FILTER_VALIDATE_URL)) {
    // OK
} else {
    $referrer = '/';
}
?>

<html class="login">
<head>
    <title>Wordle</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <h1>login</h1>

    <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "laravel";
    ?>

    <form method="POST">
        @csrf
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="hidden" name="referrer" value="<?php echo htmlspecialchars($referrer); ?>">
        <button type="submit">Login</button>
    </form>
    <form method="GET" action="">
        <input type="hidden" name="register" value="1">
        <input type="hidden" name="referrer" value="<?php echo htmlspecialchars($referrer); ?>">
        <button type="submit">aanmelden</button>
    </form>

</body>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = trim($_POST['username'] ?? '');
    $inputPassword = trim($_POST['password'] ?? '');

    try {
        // Maak verbinding met de database
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Bereid en voer de query uit om de gebruiker te vinden
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $inputUsername);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($inputPassword, $user['password'])) {
            // Stel sessievariabelen in
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect terug naar de pagina van afkomst
            header("Location: " . $referrer);
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } catch (PDOException $e) {
        echo "Fout bij verbinden: " . $e->getMessage();
    }
}
?>
</html>