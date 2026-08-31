<?php
// Script to fix invalid photo paths in the database
include('../connect.php');

echo "<h3>Fixing Photo Paths</h3>";

// Check students table for invalid photo paths
$students_query = "SELECT ID, matric_no, fullname, photo FROM students WHERE photo IS NOT NULL AND photo != ''";
$students_result = mysqli_query($conn, $students_query);

$fixed_count = 0;
$cleared_count = 0;

if ($students_result && mysqli_num_rows($students_result) > 0) {
    while($row = mysqli_fetch_assoc($students_result)) {
        $photo_path = $row['photo'];
        $file_path = str_replace('../', '', $photo_path);
        
        echo "<p><strong>{$row['fullname']} ({$row['matric_no']}):</strong> {$photo_path}</p>";
        
        if (!file_exists($file_path)) {
            echo "<p style='color: red;'>✗ File missing: {$file_path}</p>";
            
            // Clear the invalid photo path
            $update_query = "UPDATE students SET photo = NULL WHERE ID = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'i', $row['ID']);
            
            if (mysqli_stmt_execute($update_stmt)) {
                echo "<p style='color: orange;'>→ Cleared invalid photo path</p>";
                $cleared_count++;
            }
            mysqli_stmt_close($update_stmt);
        } else {
            echo "<p style='color: green;'>✓ File exists</p>";
        }
        echo "<hr>";
    }
}

// Check register table for invalid photo paths
echo "<h4>Checking Register Table:</h4>";
$register_query = "SELECT ID, matric_no, fullname, photo FROM register WHERE photo IS NOT NULL AND photo != ''";
$register_result = mysqli_query($conn, $register_query);

if ($register_result && mysqli_num_rows($register_result) > 0) {
    while($row = mysqli_fetch_assoc($register_result)) {
        $photo_path = $row['photo'];
        $file_path = str_replace('../', '', $photo_path);
        
        echo "<p><strong>{$row['fullname']} ({$row['matric_no']}):</strong> {$photo_path}</p>";
        
        if (!file_exists($file_path)) {
            echo "<p style='color: red;'>✗ File missing: {$file_path}</p>";
            
            // Clear the invalid photo path
            $update_query = "UPDATE register SET photo = NULL WHERE ID = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'i', $row['ID']);
            
            if (mysqli_stmt_execute($update_stmt)) {
                echo "<p style='color: orange;'>→ Cleared invalid photo path</p>";
                $cleared_count++;
            }
            mysqli_stmt_close($update_stmt);
        } else {
            echo "<p style='color: green;'>✓ File exists</p>";
        }
        echo "<hr>";
    }
}

echo "<h3>Summary:</h3>";
echo "<p>Fixed paths: {$fixed_count}</p>";
echo "<p>Cleared invalid paths: {$cleared_count}</p>";
echo "<p><a href='index.php?dept=Computer Science'>← Back to Dynamic Index</a></p>";
?>