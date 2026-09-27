<?php
session_start();

require_once __DIR__ . '/includes/db.php';

$message = "";
$message_type = "";

// Get reset information from session
$reset_email = $_SESSION["reset_email"] ?? "";
$reset_code = $_SESSION["reset_code"] ?? "";
$reset_expiry = $_SESSION["reset_expiry"] ?? 0;

// Code shown only for local testing
$display_code = $_SESSION["reset_display_code"] ?? "";

// Check whether a valid reset session exists
if (
    empty($reset_email) ||
    empty($reset_code) ||
    empty($reset_expiry)
) {
    $message = "No password reset request found. Please start again.";
    $message_type = "error";
}

// Handle password reset form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $code = trim($_POST["code"] ?? "");
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Check reset session
    if (
        empty($reset_email) ||
        empty($reset_code) ||
        empty($reset_expiry)
    ) {

        $message = "No password reset request found. Please start again.";
        $message_type = "error";

    // Check code expiry
    } elseif (time() > $reset_expiry) {

        $message = "Your reset code has expired. Please request a new code.";
        $message_type = "error";

        unset(
            $_SESSION["reset_email"],
            $_SESSION["reset_code"],
            $_SESSION["reset_expiry"],
            $_SESSION["reset_display_code"]
        );

    // Check reset code
    } elseif (!preg_match('/^[0-9]{6}$/', $code)) {

        $message = "Please enter the 6-digit reset code.";
        $message_type = "error";

    } elseif ($code !== (string)$reset_code) {

        $message = "Invalid reset code.";
        $message_type = "error";

    // Check passwords
    } elseif (empty($new_password) || empty($confirm_password)) {

        $message = "Please enter and confirm your new password.";
        $message_type = "error";

    } elseif ($new_password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } elseif (strlen($new_password) < 8) {

        $message = "Password must contain at least 8 characters.";
        $message_type = "error";

    } elseif (!preg_match('/[A-Z]/', $new_password)) {

        $message = "Password must contain at least one uppercase letter.";
        $message_type = "error";

    } elseif (!preg_match('/[a-z]/', $new_password)) {

        $message = "Password must contain at least one lowercase letter.";
        $message_type = "error";

    } elseif (!preg_match('/[0-9]/', $new_password)) {

        $message = "Password must contain at least one number.";
        $message_type = "error";

    } elseif (!preg_match('/[^A-Za-z0-9]/', $new_password)) {

        $message = "Password must contain at least one special character.";
        $message_type = "error";

    } else {

        // Hash new password
        $hashed_password = password_hash(
            $new_password,
            PASSWORD_DEFAULT
        );

        // Update password in users table
        $stmt = $conn->prepare(
            "UPDATE users SET password = ? WHERE email = ?"
        );

        if ($stmt) {

            $stmt->bind_param(
                "ss",
                $hashed_password,
                $reset_email
            );

            if ($stmt->execute()) {

                // Clear reset information
                unset(
                    $_SESSION["reset_email"],
                    $_SESSION["reset_code"],
                    $_SESSION["reset_expiry"],
                    $_SESSION["reset_display_code"]
                );

                // Redirect to login page
                header("Location: index.php?reset=success");
                exit;

            } else {

                $message = "Unable to update your password. Please try again.";
                $message_type = "error";
            }

            $stmt->close();

        } else {

            $message = "Database error. Please try again.";
            $message_type = "error";
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

    <title>Velora Drive | Reset Password</title>

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

        .reset-container {
            width: 100%;
            max-width: 540px;

            padding: 25px;
        }

        .reset-card {
            background: #ffffff;

            border-radius: 18px;

            padding: 50px 45px;

            box-shadow:
                0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .reset-card h1 {
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

            margin-bottom: 28px;
        }

        /* Reset code display */

        .reset-code-display {
            margin-bottom: 25px;

            padding: 13px 15px;

            text-align: center;

            background: #fff8e5;

            border: 1px solid #e6cf8a;

            border-radius: 8px;

            color: #555555;

            font-size: 14px;

            line-height: 1.5;
        }

        .reset-code-display strong {
            display: block;

            margin-top: 3px;

            color: #c8a43b;

            font-size: 20px;

            letter-spacing: 3px;
        }

        .form-group {
            margin-bottom: 22px;
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

        .password-note {
            font-size: 12px;

            color: #777777;

            margin-top: 7px;

            line-height: 1.5;
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

            font-size: 14px;

            text-align: center;

            line-height: 1.5;
        }

        .message.error {
            background: #fff1f1;

            color: #b52b2b;

            border: 1px solid #f0c3c3;
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

            .reset-container {
                padding: 15px;
            }

            .reset-card {
                padding: 40px 25px;
            }

            .reset-card h1 {
                font-size: 27px;
            }
        }

    </style>

</head>

<body>

    <div class="reset-container">

        <div class="reset-card">

            <h1>Reset Password</h1>

            <p class="subtitle">
                Enter the reset code and create a new password.
            </p>


            <?php if (!empty($display_code)): ?>

                <div class="reset-code-display">

                    Your reset code is:

                    <strong>
                        <?= htmlspecialchars(
                            $display_code,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                action="reset_password.php"
            >

                <div class="form-group">

                    <label for="code">
                        Reset Code
                    </label>

                    <input
                        type="text"
                        id="code"
                        name="code"
                        placeholder="Enter 6-digit code"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="new_password">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter new password"
                        autocomplete="new-password"
                        required
                    >

                    <p class="password-note">
                        Minimum 8 characters, including uppercase,
                        lowercase, number and special character.
                    </p>

                </div>


                <div class="form-group">

                    <label for="confirm_password">
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        autocomplete="new-password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="reset-btn"
                >
                    Reset Password
                </button>

            </form>


            <?php if (!empty($message)): ?>

                <div class="message <?= htmlspecialchars(
                    $message_type,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">

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