<?php
session_start();
include('connect.php');
error_reporting(0);

if (isset($_POST['btnlogin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['txtusername']);
    $password = mysqli_real_escape_string($conn, $_POST['txtpassword']);

    // First check if user exists
    $check_query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $user = mysqli_fetch_array($check_result);
        
        // Check if account is active
        if ($user['status'] == 'Active') {
            // ✅ Store all important session data
            $_SESSION['admin-username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['department'] = $user['department'];
            $_SESSION['photo'] = $user['photo'];

            if ($user['role'] == 'super_admin') {
                header("Location: ../super_admin/super_admin.php");
            } elseif ($user['role'] == 'department_head') {
                header("Location: department/index.php");
            } elseif ($user['role'] == 'library') {
                header("Location: library/index.php");
            } elseif ($user['role'] == 'library_chief') {
                header("Location: office/index.php");
            } elseif ($user['role'] == 'registrar') {
                header("Location: registrar/registrar.php");
            } elseif ($user['role'] == 'dean') {
                header("Location: dean/dean.php");
            } elseif ($user['role'] == 'police') {
                header("Location: police/police.php");
            } elseif ($user['role'] == 'sport') {
                header("Location: sport/sport.php");
            } elseif ($user['role'] == 'bookstore') {
                header("Location: bookstore/bookstore.php");
            } elseif ($user['role'] == 'cafeteria') {
                header("Location: cafeteria/cafeteria.php");
            } elseif ($user['role'] == 'dormitory') {
                header("Location: dormitory/dormitory.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            // Account exists but is inactive
            $_SESSION['error'] = 'Your account has been deactivated. Please contact the administrator.';
        }
    } else {
        // Invalid username or password
        $_SESSION['error'] = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - University Management System</title>
    <link rel="icon" type="image/jpg" sizes="16x16" href="images/favicon.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Amazing Professional Admin Login - University Management System */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #8B5A2B;
            --primary-light: #A0522D;
            --primary-dark: #654321;
            --accent-color: #D4AF37;
            --text-primary: #2D3748;
            --text-secondary: #718096;
            --text-light: #A0AEC0;
            --bg-primary: #F7FAFC;
            --bg-secondary: #EDF2F7;
            --white: #FFFFFF;
            --success: #48BB78;
            --error: #F56565;
            --warning: #ED8936;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --shadow-heavy: rgba(0, 0, 0, 0.25);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, 
                var(--primary-color) 0%, 
                var(--primary-light) 25%, 
                var(--accent-color) 50%, 
                var(--primary-dark) 75%, 
                var(--primary-color) 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.7;
            animation: float 6s ease-in-out infinite;
        }

        .orb-1 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, var(--accent-color), transparent);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, var(--primary-light), transparent);
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .orb-3 {
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent);
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(10px) rotate(240deg); }
        }

        /* Floating Particles */
        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: particleFloat 5s linear infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loading-content {
            text-align: center;
            color: white;
        }

        .loading-logo {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
        }

        .logo-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid transparent;
            border-top: 3px solid var(--accent-color);
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }

        .logo-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            color: var(--accent-color);
        }

        .logo-center svg {
            width: 100%;
            height: 100%;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            opacity: 0;
            animation: fadeInUp 1s ease 0.5s forwards;
        }

        .loading-bar {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            margin: 0 auto;
            overflow: hidden;
        }

        .loading-progress {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, var(--accent-color), white);
            border-radius: 2px;
            transition: width 1.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Error Notification */
        .error-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(245, 101, 101, 0.95);
            backdrop-filter: blur(10px);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(245, 101, 101, 0.3);
            z-index: 1000;
            animation: slideInRight 0.5s ease;
        }

        .error-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-icon {
            width: 20px;
            height: 20px;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Main Container */
        .main-container {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 2;
        }

        /* Brand Panel */
        .brand-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .brand-content {
            text-align: center;
            color: white;
            z-index: 2;
            position: relative;
        }

        .university-logo {
            margin-bottom: 40px;
        }

        .logo-animation {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto;
        }

        .logo-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 2px solid transparent;
            border-radius: 50%;
            animation: rotate 10s linear infinite;
        }

        .ring-1 {
            border-top: 2px solid var(--accent-color);
            animation-duration: 8s;
        }

        .ring-2 {
            border-right: 2px solid rgba(255, 255, 255, 0.5);
            animation-duration: 12s;
            animation-direction: reverse;
        }

        .ring-3 {
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            animation-duration: 15s;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .logo-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color);
            animation: pulse 3s ease-in-out infinite;
            overflow: hidden;
            padding: 10px;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .logo-center svg {
            width: 50px;
            height: 50px;
            display: none;
        }

        .logo-center:hover .brand-logo {
            transform: scale(1.05);
            border-color: var(--accent-color);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
        }

        .logo-center:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent-color);
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.3);
        }

        /* Show SVG if image fails to load */
        .logo-center .brand-logo[style*="display: none"] + svg,
        .logo-center svg:only-child {
            display: block;
        }

        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.1); }
        }

        .brand-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .title-line {
            display: block;
            opacity: 0;
            transform: translateY(30px);
            animation: slideInUp 1s ease forwards;
        }

        .title-line:nth-child(1) { animation-delay: 0.2s; }
        .title-line:nth-child(2) { animation-delay: 0.4s; }
        .title-line:nth-child(3) { animation-delay: 0.6s; }

        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand-subtitle {
            font-size: 1.2rem;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 50px;
            animation: fadeIn 1s ease 0.8s forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            to { opacity: 0.9; }
        }

        .feature-highlights {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 300px;
            margin: 0 auto;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateX(-30px);
            animation: slideInLeft 0.8s ease forwards;
        }

        .feature-item:nth-child(1) { animation-delay: 1s; }
        .feature-item:nth-child(2) { animation-delay: 1.2s; }
        .feature-item:nth-child(3) { animation-delay: 1.4s; }

        .feature-item:hover {
            transform: translateX(10px);
            background: rgba(255, 255, 255, 0.15);
        }

        @keyframes slideInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-icon {
            width: 24px;
            height: 24px;
            color: var(--accent-color);
        }

        .feature-item span {
            font-weight: 500;
        }

        /* Login Panel */
        .login-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            animation: slideInRight 1s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.8s ease;
        }

        .glass-card:hover::before {
            left: 100%;
        }

        .card-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .admin-avatar {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
        }

        .avatar-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid var(--accent-color);
            border-radius: 50%;
            animation: spin 8s linear infinite;
        }

        .avatar-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .login-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
            font-weight: 400;
        }

        /* Modern Form */
        .modern-form {
            position: relative;
        }

        .form-group {
            margin-bottom: 30px;
            position: relative;
        }

        .input-wrapper {
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .input-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .input-wrapper.focused::before {
            left: 100%;
        }

        .input-wrapper.focused {
            border-color: var(--accent-color);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
            transform: translateY(-2px);
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .input-wrapper.focused .input-icon {
            color: var(--accent-color);
            transform: translateY(-50%) scale(1.1);
        }

        .input-wrapper input {
            width: 100%;
            background: transparent;
            border: none;
            padding: 20px 24px;
            padding-left: 55px;
            color: white;
            font-size: 16px;
            font-weight: 500;
            outline: none;
            position: relative;
            z-index: 2;
        }

        .input-wrapper input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .input-border {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-color), white);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .input-wrapper.focused .input-border {
            width: 100%;
        }

        /* Password Toggle */
        .password-wrapper {
            padding-right: 60px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
            z-index: 3;
        }

        .password-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--accent-color);
            transform: translateY(-50%) scale(1.05);
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
        }

        .eye-closed {
            display: none;
        }

        .password-toggle.show-password .eye-open {
            display: none;
        }

        .password-toggle.show-password .eye-closed {
            display: block;
        }

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input[type="checkbox"] {
            display: none;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            user-select: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .checkbox-label:hover {
            color: white;
        }

        .custom-checkbox {
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .custom-checkbox::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--accent-color);
            transition: left 0.3s ease;
        }

        .remember-me input[type="checkbox"]:checked + .checkbox-label .custom-checkbox {
            border-color: var(--accent-color);
            background: var(--accent-color);
        }

        .remember-me input[type="checkbox"]:checked + .checkbox-label .custom-checkbox::before {
            left: 0;
        }

        .custom-checkbox svg {
            width: 14px;
            height: 14px;
            color: white;
            opacity: 0;
            transform: scale(0);
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .remember-me input[type="checkbox"]:checked + .checkbox-label .custom-checkbox svg {
            opacity: 1;
            transform: scale(1);
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .back-link:hover {
            color: var(--accent-color);
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-5px);
        }

        .back-link svg {
            width: 16px;
            height: 16px;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--accent-color), #F4D03F);
            border: none;
            border-radius: 16px;
            padding: 18px 32px;
            color: var(--primary-dark);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 
                0 8px 25px rgba(212, 175, 55, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 12px 35px rgba(212, 175, 55, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .btn-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-text {
            transition: opacity 0.3s ease;
        }

        .btn-loader {
            position: absolute;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .submit-btn.loading .btn-text {
            opacity: 0;
        }

        .submit-btn.loading .btn-loader {
            opacity: 1;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(45, 55, 72, 0.3);
            border-top: 2px solid var(--primary-dark);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .btn-ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            pointer-events: none;
        }

        .btn-ripple.animate {
            animation: ripple 0.6s ease-out;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-container {
                flex-direction: column;
            }
            
            .brand-panel {
                min-height: 40vh;
                padding: 30px 20px;
            }
            
            .brand-title {
                font-size: 2.5rem;
            }
            
            .feature-highlights {
                flex-direction: row;
                justify-content: center;
                max-width: none;
            }
            
            .login-panel {
                padding: 30px 20px;
            }
        }

        @media (max-width: 768px) {
            .brand-panel {
                min-height: 30vh;
                padding: 20px;
            }
            
            .brand-title {
                font-size: 2rem;
            }
            
            .feature-highlights {
                flex-direction: column;
                max-width: 300px;
            }
            
            .glass-card {
                padding: 40px 30px;
                border-radius: 20px;
            }
            
            .login-title {
                font-size: 1.8rem;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .brand-panel {
                padding: 15px;
            }
            
            .brand-title {
                font-size: 1.8rem;
            }
            
            .glass-card {
                padding: 30px 20px;
                border-radius: 16px;
            }
            
            .input-wrapper input {
                padding: 18px 20px;
                padding-left: 50px;
            }
            
            .input-icon {
                left: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
    </div>

    <!-- Floating Particles -->
    <div class="floating-particles">
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
        <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
        <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
        <div class="particle" style="left: 60%; animation-delay: 0.5s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 1.5s;"></div>
        <div class="particle" style="left: 80%; animation-delay: 2.5s;"></div>
        <div class="particle" style="left: 90%; animation-delay: 3.5s;"></div>
    </div>

    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-content">
            <div class="loading-logo">
                <div class="logo-ring"></div>
                <div class="logo-center">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
            </div>
            <h3>Admin Portal</h3>
            <div class="loading-bar">
                <div class="loading-progress"></div>
            </div>
        </div>
    </div>

    <!-- Error Notification -->
    <?php if(isset($_SESSION['error'])): ?>
        <div class="error-notification" id="errorNotification">
            <div class="error-content">
                <div class="error-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Brand Panel -->
        <div class="brand-panel">
            <div class="brand-content">
                <div class="university-logo">
                    <div class="logo-animation">
                        <div class="logo-ring ring-1"></div>
                        <div class="logo-ring ring-2"></div>
                        <div class="logo-ring ring-3"></div>
                        <div class="logo-center">
                            <img src="images/logo.png" alt="University Logo" class="brand-logo" onerror="this.style.display='none'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <h1 class="brand-title">
                    <span class="title-line">University</span>
                    <span class="title-line">Management</span>
                    <span class="title-line">System</span>
                </h1>
                <p class="brand-subtitle">Empowering Education Through Technology</p>
                
                <div class="feature-highlights">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4"/>
                                <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                                <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                                <path d="M13 12h3a2 2 0 0 1 2 2v1"/>
                                <path d="M13 12h-3a2 2 0 0 0-2 2v1"/>
                            </svg>
                        </div>
                        <span>Secure Access</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                            </svg>
                        </div>
                        <span>Fast Performance</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <span>Data Protection</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Panel -->
        <div class="login-panel">
            <div class="login-container">
                <div class="glass-card">
                    <div class="card-header">
                        <div class="admin-avatar">
                            <div class="avatar-ring"></div>
                            <div class="avatar-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                        </div>
                        <h2 class="login-title">Admin Portal</h2>
                        <p class="login-subtitle">Welcome back! Please sign in to continue</p>
                    </div>

                    <form class="modern-form" method="POST" action="">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <input type="text" name="txtusername" required autocomplete="off" placeholder="Enter your username">
                                <div class="input-border"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper password-wrapper">
                                <div class="input-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                                    </svg>
                                </div>
                                <input type="password" name="txtpassword" required autocomplete="off" placeholder="Enter your password">
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                    </svg>
                                </button>
                                <div class="input-border"></div>
                            </div>
                        </div>

                        <div class="form-options">
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember" class="checkbox-label">
                                    <div class="custom-checkbox">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </div>
                                    <span>Remember me</span>
                                </label>
                            </div>
                            <a href="../index.php" class="back-link">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 12H5"/>
                                    <path d="M12 19l-7-7 7-7"/>
                                </svg>
                                Back to Home
                            </a>
                        </div>

                        <button type="submit" name="btnlogin" class="submit-btn">
                            <div class="btn-content">
                                <span class="btn-text">Sign In to Admin Portal</span>
                                <div class="btn-loader">
                                    <div class="spinner"></div>
                                </div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Loading screen animation
        window.addEventListener('load', function() {
            const loadingScreen = document.getElementById('loadingScreen');
            const loadingProgress = document.querySelector('.loading-progress');
            
            // Animate progress bar
            setTimeout(() => {
                loadingProgress.style.width = '100%';
            }, 500);
            
            // Hide loading screen
            setTimeout(() => {
                loadingScreen.style.opacity = '0';
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 500);
            }, 2000);
        });

        // Error notification auto-hide
        const errorNotification = document.getElementById('errorNotification');
        if (errorNotification) {
            setTimeout(() => {
                errorNotification.style.opacity = '0';
                errorNotification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    errorNotification.remove();
                }, 500);
            }, 5000);
        }

        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.querySelector('input[name="txtpassword"]');
            const toggleButton = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.add('show-password');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.remove('show-password');
            }
        }

        // Enhanced form interactions
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.modern-form');
            const submitBtn = document.querySelector('.submit-btn');
            const inputs = document.querySelectorAll('.input-wrapper input');

            // Input focus animations
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });

                // Keep focused state if input has value
                if (input.value) {
                    input.parentElement.classList.add('focused');
                }
            });

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                submitBtn.classList.add('loading');
                
                // Create ripple effect
                const rect = submitBtn.getBoundingClientRect();
                const ripple = document.createElement('div');
                ripple.classList.add('btn-ripple');
                ripple.style.width = ripple.style.height = '20px';
                ripple.style.left = '50%';
                ripple.style.top = '50%';
                ripple.style.marginLeft = '-10px';
                ripple.style.marginTop = '-10px';
                
                submitBtn.appendChild(ripple);
                ripple.classList.add('animate');
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });

            // Button hover effects
            submitBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });

            submitBtn.addEventListener('mouseleave', function() {
                if (!this.classList.contains('loading')) {
                    this.style.transform = 'translateY(0)';
                }
            });

            // Floating particles animation
            function createParticle() {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 5 + 's';
                document.querySelector('.floating-particles').appendChild(particle);

                setTimeout(() => {
                    particle.remove();
                }, 5000);
            }

            // Create particles periodically
            setInterval(createParticle, 800);
        });
    </script>
</body>
</html>
