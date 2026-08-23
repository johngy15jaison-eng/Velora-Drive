<?php

session_start();

require_once __DIR__ . '/includes/db.php';


/* =========================================================
   CHECK LOGIN
========================================================= */

if (!isset($_SESSION['fullname'])) {

    header("Location: login.php");

    exit;

}


/* =========================================================
   CHECK VEHICLE ID
========================================================= */

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {

    die("Invalid vehicle selected.");

}

$vehicle_id = (int) $_GET['id'];


/* =========================================================
   GET VEHICLE FROM DATABASE
========================================================= */

$sql = "
    SELECT
        id,
        vehicle_name,
        image,
        price,
        category
    FROM vehicles
    WHERE id = ?
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);


if (!$stmt) {

    die("Database query error.");

}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $vehicle_id
);


mysqli_stmt_execute($stmt);


$result =
    mysqli_stmt_get_result($stmt);


if (
    !$result ||
    mysqli_num_rows($result) === 0
) {

    die("Invalid vehicle selected.");

}


$vehicle =
    mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


/* =========================================================
   VEHICLE INFORMATION
========================================================= */

$pricePerDay =
    (float) $vehicle['price'];

$vehicleName =
    $vehicle['vehicle_name'];

$vehicleImage =
    $vehicle['image'];

$vehicleCategory =
    $vehicle['category'];


/* =========================================================
   VEHICLE IMAGE PATH
========================================================= */

if (!empty($vehicleImage)) {

    if (
        strpos($vehicleImage, '/') === false &&
        strpos($vehicleImage, '\\') === false &&
        !preg_match(
            '/^https?:\/\//i',
            $vehicleImage
        )
    ) {

        $vehicleImagePath =
            "images/" . $vehicleImage;

    } else {

        $vehicleImagePath =
            $vehicleImage;

    }

} else {

    $vehicleImagePath = "";

}


/* =========================================================
   SAFE OUTPUT FUNCTION
========================================================= */

function e($value)
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}


/* =========================================================
   PREMIUM VEHICLE RULE
========================================================= */

/*
   For the current project, a vehicle is treated as
   premium when its daily rental price is ₹2,000 or more.

   This avoids changing your existing vehicles table.
*/

$isPremiumVehicle =
    $pricePerDay >= 2000;

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
    Velora Drive | Booking
</title>


<!-- FONT AWESOME -->

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
>


<!-- GOOGLE FONT -->

<link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
>


<style>

/* =========================================================
   RESET
========================================================= */

* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

}


body {

    font-family: 'Poppins', sans-serif;

    background: #f5f7fa;

    color: #071b34;

    line-height: 1.6;

}


/* =========================================================
   NAVBAR
========================================================= */

.navbar {

    background: #ffffff;

    height: 82px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 7%;

    border-bottom: 1px solid #e8e8e8;

    position: sticky;

    top: 0;

    z-index: 1000;

}


.logo {

    text-decoration: none;

    font-size: 29px;

    font-weight: 700;

    color: #071b34;

}


.logo span {

    color: #d19b32;

}


.nav-links {

    display: flex;

    align-items: center;

    gap: 30px;

    list-style: none;

}


.nav-links a {

    text-decoration: none;

    color: #071b34;

    font-size: 14px;

    font-weight: 500;

    transition: 0.3s;

}


.nav-links a:hover {

    color: #d19b32;

}


/* =========================================================
   MAIN CONTAINER
========================================================= */

.container {

    width: 90%;

    max-width: 1250px;

    margin: auto;

    padding: 50px 0 80px;

}


/* =========================================================
   PAGE HEADER
========================================================= */

.page-header {

    margin-bottom: 35px;

}


.small-title {

    color: #d19b32;

    font-size: 13px;

    font-weight: 700;

    letter-spacing: 3px;

    text-transform: uppercase;

}


.page-header h1 {

    font-size: 42px;

    margin: 8px 0;

    color: #071b34;

}


.page-header p {

    color: #718096;

    font-size: 16px;

}


/* =========================================================
   VEHICLE SECTION
========================================================= */

.vehicle-section {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 40px;

    background: #ffffff;

    padding: 30px;

    border-radius: 22px;

    margin-bottom: 35px;

    box-shadow:
        0 10px 35px rgba(0,0,0,0.05);

}


.vehicle-image {

    position: relative;

    min-height: 320px;

    border-radius: 18px;

    overflow: hidden;

    background: #eef1f4;

    display: flex;

    align-items: center;

    justify-content: center;

}


.vehicle-image img {

    width: 100%;

    height: 100%;

    min-height: 320px;

    object-fit: cover;

    display: block;

}


.vehicle-placeholder {

    font-size: 70px;

    color: #c9a44a;

}


