<?php

session_start();

require_once __DIR__ . '/includes/db.php';

/* =========================================
   HELPER FUNCTION
========================================= */

function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}


/* =========================================
   RECEIVE BOOKING DATA FROM booking.php
========================================= */

$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

$vehicle_id = $_POST['vehicle_id'] ?? '';
$vehicle_name = $_POST['vehicle_name'] ?? '';
$vehicle_image = $_POST['vehicle_image'] ?? '';

$price = $_POST['price'] ?? '';

$pickup_location = $_POST['pickup_location'] ?? '';
$return_location = $_POST['return_location'] ?? '';

$booking_date = $_POST['booking_date'] ?? '';
$return_date = $_POST['return_date'] ?? '';

$total_amount = $_POST['total_amount'] ?? '';


/* =========================================
   DOCUMENT UPLOADS
========================================= */

$license_file = '';
$license_path = '';

$government_id_file = '';
$government_id_path = '';


$upload_dir = "uploads/";


if (!is_dir($upload_dir)) {

    mkdir($upload_dir, 0777, true);

}


/* =========================================
   DRIVING LICENSE
========================================= */

if (
    isset($_FILES['driving_license']) &&
    $_FILES['driving_license']['error'] === UPLOAD_ERR_OK
) {

    $original_name =
        basename($_FILES['driving_license']['name']);

    $safe_name =
        time() . "_license_" . preg_replace(
            "/[^A-Za-z0-9._-]/",
            "_",
            $original_name
        );

    $license_path =
        $upload_dir . $safe_name;

    $license_file =
        $original_name;


    move_uploaded_file(
        $_FILES['driving_license']['tmp_name'],
        $license_path
    );
}


/* =========================================
   GOVERNMENT ID
========================================= */

if (
    isset($_FILES['government_id']) &&
    $_FILES['government_id']['error'] === UPLOAD_ERR_OK
) {

    $original_name =
        basename($_FILES['government_id']['name']);

    $safe_name =
        time() . "_government_" . preg_replace(
            "/[^A-Za-z0-9._-]/",
            "_",
            $original_name
        );

    $government_id_path =
        $upload_dir . $safe_name;

    $government_id_file =
        $original_name;


    move_uploaded_file(
        $_FILES['government_id']['tmp_name'],
        $government_id_path
    );
}


/* =========================================
   CHECK REQUIRED BOOKING INFORMATION
========================================= */

$missing = [];


if (empty($fullname)) {
    $missing[] = "fullname";
}

if (empty($email)) {
    $missing[] = "email";
}

if (empty($phone)) {
    $missing[] = "phone";
}

if (empty($vehicle_name)) {
    $missing[] = "vehicle_name";
}

if (empty($pickup_location)) {
    $missing[] = "pickup_location";
}

if (empty($return_location)) {
    $missing[] = "return_location";
}

if (empty($booking_date)) {
    $missing[] = "booking_date";
}

if (empty($return_date)) {
    $missing[] = "return_date";
}

if ($total_amount === '' || $total_amount === null) {
    $missing[] = "total_amount";
}


/* =========================================
   STOP IF DATA IS MISSING
========================================= */

if (!empty($missing)) {

    echo "<!DOCTYPE html>";
    echo "<html>";
    echo "<head>";
    echo "<title>Booking Information Missing</title>";
    echo "<style>";
    echo "
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 50px;
        }

        .error-box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h2 {
            color: #b00020;
        }

        li {
            margin: 8px 0;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background: #111;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
        }
    ";
    echo "</style>";
    echo "</head>";

    echo "<body>";

    echo "<div class='error-box'>";

    echo "<h2>Booking information is missing</h2>";

    echo "<p>The following information was not received:</p>";

    echo "<ul>";

    foreach ($missing as $field) {

        echo "<li>" . e($field) . "</li>";

    }

    echo "</ul>";

    echo "<a href='javascript:history.back()'>Go Back</a>";

    echo "</div>";

    echo "</body>";
    echo "</html>";

    exit;
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Velora Drive | Secure Payment</title>


<!-- GOOGLE FONT -->

<link rel="preconnect"
      href="https://fonts.googleapis.com">

<link rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet">


<!-- FONT AWESOME -->

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


<!-- PAYMENT CSS -->
<style>
<?php

$payment_css = __DIR__ . '/css/payment.css';

if (file_exists($payment_css)) {
    readfile($payment_css);
}

?>
</style>
</head>


<body>


<!-- =========================================
     NAVBAR
========================================= -->

<nav>

<div class="nav-container">


<a href="home.php"
   class="logo">

    Velora <span>Drive</span>

</a>


