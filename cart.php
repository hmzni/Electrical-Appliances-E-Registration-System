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
    <title>Cart</title>

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

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            transition: background-color 0.3s;
        }
        table th {
            background-color: #f4f4f4;
            font-size: 16px;
            color: #555;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        table img {
            border-radius: 5px;
            width: 50px;
            height: auto;
        }
        .cart-totals {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-radius: 10px;
        }
        .cart-totals table {
            width: 100%;
            border: none;
        }
        .cart-totals td {
            padding: 10px 0;
        }
        .cart-totals h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .cart-totals button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .cart-totals button:hover {
            background-color: #218838;
        }
        .remove-button {
            background-color: transparent;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s;
        }
        .remove-button:hover {
            color: #c82333;
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

    <section id="cart" class="section-p1">
        <table width="100%">
            <thead>
                <tr>
                    <th>Remove</th>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include database connection
                include 'db_connect.php';

                // Fetch cart items for the logged-in student from the database
                $sql = "SELECT a.id, a.name, a.image_url, a.price_rm, sa.quantity
                        FROM appliances a
                        JOIN student_appliances sa ON a.id = sa.appliance_id
                        WHERE sa.student_id = '" . $_SESSION['user_id'] . "'";
                $result = $conn->query($sql);

                $totalAmount = 0;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $subtotal = $row['price_rm'] * $row['quantity'];
                        $totalAmount += $subtotal;
                        echo "
                            <tr>
                                <td><button class='remove-button' onclick='removeFromCart({$row['id']})'>Remove</button></td>
                                <td><img src='{$row['image_url']}' alt='{$row['name']}' style='width: 50px; height: auto;'></td>
                                <td>{$row['name']}</td>
                                <td>RM {$row['price_rm']}</td>
                                <td>{$row['quantity']}</td>
                                <td>RM " . number_format($subtotal, 2) . "</td>
                            </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='6'>Your cart is empty.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </section>

    <section id="cart-add" class="section-p1">
        <div class="cart-totals">
            <h3>Cart Totals</h3>
            <table>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td>RM <span id="total-amount"><strong><?php echo number_format($totalAmount, 2); ?></strong></span></td>
                </tr>
            </table>
            <button style='background-color: #FF4D6A;' onclick="proceedToPayment()">Proceed to Payment</button>
        </div>
    </section>

    <script>
        function removeFromCart(applianceId) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ appliance_id: applianceId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to remove item from cart.');
                }
            });
        }

        function proceedToPayment() {
            window.location.href = 'payment.php';
        }
    </script>
</body>
</html> 