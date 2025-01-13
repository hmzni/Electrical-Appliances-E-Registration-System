<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: student_login.html");
    exit();
}

include 'db_connect.php';

$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appliance Details</title>
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

        #appliance-details {
            max-width: 600px; /* Make the section smaller */
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        #appliance-details img {
            width: 60%;
            height: auto;
            border-radius: 10px;
        }

        #appliance-details h3 {
            font-size: 24px;
            color: #FF6080;
            margin-top: 20px;
        }

        #appliance-details p {
            font-size: 18px;
            color: #333;
        }

        #quantity {
            width: 60px;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        #add-to-cart {
            background-color: #FF6080;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #add-to-cart:hover {
            background-color: #FF4D6A;
        }

        #warning-message {
            color: red;
            display: none;
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
        <i id="bar" class="fas fa-outdent"></i></div>
</section>

<section id="page-header" style="background-image: url('registrationheader.jpg');">
    <h2 style="color: white; text-shadow: 2px 2px 4px #000;">#appliance details</h2>
</section>

<div id="appliance-details">
    <img id="appliance-img" src="" alt="">
    <h3 id="appliance-name"></h3>
    <p id="appliance-price"></p>
    <p id="appliance-description"></p>
    <label for="quantity">Quantity:</label>
    <input type="number" id="quantity" name="quantity" min="1" required>
    <button id="add-to-cart">Add To Cart</button>
    <p id="warning-message"></p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const applianceId = urlParams.get('id');

    fetch('get_appliance_details.php?id=' + applianceId)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('warning-message').innerText = data.error;
                document.getElementById('warning-message').style.display = 'block';
            } else {
                document.getElementById('appliance-img').src = data.image_url;
                document.getElementById('appliance-name').innerText = data.name;
                document.getElementById('appliance-price').innerText = ' RM ' + data.price_rm;
                document.getElementById('appliance-description').innerText = data.description;

                if (data.semester_conflict) {
                    document.getElementById('warning-message').innerText = 'You can only add appliances for the same semester type.';
                    document.getElementById('warning-message').style.display = 'block';
                }

                document.getElementById('add-to-cart').addEventListener('click', function() {
                    const quantity = document.getElementById('quantity').value;

                    if (!quantity) {
                        document.getElementById('warning-message').innerText = 'Please select the quantity first.';
                        document.getElementById('warning-message').style.display = 'block';
                    } else if (quantity > data.quantity) {
                        document.getElementById('warning-message').innerText = `The appliance is only allowed to be brought home ${data.quantity} time(s).`;
                        document.getElementById('warning-message').style.display = 'block';
                    } else if (data.semester_conflict) {
                        document.getElementById('warning-message').innerText = 'You can only add appliances for the same semester type.';
                        document.getElementById('warning-message').style.display = 'block';
                    } else {
                        // Send cart data to the server
                        fetch('add_to_cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                appliance_id: data.id,
                                quantity: quantity,
                                semester: data.semester_type
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                window.location.href = 'cart.php';
                            } else {
                                document.getElementById('warning-message').innerText = result.message;
                                document.getElementById('warning-message').style.display = 'block';
                            }
                        });
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching appliance details:', error);
            document.getElementById('warning-message').innerText = 'Error fetching appliance details.';
            document.getElementById('warning-message').style.display = 'block';
        });
});
</script>
</body>
</html>
