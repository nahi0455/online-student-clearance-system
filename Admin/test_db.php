<?php
// Simple database connection test
session_start();

echo "<h2>Database Connection Test</h2>";

// Test mysqli connection
echo "<h3>Testing mysqli connection (connect.php):</h3>";
try {
    include('connect.php');
    if ($conn) {
        echo "✅ mysqli connection successful<br>";
        
        // Test query
        $result = $conn->query("SELECT COUNT(*) as count FROM tblsession");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "✅ tblsession table accessible, has " . $row['count'] . " records<br>";
        } else {
            echo "❌ Error querying tblsession: " . $conn->error . "<br>";
        }
    } else {
        echo "❌ mysqli connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ mysqli connection error: " . $e->getMessage() . "<br>";
}

echo "<br>";

// Test PDO connection
echo "<h3>Testing PDO connection (connect2.php):</h3>";
try {
    include('../login admin/connect2.php');
    if ($dbh) {
        echo "✅ PDO connection successful<br>";
        
        // Test query
        $stmt = $dbh->prepare("SELECT COUNT(*) as count FROM tblsession");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "✅ tblsession table accessible via PDO, has " . $result['count'] . " records<br>";
        
        // Show sessions
        $stmt = $dbh->prepare("SELECT * FROM tblsession ORDER BY session DESC");
        $stmt->execute();
        $sessions = $stmt->fetchAll();
        echo "<h4>Available sessions:</h4>";
        foreach($sessions as $session) {
            echo "- " . $session['session'] . "<br>";
        }
        
    } else {
        echo "❌ PDO connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ PDO connection error: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Session Check:</h3>";
if (isset($_SESSION['admin-username'])) {
    echo "✅ Admin session exists: " . $_SESSION['admin-username'] . "<br>";
} else {
    echo "❌ No admin session found. You need to login first.<br>";
    echo '<a href="login.php">Go to Admin Login</a><br>';
}

echo "<br><a href='add-student.php'>Back to Add Student</a>";
?>