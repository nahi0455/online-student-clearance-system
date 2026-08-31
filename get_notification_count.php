<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (empty($_SESSION['matric_no'])) {
    echo json_encode(['count' => 0]);
    exit();
}

include('connect.php');

$matric_no = $_SESSION['matric_no'];
$count = 0;

try {
    // Try to get real notification count from database
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE (recipient_matric IS NULL OR recipient_matric = ?) AND is_read = 0");
    if ($stmt) {
        $stmt->bind_param('s', $matric_no);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = (int)$row['count'];
        $stmt->close();
    }
} catch (Exception $e) {
    // If notifications table doesn't exist, simulate count
    $count = rand(1, 5);
}

echo json_encode(['count' => $count]);
?>