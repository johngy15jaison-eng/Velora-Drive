<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/mail_config.php';
require_once __DIR__ . '/includes/db.php';

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    // =========================
    // Validate email
    // =========================

    if (empty($email)) {

        $message = "Please enter your registered email address.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        // =========================
        // Check user email
        // =========================

        $stmt = $conn->prepare(
            "SELECT id, fullname FROM users WHERE email = ?"
        );

        if ($stmt) {

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $user = $result->fetch_assoc();

                $fullname = $user["fullname"];

                // =========================
                // Generate 6-digit code
                // =========================

                $code = random_int(100000, 999999);

                // =========================
                // Send email
                // =========================

                $mail = new PHPMailer(true);

                try {

                    // Gmail SMTP settings
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;

                    $mail->Username = $mail_username;
                    $mail->Password = $mail_password;

                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // =========================
                    // Sender
                    // =========================

                    $mail->setFrom(
                        $mail_username,
                        "Velora Drive"
                    );

                    // =========================
                    // Receiver
                    // =========================

                    $mail->addAddress(
                        $email,
                        $fullname
                    );

                    // =========================
                    // Email content
                    // =========================

                    $mail->isHTML(true);

                    $mail->Subject =
                        "Velora Drive - Password Reset Code";

                    $mail->Body = "

                    <!DOCTYPE html>

                    <html>

                    <head>

                        <meta charset='UTF-8'>

                    </head>

                    <body style='
                        margin:0;
                        padding:0;
                        background:#f6f6f6;
                        font-family:Arial, sans-serif;
                    '>

                        <div style='
                            max-width:600px;
                            margin:40px auto;
                            background:#ffffff;
                            padding:35px;
                            border-radius:12px;
                            box-shadow:0 5px 20px rgba(0,0,0,0.08);
                        '>

                            <h1 style='
                                margin:0 0 5px;
                                color:#222222;
                                font-size:28px;
                            '>
                                Velora <span style='color:#c8a43b;'>Drive</span>
                            </h1>

                            <p style='
                                color:#777777;
                                margin-top:5px;
                            '>
                                Vehicle Rental Management
                            </p>

                            <hr style='
                                border:none;
                                border-top:1px solid #eeeeee;
                                margin:25px 0;
                            '>

                            <h2 style='
                                color:#222222;
                            '>
                                Password Reset
                            </h2>

                            <p style='
                                color:#555555;
                                font-size:15px;
                                line-height:1.6;
                            '>
                                Hello " . htmlspecialchars($fullname) . ",
                            </p>

                            <p style='
                                color:#555555;
                                font-size:15px;
                                line-height:1.6;
                            '>
                                We received a request to reset your
                                Velora Drive account password.
                            </p>

                            <p style='
                                color:#555555;
                                font-size:15px;
                            '>
                                Your verification code is:
                            </p>

                            <div style='
                                background:#faf8f0;
                                border:1px solid #e8dcae;
                                border-radius:10px;
                                padding:20px;
                                text-align:center;
                                margin:25px 0;
                            '>

                                <span style='
                                    font-size:32px;
                                    font-weight:bold;
                                    letter-spacing:8px;
                                    color:#c8a43b;
                                '>
                                    " . $code . "
                                </span>

                            </div>

                            <p style='
                                color:#555555;
                                font-size:14px;
                                line-height:1.6;
                            '>
                                This verification code is valid for
                                <strong>10 minutes</strong>.
                            </p>

                            <p style='
                                color:#555555;
                                font-size:14px;
                                line-height:1.6;
                            '>
                                If you did not request a password reset,
                                please ignore this email.
                            </p>

                            <hr style='
                                border:none;
                                border-top:1px solid #eeeeee;
                                margin:25px 0;
                            '>

                            <p style='
                                color:#999999;
                                font-size:12px;
                                line-height:1.5;
                            '>
                                This is an automated email from Velora Drive.
                                Please do not reply to this message.
                            </p>

                        </div>

                    </body>

                    </html>

                    ";

                    // Plain-text version
                    $mail->AltBody =
                        "Hello " . $fullname . ",\n\n" .
                        "Your Velora Drive password reset code is: " .
                        $code . "\n\n" .
                        "This code will expire in 10 minutes.\n\n" .
                        "If you did not request a password reset, " .
                        "please ignore this email.";

                    // =========================
                    // Send email
                    // =========================

                    $mail->send();

                    // =========================
                    // Store reset information
                    // =========================

                    $_SESSION["reset_email"] = $email;

                    $_SESSION["reset_code"] =
                        (string)$code;

                    $_SESSION["reset_expiry"] =
                        time() + 600;

                    // =========================
                    // Go to reset page
                    // =========================

                    header(
                        "Location: reset_password.php"
                    );

                    exit;

                } catch (Exception $e) {

                    $message =
                        "Unable to send the verification email. " .
                        "Please check your email address and try again.";

                    $message_type = "error";
                }

            } else {

                $message =
                    "Email address not found. Please enter the email " .
                    "you used when registering.";

                $message_type = "error";
            }

            $stmt->close();

        } else {

            $message =
                "Something went wrong. Please try again.";

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

    <title>Velora Drive | Forgot Password</title>

    <!-- Google Font -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="
        https://fonts.googleapis.com/css2?family=Poppins:
        wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->

    <link rel="stylesheet"
          href="
          https://cdnjs.cloudflare.com/ajax/libs/font-awesome/
          6.6.0/css/all.min.css">

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

            max-width: 450px;

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

        <div class="logo">

            <h1>
                Velora <span>Drive</span>
            </h1>

            <p>
                Vehicle Rental Management
            </p>

        </div>

        <div class="icon">

            <i class="fa-solid fa-lock"></i>

        </div>

        <h2 class="title">
            Forgot Password?
        </h2>

        <p class="description">

            Enter the email address registered
            with your Velora Drive account.
            We'll send you a verification code.

        </p>

        <?php if (!empty($message)): ?>

            <div class="message <?= $message_type ?>">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <div class="input-box">

                    <i class="fa-solid fa-envelope"></i>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your registered email"
                        required
                        autocomplete="email"
                        value="<?= htmlspecialchars(
                            $_POST["email"] ?? ""
                        ) ?>"
                    >

                </div>

            </div>

            <button
                type="submit"
                class="button"
            >

                <i class="fa-solid fa-paper-plane"></i>

                Send Verification Code

            </button>

        </form>

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