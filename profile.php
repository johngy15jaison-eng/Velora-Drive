<?php
session_start();
include("config.php");

if(!isset($_SESSION['email'])){
    header("Location:index.php");
    exit();
}

$email=$_SESSION['email'];

$user=mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
$data=mysqli_fetch_assoc($user);

$booking=mysqli_query($conn,"SELECT COUNT(*) AS total FROM bookings WHERE email='$email'");
$totalBooking=mysqli_fetch_assoc($booking);

$spent=mysqli_query($conn,"SELECT SUM(total_amount) AS amount FROM bookings WHERE email='$email'");
$totalSpent=mysqli_fetch_assoc($spent);

$recent=mysqli_query($conn,"SELECT * FROM bookings WHERE email='$email' ORDER BY id DESC LIMIT 3");
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Velora Drive | My Profile</title>

<link rel="stylesheet" href="css/profile.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<nav>

<div class="logo">

Velora <span>Drive</span>

</div>

<ul>

<li><a href="home.php">Home</a></li>

<li><a href="vehicles.php">Vehicles</a></li>

<li><a href="mybookings.php">Bookings</a></li>

<li><a href="profile.php" class="active">Profile</a></li>

<li><a href="logout.php">Logout</a></li>

</ul>

</nav>

<section class="profile-hero">

<div class="profile-card">

<div class="profile-image">

<img src="images/profile.png" alt="Profile">

</div>

<h2>

<?php echo $data['fullname']; ?>

</h2>

<p>

Premium Velora Member

</p>

<div class="badge">

<i class="fa-solid fa-crown"></i>

Gold Member

</div>

</div>

</section>

<section class="dashboard">

<div class="card">

<div class="icon">

<i class="fa-solid fa-car-side"></i>

</div>

<div class="info">

<h1>

<?php echo $totalBooking['total']; ?>

</h1>

<p>Total Bookings</p>

</div>

</div>

<div class="card">

<div class="icon">

<i class="fa-solid fa-wallet"></i>

</div>

<div class="info">

<h1>

₹<?php echo number_format($totalSpent['amount'] ?? 0); ?>

</h1>

<p>Total Spent</p>

</div>

</div>

<div class="card">

<div class="icon">

<i class="fa-solid fa-star"></i>

</div>

<div class="info">

<h1>

Gold

</h1>

<p>Membership</p>

</div>

</div>

<div class="card">

<div class="icon">

<i class="fa-solid fa-road"></i>

</div>

<div class="info">

<h1>

<?php echo $totalBooking['total']; ?>

</h1>

<p>Completed Trips</p>

</div>

</div>

</section>

<section class="profile-details">

<div class="details-card">

<div class="title">

<i class="fa-solid fa-user"></i>

<h2>Personal Information</h2>

</div>

<div class="details-grid">

<div class="detail-box">

<label>Full Name</label>

<p><?php echo $data['fullname']; ?></p>

</div>

<div class="detail-box">

<label>Email Address</label>

<p><?php echo $data['email']; ?></p>

</div>

<div class="detail-box">

<label>Phone Number</label>

<p>
<?php
echo !empty($data['phone']) ? $data['phone'] : "Not Added";
?>
</p>

</div>

<div class="detail-box">

<label>Address</label>

<p>
<?php
echo !empty($data['address']) ? $data['address'] : "Not Added";
?>
</p>

</div>

<div class="detail-box">

<label>Member Since</label>

<p>

<?php

if(isset($data['created_at'])){

echo date("d M Y",strtotime($data['created_at']));

}else{

echo "Velora Member";

}

?>

</p>

</div>

<div class="detail-box">

<label>Membership</label>

<p>

<i class="fa-solid fa-crown"></i>

Gold Member

</p>

</div>

</div>

<div class="profile-buttons">

<a href="edit_profile.php" class="edit-btn">

<i class="fa-solid fa-user-pen"></i>

Edit Profile

</a>

<a href="change_password.php" class="password-btn">

<i class="fa-solid fa-lock"></i>

Change Password

</a>

</div>

</div>

</section>

<section class="recent-bookings">

<div class="recent-title">

<i class="fa-solid fa-clock-rotate-left"></i>

<h2>Recent Bookings</h2>

</div>

<div class="booking-list">

<?php

if(mysqli_num_rows($recent)>0){

while($book=mysqli_fetch_assoc($recent)){

?>

<div class="booking-card">

<div class="booking-icon">

<i class="fa-solid fa-car-side"></i>

</div>

<div class="booking-details">

<h3>

<?php echo $book['vehicle_name']; ?>

</h3>

<p>

<i class="fa-solid fa-calendar-days"></i>

<?php echo date("d M Y",strtotime($book['booking_date'])); ?>

</p>

<p>

<i class="fa-solid fa-credit-card"></i>

<?php echo $book['payment_method']; ?>

</p>

</div>

<div class="booking-status">

<span class="status">

<?php echo $book['booking_status']; ?>

</span>

</div>

</div>

<?php

}

}else{

?>

<div class="no-booking">

<i class="fa-solid fa-car"></i>

<p>No Bookings Found</p>

</div>

<?php

}

?>

</div>

</section>

<section class="quick-actions">

<div class="action-title">

<i class="fa-solid fa-bolt"></i>

<h2>Quick Actions</h2>

</div>

<div class="action-grid">

<a href="mybookings.php" class="action-card">

<i class="fa-solid fa-calendar-check"></i>

<h3>My Bookings</h3>

<p>View all your bookings</p>

</a>

<a href="edit_profile.php" class="action-card">

<i class="fa-solid fa-user-pen"></i>

<h3>Edit Profile</h3>

<p>Update your personal details</p>

</a>

<a href="contact.php" class="action-card">

<i class="fa-solid fa-headset"></i>

<h3>Support</h3>

<p>Need help? Contact us</p>

</a>

<a href="logout.php" class="action-card logout">

<i class="fa-solid fa-right-from-bracket"></i>

<h3>Logout</h3>

<p>Sign out safely</p>

</a>

</div>

</section>

<!-- SUPPORT -->

<section class="support-section">

<div class="support-card">

<i class="fa-solid fa-phone-volume"></i>

<h2>Need Assistance?</h2>

<p>

Our support team is available 24/7 to assist you with your bookings.

</p>

<div class="support-details">

<p>

<i class="fa-solid fa-phone"></i>

+91 98765 43210

</p>

<p>

<i class="fa-solid fa-envelope"></i>

support@veloradrive.com

</p>

</div>

</div>

</section>

<!-- FOOTER -->

<footer>

<p>

© <?php echo date("Y"); ?>

Velora Drive |

Premium Vehicle Rental Management System

</p>

</footer>

</body>

</html>