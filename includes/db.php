
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = getenv('MYSQLHOST') ?: '';
$user = getenv('MYSQLUSER') ?: '';
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQLDATABASE') ?: '';
$port = (int) (getenv('MYSQLPORT') ?: 3306);

// Stop safely if database settings are missing.
if ($host === '' || $user === '' || $database === '' || $port <= 0) {
    error_log('Velora Drive: Database environment variables are missing.');
    http_response_code(500);
    exit('Database connection is temporarily unavailable.');
}

try {
    $conn = new mysqli(
        $host,
        $user,
        $password === false ? '' : $password,
        $database,
        $port
    );

    $conn->set_charset('utf8mb4');

} catch (mysqli_sql_exception $e) {
    error_log('Velora Drive database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Database connection is temporarily unavailable.');
}