<ul class="nav-links">

    <li>
        <a href="home.php">Home</a>
    </li>

    <li>
        <a href="vehicles.php">Vehicles</a>
    </li>

    <li>
        <a href="mybookings.php">My Bookings</a>
    </li>

    <li>
        <a href="notifications.php">Notifications</a>
    </li>

    <li>
        <a href="profile.php">Profile</a>
    </li>

    <li>
        <a href="logout.php">Logout</a>
    </li>

</ul>


</div>

</nav>



<!-- =========================================
     PAGE HEADER
========================================= -->

<section class="payment-header">

    <div class="secure-label">

        <i class="fa-solid fa-shield-halved"></i>

        SECURE CHECKOUT

    </div>


    <h1>
        Complete Your Payment
    </h1>


    <p>

        Review your booking and choose your
        preferred payment method.

    </p>

</section>



<!-- =========================================
     PROGRESS
========================================= -->

<div class="checkout-progress">


    <div class="step completed">

        <div class="step-number">

            <i class="fa-solid fa-check"></i>

        </div>

        Booking

    </div>


    <div class="step-line completed-line"></div>


    <div class="step active">

        <div class="step-number">
            2
        </div>

        Payment

    </div>


    <div class="step-line"></div>


    <div class="step">

        <div class="step-number">
            3
        </div>

        Confirmation

    </div>


</div>



<!-- =========================================
     MAIN CONTAINER
========================================= -->

<div class="payment-container">



<!-- =========================================
     BOOKING SUMMARY
========================================= -->

<section class="payment-card">


<div class="card-header">

    <h2>
        Booking Summary
    </h2>

    <p>
        Your selected vehicle and rental details
    </p>

</div>


<div class="card-body">


<div class="vehicle-image">


<?php if (!empty($vehicle_image)): ?>

<img
    src="<?php echo e($vehicle_image); ?>"
    alt="<?php echo e($vehicle_name); ?>"
>


<?php else: ?>

<div class="vehicle-placeholder">

    <i class="fa-solid fa-car"></i>

</div>

<?php endif; ?>


</div>



<span class="premium-tag">

    <i class="fa-solid fa-star"></i>

    PREMIUM VEHICLE

</span>



<div class="vehicle-name">

    <?php echo e($vehicle_name); ?>

</div>



<div class="detail-list">


<div class="detail">

    <span>
        Customer
    </span>

    <strong>
        <?php echo e($fullname); ?>
    </strong>

</div>



<div class="detail">

    <span>
        Email
    </span>

    <strong>
        <?php echo e($email); ?>
    </strong>

</div>



<div class="detail">

    <span>
        Phone
    </span>

    <strong>
        <?php echo e($phone); ?>
    </strong>

</div>



<div class="detail">

    <span>
        Pickup
    </span>

    <strong>
        <?php echo e($pickup_location); ?>
    </strong>

</div>



<div class="detail">

    <span>
        Return
    </span>

    <strong>
        <?php echo e($return_location); ?>
    </strong>

</div>



<div class="detail">

    <span>
        Pickup Date
    </span>

    <strong>
        <?php echo e($booking_date); ?>
    </strong>

</div>



<div class="detail">

    <span>
        Return Date
    </span>

    <strong>
        <?php echo e($return_date); ?>
    </strong>

</div>


</div>



<div class="divider"></div>



<div class="amount-row">

    <span>
        Total Rental Amount
    </span>

    <div class="amount">

        ₹<?php echo number_format((float)$total_amount, 2); ?>

    </div>

</div>


</div>

</section>



<!-- =========================================
     PAYMENT SECTION
========================================= -->

<section class="payment-card">


<div class="card-header">

    <h2>
        Payment Method
    </h2>

    <p>
        Select how you would like to complete your booking.
    </p>

</div>



<div class="card-body">


<!-- PAYMENT METHODS -->

<div class="payment-methods">


<button
    type="button"
    class="method active"
    data-method="upi">

    <i class="fa-solid fa-mobile-screen-button"></i>

    <span>UPI</span>

</button>



<button
    type="button"
    class="method"
    data-method="card">

    <i class="fa-regular fa-credit-card"></i>

    <span>Card</span>

</button>



<button
    type="button"
    class="method"
    data-method="cash">

    <i class="fa-solid fa-money-bill-wave"></i>

    <span>Cash</span>

</button>


</div>



<!-- =========================================
     PAYMENT FORM
========================================= -->

<form
    action="complete_booking.php"
    method="POST"
    id="paymentForm">



<!-- BOOKING DATA -->

<input
    type="hidden"
    name="fullname"
    value="<?php echo e($fullname); ?>"
>


