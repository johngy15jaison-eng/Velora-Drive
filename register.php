<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Velora Drive | Register</title>

<link rel="stylesheet" href="css/register.css">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<div class="container">

    <!-- LEFT PANEL -->

    <div class="left">

        <div class="overlay"></div>

        <div class="content">

            <h1>

                Velora <span>Drive</span>

            </h1>

            <h2>

                Create Your Account

            </h2>

            <p>

                Join our premium vehicle rental service and enjoy luxury journeys with comfort, safety and convenience.

            </p>

            <div class="features">

                <div>

                    <i class="fa-solid fa-car-side"></i>

                    Luxury Vehicles

                </div>

                <div>

                    <i class="fa-solid fa-calendar-check"></i>

                    Easy Booking

                </div>

                <div>

                    <i class="fa-solid fa-credit-card"></i>

                    Secure Payment

                </div>

                <div>

                    <i class="fa-solid fa-headset"></i>

                    24/7 Support

                </div>

            </div>

        </div>

    </div>

    <!-- RIGHT PANEL -->

    <div class="right">

        <div class="register-box">

            <h2>

                Register

            </h2>

            <form
            action="register_action.php"
            method="POST"
            id="registerForm">

                <label>

                    Full Name

                </label>

                <input
                type="text"
                name="fullname"
                placeholder="Enter your full name"
                required>

                <label>

                    Email Address

                </label>

                <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required>

                <label>

                    Password

                </label>

                <div class="password-wrapper">

                    <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create Password"
                    required>

                    <i
                    class="fa-solid fa-eye password-toggle"
                    onclick="togglePassword()"></i>

                </div>

                <!-- Password Strength -->

                <div class="strength">

                    <div class="strength-head">

                        <span>

                            Password Strength

                        </span>

                        <span id="strengthText">

                            Weak

                        </span>

                    </div>

                    <div class="strength-bar">

                        <div id="strengthFill"></div>

                    </div>

                </div>

                <button
                type="submit"
                name="register">

                    Create Account

                </button>

            </form>

            <div class="login-link">

                Already have an account?

                <a href="index.php">

                    Login

                </a>

            </div>

        </div>

    </div>

</div>

<script src="js/register.js"></script>

</body>

</html>