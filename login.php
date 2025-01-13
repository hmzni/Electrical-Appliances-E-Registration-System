<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = $_POST['user_type']; // Hidden field to identify user type
    $id = $_POST['id'];
    $password = $_POST['password'];
    $redirect_page = '';

    if ($user_type == 'student') {
        $sql = "SELECT student_id, full_name, password FROM students WHERE student_id = '$id'";
        $redirect_page = 'dashboard_student.php'; // Use PHP to correctly interpret session variables
    } elseif ($user_type == 'admin') {
        $sql = "SELECT admin_id, full_name, password FROM administrators WHERE admin_id = '$id'";
        $redirect_page = 'dashboard_admin.php'; // Updated to redirect to admin dashboard
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Store user's full name and id in session
            $_SESSION['user_name'] = $row['full_name'];
            $_SESSION['user_id'] = $row[$user_type == 'student' ? 'student_id' : 'admin_id'];
            $_SESSION['user_type'] = $user_type; // Set user_type in session

            if ($user_type == 'admin') {
                $_SESSION['admin_id'] = $row['admin_id']; // Ensure this is set for admins
            }
            // Redirect to appropriate page based on user type
            header("Location: $redirect_page");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that ID.";
    }

    $conn->close();
}
?>
