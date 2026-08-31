<?php
session_start();
include('../connect.php');
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE students SET 
        is_department_approved=0,
        is_library_approved=0,
        is_bookstore_approved=0,
        is_dormitory_approved=0,
        is_cafeteria_approved=0,
        is_sport_approved=0,
        is_student_dean_approved=0,
        is_police_approved=0,
        is_registrar_approved=0
        WHERE ID='$id'";
    mysqli_query($conn, $sql);
    header("Location: student-clearance.php");
    exit();
}
?>
