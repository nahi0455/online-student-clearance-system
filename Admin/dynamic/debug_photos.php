<?php
// Debug script to check student photos
include('../connect.php');

echo "<h3>Debug: Student Photos</h3>";

// Check register table
echo "<h4>Register Table Photos:</h4>";
$register_query = "SELECT matric_no, fullname, photo FROM register WHERE photo IS NOT NULL AND photo != '' LIMIT 10";
$register_result = mysqli_query($conn, $register_query);

if ($register_result && mysqli_num_rows($register_result) > 0) {
    while($row = mysqli_fetch_assoc($register_result)) {
        echo "<p><strong>{$row['fullname']} ({$row['matric_no']}):</strong> {$row['photo']}</p>";
        
        // Check if file exists
        $file_path = '../' . $row['photo'];
        if (file_exists($file_path)) {
            echo "<p style='color: green;'>✓ File exists: {$file_path}</p>";
        } else {
            echo "<p style='color: red;'>✗ File missing: {$file_path}</p>";
        }
        echo "<hr>";
    }
} else {
    echo "<p>No photos found in register table</p>";
}

// Check students table
echo "<h4>Students Table Photos:</h4>";
$students_query = "SELECT matric_no, fullname, photo FROM students WHERE photo IS NOT NULL AND photo != '' LIMIT 10";
$students_result = mysqli_query($conn, $students_query);

if ($students_result && mysqli_num_rows($students_result) > 0) {
    while($row = mysqli_fetch_assoc($students_result)) {
        echo "<p><strong>{$row['fullname']} ({$row['matric_no']}):</strong> {$row['photo']}</p>";
        
        // Check if file exists
        $file_path = '../' . $row['photo'];
        if (file_exists($file_path)) {
            echo "<p style='color: green;'>✓ File exists: {$file_path}</p>";
        } else {
            echo "<p style='color: red;'>✗ File missing: {$file_path}</p>";
        }
        echo "<hr>";
    }
} else {
    echo "<p>No photos found in students table</p>";
}

// Check JOIN query
echo "<h4>JOIN Query Result:</h4>";
$join_query = "
    SELECT 
        s.matric_no,
        COALESCE(r.fullname, s.fullname) as fullname,
        COALESCE(r.photo, s.photo) as photo,
        r.photo as register_photo,
        s.photo as students_photo
    FROM students s
    LEFT JOIN register r ON s.matric_no = r.matric_no
    WHERE (r.photo IS NOT NULL AND r.photo != '') OR (s.photo IS NOT NULL AND s.photo != '')
    LIMIT 10
";
$join_result = mysqli_query($conn, $join_query);

if ($join_result && mysqli_num_rows($join_result) > 0) {
    while($row = mysqli_fetch_assoc($join_result)) {
        echo "<p><strong>{$row['fullname']} ({$row['matric_no']}):</strong></p>";
        echo "<p>Final photo: {$row['photo']}</p>";
        echo "<p>Register photo: {$row['register_photo']}</p>";
        echo "<p>Students photo: {$row['students_photo']}</p>";
        echo "<hr>";
    }
} else {
    echo "<p>No photos found in JOIN query</p>";
}
?>