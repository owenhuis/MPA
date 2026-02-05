<?php
session_start();

// Bepaal de pagina van afkomst
$referrer = $_POST['referrer'] ?? $_GET['referrer'] ?? $_SERVER['HTTP_REFERER'] ?? '/';
// Valideer de referrer om security issues te voorkomen
if (filter_var($referrer, FILTER_VALIDATE_URL)) {
    // OK
} else {
    $referrer = '/';
}

$register = $_GET['register'] ?? 0;
$error = '';
$success = '';

// Database verbinding
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "laravel";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $dbusername, $dbpassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($action === 'register') {
            $inputUsername = trim($_POST['username'] ?? '');
            $inputEmail = trim($_POST['email'] ?? '');
            $inputPassword = trim($_POST['password'] ?? '');
            $inputPasswordConfirm = trim($_POST['password_confirm'] ?? '');

            if (empty($inputUsername) || empty($inputEmail) || empty($inputPassword)) {
                $error = "Alle velden zijn verplicht.";
            } elseif ($inputPassword !== $inputPasswordConfirm) {
                $error = "Wachtwoorden komen niet overeen.";
            } elseif (strlen($inputPassword) < 6) {
                $error = "Wachtwoord moet minimaal 6 karakters zijn.";
            } else {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
                $stmt->bindParam(':username', $inputUsername);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $error = "Username bestaat al.";
                } else {
                    $hashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at, updated_at) VALUES (:username, :email, :password, NOW(), NOW())");
                    $stmt->bindParam(':username', $inputUsername);
                    $stmt->bindParam(':email', $inputEmail);
                    $stmt->bindParam(':password', $hashedPassword);
                    $stmt->execute();
                    
                    $success = "Account succesvol aangemaakt! Je bent nu ingelogd.";
                    
                    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = :username LIMIT 1");
                    $stmt->bindParam(':username', $inputUsername);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    $stmt = $pdo->prepare("INSERT INTO scores (naam, wordle_score, muziek_score) VALUES (:naam, 0, 0)");
                    $stmt->bindParam(':naam', $inputUsername);
                    $stmt->execute();

                    // Migrate guest favorites into the persistent favorites table
                    if (!empty($_SESSION['guest_favorites']) && is_array($_SESSION['guest_favorites'])) {
                        foreach ($_SESSION['guest_favorites'] as $game_id) {
                            $insertFav = $pdo->prepare("INSERT IGNORE INTO favorites (user_id, game_id, created_at, updated_at) VALUES (:user_id, :game_id, NOW(), NOW())");
                            $insertFav->bindParam(':user_id', $user['id']);
                            $insertFav->bindParam(':game_id', $game_id);
                            $insertFav->execute();
                        }
                        unset($_SESSION['guest_favorites']);
                    }

                    // Move guest scores into user session and persist them to leaderboard for this user
                    if (!empty($_SESSION['guest_scores']) && is_array($_SESSION['guest_scores'])) {
                        foreach ($_SESSION['guest_scores'] as $game_id => $score) {
                            // find game slug by id
                            $gstmt = $pdo->prepare("SELECT slug FROM games WHERE id = :id LIMIT 1");
                            $gstmt->bindParam(':id', $game_id);
                            $gstmt->execute();
                            $g = $gstmt->fetch(PDO::FETCH_ASSOC);
                            $slug = $g['slug'] ?? 'unknown';

                            $insertL = $pdo->prepare("INSERT INTO leaderboard_scores (user_id, name, game, score, created_at, updated_at) VALUES (:user_id, :name, :game, :score, NOW(), NOW())");
                            $insertL->bindParam(':user_id', $user['id']);
                            $insertL->bindParam(':name', $user['username']);
                            $insertL->bindParam(':game', $slug);
                            $insertL->bindParam(':score', $score);
                            $insertL->execute();
                        }
                        $_SESSION['user_scores'] = $_SESSION['guest_scores'];
                        unset($_SESSION['guest_scores']);
                    }

                    header("Location: " . $referrer);
                    exit();
                }
            }
        } elseif ($action === 'login') {
            $inputUsername = trim($_POST['username'] ?? '');
            $inputPassword = trim($_POST['password'] ?? '');

            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $inputUsername);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($inputPassword, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Migrate guest favorites and scores to user's session/persistent data
                if (!empty($_SESSION['guest_favorites']) && is_array($_SESSION['guest_favorites'])) {
                    foreach ($_SESSION['guest_favorites'] as $game_id) {
                        $insertFav = $pdo->prepare("INSERT IGNORE INTO favorites (user_id, game_id, created_at, updated_at) VALUES (:user_id, :game_id, NOW(), NOW())");
                        $insertFav->bindParam(':user_id', $user['id']);
                        $insertFav->bindParam(':game_id', $game_id);
                        $insertFav->execute();
                    }
                    unset($_SESSION['guest_favorites']);
                }
                if (!empty($_SESSION['guest_scores']) && is_array($_SESSION['guest_scores'])) {
                    $_SESSION['user_scores'] = $_SESSION['guest_scores'];
                    unset($_SESSION['guest_scores']);
                }

                header("Location: " . $referrer);
                exit();
            } else {
                $error = "Ongeldige gebruikersnaam of wachtwoord.";
            }
        }
    } catch (PDOException $e) {
        $error = "Databasefout: " . $e->getMessage();
    }
}
?>

<html class="login">
<head>
    <title>Wordle</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <?php if ($error): ?>
        <div style="color: red; margin: 10px; padding: 10px; border: 1px solid red; background: #fee;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="color: green; margin: 10px; padding: 10px; border: 1px solid green; background: #efe;">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($register == 1): ?>
        <h1>Registreren</h1>

        <form method="POST">
            @csrf
            <input type="hidden" name="action" value="register">
            <label for="reg_username">Username:</label>
            <input type="text" id="reg_username" name="username" required>
            <br>
            <label for="reg_email">Email:</label>
            <input type="email" id="reg_email" name="email" required>
            <br>
            <label for="reg_password">Password:</label>
            <input type="password" id="reg_password" name="password" required>
            <br>
            <label for="reg_password_confirm">Bevestig Password:</label>
            <input type="password" id="reg_password_confirm" name="password_confirm" required>
            <br>
            <input type="hidden" name="referrer" value="<?php echo htmlspecialchars($referrer); ?>">
            <button type="submit">Registreren</button>
        </form>
        <a class="secondary" href="?referrer=<?php echo urlencode($referrer); ?>">Terug naar Login</a>
    <?php else: ?>
        <h1>Login</h1>

        <form method="POST">
            @csrf
            <input type="hidden" name="action" value="login">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="hidden" name="referrer" value="<?php echo htmlspecialchars($referrer); ?>">
            <button type="submit">Login</button>
        </form>
        <a class="secondary" href="?register=1&referrer=<?php echo urlencode($referrer); ?>">Aanmelden</a>
    <?php endif; ?>

</body>
</html>