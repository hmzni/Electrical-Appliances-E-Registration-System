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

    // Remove cart item from the database
    $sql = "DELETE FROM student_appliances WHERE student_id = '$student_id' AND appliance_id = '$appliance_id'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

    $conn->close();
}
?>