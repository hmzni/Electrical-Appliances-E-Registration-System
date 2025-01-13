<?php
session_start();
include 'db_connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Fetch appliance details
    $stmt = $conn->prepare("SELECT * FROM appliances WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $appliance = $result->fetch_assoc();

        // Check existing cart items for the student's semester
        $stmt = $conn->prepare("SELECT a.semester_type FROM appliances a
                                JOIN student_appliances sa ON a.id = sa.appliance_id
                                WHERE sa.student_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $cart_result = $stmt->get_result();

        $semester_conflict = false;
        while ($row = $cart_result->fetch_assoc()) {
            if ($row['semester_type'] !== $appliance['semester_type']) {
                $semester_conflict = true;
                break;
            }
        }
        $stmt->close();

        $appliance['semester_conflict'] = $semester_conflict;
        echo json_encode($appliance);
    } else {
        echo json_encode(['error' => 'Appliance not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid ID parameter']);
}

$conn->close();
?>