<?php
session_start();
include('../connect.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE students SET is_library_approved = 1 WHERE ID = '$id'";
    mysqli_query($conn, $sql);
    header("Location: student-clearance.php");
    exit();
}
?>
