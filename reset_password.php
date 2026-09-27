<?php

session_start();

require_once __DIR__ . '/includes/db.php';

$message = "";
$message_type = "";

$reset_email = $_SESSION["reset_email"] ?? "";
$reset_code = $_SESSION["reset_code"] ?? "";
$reset_expiry = $_SESSION["reset_expiry"] ?? 0;


// ==========================================
// Check whether reset request exists
// ==========================================

if (
    empty($reset_email) ||
    empty($reset_code) ||
    empty($reset_expiry)
) {

    $message = "No password reset request found. Please request a new verification code.";
    $message_type = "error";

}


// ==========================================
// Process reset form
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $code = trim($_POST["code"] ?? "");

    $new_password = $_POST["new_password"] ?? "";

    $confirm_password = $_POST["confirm_password"] ?? "";


    // ==========================================
    // Check reset session
    // ==========================================

    if (
        empty($reset_email) ||
        empty($reset_code) ||
        empty($reset_expiry)
    ) {

        $message =
            "No password reset request found. Please request a new code.";

        $message_type = "error";


    // ==========================================
    // Check expiry
    // ==========================================

    } elseif (time() > $reset_expiry) {

        $message =
            "Your verification code has expired. Please request a new code.";

        $message_type = "error";

        unset(
            $_SESSION["reset_email"],
            $_SESSION["reset_code"],
            $_SESSION["reset_expiry"]
        );


    // ==========================================
    // Validate verification code
    // ==========================================

    } elseif (!preg_match('/^[0-9]{6}$/', $code)) {

        $message =
            "Please enter the 6-digit verification code.";

        $message_type = "error";


    } elseif ($code !== (string)$reset_code) {

        $message =
            "Invalid verification code. Please check your email and try again.";

        $message_type = "error";


    // ==========================================
    // Validate passwords
    // ==========================================

    } elseif (
        empty($new_password) ||
        empty($confirm_password)
    ) {

        $message =
            "Please enter and confirm your new password.";

        $message_type = "error";


    } elseif ($new_password !== $confirm_password) {

        $message =
            "Passwords do not match.";

        $message_type = "error";


    } elseif (strlen($new_password) < 8) {

        $message =
            "Password must contain at least 8 characters.";

        $message_type = "error";


    } elseif (!preg_match('/[A-Z]/', $new_password)) {

        $message =
            "Password must contain at least one uppercase letter.";

        $message_type = "error";


    } elseif (!preg_match('/[a-z]/', $new_password)) {

        $message =
            "Password must contain at least one lowercase letter.";

        $message_type = "error";


    } elseif (!preg_match('/[0-9]/', $new_password)) {

        $message =
            "Password must contain at least one number.";

        $message_type = "error";


    } elseif (!preg_match('/[^A-Za-z0-9]/', $new_password)) {

        $message =
            "Password must contain at least one special character.";

        $message_type = "error";


    } else {

        // ==========================================
        // Hash new password
        // ==========================================

        $hashed_password = password_hash(
            $new_password,
            PASSWORD_DEFAULT
        );


        // ==========================================
        // Update password
        // ==========================================

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

                // ==================================
                // Clear reset session
                // ==================================

                unset(
                    $_SESSION["reset_email"],
                    $_SESSION["reset_code"],
                    $_SESSION["reset_expiry"]
                );


                // ==================================
                // Redirect to login
                // ==================================

                header(
                    "Location: index.php?reset=success"
                );

                exit;


            } else {

                $message =
                    "Unable to update your password. Please try again.";

                $message_type = "error";

            }


            $stmt->close();


        } else {

            $message =
                "Database error. Please try again.";

            $message_type = "error";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Velora Drive | Reset Password</title>


    <!-- Google Font -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    >


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {

            font-family: "Poppins", sans-serif;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            background:
                linear-gradient(
                    135deg,
                    #f8f8f8,
                    #f1eee5
                );

            padding: 20px;

        }


        .container {

            width: 100%;

            max-width: 470px;

        }


        .card {

            background: #ffffff;

            padding: 40px;

            border-radius: 18px;

            box-shadow:
                0 15px 45px
                rgba(0, 0, 0, 0.10);

        }


        .logo {

            text-align: center;

            margin-bottom: 25px;

        }


        .logo h1 {

            font-size: 30px;

            color: #222222;

            font-weight: 700;

        }


        .logo span {

            color: #c8a43b;

        }


        .logo p {

            color: #999999;

            font-size: 13px;

            margin-top: 3px;

        }


        .icon {

            width: 65px;

            height: 65px;

            margin: 0 auto 20px;

            border-radius: 50%;

            background: #faf8f0;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #c8a43b;

            font-size: 25px;

        }


        .title {

            text-align: center;

            margin-bottom: 8px;

            color: #222222;

            font-size: 23px;

            font-weight: 600;

        }


        .description {

            text-align: center;

            color: #777777;

            font-size: 13px;

            line-height: 1.6;

            margin-bottom: 25px;

        }


        .email-info {

            background: #faf8f0;

            border: 1px solid #e8dcae;

            border-radius: 9px;

            padding: 12px;

            text-align: center;

            margin-bottom: 20px;

            font-size: 12px;

            color: #666666;

        }


        .email-info strong {

            color: #333333;

        }


        .form-group {

            margin-bottom: 18px;

        }


        label {

            display: block;

            font-size: 13px;

            font-weight: 500;

            color: #444444;

            margin-bottom: 7px;

        }


        .input-box {

            position: relative;

        }


        .input-box i {

            position: absolute;

            left: 15px;

            top: 50%;

            transform: translateY(-50%);

            color: #aaa;

            font-size: 14px;

        }


        input {

            width: 100%;

            padding: 13px 15px 13px 42px;

            border: 1px solid #dddddd;

            border-radius: 9px;

            outline: none;

            font-family: "Poppins", sans-serif;

            font-size: 13px;

            transition: 0.3s;

        }


        input:focus {

            border-color: #c8a43b;

            box-shadow:
                0 0 0 3px
                rgba(200, 164, 59, 0.10);

        }


        .code-input {

            letter-spacing: 5px;

            font-weight: 600;

            text-align: center;

            font-size: 17px;

        }


        .button {

            width: 100%;

            border: none;

            background: #c8a43b;

            color: #ffffff;

            padding: 13px;

            border-radius: 9px;

            font-family: "Poppins", sans-serif;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.3s;

        }


        .button:hover {

            background: #ad8c2f;

            transform: translateY(-1px);

        }


        .message {

            padding: 11px 13px;

            border-radius: 8px;

            font-size: 12px;

            margin-bottom: 18px;

            line-height: 1.5;

        }


        .message.error {

            background: #fff0f0;

            color: #c0392b;

            border: 1px solid #f3c5c0;

        }


        .back-login {

            text-align: center;

            margin-top: 20px;

        }


        .back-login a {

            color: #c8a43b;

            text-decoration: none;

            font-size: 13px;

            font-weight: 500;

        }


        .back-login a:hover {

            text-decoration: underline;

        }


        .security-note {

            text-align: center;

            margin-top: 18px;

            color: #999999;

            font-size: 11px;

        }


        @media (max-width: 500px) {

            .card {

                padding: 28px 22px;

            }


            .logo h1 {

                font-size: 26px;

            }

        }

    </style>

</head>


<body>

<div class="container">

    <div class="card">


        <!-- Logo -->

        <div class="logo">

            <h1>
                Velora <span>Drive</span>
            </h1>

            <p>
                Vehicle Rental Management
            </p>

        </div>


        <!-- Icon -->

        <div class="icon">

            <i class="fa-solid fa-shield-halved"></i>

        </div>


        <!-- Title -->

        <h2 class="title">

            Reset Password

        </h2>


        <p class="description">

            Enter the verification code sent to
            your email and create a new password.

        </p>


        <?php if (!empty($reset_email)): ?>

            <div class="email-info">

                Verification code sent to:

                <br>

                <strong>
                    <?= htmlspecialchars($reset_email) ?>
                </strong>

            </div>

        <?php endif; ?>


        <?php if (!empty($message)): ?>

            <div class="message <?= $message_type ?>">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <?php if (
            !empty($reset_email) &&
            !empty($reset_code) &&
            !empty($reset_expiry)
        ): ?>


            <form method="POST">


                <!-- Verification Code -->

                <div class="form-group">

                    <label for="code">

                        Verification Code

                    </label>


                    <div class="input-box">

                        <i class="fa-solid fa-key"></i>


                        <input
                            type="text"
                            id="code"
                            name="code"
                            class="code-input"
                            placeholder="6-digit code"
                            maxlength="6"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            required
                        >

                    </div>

                </div>


                <!-- New Password -->

                <div class="form-group">

                    <label for="new_password">

                        New Password

                    </label>


                    <div class="input-box">

                        <i class="fa-solid fa-lock"></i>


                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            placeholder="Enter new password"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                </div>


                <!-- Confirm Password -->

                <div class="form-group">

                    <label for="confirm_password">

                        Confirm New Password

                    </label>


                    <div class="input-box">

                        <i class="fa-solid fa-lock"></i>


                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Confirm new password"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                </div>


                <!-- Submit -->

                <button
                    type="submit"
                    class="button"
                >

                    <i class="fa-solid fa-check"></i>

                    Reset Password

                </button>


            </form>


            <div class="security-note">

                <i class="fa-solid fa-clock"></i>

                Your verification code is valid for
                10 minutes.

            </div>


        <?php endif; ?>


        <!-- Back to Login -->

        <div class="back-login">

            <a href="index.php">

                <i class="fa-solid fa-arrow-left"></i>

                Back to Login

            </a>

        </div>


    </div>

</div>

</body>

</html>