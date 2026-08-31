<?php
session_start();
include('../connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Only department heads can approve
if (empty($_SESSION['admin-username']) || $_SESSION['role'] != 'department_head') {
    $_SESSION['error_message'] = "Unauthorized access. Only department heads can approve.";
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['btnapprove']) && isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);
    $dept = $_SESSION['department'] ?? '';
    
    // If department not in session, get it from admin table
    if (empty($dept)) {
        $username = $_SESSION['admin-username'];
        $stmt = $conn->prepare("SELECT department FROM admin WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $dept = $row['department'];
            $_SESSION['department'] = $dept;
        }
        $stmt->close();
    }
    
    if (empty($dept)) {
        $_SESSION['error_message'] = "Department not found. Please contact administrator.";
        header("Location: index.php");
        exit();
    }
    
    // Update the student's department approval status
    $update_query = "UPDATE students SET is_department_approved = 1 WHERE ID = ? AND dept = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('is', $student_id, $dept);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Student clearance approved successfully!";
        } else {
            $_SESSION['error_message'] = "No changes made. Student may already be approved or not found in your department.";
        }
    } else {
        $_SESSION['error_message'] = "Database error: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid request. Missing required data.";
}

header("Location: index.php");
exit();
?>
