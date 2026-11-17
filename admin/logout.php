
<?php
session_start();

// Only accept logout requests submitted from the dashboard.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Validate the CSRF token.
$token = $_POST['csrf_token'] ?? '';
$expected = $_SESSION['logout_csrf'] ?? '';

if (
    !is_string($token) ||
    !is_string($expected) ||
    $expected === '' ||
    !hash_equals($expected, $token)
) {
    http_response_code(403);
    exit('Invalid logout request. Return to the dashboard and try again.');
}

// Clear all session data.
$_SESSION = [];

// Expire the session cookie.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'] ?? 'Lax'
    ]);
}

// Destroy the session.
session_destroy();

// Redirect to the login page.
header('Location: login.php');
exit;
