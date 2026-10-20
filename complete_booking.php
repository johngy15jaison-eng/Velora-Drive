
<?php
// IMPORTANT: This file must start with <?php as the very first bytes.
// Save as UTF-8 without BOM. Do not add blank lines or spaces before <?php.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';

function e($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Only accept booking submissions via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: booking.php");
    exit;
}

// Customer details
$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// Vehicle details
$vehicle_id = trim($_POST['vehicle_id'] ?? '');
$vehicle_name = trim($_POST['vehicle_name'] ?? '');
$vehicle_image = trim($_POST['vehicle_image'] ?? '');
$price = (float)($_POST['price'] ?? 0);

// Locations and dates
$pickup_location = trim($_POST['pickup_location'] ?? '');
$return_location = trim($_POST['return_location'] ?? '');
$booking_date = trim($_POST['booking_date'] ?? '');
$return_date = trim($_POST['return_date'] ?? '');

// Payment details
$total_amount = (float)($_POST['total_amount'] ?? 0);
$payment_method = trim($_POST['payment_method'] ?? 'Cash');

// Optional uploaded document details
$license_file = trim($_POST['license_file'] ?? '');
$license_path = trim($_POST['license_path'] ?? '');
$government_id_file = trim($_POST['government_id_file'] ?? '');
$government_id_path = trim($_POST['government_id_path'] ?? '');

// Validate required booking information.
$missing = [];

if ($fullname === '') {
    $missing[] = 'fullname';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $missing[] = 'valid email';
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

if (!empty($missing)) {
    http_response_code(400);
    echo "<h2>Booking information is missing or invalid</h2>";
    echo "<p>Please return to the payment page and check the following fields:</p>";
    echo "<ul>";

    foreach ($missing as $field) {
        echo "<li>" . e($field) . "</li>";
    }

    echo "</ul>";
    echo "<a href='javascript:history.back()'>Go back to payment</a>";
    exit;
}

// New bookings are marked confirmed and active in the simulated GPS demo.
// Existing bookings are not modified.
$payment_status = "Paid";
$booking_status = "Confirmed";
$trip_status = "Active";

$sql = "
    INSERT INTO bookings (
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
        total_amount,
        trip_status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    error_log("Booking SQL preparation failed: " . mysqli_error($conn));
    http_response_code(500);
    exit("Sorry, your booking could not be processed. Please try again later.");
}

// 15 parameters:
// 5 strings, 1 double, 7 strings, 1 double, 1 string.
$bind_result = mysqli_stmt_bind_param(
    $stmt,
    "sssssdsssssssds",
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
    $total_amount,
    $trip_status
);

if (!$bind_result) {
    error_log("Booking parameter binding failed: " . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    http_response_code(500);
    exit("Sorry, your booking could not be processed. Please try again later.");
}

if (!mysqli_stmt_execute($stmt)) {
    error_log("Booking insert failed: " . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    http_response_code(500);
    exit("Sorry, your booking could not be saved. Please try again later.");
}

// Save the newly created booking ID before redirecting.
$booking_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

$_SESSION['booking_id'] = $booking_id;
$_SESSION['booking_success'] = true;
$_SESSION['booking_vehicle'] = $vehicle_name;
$_SESSION['booking_total'] = $total_amount;

// Redirect without producing output first.
header("Location: booking_success.php");
exit;
