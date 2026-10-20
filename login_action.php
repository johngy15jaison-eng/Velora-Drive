
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $_SESSION['error'] = "Please enter your email and password.";
        header("Location: index.php");
        exit;
    }

    // Safely search for the user by email
    $sql = "SELECT fullname, email, password FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        error_log("Login query preparation failed: " . mysqli_error($conn));
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: index.php");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if ($row && password_verify($password, $row['password'])) {

        // Regenerate session ID after successful login
        session_regenerate_id(true);

        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['email'] = $row['email'];

        header("Location: home.php");
        exit;

    } else {
        // Use a generic message for security
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: index.php");
        exit;
    }
}

// If accessed without submitting the login form
header("Location: index.php");
exit;
