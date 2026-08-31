<?php
session_start();
include('connect.php');

// Quick login for testing - use existing admin from database
if (isset($_GET['login'])) {
    $query = "SELECT * FROM admin WHERE status='Active' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        
        $_SESSION['admin-username'] = $row['username'];
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['department'] = $row['department'];
        $_SESSION['photo'] = $row['photo'];
        
        echo "<script>alert('Logged in as: " . $row['fullname'] . "'); window.location='add-student.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quick Admin Login - Testing</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Quick Admin Login (Testing Only)</h2>
        
        <div class="info">
            <strong>Current Session Status:</strong><br>
            <?php if (isset($_SESSION['admin-username'])): ?>
                ✅ Logged in as: <?php echo $_SESSION['admin-username']; ?> (<?php echo $_SESSION['fullname'] ?? 'Unknown'; ?>)<br>
                Role: <?php echo $_SESSION['role'] ?? 'Unknown'; ?><br>
                <a href="add-student.php" class="btn">Go to Add Student Page</a>
            <?php else: ?>
                ❌ Not logged in
            <?php endif; ?>
        </div>

        <div class="info">
            <strong>Available Admins in Database:</strong><br>
            <?php
            $query = "SELECT username, fullname, role, status FROM admin WHERE status='Active' LIMIT 5";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_array($result)) {
                    echo "- " . $row['username'] . " (" . $row['fullname'] . ") - " . $row['role'] . "<br>";
                }
            } else {
                echo "No active admins found";
            }
            ?>
        </div>

        <?php if (!isset($_SESSION['admin-username'])): ?>
            <a href="?login=1" class="btn">Quick Login with First Available Admin</a><br><br>
        <?php endif; ?>
        
        <a href="login.php" class="btn" style="background: #28a745;">Go to Proper Login Page</a><br><br>
        <a href="test_db.php" class="btn" style="background: #6c757d;">Test Database Connection</a>
    </div>
</body>
</html>