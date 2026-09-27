<?php

/*
|--------------------------------------------------------------------------
| Velora Drive Database Connection
|--------------------------------------------------------------------------
| Local XAMPP:
|   localhost / root / veloradrive
|
| Railway:
|   Uses MYSQLHOST, MYSQLPORT, MYSQLUSER,
|   MYSQLPASSWORD and MYSQLDATABASE
|--------------------------------------------------------------------------
*/

if (getenv("MYSQLHOST")) {

    // =========================
    // Railway / Production
    // =========================

    $host = getenv("MYSQLHOST");
    $port = getenv("MYSQLPORT");
    $user = getenv("MYSQLUSER");
    $password = getenv("MYSQLPASSWORD");
    $database = getenv("MYSQLDATABASE");

    if (
        !$host ||
        !$port ||
        !$user ||
        !$password ||
        !$database
    ) {
        die("Railway MySQL environment variables are missing.");
    }

    $conn = new mysqli(
        $host,
        $user,
        $password,
        $database,
        (int)$port
    );

} else {

    // =========================
    // Local XAMPP
    // =========================

    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "veloradrive";

    $conn = new mysqli(
        $host,
        $user,
        $password,
        $database
    );
}


// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


// Use UTF-8
$conn->set_charset("utf8mb4");

?>