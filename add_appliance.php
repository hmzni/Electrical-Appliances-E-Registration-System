<?php
include 'db_connect.php';

$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$semester = $_POST['semester'];

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["image"]["name"]);

// Check if the directory exists, if not, create it
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    $image_url = $target_file;

    $sql = "INSERT INTO appliances (name, description, image_url, price_rm, semester_type)
            VALUES ('$name', '$description', '$image_url', '$price', '$semester')";

    if ($conn->query($sql) === TRUE) {
        echo "New appliance added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error uploading the file.";
}

$conn->close();
?>
