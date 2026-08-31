<?php
include('../connect.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Photo Display Test</title>
    <style>
        .img-circle { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #8B5A2B; }
        .default-avatar-small { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #8B5A2B, #A0522D); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; }
        .test-row { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <h2>Photo Display Test - Computer Science and Engineering Students</h2>
    
    <?php
    // Test the exact same query and logic as the main page
    $query_students = "
        SELECT 
            s.*,
            COALESCE(r.photo, s.photo) as photo,
            COALESCE(r.fullname, s.fullname) as fullname,
            COALESCE(r.matric_no, s.matric_no) as matric_no
        FROM students s
        LEFT JOIN register r ON s.matric_no = r.matric_no
        WHERE s.dept='Computer Science and Engineering'
        ORDER BY COALESCE(r.fullname, s.fullname) ASC
        LIMIT 5
    ";
    
    $result_students = mysqli_query($conn, $query_students);
    
    if ($result_students && mysqli_num_rows($result_students) > 0) {
        while($row = mysqli_fetch_assoc($result_students)) {
            echo "<div class='test-row'>";
            echo "<h4>{$row['fullname']} ({$row['matric_no']})</h4>";
            echo "<p><strong>Photo from DB:</strong> " . htmlspecialchars($row['photo']) . "</p>";
            
            // EXACT SAME LOGIC AS MAIN PAGE
            $photo = $row['photo'];
            $student_photo = $photo;
            if (!empty($student_photo) && !str_starts_with($student_photo, 'http') && !str_starts_with($student_photo, '../')) {
                if (!str_starts_with($student_photo, 'uploads/')) {
                    $student_photo = '../' . $student_photo;
                } else {
                    $student_photo = '../' . $student_photo;
                }
            }
            
            $image_url = htmlspecialchars($student_photo);
            if (strpos($image_url, '?') === false) {
                $image_url .= '?v=' . time();
            }
            
            echo "<p><strong>Final Image URL:</strong> " . $image_url . "</p>";
            echo "<p><strong>File Exists:</strong> " . (file_exists($student_photo) ? "✅ YES" : "❌ NO") . "</p>";
            
            echo "<div style='margin: 10px 0;'>";
            if (!empty($student_photo)) {
                echo "<img src=\"{$image_url}\" alt=\"Student Photo\" width=\"60\" height=\"60\" class=\"img-circle\" onerror=\"this.style.display='none'; this.nextElementSibling.style.display='flex';\">";
                echo "<div class=\"default-avatar-small\" style=\"display: none;\">
                        <i class=\"fa fa-user\"></i>
                      </div>";
            } else {
                echo "<div class=\"default-avatar-small\">
                        <i class=\"fa fa-user\"></i>
                      </div>";
            }
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No students found in Computer Science and Engineering department</p>";
    }
    ?>
    
    <hr>
    <p><a href="check_current_photos.php">→ Check Current Photo Status</a></p>
    <p><a href="update_photos_direct.php">→ Update Photos</a></p>
    <p><a href="index.php?dept=Computer Science and Engineering">→ View Main Student List</a></p>
</body>
</html>