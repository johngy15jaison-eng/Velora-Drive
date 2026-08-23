<?php

session_start();

/* =========================================
   GET BOOKING INFORMATION FROM SESSION
========================================= */

$booking_id =
    $_SESSION['booking_id'] ?? 0;

$vehicle_name =
    $_SESSION['booking_vehicle'] ?? 'Your Vehicle';

$total_amount =
    $_SESSION['booking_total'] ?? 0;


/* =========================================
   FORMAT BOOKING ID
========================================= */

$display_booking_id =
    'VD-' . str_pad(
        $booking_id,
        5,
        '0',
        STR_PAD_LEFT
    );


/* =========================================
   HELPER FUNCTION
========================================= */

function e($value)
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
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
        Booking Confirmed | Velora Drive
    </title>


    <!-- GOOGLE FONT -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
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

            background: #f5f7fb;

            color: #06263d;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 40px 20px;

        }


        /* =========================================
           MAIN CONTAINER
        ========================================= */

        .success-container {

            width: 100%;

            max-width: 820px;

            background: #ffffff;

            border-radius: 26px;

            padding: 55px 65px;

            box-shadow:
                0 20px 60px rgba(6, 38, 61, 0.08);

            text-align: center;

        }


        /* =========================================
           SUCCESS ICON
        ========================================= */

        .success-icon {

            width: 114px;

            height: 114px;

            margin: 0 auto 35px;

            border-radius: 50%;

            background: #e7f8f1;

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .success-icon i {

            font-size: 58px;

            color: #18a76b;

        }


        /* =========================================
           TITLE
        ========================================= */

        .success-container h1 {

            font-size: 40px;

            font-weight: 700;

            color: #071f52;

            margin-bottom: 18px;

        }


        .success-message {

            font-size: 18px;

            line-height: 1.7;

            color: #6b819d;

            max-width: 650px;

            margin: 0 auto 40px;

        }


        /* =========================================
           BOOKING DETAILS
        ========================================= */

        .booking-details {

            background: #f7f8fc;

            border-radius: 18px;

            padding: 25px 30px;

            margin-bottom: 35px;

            text-align: left;

        }


        .booking-row {

            min-height: 57px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 25px;

            border-bottom: 1px solid #dfe4ec;

        }


        .booking-row:last-child {

            border-bottom: none;

        }


        .booking-row span {

            font-size: 16px;

            color: #71839c;

        }


        .booking-row strong {

            font-size: 16px;

            font-weight: 700;

            color: #071f52;

            text-align: right;

        }


        /* =========================================
           TOTAL
        ========================================= */

        .booking-row.total-row strong {

            font-size: 23px;

            color: #cf9622;

        }


        /* =========================================
           BUTTONS
        ========================================= */

        .button-container {

            display: flex;

            justify-content: center;

            align-items: center;

            gap: 18px;

            flex-wrap: wrap;

        }


        .btn {

            min-width: 210px;

            height: 62px;

            padding: 0 25px;

            border-radius: 11px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 10px;

            text-decoration: none;

            font-size: 16px;

            font-weight: 600;

            transition: 0.25s ease;

        }


        /* PRIMARY BUTTON */

        .btn-primary {

            background: #071f52;

            color: #ffffff;

        }


        .btn-primary:hover {

            background: #06263d;

            transform: translateY(-2px);

        }


        /* SECONDARY BUTTON */

        .btn-secondary {

            background: #ffffff;

            color: #071f52;

            border: 1px solid #d5dce6;

        }


        .btn-secondary:hover {

            border-color: #071f52;

            transform: translateY(-2px);

        }


        /* =========================================
           FOOTER
        ========================================= */

        .footer-note {

            margin-top: 35px;

            color: #8293aa;

            font-size: 14px;

        }


        .footer-note i {

            color: #cf9622;

            margin-right: 7px;

        }


        /* =========================================
           MOBILE
        ========================================= */

        @media (max-width: 650px) {

            body {

                padding: 20px 12px;

            }


            .success-container {

                padding: 40px 22px;

                border-radius: 20px;

            }


            .success-icon {

                width: 90px;

                height: 90px;

            }


            .success-icon i {

                font-size: 45px;

            }


            .success-container h1 {

                font-size: 30px;

            }


            .success-message {

                font-size: 15px;

            }


            .booking-details {

                padding: 18px;

            }


            .booking-row {

                min-height: 65px;

            }


            .booking-row span,

            .booking-row strong {

                font-size: 14px;

            }


            .booking-row.total-row strong {

                font-size: 19px;

            }


            .button-container {

                flex-direction: column;

            }


            .btn {

                width: 100%;

            }

        }

    </style>

</head>


<body>


    <!-- =========================================
         SUCCESS CARD
    ========================================= -->

    <div class="success-container">


        <!-- SUCCESS ICON -->

        <div class="success-icon">

            <i class="fa-solid fa-check"></i>

        </div>


        <!-- TITLE -->

        <h1>
            Booking Confirmed!
        </h1>


        <p class="success-message">

            Your Velora Drive booking has been successfully
            confirmed. Thank you for choosing us.

        </p>


        <!-- =====================================
             BOOKING DETAILS
        ====================================== -->

        <div class="booking-details">


            <!-- BOOKING ID -->

            <div class="booking-row">

                <span>
                    Booking ID
                </span>

                <strong>
                    <?php echo e($display_booking_id); ?>
                </strong>

            </div>


            <!-- VEHICLE -->

            <div class="booking-row">

                <span>
                    Vehicle
                </span>

                <strong>
                    <?php echo e($vehicle_name); ?>
                </strong>

            </div>


            <!-- PAYMENT STATUS -->

            <div class="booking-row">

                <span>
                    Payment Status
                </span>

                <strong>
                    Confirmed
                </strong>

            </div>


            <!-- TOTAL AMOUNT -->

            <div class="booking-row total-row">

                <span>
                    Total Amount
                </span>

                <strong>
                    ₹<?php echo number_format(
                        (float)$total_amount,
                        2
                    ); ?>
                </strong>

            </div>


        </div>


        <!-- =====================================
             BUTTONS
        ====================================== -->

        <div class="button-container">


            <a
                href="mybookings.php"
                class="btn btn-primary"
            >

                <i class="fa-solid fa-calendar-check"></i>

                View My Bookings

            </a>


            <a
                href="home.php"
                class="btn btn-secondary"
            >

                <i class="fa-solid fa-house"></i>

                Back to Home

            </a>


        </div>


        <!-- =====================================
             FOOTER NOTE
        ====================================== -->

        <div class="footer-note">

            <i class="fa-solid fa-lock"></i>

            Velora Drive • Secure Booking Checkout

        </div>


    </div>


</body>

</html>