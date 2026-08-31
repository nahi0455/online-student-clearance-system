<?php
include('../connect.php');

echo "<h2>Current Photo Status Check</h2>";

// Check students table
echo "<h3>Students Table:</h3>";
$students_query = "SELECT ID, fullname, matric_no, photo FROM students WHERE dept = 'Computer Science and Engineering' ORDER BY fullname LIMIT 10";
$students_result = mysqli_query($conn, $students_query);

if ($students_result) {
    while($row = mysqli_fetch_assoc($students_result)) {
        $photo_exists = !empty($row['photo']) && file_exists('../' . $row['photo']);
        $status = $photo_exists ? "✅ EXISTS" : "❌ MISSING";
        echo "<p><strong>{$row['fullname']}</strong> (ID: {$row['ID']}) → {$row['photo']} {$status}</p>";
    }
}

echo "<hr>";

// Check register table
echo "<h3>Register Table:</h3>";
$register_query = "SELECT fullname, matric_no, photo FROM register WHERE matric_no IN (SELECT matric_no FROM students WHERE dept = 'Computer Science and Engineering') ORDER BY fullname LIMIT 10";
$register_result = mysqli_query($conn, $register_query);

if ($register_result) {
    while($row = mysqli_fetch_assoc($register_result)) {
        $photo_exists = !empty($row['photo']) && file_exists('../' . $row['photo']);
        $status = $photo_exists ? "✅ EXISTS" : "❌ MISSING";
        echo "<p><strong>{$row['fullname']}</strong> → {$row['photo']} {$status}</p>";
    }
}

echo "<hr>";
echo "<p><a href='update_photos_direct.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>→ UPDATE PHOTOS NOW</a></p>";
echo "<p><a href='index.php?dept=Computer Science and Engineering' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>→ VIEW STUDENT LIST</a></p>";
?>