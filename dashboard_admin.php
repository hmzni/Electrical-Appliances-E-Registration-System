<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.html");
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
                        <p class="home__description">The system is retrieving the latest information <br> on student appliance registrations <br> including any newly registered or updated appliances.</p>
                        <a href="admin_monitoring.php" class="home__button">Monitor now !</a>
                    </div>
                </div>
            </section>
        </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.5.1/gsap.min.js"></script>
    <script src="main.js"></script>
</body>
</html>