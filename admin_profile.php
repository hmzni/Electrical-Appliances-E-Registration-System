<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.html");
    exit();
}

include 'db_connect.php';

$upload_message = '';
$update_message = '';
$is_editing = isset($_GET['edit']) && $_GET['edit'] == '1';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"]) && $_FILES["profile_image"]["tmp_name"] != '') {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $upload_message = "File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES["profile_image"]["size"] > 500000) {
        $upload_message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $upload_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        $upload_message = "Sorry, your file was not uploaded.";
    } else {
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true); // Create directory if not exists
        }

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $sql = "UPDATE administrators SET profile_image='$target_file' WHERE admin_id='".$_SESSION['user_id']."'";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['profile_image'] = $target_file;
                $upload_message = "The file " . basename($_FILES["profile_image"]["name"]) . " has been uploaded.";
            } else {
                $upload_message = "Sorry, there was an error updating your profile image.";
            }
        } else {
            $upload_message = "Sorry, there was an error uploading your file.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $admin_id = $_POST['admin_id'];
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $department = $_POST['department'];

    $sql = "UPDATE administrators SET admin_id='$admin_id', full_name='$full_name', phone_number='$phone_number', department='$department' WHERE admin_id='".$_SESSION['user_id']."'";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['user_name'] = $full_name; // Update session name
        $update_message = "Profile updated successfully.";
    } else {
        $update_message = "Error updating profile: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_image"])) {
    $sql = "UPDATE administrators SET profile_image='' WHERE admin_id='".$_SESSION['user_id']."'";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['profile_image'] = '';
        $upload_message = "Profile image removed successfully.";
    } else {
        $upload_message = "Error removing profile image: " . $conn->error;
    }
}

$sql = "SELECT * FROM administrators WHERE admin_id='".$_SESSION['user_id']."'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            position: relative;
        }

        .profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #FF4D6A;
        }

        .profile-container .profile-img {
            display: block;
            margin: 0 auto 20px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #FF4D6A;
        }

        .profile-container .profile-details {
            margin-bottom: 20px;
            text-align: center;
        }

        .profile-container .profile-details p {
            margin: 5px 0;
            color: #555;
        }

        .profile-container .upload-section {
            text-align: center;
        }

        .profile-container .upload-section input[type="file"] {
            display: none;
        }

        .profile-container .upload-section label {
            cursor: pointer;
            background-color: #FF4D6A;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .profile-container .upload-section input[type="submit"], .btn {
            background-color: #FF4D6A;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        .profile-container .upload-section .btn.remove {
            background-color: #ff6b6b;
        }

        .profile-container .edit-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-container .edit-form input, .profile-container .edit-form select {
            width: 80%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .profile-container .edit-form input[type="submit"] {
            width: auto;
            padding: 10px 20px;
        }

        #user-info {
            display: flex;
            align-items: center;
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
        }

        #user-info .user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            border: 1px solid #FF69B4;
            background-color: #FFC0CB;
        }

        #user-info span {
            color: #1a1a1a;
            margin-right: 10px;
        }

        body {
            background-color: #FCA4A6;
        }

        #user-info .dropdown {
            position: relative;
            display: inline-block;
        }

        #user-info .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        #user-info .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        #user-info .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        #user-info .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>

<section id="header">
    <div id="user-info">
        <img src="<?php echo isset($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'default.jpg'; ?>" alt="" class="user-img">
        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <div class="dropdown">
                <i class="fas fa-caret-down"></i>
                <div class="dropdown-content">
                    <a href="admin_profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
        </div>
    </div>
    <div>
    <ul id="navbar">
        <li><a href="dashboard_admin.php">Home</a></li>
        <li><a href="manageitem.php">Manage Item</a></li>
        <li><a href="admin_review_generate.php">Payment & QR</a></li>
        <li><a href="admin_monitoring.php">Monitoring</a></li>
    </ul>
</div>
</section>

<section id="page-header" style="background-image: url('manageitemheader.jpg');">
    <h2 style="color: white; text-shadow: 2px 2px 4px #000;">#admin profile</h2>
</section>

<div class="profile-container" style="margin-top: 50px;">
    <h2>Admin Profile</h2>
    <img src="<?php echo isset($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'default.jpg'; ?>" alt="" class="profile-img" id="profile-img-display">
    <div class="profile-details">
        <?php if ($is_editing): ?>
        <form action="admin_profile.php" method="post" class="edit-form">
            <input type="hidden" name="update_profile" value="1">
            <p><strong>Staff ID:</strong> <input type="text" name="admin_id" value="<?php echo htmlspecialchars($user['admin_id']); ?>"></p>
            <p><strong>Name:</strong> <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>"></p>
            <p><strong>Phone Number:</strong> <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>"></p>
            <p><strong>Department:</strong> 
                <select name="department" required>
                    <option value="HR" <?php if ($user['department'] == 'HR') echo 'selected'; ?>>HR</option>
                    <option value="IT" <?php if ($user['department'] == 'IT') echo 'selected'; ?>>IT</option>
                    <option value="Finance" <?php if ($user['department'] == 'Finance') echo 'selected'; ?>>Finance</option>
                    <option value="Operations" <?php if ($user['department'] == 'Operations') echo 'selected'; ?>>Operations</option>
                </select>
            </p>
            <input type="submit" value="Save" class="btn">
            <a href="admin_profile.php" class="btn">Cancel</a>
        </form>
        <?php else: ?>
        <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($user['admin_id']); ?></p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
        <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department']); ?></p>
        <a href="admin_profile.php?edit=1" class="btn">Edit</a>
        <form action="admin_profile.php" method="post" style="display:inline;">
            <input type="hidden" name="remove_image" value="1">
            <input type="submit" value="Remove Image" class="btn remove">
        </form>
        <?php endif; ?>
    </div>
    <div class="upload-section">
        <form action="admin_profile.php" method="post" enctype="multipart/form-data">
            <label for="profile_image">Choose a profile picture</label>
            <input type="file" name="profile_image" id="profile_image" onchange="previewImage(event)">
            <input type="submit" value="Upload Image" name="submit">
        </form>
        <?php if (!empty($upload_message)): ?>
            <p><?php echo $upload_message; ?></p>
        <?php endif; ?>
    </div>
    <?php if (!empty($update_message)): ?>
        <p><?php echo $update_message; ?></p>
    <?php endif; ?>
</div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('profile-img-display');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
