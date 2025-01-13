<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: student_login.html");
    exit();
}

include 'db_connect.php';

$student_id = $_SESSION['user_id'];

// Ambil detail pelajar dari database
$sql = "SELECT * FROM students WHERE student_id='$student_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Ambil peralatan dan status untuk pelajar
$sql = "SELECT a.name, a.image_url, a.price_rm, a.semester_type, sa.quantity, sa.status, pr.qr_code_status, pr.qr_code_image, pr.qr_code_generation
        FROM student_appliances sa
        JOIN appliances a ON sa.appliance_id = a.id
        JOIN payment_receipts pr ON sa.student_id = pr.student_id
        WHERE sa.student_id = '$student_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticker</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
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
        
        table {
            width: 90%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        table th {
            background-color: #FF69B4;
            color: #fff;
            font-weight: bold;
        }
        
        table td {
            font-size: 14px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #FFD9E3;
        }
        
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
        
        .print-btn {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #FF69B4;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        
        .print-btn:hover {
            background-color: #FF1493;
        }
        
        .centered-heading {
            text-align: center;
            margin-top: 20px;
        }

        body {
            background-color: #FCA4A6;
        }

        #navbar #lg-bag a i {
            color: #1a1a1a;
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

        @keyframes fade {
            0% { opacity: 0; }
            50% { opacity: 1; }
            100% { opacity: 0; }
        }

        #newsletter {
            text-align: center;
            padding: 20px;
            background-color: #FFD9E3;
            margin: 20px 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .newstext h4 {
            font-size: 24px;
            color: #FF69B4;
            margin-bottom: 10px;
        }

        #quote-container {
            position: relative;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 10px;
        }

        #quote {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            text-align: center;
            animation: fade 10s infinite;
            margin: 0;
        }

        .form {
            text-align: center;
            margin: 20px 0;
        }

        .form textarea {
            width: 80%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: none;
            font-size: 16px;
        }

        .form button.normal {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #FF69B4;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .form button.normal:hover {
            background-color: #FF1493;
        }

        .newstext {
            text-align: center;
            margin-top: 20px;
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
        <h2 style="color: white; text-shadow: 2px 2px 4px #000;">#validity</h2>
        <p style="color: white; text-shadow: 2px 2px 4px #000;">"Unlocking compliance: where QR code empowers students to light up their dorm lives responsibly."</p>
    </section>

    <div class="container">
        <h2 class="centered-heading">Your Appliances</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Appliance Name</th>
                        <th>Image</th>
                        <th>Price (RM)</th>
                        <th>Semester</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Appliance Image" width="50"></td>
                            <td><?php echo htmlspecialchars($row['price_rm']); ?></td>
                            <td><?php echo htmlspecialchars($row['semester_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                        <?php $last_row = $row; // Save the last row to check the status later ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php if (isset($last_row) && $last_row['qr_code_status'] == 'Success'): ?>
                <div class="qr-code">
                    <img src="<?php echo htmlspecialchars($last_row['qr_code_image']); ?>" alt="QR Code" width="200">
                    <p>Generated on: <?php echo htmlspecialchars($last_row['qr_code_generation']); ?></p>
                    <button class="print-btn" onclick="printQRCode('<?php echo htmlspecialchars($last_row['qr_code_image']); ?>')">Print</button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>No appliances registered.</p>
        <?php endif; ?>
    </div>

    <section id="newsletter" class="section-p1 section-m1">
        <div class="newstext">
            <h4>Stay Inspired</h4>
            <div id="quote-container">
                <p id="quote">"Success is not final, failure is not fatal: It is the courage to continue that counts." – Winston Churchill</p>
            </div>
        </div>
    </section>

    <footer class="section-p1">
        <div class="col">
            <img class="logo" src="img/logo.png" alt="">
            <h4>Contact</h4>
            <p><strong>Address: </strong> 562 Wellington Road, Street 32, San Francisco</p>
            <p><strong>Phone:</strong> +01 2222 365 /(+91) 01 2345 6789</p>
            <p><strong>Hours:</strong> 10:00 - 18:00, Mon - Sat</p>
            <div class="follow">
                <h4>Follow Us</h4>
                <div class="icon">
                    <i class="fab fa-facebook-f"></i>
                    <i class="fab fa-twitter"></i>
                    <i class="fab fa-instagram"></i>
                    <i class="fab fa-pinterest-p"></i>
                    <i class="fab fa-youtube"></i>
                </div>
            </div>
        </div>

        <div class="col">
            <h4>About</h4>
            <a href="#">About Us</a>
            <a href="#">Delivery Information</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
            <a href="#">Contact Us</a>
        </div>

        <div class="col">
            <h4>My Account</h4>
            <a href="#">Sign In</a>
            <a href="#">View Cart</a>
            <a href="#">My Wishlist</a>
            <a href="#">Track My Order</a>
            <a href="#">Help</a>
        </div>

        <div class="col install">
            <h4>Install App</h4>
            <p>From App Store or Google Play</p>
            <div class="row">
                <img src="img/pay/app.jpg" alt="">
                <img src="img/pay/play.jpg" alt="">
            </div>
            <p>Secured Payment Gateways </p>
            <img src="img/pay/pay.png" alt="">
        </div>

        <div class="copyright">
            <p>© 2021, Tech2 etc - HTML CSS Ecommerce Template</p>
        </div>
    </footer>

    <script>
        function printQRCode(qrCodeImage) {
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print QR Code</title></head><body>');
            printWindow.document.write('<img src="' + qrCodeImage + '" alt="QR Code" style="width: 200px;">');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        const quotes = [
            '"The only way to do great work is to love what you do." – Steve Jobs',
            '"Success is not the key to happiness. Happiness is the key to success." – Albert Schweitzer',
            '"Don’t watch the clock; do what it does. Keep going." – Sam Levenson',
            '"Success is not final, failure is not fatal: It is the courage to continue that counts." – Winston Churchill',
            '"Believe you can and you’re halfway there." – Theodore Roosevelt',
            '"It always seems impossible until it’s done." – Nelson Mandela',
            '"Your time is limited, so don’t waste it living someone else’s life." – Steve Jobs',
            '"The future belongs to those who believe in the beauty of their dreams." – Eleanor Roosevelt'
        ];

        let quoteIndex = 0;

        function changeQuote() {
            const quoteElement = document.getElementById('quote');
            quoteElement.innerHTML = quotes[quoteIndex];
            quoteIndex = (quoteIndex + 1) % quotes.length;
        }

        setInterval(changeQuote, 10000);

        const stories = [];

        function submitStory() {
            const storyInput = document.getElementById('story');
            const story = storyInput.value.trim();

            if (story) {
                stories.push(story);
                displayStories();
                storyInput.value = '';
            }
        }

        function displayStories() {
            const storiesContainer = document.getElementById('submitted-stories');
            storiesContainer.innerHTML = '';

            stories.forEach((story, index) => {
                const storyElement = document.createElement('div');
                storyElement.className = 'story';
                storyElement.innerHTML = '<p>' + story + '</p>';
                storiesContainer.appendChild(storyElement);
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>