<?php
include 'db_connect.php';

$id = $_GET['id'];

$sql = "DELETE FROM appliances WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Appliance removed successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
