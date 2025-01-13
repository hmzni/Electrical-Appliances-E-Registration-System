<?php
include 'db_connect.php';
include 'qr_codes/phpqrcode/qrlib.php';

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Fetch appliances for the student
    $sql = "SELECT a.name, a.image_url, a.price_rm, a.semester_type, sa.quantity 
            FROM student_appliances sa
            JOIN appliances a ON sa.appliance_id = a.id
            WHERE sa.student_id = '$student_id' AND sa.status = 'Pending'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $appliances = array();
        while ($row = $result->fetch_assoc()) {
            $appliances[] = $row;
        }

        // Generate QR Code content
        $qr_content = "Student ID: $student_id\n";
        foreach ($appliances as $appliance) {
            $qr_content .= "Semester: {$appliance['semester_type']}\n";
            $qr_content .= "Appliance: {$appliance['name']}\n";
            $qr_content .= "Price: RM {$appliance['price_rm']}\n";
            $qr_content .= "Quantity: {$appliance['quantity']}\n";
        }
        $qr_content .= "Status: Success\n";
        $qr_content .= "Generated on: " . date('Y-m-d H:i:s');

        // Ensure qr_codes directory exists
        $qr_codes_dir = 'qr_codes';
        if (!file_exists($qr_codes_dir)) {
            mkdir($qr_codes_dir, 0777, true);
        }

        // Generate QR Code image
        $qr_code_filename = $qr_codes_dir . '/' . $student_id . '_qr.png';
        QRcode::png($qr_content, $qr_code_filename);

        // Verify if QR code image was created
        if (file_exists($qr_code_filename)) {
            // Update the status to Success
            $sql_update = "UPDATE student_appliances SET status = 'Success' WHERE student_id = '$student_id' AND status = 'Pending'";
            $conn->query($sql_update);

            // Save the QR code path in payment_receipts
            $sql_receipt = "UPDATE payment_receipts SET qr_code_image = '$qr_code_filename', qr_code_status = 'Success', qr_code_generation = NOW() WHERE student_id = '$student_id'";
            $conn->query($sql_receipt);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to generate QR code image.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No pending appliances found for the student.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Student ID is required.']);
}

$conn->close();
?>