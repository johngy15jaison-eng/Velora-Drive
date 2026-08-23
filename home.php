<?php
session_start();

if(!isset($_SESSION['fullname'])){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Velora Drive | Home</title>

<link rel="stylesheet" href="css/home.css">

</head>

<body>

<nav>

<h2 class="logo">
Velora <span>Drive</span>
</h2>

<ul>

<li><a href="home.php" class="nav-btn">Home</a></li>

<li><a href="vehicles.php" class="nav-btn">Vehicles</a></li>

<li><a href="mybookings.php" class="nav-btn">My Bookings</a></li>

<li><a href="profile.php">Profile</a></li>

<li><a href="about.php">About</a></li>

<li><a href="faq.php">FAQ</a></li> 

<li><a href="contact.php" class="nav-btn">Contact</a></li>

<li><a href="logout.php" class="nav-btn">Logout</a></li>

</ul>

</nav>

<section class="hero">

<h1>
Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?> 👋
</h1>

<p>
Rent luxury cars anytime, anywhere.
</p>

<a href="vehicles.php" class="btn">
Explore Vehicles
</a>

</section>

</body>

</html>