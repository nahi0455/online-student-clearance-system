<?php
include('connect.php');

echo "<h2>Fixing Super Admin Role in Database</h2>";

// Step 1: Modify the ENUM to include super_admin
echo "<h3>Step 1: Adding 'super_admin' to role ENUM...</h3>";
$alter_query = "ALTER TABLE admin 
MODIFY COLUMN role ENUM(
    'department_head',
    'library',
    'bookstore',
    'dormitory',
    'cafeteria',
    'sport',
    'dean',
    'police',
    'registrar',
    'super_admin'
) NOT NULL DEFAULT 'department_head'";

if (mysqli_query($conn, $alter_query)) {
    echo "✅ Successfully updated admin table role ENUM to include 'super_admin'<br>";
} else {
    echo "❌ Error updating ENUM: " . mysqli_error($conn) . "<br>";
}

// Step 2: Create super admin user
echo "<h3>Step 2: Creating Super Admin User...</h3>";
$insert_query = "INSERT INTO admin (username, password, designation, fullname, email, status, photo, role, department) 
VALUES ('superadmin', 'admin123', 'Super Administrator', 'Super Admin', 'superadmin@university.edu', 'Active', 'uploads/avatar_nick.png', 'super_admin', NULL)
ON DUPLICATE KEY UPDATE 
    password = VALUES(password),
    role = VALUES(role),
    status = VALUES(status),
    designation = VALUES(designation)";

if (mysqli_query($conn, $insert_query)) {
    echo "✅ Super admin user created/updated successfully<br>";
    echo "<strong>Username:</strong> superadmin<br>";
    echo "<strong>Password:</strong> admin123<br>";
    echo "<strong>Role:</strong> super_admin<br>";
} else {
    echo "❌ Error creating super admin: " . mysqli_error($conn) . "<br>";
}

// Step 3: Show current admin users
echo "<h3>Step 3: Current Admin Users:</h3>";
$select_query = "SELECT username, fullname, role, status FROM admin ORDER BY role, username";
$result = mysqli_query($conn, $select_query);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Username</th><th>Full Name</th><th>Role</th><th>Status</th></tr>";
    
    while($row = mysqli_fetch_array($result)) {
        $roleColor = ($row['role'] == 'super_admin') ? 'style="background-color: #d4edda; font-weight: bold;"' : '';
        echo "<tr $roleColor>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['fullname'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No admin users found.";
}

echo "<br><h3>Test the Fix:</h3>";
echo '<a href="add-admin.php" style="display: inline-block; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">Test Add Admin Page</a><br>';
echo '<a href="login.php" style="display: inline-block; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;">Login as Super Admin</a><br>';

echo "<br><h3>Instructions:</h3>";
echo "1. The 'super_admin' role has been added to the database<br>";
echo "2. You can now select 'Super Administrator' in the add-admin.php form<br>";
echo "3. The role will save properly to the database<br>";
echo "4. Login with username: <strong>superadmin</strong> and password: <strong>admin123</strong><br>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px 12px; text-align: left; }
th { background: #f8f9fa; }
h2 { color: #007bff; }
h3 { color: #28a745; margin-top: 20px; }
</style>