<?php
session_start();

if (!isset($_SESSION['fullname'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Velora Drive | Vehicles</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/vehicle.css">

</head>

<body>

<nav>

<div class="logo">
Velora <span>Drive</span>
</div>

<ul>
<li><a href="home.php">Home</a></li>
<li><a href="vehicles.php" class="active">Vehicles</a></li>
<li><a href="mybookings.php">My Bookings</a></li>
<li><a href="notifications.php">Notifications</a></li>
<li> <a href="offers.php">Offers</a></li>
<li><a href="profile.php">Profile</a></li>
<li><a href="about.php">About</a></li>
<li><a href="contact.php">Contact</a></li>
<li><a href="faq.php">FAQ</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>

</nav>

<section class="heading">

<h1>Our Premium Fleet</h1>

<p>Select your perfect ride for every journey.</p>

</section>

<?php

$categories = ["Car", "Bike", "Scooter"];

foreach ($categories as $category) {

echo "<section class='category'>";

echo "<h2>";

if($category=="Car")
echo "Cars";
elseif($category=="Bike")
echo "Bikes";
else
echo "Scooters";

echo "</h2>";

echo "<div class='cards'>";

$sql = "SELECT * FROM vehicles WHERE category='$category'";

$result = mysqli_query($conn,$sql);

while($row = mysqli_fetch_assoc($result))
{
?>

<div class="card">

<img src="images/<?php echo $row['image']; ?>">

<h3><?php echo $row['vehicle_name']; ?></h3>

<p><?php echo $category; ?></p>

<h4>₹<?php echo number_format($row['price']); ?> / Day</h4>

<a href="booking.php?id=<?php echo $row['id']; ?>" class="book-btn">
Book Now
</a>

</div>

<?php
}

echo "</div>";
echo "</section>";

}
?>

</body>
</html>