<?php
include('../connect.php');

echo "<h2>Database Check</h2>";

// Check available departments
echo "<h3>Available Departments:</h3>";
$dept_query = "SELECT DISTINCT dept FROM students ORDER BY dept";
$dept_result = mysqli_query($conn, $dept_query);

if ($dept_result) {
    while($row = mysqli_fetch_assoc($dept_result)) {
        echo "<p>• {$row['dept']}</p>";
    }
}

echo "<hr>";

// Check students count by department
echo "<h3>Student Count by Department:</h3>";
$count_query = "SELECT dept, COUNT(*) as count FROM students GROUP BY dept ORDER BY count DESC";
$count_result = mysqli_query($conn, $count_query);

if ($count_result) {
    while($row = mysqli_fetch_assoc($count_result)) {
        echo "<p><strong>{$row['dept']}:</strong> {$row['count']} students</p>";
    }
}

echo "<hr>";

// Check first few students from any department
echo "<h3>Sample Students (First 10):</h3>";
$sample_query = "SELECT ID, fullname, matric_no, dept, photo FROM students ORDER BY ID LIMIT 10";
$sample_result = mysqli_query($conn, $sample_query);

if ($sample_result) {
    while($row = mysqli_fetch_assoc($sample_result)) {
        echo "<p><strong>{$row['fullname']}</strong> ({$row['matric_no']}) - {$row['dept']} - Photo: {$row['photo']}</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php?dept=Computer Science and Engineering'>→ Try Computer Science and Engineering</a></p>";

// Get the first department name for testing
$first_dept_query = "SELECT DISTINCT dept FROM students ORDER BY dept LIMIT 1";
$first_dept_result = mysqli_query($conn, $first_dept_query);
if ($first_dept_result) {
    $first_dept = mysqli_fetch_assoc($first_dept_result);
    if ($first_dept) {
        echo "<p><a href='index.php?dept=" . urlencode($first_dept['dept']) . "'>→ Try " . htmlspecialchars($first_dept['dept']) . "</a></p>";
    }
}
?>