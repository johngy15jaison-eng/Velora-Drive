<?php

session_start();


/* =========================================
   LOGIN CHECK
========================================= */

if (!isset($_SESSION['fullname'])) {

    header("Location: index.php");

    exit();

}


require_once __DIR__ . '/includes/db.php';


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


/* =========================================
   CURRENT USER
========================================= */

$fullname =
    trim($_SESSION['fullname']);


/* =========================================
   GET USER BOOKINGS
========================================= */

$sql = "
    SELECT
        id,
        vehicle_name,
        booking_date,
        return_date,
        pickup_location,
        return_location,
        total_amount,
        payment_method,
        payment_status,
        booking_status
    FROM bookings
    WHERE fullname = ?
    ORDER BY id DESC
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);


if (!$stmt) {

    die(
        "Database error: " .
        mysqli_error($conn)
    );

}


mysqli_stmt_bind_param(
    $stmt,
    "s",
    $fullname
);


mysqli_stmt_execute($stmt);


$result =
    mysqli_stmt_get_result($stmt);


/* =========================================
   CREATE NOTIFICATIONS FROM BOOKINGS
========================================= */

$notifications = [];


while ($row = mysqli_fetch_assoc($result)) {


    $bookingId =
        (int)$row['id'];


    $vehicleName =
        $row['vehicle_name'];


    $bookingStatus =
        strtolower(
            trim(
                $row['booking_status']
            )
        );


    $paymentStatus =
        strtolower(
            trim(
                $row['payment_status']
            )
        );


    /* =====================================
       BOOKING CONFIRMED
    ===================================== */

    if ($bookingStatus === 'confirmed') {


        $notifications[] = [

            'id' =>
                $bookingId,

            'type' =>
                'success',

            'icon' =>
                'fa-circle-check',

            'title' =>
                'Booking Confirmed',

            'message' =>
                'Your ' .
                $vehicleName .
                ' booking has been confirmed successfully.',

            'details' =>
                'Booking ID: BK' .
                str_pad(
                    $bookingId,
                    5,
                    '0',
                    STR_PAD_LEFT
                ),

            'date' =>
                $row['booking_date'],

            'amount' =>
                $row['total_amount'],

            'payment' =>
                $row['payment_method']

        ];

    }


    /* =====================================
       BOOKING PENDING
    ===================================== */

    elseif ($bookingStatus === 'pending') {


        $notifications[] = [

            'id' =>
                $bookingId,

            'type' =>
                'warning',

            'icon' =>
                'fa-clock',

            'title' =>
                'Booking Pending',

            'message' =>
                'Your ' .
                $vehicleName .
                ' booking is currently pending.',

            'details' =>
                'Booking ID: BK' .
                str_pad(
                    $bookingId,
                    5,
                    '0',
                    STR_PAD_LEFT
                ),

            'date' =>
                $row['booking_date'],

            'amount' =>
                $row['total_amount'],

            'payment' =>
                $row['payment_method']

        ];

    }


    /* =====================================
       BOOKING CANCELLED
    ===================================== */

    elseif ($bookingStatus === 'cancelled') {


        $notifications[] = [

            'id' =>
                $bookingId,

            'type' =>
                'danger',

            'icon' =>
                'fa-circle-xmark',

            'title' =>
                'Booking Cancelled',

            'message' =>
                'Your ' .
                $vehicleName .
                ' booking has been cancelled.',

            'details' =>
                'Booking ID: BK' .
                str_pad(
                    $bookingId,
                    5,
                    '0',
                    STR_PAD_LEFT
                ),

            'date' =>
                $row['booking_date'],

            'amount' =>
                $row['total_amount'],

            'payment' =>
                $row['payment_method']

        ];

    }


    /* =====================================
       BOOKING COMPLETED
    ===================================== */

    elseif ($bookingStatus === 'completed') {


        $notifications[] = [

            'id' =>
                $bookingId,

            'type' =>
                'info',

            'icon' =>
                'fa-flag-checkered',

            'title' =>
                'Booking Completed',

            'message' =>
                'Your ' .
                $vehicleName .
                ' rental has been completed.',

            'details' =>
                'Booking ID: BK' .
                str_pad(
                    $bookingId,
                    5,
                    '0',
                    STR_PAD_LEFT
                ),

            'date' =>
                $row['return_date'],

            'amount' =>
                $row['total_amount'],

            'payment' =>
                $row['payment_method']

        ];

    }

}


mysqli_stmt_close($stmt);


/* =========================================
   NOTIFICATION COUNT
========================================= */

$notificationCount =
    count($notifications);

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
    Velora Drive | Notifications
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


html {

    scroll-behavior: smooth;

}


