<?php
session_start();

require_once __DIR__ . '/includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid booking ID.");
}

$booking_id = (int) $_GET['id'];

$sql = "SELECT 
            id,
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
            created_at
        FROM bookings
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $booking_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found.");
}

$booking = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Booking Details | Velora Drive</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f7fa;
            color: #172538;
        }

        /* NAVBAR */

        nav {
            height: 95px;
            background: #142235;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 4%;
        }

        .logo {
            font-size: 34px;
            font-weight: 700;
            color: white;
        }

        .logo span {
            color: #e6aa1c;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: #e6aa1c;
        }

        /* MAIN */

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 50px auto;
        }

        .page-title {
            font-size: 32px;
            margin-bottom: 30px;
            color: #142235;
        }

        .booking-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .booking-header {
            background: #142235;
            color: white;
            padding: 25px 30px;
        }

        .booking-header h2 {
            font-size: 26px;
        }

        .booking-header p {
            margin-top: 5px;
            color: #d9dee5;
        }

        .booking-content {
            display: grid;
            grid-template-columns: 40% 60%;
        }

        .vehicle-section {
            background: #f7f8fa;
            padding: 30px;
            text-align: center;
        }

        .vehicle-section img {
            width: 100%;
            max-width: 380px;
            height: 250px;
            object-fit: cover;
            border-radius: 12px;
        }

        .vehicle-section h3 {
            margin-top: 20px;
            font-size: 24px;
        }

        .details-section {
            padding: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 14px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: 600;
            color: #142235;
        }

        .value {
            color: #555;
            text-align: right;
        }

        .status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .confirmed {
            background: #d9f7e4;
            color: #138a43;
        }

        .cancelled {
            background: #ffe0e0;
            color: #d52d2d;
        }

        .pending {
            background: #fff0c7;
            color: #a66b00;
        }

        .paid {
            color: #138a43;
            font-weight: 600;
        }

        .total {
            font-size: 22px;
            font-weight: 700;
            color: #e0a817;
        }

        .back-button {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background: #142235;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .back-button:hover {
            background: #e6aa1c;
            color: #142235;
        }

        @media (max-width: 800px) {

            nav {
                height: auto;
                padding: 20px;
                flex-direction: column;
                gap: 20px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .booking-content {
                grid-template-columns: 1fr;
            }

            .detail-row {
                flex-direction: column;
                gap: 5px;
            }

            .value {
                text-align: left;
            }
        }

    </style>
</head>

<body>

<nav>

    <div class="logo">
        Velora <span>Drive</span>
    </div>

    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="vehicles.php">Vehicles</a>
        <a href="mybookings.php">My Bookings</a>
        <a href="notifications.php">Notifications</a>
        <a href="profile.php">Profile</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="faq.php">FAQ</a>
        <a href="logout.php">Logout</a>
    </div>

</nav>


<div class="container">

    <h1 class="page-title">Booking Details</h1>

    <div class="booking-card">

        <div class="booking-header">

            <h2>
                Booking #<?php echo htmlspecialchars($booking['id']); ?>
            </h2>

            <p>
                Created on
                <?php echo date("d M Y, h:i A", strtotime($booking['created_at'])); ?>
            </p>

        </div>


        <div class="booking-content">

            <!-- VEHICLE -->

            <div class="vehicle-section">

                <?php if (!empty($booking['vehicle_image'])): ?>

                    <img
                        src="<?php echo htmlspecialchars($booking['vehicle_image']); ?>"
                        alt="<?php echo htmlspecialchars($booking['vehicle_name']); ?>"
                    >

                <?php else: ?>

                    <div style="
                        height:250px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:#ddd;
                        border-radius:12px;
                    ">
                        No Vehicle Image
                    </div>

                <?php endif; ?>


                <h3>
                    <?php echo htmlspecialchars($booking['vehicle_name']); ?>
                </h3>

                <p style="margin-top:8px;">
                    ₹<?php echo number_format($booking['price'], 2); ?> / day
                </p>

            </div>


            <!-- DETAILS -->

            <div class="details-section">

                <div class="detail-row">
                    <span class="label">Customer Name</span>
                    <span class="value">
                        <?php echo htmlspecialchars($booking['fullname']); ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="label">Email</span>
                    <span class="value">
                        <?php echo htmlspecialchars($booking['email']); ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="label">Phone</span>
                    <span class="value">
                        <?php echo htmlspecialchars($booking['phone']); ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="label">Vehicle</span>
                    <span class="value">
                        <?php echo htmlspecialchars($booking['vehicle_name']); ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="label">Pickup Date</span>
                    <span class="value">
                        <?php echo date("d M Y", strtotime($booking['booking_date'])); ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="label">Return Date</span>
                    <span class="value">
                        <?php echo date("d M Y", strtotime($booking['return_date'])); ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="label">Pickup Location</span>
                    <span class="value">
                        <?php echo htmlspecialchars($booking['pickup_location']); ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="label">Return Location</span>
                    <span class="value">
                        <?php echo htmlspecialchars($booking['return_location']); ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="label">Payment Method</span>
                    <span class="value">
                        <?php echo htmlspecialchars($booking['payment_method']); ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="label">Payment Status</span>
                    <span class="value paid">
                        <?php echo htmlspecialchars($booking['payment_status']); ?>
                    </span>
                </div>


                <div class="detail-row">

                    <span class="label">Booking Status</span>

                    <span class="value">

                        <?php
                        $status = strtolower($booking['booking_status']);

                        if ($status === 'confirmed') {
                            echo '<span class="status confirmed">Confirmed</span>';
                        } elseif ($status === 'cancelled') {
                            echo '<span class="status cancelled">Cancelled</span>';
                        } else {
                            echo '<span class="status pending">'
                                . htmlspecialchars($booking['booking_status'])
                                . '</span>';
                        }
                        ?>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="label">Total Amount</span>

                    <span class="value total">
                        ₹<?php echo number_format($booking['total_amount'], 2); ?>
                    </span>

                </div>


                <a href="mybookings.php" class="back-button">
                    ← Back to My Bookings
                </a>

            </div>

        </div>

    </div>

</div>

</body>
</html>