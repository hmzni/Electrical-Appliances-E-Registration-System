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

// Fetch the latest registration period
$reg_sql = "SELECT * FROM registration_periods ORDER BY id DESC LIMIT 1";
$reg_result = $conn->query($reg_sql);
$registration_period = $reg_result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
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

        .dashboard-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }

        .dashboard-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .dashboard-container .profile-img {
            display: block;
            margin: 0 auto 20px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }

        .dashboard-container .profile-details {
            text-align: center;
            margin-bottom: 20px;
        }

        .dashboard-container .profile-details p {
            margin: 5px 0;
        }

        .banner {
            height: 50px;  
            overflow: hidden;
            position: relative;
            background: #000000;
        }

        .banner h3 {
            font-size: 1.5em; /* Adjust font size to fit the banner */
            color: transparent;
            -webkit-text-stroke:0.7px white;
            position: absolute;
            width: 100%;
            height: 100%;
            margin: 0;
            line-height: 50px;
            text-align: center;
            white-space: nowrap; /* Ensure the text does not wrap */
            /* Starting position */
            -moz-transform:translateX(100%);
            -webkit-transform:translateX(100%);    
            transform:translateX(100%);
            /* Apply animation to this element */  
            -moz-animation: example1 30s linear infinite;
            -webkit-animation: example1 30s linear infinite;
            animation: example1 30s linear infinite;
        }
        /* Move it (define the animation) */
        @-moz-keyframes example1 {
            0%   { -moz-transform: translateX(100%); }
            100% { -moz-transform: translateX(-100%); }
        }
        @-webkit-keyframes example1 {
            0%   { -webkit-transform: translateX(100%); }
            100% { -webkit-transform: translateX(-100%); }
        }
        @keyframes example1 {
            0%   { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
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
                <li id="lg-bag"><a href="cart.php"><i class="far fa-shopping-bag"></i></a></li>
                <a href="#" id="close"><i class="far fa-times"></i></a>
            </ul>
        </div>
        <div id="mobile">
            <a href="cart.php"><i class="far fa-shopping-bag"></i></a>
            <i id="bar" class="fas fa-outdent"></i>
        </div>
    </section>

    <div class="banner">
        <?php if ($registration_period): ?>
            <h3>// Registration Period: <?php echo htmlspecialchars($registration_period['start_date']); ?> to <?php echo htmlspecialchars($registration_period['end_date']); ?> //</h3>
        <?php else: ?>
            <h3>// No registration period set //</h3>
        <?php endif; ?>
    </div>

    <main class="l-main">
        <!--===== HOME =====-->
        <section class="home" id="home">
            <div class="home__container bd-grid">
                <div class="home__img">
                    <img src="img1.png" alt="" data-speed="-2" class="move">
                    <img src="img2.png" alt="" data-speed="2" class="move">
                    <img src="img3.png" alt="" data-speed="2" class="move">
                    <img src="img4.png" alt="" data-speed="-2" class="move">
                    <img src="img5.png" alt="" data-speed="-2" class="move">
                    <img src="img6.png" alt="" data-speed="2" class="move">
                </div>

                <div class="home__data">
                    <h1 class="home__title" style="font-size: 45px; text-align: left;">Electrical Appliances<br> E-Registration</h1>
                    <p class="home__description">Register your electrical appliances <br> for safe and efficient use in your hostel.</p>
                    <a href="registration.php" class="home__button">Register now !</a>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.5.1/gsap.min.js"></script>
    <script src="main.js"></script>
</body>
</html>
