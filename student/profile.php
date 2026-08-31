<?php
session_start();
error_reporting(1);
include('../connect.php');

// Access control
if (empty($_SESSION['matric_no'])) {
    header("Location: ../login student/login.php");
    exit();
}

$matric_no = $_SESSION['matric_no'];
$message = '';
$error = '';

// Handle profile update - Using working edit-photo.php method
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    
    // Get current photo path
    $current_photo = '';
    $photo_stmt = $conn->prepare("SELECT photo FROM students WHERE matric_no = ? LIMIT 1");
    if ($photo_stmt) {
        $photo_stmt->bind_param('s', $matric_no);
        $photo_stmt->execute();
        $photo_result = $photo_stmt->get_result();
        if ($photo_row = $photo_result->fetch_assoc()) {
            $current_photo = $photo_row['photo'] ?? '';
        }
        $photo_stmt->close();
    }
    
    $photo_path = $current_photo; // Keep existing photo by default
    
    // Handle photo upload using the working method from edit-photo.php
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $image = addslashes(file_get_contents($_FILES['profile_photo']['tmp_name']));
        $image_name = addslashes($_FILES['profile_photo']['name']);
        $image_size = getimagesize($_FILES['profile_photo']['tmp_name']);
        
        // Create uploads directory if it doesn't exist
        if (!is_dir('../uploads/')) {
            mkdir('../uploads/', 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], "../uploads/" . $_FILES["profile_photo"]["name"])) {
            $photo_path = "uploads/" . $_FILES["profile_photo"]["name"];
        } else {
            $error = 'Failed to upload photo. Please try again.';
        }
    }
    
    if (!$error && !empty($fullname)) {
        // Update students table first (primary table with all approval data)
        $stmt = $conn->prepare("UPDATE students SET fullname = ?, phone = ?, photo = ? WHERE matric_no = ?");
        if ($stmt) {
            $stmt->bind_param('ssss', $fullname, $phone, $photo_path, $matric_no);
            if ($stmt->execute()) {
                // Update session variables immediately
                $_SESSION['fullname'] = $fullname;
                $_SESSION['phone'] = $phone;
                $_SESSION['photo'] = $photo_path;
                
                // Also update register table if it exists
                $register_stmt = $conn->prepare("UPDATE register SET fullname = ?, phone = ?, photo = ? WHERE matric_no = ?");
                if ($register_stmt) {
                    $register_stmt->bind_param('ssss', $fullname, $phone, $photo_path, $matric_no);
                    $register_stmt->execute();
                    $register_stmt->close();
                }
                
                $message = 'Profile updated successfully!';
                
            } else {
                $error = 'Failed to update profile: ' . $conn->error;
            }
            $stmt->close();
        } else {
            $error = 'Failed to prepare update statement: ' . $conn->error;
        }
    } elseif (empty($fullname)) {
        $error = 'Full name is required.';
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password === $confirm_password) {
        // Verify current password from register table first, then students table
        $stmt = $conn->prepare("SELECT password FROM register WHERE matric_no = ?");
        if ($stmt) {
            $stmt->bind_param('s', $matric_no);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            
            // If not found in register table, try students table
            if (!$user) {
                $stmt = $conn->prepare("SELECT password FROM students WHERE matric_no = ?");
                if ($stmt) {
                    $stmt->bind_param('s', $matric_no);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    $stmt->close();
                }
            }
            
            if ($user && $user['password'] === $current_password) {
                // Update password in both tables
                $update_stmt = $conn->prepare("UPDATE register SET password = ? WHERE matric_no = ?");
                if ($update_stmt) {
                    $update_stmt->bind_param('ss', $new_password, $matric_no);
                    $update_stmt->execute();
                    $update_stmt->close();
                }
                
                $update_stmt = $conn->prepare("UPDATE students SET password = ? WHERE matric_no = ?");
                if ($update_stmt) {
                    $update_stmt->bind_param('ss', $new_password, $matric_no);
                    $update_stmt->execute();
                    $update_stmt->close();
                    $message = 'Password changed successfully!';
                } else {
                    $error = 'Failed to change password.';
                }
            } else {
                $error = 'Current password is incorrect.';
            }
        }
    } else {
        $error = 'New passwords do not match.';
    }
}

// Get student data from students table (primary table with all data)
$student = [];
$stmt = $conn->prepare("SELECT * FROM students WHERE matric_no = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param('s', $matric_no);
    $stmt->execute();
    $res = $stmt->get_result();
    $student = $res->fetch_assoc() ?: [];
    $stmt->close();
}

