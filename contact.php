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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />

    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            background-color: #FCA4A6;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
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

        .people {
            text-align: center;
            margin: 20px 0;
        }

        .people img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 10px;
            object-fit: cover;
            border: 2px solid #FF6080;
        }

        .people p {
            font-size: 16px;
            font-weight: 600;
            color: white;
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

        <h2 style="color: white; text-shadow: 2px 2px 4px #000;">#let's_talk</h2>
        <p style="color: white; text-shadow: 2px 2px 4px #000;">LEAVE A MESSAGE, We love to hear from you!</p>

    </section>

    <section id="contact-details" class="section-p1">
        <div class="details">
            <h2>GET IN TOUCH</h2>
            <h3>Head Office</h3>
            <div>
                <li>
                    <i class="fal fa-map"></i>
                    <p>Jalan Timur Tambahan, 45600 Bestari Jaya,
Selangor Darul Ehsan, Malaysia</p>
                </li>
                <li>
                    <i class="far fa-envelope"></i>
                    <p>residenbj@unisel.edu.my </p>
                </li>
                <li>
                    <i class="fas fa-phone-alt"></i>
                    <p>03-3280 5158 </p>
                </li>
                <li>
                    <i class="far fa-clock"></i>
                    <p>Monday to Friday: 9.00am to 5.00pm </p>
                </li>
            </div>
        </div>

        <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3182.5561632047595!2d101.4401915!3d3.4204295!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc670547128ebb%3A0xe2e529e1140896e7!2sResident%20Office%20Unisel!5e0!3m2!1sen!2smy!4v1637232208485!5m2!1sen!2smy"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </section>

    <section class="people">
        <div>
            <img src="jani.jpg" alt="Jani">
            <p><span>Muhammad Hamzani bin Khairul</span><br>Student Bachelor in Information Technology<br>University of Selangor<br>Phone: +6011-11392 597<br>Email: captainhamzani@gmail.com</p>
        </div>
    </section>

    <script src="script.js"></script>

</body>

</html>
