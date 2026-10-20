<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$email = $_SESSION['email'];
$bookings = [];
$errorMessage = '';

$sql = "SELECT id, vehicle_name, pickup_location, return_location,
               booking_date, return_date, trip_status, booking_status
        FROM bookings
        WHERE email = ? AND booking_status = 'Confirmed'
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('GPS tracking: query preparation failed.');
    $errorMessage = 'Unable to load your bookings. Please try again.';
} else {
    $stmt->bind_param('s', $email);

    if (!$stmt->execute()) {
        error_log('GPS tracking: query execution failed.');
        $errorMessage = 'Unable to load your bookings. Please try again.';
    } else {
        $stmt->bind_result(
            $id,
            $vehicleName,
            $pickup,
            $destination,
            $bookingDate,
            $returnDate,
            $tripStatus,
            $bookingStatus
        );

        while ($stmt->fetch()) {
            $bookings[] = [
                'id' => $id,
                'booking_id' => $id,
                'bookingId' => $id,
                'vehicle_name' => $vehicleName,
                'vehicleName' => $vehicleName,
                'pickup_location' => $pickup,
                'pickupLocation' => $pickup,
                'return_location' => $destination,
                'returnLocation' => $destination,
                'destination' => $destination,
                'booking_date' => $bookingDate,
                'bookingDate' => $bookingDate,
                'return_date' => $returnDate,
                'returnDate' => $returnDate,
                'trip_status' => $tripStatus,
                'booking_status' => $bookingStatus
            ];
        }
    }

    $stmt->close();
}

$json = json_encode(
    $bookings,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT |
    JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE
);

if ($json === false) {
    $json = '[]';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Tracking | Velora Drive</title>

    <link rel="stylesheet" href="css/gps_tracking.css">
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body>

<div class="gps-page">
    <header class="gps-header">
        <a class="brand" href="index.php">Velora <span>Drive</span></a>
        <h1>Vehicle GPS Tracking</h1>
        <p>View your confirmed bookings and simulated trip progress.</p>
    </header>

    <div class="demo-notice">
        <strong>Demo mode:</strong> Vehicle locations and movement are
        simulated. This page does not use a real GPS device.
    </div>

    <?php if ($errorMessage !== ''): ?>
        <div class="error-message">
            <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (!$bookings && $errorMessage === ''): ?>
        <div class="empty-message">
            <h2>No confirmed bookings found</h2>
            <p>Your confirmed bookings will appear here.</p>
        </div>
    <?php else: ?>

        <div class="tracking-layout">
            <section class="map-panel">
                <h2>Vehicle Location Map</h2>
                <div id="map"></div>
                <p class="map-caption">
                    Map markers represent simulated locations, not live GPS data.
                </p>
            </section>

            <section class="details-panel">
                <h2>Tracking Details</h2>

                <div class="detail-card">
                    <p><strong>Total Bookings:</strong>
                        <span id="vehicleCount">0</span></p>
                    <p><strong>Vehicle:</strong>
                        <span id="selectedVehicle">Select a booking</span></p>
                    <p><strong>Booking ID:</strong>
                        <span id="selectedVehicleId">--</span></p>
                    <p><strong>Pickup:</strong>
                        <span id="vehiclePickup">--</span></p>
                    <p><strong>Destination:</strong>
                        <span id="vehicleDestination">--</span></p>
                    <p><strong>Booking Starts:</strong>
                        <span id="bookingStart">--</span></p>
                    <p><strong>Return Date:</strong>
                        <span id="bookingReturn">--</span></p>
                    <p><strong>Simulated Speed:</strong>
                        <span id="vehicleSpeed">--</span></p>
                    <p><strong>Latitude:</strong>
                        <span id="vehicleLatitude">--</span></p>
                    <p><strong>Longitude:</strong>
                        <span id="vehicleLongitude">--</span></p>
                    <p><strong>Trip Progress:</strong>
                        <span id="tripProgress">--</span></p>
                    <p><strong>Trip Status:</strong>
                        <span id="tripStatus">--</span></p>
                    <p><strong>Last Updated:</strong>
                        <span id="lastUpdate">--</span></p>
                </div>

                <h2 class="bookings-heading">Your Confirmed Bookings</h2>
                <div id="vehicleList" class="booking-list"></div>
            </section>
        </div>
    <?php endif; ?>
</div>

<script>
    window.gpsBookings = <?= $json ?>;
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/gps_tracking.js"></script>
</body>
</html>
