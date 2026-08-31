<?php
session_start();
include('../connect.php');

if(empty($_SESSION['matric_no'])) {
    header("Location: ../login student/login.php"); 
    exit();
}

$matric_no = $_SESSION["matric_no"];

// Simple upload handling
if(isset($_POST["upload_photo"])) {
    echo "<h3>Form Submitted!</h3>";
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";
    
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $filename = $_FILES['photo']['name'];
        $tmp_name = $_FILES['photo']['tmp_name'];
        
        // Simple move to uploads
        if(move_uploaded_file($tmp_name, "../uploads/" . $filename)) {
            echo "<p style='color: green;'>✅ File uploaded successfully!</p>";
            
            // Simple database update
            $location = "uploads/" . $filename;
            $sql = "UPDATE register SET photo='$location' WHERE matric_no='$matric_no'";
            
            if(mysqli_query($conn, $sql)) {
                echo "<p style='color: green;'>✅ Database updated successfully!</p>";
                $_SESSION['photo'] = $location;
            } else {
                echo "<p style='color: red;'>❌ Database update failed: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ File upload failed!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No file or upload error: " . ($_FILES['photo']['error'] ?? 'unknown') . "</p>";
    }
}

// Get current photo
$sql = "SELECT photo FROM register WHERE matric_no='$matric_no'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$current_photo = $row['photo'] ?? 'uploads/default.jpg';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Photo Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .photo { width: 200px; height: 200px; border: 2px solid #ccc; margin: 20px 0; }
        .form { background: #f5f5f5; padding: 20px; margin: 20px 0; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Simple Photo Upload Test</h1>
    
    <h2>Current Photo:</h2>
    <img src="../<?php echo $current_photo; ?>" class="photo" alt="Current Photo" onerror="this.src='../images/default-avatar.png';">
    <p>Path: <?php echo $current_photo; ?></p>
    
    <div class="form">
        <h2>Upload New Photo:</h2>
        <form method="POST" enctype="multipart/form-data">
            <p>
                <label>Select Photo:</label><br>
                <input type="file" name="photo" accept="image/*" required>
            </p>
            <p>
                <button type="submit" name="upload_photo">Upload Photo</button>
            </p>
        </form>
    </div>
    
    <p><a href="edit-photo.php">← Back to Edit Photo</a></p>
    
    <h3>Debug Info:</h3>
    <p>Matric No: <?php echo $matric_no; ?></p>
    <p>Session Photo: <?php echo $_SESSION['photo'] ?? 'Not set'; ?></p>
    <p>Uploads Directory: <?php echo is_dir('../uploads/') ? '✅ Exists' : '❌ Missing'; ?></p>
    <p>Uploads Writable: <?php echo is_writable('../uploads/') ? '✅ Yes' : '❌ No'; ?></p>
</body>
</html>