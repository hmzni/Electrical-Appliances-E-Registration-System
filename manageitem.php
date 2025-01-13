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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Electrical Appliances</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #FFC0CB; /* Pink background color */
        }

        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            border: 1px solid #FF69B4;
            box-shadow: 0 0 10px rgba(255, 105, 180, 0.5); /* Pink shadow */
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        form label, form input, form textarea, form select, form button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        form input, form textarea, form select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #FF69B4;
        }

        form button {
            background-color: #FF69B4;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #FF1493;
        }

        #applianceList {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .appliance {
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #FF69B4;
            text-align: center;
            box-shadow: 0 0 10px rgba(255, 105, 180, 0.5);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .appliance:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255, 105, 180, 0.7);
        }

        .appliance img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

        .appliance button {
            background-color: #FF69B4;
            color: white;
            padding: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
            transition: background-color 0.3s;
        }

        .appliance button:hover {
            background-color: #FF1493;
        }

        #filters {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        #filters label {
            margin-right: 10px;
            font-weight: bold;
            color: #FF69B4;
        }

        #semesterFilter {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #FF69B4;
            background-color: #FFB6C1; /* Light pink background */
            color: #FF1493; /* Dark pink text */
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }

        #semesterFilter:hover {
            background-color: #FF69B4; /* Darker pink background */
            color: white; /* White text */
        }

        #editForm {
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #FF69B4;
            margin-top: 10px;
            display: none;
            animation: slideDown 0.5s ease-in-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        #editForm label, #editForm input, #editForm textarea, #editForm select, #editForm button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        #editForm input, #editForm textarea, #editForm select {
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #FF69B4;
        }

        #editForm button {
            background-color: #FF69B4;
            color: white;
            padding: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #editForm button:hover {
            background-color: #FF1493;
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
    </style>
</head>
<body>
    <section id="header">
        <div id="user-info">
            <img src="<?php echo isset($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'default.jpg'; ?>" alt="User Image" class="user-img">
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
        <h2 style="color: white; text-shadow: 2px 2px 4px #000;">#manage electrical appliances</h2>
        <p style="color: white; text-shadow: 2px 2px 4px #000;">Add, edit, or remove electrical appliances</p>
    </section>

    <form id="applianceForm" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea>
        
        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>
        
        <label for="price">Price (RM):</label>
        <input type="number" step="0.01" id="price" name="price" required>
        
        <label for="semester">Semester:</label>
        <select id="semester" name="semester">
            <option value="short">Short</option>
            <option value="long">Long</option>
        </select>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>
        
        <button type="button" onclick="addAppliance()">Add Appliance</button>
    </form>

    <h2 style="margin-top: 50px; text-align: center;">Existing Appliances</h2>
    <div id="filters">
        <label for="semesterFilter">Filter by Semester:</label>
        <select id="semesterFilter" onchange="filterBySemester()">
            <option value="all">All</option>
            <option value="short">Short</option>
            <option value="long">Long</option>
        </select>
    </div>
    <div id="applianceList"></div>

    <script>
        function fetchAppliances() {
            let semesterFilter = document.getElementById('semesterFilter').value;
            fetch('fetch_appliances.php')
                .then(response => response.json())
                .then(data => {
                    let applianceList = document.getElementById('applianceList');
                    applianceList.innerHTML = '';
                    data.forEach(appliance => {
                        if (semesterFilter === 'all' || appliance.semester_type === semesterFilter) {
                            applianceList.innerHTML += 
                                `<div id="appliance-${appliance.id}" class="appliance">
                                    <h3>${appliance.name}</h3>
                                    <img src="${appliance.image_url}" alt="${appliance.name}" style="width:100px;height:100px;"><br>
                                    <p>${appliance.description}</p>
                                    <p>Price: RM ${appliance.price_rm}</p>
                                    <p>Semester: ${appliance.semester_type}</p>
                                    <p>Quantity: ${appliance.quantity}</p>
                                    <button onclick="removeAppliance(${appliance.id})">Remove</button>
                                    <button onclick="toggleEditForm(${appliance.id})">Edit</button>
                                    <div id="editForm-${appliance.id}" style="padding: 10px; background-color: #f9f9f9; border: 1px solid #FF69B4; margin-top: 10px; display: none;">
                                        <label for="editName-${appliance.id}">Name:</label>
                                        <input type="text" id="editName-${appliance.id}" value="${appliance.name}" style="margin-bottom: 10px; width: 100%; padding: 5px; border: 1px solid #FF69B4; border-radius: 3px;">
                                        <label for="editDescription-${appliance.id}">Description:</label>
                                        <textarea id="editDescription-${appliance.id}" style="margin-bottom: 10px; width: 100%; padding: 5px; border: 1px solid #FF69B4; border-radius: 3px;">${appliance.description}</textarea>
                                        <label for="editPrice-${appliance.id}">Price (RM):</label>
                                        <input type="number" step="0.01" id="editPrice-${appliance.id}" value="${appliance.price_rm}" style="margin-bottom: 10px; width: 100%; padding: 5px; border: 1px solid #FF69B4; border-radius: 3px;">
                                        <label for="editSemester-${appliance.id}">Semester:</label>
                                        <select id="editSemester-${appliance.id}" style="margin-bottom: 10px; width: 100%; padding: 5px; border: 1px solid #FF69B4; border-radius: 3px;">
                                            <option value="short" ${appliance.semester_type === 'short' ? 'selected' : ''}>Short</option>
                                            <option value="long" ${appliance.semester_type === 'long' ? 'selected' : ''}>Long</option>
                                        </select>
                                        <label for="editQuantity-${appliance.id}">Quantity:</label>
                                        <input type="number" id="editQuantity-${appliance.id}" value="${appliance.quantity}" style="margin-bottom: 10px; width: 100%; padding: 5px; border: 1px solid #FF69B4; border-radius: 3px;">
                                        <button onclick="updateAppliance(${appliance.id})" style="background-color: #FF69B4; color: white; padding: 5px; border: none; border-radius: 3px; cursor: pointer;">Update</button>
                                    </div>
                                </div>`;
                        }
                    });
                });
        }

        function addAppliance() {
            let form = document.getElementById('applianceForm');
            let formData = new FormData(form);
            fetch('add_appliance.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text())
              .then(result => {
                  alert(result);
                  fetchAppliances();
              });
        }

        function removeAppliance(id) {
            fetch('remove_appliance.php?id=' + id)
                .then(response => response.text())
                .then(result => {
                    alert(result);
                    fetchAppliances();
                });
        }

        function toggleEditForm(id) {
            let editForm = document.getElementById('editForm-' + id);
            editForm.style.display = (editForm.style.display === 'none') ? 'block' : 'none';
        }

        function updateAppliance(id) {
            let formData = new FormData();
            formData.append('id', id);
            formData.append('name', document.getElementById('editName-' + id).value);
            formData.append('description', document.getElementById('editDescription-' + id).value);
            formData.append('price', document.getElementById('editPrice-' + id).value);
            formData.append('semester', document.getElementById('editSemester-' + id).value);
            formData.append('quantity', document.getElementById('editQuantity-' + id).value);

            fetch('edit_appliance.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text())
              .then(result => {
                  alert(result);
                  fetchAppliances();
              });
        }

        function filterBySemester() {
            fetchAppliances();
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchAppliances();
        });
    </script>
</body>
</html>
