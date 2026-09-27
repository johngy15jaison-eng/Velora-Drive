<?php
session_start();

require_once __DIR__ . '/includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if (empty($email)) {

        $message = "Please enter your registered email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, fullname FROM users WHERE email = ?"
        );

        if ($stmt) {

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                // Generate 6-digit reset code
                $code = random_int(100000, 999999);

                // Store reset information in session
                $_SESSION["reset_email"] = $email;
                $_SESSION["reset_code"] = (string)$code;
                $_SESSION["reset_expiry"] = time() + 600;

                /*
                 * Local testing:
                 * Store the code in session.
                 *
                 * In a real website, this code would
                 * normally be sent through email.
                 */
                $_SESSION["reset_display_code"] = (string)$code;

                // Automatically open Reset Password page
                header("Location: reset_password.php");
                exit;

            } else {

                $message = "Email address not found.";
            }

            $stmt->close();

        } else {

            $message = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Velora Drive | Forgot Password</title>

    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: #f7f7f7;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .forgot-container {
            width: 100%;
            max-width: 520px;
            padding: 25px;
        }

        .forgot-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 55px 45px;

            box-shadow:
                0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .forgot-card h1 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #111111;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #777777;
            font-size: 15px;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-size: 15px;
            font-weight: 500;
            color: #111111;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            height: 52px;

            border: 1px solid #dddddd;
            border-radius: 9px;

            padding: 0 15px;

            font-family: "Poppins", sans-serif;
            font-size: 14px;

            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #c8a43b;

            box-shadow:
                0 0 0 3px rgba(200, 164, 59, 0.12);
        }

        .reset-btn {
            width: 100%;
            height: 52px;

            border: none;
            border-radius: 9px;

            background: #c8a43b;
            color: #ffffff;

            font-family: "Poppins", sans-serif;
            font-size: 15px;
            font-weight: 500;

            cursor: pointer;
            transition: 0.3s;
        }

        .reset-btn:hover {
            background: #b28f2e;
        }

        .message {
            margin-top: 22px;
            padding: 13px 15px;

            border-radius: 8px;

            background: #fff1f1;
            color: #b52b2b;

            border: 1px solid #f0c3c3;

            font-size: 14px;
            text-align: center;
            line-height: 1.5;
        }

        .back-login {
            display: block;

            text-align: center;

            margin-top: 30px;

            color: #555555;

            text-decoration: none;

            font-size: 14px;
        }

        .back-login:hover {
            color: #c8a43b;
        }

        @media (max-width: 600px) {

            .forgot-container {
                padding: 15px;
            }

            .forgot-card {
                padding: 40px 25px;
            }

            .forgot-card h1 {
                font-size: 27px;
            }
        }

    </style>

</head>

<body>

    <div class="forgot-container">

        <div class="forgot-card">

            <h1>Forgot Password?</h1>

            <p class="subtitle">
                Enter your registered email address.
            </p>

            <form
                method="POST"
                action="forgot_password.php"
            >

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        value="<?= htmlspecialchars(
                            $_POST['email'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="reset-btn"
                >
                    Send Reset Code
                </button>

            </form>

            <?php if (!empty($message)): ?>

                <div class="message">
                    <?= htmlspecialchars(
                        $message,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            <?php endif; ?>

            <a
                href="index.php"
                class="back-login"
            >
                ← Back to Login
            </a>

        </div>

    </div>

</body>

</html>