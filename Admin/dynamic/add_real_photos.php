<?php
include('../connect.php');

echo "<h2>Adding Real Photos to Students</h2>";

// List of available photos in uploads directory (verified to exist)
$available_photos = [
    'uploads/avatar_nick.png',
    'uploads/aaa.jpg',
    'uploads/photo_2023-12-27_11-46-15.jpg',
    'uploads/616A7408 (1).jpg',
    'uploads/code .png',
    'uploads/FINAL.png',
    'uploads/Screenshot 2022-05-16 190919.png',
    'uploads/Screenshot 2022-05-16 191223.png'
];

// Get students from Computer Science and Engineering department
$students_query = "SELECT ID, matric_no, fullname FROM students WHERE dept = 'Computer Science and Engineering' ORDER BY fullname LIMIT 10";
$students_result = mysqli_query($conn, $students_query);

if ($students_result && mysqli_num_rows($students_result) > 0) {
    $count = 0;
    while($row = mysqli_fetch_assoc($students_result)) {
        // Assign photos in rotation
        $photo_to_assign = $available_photos[$count % count($available_photos)];
        
        // Update students table
        $update_students = "UPDATE students SET photo = ? WHERE ID = ?";
        $stmt1 = mysqli_prepare($conn, $update_students);
        mysqli_stmt_bind_param($stmt1, 'si', $photo_to_assign, $row['ID']);
        $result1 = mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);
        
        // Update register table
        $update_register = "UPDATE register SET photo = ? WHERE matric_no = ?";
        $stmt2 = mysqli_prepare($conn, $update_register);
        mysqli_stmt_bind_param($stmt2, 'ss', $photo_to_assign, $row['matric_no']);
        $result2 = mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
        
        if ($result1) {
            echo "<p>✅ <strong>{$row['fullname']}</strong> ({$row['matric_no']}) → {$photo_to_assign}</p>";
        } else {
            echo "<p>❌ Failed to update {$row['fullname']}</p>";
        }
        
        $count++;
    }
    
    echo "<hr>";
    echo "<h3>✅ Updated {$count} students with real photos!</h3>";
    echo "<p><strong>Now go check the admin page - you should see real photos!</strong></p>";
    echo "<p><a href='index.php?dept=Computer Science and Engineering' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>→ VIEW UPDATED STUDENT LIST</a></p>";
} else {
    echo "<p>No students found in Computer Science and Engineering department</p>";
}
?>