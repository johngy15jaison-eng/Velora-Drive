<?php

session_start();

if (!isset($_SESSION['fullname'])) {

    header("Location: index.php");
    exit();

}

require_once __DIR__ . '/includes/db.php';

$fullname = $_SESSION['fullname'];


/* =========================================
   GET BOOKINGS
========================================= */

$sql = "SELECT *
        FROM bookings
        WHERE fullname = ?
        ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $fullname
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


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
    Velora Drive | My Bookings
</title>


<!-- CSS -->

<link
    rel="stylesheet"
    href="css/mybookings.css"
>


<!-- FONT AWESOME -->

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
>


<!-- GOOGLE FONT -->

<link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
>

</head>


<body>


<!-- =========================================
     NAVBAR
========================================= -->

<nav>

    <div class="logo">

        Velora <span>Drive</span>

    </div>


    <ul>

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
            <a
                href="mybookings.php"
                class="active"
            >
                My Bookings
            </a>
        </li>


        <li>
            <a href="notifications.php">
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

</nav>


<!-- =========================================
     SUCCESS MESSAGE
========================================= -->

<?php

if (
    isset($_GET['success']) &&
    $_GET['success'] === "cancelled"
) {

?>

<div class="success-message">

    <i class="fa-solid fa-circle-check"></i>

    Booking cancelled successfully.

</div>

<?php

}

?>


<!-- =========================================
     HERO
========================================= -->

<section class="hero">

    <div class="hero-content">

        <h1>
            My <span>Bookings</span>
        </h1>

        <p>
            View all your vehicle bookings in one place.
        </p>

    </div>

</section>


<!-- =========================================
     SEARCH & FILTER
========================================= -->

<section class="tools">

    <input
        type="text"
        id="searchBooking"
        placeholder="Search Booking"
    >


    <select id="statusFilter">

        <option value="All">
            All
        </option>

        <option value="Confirmed">
            Confirmed
        </option>

        <option value="Pending">
            Pending
        </option>

        <option value="Completed">
            Completed
        </option>

        <option value="Cancelled">
            Cancelled
        </option>

    </select>

</section>


<!-- =========================================
     BOOKINGS
========================================= -->

<section class="bookings">


<?php

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {


        /* =====================================
           VEHICLE IMAGE PATH
        ===================================== */

        $vehicleImage = trim(
            $row['vehicle_image'] ?? ''
        );


        $imagePath = 'images/default-car.png';


        if ($vehicleImage !== '') {

            /*
             * First check the exact path stored
             * in the database.
             */

            $exactPath =
                __DIR__ . '/' .
                ltrim($vehicleImage, '/\\');


            if (file_exists($exactPath)) {

                $imagePath =
                    ltrim(
                        str_replace('\\', '/', $vehicleImage),
                        '/'
                    );

            }

            /*
             * If database contains only the
             * filename, look inside images/.
             */

            else {

                $filename =
                    basename($vehicleImage);


                $imagesPath =
                    __DIR__ . '/images/' .
                    $filename;


                if (file_exists($imagesPath)) {

                    $imagePath =
                        'images/' . $filename;

                }

                /*
                 * Also check uploads/ in case
                 * older bookings used uploads.
                 */

                else {

                    $uploadsPath =
                        __DIR__ . '/uploads/' .
                        $filename;


                    if (file_exists($uploadsPath)) {

                        $imagePath =
                            'uploads/' . $filename;

                    }

                }

            }

        }

?>


<!-- =========================================
     BOOKING CARD
========================================= -->

<div
    class="booking-card"
    data-status="<?php echo e($row['booking_status']); ?>"
>


    <!-- VEHICLE IMAGE -->

    <div class="vehicle-image">

        <img
            src="<?php echo e($imagePath); ?>"
            alt="<?php echo e($row['vehicle_name']); ?>"
            onerror="this.src='images/default-car.png';"
        >

    </div>


    <!-- BOOKING DETAILS -->

    <div class="booking-details">


        <h2>

            <?php echo e($row['vehicle_name']); ?>

        </h2>


        <div class="booking-info">


            <!-- BOOKING ID -->

            <p>

                <strong>
                    Booking ID :
                </strong>

                BK<?php echo str_pad(
                    $row['id'],
                    5,
                    "0",
                    STR_PAD_LEFT
                ); ?>

            </p>


            <!-- PICKUP DATE -->

            <p>

                <strong>
                    Pickup Date :
                </strong>

                <?php

                echo !empty($row['booking_date'])
                    ? date(
                        "d M Y",
                        strtotime($row['booking_date'])
                    )
                    : '-';

                ?>

            </p>


            <!-- RETURN DATE -->

            <p>

                <strong>
                    Return Date :
                </strong>

                <?php

                echo !empty($row['return_date'])
                    ? date(
                        "d M Y",
                        strtotime($row['return_date'])
                    )
                    : '-';

                ?>

            </p>


            <!-- PICKUP LOCATION -->

            <p>

                <strong>
                    Pickup Location :
                </strong>

                <?php echo e(
                    $row['pickup_location']
                ); ?>

            </p>


            <!-- RETURN LOCATION -->

            <p>

                <strong>
                    Return Location :
                </strong>

                <?php echo e(
                    $row['return_location']
                ); ?>

            </p>


            <!-- TOTAL AMOUNT -->

            <p>

                <strong>
                    Total Amount :
                </strong>

                ₹<?php echo number_format(
                    (float)$row['total_amount'],
                    2
                ); ?>

            </p>


            <!-- PAYMENT -->

            <p>

                <strong>
                    Payment :
                </strong>

                <span class="payment">

                    <?php echo e(
                        $row['payment_status']
                    ); ?>

                </span>

            </p>


            <!-- STATUS -->

            <p>

                <strong>
                    Status :
                </strong>

                <span
                    class="status <?php echo strtolower(
                        e($row['booking_status'])
                    ); ?>"
                >

                    <?php echo e(
                        $row['booking_status']
                    ); ?>

                </span>

            </p>


        </div>


        <!-- =====================================
             BUTTONS
        ====================================== -->

        <div class="booking-buttons">


            <a
                href="booking_details.php?id=<?php echo (int)$row['id']; ?>"
                class="view-btn"
            >

                View Details

            </a>


            <?php

            if (
                $row['booking_status'] === "Confirmed" ||
                $row['booking_status'] === "Pending"
            ) {

            ?>

                <a
                    href="cancel_booking.php?id=<?php echo (int)$row['id']; ?>"
                    class="cancel-btn"
                    onclick="return confirm(
                        'Are you sure you want to cancel this booking?'
                    );"
                >

                    Cancel Booking

                </a>

            <?php

            }

            ?>


        </div>


    </div>


</div>


<?php

    }

}

else {

?>


<!-- =========================================
     EMPTY BOOKING
========================================= -->

<div class="empty-booking">

    <i class="fa-solid fa-car"></i>

    <h2>
        No Bookings Found
    </h2>

    <p>
        You haven't booked any vehicle yet.
    </p>

    <a href="vehicles.php">
        Book a Vehicle
    </a>

</div>


<?php

}

?>


</section>


<!-- =========================================
     JAVASCRIPT
========================================= -->

<script src="js/mybookings.js"></script>


</body>

</html>

<?php

mysqli_stmt_close($stmt);

?>