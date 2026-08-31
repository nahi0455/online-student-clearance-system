<?php
// Script to assign existing photos to students for testing
include('../connect.php');

echo "<h3>Assigning Real Photos to Students</h3>";

// Available photos in uploads directory
$available_photos = [
    'uploads/616A7408 (1).jpg',
    'uploads/aaa.jpg',
    'uploads/photo_2023-12-27_11-46-15.jpg',
    'uploads/avatar_nick.png',
    'uploads/logo.png'
];

// Get some students from Computer Science department
$students_query = "SELECT ID, matric_no, fullname FROM students WHERE dept = 'Computer Science and Engineering' LIMIT 10";
$students_result = mysqli_query($conn, $students_query);

$assigned_count = 0;

if ($students_result && mysqli_num_rows($students_result) > 0) {
    while($row = mysqli_fetch_assoc($students_result)) {
        // Assign a random photo from available photos
        $photo_index = $assigned_count % count($available_photos);
        $assigned_photo = $available_photos[$photo_index];
        
        // Update both students and register tables
        $update_students = "UPDATE students SET photo = ? WHERE ID = ?";
        $stmt1 = mysqli_prepare($conn, $update_students);
        mysqli_stmt_bind_param($stmt1, 'si', $assigned_photo, $row['ID']);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);
        
        // Also update register table if the student exists there
        $update_register = "UPDATE register SET photo = ? WHERE matric_no = ?";
        $stmt2 = mysqli_prepare($conn, $update_register);
        mysqli_stmt_bind_param($stmt2, 'ss', $assigned_photo, $row['matric_no']);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
        
        echo "<p>✅ Assigned <strong>{$assigned_photo}</strong> to <strong>{$row['fullname']} ({$row['matric_no']})</strong></p>";
        $assigned_count++;
    }
}

echo "<h3>Summary:</h3>";
echo "<p>Assigned photos to {$assigned_count} students</p>";
echo "<p><a href='index.php?dept=Computer Science and Engineering' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>→ View Updated Student List</a></p>";
?>