
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Velora Drive | Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/index.css?v=3">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        .error-message {
            color: #b42318;
            background: #fef3f2;
            border: 1px solid #fecdca;
            border-radius: 8px;
            padding: 12px 15px;
            margin: 0 0 18px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>

<body>

<nav>
    <div class="logo">
        Velora <span>Drive</span>
    </div>

    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="vehicles.php">Vehicles</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>

    <a href="register.php" class="register-btn">Register</a>
</nav>

<div class="container">

    <div class="left">

        <h1>Welcome Back</h1>

        <p>
            Sign in to continue your premium vehicle rental experience.
        </p>

        <?php if (!empty($error)): ?>
            <div class="error-message" role="alert">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="login_action.php" method="POST">

            <div class="input-group">
                <label for="email">Email Address</label>

                <i class="fa-solid fa-envelope"></i>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    autocomplete="email"
                    required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>

                <i class="fa-solid fa-lock"></i>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required>
            </div>

            <div class="options">
                <label>
                    <input type="checkbox" name="remember_me" value="1">
                    Remember Me
                </label>

                <a href="forgot_password.php">Forgot Password?</a>
            </div>

            <button type="submit" name="login" class="login-btn">
                Login
            </button>

        </form>

        <div class="signup">
            Don't have an account?
            <a href="register.php">Create Account</a>
        </div>

    </div>

    <div class="right">
        <img src="images/car.jpg" alt="Luxury Cars">
    </div>

</div>

</body>
</html>
```