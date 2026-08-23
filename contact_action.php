<?php

include("db.php");

if(isset($_POST['send'])){

    $fullname = mysqli_real_escape_string($conn,$_POST['fullname']);

    $email = mysqli_real_escape_string($conn,$_POST['email']);

    $subject = mysqli_real_escape_string($conn,$_POST['subject']);

    $message = mysqli_real_escape_string($conn,$_POST['message']);

    $sql = "INSERT INTO contact_messages
            (fullname,email,subject,message)
            VALUES
            ('$fullname','$email','$subject','$message')";

    if(mysqli_query($conn,$sql)){

        echo "<script>

        alert('Your message has been sent successfully!');

        window.location='contact.php';

        </script>";

    }

    else{

        echo "<script>

        alert('Something went wrong!');

        window.location='contact.php';

        </script>";

    }

}

?>