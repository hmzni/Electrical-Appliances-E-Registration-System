<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: student_login.html");
    exit();
}

// Include database connection
include 'db_connect.php';

// Fetch user details from database
$sql = "SELECT * FROM students WHERE student_id='" . $_SESSION['user_id'] . "'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="dashboard.css">
    <style>

                body {
            background-color: #FCA4A6; /* Change background color */
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .container img {
            width: 200px;
            height: auto;
        }
        .container input[type="file"] {
            margin: 20px 0;
        }
        .container button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .container .warning {
            color: red;
            margin-bottom: 20px;
        }
        .container .success {
            color: green;
            margin-bottom: 20px;
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

                #navbar #lg-bag a i {
        color: #1a1a1a; /* Adjust the color for the shopping bag icon */
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
                    <a href="student_profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
        </div>
        </div>  
        <div>
            <ul id="navbar">
                <li><a href="dashboard_student.php">Home</a></li>
                <li><a href="registration.php">Registration</a></li>
                <li><a href="sticker.php">Sticker</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li id="lg-bag"><a class="active" href="cart.php"><i class="far fa-shopping-bag"></i></a></li>
                <a id="close" href="#"><i class="far fa-times"></i></a>
            </ul>
        </div>
        <div id="mobile">
            <a href="cart.php"><i class="far fa-shopping-bag"></i></a>
            <i id="bar" class="fas fa-outdent"></i>
        </div>
    </section>

    <section id="page-header" style="background-image: url('registrationheader.jpg');">
        <h2 style="color: white; text-shadow: 2px 2px 4px #000;">#cart</h2>
        <p style="color: white; text-shadow: 2px 2px 4px #000;">Please confirm the item before doing the next process</p>
    </section>


<div class="container">
    <h2>QR Code Payment</h2>
    <img src="qr_code.PNG" alt="QR Code">
    <p class="warning" id="warning">Please make sure to upload your payment receipt before making the payment.</p>
    <form id="payment-form" action="upload_receipt.php" method="post" enctype="multipart/form-data">
        <input type="file" name="receipt" accept=".pdf" required>
        <button style='background-color: #FF4D6A;' type="submit">Submit Appliance and Receipt Verification</button>
    </form>
    <p id="success-message" class="success" style="display:none;">Receipt submission was successful. The review will take at least 3 working days.</p>
</div>

<script>
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        // Validate file type on the client side
        var fileInput = document.querySelector('input[type="file"]');
        var file = fileInput.files[0];
        if (file.type !== 'application/pdf') {
            alert('Only PDF files are allowed.');
            return;
        }

        // Add cart data to the form data
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        console.log(cart); // Log the cart data for debugging
        formData.append('cart', JSON.stringify(cart));

        fetch('upload_receipt.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // Log the response for debugging
            document.getElementById('warning').style.display = 'none';
            document.getElementById('success-message').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>
</body>
</html>

<?php
$conn->close();
?>