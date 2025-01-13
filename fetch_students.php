<?php
include 'db_connect.php';

$semester = isset($_GET['semester']) ? $conn->real_escape_string($_GET['semester']) : '';
$program = isset($_GET['program']) ? $conn->real_escape_string($_GET['program']) : '';
$faculty = isset($_GET['faculty']) ? $conn->real_escape_string($_GET['faculty']) : '';
$course = isset($_GET['course']) ? $conn->real_escape_string($_GET['course']) : '';

$sql = "SELECT student_id, full_name, profile_image, receipt 
        FROM students 
        WHERE program='$program' 
          AND faculty='$faculty' 
          AND course='$course' 
          AND semester='$semester' 
          AND receipt IS NOT NULL";

$result = $conn->query($sql);

if (!$result) {
    // Error handling
    echo "Error: " . $sql . "<br>" . $conn->error;
} else {
    $students = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    } else {
        $students = ["error" => "No students found"];
    }

    echo json_encode($students);
}

$conn->close();
?>