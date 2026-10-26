<?php
// Report database errors as exceptions.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Local XAMPP database settings.
$host = 'localhost';
$user = 'root';
$pwd = '';
$sql_db = 'blackvault_db';

// Create the shared connection.
$conn = new mysqli($host, $user, $pwd, $sql_db);

// Support multilingual text.
$conn->set_charset('utf8mb4');