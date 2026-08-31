<?php
// Temporary script to remove specific sessions
session_start();
include('../connect.php');

// Sessions to remove
$sessions_to_remove = ['2024/2025', '2022/2023'];

echo "<h3>Removing Sessions from Database</h3>";

foreach ($sessions_to_remove as $session_name) {
    // Check if session exists
    $check_stmt = $conn->prepare("SELECT ID FROM tblsession WHERE session = ?");
    if ($check_stmt) {
        $check_stmt->bind_param('s', $session_name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Session exists, delete it
            $delete_stmt = $conn->prepare("DELETE FROM tblsession WHERE session = ?");
            if ($delete_stmt) {
                $delete_stmt->bind_param('s', $session_name);
                if ($delete_stmt->execute()) {
                    echo "<p style='color:green;'>✅ Successfully removed session: " . htmlspecialchars($session_name) . "</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to remove session: " . htmlspecialchars($session_name) . "</p>";
                }
                $delete_stmt->close();
            }
        } else {
            echo "<p style='color:orange;'>⚠️ Session not found: " . htmlspecialchars($session_name) . "</p>";
        }
        $check_stmt->close();
    }
}

echo "<br><a href='Manage_Students.php' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>← Back to Manage Students</a>";
?>