<?php
include 'db_connect.php';

// Ensure no previous output
header('Content-Type: application/json');

$semester = isset($_GET['semester']) ? $conn->real_escape_string($_GET['semester']) : '';

$sql = "SELECT * FROM appliances WHERE semester_type = '$semester'";
$result = $conn->query($sql);

$appliances = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appliances[] = $row;
    }
}

// Ensure no output other than JSON
echo json_encode($appliances);

$conn->close();
?>
