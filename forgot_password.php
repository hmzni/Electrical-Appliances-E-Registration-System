<?php
include 'db_connect.php';

$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : '';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = $_POST['user_type'];
    $id = $_POST['id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        if ($user_type == 'student') {
            $check_query = "SELECT * FROM students WHERE student_id = '$id'";
            $update_query = "UPDATE students SET password = '$password_hashed' WHERE student_id = '$id'";
        } elseif ($user_type == 'admin') {
            $check_query = "SELECT * FROM administrators WHERE admin_id = '$id'";
            $update_query = "UPDATE administrators SET password = '$password_hashed' WHERE admin_id = '$id'";
        }

        $result = $conn->query($check_query);

        if ($result->num_rows > 0) {
            // User exists, proceed with update
            if ($conn->query($update_query) === TRUE) {
                $message = "Password has been updated successfully.";
            } else {
                $message = "Error updating password: " . $conn->error;
            }
        } else {
            $message = "User ID not found. Please sign up first.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="index.css">
    <style>
        .input-field {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .message {
            margin-top: 20px;
            color: green;
        }
        .buttons-container {
            display: flex;
            justify-content: space-between;
        }
.btn-back {
    width: 200px; /* Adjust width as needed */
    background-color: #FCA4A6;
    border: none;
    outline: none;
    height: 49px;
    border-radius: 49px;
    color: #fff;
    text-transform: uppercase;
    font-weight: 600;
    margin: 10px auto; /* Center the button horizontally */
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center; /* Center text horizontally */
    font-size: 15px; /* Decreased font size */
}

.btn-back:hover {
    background-color: #FFC0CB;
    color: #fff;
}

    </style>
    <title>Forgot Password</title>
</head>
<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form action="forgot_password.php" method="post" class="sign-in-form">
                    <h2 class="title">Forgot Password</h2>
                    <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($user_type); ?>">
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="id" placeholder="<?php echo $user_type == 'student' ? 'Student ID' : 'Admin ID'; ?>" required>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="new_password" placeholder="New Password" id="password-new" required>
                        <i class="fas fa-eye toggle-password" id="toggle-password-new"></i>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" id="confirm-password-new" required>
                        <i class="fas fa-eye toggle-password" id="toggle-confirm-password-new"></i>
                    </div>
                    <div class="buttons-container">
                        <input type="submit" value="Reset Password" class="btn solid">
                        <a href="<?php echo $user_type == 'student' ? 'student_login.html' : 'admin_login.html'; ?>" class="btn-back">Back to Login</a>
                    </div>
                    <?php if (!empty($message)): ?>
                        <p class="message"><?php echo htmlspecialchars($message); ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePasswordNew = document.getElementById('toggle-password-new');
            const toggleConfirmPasswordNew = document.getElementById('toggle-confirm-password-new');
            const passwordFieldNew = document.getElementById('password-new');
            const confirmPasswordFieldNew = document.getElementById('confirm-password-new');

            togglePasswordNew.addEventListener('click', function() {
                if (passwordFieldNew.type === 'password') {
                    passwordFieldNew.type = 'text';
                    togglePasswordNew.classList.remove('fa-eye');
                    togglePasswordNew.classList.add('fa-eye-slash');
                } else {
                    passwordFieldNew.type = 'password';
                    togglePasswordNew.classList.remove('fa-eye-slash');
                    togglePasswordNew.classList.add('fa-eye');
                }
            });

            toggleConfirmPasswordNew.addEventListener('click', function() {
                if (confirmPasswordFieldNew.type === 'password') {
                    confirmPasswordFieldNew.type = 'text';
                    toggleConfirmPasswordNew.classList.remove('fa-eye');
                    toggleConfirmPasswordNew.classList.add('fa-eye-slash');
                } else {
                    confirmPasswordFieldNew.type = 'password';
                    toggleConfirmPasswordNew.classList.remove('fa-eye-slash');
                    toggleConfirmPasswordNew.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>
