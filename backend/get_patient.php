<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Function to return a JSON response
function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Check if HN is set
if (isset($_GET['hn'])) {
    $hn = trim($_GET['hn']); // Trim whitespace

    // Input validation (example: check if HN is numeric)
    if (!preg_match('/^\d+$/', $hn)) {
        jsonResponse(["error" => "HN must be numeric"]);
    }

    // Prepare SQL statement
    $sql = "SELECT HN, Prefix, name, lastname FROM data WHERE HN = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        jsonResponse(["error" => "Error in SQL preparation: " . htmlspecialchars($conn->error)]);
    }
    
    $stmt->bind_param("s", $hn); // Bind HN

    // Execute SQL statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        // Check if data was found
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            jsonResponse($row); // Return patient data in JSON
        } else {
            jsonResponse(["error" => "ไม่พบข้อมูล"]);
        }
    } else {
        jsonResponse(["error" => "เกิดข้อผิดพลาดในการค้นหาข้อมูล"]);
    }

    $stmt->close();
} else {
    jsonResponse(["error" => "กรุณาส่งค่า HN"]);
}

$conn->close();
?>
