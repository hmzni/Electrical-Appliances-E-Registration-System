<?php
include 'db_connect.php';

$id = $_POST['id'];
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$semester = $_POST['semester'];
$quantity = $_POST['quantity'];

$sql = "UPDATE appliances SET name='$name', description='$description', price_rm='$price', semester_type='$semester', quantity='$quantity' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Appliance updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
