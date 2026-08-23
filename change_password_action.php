<?php

session_start();

include("config.php");

if(!isset($_SESSION['email'])){
    header("Location:index.php");
    exit();
}

$email = $_SESSION['email'];

$currentPassword = $_POST['current_password'];
$newPassword = $_POST['new_password'];
$confirmPassword = $_POST['confirm_password'];

/* ==========================
   GET CURRENT PASSWORD
========================== */

$result = mysqli_query($conn,"SELECT password FROM users WHERE email='$email'");

$user = mysqli_fetch_assoc($result);

if(!$user){

    echo "<script>
    alert('User not found.');
    window.location='change_password.php';
    </script>";
    exit();

}

/* ==========================
   VERIFY CURRENT PASSWORD
========================== */

if(!password_verify($currentPassword,$user['password'])){

    echo "<script>
    alert('Current Password is incorrect.');
    window.location='change_password.php';
    </script>";
    exit();

}

/* ==========================
   CHECK NEW PASSWORD
========================== */

if($newPassword != $confirmPassword){

    echo "<script>
    alert('New Password and Confirm Password do not match.');
    window.location='change_password.php';
    </script>";
    exit();

}

/* ==========================
   PASSWORD LENGTH
========================== */

if(strlen($newPassword) < 8){

    echo "<script>
    alert('Password must contain at least 8 characters.');
    window.location='change_password.php';
    </script>";
    exit();

}

/* ==========================
   HASH PASSWORD
========================== */

$hashedPassword = password_hash($newPassword,PASSWORD_DEFAULT);

/* ==========================
   UPDATE PASSWORD
========================== */

$sql = "UPDATE users
SET password='$hashedPassword'
WHERE email='$email'";

if(mysqli_query($conn,$sql)){

    echo "<script>

    alert('Password Updated Successfully!');

    window.location='profile.php';

    </script>";

}else{

    echo "<script>

    alert('Unable to update password.');

    window.location='change_password.php';

    </script>";

}

?>