<input
    type="hidden"
    name="email"
    value="<?php echo e($email); ?>"
>


<input
    type="hidden"
    name="phone"
    value="<?php echo e($phone); ?>"
>


<input
    type="hidden"
    name="vehicle_id"
    value="<?php echo e($vehicle_id); ?>"
>


<input
    type="hidden"
    name="vehicle_name"
    value="<?php echo e($vehicle_name); ?>"
>


<input
    type="hidden"
    name="vehicle_image"
    value="<?php echo e($vehicle_image); ?>"
>


<input
    type="hidden"
    name="price"
    value="<?php echo e($price); ?>"
>


<input
    type="hidden"
    name="pickup_location"
    value="<?php echo e($pickup_location); ?>"
>


<input
    type="hidden"
    name="return_location"
    value="<?php echo e($return_location); ?>"
>


<input
    type="hidden"
    name="booking_date"
    value="<?php echo e($booking_date); ?>"
>


<input
    type="hidden"
    name="return_date"
    value="<?php echo e($return_date); ?>"
>


<input
    type="hidden"
    name="total_amount"
    value="<?php echo e($total_amount); ?>"
>



<!-- DOCUMENT DATA -->

<input
    type="hidden"
    name="license_file"
    value="<?php echo e($license_file); ?>"
>


<input
    type="hidden"
    name="license_path"
    value="<?php echo e($license_path); ?>"
>


<input
    type="hidden"
    name="government_id_file"
    value="<?php echo e($government_id_file); ?>"
>


<input
    type="hidden"
    name="government_id_path"
    value="<?php echo e($government_id_path); ?>"
>



<!-- PAYMENT METHOD -->

<input
    type="hidden"
    name="payment_method"
    id="payment_method"
    value="UPI"
>



<!-- =========================================
     UPI
========================================= -->

<div
    class="payment-section active"
    id="upi"
>


<div class="payment-title">

    <div class="payment-title-icon">

        <i class="fa-solid fa-mobile-screen-button"></i>

    </div>

    <h3>
        Pay with UPI
    </h3>

</div>



<p class="payment-description">

    Enter your UPI ID to continue securely.

</p>



<div class="form-group">

<label for="upi_id">

    UPI ID

</label>


<div class="input-wrap">

    <i class="fa-solid fa-at"></i>

    <input
        type="text"
        id="upi_id"
        name="upi_id"
        placeholder="example@upi"
        maxlength="50"
        autocomplete="off"
    >

</div>

</div>



<div class="security-note">

    <i class="fa-solid fa-circle-info"></i>

    <span>

        This is a college-project payment simulation.
        No real UPI transaction will be processed.

    </span>

</div>


</div>



<!-- =========================================
     CARD
========================================= -->

<div
    class="payment-section"
    id="card"
>


<div class="payment-title">

    <div class="payment-title-icon">

        <i class="fa-regular fa-credit-card"></i>

    </div>

    <h3>
        Card Payment
    </h3>

</div>



<p class="payment-description">

    Enter demonstration card information.

</p>



<div class="form-group">

<label>
    Card Number
</label>


<div class="input-wrap">

    <i class="fa-regular fa-credit-card"></i>

    <input
        type="text"
        id="card_number"
        name="card_number"
        placeholder="1234 5678 9012 3456"
        maxlength="19"
        inputmode="numeric"
        autocomplete="off"
    >

</div>

</div>



<div class="form-group">

<label>
    Cardholder Name
</label>


<div class="input-wrap">

    <i class="fa-regular fa-user"></i>

    <input
        type="text"
        id="cardholder_name"
        name="cardholder_name"
        placeholder="Cardholder name"
        maxlength="100"
        autocomplete="off"
    >

</div>

</div>



<div class="form-row">


<div class="form-group">

<label>
    Expiry Date
</label>


<div class="input-wrap">

    <i class="fa-regular fa-calendar"></i>

    <input
        type="text"
        id="expiry_date"
        name="expiry_date"
        placeholder="MM / YY"
        maxlength="7"
        inputmode="numeric"
        autocomplete="off"
    >

</div>

</div>



<div class="form-group">

<label>
    CVV
</label>


<div class="input-wrap">

    <i class="fa-solid fa-lock"></i>

    <input
        type="password"
        id="cvv"
        name="cvv"
        placeholder="•••"
        maxlength="3"
        inputmode="numeric"
        autocomplete="off"
    >

</div>

</div>


</div>



<div class="security-note">

    <i class="fa-solid fa-shield-halved"></i>

    <span>

        Demonstration only.
        Never enter a real card number or CVV.

    </span>

</div>


</div>



<!-- =========================================
     CASH
========================================= -->

