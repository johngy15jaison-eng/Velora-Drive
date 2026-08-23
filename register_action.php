<?php

include("db.php");

if(isset($_POST['register'])){

    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    // Password Validation
if (
    strlen($password) < 8 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[\W_]/', $password)
) {

    echo "<script>
    alert('Password must contain:\\n\\n• At least 8 characters\\n• One uppercase letter\\n• One lowercase letter\\n• One number\\n• One special character');
    window.location='register.php';
    </script>";

    exit();
}

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){

        echo "<script>
        alert('Email already exists!');
        window.location='register.php';
        </script>";

        exit();

    }

    // Encrypt password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO users(fullname,email,password)
            VALUES('$fullname','$email','$hashedPassword')";

    if(mysqli_query($conn,$sql)){

        echo "<script>
        alert('Registration Successful!');
        window.location='index.php';
        </script>";

    }else{

        echo "Error : " . mysqli_error($conn);

    
    }


}

?>