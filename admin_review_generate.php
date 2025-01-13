<?php
session_start();
if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.html");
    exit();
}

include 'db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $semester_type = $_POST['semester_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    $insert_sql = "INSERT INTO registration_periods (semester_type, start_date, end_date, month, year)
                   VALUES ('$semester_type', '$start_date', '$end_date', '$month', '$year')";
    if ($conn->query($insert_sql) === TRUE) {
        header("Location: admin_review_generate.php");
        exit();
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM administrators WHERE admin_id='" . $_SESSION['user_id'] . "'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$reg_sql = "SELECT * FROM registration_periods ORDER BY id DESC";
$reg_result = $conn->query($reg_sql);
$registration_periods = [];
while ($row = $reg_result->fetch_assoc()) {
    $registration_periods[] = $row;
}

$current_period = $registration_periods[0] ?? null;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Review</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #FFC0CB;
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

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .students {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .student {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .student:hover {
            transform: scale(1.05);
        }

        .student img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 3px solid #FF69B4;
            object-fit: cover;
        }

        .student h3 {
            margin: 10px 0;
        }

        .student button {
            background-color: #FF69B4;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        .semester-filter, .status-filter, .registration-period-filter {
            margin-bottom: 20px;
            text-align: center;
        }

        .semester-filter select, .status-filter select, .registration-period-filter select {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #FF69B4;
            background-color: white;
            transition: border-color 0.3s ease-in-out;
        }

        .semester-filter select:hover, .status-filter select:hover, .registration-period-filter select:hover {
            border-color: #FF69B4;
        }

        .semester-filter select option, .status-filter select option, .registration-period-filter select option {
            background-color: #FFC0CB;
        }

        .registration-form {
            background-color: white;
            padding: 20px;
            margin: 20px auto 40px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            text-align: center;
        }

        .registration-form h2 {
            margin-bottom: 20px;
            color: #FF69B4;
        }

        .registration-form label {
            display: block;
            margin-bottom: 10px;
        }

        .registration-form input, .registration-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .registration-form button {
            background-color: #FF69B4;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

    </style>
</head>
<body>

<section id="header">
    <div id="user-info">
        <img src="<?php echo htmlspecialchars($user['profile_image'] ?? 'default.jpg'); ?>" alt="User Image" class="user-img">
        <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
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
    <h2 style="color: white; text-shadow: 2px 2px 4px #000;">#review and generate</h2>
</section>

<div class="container">
    <div class="registration-form">
        <h2>Set Registration Period</h2>
        <form method="POST" action="admin_review_generate.php">
            <label for="semester_type">Semester Type:</label>
            <select id="semester_type" name="semester_type" required>
                <option value="long">Long</option>
                <option value="short">Short</option>
            </select>

            <label for="start_date">Start Date:</label>
            <input type="datetime-local" id="start_date" name="start_date" required>

            <label for="end_date">End Date:</label>
            <input type="datetime-local" id="end_date" name="end_date" required>

            <label for="month">Month:</label>
            <input type="text" id="month" name="month" required>

            <label for="year">Year:</label>
            <input type="number" id="year" name="year" required>

            <button type="submit">Save</button>
        </form>
        <?php if ($current_period): ?>
        <h3>Current Registration Settings:</h3>
        <p>Registration date and time: <?php echo htmlspecialchars($current_period['start_date'] ?? '') . ' to ' . htmlspecialchars($current_period['end_date'] ?? ''); ?></p>
        <p>Semester: <?php echo htmlspecialchars($current_period['semester_type'] ?? ''); ?></p>
        <p>Semester Month and Year: <?php echo htmlspecialchars($current_period['month'] ?? '') . ' ' . htmlspecialchars($current_period['year'] ?? ''); ?></p>
        <?php endif; ?>
    </div>

    <h2>Admin Review</h2>
    <div class="registration-period-filter">
        <label for="registration-period-select">Filter by Registration Period: </label>
        <select id="registration-period-select" onchange="filterByRegistrationPeriod()">
            <option value="">All Periods</option>
            <?php foreach ($registration_periods as $period): ?>
                <option value="<?php echo $period['id']; ?>">
                    <?php echo htmlspecialchars($period['semester_type'] ?? '') . ' - ' . htmlspecialchars($period['month'] ?? '') . ' ' . htmlspecialchars($period['year'] ?? ''); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="status-filter">
        <label for="status-select">Filter by Status: </label>
        <select id="status-select" onchange="filterByRegistrationPeriod()">
            <option value="">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Success">Success</option>
        </select>
    </div>

    <div class="students" id="students-container">
        <?php
        include 'db_connect.php';

        $sql = "SELECT DISTINCT s.student_id, s.full_name, s.profile_image, pr.receipt_path, pr.qr_code_status, a.semester_type
                FROM students s
                JOIN payment_receipts pr ON s.student_id  = pr.student_id
                JOIN student_appliances sa ON s.student_id = sa.student_id
                JOIN appliances a ON sa.appliance_id = a.id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $profile_image = htmlspecialchars($row['profile_image'] ?? 'default.jpg');
                $full_name = htmlspecialchars($row['full_name'] ?? '');
                $student_id = htmlspecialchars($row['student_id'] ?? '');
                $receipt_path = htmlspecialchars($row['receipt_path'] ?? '');
                $qr_code_status = htmlspecialchars($row['qr_code_status'] ?? '');
                $semester_type = htmlspecialchars($row['semester_type'] ?? '');
                echo "<div class='student' data-semester='{$semester_type}' data-status='{$qr_code_status}'>
                        <img src='{$profile_image}' alt='Profile Image'>
                        <h3>{$full_name}</h3>
                        <p>{$student_id}</p>
                        <button onclick=\"window.open('{$receipt_path}')\">Review Payment</button>
                        <button onclick=\"fetchAppliances('{$student_id}')\">Check Appliance</button>
                        <div id='appliance-{$student_id}'></div>";
                if ($qr_code_status == 'Pending') {
                    echo "<button onclick=\"generateQRCode('{$student_id}')\">Generate QR Code</button>";
                }
                echo "</div>";
            }
        } else {
            echo "<p>No payment receipts to review.</p>";
        }

        $conn->close();
        ?>
    </div>
</div>

<script>
function filterByRegistrationPeriod() {
    const periodId = document.getElementById('registration-period-select').value;
    const status = document.getElementById('status-select').value;
    const studentsContainer = document.getElementById('students-container');
    studentsContainer.innerHTML = '<p>Loading...</p>';

    fetch(`fetch_students_by_period.php?period_id=${periodId}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            studentsContainer.innerHTML = '';
            if (data.length > 0) {
                data.forEach(student => {
                    studentsContainer.innerHTML += `
                        <div class="student" data-semester="${student.semester_type}" data-status="${student.qr_code_status}">
                            <img src="${student.profile_image}" alt="Profile Image">
                            <h3>${student.full_name}</h3>
                            <p>${student.student_id}</p>
                            <button onclick="window.open('${student.receipt_path}')">Review Payment</button>
                            <button onclick="fetchAppliances('${student.student_id}')">Check Appliance</button>
                            <div id="appliance-${student.student_id}"></div>
                            ${student.qr_code_status === 'Pending' ? '<button onclick="generateQRCode(\'' + student.student_id + '\')">Generate QR Code</button>' : ''}
                        </div>
                    `;
                });
            } else {
                studentsContainer.innerHTML = '<p>No students found for the selected period and status.</p>';
            }
        })
        .catch(error => {
            studentsContainer.innerHTML = '<p style="color: red;">Error fetching students: ' + error.message + '</p>';
            console.error('Error fetching students:', error);
        });
}

function fetchAppliances(studentId) {
    let applianceDiv = document.getElementById(`appliance-${studentId}`);
    applianceDiv.innerHTML = '<p>Loading...</p>';

    fetch(`fetch_student_appliances.php?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            applianceDiv.innerHTML = '<h4>Appliances:</h4>';
            if (data.length > 0) {
                data.forEach(appliance => {
                    applianceDiv.innerHTML += `
                        <p>${appliance.name} (Quantity: ${appliance.quantity})</p>
                        <img src="${appliance.image_url}" alt="${appliance.name}" style="width: 50px; height: auto;">
                    `;
                });
            } else {
                applianceDiv.innerHTML += '<p>No appliances found.</p>';
            }
        })
        .catch(error => {
            applianceDiv.innerHTML = '<p style="color: red;">Error fetching appliances: ' + error.message + '</p>';
            console.error('Error fetching appliances:', error);
        });
}

function generateQRCode(studentId) {
    fetch(`generate_qr_code.php?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('QR Code generated successfully');
                location.reload();
            } else {
                alert('Failed to generate QR Code');
            }
        })
        .catch(error => {
            alert('Error generating QR Code');
            console.error('Error generating QR Code:', error);
        });
}
</script>
</body>
</html>
