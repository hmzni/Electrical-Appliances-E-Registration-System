<?php
include 'db_connect.php';

$period_id = isset($_GET['period_id']) ? $conn->real_escape_string($_GET['period_id']) : '';
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

$sql = "SELECT DISTINCT s.student_id, s.full_name, s.profile_image, pr.receipt_path, pr.qr_code_status 
        FROM students s
        JOIN payment_receipts pr ON s.student_id = pr.student_id
        JOIN student_appliances sa ON s.student_id = sa.student_id
        JOIN appliances a ON sa.appliance_id = a.id
        WHERE a.semester_type = (SELECT semester_type FROM registration_periods WHERE id = '$period_id')";

if (!empty($status)) {
    $sql .= " AND pr.qr_code_status = '$status'";
}

$result = $conn->query($sql);

$students = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
echo json_encode($students);

$conn->close();
?>
