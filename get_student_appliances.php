<?php
include 'db_connect.php';

$student_id = $_GET['student_id'];

$sql = "SELECT appliances.id, appliances.name, appliances.image_url, student_appliances.quantity
        FROM student_appliances 
        JOIN appliances ON student_appliances.appliance_id = appliances.id
        WHERE student_appliances.student_id='$student_id'";
$result = $conn->query($sql);

$appliances = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appliances[] = $row;
    }
}
echo json_encode($appliances);

$conn->close();
?>
