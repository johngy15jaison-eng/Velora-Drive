<?php

session_start();

include 'config.php';

/* =========================================================
   CHECK LOGIN
========================================================= */

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['email'];


/* =========================================================
   GET BOOKING ID
========================================================= */

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($booking_id <= 0) {
    die("Invalid booking ID.");
}


/* =========================================================
   FETCH BOOKING
========================================================= */

$stmt = $conn->prepare("
    SELECT
        id,
        fullname,
        email,
        vehicle_name,
        vehicle_image,
        pickup_location,
        return_location,
        booking_date,
        return_date,
        total_amount,
        payment_method,
        payment_status,
        booking_status
    FROM bookings
    WHERE id = ? AND email = ?
");

$stmt->bind_param("is", $booking_id, $user_email);

$stmt->execute();

$result = $stmt->get_result();

$booking = $result->fetch_assoc();

$stmt->close();


/* =========================================================
   CHECK BOOKING EXISTS
========================================================= */

if (!$booking) {
    die("Booking not found or you do not have permission to cancel this booking.");
}


/* =========================================================
   CHECK CURRENT BOOKING STATUS
========================================================= */

$current_status = strtolower(trim($booking['booking_status']));

if ($current_status === 'cancelled') {
    header("Location: mybookings.php");
    exit;
}


/* =========================================================
   CHECK BOOKING DATE
========================================================= */

$today = new DateTime();

$booking_date = new DateTime($booking['booking_date']);


/*
   Cancellation is allowed only before the pickup date.
*/

if ($booking_date <= $today) {

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport"
              content="width=device-width, initial-scale=1.0">

        <title>Cannot Cancel Booking | Velora Drive</title>

        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
            rel="stylesheet"
        >

        <style>

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Poppins', sans-serif;
                background: #f5f6f8;
                min-height: 100vh;

                display: flex;
                align-items: center;
                justify-content: center;

                padding: 30px;
            }

            .message-card {
                width: 100%;
                max-width: 600px;

                background: #ffffff;

                padding: 45px;

                border-radius: 20px;

                box-shadow:
                    0 15px 40px rgba(0,0,0,0.08);

                text-align: center;
            }

            .icon {
                width: 70px;
                height: 70px;

                margin: 0 auto 20px;

                border-radius: 50%;

                background: #fff1f1;

                color: #c62828;

                display: flex;
                align-items: center;
                justify-content: center;

                font-size: 32px;
            }

            h1 {
                color: #071d35;

                margin-bottom: 15px;

                font-size: 26px;
            }

            p {
                color: #666;

                line-height: 1.7;

                margin-bottom: 25px;
            }

            .btn {
                display: inline-block;

                padding: 13px 25px;

                background: #071d35;

                color: white;

                text-decoration: none;

                border-radius: 8px;

                font-weight: 600;
            }

            .btn:hover {
                background: #c99732;
            }

        </style>

    </head>

    <body>

        <div class="message-card">

            <div class="icon">
                ✕
            </div>

            <h1>
                Booking Cannot Be Cancelled
            </h1>

            <p>
                This booking has already reached its pickup date.
                Cancellation is no longer available.
            </p>

            <a href="mybookings.php" class="btn">
                Back to My Bookings
            </a>

        </div>

    </body>

    </html>

    <?php

    exit;
}


/* =========================================================
   CANCEL BOOKING
========================================================= */

$new_status = "Cancelled";

