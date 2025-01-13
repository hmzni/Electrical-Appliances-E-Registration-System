<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: student_login.html");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

date_default_timezone_set('Asia/Kuala_Lumpur');

$current_date = new DateTime();
$registration_open = false;

$student_id = $_SESSION['user_id'];
$user_stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$user_stmt->bind_param("i", $student_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

$reg_stmt = $conn->prepare("SELECT * FROM registration_periods ORDER BY id DESC LIMIT 1");
$reg_stmt->execute();
$current_period = $reg_stmt->get_result()->fetch_assoc();

if ($current_period) {
    $start_date = new DateTime($current_period['start_date']);
    $end_date = new DateTime($current_period['end_date']);

    if ($current_date >= $start_date && $current_date <= $end_date) {
        $registration_open = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appliance_id = $_POST['appliance_id'];
    $quantity = $_POST['quantity'];
    $registration_period_id = $current_period['id'];

    // Check if the appliance already exists in the student's appliances with status 'Success'
    $sql_check = "SELECT * FROM student_appliances WHERE student_id=? AND appliance_id=? AND status='Success'";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $student_id, $appliance_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Prevent further registration
        echo "<script>alert('You cannot register for this appliance again as the previous registration was successful.');</script>";
    } else {
        // Check if the appliance already exists in the student's appliances
        $sql_check_pending = "SELECT * FROM student_appliances WHERE student_id=? AND appliance_id=? AND status!='Success'";
        $stmt_check_pending = $conn->prepare($sql_check_pending);
        $stmt_check_pending->bind_param("ii", $student_id, $appliance_id);
        $stmt_check_pending->execute();
        $result_check_pending = $stmt_check_pending->get_result();

        if ($result_check_pending->num_rows > 0) {
            // Update the quantity if the appliance exists
            $sql_update = "UPDATE student_appliances SET quantity=quantity + ? WHERE student_id=? AND appliance_id=?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("iii", $quantity, $student_id, $appliance_id);
            $stmt_update->execute();
        } else {
            // Insert a new row if the appliance does not exist
            $sql_insert = "INSERT INTO student_appliances (student_id, appliance_id, quantity, status, registration_period_id) VALUES (?, ?, ?, 'Pending', ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iiii", $student_id, $appliance_id, $quantity, $registration_period_id);
            $stmt_insert->execute();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .pro img {
            width: 100px;
            height: auto;
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

        .semester-select-container {
            margin-top: 50px;
            text-align: center;
        }

        .semester-select-container label {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            margin-right: 10px;
        }

        .semester-select-container select {
            padding: 10px;
            border-radius: 5px;
            border: 2px solid #fff;
            background-color: #FFC0CB;
            color: #1a1a1a;
            font-size: 16px;
            font-weight: 600;
            outline: none;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .semester-select-container select:hover {
            background-color: #FFB6C1;
            border-color: #FCA4A6;
        }

        .semester-select-container select:focus {
            border-color: #FF69B4;
        }

        .pro-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 90%;
            gap: 20px;
        }

        .pro {
            background-color: #FFDEE9;
            padding: 20px;
            margin: 0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            flex: 1 1 200px;
        }

        .pro:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .pro .des {
            margin-top: 10px;
        }

        .pro .des h3 {
            color: #1a1a1a;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .pro .des p {
            color: #1a1a1a;
            font-size: 14px;
            margin-bottom: 5px;
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
                <li id="lg-bag"><a href="cart.php"><i class="far fa-shopping-bag"></i></a></li>
                <a href="#" id="close"><i class="far fa-times"></i></a>
            </ul>
        </div>
        <div id="mobile">
            <a href="cart.php"><i class="far fa-shopping-bag"></i></a>
            <i id="bar" class="fas fa-outdent"></i>
        </div>
    </section>

    <section id="page-header" style="background-image: url('registrationheader.jpg');">
        <h2 style="color: white; text-shadow: 2px 2px 4px #000;">#rule adherence</h2>
        <p style="color: white; text-shadow: 2px 2px 4px #000;">"Discipline molds dreams, adherence to rules fuels aspirations."</p>
    </section>

    <?php if ($registration_open): ?>
    <section id="product1" class="section-p1">
        <div class="pro-container" id="pro-container">
            <!-- Appliance data will be inserted here dynamically -->
        </div>
    </section>
    <?php else: ?>
    <section id="expired" class="section-p1">
        <div class="container">
            <h2>The registration period has expired.</h2>
        </div>
    </section>
    <?php endif; ?>

    <script>
    <?php if ($registration_open): ?>
        function fetchAppliances() {
            fetch(`fetch_appliances_by_semester.php?semester=<?php echo $current_period['semester_type']; ?>`)
                .then(response => response.json())
                .then(data => {
                    let proContainer = document.getElementById('pro-container');
                    proContainer.innerHTML = '';
                    data.forEach(appliance => {
                        proContainer.innerHTML += `
                            <div class="pro">
                                <img src="${appliance.image_url}" alt="${appliance.name}" class="pro-img">
                                <div class="des">
                                    <h3>${appliance.name}</h3>
                                    <p>Price: RM ${appliance.price_rm}</p>
                                    <p>Semester: ${appliance.semester_type}</p>
                                </div>
                                <a href="appliance_detail.php?id=${appliance.id}"><i class="fal fa-shopping-cart cart"></i></a>
                            </div>
                        `;
                    });
                });
        }
        fetchAppliances();
    <?php endif; ?>
    </script>
</body>
</html>