// If no data in students table, try register table as fallback
if (empty($student)) {
    $stmt = $conn->prepare("SELECT * FROM register WHERE matric_no = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $matric_no);
        $stmt->execute();
        $res = $stmt->get_result();
        $student = $res->fetch_assoc() ?: [];
        $stmt->close();
    }
}

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Get student photo - prioritize register table, fallback to students table, then session
$student_photo = '';
if (!empty($student['photo'])) {
    $student_photo = $student['photo'];
} elseif (!empty($_SESSION['photo'])) {
    $student_photo = $_SESSION['photo'];
}

// Ensure photo path is properly formatted
if (!empty($student_photo) && !str_starts_with($student_photo, 'http') && !str_starts_with($student_photo, '../')) {
    // Add relative path if not already present
    if (!str_starts_with($student_photo, 'uploads/')) {
        $student_photo = '../' . $student_photo;
    } else {
        $student_photo = '../' . $student_photo;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>My Profile | Student Portal</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
    
    <style>
        :root {
            --university-primary: #8B5A2B;
            --university-primary-dark: #A0522D;
            --university-primary-light: #D2B48C;
            --university-accent: #CD853F;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-radius: 16px;
            --shadow-sm: 0 2px 8px rgba(139, 90, 43, 0.1);
            --shadow-md: 0 4px 16px rgba(139, 90, 43, 0.15);
            --shadow-lg: 0 8px 30px rgba(139, 90, 43, 0.2);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(139, 90, 43, 0.1);
            --glass-shadow: 0 8px 32px rgba(139, 90, 43, 0.1);
        }

        body {
            background: linear-gradient(-45deg, var(--university-primary-light), #f8fafc, var(--university-accent), #ffffff);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            color: var(--text-primary);
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .main-content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }

        .profile-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: var(--glass-shadow);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(139, 90, 43, 0.1), transparent);
            transition: var(--transition);
        }

        .profile-header:hover::before {
            left: 100%;
        }

        .profile-header h2 {
            font-size: 28px;
            font-weight: 900;
            color: var(--university-primary-dark);
            margin: 16px 0 0 0;
            text-shadow: 0 2px 4px rgba(139, 90, 43, 0.2);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--university-primary-light);
            margin-bottom: 16px;
            transition: var(--transition);
            animation: profilePulse 3s ease-in-out infinite;
        }

        .profile-avatar:hover {
            transform: scale(1.1);
            border-color: var(--university-primary);
        }

        .profile-avatar-container {
            position: relative;
            display: inline-block;
        }

        .default-profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            border: 4px solid rgba(139, 90, 43, 0.2);
            box-shadow: 0 8px 25px rgba(139, 90, 43, 0.3);
            transition: var(--transition);
            animation: profilePulse 4s ease-in-out infinite;
        }

        .default-profile-avatar:hover {
            transform: scale(1.1);
            border-color: var(--university-primary);
            box-shadow: 0 12px 35px rgba(139, 90, 43, 0.4);
        }

        @keyframes profilePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(139, 90, 43, 0.7); }
            50% { box-shadow: 0 0 0 20px rgba(139, 90, 43, 0); }
        }

        .profile-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--glass-shadow);
            transition: var(--transition);
        }

        .profile-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(139, 90, 43, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            border-color: var(--university-primary);
            box-shadow: 0 0 0 3px rgba(139, 90, 43, 0.1);
            outline: none;
        }

        /* Enhanced Profile Form Styling */
        .card-header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--glass-border);
        }

        .card-header h4 {
            font-size: 20px;
            font-weight: 700;
            color: var(--university-primary-dark);
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
            font-weight: 500;
        }

        .profile-form {
            padding: 0;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .form-row .col-md-6 {
            padding-left: 8px;
            padding-right: 8px;
        }

        .form-row .col-md-6:first-child {
            padding-left: 0;
        }

        .form-row .col-md-6:last-child {
            padding-right: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .form-label i {
            color: var(--university-primary);
            width: 16px;
            text-align: center;
        }

        .form-control {
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.8);
            position: relative;
        }

        .form-control:focus {
            border-color: var(--university-primary);
            box-shadow: 0 0 0 3px rgba(139, 90, 43, 0.1);
            outline: none;
            background: white;
            transform: translateY(-1px);
        }

        .form-control:hover {
            border-color: var(--university-primary-light);
            background: white;
        }

        .form-help {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 4px;
            font-style: italic;
            opacity: 0.8;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid var(--glass-border);
        }

        .btn-update {
            position: relative;
            overflow: hidden;
            min-width: 140px;
        }

        .btn-update .btn-loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(107, 114, 128, 0.2);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
            background: linear-gradient(135deg, #4b5563, #374151);
            color: white;
        }

        .form-footer {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--glass-border);
            text-align: center;
        }

        .form-footer small {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--text-secondary);
        }

        .form-footer i {
            color: var(--university-primary);
        }

        /* Enhanced Photo Upload Styling */
        .photo-upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            padding: 20px;
            background: rgba(139, 90, 43, 0.03);
            border: 2px dashed var(--glass-border);
            border-radius: 16px;
            transition: var(--transition);
        }

        .photo-upload-container:hover {
            border-color: var(--university-primary-light);
            background: rgba(139, 90, 43, 0.06);
        }

        .current-photo {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            transition: var(--transition);
        }

        .current-photo:hover {
            transform: scale(1.05);
        }

        .current-photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 3px solid var(--university-primary-light);
            border-radius: 50%;
            transition: var(--transition);
        }

        .default-photo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            border: 3px solid var(--university-primary-light);
            border-radius: 50%;
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transition: var(--transition);
            border-radius: 50%;
        }

        .current-photo:hover .photo-overlay {
            opacity: 1;
        }

        .photo-overlay i {
            font-size: 24px;
            margin-bottom: 4px;
        }

        .photo-overlay span {
            font-size: 12px;
            font-weight: 600;
        }

        .photo-upload-controls {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .photo-input {
            display: none;
        }

        .btn-photo-upload {
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(139, 90, 43, 0.2);
        }

        .btn-photo-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3);
            background: linear-gradient(135deg, var(--university-primary-dark), var(--university-primary));
        }

        .btn-photo-remove {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
        }

        .btn-photo-remove:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        .photo-requirements {
            text-align: center;
            color: var(--text-secondary);
        }

        .photo-requirements small {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
        }

        .photo-requirements i {
            color: var(--university-primary);
        }

        /* Photo Upload Animation */
        .photo-uploading .current-photo {
            position: relative;
            overflow: visible;
        }

        .photo-uploading .current-photo::after {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border: 3px solid var(--university-primary);
            border-top-color: transparent;
            border-radius: 50%;
            animation: photoUploadSpin 1s linear infinite;
        }

        @keyframes photoUploadSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .form-control {
            position: relative;
        }

        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
            transition: var(--transition);
        }

        .form-control:focus::placeholder {
            opacity: 0.5;
            transform: translateY(-2px);
        }

        /* Form Validation Styling */
        .form-control:valid {
            border-color: var(--success-color);
        }

        .form-control:invalid:not(:placeholder-shown) {
            border-color: var(--danger-color);
        }

        .form-control:valid:not(:placeholder-shown) {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2310b981' d='m2.3 6.73.94-.94 2.94 2.94L8.5 6.4l.94.94L6.5 10.27z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        /* Loading State */
        .btn-update.loading span {
            opacity: 0;
        }

        .btn-update.loading .btn-loader {
            display: block !important;
        }

        /* Responsive Form Design */
        @media (max-width: 768px) {
            .form-row .col-md-6 {
                padding-left: 0;
                padding-right: 0;
                margin-bottom: 16px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-update,
            .btn-secondary {
                width: 100%;
            }
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            transition: var(--transition);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 90, 43, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 16px 20px;
            margin-bottom: 20px;
            animation: slideInFromTop 0.5s ease-out;
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
        }

        @keyframes slideInFromTop {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-secondary);
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 500;
        }

        .tab-nav {
            display: flex;
            background: var(--glass-bg);
            border-radius: var(--border-radius);
            padding: 4px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }

        .tab-btn {
            flex: 1;
            padding: 12px 16px;
            border: none;
            background: transparent;
            border-radius: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            transition: var(--transition);
            cursor: pointer;
        }

        .tab-btn.active {
            background: var(--university-primary);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            
            .profile-header {
                padding: 24px 16px;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
            }

            .default-profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }
        }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <!-- Profile Header -->
        <div class="profile-header">
        <div class="profile-image-container">
            <?php 
            // Add cache busting parameter to force image refresh
            $image_url = htmlspecialchars($student_photo);
            if (strpos($image_url, '?') === false) {
                $image_url .= '?v=' . time();
            }
            ?>
            <img src="<?php echo $image_url; ?>" alt="Student Photo" class="profile-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="default-avatar" style="display: none;">
                <i class="fa fa-user"></i>
            </div>
        </div>
            <h2><?php echo e($_SESSION['fullname'] ?? $student['fullname'] ?? 'Student'); ?></h2>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> <?php echo e($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <!-- Tab Navigation -->
        <div class="tab-nav">
            <button class="tab-btn active" onclick="showTab('profile')">
                <i class="fa fa-user"></i> Profile Information
            </button>
            <button class="tab-btn" onclick="showTab('security')">
                <i class="fa fa-lock"></i> Security Settings
            </button>
        </div>

        <!-- Profile Information Tab -->
        <div id="profile-tab" class="tab-content active">
            <div class="row">
                <div class="col-md-6">
                    <div class="profile-card">
                        <h4 class="mb-3"><i class="fa fa-info-circle"></i> Personal Information</h4>
                        <div class="info-item">
                            <span class="info-label">Full Name:</span>
                            <span class="info-value"><?php echo htmlspecialchars($_SESSION['fullname'] ?? $student['fullname'] ?? 'Not set'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">ID Number:</span>
                            <span class="info-value"><?php echo htmlspecialchars($matric_no); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Faculty:</span>
                            <span class="info-value"><?php echo htmlspecialchars($_SESSION['faculty'] ?? $student['faculty'] ?? 'Not set'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Department:</span>
                            <span class="info-value"><?php echo htmlspecialchars($_SESSION['dept'] ?? $student['dept'] ?? 'Not set'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="profile-card">
                        <div class="card-header">
                            <h4><i class="fa fa-camera"></i> Update Profile Photo</h4>
                            <p class="card-subtitle">Upload a new profile picture</p>
                        </div>
                        <!-- Photo Upload Link - Redirect to Working Edit Photo Page -->
                        <div class="upload-form" style="max-width: 400px; margin: 0 auto; text-align: center;">
                            <a href="edit-photo.php" class="btn btn-update" style="background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark)); color: white; border: none; border-radius: 12px; padding: 14px 32px; font-weight: 600; font-size: 16px; width: 100%; transition: var(--transition); box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3); text-decoration: none; display: inline-block; text-align: center;">
                                <i class="fa fa-image"></i>
                                Select New Photo
                            </a>
                            
                            <!-- Link to password change -->
                            <div class="text-center mt-3">
                                <small><a href="#" onclick="showTab('security')" style="color: var(--university-primary);">Need to change password? Click here</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings Tab -->
        <div id="security-tab" class="tab-content">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="profile-card">
                        <h4 class="mb-3"><i class="fa fa-key"></i> Change Password</h4>
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="change_password" class="class="btn btn-update" style="background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark)); color: white; border: none; border-radius: 12px; padding: 14px 32px; font-weight: 600; font-size: 16px; width: 100%; transition: var(--transition); box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3); text-decoration: none; display: inline-block; text-align: center;">
                                <i class="fa fa-lock"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-update" style="background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark)); color: white; border: none; border-radius: 12px; padding: 14px 32px; font-weight: 600; font-size: 16px; width: 100%; transition: var(--transition); box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3); text-decoration: none; display: inline-block; text-align: center;">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script src="../js/jquery-2.1.1.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }

        // Enhanced form functionality
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            const resetBtn = document.querySelector('.btn-reset');
            const updateBtn = document.querySelector('.btn-update');
            
            // Form submission with loading state and immediate UI updates
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && submitBtn.classList.contains('btn-update')) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                        
                        // Get form data for immediate UI update
                        const formData = new FormData(this);
                        const fullname = formData.get('fullname');
                        const photoFile = formData.get('profile_photo');
                        
                        // Update UI immediately if name changed
                        if (fullname) {
                            updateProfileName(fullname);
                        }
                        
                        // Update photo preview if new photo selected
                        if (photoFile && photoFile.size > 0) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                updateProfilePhoto(e.target.result);
                            };
                            reader.readAsDataURL(photoFile);
                        }
                        
                        // Re-enable after 3 seconds (fallback)
                        setTimeout(() => {
                            submitBtn.classList.remove('loading');
                            submitBtn.disabled = false;
                        }, 3000);
                    }
                });
            });
            
            // Function to update profile name in sidebar and header
            function updateProfileName(newName) {
                // Update sidebar profile name
                const sidebarName = document.querySelector('.profile-info h4');
                if (sidebarName) {
                    sidebarName.textContent = newName;
                }
                
                // Update profile header name
                const headerName = document.querySelector('.profile-header h2');
                if (headerName) {
                    headerName.textContent = newName;
                }
            }
            
            // Function to update profile photo in sidebar and header
            function updateProfilePhoto(newPhotoSrc) {
                // Update sidebar profile photo
                const sidebarPhoto = document.querySelector('.profile-image');
                if (sidebarPhoto) {
                    sidebarPhoto.src = newPhotoSrc;
                    sidebarPhoto.style.display = 'block';
                    const placeholder = sidebarPhoto.nextElementSibling;
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                }
                
                // Update profile header photo
                const headerPhoto = document.querySelector('.profile-avatar');
                if (headerPhoto) {
                    headerPhoto.src = newPhotoSrc;
                    headerPhoto.style.display = 'block';
                    const placeholder = headerPhoto.nextElementSibling;
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                }
            }
            
            // Reset form functionality
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    const form = this.closest('form');
                    if (form) {
                        // Reset form to original values
                        form.reset();
                        
                        // Show confirmation
                        this.innerHTML = '<i class="fa fa-check"></i> Reset Complete';
                        this.style.background = 'linear-gradient(135deg, var(--success-color), #059669)';
                        
                        setTimeout(() => {
                            this.innerHTML = '<i class="fa fa-refresh"></i> Reset Changes';
                            this.style.background = '';
                        }, 2000);
                    }
                });
            }
            
            // Real-time form validation
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Remove any existing validation classes
                    this.classList.remove('is-valid', 'is-invalid');
                    
                    // Add validation class based on validity
                    if (this.value.trim() !== '') {
                        if (this.checkValidity()) {
                            this.classList.add('is-valid');
                        } else {
                            this.classList.add('is-invalid');
                        }
                    }
                });
                
                // Enhanced focus effects
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
            
            // Phone number formatting
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.startsWith('251')) {
                        value = value.substring(3);
                    }
                    if (value.length > 0) {
                        if (value.length <= 3) {
                            this.value = '+251-' + value;
                        } else if (value.length <= 6) {
                            this.value = '+251-' + value.substring(0, 3) + '-' + value.substring(3);
                        } else {
                            this.value = '+251-' + value.substring(0, 3) + '-' + value.substring(3, 6) + '-' + value.substring(6, 9);
                        }
                    }
                });
            }
            
            // Auto-save indication
            let saveTimeout;
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(saveTimeout);
                    
                    // Show "changes detected" indicator
                    if (updateBtn) {
                        updateBtn.innerHTML = '<i class="fa fa-exclamation-circle"></i> <span>Changes Detected</span>';
                        updateBtn.style.background = 'linear-gradient(135deg, var(--warning-color), #d97706)';
                    }
                    
                    // Reset after 3 seconds of no changes
                    saveTimeout = setTimeout(() => {
                        if (updateBtn) {
                            updateBtn.innerHTML = '<i class="fa fa-save"></i> <span>Update Profile</span>';
                            updateBtn.style.background = '';
                        }
                    }, 3000);
                });
            });
        });

        // Photo upload functions - Using working method from edit-photo.php
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('current-photo-preview');
                    const placeholder = preview.nextElementSibling;
                    
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    
                    // Add upload animation
                    const container = preview.closest('.photo-upload-container');
                    container.classList.add('photo-uploading');
                    
                    setTimeout(() => {
                        container.classList.remove('photo-uploading');
                    }, 1500);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Alternative display_img function from edit-photo.php for compatibility
        function display_img(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    input.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, PNG, or GIF)');
                    input.value = '';
                    return;
                }
                
                var reader = new FileReader();
                reader.onload = function (e) {
                    // Update the photo preview
                    const photoPreview = document.getElementById('photo-preview');
                    if (photoPreview) {
                        photoPreview.src = e.target.result;
                        photoPreview.style.display = 'block';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removePhoto() {
            const input = document.getElementById('profile-photo-input');
            const preview = document.getElementById('current-photo-preview');
            const placeholder = preview.nextElementSibling;
            
            // Clear file input
            input.value = '';
            
            // Show placeholder
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
            
            // Show confirmation
            const removeBtn = document.querySelector('.btn-photo-remove');
            const originalText = removeBtn.innerHTML;
            removeBtn.innerHTML = '<i class="fa fa-check"></i> Removed';
            removeBtn.style.background = 'linear-gradient(135deg, var(--success-color), #059669)';
            
            setTimeout(() => {
                removeBtn.innerHTML = originalText;
                removeBtn.style.background = '';
            }, 2000);
        }
    </script>
</body>
</html>