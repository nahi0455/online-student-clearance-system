<?php
session_start();

// Get selected department
$dept = $_GET['dept'] ?? '';

// If no department selected, block access
if ($dept == '') {
    echo "Invalid department.";
    exit();
}

// Auto-generate session
$_SESSION['role'] = 'department_head';
$_SESSION['department'] = $dept;
$_SESSION['admin-username'] = strtolower(str_replace(' ', '_', $dept)) . "_head";
$_SESSION['fullname'] = $dept . " Head";
$_SESSION['email'] = $_SESSION['admin-username'] . "@university.edu";
$_SESSION['photo'] = "uploads/default.png"; // your default image

// Redirect to department dashboard
header("Location: department/index.php");
exit();
?>
