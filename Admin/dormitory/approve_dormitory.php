<?php
session_start();
include('connect.php');

if (isset($_POST['btnapprove']) && isset($_POST['student_id'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    
    // Update the dormitory approval status
    $sql = "UPDATE students SET is_dormitory_approved = 1 WHERE ID = '$student_id'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Student dormitory clearance approved successfully!";
    } else {
        $_SESSION['error_message'] = "Error approving dormitory clearance: " . mysqli_error($conn);
    }
    
    // Redirect back to the dormitory page
    header("Location: dormitory.php");
    exit();
} else {
    // Invalid request
    $_SESSION['error_message'] = "Invalid approval request.";
    header("Location: dormitory.php");
    exit();
}
?>