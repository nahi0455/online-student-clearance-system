<?php
session_start();
include('../connect.php');

if (isset($_POST['btnapprove']) && isset($_POST['student_id'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    
    // Update the department approval status
    $sql = "UPDATE students SET is_department_approved = 1 WHERE ID = '$student_id'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Student department clearance approved successfully!";
    } else {
        $_SESSION['error_message'] = "Error approving department clearance: " . mysqli_error($conn);
    }
    
    // Redirect back to the department page
    $dept = $_SESSION['department'] ?? '';
    header("Location: admin.php?dept=" . urlencode($dept));
    exit();
} else {
    // Invalid request
    $_SESSION['error_message'] = "Invalid approval request.";
    header("Location: ../index.php");
    exit();
}
?>