<?php
session_start();

$error = "";

if(isset($_SESSION['error'])){
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Velora Drive | Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/index.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<style>

.error-message{
    color:red;
    text-align:center;
    margin-bottom:15px;
    font-weight:bold;
}

</style>

</head>

<body>

<nav>

<div class="logo">
Velora <span>Drive</span>
</div>

<ul>
<li><a href="#">Home</a></li>
<li><a href="#">Vehicles</a></li>
<li><a href="#">About</a></li>
<li><a href="#">Contact</a></li>
</ul>

<a href="register.php" class="register-btn">Register</a>

</nav>

<div class="container">

<div class="left">

<h1>Welcome Back</h1>

<p>
Sign in to continue your premium vehicle rental experience.
</p>

<?php if($error!=""){ ?>
<div class="error-message">
<?php echo $error; ?>
</div>
<?php } ?>

<form action="login_action.php" method="POST">

<div class="input-group">

<label>Email Address</label>

<i class="fa-solid fa-envelope"></i>

<input
type="email"
name="email"
placeholder="Enter your email"
required>

</div>

<div class="input-group">

<label>Password</label>

<i class="fa-solid fa-lock"></i>

<input
type="password"
name="password"
placeholder="Enter your password"
required>

</div>

<div class="options">

<label>
<input type="checkbox">
Remember Me
</label>

<a href="#">Forgot Password?</a>

</div>

<button type="submit" name="login" class="login-btn">
Login
</button>

</form>

<div class="signup">

Don't have an account?

<a href="register.php">
Create Account
</a>

</div>

</div>

<div class="right">

<img src="images/car.jpg" alt="Luxury Cars">

</div>

</div>

</body>
</html>