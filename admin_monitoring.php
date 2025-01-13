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

// Fetch data for charts

// 1. Number of students enrolled in each program, faculty, and course
$program_sql = "SELECT program, COUNT(*) as count FROM students GROUP BY program";
$faculty_sql = "SELECT faculty, COUNT(*) as count FROM students GROUP BY faculty";
$course_sql = "SELECT course, COUNT(*) as count FROM students GROUP BY course";
$program_result = $conn->query($program_sql);
$faculty_result = $conn->query($faculty_sql);
$course_result = $conn->query($course_sql);

$program_data = [];
while ($row = $program_result->fetch_assoc()) {
    $program_data[] = $row;
}
$faculty_data = [];
while ($row = $faculty_result->fetch_assoc()) {
    $faculty_data[] = $row;
}
$course_data = [];
while ($row = $course_result->fetch_assoc()) {
    $course_data[] = $row;
}

// 2. The number of registered male and female students (assuming gender column exists in students table)
$gender_sql = "SELECT gender, COUNT(*) as count FROM students GROUP BY gender";
$gender_result = $conn->query($gender_sql);
$gender_data = [];
while ($row = $gender_result->fetch_assoc()) {
    $gender_data[] = $row;
}

// 3. What appliances are registered the most to the least
$appliance_sql = "SELECT a.name, COUNT(sa.appliance_id) as count FROM student_appliances sa JOIN appliances a ON sa.appliance_id = a.id GROUP BY sa.appliance_id ORDER BY count DESC";
$appliance_result = $conn->query($appliance_sql);
$appliance_data = [];
while ($row = $appliance_result->fetch_assoc()) {
    $appliance_data[] = $row;
}

// 4. The number of male and female students is the most to the least according to the device
$appliance_gender_sql = "SELECT a.name, s.gender, COUNT(sa.appliance_id) as count FROM student_appliances sa JOIN appliances a ON sa.appliance_id = a.id JOIN students s ON sa.student_id = s.student_id GROUP BY sa.appliance_id, s.gender ORDER BY count DESC";
$appliance_gender_result = $conn->query($appliance_gender_sql);
$appliance_gender_data = [];
while ($row = $appliance_gender_result->fetch_assoc()) {
    $appliance_gender_data[] = $row;
}

// 5. Number of appliances registered per semester type (long semester vs. short semester)
$semester_sql = "SELECT semester_type, COUNT(*) as count FROM appliances GROUP BY semester_type";
$semester_result = $conn->query($semester_sql);
$semester_data = [];
while ($row = $semester_result->fetch_assoc()) {
    $semester_data[] = $row;
}

// 6. How much money is accumulated per semester
$money_sql = "SELECT semester_type, SUM(price_rm) as total FROM student_appliances sa JOIN appliances a ON sa.appliance_id = a.id GROUP BY semester_type";
$money_result = $conn->query($money_sql);
$money_data = [];
while ($row = $money_result->fetch_assoc()) {
    $money_data[] = $row;
}

