
<?php
// Do not put blank lines, spaces, or HTML before this opening PHP tag.

if (headers_sent($sentFile, $sentLine)) {
    error_log(
        "Output was sent before gps_tracking.php: " .
        $sentFile . " on line " . $sentLine
    );
    exit('A response was started before GPS tracking could load. Please contact support.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login before producing any page output.
if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

// Load the database only after session handling.
require_once __DIR__ . '/includes/db.php';

$userEmail = $_SESSION['email'];
$bookings = [];
$errorMessage = '';

$sql = "SELECT
            id,
            vehicle_name,
            pickup_location,
            return_location,
            booking_date,
            return_date,
            trip_status
        FROM bookings
        WHERE email = ?
          AND booking_status = 'Confirmed'
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log('GPS tracking: failed to prepare bookings query.');
    $errorMessage = 'Unable to load bookings right now.';
} else {
    $stmt->bind_param('s', $userEmail);

    if (!$stmt->execute()) {
        error_log('GPS tracking: failed to execute bookings query.');
        $errorMessage = 'Unable to load bookings right now.';
    } else {
        $stmt->bind_result(
            $bookingId,
            $vehicleName,
            $pickupLocation,
            $returnLocation,
            $bookingDate,
            $returnDate,
            $tripStatus
        );

        while ($stmt->fetch()) {
            $bookings[] = [
                'id' => $bookingId,
                'vehicle_name' => $vehicleName,
                'pickup_location' => $pickupLocation,
                'return_location' => $returnLocation,
                'booking_date' => $bookingDate,
                'return_date' => $returnDate,
                'trip_status' => $tripStatus
            ];
        }
    }

    $stmt->close();
}

$bookingsJson = json_encode(
    $bookings,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_HEX_AMP |
    JSON_INVALID_UTF8_SUBSTITUTE
);

if ($bookingsJson === false) {
    $bookingsJson = '[]';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Tracking | Velora Drive</title>

    <link rel="stylesheet" href="css/gps_tracking.css">

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <style>
        .gps-demo-notice {
            margin: 15px 0;
            padding: 12px 16px;
            border: 1px solid #e4cf88;
            border-radius: 10px;
            background: #fff9e8;
            color: #594817;
            line-height: 1.5;
        }

        .gps-error-message {
            margin: 15px 0;
            padding: 12px 16px;
            border-radius: 8px;
            background: #fff0f0;
            color: #a32020;
        }

        #map {
            width: 100%;
            min-height: 350px;
            border-radius: 12px;
            overflow: hidden;
        }

        .gps-empty-message {
            padding: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <main class="gps-container">

        <header class="gps-header">
            <h1>Vehicle GPS Tracking</h1>
            <p>Track your booked vehicle's simulated journey.</p>
        </header>

        <div class="gps-demo-notice">
            <strong>Demo mode:</strong>
            Vehicle locations on this page are simulated. This page does
            not access a real GPS device or show a vehicle's actual location.
        </div>

        <?php if ($errorMessage !== ''): ?>
            <div class="gps-error-message">
                <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (empty($bookings) && $errorMessage === ''): ?>
            <div class="gps-empty-message">
                <h2>No confirmed bookings found</h2>
                <p>Your confirmed vehicle bookings will appear here.</p>
            </div>
        <?php else: ?>
            <section class="gps-dashboard">

                <div class="gps-map-section">
                    <h2>Vehicle Location Map</h2>
                    <div id="map"></div>
                </div>

                <div class="gps-details-section">
                    <h2>Tracking Details</h2>

                    <div class="gps-info-card">
                        <p><strong>Total Vehicles:</strong>
                            <span id="vehicleCount">0</span>
                        </p>

                        <p><strong>Selected Vehicle:</strong>
                            <span id="selectedVehicle">Select a booking</span>
                        </p>

                        <p><strong>Booking ID:</strong>
                            <span id="selectedVehicleId">--</span>
                        </p>

                        <p><strong>Region:</strong>
                            <span id="vehicleRegion">--</span>
                        </p>

                        <p><strong>Simulated Speed:</strong>
                            <span id="vehicleSpeed">0 km/h</span>
                        </p>

                        <p><strong>Latitude:</strong>
                            <span id="vehicleLatitude">--</span>
                        </p>

                        <p><strong>Longitude:</strong>
                            <span id="vehicleLongitude">--</span>
                        </p>

                        <p><strong>Trip Progress:</strong>
                            <span id="tripProgress">0%</span>
                        </p>

                        <p><strong>Last Updated:</strong>
                            <span id="lastUpdate">Waiting for update</span>
                        </p>

                        <p><strong>Trip Status:</strong>
                            <span id="tripStatus">Checking...</span>
                        </p>
                    </div>

                    <h3>Your Confirmed Bookings</h3>
                    <div id="vehicleList"></div>
                </div>

            </section>
        <?php endif; ?>

    </main>

    <script>
        window.gpsBookings = <?= $bookingsJson ?>;
        window.bookings = window.gpsBookings;
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/gps_tracking.js"></script>
</body>
</html>