.vehicle-badge {

    position: absolute;

    top: 18px;

    left: 18px;

    background: #ffffff;

    color: #071b34;

    padding: 9px 15px;

    border-radius: 30px;

    font-size: 12px;

    font-weight: 600;

    box-shadow:
        0 5px 15px rgba(0,0,0,0.12);

}


.vehicle-badge i {

    color: #d19b32;

    margin-right: 5px;

}


.vehicle-content {

    display: flex;

    flex-direction: column;

    justify-content: center;

}


.vehicle-content h2 {

    font-size: 36px;

    margin: 8px 0 12px;

}


.rating {

    display: flex;

    align-items: center;

    gap: 5px;

    margin-bottom: 18px;

}


.rating i {

    color: #d19b32;

    font-size: 14px;

}


.rating span {

    color: #718096;

    margin-left: 8px;

    font-size: 14px;

}


.vehicle-description {

    color: #718096;

    margin-bottom: 22px;

    font-size: 15px;

}


.vehicle-price {

    font-size: 30px;

    font-weight: 700;

    color: #071b34;

    margin-bottom: 22px;

}


.vehicle-price span {

    font-size: 14px;

    font-weight: 400;

    color: #718096;

}


.vehicle-features {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 12px;

}


.vehicle-features div {

    background: #f7f8fa;

    padding: 12px;

    border-radius: 10px;

    display: flex;

    align-items: center;

    gap: 9px;

    font-size: 13px;

}


.vehicle-features i {

    color: #d19b32;

}


/* =========================================================
   FORM CARD
========================================================= */

.form-card {

    background: #ffffff;

    border-radius: 20px;

    padding: 32px;

    margin-bottom: 25px;

    box-shadow:
        0 8px 30px rgba(0,0,0,0.04);

}


.section-title {

    display: flex;

    align-items: center;

    gap: 15px;

    margin-bottom: 28px;

}


.section-icon {

    width: 58px;

    height: 58px;

    border-radius: 16px;

    background: #fff2c9;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #c9942d;

    font-size: 22px;

}


.section-title h3 {

    font-size: 21px;

    margin-bottom: 3px;

}


.section-title p {

    color: #718096;

    font-size: 13px;

}


/* =========================================================
   FORM GRID
========================================================= */

.form-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 22px 28px;

}


.form-group {

    display: flex;

    flex-direction: column;

    gap: 8px;

}


.form-group.full {

    grid-column: 1 / -1;

}


.form-group label {

    font-size: 14px;

    font-weight: 600;

    color: #071b34;

}


.input-wrapper {

    position: relative;

}


.input-wrapper i {

    position: absolute;

    left: 16px;

    top: 50%;

    transform: translateY(-50%);

    color: #c9942d;

    pointer-events: none;

}


.input-wrapper input,
.input-wrapper select {

    width: 100%;

    height: 52px;

    border: 1px solid #d9dde3;

    border-radius: 12px;

    padding: 0 16px 0 45px;

    font-family: inherit;

    font-size: 14px;

    outline: none;

    background: #ffffff;

    color: #071b34;

    transition: 0.3s;

}


.input-wrapper input:focus,
.input-wrapper select:focus {

    border-color: #d19b32;

    box-shadow:
        0 0 0 3px rgba(209,155,50,0.12);

}


input[type="date"] {

    color: #071b34;

}


/* =========================================================
   DISTRICT SELECT
========================================================= */

.district-wrapper {

    position: relative;

}


.district-wrapper i {

    position: absolute;

    left: 16px;

    top: 50%;

    transform: translateY(-50%);

    color: #c9942d;

    z-index: 2;

    pointer-events: none;

}


.district-wrapper select {

    width: 100%;

    height: 52px;

    border: 1px solid #d9dde3;

    border-radius: 12px;

    padding: 0 40px 0 45px;

    font-family: 'Poppins', sans-serif;

    font-size: 14px;

    background: #ffffff;

    color: #071b34;

    outline: none;

    cursor: pointer;

}


.district-wrapper select:focus {

    border-color: #d19b32;

    box-shadow:
        0 0 0 3px rgba(209,155,50,0.12);

}


/* =========================================================
   DOCUMENTS
========================================================= */

.document-info {

    display: flex;

    gap: 14px;

    background: #f8f5ec;

    border: 1px solid #eee4c8;

    padding: 16px;

    border-radius: 12px;

    margin-bottom: 25px;

}


.info-icon {

    color: #c9942d;

    font-size: 20px;

}


.document-info h4 {

    font-size: 14px;

    margin-bottom: 3px;

}


.document-info p {

    font-size: 12px;

    color: #718096;

}


.document-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 22px;

}


.document-card {

    border: 1px solid #e1e5ea;

    border-radius: 16px;

    padding: 22px;

}


.document-icon {

    width: 45px;

    height: 45px;

    border-radius: 12px;

    background: #fff2c9;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #c9942d;

    margin-bottom: 12px;

}