// 7. Status of payments for registered appliances (pending, success)
$status_sql = "SELECT qr_code_status, COUNT(*) as count FROM payment_receipts GROUP BY qr_code_status";
$status_result = $conn->query($status_sql);
$status_data = [];
while ($row = $status_result->fetch_assoc()) {
    $status_data[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #FFC0CB; /* Pink background color */
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

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .chart-container {
            width: 80%;
            height: 400px;
            margin: 50px auto;
            border: 2px solid #FF4D6A; 
            padding: 10px; 
            border-radius: 10px;
        }
        .print-btn {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            display: block;
            margin: 20px auto;
        }
        .print-btn:hover {
            background-color: #218838;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <h2 style="color: white; text-shadow: 2px 2px 4px #000;">#monitoring dashboard</h2>
    <p style="color: white; text-shadow: 2px 2px 4px #000;">Overview of Electrical Appliance Registration</p>
</section>

<div class="container">
    <div class="chart-container">
        <canvas id="programChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="facultyChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="courseChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="genderChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="applianceChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="applianceGenderChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="semesterChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="moneyChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="statusChart"></canvas>
    </div>

    <button class="print-btn" style='background-color: #FF4D6A;' onclick="window.print()">Print</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart data
        const programData = <?php echo json_encode($program_data); ?>;
        const facultyData = <?php echo json_encode($faculty_data); ?>;
        const courseData = <?php echo json_encode($course_data); ?>;
        const genderData = <?php echo json_encode($gender_data); ?>;
        const applianceData = <?php echo json_encode($appliance_data); ?>;
        const applianceGenderData = <?php echo json_encode($appliance_gender_data); ?>;
        const semesterData = <?php echo json_encode($semester_data); ?>;
        const moneyData = <?php echo json_encode($money_data); ?>;
        const statusData = <?php echo json_encode($status_data); ?>;

        // Function to create chart
        function createChart(ctx, type, data, options) {
            return new Chart(ctx, {
                type: type,
                data: data,
                options: options
            });
        }

        // Prepare data for charts
        const programChartData = {
            labels: programData.map(item => item.program),
            datasets: [{
                label: 'Number of Students',
                data: programData.map(item => item.count),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        const facultyChartData = {
            labels: facultyData.map(item => item.faculty),
            datasets: [{
                label: 'Number of Students',
                data: facultyData.map(item => item.count),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        const courseChartData = {
            labels: courseData.map(item => item.course),
            datasets: [{
                label: 'Number of Students',
                data: courseData.map(item => item.count),
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        };

        const genderChartData = {
            labels: genderData.map(item => item.gender),
            datasets: [{
                label: 'Number of Students',
                data: genderData.map(item => item.count),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        };

        const applianceChartData = {
            labels: applianceData.map(item => item.name),
            datasets: [{
                label: 'Number of Appliances',
                data: applianceData.map(item => item.count),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };

        const applianceGenderChartData = {
            labels: applianceGenderData.map(item => item.name + ' (' + item.gender + ')'),
            datasets: [{
                label: 'Number of Appliances',
                data: applianceGenderData.map(item => item.count),
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        };

        const semesterChartData = {
            labels: semesterData.map(item => item.semester_type),
            datasets: [{
                label: 'Number of Appliances',
                data: semesterData.map(item => item.count),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        const moneyChartData = {
            labels: moneyData.map(item => item.semester_type),
            datasets: [{
                label: 'Total Money (RM)',
                data: moneyData.map(item => item.total),
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        };

        const statusChartData = {
            labels: statusData.map(item => item.qr_code_status),
            datasets: [{
                label: 'Number of Payments',
                data: statusData.map(item => item.count),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        };

        // Chart options with titles
        const chartOptions = (title) => ({
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                }
            }
        });

        // Create charts
        const programChartCtx = document.getElementById('programChart').getContext('2d');
        createChart(programChartCtx, 'bar', programChartData, chartOptions('Number of Students Enrolled in Each Program'));

        const facultyChartCtx = document.getElementById('facultyChart').getContext('2d');
        createChart(facultyChartCtx, 'bar', facultyChartData, chartOptions('Number of Students Enrolled in Each Faculty'));

        const courseChartCtx = document.getElementById('courseChart').getContext('2d');
        createChart(courseChartCtx, 'bar', courseChartData, chartOptions('Number of Students Enrolled in Each Course'));

        const genderChartCtx = document.getElementById('genderChart').getContext('2d');
        createChart(genderChartCtx, 'doughnut', genderChartData, chartOptions('Number of Registered Male and Female Students'));

        const applianceChartCtx = document.getElementById('applianceChart').getContext('2d');
        createChart(applianceChartCtx, 'bar', applianceChartData, chartOptions('Appliances Registered from Most to Least'));

        const applianceGenderChartCtx = document.getElementById('applianceGenderChart').getContext('2d');
        createChart(applianceGenderChartCtx, 'bar', applianceGenderChartData, chartOptions('Number of Male and Female Students per Appliance'));

        const semesterChartCtx = document.getElementById('semesterChart').getContext('2d');
        createChart(semesterChartCtx, 'bar', semesterChartData, chartOptions('Number of Appliances Registered per Semester Type'));

        const moneyChartCtx = document.getElementById('moneyChart').getContext('2d');
        createChart(moneyChartCtx, 'bar', moneyChartData, chartOptions('Total Money Accumulated per Semester'));

        const statusChartCtx = document.getElementById('statusChart').getContext('2d');
        createChart(statusChartCtx, 'pie', statusChartData, chartOptions('Status of Payments for Registered Appliances'));
    });
</script>
</body>
</html>
