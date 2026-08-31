<?php
session_start();
error_reporting(1);
include('../connect.php');

// Access control
if (empty($_SESSION['matric_no'])) {
    header("Location: ../login student/login.php");
    exit();
}

// Get session details (set defaults if not present)
$ID = isset($_SESSION["ID"]) ? (int)$_SESSION["ID"] : 0;
$matric_no = $_SESSION["matric_no"];
$dept = isset($_SESSION['dept']) ? $_SESSION['dept'] : '';
$faculty = isset($_SESSION['faculty']) ? $_SESSION['faculty'] : '';

// Helper for safe echo
function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Get student record from register table first, then students table as fallback
$student = [];
$stmt = $conn->prepare("SELECT * FROM register WHERE matric_no = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param('s', $matric_no);
    $stmt->execute();
    $res = $stmt->get_result();
    $student = $res->fetch_assoc() ?: [];
    $stmt->close();
}

// If no data in register table, try students table as fallback
if (empty($student)) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE matric_no = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $matric_no);
        $stmt->execute();
        $res = $stmt->get_result();
        $student = $res->fetch_assoc() ?: [];
        $stmt->close();
    } else {
        die("Failed to prepare student query: " . $conn->error);
    }
}

// If we have data from register table but no approval columns, get approvals from students table
if (!empty($student) && !isset($student['is_department_approved'])) {
    $approval_stmt = $conn->prepare("SELECT is_department_approved, is_library_approved, is_bookstore_approved, is_dormitory_approved, is_cafeteria_approved, is_sport_approved, is_dean_approved, is_police_approved, is_registrar_approved, request_year FROM students WHERE matric_no = ? LIMIT 1");
    if ($approval_stmt) {
        $approval_stmt->bind_param('s', $matric_no);
        $approval_stmt->execute();
        $approval_res = $approval_stmt->get_result();
        $approval_data = $approval_res->fetch_assoc();
        if ($approval_data) {
            // Merge approval data into student array
            $student = array_merge($student, $approval_data);
        }
        $approval_stmt->close();
    }
}

// Provide safe defaults if columns missing
$is_department_approved = isset($student['is_department_approved']) ? (string)$student['is_department_approved'] : "0";
$is_library_approved    = isset($student['is_library_approved']) ? (string)$student['is_library_approved'] : "0";
$is_bookstore_approved  = isset($student['is_bookstore_approved']) ? (string)$student['is_bookstore_approved'] : "0";
$is_dormitory_approved  = isset($student['is_dormitory_approved']) ? (string)$student['is_dormitory_approved'] : "0";
$is_cafeteria_approved  = isset($student['is_cafeteria_approved']) ? (string)$student['is_cafeteria_approved'] : "0";
$is_sport_approved      = isset($student['is_sport_approved']) ? (string)$student['is_sport_approved'] : "0";
$is_dean_approved       = isset($student['is_dean_approved']) ? (string)$student['is_dean_approved'] : "0";
$is_police_approved     = isset($student['is_police_approved']) ? (string)$student['is_police_approved'] : "0";
$is_registrar_approved  = isset($student['is_registrar_approved']) ? (string)$student['is_registrar_approved'] : "0";

$fullname = $student['fullname'] ?? ($student['name'] ?? 'Student');
$photo = $student['photo'] ?? '';

// Calculate progress
$approvalsDone = (int)$is_department_approved + (int)$is_library_approved + (int)$is_bookstore_approved + (int)$is_dormitory_approved + (int)$is_cafeteria_approved + (int)$is_sport_approved + (int)$is_dean_approved + (int)$is_police_approved + (int)$is_registrar_approved;
$approvalsTotal = 9;
$progressPct = $approvalsTotal > 0 ? round(($approvalsDone / $approvalsTotal) * 100) : 0;

// Are all approvals done?
$allCleared = ($is_department_approved === "1"
    && $is_library_approved === "1"
    && $is_bookstore_approved === "1"
    && $is_dormitory_approved === "1"
    && $is_cafeteria_approved === "1"
    && $is_sport_approved === "1"
    && $is_dean_approved === "1"
    && $is_police_approved === "1"
    && $is_registrar_approved === "1");

