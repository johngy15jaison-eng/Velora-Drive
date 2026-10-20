
<?php
// Save this file as UTF-8 without BOM.
// There must be no content before this opening PHP tag.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login before displaying customer tracking information.
if (empty($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/includes/db.php';

$customerEmail = trim($_SESSION['email']);
$gpsBookings = [];
$loadError = false;

$sql = "
    SELECT
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
      AND trip_status = 'Active'
    ORDER BY id DESC
";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $customerEmail);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result(
            $stmt,
            $bookingId,
            $vehicleName,
            $pickupLocation,
            $returnLocation,
            $bookingDate,
            $returnDate,
            $tripStatus
        );

        while (mysqli_stmt_fetch($stmt)) {
            $gpsBookings[] = [
                'bookingId' => (int)$bookingId,
                'name' => $vehicleName,
                'pickup' => $pickupLocation,
                'destination' => $returnLocation,
                'bookingDate' => $bookingDate,
                'returnDate' => $returnDate,
                'tripStatus' => $tripStatus
            ];
        }
    } else {
        error_log("GPS booking query failed: " . mysqli_stmt_error($stmt));
        $loadError = true;
    }

    mysqli_stmt_close($stmt);
} else {
    error_log("GPS query preparation failed: " . mysqli_error($conn));
    $loadError = true;
}

$vehicleCount = count($gpsBookings);
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
    >

    <link rel="stylesheet" href="css/gps_tracking.css">
</head>

<body>

<header class="topbar">
    <a href="index.php" class="brand">
        Velora <span>Drive</span>
    </a>

    <div class="header-right">
        <span class="demo-badge">
            <span class="status-dot"></span>
            GPS Demo Mode
        </span>
    </div>
</header>

<main class="dashboard">

    <section class="page-heading">
        <div>
            <p class="eyebrow">MY RENTALS</p>
            <h1>GPS Tracking Dashboard</h1>
            <p class="subtitle">
                View simulated tracking for your eligible confirmed bookings.
            </p>
        </div>
    </section>

    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Your Active Trips</span>
                <span class="stat-icon">🚘</span>
            </div>
            <h2 id="vehicleCount"><?= $vehicleCount ?></h2>
            <p>Eligible confirmed bookings</p>
        </article>

        <article class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Tracking</span>
                <span class="stat-icon">📡</span>
            </div>
            <h2 class="gold-text">Demo</h2>
            <p>Simulated movement only</p>
        </article>

        <article class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Map Region</span>
                <span class="stat-icon">📍</span>
            </div>
            <h2>India</h2>
            <p>Illustrative demo routes</p>
        </article>
    </section>

    <?php if ($loadError): ?>
        <section class="panel">
            <h2>Tracking information unavailable</h2>
            <p>
                We could not load your bookings. Please refresh the page
                later or contact support if the problem continues.
            </p>
        </section>

    <?php elseif ($vehicleCount === 0): ?>
        <section class="panel">
            <h2>No eligible trips to track</h2>
            <p>
                This page currently shows confirmed bookings whose trip
                status is Active. Cancelled bookings and bookings that
                have not been activated are not displayed.
            </p>
            <p>
                The GPS feature is a simulation and does not use a physical
                GPS device.
            </p>
        </section>

    <?php else: ?>

        <section class="panel map-panel">
            <div class="panel-heading">
                <div>
                    <h2>Your Vehicle Map</h2>
                    <p>Select a marker to inspect your booking.</p>
                </div>

                <div class="map-actions">
                    <button type="button" id="indiaBtn" class="secondary-btn">
                        View All India
                    </button>

                    <button type="button" id="keralaBtn" class="primary-btn">
                        Focus Kerala
                    </button>
                </div>
            </div>

            <div id="map" aria-label="Simulated vehicle tracking map"></div>

            <div class="map-legend">
                <span><i class="legend-dot moving-dot"></i> Simulated vehicle</span>
                <span><i class="legend-line"></i> Illustrative demo route</span>
            </div>

            <p class="disclaimer">
                Demonstration only: vehicle locations, speeds and routes are
                simulated. They do not show real-time vehicle positions.
                No physical GPS device is connected.
            </p>
        </section>

        <section class="panel details-panel">
            <div class="panel-heading details-heading">
                <div>
                    <h2>Selected Booking Details</h2>
                    <p id="selectionHint">Select a booking marker to view details.</p>
                </div>

                <span class="trip-badge" id="tripStatus">DEMO MODE</span>
            </div>

            <div class="details-grid">
                <article class="detail-item">
                    <span class="detail-label">Vehicle</span>
                    <strong id="selectedVehicle">Select a booking</strong>
                </article>

                <article class="detail-item">
                    <span class="detail-label">Booking ID</span>
                    <strong id="selectedVehicleId">--</strong>
                </article>

                <article class="detail-item">
                    <span class="detail-label">Pickup</span>
                    <strong id="vehicleRegion">--</strong>
                </article>

                <article class="detail-item">
                    <span class="detail-label">Destination</span>
                    <strong id="vehicleSpeed">--</strong>
                </article>

                <article class="detail-item">
                    <span class="detail-label">Latitude</span>
                    <strong id="vehicleLatitude">--</strong>
                </article>

                <article class="detail-item">
                    <span class="detail-label">Longitude</span>
                    <strong id="vehicleLongitude">--</strong>
                </article>

                <article class="detail-item">
                    <span class="detail-label">Trip Progress</span>
                    <strong id="tripProgress">0%</strong>
                </article>

                <article class="detail-item">
                    <span class="detail-label">Last Update</span>
                    <strong id="lastUpdate">Waiting...</strong>
                </article>
            </div>
        </section>

        <section class="panel vehicle-panel">
            <div class="panel-heading">
                <div>
                    <h2>Your Trackable Bookings</h2>
                    <p>Select a booking to view its simulated route.</p>
                </div>
            </div>

            <div id="vehicleList" class="vehicle-list"></div>
        </section>

    <?php endif; ?>

</main>

<footer class="footer">
    <p>© 2026 Velora Drive | Simulated GPS Demonstration</p>
</footer>

<?php if (!$loadError && $vehicleCount > 0): ?>
<script>
window.gpsBookings = <?= json_encode(
    $gpsBookings,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/gps_tracking.js"></script>
<?php endif; ?>

</body>
</html>
