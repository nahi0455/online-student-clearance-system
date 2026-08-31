<?php
session_start();
include('../connect.php');

// Test script to debug profile update issues
echo "<h2>Profile Update Debug Information</h2>";

if (empty($_SESSION['matric_no'])) {
    echo "<p style='color: red;'>No session found. Please login first.</p>";
    exit();
}

$matric_no = $_SESSION['matric_no'];
echo "<h3>Session Information:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Students Table Data:</h3>";
$stmt = $conn->prepare("SELECT * FROM students WHERE matric_no = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param('s', $matric_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_data = $result->fetch_assoc();
    echo "<pre>";
    print_r($student_data);
    echo "</pre>";
    $stmt->close();
} else {
    echo "<p style='color: red;'>Failed to query students table</p>";
}

echo "<h3>Register Table Data:</h3>";
$stmt = $conn->prepare("SELECT * FROM register WHERE matric_no = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param('s', $matric_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $register_data = $result->fetch_assoc();
    echo "<pre>";
    print_r($register_data);
    echo "</pre>";
    $stmt->close();
} else {
    echo "<p style='color: red;'>Failed to query register table</p>";
}

echo "<h3>Database Tables Structure:</h3>";
echo "<h4>Students Table Columns:</h4>";
$result = $conn->query("SHOW COLUMNS FROM students");
if ($result) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
    }
    echo "</ul>";
}

echo "<h4>Register Table Columns:</h4>";
$result = $conn->query("SHOW COLUMNS FROM register");
if ($result) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
    }
    echo "</ul>";
}

echo "<p><a href='profile.php'>Back to Profile</a> | <a href='index.php'>Back to Dashboard</a></p>";
?>