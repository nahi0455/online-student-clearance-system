<?php
session_start();

$dept = $_GET['dept'] ?? '';

if ($dept == '') {
    echo "Invalid department.";
    exit();
}

$_SESSION['role'] = 'department_head';
$_SESSION['department'] = $dept;
$_SESSION['admin-username'] = strtolower(str_replace(' ', '_', $dept)) . "_head";
$_SESSION['fullname'] = $dept . " Head";
$_SESSION['email'] = $_SESSION['admin-username'] . "@university.edu";
$_SESSION['photo'] = "uploads/default.png";

header("Location: dynamic/index.php?dept=" . urlencode($dept));
exit();
?>
