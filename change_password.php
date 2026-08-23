<?php
session_start();

include("config.php");

if(!isset($_SESSION['email'])){
    header("Location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Velora Drive | Change Password</title>

<link rel="stylesheet" href="css/change_password.css">

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

<li><a href="profile.php">Profile</a></li>

<li><a href="contact.php">Contact</a></li>

<li><a href="logout.php">Logout</a></li>

</ul>

</nav>

<section class="password-section">

<div class="password-card">

<h1>

<i class="fa-solid fa-lock"></i>

Change Password

</h1>

<p>

Keep your Velora Drive account secure by updating your password regularly.

</p>

<form action="change_password_action.php" method="POST">

<!-- CURRENT PASSWORD -->

<div class="input-box">

<label>

Current Password

</label>

<div class="password-field">

<input

type="password"

name="current_password"

id="currentPassword"

placeholder="Enter Current Password"

required>

<i class="fa-solid fa-eye toggle-password"
onclick="togglePassword('currentPassword',this)"></i>

</div>

</div>


<!-- NEW PASSWORD -->

<div class="input-box">

<label>

New Password

</label>

<div class="password-field">

<input

type="password"

name="new_password"

id="newPassword"

placeholder="Enter New Password"

required>

<i class="fa-solid fa-eye toggle-password"
onclick="togglePassword('newPassword',this)"></i>

</div>

</div>


<!-- CONFIRM PASSWORD -->

<div class="input-box">

<label>

Confirm New Password

</label>

<div class="password-field">

<input

type="password"

name="confirm_password"

id="confirmPassword"

placeholder="Confirm New Password"

required>

<i class="fa-solid fa-eye toggle-password"
onclick="togglePassword('confirmPassword',this)"></i>

</div>

</div>


<!-- PASSWORD REQUIREMENTS -->

<div class="password-rules">

<h4>Password must contain:</h4>

<ul>

<li>✔ At least 8 characters</li>

<li>✔ One uppercase letter</li>

<li>✔ One lowercase letter</li>

<li>✔ One number</li>

<li>✔ One special character</li>

</ul>

</div>

<div class="button-group">

<button type="submit" class="update-btn">

<i class="fa-solid fa-key"></i>

Update Password

</button>

<a href="profile.php" class="cancel-btn">

<i class="fa-solid fa-arrow-left"></i>

Cancel

</a>

</div>

</form>

</div>

</section>

<script>

function togglePassword(id,icon){

var input=document.getElementById(id);

if(input.type==="password"){

input.type="text";

icon.classList.remove("fa-eye");

icon.classList.add("fa-eye-slash");

}else{

input.type="password";

icon.classList.remove("fa-eye-slash");

icon.classList.add("fa-eye");

}

}

</script>

<script>
document.querySelector("form").addEventListener("submit", function(e){

    let password = document.getElementById("newPassword").value;
    let confirm = document.getElementById("confirmPassword").value;

    let pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.#])[A-Za-z\d@$!%*?&.#]{8,}$/;

    if(!pattern.test(password)){
        alert("Password must contain:\n\n• At least 8 characters\n• One uppercase letter\n• One lowercase letter\n• One number\n• One special character");
        e.preventDefault();
        return;
    }

    if(password !== confirm){
        alert("New Password and Confirm Password do not match.");
        e.preventDefault();
    }

});
</script>

</body>

</html>