// Status helper function - same as index.php
function status_badge($flag) {
    if ((string)$flag === "1" || strtolower((string)$flag) === "cleared") {
        return '<span class="status-badge status-cleared"><i class="fa fa-check"></i> Cleared</span>';
    }
    return '<span class="status-badge status-pending"><i class="fa fa-clock-o"></i> Pending</span>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Clearance Status | Student Portal</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
    
    <style>
        /* Include the same CSS variables and styling from index.php */
        :root {
            --university-primary: #8B5A2B;
            --university-primary-dark: #A0522D;
            --university-primary-light: #D2B48C;
            --university-accent: #CD853F;
            --success-color: #10b981;
            --success-dark: #059669;
            --warning-color: #f59e0b;
            --warning-dark: #d97706;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --dark-color: #2d3748;
            --light-color: #f7fafc;
            --border-color: rgba(139, 90, 43, 0.15);
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

        body.dark-mode {
            background: linear-gradient(-45deg, var(--university-primary-dark), #1e293b, var(--university-primary), #475569);
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

        .page-header {
            background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(255, 255, 255, 0.98) 100%);
            backdrop-filter: blur(25px);
            border: 2px solid var(--glass-border);
            border-radius: 20px;
            padding: 32px 40px;
            margin-bottom: 32px;
            box-shadow: 0 12px 40px rgba(139, 90, 43, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: slideInFromTop 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--university-primary), var(--university-accent), var(--university-primary-dark));
            border-radius: 20px 20px 0 0;
        }

        .page-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(139, 90, 43, 0.03), transparent);
            transform: rotate(45deg);
            transition: var(--transition);
            opacity: 0;
        }

        .page-header:hover::after {
            opacity: 1;
            animation: shimmer 2s ease-in-out;
        }

        .page-header:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(139, 90, 43, 0.25), 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--university-primary-dark);
            margin-bottom: 12px;
            text-shadow: 0 2px 4px rgba(139, 90, 43, 0.1);
            letter-spacing: -0.8px;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .page-header h1 i {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3);
            animation: pulse 3s ease-in-out infinite;
        }

        .page-header p {
            font-size: 16px;
            color: var(--text-secondary);
            margin: 0;
            font-weight: 500;
            opacity: 0.9;
            line-height: 1.5;
        }

        .page-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .page-header-info {
            flex: 1;
            min-width: 300px;
        }

        .page-header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .header-badge {
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            box-shadow: 0 3px 8px rgba(139, 90, 43, 0.3);
            animation: bounceIn 1s ease-out;
        }

        @keyframes shimmer {
            0% { transform: rotate(45deg) translateX(-200%); }
            100% { transform: rotate(45deg) translateX(200%); }
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }

        .status-card {
            background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(255, 255, 255, 0.98) 100%);
            backdrop-filter: blur(25px);
            border: 2px solid var(--glass-border);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 12px 40px rgba(139, 90, 43, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            animation: fadeInUp 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--university-primary), var(--success-color), var(--university-accent));
            border-radius: 20px 20px 0 0;
        }

        .status-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(139, 90, 43, 0.02), transparent);
            transform: rotate(45deg);
            transition: var(--transition);
            opacity: 0;
        }

        .status-card:hover::after {
            opacity: 1;
            animation: shimmer 2s ease-in-out;
        }

        .status-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 20px 60px rgba(139, 90, 43, 0.25), 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: var(--university-primary-light);
        }

        .status-card h4 {
            font-size: 20px;
            font-weight: 700;
            color: var(--university-primary-dark);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-shadow: 0 1px 3px rgba(139, 90, 43, 0.1);
            letter-spacing: -0.3px;
        }

        .status-card h4 i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            box-shadow: 0 3px 12px rgba(139, 90, 43, 0.3);
            animation: pulse 3s ease-in-out infinite;
        }

        /* Enhanced Student Information Grid */
        .student-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-top: 8px;
        }

        .info-item {
            background: rgba(139, 90, 43, 0.04);
            border: 1px solid rgba(139, 90, 43, 0.1);
            border-radius: 16px;
            padding: 20px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .info-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--university-primary), var(--university-accent));
            border-radius: 16px 16px 0 0;
        }

        .info-item:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(139, 90, 43, 0.2);
            background: rgba(139, 90, 43, 0.08);
            border-color: var(--university-primary-light);
        }

        .info-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--university-primary);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-label i {
            font-size: 14px;
            opacity: 0.8;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
            word-break: break-word;
        }

        .info-value.not-set {
            color: var(--text-secondary);
            font-style: italic;
            opacity: 0.7;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .student-info-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .info-item {
                padding: 16px;
            }
            
            .status-card {
                padding: 24px 20px;
            }
            
            .status-card h4 {
                font-size: 18px;
            }
        }

        @media (max-width: 576px) {
            .student-info-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .page-header h1 {
                font-size: 24px;
                justify-content: center;
            }
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transition: var(--transition);
            box-shadow: 0 3px 8px rgba(0,0,0,0.12);
        }

        .status-cleared {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .status-pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .progress-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--glass-shadow);
            text-align: center;
        }

        .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(var(--success-color) 0deg, var(--success-color) calc(var(--progress) * 3.6deg), #e5e7eb calc(var(--progress) * 3.6deg));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            position: relative;
        }

        .progress-circle::before {
            content: '';
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--glass-bg);
            position: absolute;
        }

        .progress-text {
            position: relative;
            z-index: 1;
            font-size: 24px;
            font-weight: 800;
            color: var(--university-primary);
        }

        .department-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }

        .department-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--glass-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .department-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(139, 90, 43, 0.1), transparent);
            transition: var(--transition);
        }

        .department-card:hover::before {
            left: 100%;
        }

        .department-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 40px rgba(139, 90, 43, 0.25);
        }

        .department-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-bottom: 16px;
        }

        .department-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .department-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @keyframes slideInFromTop {
            0% { opacity: 0; transform: translateY(-30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            
            .department-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-header-info">
                    <h1>
                        <i class="fa fa-clipboard-list"></i>
                        Clearance Status
                    </h1>
                    <p>Track your clearance progress across all departments and monitor your completion status</p>
                </div>
                <div class="page-header-actions">
                    <div class="header-badge">
                        <?php echo $progressPct; ?>% Complete
                    </div>
                </div>
            </div>
        </div>


        <!-- Student Information -->
        <div class="status-card">
            <h4><i class="fa fa-user"></i> Student Information</h4>
            <div class="student-info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fa fa-id-card"></i>
                        Full Name
                    </div>
                    <div class="info-value">
                        <?php echo e($fullname); ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fa fa-hashtag"></i>
                        Matric Number
                    </div>
                    <div class="info-value">
                        <?php echo e($matric_no); ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fa fa-university"></i>
                        Faculty
                    </div>
                    <div class="info-value <?php echo empty($faculty) && empty($student['faculty']) ? 'not-set' : ''; ?>">
                        <?php echo e($faculty ?: ($student['faculty'] ?? 'Not Set')); ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fa fa-graduation-cap"></i>
                        Department
                    </div>
                    <div class="info-value <?php echo empty($dept) && empty($student['dept']) ? 'not-set' : ''; ?>">
                        <?php echo e($dept ?: ($student['dept'] ?? 'Not Set')); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Status Grid -->
        <div class="department-grid">
            <div class="department-card">
                <div class="department-icon">
                    <i class="fa fa-graduation-cap"></i>
                </div>
                <div class="department-name">Department Head</div>
                <div class="department-status">
                    <?php echo status_badge($is_department_approved); ?>
                    <small class="text-muted">Academic Clearance</small>
                </div>
            </div>

            <div class="department-card">
                <div class="department-icon">
                    <i class="fa fa-book"></i>
                </div>
                <div class="department-name">Library</div>
                <div class="department-status">
                    <?php echo status_badge($is_library_approved); ?>
                    <small class="text-muted">Book Returns</small>
                </div>
            </div>

            <div class="department-card">
                <div class="department-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="department-name">Bookstore</div>
                <div class="department-status">
                    <?php echo status_badge($is_bookstore_approved); ?>
                    <small class="text-muted">Outstanding Payments</small>
                </div>
            </div>

            <div class="department-card">
                <div class="department-icon">
                    <i class="fa fa-home"></i>
                </div>
                <div class="department-name">Dormitory</div>
                <div class="department-status">
                    <?php echo status_badge($is_dormitory_approved); ?>
                    <small class="text-muted">Accommodation</small>
                </div>
            </div>

            <div class="department-card">
                <div class="department-icon">
                    <i class="fa fa-cutlery"></i>
                </div>
                <div class="department-name">Cafeteria</div>
                <div class="department-status">
                    <?php echo status_badge($is_cafeteria_approved); ?>
                    <small class="text-muted">Meal Plans</small>
                </div>
            </div>

            <div class="department-card">
                <div class="department-icon">
                    <i class="fa fa-trophy"></i>
                </div>
                <div class="department-name">Sports</div>
                <div class="department-status">
                    <?php echo status_badge($is_sport_approved); ?>
                    <small class="text-muted">Equipment Returns</small>
                </div>
            </div>

            <div class="department-card">
                <div class="department-icon">
                    <i class="fa fa-university"></i>
                </div>
                <div class="department-name">Dean's Office</div>
                <div class="department-status">
                    <?php echo status_badge($is_dean_approved); ?>
                    <small class="text-muted">Final Approval</small>
                </div>
            </div>

            <div class="department-card">
                <div class="department-icon">
                    <i class="fa fa-shield"></i>
                </div>
                <div class="department-name">Campus Police</div>
                <div class="department-status">
                    <?php echo status_badge($is_police_approved); ?>
                    <small class="text-muted">Security Clearance</small>
                </div>
            </div>

            <div class="department-card">
                <div class="department-icon">
                    <i class="fa fa-file-text"></i>
                </div>
                <div class="department-name">Registrar</div>
                <div class="department-status">
                    <?php echo status_badge($is_registrar_approved); ?>
                    <small class="text-muted">Academic Records</small>
                </div>
            </div>
        </div>

        <?php if ($allCleared): ?>
        <div class="status-card text-center" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: 2px solid #059669;">
            <h3><i class="fa fa-check-circle"></i> Congratulations!</h3>
            <p class="mb-3">Your clearance process is complete. All departments have approved your clearance request.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="../letter.php" target="_blank" class="btn btn-light">
                    <i class="fa fa-download"></i> Download Certificate
                </a>
                <a href="index.php" class="btn btn-outline-light">
                    <i class="fa fa-dashboard"></i> Back to Dashboard
                </a>
            </div>
        </div>
        <?php elseif ($progressPct > 0): ?>
        <div class="status-card text-center" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: 2px solid #d97706;">
            <h4><i class="fa fa-clock-o"></i> Clearance In Progress</h4>
            <p class="mb-3">You have completed <?php echo $approvalsDone; ?> out of <?php echo $approvalsTotal; ?> clearance requirements. Please follow up with the remaining departments.</p>
            <a href="index.php" class="btn btn-light">
                <i class="fa fa-dashboard"></i> View Dashboard
            </a>
        </div>
        <?php else: ?>
        <div class="status-card text-center" style="background: linear-gradient(135deg, #6b7280, #4b5563); color: white; border: 2px solid #4b5563;">
            <h4><i class="fa fa-info-circle"></i> Clearance Not Started</h4>
            <p class="mb-3">You haven't started your clearance process yet. Please submit your clearance request to begin.</p>
            <a href="index.php" class="btn btn-light">
                <i class="fa fa-play"></i> Start Clearance Process
            </a>
        </div>
        <?php endif; ?>
    </div>

    <script src="../js/jquery-2.1.1.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script>
        // Add entrance animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.department-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.style.animation = 'fadeInUp 0.6s ease-out forwards';
            });
        });
    </script>
</body>
</html>