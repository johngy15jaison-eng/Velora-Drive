<?php

session_start();

require_once __DIR__ . "/includes/db.php";


/*
|--------------------------------------------------------------------------
| RESEND API CONFIGURATION
|--------------------------------------------------------------------------
*/

if (getenv("RESEND_API_KEY")) {

    // Railway
    $resend_api_key = getenv("RESEND_API_KEY");

} else {

    // Local XAMPP
    $local_config = __DIR__ . "/includes/resend_config.php";

    if (file_exists($local_config)) {

        require_once $local_config;

    } else {

        $resend_api_key = "";

    }
}


/*
|--------------------------------------------------------------------------
| CHECK RESET SESSION
|--------------------------------------------------------------------------
*/

$reset_email = $_SESSION["reset_email"] ?? "";
$reset_code = $_SESSION["reset_code"] ?? "";
$reset_expiry = $_SESSION["reset_expiry"] ?? 0;


/*
|--------------------------------------------------------------------------
| BASIC SESSION CHECK
|--------------------------------------------------------------------------
*/

if (empty($reset_email) || empty($reset_code)) {

    header("Location: forgot_password.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| VARIABLES
|--------------------------------------------------------------------------
*/

$message = "";
$message_type = "";

$show_password_form = false;


/*
|--------------------------------------------------------------------------
| CHECK EXPIRY
|--------------------------------------------------------------------------
*/

if (time() > $reset_expiry) {

    $message =
        "Your verification code has expired. Please request a new code.";

    $message_type = "error";

}


/*
|--------------------------------------------------------------------------
| RESEND CODE
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["resend_code"])
) {


    /*
    |--------------------------------------------------------------------------
    | CHECK RESEND CONFIGURATION
    |--------------------------------------------------------------------------
    */

    if (empty($resend_api_key)) {

        $message =
            "Email service configuration is missing.";

        $message_type = "error";

    } else {


        /*
        |--------------------------------------------------------------------------
        | CHECK COOLDOWN
        |--------------------------------------------------------------------------
        */

        $last_resend =
            $_SESSION["last_resend_time"] ?? 0;

        $seconds_since_resend =
            time() - $last_resend;


        if ($seconds_since_resend < 60) {

            $remaining =
                60 - $seconds_since_resend;

            $message =
                "Please wait " .
                $remaining .
                " seconds before requesting another code.";

            $message_type = "error";

        } else {


            /*
            |--------------------------------------------------------------------------
            | GET USER DETAILS
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "SELECT fullname FROM users WHERE email = ? LIMIT 1"
            );


            if (!$stmt) {

                $message =
                    "Something went wrong. Please try again.";

                $message_type = "error";

            } else {

                $stmt->bind_param(
                    "s",
                    $reset_email
                );

                $stmt->execute();

                $result = $stmt->get_result();


                if ($result->num_rows === 0) {

                    $message =
                        "Unable to find your account.";

                    $message_type = "error";

                } else {


                    $user =
                        $result->fetch_assoc();

                    $fullname =
                        $user["fullname"];


                    /*
                    |--------------------------------------------------------------------------
                    | GENERATE NEW CODE
                    |--------------------------------------------------------------------------
                    */

                    $new_code =
                        random_int(
                            100000,
                            999999
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | EMAIL SUBJECT
                    |--------------------------------------------------------------------------
                    */

                    $subject =
                        "Velora Drive - New Password Reset Code";


                    /*
                    |--------------------------------------------------------------------------
                    | EMAIL HTML
                    |--------------------------------------------------------------------------
                    */

                    $html = "

                    <!DOCTYPE html>

                    <html>

                    <head>

                        <meta charset='UTF-8'>

                    </head>

                    <body style='margin:0; padding:0; background:#f6f5f1; font-family:Arial,sans-serif;'>

                        <div style='max-width:600px; margin:40px auto; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e5e0d5;'>

                            <div style='background:#222222; padding:25px; text-align:center;'>

                                <h1 style='margin:0; color:#ffffff; font-size:28px;'>

                                    Velora <span style='color:#c8a43b;'>Drive</span>

                                </h1>

                                <p style='margin:6px 0 0; color:#cccccc; font-size:13px;'>

                                    Vehicle Rental Management

                                </p>

                            </div>


                            <div style='padding:35px 30px;'>

                                <h2 style='margin-top:0; color:#222222;'>

                                    New Verification Code

                                </h2>


                                <p style='color:#555555; line-height:1.6;'>

                                    Hello " .
                                    htmlspecialchars($fullname) .
                                    ",

                                </p>


                                <p style='color:#555555; line-height:1.6;'>

                                    You requested a new verification code
                                    for your Velora Drive account.

                                </p>


                                <div style='text-align:center; margin:30px 0;'>

                                    <span style='display:inline-block; padding:15px 28px; background:#faf5e5; border:1px solid #e5d49b; border-radius:10px; color:#b28f2e; font-size:30px; font-weight:bold; letter-spacing:7px;'>

                                        " .
                                        $new_code .
                                        "

                                    </span>

                                </div>


                                <p style='color:#777777; font-size:13px; line-height:1.6;'>

                                    This code will expire in
                                    <strong>10 minutes</strong>.

                                </p>


                                <p style='color:#777777; font-size:13px; line-height:1.6;'>

                                    If you did not request this code,
                                    please ignore this email.

                                </p>

                            </div>


                            <div style='background:#f7f6f2; padding:18px; text-align:center;'>

                                <p style='margin:0; color:#999999; font-size:12px;'>

                                    © 2026 Velora Drive. All rights reserved.

                                </p>

                            </div>

                        </div>

                    </body>

                    </html>

                    ";


                    /*
                    |--------------------------------------------------------------------------
                    | RESEND API PAYLOAD
                    |--------------------------------------------------------------------------
                    */

                    $payload = [

                        "from" =>
                            "Velora Drive <onboarding@resend.dev>",

                        "to" =>
                            [$reset_email],

                        "subject" =>
                            $subject,

                        "html" =>
                            $html

                    ];


                    /*
                    |--------------------------------------------------------------------------
                    | SEND EMAIL
                    |--------------------------------------------------------------------------
                    */

                    $ch = curl_init(
                        "https://api.resend.com/emails"
                    );


                    curl_setopt(
                        $ch,
                        CURLOPT_RETURNTRANSFER,
                        true
                    );


                    curl_setopt(
                        $ch,
                        CURLOPT_POST,
                        true
                    );


                    curl_setopt(
                        $ch,
                        CURLOPT_POSTFIELDS,
                        json_encode($payload)
                    );


                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        [
                            "Authorization: Bearer " .
                            $resend_api_key,

                            "Content-Type: application/json"
                        ]
                    );


                    curl_setopt(
                        $ch,
                        CURLOPT_TIMEOUT,
                        15
                    );


                    $response =
                        curl_exec($ch);


                    $curl_error =
                        curl_error($ch);


                    $http_code =
                        curl_getinfo(
                            $ch,
                            CURLINFO_HTTP_CODE
                        );


                    curl_close($ch);


                    /*
                    |--------------------------------------------------------------------------
                    | EMAIL RESULT
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $response === false ||
                        !empty($curl_error)
                    ) {

                        $message =
                            "Unable to connect to the email service. Please try again.";

                        $message_type =
                            "error";

                    } elseif (
                        $http_code < 200 ||
                        $http_code >= 300
                    ) {

                        $message =
                            "Unable to send the verification code. Please try again.";

                        $message_type =
                            "error";

                    } else {


                        /*
                        |--------------------------------------------------------------------------
                        | SAVE NEW CODE
                        |--------------------------------------------------------------------------
                        */

                        $_SESSION["reset_code"] =
                            (string)$new_code;


                        $_SESSION["reset_expiry"] =
                            time() + (10 * 60);


                        $_SESSION["last_resend_time"] =
                            time();


                        /*
                        |--------------------------------------------------------------------------
                        | SUCCESS MESSAGE
                        |--------------------------------------------------------------------------
                        */

                        $message =
                            "A new verification code has been sent to your email.";

                        $message_type =
                            "success";

                    }

                }


                $stmt->close();

            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| VERIFY CODE
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["verify_code"])
) {


    $entered_code =
        trim($_POST["verification_code"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | CHECK EXPIRY
    |--------------------------------------------------------------------------
    */

    if (time() > ($_SESSION["reset_expiry"] ?? 0)) {

        $message =
            "Your verification code has expired. Please resend a new code.";

        $message_type =
            "error";

    } elseif (
        !preg_match(
            "/^[0-9]{6}$/",
            $entered_code
        )
    ) {

        $message =
            "Please enter the 6-digit verification code.";

        $message_type =
            "error";

    } elseif (
        $entered_code !==
        (string)($_SESSION["reset_code"] ?? "")
    ) {

        $message =
            "The verification code is incorrect.";

        $message_type =
            "error";

    } else {


        /*
        |--------------------------------------------------------------------------
        | CODE VERIFIED
        |--------------------------------------------------------------------------
        */

        $_SESSION["code_verified"] = true;


        $show_password_form = true;

    }

}


/*
|--------------------------------------------------------------------------
| PASSWORD RESET
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["reset_password"])
) {


    /*
    |--------------------------------------------------------------------------
    | CHECK VERIFICATION
    |--------------------------------------------------------------------------
    */

    if (
        empty($_SESSION["code_verified"]) ||
        $_SESSION["code_verified"] !== true
    ) {

        $message =
            "Please verify your email code first.";

        $message_type =
            "error";

    } else {


        $password =
            $_POST["password"] ?? "";

        $confirm_password =
            $_POST["confirm_password"] ?? "";


        /*
        |--------------------------------------------------------------------------
        | PASSWORD VALIDATION
        |--------------------------------------------------------------------------
        */

        if (empty($password)) {

            $message =
                "Please enter a new password.";

            $message_type =
                "error";

        } elseif (
            strlen($password) < 8
        ) {

            $message =
                "Password must contain at least 8 characters.";

            $message_type =
                "error";

        } elseif (
            !preg_match("/[A-Z]/", $password)
        ) {

            $message =
                "Password must contain at least one uppercase letter.";

            $message_type =
                "error";

        } elseif (
            !preg_match("/[a-z]/", $password)
        ) {

            $message =
                "Password must contain at least one lowercase letter.";

            $message_type =
                "error";

        } elseif (
            !preg_match("/[0-9]/", $password)
        ) {

            $message =
                "Password must contain at least one number.";

            $message_type =
                "error";

        } elseif (
            !preg_match(
                "/[^A-Za-z0-9]/",
                $password
            )
        ) {

            $message =
                "Password must contain at least one special character.";

            $message_type =
                "error";

        } elseif (
            $password !== $confirm_password
        ) {

            $message =
                "Passwords do not match.";

            $message_type =
                "error";

        } else {


            /*
            |--------------------------------------------------------------------------
            | HASH PASSWORD
            |--------------------------------------------------------------------------
            */

            $hashed_password =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            /*
            |--------------------------------------------------------------------------
            | UPDATE PASSWORD
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "UPDATE users SET password = ? WHERE email = ?"
            );


            if (!$stmt) {

                $message =
                    "Something went wrong. Please try again.";

                $message_type =
                    "error";

            } else {

                $stmt->bind_param(
                    "ss",
                    $hashed_password,
                    $reset_email
                );


                if ($stmt->execute()) {


                    /*
                    |--------------------------------------------------------------------------
                    | CLEAR RESET SESSION
                    |--------------------------------------------------------------------------
                    */

                    unset(
                        $_SESSION["reset_email"],
                        $_SESSION["reset_code"],
                        $_SESSION["reset_expiry"],
                        $_SESSION["last_resend_time"],
                        $_SESSION["code_verified"]
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    header(
                        "Location: index.php?reset=success"
                    );

                    exit;

                } else {

                    $message =
                        "Unable to update your password. Please try again.";

                    $message_type =
                        "error";

                }


                $stmt->close();

            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| SHOW PASSWORD FORM AFTER SUCCESSFUL VERIFICATION
|--------------------------------------------------------------------------
*/

if (
    !empty($_SESSION["code_verified"]) &&
    $_SESSION["code_verified"] === true
) {

    $show_password_form = true;

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

    <title>
        Reset Password | Velora Drive
    </title>


    <!-- Poppins -->

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
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
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

            background:
                linear-gradient(
                    135deg,
                    #f8f7f3,
                    #f1eee5
                );

            min-height: 100vh;

            color: #222;

            display: flex;

            flex-direction: column;

        }


        /* HEADER */

        .topbar {

            height: 82px;

            background: #fff;

            border-bottom:
                1px solid #e8e3d7;

            display: flex;

            align-items: center;

            padding: 0 7%;

        }


        .brand {

            text-decoration: none;

            font-size: 29px;

            font-weight: 700;

            color: #222;

        }


        .brand span {

            color: #c8a43b;

        }


        .tagline {

            font-size: 11px;

            color: #888;

        }


        /* MAIN */

        .main-container {

            flex: 1;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 45px 20px;

        }


        /* CARD */

        .reset-card {

            width: 100%;

            max-width: 470px;

            background: #fff;

            border-radius: 20px;

            padding: 40px;

            border:
                1px solid #e5e0d5;

            box-shadow:
                0 20px 55px
                rgba(0,0,0,.09);

        }


        /* ICON */

        .icon-circle {

            width: 70px;

            height: 70px;

            margin:
                0 auto 20px;

            border-radius: 50%;

            background: #faf5e5;

            border:
                1px solid #eadcae;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #c8a43b;

            font-size: 27px;

        }


        /* TITLE */

        .reset-card h1 {

            text-align: center;

            font-size: 27px;

            margin-bottom: 10px;

        }


        .description {

            text-align: center;

            color: #777;

            font-size: 13px;

            line-height: 1.7;

            margin-bottom: 27px;

        }


        /* ALERT */

        .alert {

            padding: 12px 14px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 13px;

        }


        .alert.error {

            background: #fff1f1;

            border:
                1px solid #f1c5c5;

            color: #b42318;

        }


        .alert.success {

            background: #effaf1;

            border:
                1px solid #bee3c6;

            color: #27743b;

        }


        /* LABEL */

        .label {

            display: block;

            font-size: 13px;

            font-weight: 600;

            margin-bottom: 8px;

        }


        /* INPUT */

        .input-wrapper {

            position: relative;

            margin-bottom: 20px;

        }


        .input-wrapper i {

            position: absolute;

            left: 16px;

            top: 50%;

            transform:
                translateY(-50%);

            color: #aaa;

        }


        .input {

            width: 100%;

            height: 52px;

            border:
                1px solid #ddd;

            border-radius: 10px;

            padding:
                0 15px 0 45px;

            outline: none;

            font-family:
                "Poppins", sans-serif;

            font-size: 14px;

        }


        .input:focus {

            border-color:
                #c8a43b;

            box-shadow:
                0 0 0 3px
                rgba(200,164,59,.12);

        }


        /* CODE INPUT */

        .code-input {

            text-align: center;

            letter-spacing: 7px;

            font-size: 20px;

            font-weight: 600;

            padding-left: 15px;

        }


        /* BUTTON */

        .button {

            width: 100%;

            height: 52px;

            border: none;

            border-radius: 10px;

            background:
                linear-gradient(
                    135deg,
                    #d1ad3f,
                    #bd982d
                );

            color: #fff;

            font-family:
                "Poppins", sans-serif;

            font-weight: 600;

            font-size: 14px;

            cursor: pointer;

            transition: .25s;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

        }


        .button:hover {

            transform:
                translateY(-1px);

            box-shadow:
                0 8px 20px
                rgba(190,150,40,.25);

        }


        /* RESEND */

        .resend-section {

            text-align: center;

            margin-top: 22px;

            padding-top: 20px;

            border-top:
                1px solid #eee;

        }


        .resend-text {

            font-size: 13px;

            color: #777;

            margin-bottom: 8px;

        }


        .resend-button {

            border: none;

            background: none;

            color: #b28f2e;

            font-family:
                "Poppins", sans-serif;

            font-size: 13px;

            font-weight: 600;

            cursor: pointer;

        }


        .resend-button:hover {

            text-decoration: underline;

        }


        .resend-button:disabled {

            color: #aaa;

            cursor: not-allowed;

            text-decoration: none;

        }


        .cooldown {

            display: block;

            margin-top: 6px;

            font-size: 11px;

            color: #999;

        }


        /* BACK */

        .back-login {

            display: block;

            text-align: center;

            margin-top: 22px;

            color: #666;

            text-decoration: none;

            font-size: 13px;

        }


        .back-login:hover {

            color: #c8a43b;

        }


        /* FOOTER */

        .footer {

            height: 45px;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #999;

            font-size: 11px;

        }


        /* MOBILE */

        @media(max-width:600px) {

            .topbar {

                padding: 0 20px;

            }


            .brand {

                font-size: 25px;

            }


            .tagline {

                display: none;

            }


            .main-container {

                padding:
                    30px 15px;

            }


            .reset-card {

                padding:
                    30px 22px;

            }

        }

    </style>

</head>


<body>


<header class="topbar">

    <div>

        <a
            href="index.php"
            class="brand"
        >
            Velora <span>Drive</span>
        </a>

        <div class="tagline">
            Vehicle Rental Management
        </div>

    </div>

</header>



<main class="main-container">

    <div class="reset-card">


        <div class="icon-circle">

            <?php if ($show_password_form): ?>

                <i class="fa-solid fa-key"></i>

            <?php else: ?>

                <i class="fa-solid fa-shield-halved"></i>

            <?php endif; ?>

        </div>



        <?php if (!$show_password_form): ?>


            <h1>
                Verify Your Email
            </h1>


            <p class="description">

                Enter the 6-digit verification code
                sent to your email address.

            </p>



            <?php if (!empty($message)): ?>

                <div
                    class="alert <?php echo htmlspecialchars($message_type); ?>"
                >

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>



            <form method="POST">


                <label class="label">

                    Verification Code

                </label>


                <div class="input-wrapper">

                    <i class="fa-solid fa-shield-halved"></i>

                    <input
                        type="text"
                        name="verification_code"
                        class="input code-input"
                        placeholder="000000"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required
                    >

                </div>


                <button
                    type="submit"
                    name="verify_code"
                    class="button"
                >

                    <i class="fa-solid fa-check"></i>

                    Verify Code

                </button>


            </form>



            <!-- RESEND -->

            <div class="resend-section">

                <div class="resend-text">

                    Didn't receive the code?

                </div>


                <form method="POST">

                    <button
                        type="submit"
                        name="resend_code"
                        id="resendButton"
                        class="resend-button"
                    >

                        <i class="fa-solid fa-rotate-right"></i>

                        Resend Code

                    </button>

                </form>


                <span
                    id="cooldownText"
                    class="cooldown"
                ></span>

            </div>


        <?php else: ?>


            <h1>
                Create New Password
            </h1>


            <p class="description">

                Your verification code has been confirmed.
                Create a strong new password for your account.

            </p>



            <?php if (!empty($message)): ?>

                <div
                    class="alert <?php echo htmlspecialchars($message_type); ?>"
                >

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>



            <form method="POST">


                <label class="label">

                    New Password

                </label>


                <div class="input-wrapper">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        name="password"
                        class="input"
                        placeholder="Enter new password"
                        required
                    >

                </div>



                <label class="label">

                    Confirm Password

                </label>


                <div class="input-wrapper">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        name="confirm_password"
                        class="input"
                        placeholder="Confirm new password"
                        required
                    >

                </div>



                <button
                    type="submit"
                    name="reset_password"
                    class="button"
                >

                    <i class="fa-solid fa-key"></i>

                    Reset Password

                </button>


            </form>


        <?php endif; ?>


        <a
            href="index.php"
            class="back-login"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Back to Login

        </a>


    </div>

</main>



<footer class="footer">

    © 2026 Velora Drive. All rights reserved.

</footer>



<script>

/*
|--------------------------------------------------------------------------
| RESEND COOLDOWN
|--------------------------------------------------------------------------
*/

const resendButton =
    document.getElementById("resendButton");

const cooldownText =
    document.getElementById("cooldownText");


if (resendButton && cooldownText) {


    let cooldown =
        <?php
            $last_resend =
                $_SESSION["last_resend_time"] ?? 0;

            $remaining =
                max(
                    0,
                    60 - (time() - $last_resend)
                );

            echo (int)$remaining;
        ?>;


    function updateCooldown() {

        if (cooldown > 0) {

            resendButton.disabled = true;

            cooldownText.textContent =
                "You can resend the code in " +
                cooldown +
                " seconds";

            cooldown--;

            setTimeout(
                updateCooldown,
                1000
            );

        } else {

            resendButton.disabled = false;

            cooldownText.textContent = "";

        }

    }


    updateCooldown();

}

</script>


</body>

</html>