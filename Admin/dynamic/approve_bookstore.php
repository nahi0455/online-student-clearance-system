<?php
session_start();
include('../connect.php');
error_reporting(0);

// ✅ Ensure the session is set
if (empty($_SESSION['role']) || $_SESSION['role'] != 'department_head') {
    // Instead of redirecting to login, just exit with error
    $_SESSION['error'] = "You don't have permission to approve.";
    header("Location: bookstore.php?dept=" . urlencode($_SESSION['department']));
    exit();
}

if (isset($_POST['btnapprove'])) {
    $student_id = intval($_POST['student_id']);
    $dept = $_SESSION['department'];

    // Update the student's bookstore approval
    $sql = "UPDATE students SET is_bookstore_approved = 1 WHERE ID = '$student_id' AND dept = '$dept'";
    $result = mysqli_query($conn, $sql);

}

// Redirect back to the same department page
header("Location: bookstore.php?dept=" . urlencode($_SESSION['department']));
exit();
?>
