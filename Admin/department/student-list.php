<?php

session_start();
include('../connect.php');

if ($_SESSION['role'] == 'department_head') {
    $dept = $_SESSION['department'];
    $sql = "SELECT * FROM students WHERE dept='$dept'";
} else {
    $sql = "SELECT * FROM students";
}

$result = mysqli_query($conn, $sql);
