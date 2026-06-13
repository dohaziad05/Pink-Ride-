<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "pink_ride_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// دعم اللغة العربية
$conn->set_charset("utf8mb4");
$conn->query("SET time_zone = '+03:00'");

?>
