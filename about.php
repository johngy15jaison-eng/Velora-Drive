<?php
session_start();

if(!isset($_SESSION['fullname'])){
    header("Location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Velora Drive | About Us</title>

<link rel="stylesheet" href="css/about.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<!-- Navigation -->

<nav>

<div class="logo">

Velora <span>Drive</span>

</div>

<ul>

<li><a href="home.php">Home</a></li>

<li><a href="vehicles.php">Vehicles</a></li>

<li><a href="mybookings.php">Bookings</a></li>

<li><a href="notifications.php">Notifications</a></li>

<li><a href="profile.php">Profile</a></li>

<li><a href="about.php" class="active">About</a></li>

<li><a href="contact.php">Contact</a></li>

<li><a href="logout.php">Logout</a></li>

</ul>

</nav>

<!-- Hero Section -->

<section class="hero">

<div class="overlay"></div>

<div class="hero-content">

<h1>About Velora <span>Drive</span></h1>

<p>
Luxury. Comfort. Trust.
Experience premium vehicle rentals with confidence.
</p>

</div>

</section>

<!-- About Section -->

<section class="about">

<div class="about-text">

<h2>Who We Are</h2>

<p>

Velora Drive is a premium vehicle rental management system designed to provide customers with a safe, convenient, and luxurious travel experience.

Our platform offers seamless online booking, secure payments, verified vehicles, and dedicated customer support.

</p>

</div>

</section>
<!-- ==========================
MISSION & VISION
========================== -->

<section class="mission">

<div class="card">

<i class="fa-solid fa-bullseye"></i>

<h2>Our Mission</h2>

<p>

To provide reliable, luxurious and affordable vehicle rental services while ensuring a seamless booking experience and excellent customer satisfaction.

</p>

</div>

<div class="card">

<i class="fa-solid fa-eye"></i>

<h2>Our Vision</h2>

<p>

To become India's most trusted premium vehicle rental platform by delivering innovation, quality and exceptional customer service.

</p>

</div>

</section>


<!-- ==========================
WHY CHOOSE US
========================== -->

<section class="choose">

<h2>Why Choose Velora Drive?</h2>

<div class="choose-grid">

<div>

<i class="fa-solid fa-car-side"></i>

<h3>Luxury Fleet</h3>

<p>

Premium cars maintained with the highest safety standards.

</p>

</div>

<div>

<i class="fa-solid fa-credit-card"></i>

<h3>Secure Payments</h3>

<p>

100% secure online payment gateway with instant confirmation.

</p>

</div>

<div>

<i class="fa-solid fa-headset"></i>

<h3>24/7 Support</h3>

<p>

Dedicated support team ready to assist you anytime.

</p>

</div>

<div>

<i class="fa-solid fa-shield-halved"></i>

<h3>Trusted Rentals</h3>

<p>

Verified vehicles and transparent booking process.

</p>

</div>

</div>

</section>


<!-- ==========================
STATISTICS
========================== -->

<section class="stats">

<div>

<h1>10K+</h1>

<p>Happy Customers</p>

</div>

<div>

<h1>500+</h1>

<p>Luxury Vehicles</p>

</div>

<div>

<h1>50+</h1>

<p>Cities Covered</p>

</div>

<div>

<h1>24/7</h1>

<p>Customer Support</p>

</div>

</section>


<!-- ==========================
FOOTER
========================== -->

<footer>

<p>

© <?php echo date("Y"); ?>

<span>Velora Drive</span>

| Premium Vehicle Rental Management System

</p>

</footer>

<script src="js/about.js"></script>

</body>

</html>