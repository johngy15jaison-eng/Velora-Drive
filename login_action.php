
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
    header('Location: index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['error'] = 'Please enter your email and password.';
    header('Location: index.php');
    exit;
}

try {
    $sql = 'SELECT fullname, email, password
            FROM users
            WHERE email = ?
            LIMIT 1';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['email'] = $user['email'];

        header('Location: home.php');
        exit;
    }

    $_SESSION['error'] = 'Invalid email or password.';
    header('Location: index.php');
    exit;

} catch (mysqli_sql_exception $e) {
    error_log('Velora Drive login error: ' . $e->getMessage());

    $_SESSION['error'] = 'Login is temporarily unavailable. Please try again.';
    header('Location: index.php');
    exit;
}
