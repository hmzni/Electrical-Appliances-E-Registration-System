<?php
$servername = "localhost"; // Hostinger database host
$username = "u446885939_jani_username"; // MySQL username
$password = "Hamzani@10"; // MySQL password
$dbname = "u446885939_jani_db"; // MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Comment out or remove the following line
// echo "Connected successfully";
?>