.document-card h4 {

    font-size: 16px;

    margin-bottom: 5px;

}


.document-card p {

    font-size: 12px;

    color: #718096;

    margin-bottom: 15px;

}


.upload-box {

    border: 2px dashed #d8dce2;

    border-radius: 12px;

    padding: 20px;

    display: flex;

    flex-direction: column;

    align-items: center;

    text-align: center;

    cursor: pointer;

    transition: 0.3s;

}


.upload-box:hover {

    border-color: #d19b32;

    background: #fffaf0;

}


.upload-box i {

    font-size: 25px;

    color: #d19b32;

    margin-bottom: 8px;

}


.upload-box strong {

    font-size: 13px;

}


.upload-box span {

    color: #718096;

    font-size: 10px;

    margin-top: 4px;

}


.upload-box input {

    display: none;

}


.file-name {

    font-size: 11px;

    color: #718096;

    margin-top: 10px;

}


.file-name i {

    color: #35a46a;

    margin-right: 5px;

}


.file-name.selected {

    color: #35a46a;

}


.document-security {

    display: flex;

    align-items: center;

    gap: 8px;

    margin-top: 20px;

    color: #718096;

    font-size: 12px;

}


.document-security i {

    color: #d19b32;

}


/* =========================================================
   BOOKING SUMMARY
========================================================= */

.summary-row {

    display: flex;

    justify-content: space-between;

    padding: 12px 0;

    border-bottom: 1px solid #edf0f3;

    font-size: 14px;

}


.summary-row span {

    color: #718096;

}


.summary-row strong {

    color: #071b34;

}


/* =========================================================
   OFFER ROWS
========================================================= */

.offer-row {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;

    padding: 13px 0;

    border-bottom: 1px solid #edf0f3;

}


.offer-row .offer-label {

    color: #718096;

    font-size: 14px;

}


.offer-row .offer-value {

    color: #071b34;

    font-size: 14px;

    font-weight: 600;

    text-align: right;

}


.offer-row .offer-discount {

    color: #1a9b63;

    font-size: 15px;

    font-weight: 700;

}


.offer-row.active-offer {

    margin-top: 5px;

    padding: 13px 14px;

    background: #f0faf5;

    border: 1px solid #cfeee0;

    border-radius: 10px;

}


.offer-badge {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 5px 9px;

    border-radius: 14px;

    background: #e3f7ed;

    color: #188355;

    font-size: 10px;

    font-weight: 700;

}


/* =========================================================
   TOTAL
========================================================= */

.total-row {

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding-top: 20px;

}


.total-row span {

    font-size: 18px;

    font-weight: 600;

}


.total-row strong {

    font-size: 27px;

    color: #d19b32;

}


/* =========================================================
   BUTTON
========================================================= */

.confirm-btn {

    width: 100%;

    height: 58px;

    border: none;

    border-radius: 13px;

    background: #071b34;

    color: #ffffff;

    font-family: inherit;

    font-size: 16px;

    font-weight: 600;

    cursor: pointer;

    transition: 0.3s;

}


.confirm-btn:hover {

    background: #d19b32;

    transform: translateY(-2px);

}


.bottom-note {

    text-align: center;

    margin-top: 14px;

    color: #718096;

    font-size: 12px;

}


.bottom-note i {

    color: #d19b32;

    margin-right: 5px;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 900px) {

    .nav-links {

        gap: 12px;

    }


    .vehicle-section {

        grid-template-columns: 1fr;

    }


    .form-grid {

        grid-template-columns: 1fr;

    }


    .form-group.full {

        grid-column: auto;

    }


    .document-grid {

        grid-template-columns: 1fr;

    }

}


@media (max-width: 650px) {

    .navbar {

        padding: 0 20px;

    }


    .nav-links {

        display: none;

    }


    .container {

        width: 94%;

        padding-top: 30px;

    }


    .page-header h1 {

        font-size: 30px;

    }


    .form-card {

        padding: 20px;

    }


    .vehicle-section {

        padding: 18px;

    }


    .offer-row {

        flex-direction: column;

        align-items: flex-start;

    }


    .offer-row .offer-value {

        text-align: left;

    }


    .total-row {

        gap: 10px;

    }

}

</style>

</head>


<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar">


    <a href="home.php" class="logo">

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
            <a href="notifications.php">
                Notifications
            </a>
        </li>


        <li>
            <a href="offers.php">
                Offers
            </a>
        </li>


        <li>
            <a href="profile.php">
                Profile
            </a>
        </li>


        <li>
            <a href="logout.php">
                Logout
            </a>
        </li>


    </ul>

</nav>


<!-- =========================================================
     MAIN
========================================================= -->

