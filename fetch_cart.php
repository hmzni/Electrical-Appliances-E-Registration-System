<?php
include 'db_connect.php';

session_start(); // Start or resume session

$student_id = $_SESSION['student_id']; // Assuming you store student_id in session upon login

$sql = "SELECT c.id as cart_id, a.id, a.name, a.description, a.image_url, a.price_rm, c.quantity
        FROM cart_appliances c
        INNER JOIN appliances a ON c.appliance_id = a.id
        WHERE c.student_id = '$student_id'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $cart_items = array();
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
    echo json_encode($cart_items);
} else {
    echo json_encode([]);
}

$conn->close();
?>
