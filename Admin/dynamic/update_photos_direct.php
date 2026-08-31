<?php
include('../connect.php');

echo "<h2>Direct Photo Update Script</h2>";

// Available photos that exist in uploads directory
$photos = [
    'uploads/avatar_nick.png',
    'uploads/aaa.jpg', 
    'uploads/photo_2023-12-27_11-46-15.jpg',
    'uploads/616A7408 (1).jpg',
    'uploads/code .png',
    'uploads/FINAL.png'
];

// Update ALL students with real photos
$update_query = "
UPDATE students 
SET photo = CASE 
    WHEN ID % 6 = 1 THEN 'uploads/avatar_nick.png'
    WHEN ID % 6 = 2 THEN 'uploads/aaa.jpg'
    WHEN ID % 6 = 3 THEN 'uploads/photo_2023-12-27_11-46-15.jpg'
    WHEN ID % 6 = 4 THEN 'uploads/616A7408 (1).jpg'
    WHEN ID % 6 = 5 THEN 'uploads/code .png'
    ELSE 'uploads/FINAL.png'
END
";

$result = mysqli_query($conn, $update_query);

if ($result) {
    $affected_rows = mysqli_affected_rows($conn);
    echo "<p>✅ Successfully updated {$affected_rows} students with real photos!</p>";
    
    // Also update register table for ALL students
    $update_register = "
    UPDATE register r
    JOIN students s ON r.matric_no = s.matric_no
    SET r.photo = s.photo
    ";
    
    $register_result = mysqli_query($conn, $update_register);
    if ($register_result) {
        $register_affected = mysqli_affected_rows($conn);
        echo "<p>✅ Also updated {$register_affected} records in register table!</p>";
    }
    
    echo "<hr>";
    echo "<h3>🎉 Photo update completed!</h3>";
    echo "<p><a href='check_database.php' style='background: #17a2b8; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>→ CHECK AVAILABLE DEPARTMENTS</a></p>";
    echo "<p><a href='index.php?dept=Computer Science and Engineering' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>→ VIEW STUDENT LIST</a></p>";
    
} else {
    echo "<p>❌ Error updating photos: " . mysqli_error($conn) . "</p>";
}

// Show current photo assignments for first department
echo "<hr><h3>Current Photo Assignments:</h3>";
$check_query = "SELECT fullname, matric_no, photo, dept FROM students ORDER BY dept, fullname LIMIT 15";
$check_result = mysqli_query($conn, $check_query);

if ($check_result) {
    while($row = mysqli_fetch_assoc($check_result)) {
        echo "<p><strong>{$row['fullname']}</strong> ({$row['matric_no']}) - {$row['dept']} → {$row['photo']}</p>";
    }
}
?>