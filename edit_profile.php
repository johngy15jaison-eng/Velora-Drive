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

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Profile | Velora Drive</title>

<link rel="stylesheet" href="css/edit_profile.css">

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

<li><a href="profile.php">Profile</a></li>

<li><a href="logout.php">Logout</a></li>

</ul>

</nav>

<section class="edit-container">

<div class="edit-card">

<h1>

<i class="fa-solid fa-user-pen"></i>

Edit Profile

</h1>

<form action="update_profile.php"

method="POST"

enctype="multipart/form-data">
<form action="update_profile.php"
method="POST"
enctype="multipart/form-data">

<!-- PROFILE IMAGE -->

<div class="profile-upload">

<img src="images/profile.png" class="profile-preview">

<label>

<i class="fa-solid fa-camera"></i>

Change Photo

</label>

<input

type="file"

name="profile_image"

accept="image/*">

</div>


<!-- FULL NAME -->

<div class="input-box">

<label>

Full Name

</label>

<input

type="text"

name="fullname"

value="<?php echo $data['fullname']; ?>"

required>

</div>


<!-- EMAIL -->

<div class="input-box">

<label>

Email Address

</label>

<input

type="email"

value="<?php echo $data['email']; ?>"

readonly>

</div>


<!-- PHONE -->

<div class="input-box">

<label>

Phone Number

</label>

<input

type="text"

name="phone"

value="<?php echo isset($data['phone']) ? $data['phone'] : ''; ?>"

placeholder="Enter Phone Number">

</div>


<!-- ADDRESS -->

<div class="input-box">

<label>

Address

</label>

<textarea

name="address"

rows="4"

placeholder="Enter Address"><?php echo isset($data['address']) ? $data['address'] : ''; ?></textarea>

</div>

<div class="button-group">

<button type="submit" class="save-btn">

<i class="fa-solid fa-floppy-disk"></i>

Save Changes

</button>

<a href="profile.php" class="cancel-btn">

<i class="fa-solid fa-arrow-left"></i>

Cancel

</a>

</div>

</form>

</div>

</section>

</body>

</html>