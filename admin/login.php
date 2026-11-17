
<?php
session_start();

require_once __DIR__ . '/../config/db.php';

// Redirect already authenticated administrators.
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

if (empty($_SESSION['login_csrf'])) {
    $_SESSION['login_csrf'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) ||
        !hash_equals($_SESSION['login_csrf'], $token)) {
        $error = 'Invalid request. Please refresh the page.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare(
            'SELECT id, username, password_hash
             FROM admins
             WHERE username = :username
             LIMIT 1'
        );
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        if ($admin &&
            password_verify($password, $admin['password_hash'])) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['logout_csrf'] = bin2hex(random_bytes(32));

            unset($_SESSION['login_csrf']);

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Walayta Sodo Kalehiwot Church</title>
    <link rel="stylesheet" href="../assets/css/admin-login.css">
</head>
<body>
<main class="login-card">
    <h1>Church Admin Login</h1>
    <p class="muted">Walayta Sodo Kalehiwot Church</p>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars(
                   $_SESSION['login_csrf'],
                   ENT_QUOTES,
                   'UTF-8'
               ) ?>">

        <label for="username">Username</label>
        <input id="username" name="username"
               autocomplete="username" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password"
               autocomplete="current-password" required>

        <button type="submit">Log In</button>
    </form>

    <p class="muted">
        <a href="../index.php">Return to church website</a>
    </p>
</main>
</body>
</html>
