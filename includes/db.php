<?php

$host = getenv("MYSQLHOST");
$port = getenv("MYSQLPORT");
$user = getenv("MYSQLUSER");
$password = getenv("MYSQLPASSWORD");
$database = getenv("MYSQLDATABASE");

if (!$host || !$port || !$user || !$password || !$database) {
    die("MySQL environment variables are missing.");
}

$conn = new mysqli(
    $host,
    $user,
    $password,
    "",
    (int)$port
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!$conn->select_db($database)) {
    die("Database selection failed: " . $conn->error);
}

$conn->set_charset("utf8mb4");

?>