<main class="container">


    <!-- PAGE HEADER -->

    <div class="page-header">


        <span class="small-title">
            RESERVE YOUR VEHICLE
        </span>


        <h1>
            Booking Details
        </h1>


        <p>
            Tell us about your rental and upload the required
            documents for verification.
        </p>


    </div>


    <!-- =====================================================
         VEHICLE
    ====================================================== -->

    <section class="vehicle-section">


        <div class="vehicle-image">


            <?php if (!empty($vehicleImagePath)): ?>


                <img
                    src="<?php echo e($vehicleImagePath); ?>"
                    alt="<?php echo e($vehicleName); ?>"
                    onerror="this.style.display='none'; document.getElementById('vehiclePlaceholder').style.display='flex';"
                >


                <div
                    class="vehicle-placeholder"
                    id="vehiclePlaceholder"
                    style="display:none;"
                >

                    <i class="fa-solid fa-car"></i>

                </div>


            <?php else: ?>


                <div class="vehicle-placeholder">

                    <i class="fa-solid fa-car"></i>

                </div>


            <?php endif; ?>


            <span class="vehicle-badge">

                <i class="fa-solid fa-star"></i>

                Premium Vehicle

            </span>


        </div>


        <div class="vehicle-content">


            <span class="small-title">
                VELORA DRIVE
            </span>


            <h2>

                <?php echo e($vehicleName); ?>

            </h2>


            <div class="rating">


                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>


                <span>
                    Premium Ride
                </span>


            </div>


            <p class="vehicle-description">

                Experience a comfortable and reliable journey
                with Velora Drive. Enjoy premium vehicles,
                smooth driving and dependable rental service.

            </p>


            <div class="vehicle-price">

                ₹<?php echo number_format(
                    $pricePerDay,
                    2
                ); ?>


                <span>
                    / day
                </span>


            </div>


            <div class="vehicle-features">


                <div>

                    <i class="fa-solid fa-user-group"></i>

                    <span>
                        5 Seats
                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-snowflake"></i>

                    <span>
                        AC
                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-gas-pump"></i>

                    <span>
                        Fuel Efficient
                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-shield-halved"></i>

                    <span>
                        Insured
                    </span>

                </div>


            </div>


        </div>


    </section>


    <!-- =====================================================
         BOOKING FORM
    ====================================================== -->

    <form
        action="payment.php"
        method="POST"
        enctype="multipart/form-data"
        id="bookingForm"
    >


        <!-- =================================================
             HIDDEN VEHICLE DATA
        ================================================== -->

        <input
            type="hidden"
            name="vehicle_id"
            value="<?php echo e(
                $vehicle['id']
            ); ?>"
        >


        <input
            type="hidden"
            name="vehicle_name"
            value="<?php echo e(
                $vehicleName
            ); ?>"
        >


        <input
            type="hidden"
            name="vehicle_image"
            value="<?php echo e(
                $vehicleImagePath
            ); ?>"
        >


        <input
            type="hidden"
            name="price"
            id="price"
            value="<?php echo e(
                $pricePerDay
            ); ?>"
        >


        <!-- =================================================
             OFFER DATA
        ================================================== -->


        <input
            type="hidden"
            name="offer_name"
            id="offer_name"
            value=""
        >


        <input
            type="hidden"
            name="discount_percent"
            id="discount_percent"
            value="0"
        >


        <input
            type="hidden"
            name="discount_amount"
            id="discount_amount"
            value="0"
        >


        <input
            type="hidden"
            name="base_amount"
            id="base_amount"
            value=""
        >


        <!-- =================================================
             PERSONAL INFORMATION
        ================================================== -->

        <div class="form-card">


            <div class="section-title">


                <div class="section-icon">

                    <i class="fa-solid fa-user"></i>

                </div>


                <div>

                    <h3>
                        Personal Information
                    </h3>


                    <p>
                        Your contact details
                    </p>


                </div>


            </div>


            <div class="form-grid">


                <!-- FULL NAME -->

                <div class="form-group">


                    <label>
                        Full Name
                    </label>


                    <div class="input-wrapper">


                        <i class="fa-solid fa-user"></i>


                        <input
                            type="text"
                            name="fullname"
                            value="<?php echo e(
                                $_SESSION['fullname']
                            ); ?>"
                            readonly
                        >


                    </div>


                </div>


                <!-- EMAIL -->

                <div class="form-group">


                    <label>
                        Email Address
                    </label>


                    <div class="input-wrapper">


                        <i class="fa-solid fa-envelope"></i>


                        <input
                            type="email"
                            name="email"
                            value="<?php echo e(
                                $_SESSION['email'] ?? ''
                            ); ?>"
                            placeholder="Enter your email"
                            required
                        >


                    </div>


                </div>


                <!-- PHONE -->

                <div class="form-group full">


                    <label>
                        Phone Number
                    </label>


                    <div class="input-wrapper">


                        <i class="fa-solid fa-phone"></i>


                        <input
                            type="tel"
                            name="phone"
                            placeholder="Enter 10-digit phone number"
                            pattern="[0-9]{10}"
                            maxlength="10"
                            required
                        >


                    </div>


                </div>


            </div>


        </div>


        <!-- =================================================
             RENTAL DETAILS
        ================================================== -->

        <div class="form-card">


            <div class="section-title">


                <div class="section-icon">

                    <i class="fa-solid fa-calendar-days"></i>

                </div>


                <div>

                    <h3>
                        Rental Details
                    </h3>


                    <p>
                        Choose your rental dates and locations
                    </p>


                </div>


            </div>


            <div class="form-grid">


                <!-- PICKUP DATE -->

                <div class="form-group">


                    <label for="booking_date">

                        Pickup Date

                    </label>


                    <div class="input-wrapper">


                        <i class="fa-solid fa-calendar"></i>


                        <input
                            type="date"
                            id="booking_date"
                            name="booking_date"
                            required
                        >


                    </div>


                </div>


                <!-- RETURN DATE -->

                <div class="form-group">


                    <label for="return_date">

                        Return Date

                    </label>


                    <div class="input-wrapper">


                        <i class="fa-solid fa-calendar-check"></i>


                        <input
                            type="date"
                            id="return_date"
                            name="return_date"
                            required
                        >


                    </div>


                </div>


                <!-- PICKUP DISTRICT -->

                <div class="form-group">


                    <label for="pickup_location">

                        Pickup District

                    </label>


                    <div class="district-wrapper">


                        <i class="fa-solid fa-location-dot"></i>


                        <select
                            name="pickup_location"
                            id="pickup_location"
                            required
                        >


                            <option value="">
                                Select pickup district
                            </option>


                            <option>
                                Alappuzha
                            </option>


                            <option>
                                Ernakulam
                            </option>


                            <option>
                                Idukki
                            </option>


                            <option>
                                Kannur
                            </option>


                            <option>
                                Kasaragod
                            </option>


                            <option>
                                Kollam
                            </option>


                            <option>
                                Kottayam
                            </option>


                            <option>
                                Kozhikode
                            </option>


                            <option>
                                Malappuram
                            </option>


                            <option>
                                Palakkad
                            </option>


                            <option>
                                Pathanamthitta
                            </option>


                            <option>
                                Thiruvananthapuram
                            </option>


                            <option>
                                Thrissur
                            </option>


                            <option>
                                Wayanad
                            </option>


                        </select>


                    </div>


                </div>


                <!-- RETURN DISTRICT -->

                <div class="form-group">


                    <label for="return_location">

                        Return District

                    </label>


                    <div class="district-wrapper">


                        <i class="fa-solid fa-location-dot"></i>


                        <select
                            name="return_location"
                            id="return_location"
                            required
                        >


                            <option value="">
                                Select return district
                            </option>


                            <option>
                                Alappuzha
                            </option>


                            <option>
                                Ernakulam
                            </option>


                            <option>
                                Idukki
                            </option>


                            <option>
                                Kannur
                            </option>


                            <option>
                                Kasaragod
                            </option>


                            <option>
                                Kollam
                            </option>


                            <option>
                                Kottayam
                            </option>


                            <option>
                                Kozhikode
                            </option>


                            <option>
                                Malappuram
                            </option>


                            <option>
                                Palakkad
                            </option>


                            <option>
                                Pathanamthitta
                            </option>


                            <option>
                                Thiruvananthapuram
                            </option>


                            <option>
                                Thrissur
                            </option>


                            <option>
                                Wayanad
                            </option>


                        </select>


                    </div>


                </div>


            </div>


        </div>


        <!-- =================================================
             DOCUMENTS
        ================================================== -->

        <div class="form-card">


            <div class="section-title">


                <div class="section-icon">

                    <i class="fa-solid fa-file-shield"></i>

                </div>


                <div>


                    <h3>
                        Documents Required
                    </h3>


                    <p>
                        Upload the documents required for verification
                    </p>


                </div>


            </div>


            <div class="document-info">


                <div class="info-icon">

                    <i class="fa-solid fa-circle-info"></i>

                </div>


                <div>


                    <h4>
                        Please keep your documents ready
                    </h4>


                    <p>
                        A valid Driving License and Government ID
                        proof are required for vehicle rental verification.
                    </p>


                </div>


            </div>


            <div class="document-grid">


                <!-- DRIVING LICENSE -->

                <div class="document-card">


                    <div class="document-icon">

                        <i class="fa-solid fa-id-card"></i>

                    </div>


                    <h4>
                        Driving License
                    </h4>


                    <p>
                        Upload a clear copy of your valid
                        driving license.
                    </p>


                    <label class="upload-box">


                        <i class="fa-solid fa-cloud-arrow-up"></i>


                        <strong>
                            Click to upload
                        </strong>


                        <span>
                            JPG, PNG or PDF • Max 5MB
                        </span>


                        <input
                            type="file"
                            name="driving_license"
                            accept=".jpg,.jpeg,.png,.pdf"
                            required
                            onchange="showFileName(
                                this,
                                'licenseName'
                            )"
                        >


                    </label>


                    <div
                        class="file-name"
                        id="licenseName"
                    >


                        <i class="fa-solid fa-circle-check"></i>

                        No file selected


                    </div>


                </div>


                <!-- GOVERNMENT ID -->

                <div class="document-card">


                    <div class="document-icon">

                        <i class="fa-solid fa-address-card"></i>

                    </div>


                    <h4>
                        Government ID Proof
                    </h4>


                    <p>
                        Upload Aadhaar, Passport, Voter ID or
                        another valid government ID.
                    </p>


                    <label class="upload-box">


                        <i class="fa-solid fa-cloud-arrow-up"></i>


                        <strong>
                            Click to upload
                        </strong>


                        <span>
                            JPG, PNG or PDF • Max 5MB
                        </span>


                        <input
                            type="file"
                            name="government_id"
                            accept=".jpg,.jpeg,.png,.pdf"
                            required
                            onchange="showFileName(
                                this,
                                'idName'
                            )"
                        >


                    </label>


                    <div
                        class="file-name"
                        id="idName"
                    >


                        <i class="fa-solid fa-circle-check"></i>

                        No file selected


                    </div>


                </div>


            </div>


            <div class="document-security">


                <i class="fa-solid fa-lock"></i>


                <span>

                    Your documents are securely handled and used only
                    for booking verification.

                </span>


            </div>


        </div>


        <!-- =================================================
             BOOKING SUMMARY
        ================================================== -->

        <div class="form-card">


            <div class="section-title">


                <div class="section-icon">

                    <i class="fa-solid fa-receipt"></i>

                </div>


                <div>


                    <h3>
                        Booking Summary
                    </h3>


                    <p>
                        Review your rental before continuing
                    </p>


                </div>


            </div>


            <!-- VEHICLE -->

            <div class="summary-row">


                <span>
                    Vehicle
                </span>


                <strong>
                    <?php echo e(
                        $vehicleName
                    ); ?>
                </strong>


            </div>


            <!-- PRICE -->

            <div class="summary-row">


                <span>
                    Price / Day
                </span>


                <strong>
                    ₹<?php echo number_format(
                        $pricePerDay,
                        2
                    ); ?>
                </strong>


            </div>


            <!-- RENTAL DAYS -->

            <div class="summary-row">


                <span>
                    Rental Days
                </span>


                <strong id="rentalDays">
                    0
                </strong>


            </div>


            <!-- BASE AMOUNT -->

            <div class="summary-row">


                <span>
                    Base Rental Amount
                </span>


                <strong>
                    ₹<span id="baseAmountDisplay">
                        0.00
                    </span>
                </strong>


            </div>


            <!-- APPLIED OFFER -->

            <div
                class="offer-row active-offer"
                id="offerRow"
                style="display:none;"
            >


                <div>

                    <div class="offer-label">

                        Applied Offer

                    </div>


                    <span class="offer-badge">

                        <i class="fa-solid fa-tag"></i>

                        <span id="offerBadgeText">
                            Offer
                        </span>

                    </span>


                </div>


                <div
                    class="offer-value offer-discount"
                    id="offerDiscountDisplay"
                >

                    -₹0.00

                </div>


            </div>


            <!-- NO OFFER -->

            <div
                class="summary-row"
                id="noOfferRow"
            >

                <span>
                    Offer
                </span>


                <strong>
                    No offer applied
                </strong>


            </div>


            <!-- DISCOUNT -->

            <div
                class="summary-row"
                id="discountRow"
                style="display:none;"
            >


                <span>
                    Discount
                </span>


                <strong
                    style="color:#1a9b63;"
                >

                    -₹<span id="discountAmountDisplay">
                        0.00
                    </span>

                </strong>


            </div>


            <!-- FINAL TOTAL -->

            <div class="total-row">


                <span>
                    Final Amount
                </span>


                <strong>

                    ₹<span id="displayAmount">
                        0.00
                    </span>

                </strong>


            </div>


            <!-- FINAL TOTAL SENT TO PAYMENT -->

            <input
                type="hidden"
                name="total_amount"
                id="total_amount"
                value=""
            >


        </div>


        <!-- =================================================
             BUTTON
        ================================================== -->

        <button
            type="submit"
            class="confirm-btn"
        >


            <i class="fa-solid fa-arrow-right"></i>


            Continue to Payment


        </button>


        <p class="bottom-note">


            <i class="fa-solid fa-lock"></i>


            Your booking information is protected and securely processed.


        </p>


    </form>


