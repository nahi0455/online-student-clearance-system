<?php
session_start();
include('connect.php');

echo "<h2>Super Admin Test</h2>";

// Check if super admin exists in database
$query = "SELECT * FROM admin WHERE role='super_admin'";
$result = mysqli_query($conn, $query);

echo "<h3>Super Admin Users in Database:</h3>";
if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    
    while($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['fullname'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ No super admin users found.<br>";
    
    // Create super admin user
    $insert_query = "INSERT INTO admin (username, password, designation, fullname, email, status, photo, role, department) 
                     VALUES ('superadmin', 'admin123', 'Super Administrator', 'Super Admin', 'superadmin@university.edu', 'Active', 'uploads/avatar_nick.png', 'super_admin', NULL)";
    
    if (mysqli_query($conn, $insert_query)) {
        echo "✅ Super admin user created successfully!<br>";
        echo "<strong>Username:</strong> superadmin<br>";
        echo "<strong>Password:</strong> admin123<br>";
    } else {
        echo "❌ Error creating super admin: " . mysqli_error($conn) . "<br>";
    }
}

echo "<br><h3>Current Session:</h3>";
if (isset($_SESSION['admin-username'])) {
    echo "✅ Logged in as: " . $_SESSION['admin-username'] . "<br>";
    echo "Role: " . ($_SESSION['role'] ?? 'Unknown') . "<br>";
    echo "Full Name: " . ($_SESSION['fullname'] ?? 'Unknown') . "<br>";
} else {
    echo "❌ Not logged in<br>";
}

echo "<br><h3>Test Links:</h3>";
echo '<a href="login.php" style="display: inline-block; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">Admin Login</a><br>';
echo '<a href="../super_admin/super_admin.php" style="display: inline-block; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">Super Admin Dashboard</a><br>';
echo '<a href="add-student.php" style="display: inline-block; padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">Add Student</a><br>';

echo "<br><h3>Login Instructions:</h3>";
echo "1. Go to <a href='login.php'>Admin Login</a><br>";
echo "2. Use username: <strong>superadmin</strong><br>";
echo "3. Use password: <strong>admin123</strong><br>";
echo "4. You will be redirected to the Super Admin Dashboard<br>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px 12px; text-align: left; }
th { background: #f8f9fa; }
</style>