<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = $_POST['user_type'];
    $id = $_POST['id'];
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender']; // New gender field
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if user already exists
    $check_query = "";
    if ($user_type == 'student') {
        $check_query = "SELECT * FROM students WHERE student_id = '$id'";
    } elseif ($user_type == 'admin') {
        $check_query = "SELECT * FROM administrators WHERE admin_id = '$id'";
    }

    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo "User already exists.";
    } else {
        // Proceed with insertion
        if ($user_type == 'student') {
            $program = $_POST['program'];
            $faculty_or_department = $_POST['faculty_or_department'];
            $course = $_POST['course'];

            $insert_query = "INSERT INTO students (student_id, full_name, phone_number, gender, program, faculty, course, password)
                             VALUES ('$id', '$full_name', '$phone_number', '$gender', '$program', '$faculty_or_department', '$course', '$password')";
            $conn->query($insert_query);

            // Insert additional data for the student (if any)
        } elseif ($user_type == 'admin') {
            $department = $_POST['faculty_or_department'];

            $insert_query = "INSERT INTO administrators (admin_id, full_name, phone_number, department, password)
                             VALUES ('$id', '$full_name', '$phone_number', '$department', '$password')";
            $conn->query($insert_query);
        }

        if ($conn->affected_rows > 0) {
            echo "Sign up successful!";
        } else {
            echo "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>