</main>


<script>

/* =========================================================
   ELEMENTS
========================================================= */

const bookingDate =
    document.getElementById(
        "booking_date"
    );


const returnDate =
    document.getElementById(
        "return_date"
    );


const rentalDays =
    document.getElementById(
        "rentalDays"
    );


const baseAmountDisplay =
    document.getElementById(
        "baseAmountDisplay"
    );


const displayAmount =
    document.getElementById(
        "displayAmount"
    );


const totalAmount =
    document.getElementById(
        "total_amount"
    );


const price =
    parseFloat(
        document.getElementById(
            "price"
        ).value
    ) || 0;


/* OFFER ELEMENTS */

const offerRow =
    document.getElementById(
        "offerRow"
    );


const noOfferRow =
    document.getElementById(
        "noOfferRow"
    );


const discountRow =
    document.getElementById(
        "discountRow"
    );


const offerBadgeText =
    document.getElementById(
        "offerBadgeText"
    );


const offerDiscountDisplay =
    document.getElementById(
        "offerDiscountDisplay"
    );


const discountAmountDisplay =
    document.getElementById(
        "discountAmountDisplay"
    );


const offerNameInput =
    document.getElementById(
        "offer_name"
    );


const discountPercentInput =
    document.getElementById(
        "discount_percent"
    );


