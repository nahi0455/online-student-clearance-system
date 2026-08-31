<?php
session_start();
include('../connect.php');

echo "<h2>Photo Upload Debug Information</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>FILES Data:</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    if (isset($_FILES['userImage'])) {
        $file = $_FILES['userImage'];
        echo "<h3>File Upload Analysis:</h3>";
        echo "File Name: " . $file['name'] . "<br>";
        echo "File Size: " . $file['size'] . " bytes<br>";
        echo "File Type: " . $file['type'] . "<br>";
        echo "Temp Name: " . $file['tmp_name'] . "<br>";
        echo "Error Code: " . $file['error'] . "<br>";
        
        // Check if temp file exists
        if (file_exists($file['tmp_name'])) {
            echo "✅ Temp file exists<br>";
        } else {
            echo "❌ Temp file does not exist<br>";
        }
        
        // Check uploads directory
        if (is_dir('../uploads/')) {
            echo "✅ Uploads directory exists<br>";
        } else {
            echo "❌ Uploads directory does not exist<br>";
        }
        
        // Check permissions
        if (is_writable('../uploads/')) {
            echo "✅ Uploads directory is writable<br>";
        } else {
            echo "❌ Uploads directory is not writable<br>";
        }
    }
} else {
    echo "<p>No POST data received. Please submit the form first.</p>";
}

echo "<p><a href='edit-photo.php'>Back to Edit Photo</a></p>";
?>