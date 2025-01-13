<?php
include 'db_connect.php';

$sql = "SELECT * FROM appliances";
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