const discountAmountInput =
    document.getElementById(
        "discount_amount"
    );


const baseAmountInput =
    document.getElementById(
        "base_amount"
    );


/* =========================================================
   PREMIUM RULE
========================================================= */

const isPremiumVehicle =
    <?php echo $isPremiumVehicle
        ? 'true'
        : 'false'; ?>;


/* =========================================================
   TODAY
========================================================= */

const today =
    new Date()
        .toISOString()
        .split("T")[0];


bookingDate.min =
    today;


returnDate.min =
    today;


/* =========================================================
   RETURN DATE MINIMUM
========================================================= */

bookingDate.addEventListener(
    "change",
    function() {


        if (bookingDate.value) {


            returnDate.min =
                bookingDate.value;


            if (
                returnDate.value &&
                returnDate.value <
                bookingDate.value
            ) {

                returnDate.value = "";

            }

        }


        calculateTotal();

    }
);


/* =========================================================
   CHECK WEEKEND
========================================================= */

function rentalIncludesWeekend(
    startDate,
    endDate
) {


    let current =
        new Date(startDate);


    const end =
        new Date(endDate);


    while (
        current <= end
    ) {


        const day =
            current.getDay();


        /*
           JavaScript:
           0 = Sunday
           6 = Saturday
        */


        if (
            day === 0 ||
            day === 6
        ) {

            return true;

        }


        current.setDate(
            current.getDate() + 1
        );

    }


    return false;

}


