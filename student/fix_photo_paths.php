<?php
// Photo Path Helper Functions
// This file contains functions to handle photo paths consistently across the student portal

function getCorrectPhotoPath($photo_path, $current_directory = 'student') {
    // Handle empty or null photo paths
    if (empty($photo_path)) {
        return '../images/default-avatar.png';
    }
    
    // If it's already a full URL, return as is
    if (strpos($photo_path, 'http') === 0) {
        return $photo_path;
    }
    
    // If it's a default avatar, return correct path
    if (strpos($photo_path, 'default-avatar') !== false) {
        return '../images/default-avatar.png';
    }
    
    // Handle different path formats from database
    $corrected_path = $photo_path;
    
    // Remove any existing ../ prefixes to normalize
    $corrected_path = str_replace('../', '', $corrected_path);
    
    // Add correct prefix based on current directory
    if ($current_directory === 'student') {
        // We're in student/ directory, need to go up one level
        $corrected_path = '../' . $corrected_path;
    } else {
        // We're in root directory
        $corrected_path = $corrected_path;
    }
    
    return $corrected_path;
}

function savePhotoPath($photo_filename, $matric_no, $conn) {
    // Save photo path consistently to both tables
    $relative_path = 'uploads/' . $photo_filename;
    
    // Update register table
    $sql_register = "UPDATE register SET photo = ? WHERE matric_no = ?";
    $stmt_register = $conn->prepare($sql_register);
    if ($stmt_register) {
        $stmt_register->bind_param('ss', $relative_path, $matric_no);
        $stmt_register->execute();
        $stmt_register->close();
    }
    
    // Update students table for consistency
    $sql_students = "UPDATE students SET photo = ? WHERE matric_no = ?";
    $stmt_students = $conn->prepare($sql_students);
    if ($stmt_students) {
        $stmt_students->bind_param('ss', $relative_path, $matric_no);
        $stmt_students->execute();
        $stmt_students->close();
    }
    
    // Update session
    $_SESSION['photo'] = $relative_path;
    
    return $relative_path;
}

function checkAndCreateUploadsDir() {
    $upload_dirs = [
        '../uploads/',
        'uploads/',
        '../uploads/student_photos/'
    ];
    
    foreach ($upload_dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
?>