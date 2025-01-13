<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit();
    }

    $student_id = $_SESSION['user_id'];
    $appliance_id = $data['appliance_id'];
    $quantity = $data['quantity'];
    $semester = $data['semester'];

    // Insert cart item into the database
    $sql = "INSERT INTO student_appliances (student_id, appliance_id, quantity)
            VALUES ('$student_id', '$appliance_id', '$quantity')
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

    $conn->close();
}
?>