/* =========================================================
   FIND BEST OFFER
========================================================= */

function getBestOffer(
    startDate,
    endDate,
    days
) {


    const eligibleOffers = [];


    /* =====================================
       WEEKEND DRIVE
    ====================================== */

    if (
        rentalIncludesWeekend(
            startDate,
            endDate
        )
    ) {


        eligibleOffers.push({

            name: "Weekend Drive",

            percent: 20

        });

    }


    /* =====================================
       LONG RENTAL
    ====================================== */

    if (
        days >= 5
    ) {


        eligibleOffers.push({

            name: "Long Rental",

            percent: 15

        });

    }


    /* =====================================
       PREMIUM WEEKEND
    ====================================== */

    if (
        isPremiumVehicle &&
        rentalIncludesWeekend(
            startDate,
            endDate
        )
    ) {


        eligibleOffers.push({

            name: "Premium Weekend",

            percent: 10

        });

    }


    /* =====================================
       NO OFFERS
    ====================================== */

    if (
        eligibleOffers.length === 0
    ) {

        return null;

    }


    /* =====================================
       HIGHEST DISCOUNT ONLY
    ====================================== */

    eligibleOffers.sort(
        function(a, b) {

            return b.percent -
                   a.percent;

        }
    );


    return eligibleOffers[0];

}


/* =========================================================
   CALCULATE TOTAL
========================================================= */

