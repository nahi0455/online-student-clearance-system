<?php
// Simple test script to verify database connection and register table
include('connect.php');

echo "<h2>Database Connection Test</h2>";

// Test connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "✅ Database connection successful<br>";
}

// Create register table
$create_table_sql = "CREATE TABLE IF NOT EXISTS `register` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `fullname` varchar(255) NOT NULL,
    `matric_no` varchar(50) NOT NULL,
    `password` varchar(50) NOT NULL,
    `session` varchar(20) NOT NULL,
    `faculty` varchar(100) NOT NULL,
    `dept` varchar(100) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `photo` varchar(255) DEFAULT 'uploads/avatar_nick.png',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `matric_no` (`matric_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$create_result = mysqli_query($conn, $create_table_sql);
if (!$create_result) {
    echo "❌ Error creating table: " . mysqli_error($conn) . "<br>";
} else {
    echo "✅ Register table created/verified successfully<br>";
}

// Test insert
$test_fullname = "Test Student " . date('Y-m-d H:i:s');
$test_matric = "TEST" . rand(1000, 9999);
$test_password = "test123";
$test_session = "2023/2024";
$test_faculty = "Engineering & Technology";
$test_dept = "Computer Science";
$test_phone = "08012345678";

$insert_sql = "INSERT INTO register (fullname, matric_no, password, session, faculty, dept, phone, photo) 
              VALUES ('$test_fullname', '$test_matric', '$test_password', '$test_session', '$test_faculty', '$test_dept', '$test_phone', 'uploads/avatar_nick.png')";

$insert_result = mysqli_query($conn, $insert_sql);

if($insert_result) {
    echo "✅ Test record inserted successfully<br>";
    echo "Test Matric No: $test_matric<br>";
    echo "Test Password: $test_password<br>";
} else {
    echo "❌ Error inserting test record: " . mysqli_error($conn) . "<br>";
}

// Show all records
$select_sql = "SELECT * FROM register ORDER BY created_at DESC LIMIT 5";
$select_result = mysqli_query($conn, $select_sql);

echo "<h3>Recent Records:</h3>";
if (mysqli_num_rows($select_result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Full Name</th><th>Matric No</th><th>Faculty</th><th>Department</th><th>Created At</th></tr>";
    while($row = mysqli_fetch_assoc($select_result)) {
        echo "<tr>";
        echo "<td>" . $row['ID'] . "</td>";
        echo "<td>" . $row['fullname'] . "</td>";
        echo "<td>" . $row['matric_no'] . "</td>";
        echo "<td>" . $row['faculty'] . "</td>";
        echo "<td>" . $row['dept'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No records found in register table.";
}

mysqli_close($conn);
?>