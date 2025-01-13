<?php
include 'db_connect.php';

$student_id = isset($_GET['student_id']) ? $conn->real_escape_string($_GET['student_id']) : '';

$sql = "SELECT name, image_url, quantity 
        FROM appliances 
        WHERE student_id='$student_id'";

$result = $conn->query($sql);

if (!$result) {
    // Error handling
    echo "Error: " . $sql . "<br>" . $conn->error;
} else {
    $appliances = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $appliances[] = $row;
        }
    } else {
        $appliances = ["error" => "No appliances found"];
    }

    echo json_encode($appliances);
}

$conn->close();
?>