function calculateTotal() {


    /* =====================================
       CHECK DATES
    ====================================== */

    if (
        !bookingDate.value ||
        !returnDate.value
    ) {


        rentalDays.textContent =
            "0";


        baseAmountDisplay.textContent =
            "0.00";


        displayAmount.textContent =
            "0.00";


        totalAmount.value =
            "";


        baseAmountInput.value =
            "";


        discountAmountInput.value =
            "0";


        discountPercentInput.value =
            "0";


        offerNameInput.value =
            "";


        offerRow.style.display =
            "none";


        discountRow.style.display =
            "none";


        noOfferRow.style.display =
            "flex";


        return;

    }


    /* =====================================
       DATE OBJECTS
    ====================================== */

    const start =
        new Date(
            bookingDate.value
        );


    const end =
        new Date(
            returnDate.value
        );


    /* =====================================
       DATE DIFFERENCE
    ====================================== */

    const difference =
        end.getTime() -
        start.getTime();


    let days =
        Math.ceil(
            difference /
            (
                1000 *
                60 *
                60 *
                24
            )
        );


    /* =====================================
       INVALID DATE
    ====================================== */

    if (
        days < 1
    ) {


        rentalDays.textContent =
            "0";


        baseAmountDisplay.textContent =
            "0.00";


        displayAmount.textContent =
            "0.00";


        totalAmount.value =
            "";


        return;

    }


    /* =====================================
       BASE AMOUNT
    ====================================== */

    const baseAmount =
        days * price;


    rentalDays.textContent =
        days;


    baseAmountDisplay.textContent =
        baseAmount.toFixed(2);


    baseAmountInput.value =
        baseAmount.toFixed(2);


    /* =====================================
       FIND OFFER
    ====================================== */

    const bestOffer =
        getBestOffer(
            bookingDate.value,
            returnDate.value,
            days
        );


    let discountPercent =
        0;


    let discountAmount =
        0;


    let finalAmount =
        baseAmount;


    /* =====================================
       APPLY OFFER
    ====================================== */

    if (
        bestOffer
    ) {


        discountPercent =
            bestOffer.percent;


        discountAmount =
            baseAmount *
            (
                discountPercent /
                100
            );


        finalAmount =
            baseAmount -
            discountAmount;


        /* DISPLAY */

        offerRow.style.display =
            "flex";


        noOfferRow.style.display =
            "none";


        discountRow.style.display =
            "flex";


        offerBadgeText.textContent =
            bestOffer.name +
            " • " +
            bestOffer.percent +
            "% OFF";


        offerDiscountDisplay.textContent =
            "-₹" +
            discountAmount.toFixed(2);


        discountAmountDisplay.textContent =
            discountAmount.toFixed(2);


        /* HIDDEN DATA */

        offerNameInput.value =
            bestOffer.name;


        discountPercentInput.value =
            discountPercent;


        discountAmountInput.value =
            discountAmount.toFixed(2);

    }

    else {


        /* NO OFFER */

        offerRow.style.display =
            "none";


        discountRow.style.display =
            "none";


        noOfferRow.style.display =
            "flex";


        offerNameInput.value =
            "";


        discountPercentInput.value =
            "0";


        discountAmountInput.value =
            "0";


        offerBadgeText.textContent =
            "No Offer";


        offerDiscountDisplay.textContent =
            "-₹0.00";


        discountAmountDisplay.textContent =
            "0.00";

    }


    /* =====================================
       FINAL AMOUNT
    ====================================== */

    displayAmount.textContent =
        finalAmount.toFixed(2);


    /*
       IMPORTANT:

       This is the amount sent to
       payment.php.

       It is now the DISCOUNTED amount.
    */

    totalAmount.value =
        finalAmount.toFixed(2);

}


/* =========================================================
   RETURN DATE CHANGE
========================================================= */

returnDate.addEventListener(
    "change",
    calculateTotal
);


/* =========================================================
   FILE NAME DISPLAY
========================================================= */

function showFileName(
    input,
    elementId
) {


    const element =
        document.getElementById(
            elementId
        );


    if (
        input.files &&
        input.files.length > 0
    ) {


        element.innerHTML =
            '<i class="fa-solid fa-circle-check"></i> ' +
            input.files[0].name;


        element.classList.add(
            "selected"
        );

    }

    else {


        element.innerHTML =
            '<i class="fa-solid fa-circle-check"></i> ' +
            'No file selected';


        element.classList.remove(
            "selected"
        );

    }

}


/* =========================================================
   FORM VALIDATION
========================================================= */

document
    .getElementById("bookingForm")
    .addEventListener(
        "submit",
        function(event) {


            /* Recalculate */

            calculateTotal();


            /* =================================
               CHECK TOTAL
            ================================= */

            if (
                !totalAmount.value ||
                parseFloat(
                    totalAmount.value
                ) <= 0
            ) {


                event.preventDefault();


                alert(
                    "Please select valid pickup and return dates."
                );


                return;

            }


            /* =================================
               CHECK RETURN DATE
            ================================= */

            if (
                bookingDate.value &&
                returnDate.value &&
                returnDate.value <=
                bookingDate.value
            ) {


                event.preventDefault();


                alert(
                    "Return date must be after the pickup date."
                );


                return;

            }


        }
    );

</script>


</body>

</html>