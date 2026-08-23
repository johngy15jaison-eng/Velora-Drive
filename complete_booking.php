<?php

session_start();

require_once __DIR__ . '/includes/db.php';

/* =========================================
   HELPER FUNCTION
========================================= */

function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}


/* =========================================
   ONLY ALLOW POST
========================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: booking.php");

    exit;

}


/* =========================================
   RECEIVE DATA
========================================= */

$fullname =
    trim($_POST['fullname'] ?? '');

$email =
    trim($_POST['email'] ?? '');

$phone =
    trim($_POST['phone'] ?? '');


$vehicle_id =
    trim($_POST['vehicle_id'] ?? '');

$vehicle_name =
    trim($_POST['vehicle_name'] ?? '');

$vehicle_image =
    trim($_POST['vehicle_image'] ?? '');

$price =
    (float)($_POST['price'] ?? 0);


$pickup_location =
    trim($_POST['pickup_location'] ?? '');

$return_location =
    trim($_POST['return_location'] ?? '');


$booking_date =
    trim($_POST['booking_date'] ?? '');

$return_date =
    trim($_POST['return_date'] ?? '');


$total_amount =
    (float)($_POST['total_amount'] ?? 0);


$payment_method =
    trim($_POST['payment_method'] ?? 'Cash');


/* =========================================
   DOCUMENT DATA
========================================= */

$license_file =
    trim($_POST['license_file'] ?? '');

$license_path =
    trim($_POST['license_path'] ?? '');

$government_id_file =
    trim($_POST['government_id_file'] ?? '');

$government_id_path =
    trim($_POST['government_id_path'] ?? '');


/* =========================================
   CHECK REQUIRED DATA
========================================= */

$missing = [];


if ($fullname === '') {
    $missing[] = 'fullname';
}

if ($email === '') {
    $missing[] = 'email';
}

if ($phone === '') {
    $missing[] = 'phone';
}

if ($vehicle_name === '') {
    $missing[] = 'vehicle_name';
}

if ($pickup_location === '') {
    $missing[] = 'pickup_location';
}

if ($return_location === '') {
    $missing[] = 'return_location';
}

if ($booking_date === '') {
    $missing[] = 'booking_date';
}

if ($return_date === '') {
    $missing[] = 'return_date';
}

if ($total_amount <= 0) {
    $missing[] = 'total_amount';
}


/* =========================================
   ERROR IF DATA MISSING
========================================= */

if (!empty($missing)) {

    echo "<h2>Booking information is missing</h2>";

    echo "<p>The following fields were not received:</p>";

    echo "<ul>";

    foreach ($missing as $field) {

        echo "<li>" . e($field) . "</li>";

    }

    echo "</ul>";

    echo "<a href='javascript:history.back()'>Go back to payment</a>";

    exit;
}


/* =========================================
   PAYMENT STATUS
========================================= */

$payment_status = "Paid";

$booking_status = "Confirmed";


/* =========================================
   INSERT INTO BOOKINGS
========================================= */

$sql = "

INSERT INTO bookings

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
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?
)

";


$stmt = mysqli_prepare($conn, $sql);


if (!$stmt) {

    die(
        "Database preparation failed: "
        . mysqli_error($conn)
    );

}


/* =========================================
   BIND VALUES
========================================= */

mysqli_stmt_bind_param(

    $stmt,

    "sssssdsisssssd",

    $fullname,
    $email,
    $phone,
    $vehicle_name,
    $vehicle_image,
    $price,
    $pickup_location,
    $return_location,
    $booking_date,
    $return_date,
    $payment_method,
    $payment_status,
    $booking_status,
    $total_amount

);


/* =========================================
   EXECUTE
========================================= */

if (!mysqli_stmt_execute($stmt)) {

    die(

        "Booking could not be saved.<br><br>"

        . "Database Error: "

        . mysqli_stmt_error($stmt)

    );

}


/* =========================================
   GET NEW BOOKING ID
========================================= */

$booking_id =
    mysqli_insert_id($conn);


/* =========================================
   CLOSE STATEMENT
========================================= */

mysqli_stmt_close($stmt);


/* =========================================
   SAVE BOOKING ID IN SESSION
========================================= */

$_SESSION['booking_id'] =
    $booking_id;


/* =========================================
   SAVE BOOKING INFORMATION
========================================= */

$_SESSION['booking_success'] = true;

$_SESSION['booking_vehicle'] =
    $vehicle_name;

$_SESSION['booking_total'] =
    $total_amount;


/* =========================================
   REDIRECT
========================================= */

header(
    "Location: booking_success.php"
);

exit;

?>