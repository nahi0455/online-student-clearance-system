<?php
include('../connect.php');

echo "<h2>Photo Debug Test</h2>";

// Test the exact same query as the main page
$dept = 'Computer Science and Engineering';
$query_students = "
    SELECT 
        s.*,
        COALESCE(r.photo, s.photo) as photo,
        COALESCE(r.fullname, s.fullname) as fullname,
        COALESCE(r.matric_no, s.matric_no) as matric_no
    FROM students s
    LEFT JOIN register r ON s.matric_no = r.matric_no
    WHERE s.dept='$dept'
    ORDER BY COALESCE(r.fullname, s.fullname) ASC
    LIMIT 5
";

echo "<h3>Query:</h3>";
echo "<pre>" . htmlspecialchars($query_students) . "</pre>";

$result_students = mysqli_query($conn, $query_students);

if (!$result_students) {
    echo "<p style='color: red;'>Query Error: " . mysqli_error($conn) . "</p>";
} else {
    echo "<h3>Results:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Name</th><th>Matric No</th><th>Photo Path</th><th>File Exists?</th><th>Preview</th></tr>";
    
    while($row = mysqli_fetch_assoc($result_students)) {
        $student_photo = $row['photo'];
        $file_exists = 'N/A';
        $preview = 'No photo';
        
        if (!empty($student_photo)) {
            // Format path
            if (!str_starts_with($student_photo, '../') && !str_starts_with($student_photo, 'http')) {
                if (str_starts_with($student_photo, 'uploads/')) {
                    $student_photo = '../' . $student_photo;
                } else {
                    $student_photo = '../uploads/' . $student_photo;
                }
            }
            
            $file_path = str_replace('../', '', $student_photo);
            $file_exists = file_exists($file_path) ? 'YES' : 'NO';
            
            if (file_exists($file_path)) {
                $preview = "<img src='{$student_photo}' width='50' height='50' style='object-fit: cover; border-radius: 50%;'>";
            } else {
                $preview = 'File missing';
            }
        }
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['matric_no']) . "</td>";
        echo "<td>" . htmlspecialchars($student_photo) . "</td>";
        echo "<td style='color: " . ($file_exists === 'YES' ? 'green' : 'red') . ";'>{$file_exists}</td>";
        echo "<td>{$preview}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<p><a href='assign_photos.php'>→ Assign Photos to Students</a></p>";
echo "<p><a href='index.php?dept=Computer Science and Engineering'>→ Back to Main Page</a></p>";
?>