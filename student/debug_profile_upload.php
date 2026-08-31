<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../connect.php');

echo "<h2>Debug Profile Upload Test</h2>";
echo "<p>Session matric_no: " . ($_SESSION['matric_no'] ?? 'NOT SET') . "</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    echo "<h3>FILES Data Received:</h3>";
    echo "<pre>" . print_r($_FILES, true) . "</pre>";
    
    if (isset($_POST['btnedit'])) {
        echo "<p><strong>✅ btnedit button clicked!</strong></p>";
        
        if (isset($_FILES['userImage'])) {
            echo "<p><strong>✅ userImage file found!</strong></p>";
            echo "<p>File error code: " . $_FILES['userImage']['error'] . "</p>";
            echo "<p>Upload error meanings: 0=OK, 1=too large, 2=too large, 3=partial, 4=no file, 6=no temp dir, 7=write failed</p>";
            
            if ($_FILES['userImage']['error'] === UPLOAD_ERR_OK) {
                echo "<p><strong>✅ File upload OK!</strong></p>";
                
                $matric_no = $_SESSION['matric_no'];
                $image = addslashes(file_get_contents($_FILES['userImage']['tmp_name']));
                $image_name = addslashes($_FILES['userImage']['name']);
                $image_size = getimagesize($_FILES['userImage']['tmp_name']);
                
                echo "<p>Image name: " . $image_name . "</p>";
                echo "<p>Image size: " . print_r($image_size, true) . "</p>";
                
                // Create uploads directory if it doesn't exist
                if (!is_dir('../uploads/')) {
                    mkdir('../uploads/', 0755, true);
                    echo "<p>✅ Created uploads directory</p>";
                } else {
                    echo "<p>✅ Uploads directory exists</p>";
                }
                
                // Move uploaded file
                $target_path = "../uploads/" . $_FILES["userImage"]["name"];
                echo "<p>Target path: " . $target_path . "</p>";
                
                if (move_uploaded_file($_FILES["userImage"]["tmp_name"], $target_path)) {
                    echo "<p><strong>✅ File moved successfully!</strong></p>";
                    
                    $location = "uploads/" . $_FILES["userImage"]["name"];
                    echo "<p>Database location: " . $location . "</p>";
                    
                    // Update register table photo column (PRIMARY)
                    $sql = "UPDATE register SET photo='$location' WHERE matric_no='$matric_no'";
                    echo "<p>SQL Query: " . $sql . "</p>";
                    
                    if (mysqli_query($conn, $sql)) {
                        echo "<p><strong>✅ Register table updated!</strong></p>";
                        echo "<p>Affected rows: " . mysqli_affected_rows($conn) . "</p>";
                        
                        // Also update students table for consistency
                        $sql_students = "UPDATE students SET photo='$location' WHERE matric_no='$matric_no'";
                        echo "<p>Students SQL: " . $sql_students . "</p>";
                        
                        if (mysqli_query($conn, $sql_students)) {
                            echo "<p><strong>✅ Students table updated!</strong></p>";
                            echo "<p>Students affected rows: " . mysqli_affected_rows($conn) . "</p>";
                        } else {
                            echo "<p>❌ Students table update failed: " . mysqli_error($conn) . "</p>";
                        }
                        
                        // Update session photo as well
                        $_SESSION['photo'] = $location;
                        echo "<p><strong>✅ Session updated!</strong></p>";
                        
                        echo "<p><strong>🎉 SUCCESS! Photo upload completed!</strong></p>";
                        
                    } else {
                        echo "<p>❌ Database update failed: " . mysqli_error($conn) . "</p>";
                    }
                } else {
                    echo "<p>❌ Failed to move uploaded file</p>";
                    echo "<p>Check permissions on uploads directory</p>";
                }
            } else {
                echo "<p>❌ File upload error: " . $_FILES['userImage']['error'] . "</p>";
            }
        } else {
            echo "<p>❌ No userImage file found in upload</p>";
        }
    } else {
        echo "<p>❌ btnedit button not clicked</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Profile Upload</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-container { background: #f5f5f5; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .btn { background: #8B5A2B; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #A0522D; }
        input[type="file"] { margin: 10px 0; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>Test Photo Upload Form</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <p>
                <label>Select Photo:</label><br>
                <input name="userImage" type="file" accept="image/*" required>
            </p>
            <p>
                <button type="submit" name="btnedit" class="btn">Upload Photo</button>
            </p>
        </form>
    </div>
    
    <p><a href="profile.php">← Back to Profile</a></p>
    
    <h3>Current Session Data:</h3>
    <pre><?php print_r($_SESSION); ?></pre>
    
    <h3>Current Database Data:</h3>
    <?php
    if (!empty($_SESSION['matric_no'])) {
        $matric_no = $_SESSION['matric_no'];
        
        echo "<h4>Register Table:</h4>";
        $result = mysqli_query($conn, "SELECT * FROM register WHERE matric_no='$matric_no'");
        if ($row = mysqli_fetch_assoc($result)) {
            echo "<pre>" . print_r($row, true) . "</pre>";
        } else {
            echo "<p>No data found in register table</p>";
        }
        
        echo "<h4>Students Table:</h4>";
        $result = mysqli_query($conn, "SELECT * FROM students WHERE matric_no='$matric_no'");
        if ($row = mysqli_fetch_assoc($result)) {
            echo "<pre>" . print_r($row, true) . "</pre>";
        } else {
            echo "<p>No data found in students table</p>";
        }
    }
    ?>
</body>
</html>