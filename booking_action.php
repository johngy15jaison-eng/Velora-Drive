<?php

session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $vehicle = mysqli_real_escape_string($conn, $_POST['vehicle']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    // These names must match your booking.php form
    $booking_date = mysqli_real_escape_string($conn, $_POST['pickup_date']);
    $return_date = mysqli_real_escape_string($conn, $_POST['return_date']);

    $sql = "INSERT INTO bookings
            (fullname, email, vehicle_name, price, booking_date, return_date)
            VALUES
            ('$fullname',
             '$email',
             '$vehicle',
             '$price',
             '$booking_date',
             '$return_date')";

    if (mysqli_query($conn, $sql)) {

        $_SESSION['booking_success'] = "Booking Successful!";

        header("Location: booking_success.php");
        exit();

    } else {

        die("Database Error: " . mysqli_error($conn));

    }

} else {

    header("Location: booking.php");
    exit();

}

?>