<?php
session_start();
include('connect.php');
error_reporting(0);

if ($_SESSION['role'] != 'sport') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['btnapprove'])) {
    $id = intval($_POST['student_id']);

    // ✅ Check if Department Head has approved first
    $check = mysqli_query($conn, "SELECT is_department_approved FROM students WHERE ID='$id'");
    $row = mysqli_fetch_assoc($check);

    if ($row['is_department_approved'] != '1') {
        $_SESSION['error'] = "⚠️ You cannot approve this student until the Department Head has approved first!";
        header("Location: sport_clearance.php");
        exit();
    }

    // ✅ If approved, proceed with library approval
    $query = "UPDATE students SET is_sport_approved='1' WHERE ID='$id'";
    mysqli_query($conn, $query);

    $_SESSION['success'] = "✅ sport clearance approved successfully!";
    header("Location: sport.php");
    exit();
}
?>
