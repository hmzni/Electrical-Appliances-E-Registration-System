<?php
session_start();
include 'db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['receipt'])) {
    $student_id = $_SESSION['user_id'];
    $target_dir = "receipts/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true); // Create directory if it does not exist
    }
    $target_file = $target_dir . basename($_FILES["receipt"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is a PDF
    if ($fileType != "pdf") {
        echo "Sorry, only PDF files are allowed.";
        $uploadOk = 0;
    }

    // Check file size if necessary
    if ($_FILES["receipt"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $target_file)) {
            // Save receipt data
            error_log("Saving receipt data...");
            $sql_receipt = "INSERT INTO payment_receipts (student_id, receipt_path) 
                            VALUES (?, ?)";
            $stmt_receipt = $conn->prepare($sql_receipt);
            if (!$stmt_receipt) {
                error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
                echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                exit;
            }
            $stmt_receipt->bind_param("ss", $student_id, $target_file);

            if ($stmt_receipt->execute()) {
                error_log("Receipt data saved successfully.");
                // Save appliance data
                $cart = json_decode($_POST['cart'], true);
                error_log("Cart data: " . print_r($cart, true)); // Log cart data
                foreach ($cart as $item) {
                    $appliance_id = $item['id'];
                    $quantity = $item['quantity'];
                    $sql_appliance = "INSERT INTO student_appliances (student_id, appliance_id, quantity)
                                      VALUES (?, ?, ?)";
                    $stmt_appliance = $conn->prepare($sql_appliance);
                    if (!$stmt_appliance) {
                        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
                        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                        exit;
                    }
                    $stmt_appliance->bind_param("iii", $student_id, $appliance_id, $quantity);

                    if (!$stmt_appliance->execute()) {
                        error_log("Error inserting appliance: " . $stmt_appliance->error);
                        echo "Error inserting appliance: " . $stmt_appliance->error;
                        exit;
                    }
                }
                echo "Receipt and appliance data uploaded successfully.";
            } else {
                error_log("Error inserting receipt: " . $stmt_receipt->error);
                echo "Error inserting receipt: " . $stmt_receipt->error;
            }
        } else {
            error_log("Sorry, there was an error uploading your file.");
            echo "Sorry, there was an error uploading your file.";
        }
    }

    if (isset($stmt_receipt)) {
        $stmt_receipt->close();
    }
    if (isset($stmt_appliance)) {
        $stmt_appliance->close();
    }
    $conn->close();
}
?>
