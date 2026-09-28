<?php

session_start();

require_once __DIR__ . "/includes/db.php";


/*
|--------------------------------------------------------------------------
| RESEND API CONFIGURATION
|--------------------------------------------------------------------------
|
| Railway:
|   Uses RESEND_API_KEY environment variable.
|
| Local XAMPP:
|   Uses includes/resend_config.php
|
|--------------------------------------------------------------------------
*/

if (getenv("RESEND_API_KEY")) {

    // Railway / Production
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
| VARIABLES
|--------------------------------------------------------------------------
*/

$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| FORM SUBMISSION
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {


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
        | GET EMAIL
        |--------------------------------------------------------------------------
        */

        $email = trim($_POST["email"] ?? "");


        /*
        |--------------------------------------------------------------------------
        | VALIDATE EMAIL
        |--------------------------------------------------------------------------
        */

        if (empty($email)) {

            $message =
                "Please enter your email address.";

            $message_type = "error";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $message =
                "Please enter a valid email address.";

            $message_type = "error";

        } else {


            /*
            |--------------------------------------------------------------------------
            | CHECK REGISTERED USER
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "SELECT id, fullname
                 FROM users
                 WHERE email = ?
                 LIMIT 1"
            );


            if (!$stmt) {

                $message =
                    "Something went wrong. Please try again later.";

                $message_type = "error";

            } else {

                $stmt->bind_param("s", $email);

                $stmt->execute();

                $result = $stmt->get_result();


                /*
                |--------------------------------------------------------------------------
                | USER NOT FOUND
                |--------------------------------------------------------------------------
                */

                if ($result->num_rows === 0) {

                    $message =
                        "No account was found with this email address.";

                    $message_type = "error";

                } else {


                    /*
                    |--------------------------------------------------------------------------
                    | USER FOUND
                    |--------------------------------------------------------------------------
                    */

                    $user = $result->fetch_assoc();

                    $fullname = $user["fullname"];


                    /*
                    |--------------------------------------------------------------------------
                    | GENERATE VERIFICATION CODE
                    |--------------------------------------------------------------------------
                    */

                    $verification_code =
                        random_int(100000, 999999);


                    /*
                    |--------------------------------------------------------------------------
                    | EMAIL SUBJECT
                    |--------------------------------------------------------------------------
                    */

                    $subject =
                        "Velora Drive - Password Reset Verification Code";


                    /*
                    |--------------------------------------------------------------------------
                    | SAFELY ESCAPE USER NAME
                    |--------------------------------------------------------------------------
                    */

                    $safe_fullname =
                        htmlspecialchars(
                            $fullname,
                            ENT_QUOTES,
                            "UTF-8"
                        );


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


                    <body
                        style='
                            margin:0;
                            padding:0;
                            background:#f6f5f1;
                            font-family:Arial,sans-serif;
                        '
                    >

                        <div
                            style='
                                max-width:600px;
                                margin:40px auto;
                                background:#ffffff;
                                border-radius:12px;
                                overflow:hidden;
                                border:1px solid #e5e0d5;
                            '
                        >


                            <!-- HEADER -->

                            <div
                                style='
                                    background:#222222;
                                    padding:25px;
                                    text-align:center;
                                '
                            >

                                <h1
                                    style='
                                        margin:0;
                                        color:#ffffff;
                                        font-size:28px;
                                    '
                                >

                                    Velora

                                    <span
                                        style='
                                            color:#c8a43b;
                                        '
                                    >
                                        Drive
                                    </span>

                                </h1>


                                <p
                                    style='
                                        margin:6px 0 0;
                                        color:#cccccc;
                                        font-size:13px;
                                    '
                                >
                                    Vehicle Rental Management
                                </p>

                            </div>


                            <!-- CONTENT -->

                            <div
                                style='
                                    padding:35px 30px;
                                '
                            >

                                <h2
                                    style='
                                        margin-top:0;
                                        color:#222222;
                                    '
                                >
                                    Password Reset
                                </h2>


                                <p
                                    style='
                                        color:#555555;
                                        line-height:1.6;
                                    '
                                >

                                    Hello {$safe_fullname},

                                </p>


                                <p
                                    style='
                                        color:#555555;
                                        line-height:1.6;
                                    '
                                >

                                    We received a request to reset
                                    the password for your
                                    Velora Drive account.

                                </p>


                                <p
                                    style='
                                        color:#555555;
                                        line-height:1.6;
                                    '
                                >

                                    Your verification code is:

                                </p>


                                <!-- CODE -->

                                <div
                                    style='
                                        text-align:center;
                                        margin:30px 0;
                                    '
                                >

                                    <span
                                        style='
                                            display:inline-block;
                                            padding:15px 28px;
                                            background:#faf5e5;
                                            border:1px solid #e5d49b;
                                            border-radius:10px;
                                            color:#b28f2e;
                                            font-size:30px;
                                            font-weight:bold;
                                            letter-spacing:7px;
                                        '
                                    >

                                        {$verification_code}

                                    </span>

                                </div>


                                <p
                                    style='
                                        color:#777777;
                                        font-size:13px;
                                        line-height:1.6;
                                    '
                                >

                                    This verification code will expire
                                    in <strong>10 minutes</strong>.

                                </p>


                                <p
                                    style='
                                        color:#777777;
                                        font-size:13px;
                                        line-height:1.6;
                                    '
                                >

                                    If you did not request a password
                                    reset, you can safely ignore
                                    this email.

                                </p>

                            </div>


                            <!-- FOOTER -->

                            <div
                                style='
                                    background:#f7f6f2;
                                    padding:18px;
                                    text-align:center;
                                '
                            >

                                <p
                                    style='
                                        margin:0;
                                        color:#999999;
                                        font-size:12px;
                                    '
                                >

                                    © 2026 Velora Drive.
                                    All rights reserved.

                                </p>

                            </div>


                        </div>

                    </body>

                    </html>

                    ";


                    /*
                    |--------------------------------------------------------------------------
                    | RESEND API REQUEST
                    |--------------------------------------------------------------------------
                    */

                    $payload = [

                        "from" =>
                            "Velora Drive <onboarding@resend.dev>",

                        "to" => [$email],

                        "subject" => $subject,

                        "html" => $html

                    ];


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


                    /*
                    |--------------------------------------------------------------------------
                    | SEND REQUEST
                    |--------------------------------------------------------------------------
                    */

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
                    | CHECK CURL ERROR
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $response === false ||
                        !empty($curl_error)
                    ) {

                        $message =
                            "Unable to connect to the email service.";

                        $message_type = "error";

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CHECK RESEND ERROR
                    |--------------------------------------------------------------------------
                    */

                    elseif (
                        $http_code < 200 ||
                        $http_code >= 300
                    ) {


                        /*
                        | Decode Resend response
                        */

                        $resend_data =
                            json_decode(
                                $response,
                                true
                            );


                        /*
                        | Get actual Resend error
                        */

                        $resend_error =
                            $resend_data["message"]
                            ?? "Unknown Resend error";


                        /*
                        | Show actual error
                        */

                        $message =
                            "Resend Error: " .
                            $resend_error;

                        $message_type = "error";

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | EMAIL SENT SUCCESSFULLY
                    |--------------------------------------------------------------------------
                    */

                    else {


                        /*
                        |--------------------------------------------------------------------------
                        | SAVE RESET EMAIL
                        |--------------------------------------------------------------------------
                        */

                        $_SESSION["reset_email"] =
                            $email;


                        /*
                        |--------------------------------------------------------------------------
                        | SAVE VERIFICATION CODE
                        |--------------------------------------------------------------------------
                        */

                        $_SESSION["reset_code"] =
                            (string)$verification_code;


                        /*
                        |--------------------------------------------------------------------------
                        | CODE EXPIRY - 10 MINUTES
                        |--------------------------------------------------------------------------
                        */

                        $_SESSION["reset_expiry"] =
                            time() + (10 * 60);


                        /*
                        |--------------------------------------------------------------------------
                        | RESET CODE NOT VERIFIED YET
                        |--------------------------------------------------------------------------
                        */

                        $_SESSION["code_verified"] =
                            false;


                        /*
                        |--------------------------------------------------------------------------
                        | GO TO RESET PASSWORD PAGE
                        |--------------------------------------------------------------------------
                        */

                        header(
                            "Location: reset_password.php"
                        );

                        exit;

                    }

                }


                $stmt->close();

            }

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


    <title>
        Forgot Password | Velora Drive
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


        /* ==========================================
           RESET
        ========================================== */

        * {

            margin: 0;

            padding: 0;

            box-sizing: border-box;

        }


        /* ==========================================
           BODY
        ========================================== */

        body {

            font-family: "Poppins", sans-serif;

            background:

                linear-gradient(
                    135deg,
                    #f8f7f3 0%,
                    #f1eee5 100%
                );

            min-height: 100vh;

            color: #222;

            display: flex;

            flex-direction: column;

        }


        /* ==========================================
           HEADER
        ========================================== */

        .topbar {

            width: 100%;

            height: 82px;

            background:
                rgba(255,255,255,0.97);

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

            letter-spacing: -0.7px;

        }


        .brand span {

            color: #c8a43b;

        }


        .tagline {

            margin-top: 2px;

            font-size: 11px;

            color: #888;

            letter-spacing: 0.2px;

        }


        /* ==========================================
           MAIN
        ========================================== */

        .main-container {

            flex: 1;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 50px 20px;

        }


        /* ==========================================
           CARD
        ========================================== */

        .forgot-card {

            width: 100%;

            max-width: 470px;

            background: #ffffff;

            border-radius: 20px;

            padding: 42px;

            border:
                1px solid #e6e1d5;

            box-shadow:
                0 20px 55px
                rgba(0,0,0,0.09);

        }


        /* ==========================================
           ICON
        ========================================== */

        .icon-circle {

            width: 72px;

            height: 72px;

            margin:
                0 auto 22px;

            border-radius: 50%;

            background:
                #faf5e5;

            border:
                1px solid #eadcae;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #c8a43b;

            font-size: 28px;

        }


        /* ==========================================
           TITLE
        ========================================== */

        .forgot-card h1 {

            text-align: center;

            font-size: 28px;

            font-weight: 700;

            color: #222;

            margin-bottom: 10px;

        }


        .description {

            text-align: center;

            color: #777;

            font-size: 14px;

            line-height: 1.7;

            margin-bottom: 30px;

        }


        /* ==========================================
           ALERT
        ========================================== */

        .alert {

            padding: 12px 15px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 13px;

            line-height: 1.5;

        }


        .alert.error {

            background: #fff1f1;

            color: #b42318;

            border:
                1px solid #f3c2c2;

        }


        .alert.success {

            background: #effaf1;

            color: #26733a;

            border:
                1px solid #bce5c6;

        }


        /* ==========================================
           FORM
        ========================================== */

        .form-label {

            display: block;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 600;

            color: #333;

        }


        .input-wrapper {

            position: relative;

            margin-bottom: 22px;

        }


        .input-wrapper i {

            position: absolute;

            left: 17px;

            top: 50%;

            transform:
                translateY(-50%);

            color: #aaa;

            font-size: 15px;

            pointer-events: none;

        }


        .email-input {

            width: 100%;

            height: 53px;

            padding:
                0 16px 0 46px;

            border:
                1px solid #dcdcdc;

            border-radius: 11px;

            outline: none;

            background: #fff;

            color: #333;

            font-family:
                "Poppins", sans-serif;

            font-size: 14px;

            transition: 0.25s;

        }


        .email-input::placeholder {

            color: #aaa;

        }


        .email-input:focus {

            border-color:
                #c8a43b;

            box-shadow:
                0 0 0 3px
                rgba(200,164,59,0.12);

        }


        /* ==========================================
           BUTTON
        ========================================== */

        .send-button {

            width: 100%;

            height: 53px;

            border: none;

            border-radius: 11px;

            background:

                linear-gradient(
                    135deg,
                    #d1ad3f,
                    #bd982d
                );

            color: #fff;

            font-family:
                "Poppins", sans-serif;

            font-size: 15px;

            font-weight: 600;

            cursor: pointer;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 9px;

            transition: 0.25s;

        }


        .send-button:hover {

            background:

                linear-gradient(
                    135deg,
                    #c29e32,
                    #ae8b25
                );

            transform:
                translateY(-1px);

            box-shadow:
                0 9px 22px
                rgba(190,150,40,0.25);

        }


        .send-button:active {

            transform:
                translateY(0);

        }


        /* ==========================================
           BACK TO LOGIN
        ========================================== */

        .back-login {

            display: block;

            text-align: center;

            margin-top: 25px;

            color: #666;

            text-decoration: none;

            font-size: 14px;

            transition: 0.2s;

        }


        .back-login:hover {

            color: #c8a43b;

        }


        .back-login i {

            margin-right: 5px;

        }


        /* ==========================================
           FOOTER
        ========================================== */

        .footer {

            height: 46px;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #999;

            font-size: 11px;

        }


        /* ==========================================
           MOBILE
        ========================================== */

        @media (max-width: 600px) {

            .topbar {

                height: 72px;

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


            .forgot-card {

                padding:
                    32px 22px;

                border-radius:
                    16px;

            }


            .forgot-card h1 {

                font-size: 24px;

            }


            .icon-circle {

                width: 64px;

                height: 64px;

                font-size: 25px;

            }

        }


    </style>


</head>


<body>


    <!-- ==========================================
         HEADER
    ========================================== -->

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



    <!-- ==========================================
         MAIN
    ========================================== -->

    <main class="main-container">

        <div class="forgot-card">


            <!-- LOCK ICON -->

            <div class="icon-circle">

                <i class="fa-solid fa-lock"></i>

            </div>



            <!-- TITLE -->

            <h1>

                Forgot Password?

            </h1>


            <p class="description">

                Enter the email address registered
                with your Velora Drive account.
                We'll send you a verification code.

            </p>



            <!-- ERROR / SUCCESS MESSAGE -->

            <?php if (!empty($message)): ?>

                <div
                    class="alert <?php echo htmlspecialchars($message_type); ?>"
                >

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php endif; ?>



            <!-- FORM -->

            <form
                method="POST"
                action=""
            >


                <label class="form-label">

                    Email Address

                </label>


                <div class="input-wrapper">

                    <i class="fa-solid fa-envelope"></i>


                    <input
                        type="email"
                        name="email"
                        class="email-input"
                        placeholder="Enter your email"
                        autocomplete="email"
                        required
                    >

                </div>



                <button
                    type="submit"
                    class="send-button"
                >

                    <i class="fa-solid fa-paper-plane"></i>

                    Send Verification Code

                </button>


            </form>



            <!-- BACK TO LOGIN -->

            <a
                href="index.php"
                class="back-login"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Back to Login

            </a>


        </div>

    </main>



    <!-- ==========================================
         FOOTER
    ========================================== -->

    <footer class="footer">

        © 2026 Velora Drive. All rights reserved.

    </footer>


</body>

</html>