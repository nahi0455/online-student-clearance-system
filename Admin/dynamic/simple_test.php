<?php
include('../connect.php');

echo "<h2>Simple Photo Test</h2>";

// First, let's manually assign a photo that we KNOW exists
$known_photo = 'uploads/avatar_nick.png';
$test_student_id = 26; // From your database dump, this is "nati ebist"

// Update the student with a known good photo
$update_query = "UPDATE students SET photo = ? WHERE ID = ?";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, 'si', $known_photo, $test_student_id);
$result = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($result) {
    echo "<p style='color: green;'>✅ Updated student ID {$test_student_id} with photo: {$known_photo}</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to update student</p>";
}

// Also update register table
$update_register = "UPDATE register SET photo = ? WHERE matric_no = (SELECT matric_no FROM students WHERE ID = ?)";
$stmt2 = mysqli_prepare($conn, $update_register);
mysqli_stmt_bind_param($stmt2, 'si', $known_photo, $test_student_id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

// Test if the file exists
$file_path = '../' . $known_photo;
echo "<p>Testing file: <strong>{$file_path}</strong></p>";
if (file_exists($file_path)) {
    echo "<p style='color: green;'>✅ File exists!</p>";
    echo "<p>Preview: <img src='{$file_path}' width='100' height='100' style='border-radius: 50%; object-fit: cover;'></p>";
} else {
    echo "<p style='color: red;'>❌ File does not exist</p>";
}

// Now test the query
$test_query = "
    SELECT 
        s.ID,
        s.matric_no,
        COALESCE(r.fullname, s.fullname) as fullname,
        COALESCE(r.photo, s.photo) as photo
    FROM students s
    LEFT JOIN register r ON s.matric_no = r.matric_no
    WHERE s.ID = ?
";

$stmt3 = mysqli_prepare($conn, $test_query);
mysqli_stmt_bind_param($stmt3, 'i', $test_student_id);
mysqli_stmt_execute($stmt3);
$result3 = mysqli_stmt_get_result($stmt3);
$test_row = mysqli_fetch_assoc($result3);
mysqli_stmt_close($stmt3);

if ($test_row) {
    echo "<h3>Query Result:</h3>";
    echo "<p><strong>Name:</strong> {$test_row['fullname']}</p>";
    echo "<p><strong>Matric:</strong> {$test_row['matric_no']}</p>";
    echo "<p><strong>Photo:</strong> {$test_row['photo']}</p>";
    
    if (!empty($test_row['photo'])) {
        $photo_path = '../' . $test_row['photo'];
        echo "<p><strong>Full Path:</strong> {$photo_path}</p>";
        echo "<p><strong>File Exists:</strong> " . (file_exists($photo_path) ? 'YES' : 'NO') . "</p>";
        
        if (file_exists($photo_path)) {
            echo "<p>Direct Image Test: <img src='{$photo_path}?v=" . time() . "' width='100' height='100' style='border-radius: 50%; object-fit: cover;'></p>";
        }
    }
}

echo "<hr>";
echo "<p><a href='index.php?dept=Computer Science and Engineering' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>→ Check Main Page Now</a></p>";
?>