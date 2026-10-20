
<?php
// Save this file as UTF-8 without BOM.
// Keep <?php as the first content in the file.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require customer login.
if (empty($_SESSION['email'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$customerEmail = trim($_SESSION['email']);
$gpsBookings = [];
$loadError = false;

// Load all confirmed bookings belonging to the logged-in customer.
// JavaScript uses the booking dates to determine trip status.
$sql = "SELECT id, vehicle_name, pickup_location, return_location,
               booking_date, return_date, trip_status
        FROM bookings
        WHERE email = ?
          AND booking_status = 'Confirmed'
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log('GPS tracking query preparation failed: ' . $conn->error);
    $loadError = true;
} else {
    $stmt->bind_param('s', $customerEmail);

    if (!$stmt->execute()) {
        error_log('GPS tracking query execution failed: ' . $stmt->error);
        $loadError = true;
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
            $gpsBookings[] = [
                'bookingId'   => (int) $bookingId,
                'name'        => (string) $vehicleName,
                'pickup'      => (string) $pickupLocation,
                'destination' => (string) $returnLocation,
                'bookingDate' => (string) $bookingDate,
                'returnDate'  => (string) $returnDate,
                'tripStatus'  => (string) $tripStatus
            ];
        }
    }

    $stmt->close();
}

// Safely pass booking data to JavaScript.
$gpsBookingsJson = json_encode(
    $gpsBookings,
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_INVALID_UTF8_SUBSTITUTE
);

if ($gpsBookingsJson === false) {
    $gpsBookingsJson = '[]';
    $loadError = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GPS Tracking | Velora Drive</title>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    >

    <link rel="stylesheet" href="css/gps_tracking.css">

    <style>
        .gps-page {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        .gps-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .gps-header h1 {
            margin: 0 0 8px;
        }

        .demo-badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            background: #fff3cd;
            color: #765b00;
            font-size: 13px;
            font-weight: 600;
        }

        .gps-notice {
            margin: 16px 0;
            padding: 12px 16px;
            border-radius: 8px;
            background: #fff8e6;
            color: #624d12;
        }

        .gps-error {
            background: #fff0f0;
            color: #9b2020;
        }

        .gps-map-controls {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 16px 0;
        }

        .gps-map-controls button {
            cursor: pointer;
            padding: 9px 15px;
            border: 1px solid #c8a43b;
            border-radius: 6px;
            background: #fff;
        }

        #map {
            width: 100%;
            min-height: 400px;
            height: 55vh;
            border-radius: 12px;
            z-index: 1;
        }

        .gps-details,
        .gps-vehicle-list {
            margin-top: 20px;
        }

        .gps-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
        }

        .gps-detail-item {
            padding: 14px;
            border: 1px solid #e8e2d4;
            border-radius: 8px;
            overflow-wrap: anywhere;
        }

        .gps-detail-item small {
            display: block;
            margin-bottom: 6px;
            color: #666;
        }

        .gps-empty {
            padding: 20px;
            border: 1px solid #e8e2d4;
            border-radius: 10px;
            margin-top: 16px;
        }

        @media (max-width: 600px) {
            .gps-page {
                padding: 14px;
            }

            #map {
                height: 380px;
            }
        }
    </style>
</head>

<body>

<main class="gps-page">

    <header class="gps-header">
        <div>
            <h1>GPS Tracking Dashboard</h1>
            <p>View the simulated progress of your confirmed bookings.</p>
        </div>

        <span class="demo-badge">Demo Mode</span>
    </header>

    <div class="gps-notice">
        <strong>Demo only:</strong>
        This page simulates vehicle movement on illustrative routes.
        It does not use a real GPS device or show the actual location
        of a vehicle.
    </div>

    <?php if ($loadError): ?>

        <div class="gps-notice gps-error">
            We couldn't load your tracking information.
            Please try again later.
        </div>

    <?php elseif (empty($gpsBookings)): ?>

        <div class="gps-empty">
            <h3>No confirmed bookings to track</h3>
            <p>
                Your confirmed bookings will appear here.
                Trip status is calculated from the booking dates
                in the tracking simulation.
            </p>
        </div>

    <?php else: ?>

        <section aria-label="Tracking summary">

            <h2>
                Confirmed Bookings:
                <span id="vehicleCount">
                    <?= count($gpsBookings) ?>
                </span>
            </h2>

            <div class="gps-map-controls">
                <button type="button" id="indiaBtn">View India</button>
                <button type="button" id="keralaBtn">View Kerala</button>
            </div>

            <div
                id="map"
                aria-label="Simulated vehicle tracking map"
            ></div>

            <section
                class="gps-details"
                aria-label="Selected booking details"
            >

                <div class="gps-detail-item">
                    <small>Vehicle</small>
                    <strong id="selectedVehicle">Select a booking</strong>
                </div>

                <div class="gps-detail-item">
                    <small>Booking ID</small>
                    <strong id="selectedVehicleId">—</strong>
                </div>

                <div class="gps-detail-item">
                    <small>Pickup Location</small>
                    <strong id="vehicleRegion">—</strong>
                </div>

                <div class="gps-detail-item">
                    <small>Destination</small>
                    <strong id="vehicleSpeed">—</strong>
                </div>

                <div class="gps-detail-item">
                    <small>Latitude</small>
                    <strong id="vehicleLatitude">—</strong>
                </div>

                <div class="gps-detail-item">
                    <small>Longitude</small>
                    <strong id="vehicleLongitude">—</strong>
                </div>

                <div class="gps-detail-item">
                    <small>Simulated Trip Progress</small>
                    <strong id="tripProgress">—</strong>
                </div>

                <div class="gps-detail-item">
                    <small>Last Demo Update</small>
                    <strong id="lastUpdate">—</strong>
                </div>

                <div class="gps-detail-item">
                    <small>Trip Status</small>
                    <strong id="tripStatus">Checking booking dates...</strong>
                </div>

            </section>

            <p id="selectionHint">
                Select a booking to view its simulated route.
            </p>

            <section class="gps-vehicle-list">
                <h2>Your Confirmed Bookings</h2>
                <div id="vehicleList"></div>
            </section>

        </section>

    <?php endif; ?>

</main>

<?php if (!$loadError && !empty($gpsBookings)): ?>

<script>
    window.gpsBookings = <?= $gpsBookingsJson ?>;
</script>

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    crossorigin=""
></script>

<script src="js/gps_tracking.js"></script>

<?php endif; ?>

</body>
</html>
