<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

header('Content-Type: application/json');

// Check for missing parameters and log them
$missing_params = [];

if (!isset($_GET['semester'])) $missing_params[] = 'semester';
if (!isset($_GET['program'])) $missing_params[] = 'program';
if (!isset($_GET['faculty'])) $missing_params[] = 'faculty';
if (!isset($_GET['course'])) $missing_params[] = 'course';

if (!empty($missing_params)) {
    echo json_encode(['error' => 'Missing parameters: ' . implode(', ', $missing_params)]);
    exit;
}

$semester = $_GET['semester'];
$program = $_GET['program'];
$faculty = $_GET['faculty'];
$course = $_GET['course'];

// Log the received parameters for debugging
error_log("Received parameters: semester=$semester, program=$program, faculty=$faculty, course=$course");

// Prepare the SQL query using prepared statements
$sql = "SELECT students.student_id, students.full_name, students.profile_image
        FROM students
        JOIN payments ON students.student_id = payments.student_id
        WHERE payments.status = 'pending'
        AND students.program = ?
        AND students.faculty = ?
        AND students.course = ?
        AND students.semester = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Database prepare statement failed: ' . $conn->error]);
    exit;
}

// Bind the parameters to the SQL query
$stmt->bind_param("ssss", $program, $faculty, $course, $semester);

// Execute the query and check for errors
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Database query execution failed: ' . $stmt->error]);
    exit;
}

// Get the result of the query
$result = $stmt->get_result();

// Fetch the students and store them in an array
$students = array();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Output the result as JSON
echo json_encode($students);

// Close the statement and connection
$stmt->close();
$conn->close();
?>