body {

    font-family: 'Poppins', sans-serif;

    background: #f5f7fa;

    color: #102a43;

    min-height: 100vh;

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

    gap: 27px;

}


.nav-links li {

    list-style: none;

}


.nav-links a {

    text-decoration: none;

    color: #102a43;

    font-size: 15px;

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
   PAGE HEADER
========================================= */

.page-header {

    max-width: 1100px;

    margin: auto;

    padding: 55px 30px 30px;

}


.page-label {

    color: #d09a32;

    font-size: 12px;

    font-weight: 700;

    letter-spacing: 2px;

    margin-bottom: 10px;

}


.page-header h1 {

    color: #06263d;

    font-size: 42px;

    margin-bottom: 10px;

}


.page-header h1 span {

    color: #d09a32;

}


.page-header p {

    color: #718096;

    font-size: 15px;

}


/* =========================================
   SUMMARY BAR
========================================= */

.notification-summary {

    max-width: 1040px;

    margin: 0 auto 25px;

    padding: 18px 25px;

    background: #ffffff;

    border: 1px solid #e2e8ee;

    border-radius: 14px;

    box-shadow:
        0 6px 22px rgba(15, 42, 67, 0.05);

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

}


.summary-left {

    display: flex;

    align-items: center;

    gap: 13px;

}


.summary-icon {

    width: 42px;

    height: 42px;

    border-radius: 11px;

    background: #fff6e5;

    color: #c58d29;

    display: flex;

    align-items: center;

    justify-content: center;

}


.summary-left strong {

    color: #06263d;

    font-size: 15px;

}


.summary-left span {

    display: block;

    color: #8a98a8;

    font-size: 12px;

    margin-top: 2px;

}


.notification-number {

    min-width: 34px;

    height: 34px;

    padding: 0 10px;

    border-radius: 18px;

    background: #06263d;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 13px;

    font-weight: 600;

}


/* =========================================
   NOTIFICATION CONTAINER
========================================= */

.notifications-container {

    max-width: 1040px;

    margin: 0 auto;

    padding: 0 20px 60px;

}


/* =========================================
   NOTIFICATION CARD
========================================= */

.notification-card {

    background: #ffffff;

    border: 1px solid #e2e8ee;

    border-radius: 16px;

    padding: 22px 24px;

    margin-bottom: 15px;

    display: flex;

    align-items: flex-start;

    gap: 17px;

    box-shadow:
        0 7px 25px rgba(15, 42, 67, 0.05);

    transition: 0.25s ease;

}


.notification-card:hover {

    transform: translateY(-2px);

    box-shadow:
        0 10px 30px rgba(15, 42, 67, 0.08);

}


/* =========================================
   ICON
========================================= */

.notification-icon {

    flex-shrink: 0;

    width: 52px;

    height: 52px;

    border-radius: 14px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 20px;

}


/* SUCCESS */

.notification-card.success
.notification-icon {

    background: #e9f8f1;

    color: #159568;

}


/* WARNING */

.notification-card.warning
.notification-icon {

    background: #fff7df;

    color: #c58d29;

}


/* DANGER */

.notification-card.danger
.notification-icon {

    background: #fff0f0;

    color: #c53030;

}


/* INFO */

.notification-card.info
.notification-icon {

    background: #edf5ff;

    color: #2777c9;

}


/* =========================================
   CONTENT
========================================= */

.notification-content {

    flex: 1;

    min-width: 0;

}


.notification-top {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 5px;

}


.notification-title {

    color: #06263d;

    font-size: 16px;

    font-weight: 700;

}


.notification-time {

    color: #97a4b1;

    font-size: 11px;

    white-space: nowrap;

}


.notification-message {

    color: #627487;

    font-size: 13px;

    line-height: 1.6;

    margin-bottom: 9px;

}


.notification-details {

    display: flex;

    flex-wrap: wrap;

    gap: 8px 18px;

    margin-top: 7px;

}


.notification-details span {

    color: #7b8997;

    font-size: 11px;

}


.notification-details strong {

    color: #38566e;

    font-weight: 600;

}


/* =========================================
   VIEW BOOKING
========================================= */

.notification-actions {

    margin-top: 13px;

}


.view-booking-btn {

    display: inline-flex;

    align-items: center;

    gap: 7px;

    padding: 8px 13px;

    border-radius: 8px;

    text-decoration: none;

    background: #06263d;

    color: #ffffff;

    font-size: 11px;

    font-weight: 600;

    transition: 0.25s;

}


.view-booking-btn:hover {

    background: #0d3b5c;

}


/* =========================================
   EMPTY
========================================= */

.empty-notifications {

    background: #ffffff;

    border: 1px solid #e2e8ee;

    border-radius: 18px;

    padding: 65px 30px;

    text-align: center;

    box-shadow:
        0 8px 25px rgba(15, 42, 67, 0.05);

}


.empty-icon {

    width: 75px;

    height: 75px;

    border-radius: 50%;

    margin: 0 auto 20px;

    background: #f1f4f7;

    color: #a8b5c0;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 28px;

}


.empty-notifications h2 {

    color: #06263d;

    font-size: 22px;

    margin-bottom: 7px;

}


.empty-notifications p {

    color: #7b8a99;

    font-size: 13px;

    margin-bottom: 20px;

}


.book-vehicle-btn {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 11px 18px;

    border-radius: 9px;

    text-decoration: none;

    background: #06263d;

    color: #ffffff;

    font-size: 13px;

    font-weight: 600;

}


/* =========================================
   FOOTER
========================================= */

footer {

    text-align: center;

    padding: 24px;

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

@media (max-width: 900px) {

    .nav-container {

        padding: 0 20px;

    }


    .nav-links {

        gap: 14px;

    }


    .nav-links a {

        font-size: 13px;

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


    .page-header {

        padding-top: 35px;

    }


    .page-header h1 {

        font-size: 32px;

    }


    .notification-card {

        padding: 18px;

    }


    .notification-top {

        align-items: flex-start;

        flex-direction: column;

        gap: 3px;

    }


    .notification-details {

        flex-direction: column;

        gap: 5px;

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
                    class="active"
                >
                    Notifications
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
     PAGE HEADER
========================================= -->

<section class="page-header">

    <div class="page-label">
        STAY UPDATED
    </div>


    <h1>
        Your <span>Notifications</span>
    </h1>


    <p>
        Stay informed about your Velora Drive bookings
        and rental activity.
    </p>

</section>


<!-- =========================================
     SUMMARY
========================================= -->

<div class="notification-summary">


    <div class="summary-left">


        <div class="summary-icon">

            <i class="fa-solid fa-bell"></i>

        </div>


        <div>

            <strong>
                Recent Notifications
            </strong>

            <span>
                Your latest booking updates
            </span>

        </div>


    </div>


    <div class="notification-number">

        <?php echo $notificationCount; ?>

    </div>


</div>


<!-- =========================================
     NOTIFICATIONS
========================================= -->

<section class="notifications-container">


<?php

if (!empty($notifications)) {

    foreach ($notifications as $notification) {

?>



<div
    class="notification-card <?php echo e(
        $notification['type']
    ); ?>"
>


    <div class="notification-icon">

        <i class="fa-solid <?php echo e(
            $notification['icon']
        ); ?>"></i>

    </div>


    <div class="notification-content">


        <div class="notification-top">


            <div class="notification-title">

                <?php echo e(
                    $notification['title']
                ); ?>

            </div>


            <div class="notification-time">

                <?php

                if (!empty($notification['date'])) {

                    echo e(
                        date(
                            "d M Y",
                            strtotime(
                                $notification['date']
                            )
                        )
                    );

                } else {

                    echo "Recently";

                }

                ?>

            </div>


        </div>


        <div class="notification-message">

            <?php echo e(
                $notification['message']
            ); ?>

        </div>


        <div class="notification-details">


            <span>

                <?php echo e(
                    $notification['details']
                ); ?>

            </span>


            <span>

                Amount:

                <strong>
                    ₹<?php echo number_format(
                        (float)$notification['amount'],
                        2
                    ); ?>
                </strong>

            </span>


            <span>

                Payment:

                <strong>

                    <?php echo e(
                        $notification['payment']
                    ); ?>

                </strong>

            </span>


        </div>


        <div class="notification-actions">

            <a
                href="booking_details.php?id=<?php echo (int)$notification['id']; ?>"
                class="view-booking-btn"
            >

                <i class="fa-solid fa-arrow-right"></i>

                View Booking

            </a>

        </div>


    </div>


</div>


<?php

    }

}

else {

?>


<!-- =========================================
     EMPTY NOTIFICATIONS
========================================= -->

<div class="empty-notifications">


    <div class="empty-icon">

        <i class="fa-regular fa-bell"></i>

    </div>


    <h2>
        No Notifications Yet
    </h2>


    <p>

        You don't have any booking updates yet.

        Once you make a booking, your booking
        confirmation will appear here.

    </p>


    <a
        href="vehicles.php"
        class="book-vehicle-btn"
    >

        <i class="fa-solid fa-car"></i>

        Browse Vehicles

    </a>


</div>


<?php

}

?>


</section>


<!-- =========================================
     FOOTER
========================================= -->

<footer>

    <i class="fa-solid fa-lock"></i>

    Velora Drive • Secure Rental Service

</footer>


</body>

</html>