<?php

session_start();

require_once __DIR__ . '/includes/db.php';


// ---------------------------------------------------------
// RESEND API KEY
// ---------------------------------------------------------

$resend_api_key = getenv("RESEND_API_KEY");


// ---------------------------------------------------------
// CHECK API KEY
// ---------------------------------------------------------

if (!$resend_api_key) {
    die("Email service configuration is missing.");
}


// ---------------------------------------------------------
// PROCESS FORM
// ---------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");


    // -----------------------------------------------------
    // VALIDATE EMAIL
    // -----------------------------------------------------

    if (empty($email)) {
        $error = "Please enter your email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";

    } else {

        // -------------------------------------------------
        // FIND USER
        // -------------------------------------------------

        $stmt = $conn->prepare(
            "SELECT id, fullname FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {

            $error = "No account was found with that email address.";

        } else {

            $user = $result->fetch_assoc();

            $fullname = $user["fullname"];


            // ---------------------------------------------
            // GENERATE 6-DIGIT VERIFICATION CODE
            // ---------------------------------------------

            $reset_code = random_int(100000, 999999);


            // ---------------------------------------------
            // EMAIL CONTENT
            // ---------------------------------------------

            $subject = "Velora Drive - Password Reset Verification Code";

            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Velora Drive Password Reset</title>
            </head>

            <body style="
                margin:0;
                padding:0;
                background:#f5f5f5;
                font-family:Arial, sans-serif;
            ">

                <div style="
                    max-width:600px;
                    margin:40px auto;
                    background:#ffffff;
                    border-radius:12px;
                    overflow:hidden;
                    box-shadow:0 4px 20px rgba(0,0,0,0.08);
                ">

                    <div style="
                        background:#111111;
                        padding:25px;
                        text-align:center;
                    ">

                        <h1 style="
                            margin:0;
                            color:#ffffff;
                            font-size:28px;
                        ">
                            Velora
                            <span style="color:#c8a43b;">
                                Drive
                            </span>
                        </h1>

                    </div>


                    <div style="
                        padding:35px;
                        color:#333333;
                    ">

                        <h2 style="
                            margin-top:0;
                            color:#222222;
                        ">
                            Password Reset
                        </h2>


                        <p>
                            Hello ' . htmlspecialchars($fullname) . ',
                        </p>


                        <p>
                            We received a request to reset the password
                            for your Velora Drive account.
                        </p>


                        <p>
                            Your verification code is:
                        </p>


                        <div style="
                            margin:30px 0;
                            text-align:center;
                        ">

                            <span style="
                                display:inline-block;
                                padding:18px 30px;
                                background:#f8f3e5;
                                color:#c8a43b;
                                font-size:32px;
                                font-weight:bold;
                                letter-spacing:8px;
                                border-radius:8px;
                            ">
                                ' . $reset_code . '
                            </span>

                        </div>


                        <p>
                            This code is valid for
                            <strong>10 minutes</strong>.
                        </p>


                        <p>
                            If you did not request a password reset,
                            you can safely ignore this email.
                        </p>


                        <p style="
                            margin-top:30px;
                        ">
                            Regards,<br>
                            <strong>Velora Drive Team</strong>
                        </p>

                    </div>


                    <div style="
                        padding:20px;
                        background:#f7f7f7;
                        text-align:center;
                        color:#888888;
                        font-size:12px;
                    ">
                        © Velora Drive. All rights reserved.
                    </div>

                </div>

            </body>
            </html>
            ';


            // -------------------------------------------------
            // SEND EMAIL USING RESEND HTTPS API
            // -------------------------------------------------

            $payload = [
                "from" => "Velora Drive <onboarding@resend.dev>",
                "to" => [$email],
                "subject" => $subject,
                "html" => $html,
            ];


            $ch = curl_init("https://api.resend.com/emails");

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer " . $resend_api_key,
                "Content-Type: application/json"
            ]);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_TIMEOUT, 15);


            $response = curl_exec($ch);

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $curl_error = curl_error($ch);

            curl_close($ch);


            // -------------------------------------------------
            // CHECK RESEND RESPONSE
            // -------------------------------------------------

            if ($response === false || !empty($curl_error)) {

                $error =
                    "Unable to send the verification email. " .
                    "Please try again later.";

            } elseif ($http_code < 200 || $http_code >= 300) {

                $error =
                    "Unable to send the verification email. " .
                    "Please try again later.";

            } else {

                // ---------------------------------------------
                // SAVE RESET INFORMATION IN SESSION
                // ---------------------------------------------

                $_SESSION["reset_email"] = $email;

                $_SESSION["reset_code"] = (string)$reset_code;

                $_SESSION["reset_expiry"] = time() + 600;


                // ---------------------------------------------
                // REDIRECT TO RESET PASSWORD PAGE
                // ---------------------------------------------

                header("Location: reset_password.php");

                exit;
            }
        }

        $stmt->close();
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
        Velora Drive | Forgot Password
    </title>


    <link
        rel="stylesheet"
        href="css/index.css"
    >


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    >

</head>


<body>


<div class="login-container">

    <div class="login-card">


        <div class="logo">

            Velora
            <span>Drive</span>

        </div>


        <p class="tagline">
            Vehicle Rental Management
        </p>


        <div class="login-icon">

            <i class="fa-solid fa-lock"></i>

        </div>


        <h2>
            Forgot Password?
        </h2>


        <p class="description">

            Enter the email address registered with
            your Velora Drive account.
            We'll send you a verification code.

        </p>


        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="input-group">

                <label>
                    Email Address
                </label>


                <div class="input-wrapper">

                    <i class="fa-solid fa-envelope"></i>


                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                        required
                    >

                </div>

            </div>


            <button
                type="submit"
                class="login-btn"
            >

                <i class="fa-solid fa-paper-plane"></i>

                Send Verification Code

            </button>


        </form>


        <div class="register-link">

            <a href="index.php">

                <i class="fa-solid fa-arrow-left"></i>

                Back to Login

            </a>

        </div>


    </div>

</div>


</body>

</html>