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

<title>Velora Drive | FAQ</title>

<link rel="stylesheet" href="css/faq.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<!-- NAVBAR -->

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

<li><a href="contact.php">Contact</a></li>

<li><a href="faq.php" class="active">FAQ</a></li>

<li><a href="logout.php">Logout</a></li>

</ul>

</nav>

<!-- HERO -->

<section class="hero">

<div class="overlay"></div>

<div class="hero-content">

<h1>Frequently Asked <span>Questions</span></h1>

<p>

Find answers to the most common questions about Velora Drive.

</p>

</div>

</section>

<!-- FAQ -->

<section class="faq-section">

<h2>Frequently Asked Questions</h2>

<div class="faq-container">

<div class="faq">

<button class="question">

How do I book a vehicle?

<i class="fa-solid fa-plus"></i>

</button>

<div class="answer">

<p>

Browse the available vehicles, select your preferred vehicle, choose rental dates and complete the payment.

</p>

</div>

</div>

<div class="faq">

<button class="question">

Can I cancel my booking?

<i class="fa-solid fa-plus"></i>

</button>

<div class="answer">

<p>

Yes. You can cancel your booking before the pickup time according to our cancellation policy.

</p>

</div>

</div>

<div class="faq">

<button class="question">

What payment methods are accepted?

<i class="fa-solid fa-plus"></i>

</button>

<div class="answer">

<p>

We accept UPI, Credit Card, Debit Card and Net Banking.

</p>

</div>

</div>

<div class="faq">

<button class="question">

What documents are required?

<i class="fa-solid fa-plus"></i>

</button>

<div class="answer">

<p>

A valid Driving License and Government ID proof are required.

</p>

</div>

</div>

<div class="faq">

<button class="question">

Can I extend my rental period?

<i class="fa-solid fa-plus"></i>

</button>

<div class="answer">

<p>

Yes, subject to vehicle availability.

</p>

</div>

</div>

<div class="faq">

<button class="question">

What if the vehicle breaks down?

<i class="fa-solid fa-plus"></i>

</button>

<div class="answer">

<p>

Contact our 24/7 customer support immediately for roadside assistance.

</p>

</div>

</div>

</div>

</section>

<!-- CONTACT SUPPORT -->

<section class="support">

<h2>Still have questions?</h2>

<p>

Our support team is always ready to help you.

</p>

<a href="contact.php">

Contact Support

</a>

</section>

<!-- FOOTER -->

<footer>

<p>

© <?php echo date("Y"); ?>

<span>Velora Drive</span>

| Premium Vehicle Rental Management System

</p>

</footer>

<script src="js/faq.js"></script>

</body>

</html>