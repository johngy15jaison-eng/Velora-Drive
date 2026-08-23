<?php

session_start();

include("config.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $fullname = mysqli_real_escape_string($conn,$_POST['fullname']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);

    $vehicle = mysqli_real_escape_string($conn,$_POST['vehicle']);
    $image = mysqli_real_escape_string($conn,$_POST['image']);

    $price = $_POST['price'];

    $pickup_location = mysqli_real_escape_string($conn,$_POST['pickup_location']);
    $return_location = mysqli_real_escape_string($conn,$_POST['return_location']);

    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];

    $total = $_POST['total'];

    $payment_method = mysqli_real_escape_string($conn,$_POST['payment_method']);

    $payment_status = "Paid";

    $booking_status = "Confirmed";

    $sql = "INSERT INTO bookings
    (
        fullname,
        email,
        phone,
        vehicle_name,
        vehicle_image,
        price,
        pickup_location,
        return_location,
        booking_date,
        return_date,
        payment_method,
        payment_status,
        booking_status,
        total_amount
    )

    VALUES

    (
        '$fullname',
        '$email',
        '$phone',
        '$vehicle',
        '$image',
        '$price',
        '$pickup_location',
        '$return_location',
        '$pickup_date',
        '$return_date',
        '$payment_method',
        '$payment_status',
        '$booking_status',
        '$total'
    )";

    if(mysqli_query($conn,$sql)){

        $_SESSION['booking_id']=mysqli_insert_id($conn);

        header("Location: booking_success.php");

        exit();

    }else{

    die("Database Error: " . mysqli_error($conn));

}

}

?>