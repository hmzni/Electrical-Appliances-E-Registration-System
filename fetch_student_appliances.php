<?php
include 'db_connect.php';

$student_id = $_GET['student_id'];

$sql = "SELECT a.name, a.image_url, sa.quantity 
        FROM appliances a
        JOIN student_appliances sa ON a.id = sa.appliance_id
        WHERE sa.student_id = '$student_id'";
$result = $conn->query($sql);

$appliances = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $appliances[] = $row;
    }
}
echo json_encode($appliances);

$conn->close();
?>
