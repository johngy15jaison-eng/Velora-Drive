<?php

session_start();

include("config.php");   // Change to db.php if your connection file is db.php

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0){

        $row = mysqli_fetch_assoc($result);

        if(password_verify($password,$row['password'])){

            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['email'] = $row['email'];

            header("Location: home.php");
            exit();

        }else{

            $_SESSION['error'] = "Invalid Password!";
            header("Location: index.php");
            exit();

        }

    }else{

        $_SESSION['error'] = "Email not found!";
        header("Location: index.php");
        exit();

    }

}
?>