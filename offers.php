<?php

session_start();

if (!isset($_SESSION['fullname'])) {
    header("Location: index.php");
    exit();
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
        Velora Drive | Offers & Deals
    </title>


    <!-- GOOGLE FONT -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >


    <style>

        /* =========================================
           RESET
        ========================================= */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {

            font-family: 'Poppins', sans-serif;

            background: #f5f7fa;

            color: #102a43;

        }


        /* =========================================
           NAVBAR
        ========================================= */

        nav {

            width: 100%;

            background: #ffffff;

            border-bottom: 1px solid #e5e7eb;

            position: sticky;

            top: 0;

            z-index: 1000;

        }


        .nav-container {

            max-width: 1280px;

            min-height: 86px;

            margin: auto;

            padding: 0 35px;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .logo {

            text-decoration: none;

            color: #06263d;

            font-size: 32px;

            font-weight: 700;

            letter-spacing: -1px;

        }


        .logo span {

            color: #d09a32;

        }


        .nav-links {

            list-style: none;

            display: flex;

            align-items: center;

            gap: 25px;

        }


        .nav-links li {

            list-style: none;

        }


        .nav-links a {

            text-decoration: none;

            color: #102a43;

            font-size: 14px;

            font-weight: 500;

            transition: 0.3s;

        }


        .nav-links a:hover {

            color: #d09a32;

        }


        .nav-links a.active {

            color: #d09a32;

            font-weight: 600;

        }


        /* =========================================
           HERO
        ========================================= */

        .offers-hero {

            max-width: 1280px;

            margin: auto;

            padding: 65px 35px 45px;

        }


        .offer-label {

            color: #d09a32;

            font-size: 12px;

            font-weight: 700;

            letter-spacing: 2px;

            margin-bottom: 12px;

        }


        .offers-hero h1 {

            font-size: 44px;

            line-height: 1.2;

            color: #06263d;

            margin-bottom: 12px;

        }


        .offers-hero h1 span {

            color: #d09a32;

        }


        .offers-hero p {

            max-width: 650px;

            color: #718096;

            font-size: 15px;

            line-height: 1.7;

        }


        /* =========================================
           OFFERS GRID
        ========================================= */

        .offers-container {

            max-width: 1280px;

            margin: 0 auto;

            padding: 0 35px 70px;

        }


        .offers-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 25px;

        }


        /* =========================================
           OFFER CARD
        ========================================= */

        .offer-card {

            background: #ffffff;

            border: 1px solid #e2e8ee;

            border-radius: 20px;

            overflow: hidden;

            box-shadow:
                0 8px 30px
                rgba(15, 42, 67, 0.06);

            transition: 0.3s ease;

            position: relative;

        }


        .offer-card:hover {

            transform: translateY(-5px);

            box-shadow:
                0 14px 35px
                rgba(15, 42, 67, 0.10);

        }


        /* =========================================
           OFFER TOP
        ========================================= */

        .offer-top {

            min-height: 175px;

            padding: 30px;

            display: flex;

            flex-direction: column;

            justify-content: center;

            align-items: flex-start;

        }


        .offer-top.gold {

            background:
                linear-gradient(
                    135deg,
                    #fff7e2,
                    #fdf0c9
                );

        }


        .offer-top.blue {

            background:
                linear-gradient(
                    135deg,
                    #edf6fc,
                    #dcebf5
                );

        }


        .offer-top.green {

            background:
                linear-gradient(
                    135deg,
                    #eaf8f2,
                    #d9f0e5
                );

        }


        .offer-icon {

            width: 52px;

            height: 52px;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 17px;

            font-size: 23px;

        }


        .gold .offer-icon {

            background: #ffffff;

            color: #c18b27;

        }


        .blue .offer-icon {

            background: #ffffff;

            color: #24668e;

        }


        .green .offer-icon {

            background: #ffffff;

            color: #16865f;

        }


        .discount {

            font-size: 31px;

            font-weight: 700;

            color: #06263d;

        }


        .offer-body {

            padding: 25px 28px 28px;

        }


        .offer-body h2 {

            color: #06263d;

            font-size: 20px;

            margin-bottom: 9px;

        }


        .offer-body p {

            color: #718096;

            font-size: 13px;

            line-height: 1.7;

            min-height: 65px;

        }


        .offer-code {

            margin-top: 18px;

            padding: 10px 12px;

            border-radius: 9px;

            background: #f7f9fb;

            border: 1px dashed #d4dce4;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

        }


        .offer-code span {

            color: #8a98a8;

            font-size: 11px;

        }


        .offer-code strong {

            color: #06263d;

            font-size: 13px;

            letter-spacing: 1px;

        }


        .offer-btn {

            width: 100%;

            height: 48px;

            margin-top: 18px;

            border-radius: 9px;

            background: #06263d;

            color: #ffffff;

            text-decoration: none;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            font-size: 13px;

            font-weight: 600;

            transition: 0.25s;

        }


        .offer-btn:hover {

            background: #0d3b5c;

        }


        /* =========================================
           TERMS
        ========================================= */

        .offer-terms {

            text-align: center;

            margin-top: 30px;

            color: #9aa7b4;

            font-size: 11px;

        }


        /* =========================================
           WHY OFFERS
        ========================================= */

        .benefits {

            max-width: 1280px;

            margin: 0 auto;

            padding: 0 35px 75px;

        }


        .benefits h2 {

            text-align: center;

            color: #06263d;

            font-size: 27px;

            margin-bottom: 28px;

        }


        .benefit-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 22px;

        }


        .benefit {

            background: #ffffff;

            border: 1px solid #e2e8ee;

            border-radius: 16px;

            padding: 25px;

            text-align: center;

        }


        .benefit i {

            width: 48px;

            height: 48px;

            border-radius: 13px;

            background: #fff7e6;

            color: #c58d29;

            display: flex;

            align-items: center;

            justify-content: center;

            margin: 0 auto 15px;

            font-size: 20px;

        }


        .benefit h3 {

            color: #06263d;

            font-size: 16px;

            margin-bottom: 7px;

        }


        .benefit p {

            color: #7b8a99;

            font-size: 12px;

            line-height: 1.6;

        }


        /* =========================================
           FOOTER
        ========================================= */

        footer {

            text-align: center;

            padding: 25px;

            background: #ffffff;

            border-top: 1px solid #e5e9ed;

            color: #8a98a8;

            font-size: 12px;

        }


        footer i {

            color: #d09a32;

            margin-right: 6px;

        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 950px) {

            .nav-container {

                padding: 0 20px;

            }


            .nav-links {

                gap: 13px;

            }


            .nav-links a {

                font-size: 12px;

            }


            .offers-grid {

                grid-template-columns:
                    1fr 1fr;

            }


            .benefit-grid {

                grid-template-columns:
                    1fr 1fr;

            }

        }


        @media (max-width: 700px) {

            .nav-container {

                flex-direction: column;

                padding: 18px 15px;

                gap: 15px;

            }


            .nav-links {

                flex-wrap: wrap;

                justify-content: center;

            }


            .offers-hero {

                padding: 40px 20px 30px;

            }


            .offers-hero h1 {

                font-size: 32px;

            }


            .offers-container {

                padding: 0 20px 50px;

            }


            .offers-grid {

                grid-template-columns: 1fr;

            }


            .benefits {

                padding: 0 20px 55px;

            }


            .benefit-grid {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<!-- =========================================
     NAVBAR
========================================= -->

<nav>

    <div class="nav-container">


        <a
            href="home.php"
            class="logo"
        >

            Velora <span>Drive</span>

        </a>


        <ul class="nav-links">


            <li>

                <a href="home.php">

                    Home

                </a>

            </li>


            <li>

                <a href="vehicles.php">

                    Vehicles

                </a>

            </li>


            <li>

                <a href="mybookings.php">

                    My Bookings

                </a>

            </li>


            <li>

                <a
                    href="notifications.php"
                >

                    Notifications

                </a>

            </li>


            <li>

                <a
                    href="offers.php"
                    class="active"
                >

                    Offers

                </a>

            </li>


            <li>

                <a href="profile.php">

                    Profile

                </a>

            </li>


            <li>

                <a href="about.php">

                    About

                </a>

            </li>


            <li>

                <a href="contact.php">

                    Contact

                </a>

            </li>


            <li>

                <a href="faq.php">

                    FAQ

                </a>

            </li>


            <li>

                <a href="logout.php">

                    Logout

                </a>

            </li>


        </ul>


    </div>

</nav>


<!-- =========================================
     HERO
========================================= -->

<section class="offers-hero">


    <div class="offer-label">

        EXCLUSIVE DEALS

    </div>


    <h1>

        Save More on Your
        <span>Next Ride</span>

    </h1>


    <p>

        Discover special rental offers from
        Velora Drive and enjoy premium vehicles
        at better prices.

    </p>


</section>


<!-- =========================================
     OFFERS
========================================= -->

<section class="offers-container">


    <div class="offers-grid">


        <!-- =====================================
             OFFER 1
        ====================================== -->

        <div class="offer-card">


            <div class="offer-top gold">


                <div class="offer-icon">

                    <i class="fa-solid fa-calendar-week"></i>

                </div>


                <div class="discount">

                    20% OFF

                </div>


            </div>


            <div class="offer-body">


                <h2>

                    Weekend Drive

                </h2>


                <p>

                    Enjoy 20% off selected vehicles
                    when you make a weekend rental.

                </p>


                <div class="offer-code">

                    <span>

                        Offer Code

                    </span>

                    <strong>

                        WEEKEND20

                    </strong>

                </div>


                <a
                    href="vehicles.php"
                    class="offer-btn"
                >

                    Browse Vehicles

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


            </div>


        </div>


        <!-- =====================================
             OFFER 2
        ====================================== -->

        <div class="offer-card">


            <div class="offer-top blue">


                <div class="offer-icon">

                    <i class="fa-solid fa-car-side"></i>

                </div>


                <div class="discount">

                    15% OFF

                </div>


            </div>


            <div class="offer-body">


                <h2>

                    Long Rental

                </h2>


                <p>

                    Plan a longer trip and enjoy
                    15% off rentals of five days
                    or more.

                </p>


                <div class="offer-code">

                    <span>

                        Offer Code

                    </span>

                    <strong>

                        LONG15

                    </strong>

                </div>


                <a
                    href="vehicles.php"
                    class="offer-btn"
                >

                    Browse Vehicles

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


            </div>


        </div>


        <!-- =====================================
             OFFER 3
        ====================================== -->

        <div class="offer-card">


            <div class="offer-top green">


                <div class="offer-icon">

                    <i class="fa-solid fa-crown"></i>

                </div>


                <div class="discount">

                    10% OFF

                </div>


            </div>


            <div class="offer-body">


                <h2>

                    Premium Weekend

                </h2>


                <p>

                    Get 10% off selected premium
                    and luxury vehicles for your
                    next weekend trip.

                </p>


                <div class="offer-code">

                    <span>

                        Offer Code

                    </span>

                    <strong>

                        PREMIUM10

                    </strong>

                </div>


                <a
                    href="vehicles.php"
                    class="offer-btn"
                >

                    Explore Premium

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


            </div>


        </div>


    </div>


    <div class="offer-terms">

        Offers are subject to availability.
        One promotional offer may be applied
        per booking.

    </div>


</section>


<!-- =========================================
     BENEFITS
========================================= -->

<section class="benefits">


    <h2>

        Why Book With Velora Drive?

    </h2>


    <div class="benefit-grid">


        <div class="benefit">


            <i class="fa-solid fa-tags"></i>


            <h3>

                Better Prices

            </h3>


            <p>

                Enjoy special offers and
                competitive rental prices.

            </p>


        </div>


        <div class="benefit">


            <i class="fa-solid fa-car"></i>


            <h3>

                Premium Vehicles

            </h3>


            <p>

                Choose from a growing range
                of comfortable and reliable vehicles.

            </p>


        </div>


        <div class="benefit">


            <i class="fa-solid fa-shield-halved"></i>


            <h3>

                Secure Booking

            </h3>


            <p>

                Your booking details are handled
                securely throughout the rental process.

            </p>


        </div>


    </div>


</section>


<!-- =========================================
     FOOTER
========================================= -->

<footer>

    <i class="fa-solid fa-lock"></i>

    Velora Drive • Premium Vehicle Rental

</footer>


</body>

</html>