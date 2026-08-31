<?php
/* Local Database*/

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_clearance";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure students table has an auto-increment ID column for tracking requests
$hasIdCol = mysqli_query($conn, "SHOW COLUMNS FROM students LIKE 'ID'");
if ($hasIdCol && mysqli_num_rows($hasIdCol) === 0) {
    mysqli_query($conn, "ALTER TABLE students ADD COLUMN ID INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE");
}

// Ensure request_year column exists for yearly request control
$hasReqYearCol = mysqli_query($conn, "SHOW COLUMNS FROM students LIKE 'request_year'");
if ($hasReqYearCol && mysqli_num_rows($hasReqYearCol) === 0) {
    mysqli_query($conn, "ALTER TABLE students ADD COLUMN request_year INT NULL");
}
?> 
