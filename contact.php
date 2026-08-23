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

<title>Velora Drive | Contact Us</title>

<link rel="stylesheet" href="css/contact.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<!-- ==========================
NAVBAR
========================== -->

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

<li><a href="about.php">About</a></li>

<li><a href="contact.php" class="active">Contact</a></li>

<li><a href="logout.php">Logout</a></li>

</ul>

</nav>

<!-- ==========================
HERO
========================== -->

<section class="hero">

<div class="overlay"></div>

<div class="hero-content">

<h1>Contact <span>Us</span></h1>

<p>

We are always here to help you with your vehicle rental needs.

</p>

</div>

</section>

<!-- ==========================
CONTACT SECTION
========================== -->

<section class="contact-section">

<div class="contact-info">

<h2>Get In Touch</h2>

<div class="info">

<i class="fa-solid fa-location-dot"></i>

<div>

<h3>Address</h3>

<p>Kochi, Kerala, India</p>

</div>

</div>

<div class="info">

<i class="fa-solid fa-phone"></i>

<div>

<h3>Phone</h3>

<p>+91 98765 43210</p>

</div>

</div>

<div class="info">

<i class="fa-solid fa-envelope"></i>

<div>

<h3>Email</h3>

<p>support@veloradrive.com</p>

</div>

</div>

<div class="info">

<i class="fa-solid fa-clock"></i>

<div>

<h3>Working Hours</h3>

<p>Monday - Sunday<br>8:00 AM - 10:00 PM</p>

</div>

</div>

</div>

<div class="contact-form">

<h2>Send Us a Message</h2>

<form action="contact_action.php" method="POST">

<input
type="text"
name="fullname"
placeholder="Full Name"
required>

<input
type="email"
name="email"
placeholder="Email Address"
required>

<input
type="text"
name="subject"
placeholder="Subject"
required>

<textarea
name="message"
rows="6"
placeholder="Write your message..."
required></textarea>

<button
type="submit"
name="send">

Send Message

</button>

</form>

</div>

</section>

<!-- ==========================
MAP
========================== -->

<section class="map">

<iframe
src="https://www.google.com/maps?q=Kochi,Kerala&output=embed"
allowfullscreen
loading="lazy">

</iframe>

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

</body>

</html>