$update = $conn->prepare("
    UPDATE bookings
    SET booking_status = ?
    WHERE id = ? AND email = ?
");

$update->bind_param(
    "sis",
    $new_status,
    $booking_id,
    $user_email
);

$update->execute();

$update_success = $update->affected_rows > 0;

$update->close();


/* =========================================================
   SHOW RESULT
========================================================= */

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Booking Cancelled | Velora Drive
    </title>


    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {

            font-family: 'Poppins', sans-serif;

            background: #f5f6f8;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px;

        }


        .success-card {

            width: 100%;

            max-width: 650px;

            background: #ffffff;

            padding: 50px;

            border-radius: 22px;

            text-align: center;

            box-shadow:
                0 20px 50px rgba(0,0,0,0.08);

        }


        .success-icon {

            width: 80px;

            height: 80px;

            margin: 0 auto 25px;

            border-radius: 50%;

            background: #eaf8ef;

            color: #198754;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 40px;

            font-weight: 700;

        }


        h1 {

            color: #071d35;

            font-size: 30px;

            margin-bottom: 15px;

        }


        .message {

            color: #666;

            line-height: 1.7;

            margin-bottom: 30px;

        }


        .booking-details {

            background: #f8f9fb;

            border-radius: 15px;

            padding: 25px;

            text-align: left;

            margin-bottom: 30px;

        }


        .detail {

            display: flex;

            justify-content: space-between;

            gap: 20px;

            padding: 12px 0;

            border-bottom: 1px solid #e5e5e5;

        }


        .detail:last-child {

            border-bottom: none;

        }


        .detail span {

            color: #777;

        }


        .detail strong {

            color: #071d35;

            text-align: right;

        }


        .status {

            color: #c62828 !important;

        }


        .buttons {

            display: flex;

            justify-content: center;

            gap: 12px;

            flex-wrap: wrap;

        }


        .btn {

            display: inline-block;

            padding: 13px 25px;

            border-radius: 9px;

            text-decoration: none;

            font-weight: 600;

            transition: 0.2s;

        }


        .primary-btn {

            background: #071d35;

            color: white;

        }


        .primary-btn:hover {

            background: #c99732;

        }


        .secondary-btn {

            background: #eeeeee;

            color: #333;

        }


        .secondary-btn:hover {

            background: #dddddd;

        }


        .note {

            margin-top: 25px;

            font-size: 13px;

            color: #888;

        }


        @media (max-width: 600px) {

            .success-card {

                padding: 30px 20px;

            }


            h1 {

                font-size: 24px;

            }


            .detail {

                flex-direction: column;

                gap: 4px;

            }


            .detail strong {

                text-align: left;

            }

        }

    </style>

</head>


<body>


<div class="success-card">


    <?php if ($update_success): ?>


        <div class="success-icon">
            ✓
        </div>


        <h1>
            Booking Cancelled
        </h1>


        <p class="message">

            Your Velora Drive booking has been successfully
            cancelled.

        </p>


        <div class="booking-details">


            <div class="detail">

                <span>
                    Booking ID
                </span>

                <strong>
                    #<?php echo htmlspecialchars($booking['id']); ?>
                </strong>

            </div>


            <div class="detail">

                <span>
                    Vehicle
                </span>

                <strong>
                    <?php echo htmlspecialchars($booking['vehicle_name']); ?>
                </strong>

            </div>


            <div class="detail">

                <span>
                    Pickup Date
                </span>

                <strong>
                    <?php echo htmlspecialchars($booking['booking_date']); ?>
                </strong>

            </div>


            <div class="detail">

                <span>
                    Total Amount
                </span>

                <strong>
                    ₹<?php echo number_format(
                        (float)$booking['total_amount'],
                        2
                    ); ?>
                </strong>

            </div>


            <div class="detail">

                <span>
                    Booking Status
                </span>

                <strong class="status">
                    Cancelled
                </strong>

            </div>


        </div>


        <div class="buttons">


            <a
                href="mybookings.php"
                class="btn primary-btn"
            >
                My Bookings
            </a>


            <a
                href="home.php"
                class="btn secondary-btn"
            >
                Back to Home
            </a>


        </div>


        <p class="note">

            Your cancelled booking will remain in your booking
            history for reference.

        </p>


    <?php else: ?>


        <div class="success-icon"
             style="background:#fff1f1;color:#c62828;">

            !

        </div>


        <h1>
            Cancellation Failed
        </h1>


        <p class="message">

            We could not cancel this booking.
            Please try again or contact the Velora Drive administrator.

        </p>


        <div class="buttons">

            <a
                href="mybookings.php"
                class="btn primary-btn"
            >
                Back to My Bookings
            </a>

        </div>


    <?php endif; ?>


</div>


</body>

</html>