<?php
session_start();

if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    session_unset();
    session_destroy();
    
    if ($user_type == 'student') {
        header("Location: student_login.html");
    } else if ($user_type == 'admin') {
        header("Location: admin_login.html");
    }
} else {
    session_unset();
    session_destroy();
    header("Location: index.html"); // Fallback redirect if user type is not set
}
exit();
?>
