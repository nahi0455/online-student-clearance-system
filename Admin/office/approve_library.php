<?php
session_start();
include('../connect.php');
error_reporting(0);

if ($_SESSION['role'] != 'library_chief') {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['btnapprove'])) {
    $id = intval($_POST['student_id']);
    $query = "UPDATE students SET is_library_approved='1' WHERE ID='$id'";
    mysqli_query($conn, $query);
    $_SESSION['success'] = "Student library clearance approved!";
    header("Location: library_clearance.php");
    exit();
}
?>
