<?php

session_start();
include("config.php");

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

/* ==========================
   GET FORM DATA
========================== */

$fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
$address = mysqli_real_escape_string($conn, trim($_POST['address']));

/* ==========================
   GET CURRENT USER
========================== */

$result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

$user = mysqli_fetch_assoc($result);

$currentImage = $user['profile_image'];

/* ==========================
   IMAGE UPLOAD
========================== */

$imageName = $currentImage;

if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error']==0){

    $allowed = array("jpg","jpeg","png","webp");

    $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

    if(in_array($extension,$allowed)){

        $imageName = time().".".$extension;

        move_uploaded_file(
            $_FILES['profile_image']['tmp_name'],
            "images/".$imageName
        );

    }

}

/* ==========================
   UPDATE USER
========================== */

$sql = "UPDATE users SET

fullname='$fullname',

phone='$phone',

address='$address',

profile_image='$imageName'

WHERE email='$email'";

if(mysqli_query($conn,$sql)){

    $_SESSION['fullname']=$fullname;

    echo "<script>

    alert('Profile Updated Successfully!');

    window.location='profile.php';

    </script>";

}else{

    echo "<script>

    alert('Unable to update profile.');

    window.history.back();

    </script>";

}

?>