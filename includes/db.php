<?php

// Use Railway MySQL when deployed,
// otherwise use local XAMPP MySQL.

if (getenv('MYSQLHOST')) {

    // Railway
    $host = getenv('MYSQLHOST');
    $port = getenv('MYSQLPORT') ?: 3306;
    $username = getenv('MYSQLUSER');
    $password = getenv('MYSQLPASSWORD');
    $database = getenv('MYSQLDATABASE');

} else {

    // Local XAMPP
    $host = "localhost";
    $port = 3306;
    $username = "root";
    $password = "";
    $database = "veloradrive";
}

// Create connection
$conn = new mysqli(
    $host,
    $username,
    $password,
    $database,
    $port
);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set character encoding
$conn->set_charset("utf8mb4");

?>