<div
    class="payment-section"
    id="cash"
>


<div class="payment-title">

    <div class="payment-title-icon">

        <i class="fa-solid fa-money-bill-wave"></i>

    </div>

    <h3>
        Cash on Pickup
    </h3>

</div>



<p class="payment-description">

    Pay the rental amount when you collect the vehicle.

</p>



<div class="cash-box">

    <strong>
        Cash payment selected
    </strong>

    <br><br>

    Please keep the total rental amount ready
    when collecting your vehicle.

</div>


</div>



<!-- =========================================
     CONFIRM BUTTON
========================================= -->

<button
    type="submit"
    class="pay-btn"
>

    <i class="fa-solid fa-lock"></i>

    Confirm Booking

</button>


</form>


</div>

</section>



</div>



<!-- =========================================
     FOOTER
========================================= -->

<footer>

    <i class="fa-solid fa-lock"></i>

    Velora Drive • Secure Booking Checkout

</footer>



<script>

/* =========================================
   PAYMENT METHOD SWITCHING
========================================= */

const methods =
    document.querySelectorAll(".method");

const sections =
    document.querySelectorAll(".payment-section");

const paymentMethod =
    document.getElementById("payment_method");


methods.forEach(function(method) {

    method.addEventListener("click", function() {


        /* Remove active from all buttons */

        methods.forEach(function(item) {

            item.classList.remove("active");

        });


        /* Add active to clicked button */

        method.classList.add("active");


        /* Get selected method */

        const selected =
            method.dataset.method;


        /* Update hidden field */

        if (selected === "upi") {

            paymentMethod.value = "UPI";

        }

        else if (selected === "card") {

            paymentMethod.value = "Card";

        }

        else if (selected === "cash") {

            paymentMethod.value = "Cash";

        }


        /* Hide all payment sections */

        sections.forEach(function(section) {

            section.classList.remove("active");

        });


        /* Show selected section */

        const selectedSection =
            document.getElementById(selected);


        if (selectedSection) {

            selectedSection.classList.add("active");

        }

    });

});


/* =========================================
   FORM SUBMIT
========================================= */

document
    .getElementById("paymentForm")
    .addEventListener("submit", function(event) {

        /*
         * Payment is a college-project simulation.
         * No real transaction is processed.
         */

        const selectedMethod =
            paymentMethod.value;


        /* =========================================
           UPI VALIDATION
        ========================================= */

        if (selectedMethod === "UPI") {

            const upiInput =
                document.getElementById("upi_id");

            const upiValue =
                upiInput.value.trim();


            if (upiValue === "") {

                event.preventDefault();

                alert("Please enter your UPI ID.");

                upiInput.focus();

                return;

            }


            const upiPattern =
                /^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+$/;


            if (!upiPattern.test(upiValue)) {

                event.preventDefault();

                alert(
                    "Please enter a valid UPI ID, for example: name@upi"
                );

                upiInput.focus();

                return;

            }

        }


        /* =========================================
           CARD VALIDATION
        ========================================= */

        if (selectedMethod === "Card") {

            const cardNumberInput =
                document.getElementById("card_number");

            const cardholderInput =
                document.getElementById("cardholder_name");

            const expiryInput =
                document.getElementById("expiry_date");

            const cvvInput =
                document.getElementById("cvv");


            const cardNumber =
                cardNumberInput.value.replace(/\s+/g, "").trim();

            const cardholderName =
                cardholderInput.value.trim();

            const expiry =
                expiryInput.value.trim();

            const cvv =
                cvvInput.value.trim();


            /* Card number */

            if (!/^\d{16}$/.test(cardNumber)) {

                event.preventDefault();

                alert(
                    "Please enter a valid 16-digit card number."
                );

                cardNumberInput.focus();

                return;

            }


            /* Cardholder name */

            if (cardholderName === "") {

                event.preventDefault();

                alert("Please enter the cardholder name.");

                cardholderInput.focus();

                return;

            }


            /* Expiry date */

            const expiryPattern =
                /^(0[1-9]|1[0-2])\s*\/\s*([0-9]{2}|[0-9]{4})$/;


            if (!expiryPattern.test(expiry)) {

                event.preventDefault();

                alert("Please enter expiry date as MM / YY.");

                expiryInput.focus();

                return;

            }


            /* CVV */

            if (!/^\d{3}$/.test(cvv)) {

                event.preventDefault();

                alert("Please enter a valid 3-digit CVV.");

                cvvInput.focus();

                return;

            }

        }


        /* =========================================
           CASH
        ========================================= */

        /*
         * Cash requires no payment details.
         * The booking can continue normally.
         */

    });

</script>


</body>

</html>