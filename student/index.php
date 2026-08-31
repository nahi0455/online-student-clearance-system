<?php
// student/index.php - Student Dashboard

// SHOW ERRORS for debugging (remove or restrict in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include_once('../connect.php'); // should set $conn (mysqli object)
if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Database connection not found. Make sure connect.php defines \$conn as mysqli object.");
}

$global_open = '0';
$today_open = 0;
$today = date('Y-m-d');
$gs = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key=?");
if ($gs) {
    $key = 'clearance_open';
    $gs->bind_param('s', $key);
    if ($gs->execute()) {
        $rgs = $gs->get_result();
        if ($rgs && ($row = $rgs->fetch_assoc())) { $global_open = $row['setting_value']; }
    }
    $gs->close();
}
$ds = $conn->prepare("SELECT is_open FROM clearance_day_control WHERE date=? LIMIT 1");
if ($ds) {
    $ds->bind_param('s', $today);
    if ($ds->execute()) {
        $rds = $ds->get_result();
        if ($rds && ($row = $rds->fetch_assoc())) { $today_open = (int)$row['is_open']; }
    }
    $ds->close();
}
$clearance_window_open = ($global_open === '1' && $today_open === 1);

// Ensure students table has request_year column
$col = mysqli_query($conn, "SHOW COLUMNS FROM students LIKE 'request_year'");
if ($col && mysqli_num_rows($col) === 0) {
    mysqli_query($conn, "ALTER TABLE students ADD COLUMN request_year INT NULL");
}

$currentYear = (int)date('Y');
$hasRequestedThisYear = false;
$rq = $conn->prepare("SELECT request_year FROM students WHERE matric_no=? LIMIT 1");
if ($rq) {
    $rq->bind_param('s', $_SESSION['matric_no']);
    if ($rq->execute()) { $rrq = $rq->get_result(); if ($rrq && ($row = $rrq->fetch_assoc())) { $hasRequestedThisYear = ((int)($row['request_year'] ?? 0) === $currentYear); } }
    $rq->close();
}

// Access control
if (empty($_SESSION['matric_no'])) {
    header("Location: ../login student/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_request_clearance'])) {
    if (!$clearance_window_open) {
        $_SESSION['error'] = 'Clearance requests are closed by admin.';
        header("Location: index.php");
        exit();
    }
    if ($hasRequestedThisYear) {
        $_SESSION['success'] = 'Clearance already requested for ' . $currentYear;
        header("Location: index.php");
        exit();
    }
    $fullname = $_SESSION['fullname'] ?? '';
    $matric_no = $_SESSION['matric_no'] ?? '';
    $sessionVal = $_SESSION['session'] ?? '';
    $faculty = $_SESSION['faculty'] ?? '';
    $dept = $_SESSION['dept'] ?? '';
    $phone = $_SESSION['phone'] ?? '';
    $photo = $_SESSION['photo'] ?? '';
    $passwordVal = $_SESSION['password'] ?? '';

    if ($passwordVal === '') {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $passwordVal = substr(str_shuffle($chars), 0, 6);
    }

    if ($fullname !== '' && $matric_no !== '') {
        $chk = $conn->prepare("SELECT ID, request_year FROM students WHERE matric_no = ? LIMIT 1");
        if ($chk) {
            $chk->bind_param('s', $matric_no);
            $chk->execute();
            $resChk = $chk->get_result();
            if ($resChk && $resChk->num_rows === 0) {
                $ins = $conn->prepare("INSERT INTO students (fullname, matric_no, password, session, faculty, dept, phone, photo, request_year) VALUES (?,?,?,?,?,?,?,?,?)");
                if ($ins) { $ins->bind_param('ssssssssi', $fullname, $matric_no, $passwordVal, $sessionVal, $faculty, $dept, $phone, $photo, $currentYear); $ins->execute(); }
            }
            $chk->close();
            $updReq = $conn->prepare("UPDATE students SET request_year=? WHERE matric_no=?");
            if ($updReq) { $updReq->bind_param('is', $currentYear, $matric_no); if ($updReq->execute()) { $_SESSION['success'] = 'Clearance request submitted for ' . $currentYear; } else { $_SESSION['error'] = 'Unable to request clearance'; } $updReq->close(); }
        } else { $_SESSION['error'] = 'Unable to request clearance'; }
    } else {
        $_SESSION['error'] = 'Missing profile details';
    }
    header("Location: index.php");
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

// Ensure photo path is properly formatted for display
$student_photo = $photo;
if (!empty($student_photo) && !str_starts_with($student_photo, 'http') && !str_starts_with($student_photo, '../')) {
    // Add relative path if not already present
    if (!str_starts_with($student_photo, 'uploads/')) {
        $student_photo = '../' . $student_photo;
    } else {
        $student_photo = '../' . $student_photo;
    }
}

// Short helper to convert flag to "Cleared"/"Pending" HTML with enhanced badges
function status_label($flag) {
    if ((string)$flag === "1" || strtolower((string)$flag) === "cleared") {
        return '<div align="center"><span class="status-badge status-cleared">Cleared</span></div>';
    }
    return '<div align="center"><span class="status-badge status-pending">Pending</span></div>';
}

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

$approvalsDone = (int)$is_department_approved + (int)$is_library_approved + (int)$is_bookstore_approved + (int)$is_dormitory_approved + (int)$is_cafeteria_approved + (int)$is_sport_approved + (int)$is_dean_approved + (int)$is_police_approved + (int)$is_registrar_approved;
$approvalsTotal = 9;
$progressPct = $approvalsTotal > 0 ? round(($approvalsDone / $approvalsTotal) * 100) : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Student Dashboard | Online Clearance System</title>

<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
<link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
<style>
/* Modern CSS Variables for consistent university theming */
:root {
  /* University Primary Colors - Brown Theme */
  --university-primary: #8B5A2B;
  --university-primary-dark: #A0522D;
  --university-primary-light: #D2B48C;
  --university-accent: #CD853F;
  
  /* University Secondary Colors */
  --university-blue: #007bff;
  --university-blue-dark: #0056b3;
  --university-blue-light: #ccccff;
  
  /* Status Colors - Consistent with Admin */
  --success-color: #10b981;
  --success-dark: #059669;
  --warning-color: #f59e0b;
  --warning-dark: #d97706;
  --danger-color: #ef4444;
  --info-color: #3b82f6;
  
  /* Neutral Colors */
  --dark-color: #2d3748;
  --light-color: #f7fafc;
  --border-color: rgba(139, 90, 43, 0.15);
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  
  /* Design System */
  --border-radius: 16px;
  --shadow-sm: 0 2px 8px rgba(139, 90, 43, 0.1);
  --shadow-md: 0 4px 16px rgba(139, 90, 43, 0.15);
  --shadow-lg: 0 8px 30px rgba(139, 90, 43, 0.2);
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  
  /* Glass Effects */
  --glass-bg: rgba(255, 255, 255, 0.95);
  --glass-border: rgba(139, 90, 43, 0.1);
  --glass-shadow: 0 8px 32px rgba(139, 90, 43, 0.1);
}

/* Modern Light/Dark Theme Background */
body { 
  background: linear-gradient(-45deg, #f8fafc, #e2e8f0, #f1f5f9, #ffffff);
  background-size: 400% 400%;
  animation: gradientShift 15s ease infinite;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  min-height: 100vh;
  transition: all 0.3s ease;
}

/* Dark mode */
body.dark-mode {
  background: linear-gradient(-45deg, #0f172a, #1e293b, #334155, #475569);
  color: #e2e8f0;
}

@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* Enhanced Professional Hero Section */
.hero { 
  background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(255, 255, 255, 0.98) 100%);
  backdrop-filter: blur(25px);
  border: 2px solid var(--glass-border);
  border-radius: 20px;
  padding: 32px 40px;
  display: flex;
  gap: 24px;
  align-items: center;
  margin: 32px 0;
  box-shadow: 0 12px 40px rgba(139, 90, 43, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: var(--transition);
  animation: slideInFromTop 0.8s ease-out;
  position: relative;
  overflow: hidden;
  min-height: 140px;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--university-primary), var(--university-accent), var(--university-primary-dark));
  border-radius: 20px 20px 0 0;
}

.hero::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(45deg, transparent, rgba(139, 90, 43, 0.03), transparent);
  transform: rotate(45deg);
  transition: var(--transition);
  opacity: 0;
}

.hero:hover::after {
  opacity: 1;
  animation: shimmer 2s ease-in-out;
}

.hero:hover {
  transform: translateY(-6px) scale(1.01);
  box-shadow: 0 20px 60px rgba(139, 90, 43, 0.25), 0 8px 25px rgba(0, 0, 0, 0.15);
  border-color: var(--university-primary-light);
}

/* Professional Profile Image Container */
.hero-profile-container {
  position: relative;
  flex-shrink: 0;
}

.hero img { 
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid var(--university-primary-light);
  box-shadow: 0 8px 25px rgba(139, 90, 43, 0.3), 0 0 0 8px rgba(139, 90, 43, 0.1);
  transition: var(--transition);
  animation: profilePulse 3s ease-in-out infinite;
  position: relative;
  background: linear-gradient(135deg, var(--university-primary-light), var(--university-accent));
}

.hero img:hover {
  transform: scale(1.08) rotate(3deg);
  border-color: var(--university-primary);
  box-shadow: 0 12px 35px rgba(139, 90, 43, 0.4), 0 0 0 12px rgba(139, 90, 43, 0.15);
}

/* Default Avatar Styling */
.hero-default-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 40px;
  border: 4px solid var(--university-primary-light);
  box-shadow: 0 8px 25px rgba(139, 90, 43, 0.3), 0 0 0 8px rgba(139, 90, 43, 0.1);
  transition: var(--transition);
  animation: profilePulse 3s ease-in-out infinite;
}

.hero-default-avatar:hover {
  transform: scale(1.08) rotate(-3deg);
  border-color: var(--university-primary);
  box-shadow: 0 12px 35px rgba(139, 90, 43, 0.4), 0 0 0 12px rgba(139, 90, 43, 0.15);
}

/* Professional Status Indicator */
.hero-status-indicator {
  position: absolute;
  bottom: 8px;
  right: 8px;
  width: 20px;
  height: 20px;
  background: linear-gradient(135deg, #10b981, #059669);
  border: 3px solid white;
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
  animation: pulse 2s ease-in-out infinite;
}

/* Enhanced Hero Content */
.hero-content {
  flex: 1;
  min-width: 0;
}

.hero-name {
  font-size: 24px !important;
  font-weight: 800 !important;
  color: var(--university-primary-dark) !important;
  margin-bottom: 8px !important;
  text-shadow: 0 1px 3px rgba(139, 90, 43, 0.1) !important;
  letter-spacing: -0.5px !important;
  line-height: 1.2 !important;
}

.hero-details {
  font-size: 14px !important;
  color: var(--text-secondary) !important;
  font-weight: 500 !important;
  opacity: 0.85 !important;
  line-height: 1.4 !important;
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 12px !important;
  align-items: center !important;
}

.hero-detail-item {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  background: rgba(139, 90, 43, 0.08);
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  color: var(--university-primary-dark);
  transition: var(--transition);
}

.hero-detail-item:hover {
  background: rgba(139, 90, 43, 0.15);
  transform: translateY(-1px);
}

.hero-detail-separator {
  width: 4px;
  height: 4px;
  background: var(--university-accent);
  border-radius: 50%;
  opacity: 0.6;
}

/* Professional Action Buttons */
.hero-actions {
  margin-left: auto;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
  align-items: flex-end;
}

.hero .btn-light {
  background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark)) !important;
  color: white !important;
  border: none !important;
  border-radius: 12px !important;
  padding: 12px 24px !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3) !important;
  transition: var(--transition) !important;
  position: relative !important;
  overflow: hidden !important;
  min-width: 180px !important;
  text-align: center !important;
}

.hero .btn-light::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: var(--transition);
}

.hero .btn-light:hover::before {
  left: 100%;
}

.hero .btn-light:hover {
  transform: translateY(-3px) scale(1.05) !important;
  box-shadow: 0 8px 25px rgba(139, 90, 43, 0.4) !important;
  background: linear-gradient(135deg, var(--university-primary-dark), var(--university-primary)) !important;
  color: white !important;
}

.hero .btn-light:disabled {
  background: linear-gradient(135deg, #9ca3af, #6b7280) !important;
  color: white !important;
  cursor: not-allowed !important;
  transform: none !important;
  box-shadow: 0 2px 8px rgba(156, 163, 175, 0.2) !important;
}

.hero .btn-light:disabled:hover {
  transform: none !important;
  box-shadow: 0 2px 8px rgba(156, 163, 175, 0.2) !important;
}

/* Success State Styling */
.hero-success .btn-light {
  background: linear-gradient(135deg, var(--success-color), #059669) !important;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3) !important;
}

.hero-success .btn-light:hover {
  background: linear-gradient(135deg, #059669, var(--success-color)) !important;
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4) !important;
}

/* Action Button Icons */
.hero .btn-light i {
  margin-right: 8px;
  font-size: 16px;
}

/* Professional Form Styling */
.hero form {
  display: inline-block !important;
  width: 100%;
}

.hero form button {
  width: 100%;
}

/* Enhanced Animations */
@keyframes profilePulse {
  0%, 100% { 
    transform: scale(1); 
    box-shadow: 0 8px 25px rgba(139, 90, 43, 0.3), 0 0 0 8px rgba(139, 90, 43, 0.1);
  }
  50% { 
    transform: scale(1.02); 
    box-shadow: 0 12px 35px rgba(139, 90, 43, 0.4), 0 0 0 12px rgba(139, 90, 43, 0.15);
  }
}

@keyframes shimmer {
  0% { transform: rotate(45deg) translateX(-200%); }
  100% { transform: rotate(45deg) translateX(200%); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero {
    flex-direction: column;
    text-align: center;
    padding: 24px 20px;
    gap: 20px;
  }
  
  .hero-actions {
    margin-left: 0;
    align-items: center;
    width: 100%;
  }
  
  .hero .btn-light {
    min-width: 200px !important;
  }
  
  .hero-details {
    justify-content: center !important;
  }
  
  .hero-name {
    font-size: 20px !important;
  }
}

@keyframes profilePulse {
  0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(139, 90, 43, 0.7); }
  50% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(139, 90, 43, 0); }
}

@keyframes slideInFromTop {
  0% { opacity: 0; transform: translateY(-50px) scale(0.9); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

/* Enhanced cards with advanced animations and glassmorphism */
.ibox { 
  border-radius: var(--border-radius);
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  padding: 20px;
  box-shadow: var(--glass-shadow);
  border: 1px solid var(--glass-border);
  transition: var(--transition);
  animation: fadeInUp 0.6s ease-out;
  position: relative;
  overflow: hidden;
}

.ibox:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 15px 40px rgba(139, 90, 43, 0.25);
  background: rgba(255, 255, 255, 1);
}

@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(30px) scale(0.9); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

/* Enhanced Status Badges - Professional University Style */
.status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 16px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  transition: var(--transition);
  box-shadow: 0 3px 8px rgba(0,0,0,0.12);
  position: relative;
  overflow: hidden;
  min-width: 70px;
  text-align: center;
  animation: bounceIn 0.6s ease-out;
}

.status-cleared {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.status-cleared:hover {
  transform: translateY(-3px) scale(1.1) rotate(2deg);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
}

.status-pending {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
  border: 1px solid rgba(245, 158, 11, 0.2);
}

.status-pending:hover {
  transform: translateY(-3px) scale(1.1) rotate(-2deg);
  box-shadow: 0 8px 25px rgba(245, 158, 11, 0.6);
}

/* Enhanced table styling */
.table thead { 
  background: linear-gradient(135deg, #8B5A2B 0%, #A0522D 100%);
  color: #fff;
}

.table {
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  background: white;
  border: 1px solid rgba(139, 90, 43, 0.1);
}

.table thead th {
  background: linear-gradient(135deg, #8B5A2B 0%, #A0522D 100%) !important;
  color: #fff !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.8px !important;
  font-size: 11px !important;
  position: relative;
  border: none;
  padding: 18px 12px;
  text-align: center;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.table tbody tr {
  transition: var(--transition);
  animation: tableRowSlide 0.6s ease-out;
  border-bottom: 1px solid var(--border-color);
  position: relative;
}

.table tbody tr:nth-child(even) {
  background: rgba(139, 90, 43, 0.02);
}

.table tbody tr:hover {
  background: linear-gradient(135deg, rgba(139, 90, 43, 0.12), rgba(160, 82, 45, 0.08)) !important;
  transform: translateY(-2px) scale(1.01);
  box-shadow: 0 8px 25px rgba(139, 90, 43, 0.2);
  border-color: var(--university-primary-light);
}

.table tbody td {
  padding: 16px 12px;
  vertical-align: middle;
  border-color: var(--border-color);
  font-size: 13px;
  text-align: center;
  font-weight: 500;
  transition: var(--transition);
  color: var(--text-primary);
  position: relative;
}

/* Enhanced layout with modern sidebar */
.layout { 
  display: flex;
  min-height: 100vh;
}

/* Adjust content area for fixed sidebar */
.content { 
  flex: 1;
  min-width: 0;
  padding: 20px;
  margin-left: 280px; /* Account for fixed sidebar width */
  width: calc(100% - 280px);
}

/* Override for student sidebar positioning */
.modern-sidebar {
  position: fixed !important;
  left: 0 !important;
  top: 0 !important;
  z-index: 1000 !important;
}

/* Professional Clearance Window Section */
.clearance-status-card {
  background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(255, 255, 255, 0.98) 100%);
  backdrop-filter: blur(25px);
  border: 2px solid var(--glass-border);
  border-radius: 16px;
  padding: 24px 32px;
  margin: 24px 0;
  box-shadow: 0 8px 30px rgba(139, 90, 43, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.clearance-status-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--university-primary), var(--university-accent), var(--university-primary-dark));
  border-radius: 16px 16px 0 0;
}

.clearance-status-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(139, 90, 43, 0.18), 0 4px 12px rgba(0, 0, 0, 0.12);
}

.status-header {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.status-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 24px;
  box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3);
  flex-shrink: 0;
}

.status-content {
  flex: 1;
  min-width: 200px;
}

.status-content h4 {
  font-size: 18px;
  font-weight: 700;
  color: var(--university-primary-dark);
  margin: 0 0 8px 0;
}

.status-badge-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.window-status {
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: var(--transition);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.window-status.status-open {
  background: linear-gradient(135deg, var(--success-color), #059669);
  color: white;
  animation: pulse 2s ease-in-out infinite;
}

.window-status.status-closed {
  background: linear-gradient(135deg, var(--warning-color), #d97706);
  color: white;
}

.status-details {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
  margin-left: auto;
}

.detail-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 12px 16px;
  background: rgba(139, 90, 43, 0.05);
  border-radius: 12px;
  transition: var(--transition);
  min-width: 100px;
}

.detail-item:hover {
  background: rgba(139, 90, 43, 0.1);
  transform: translateY(-2px);
}

.detail-label {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.detail-value {
  font-size: 14px;
  font-weight: 700;
  color: var(--text-primary);
}

.detail-value.text-success {
  color: var(--success-color);
}

.detail-value.text-warning {
  color: var(--warning-color);
}

/* Professional Progress Section - Clean Design from Clearance_Status.php */
.progress-section {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: var(--border-radius);
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--glass-shadow);
    text-align: center;
    transition: var(--transition);
    animation: fadeInUp 0.6s ease-out;
    position: relative;
    overflow: hidden;
}

.progress-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--university-primary), var(--success-color), var(--university-accent));
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.progress-section:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 12px 40px rgba(139, 90, 43, 0.25);
}

.progress-section h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--university-primary-dark);
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
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
    transition: var(--transition);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.progress-circle::before {
    content: '';
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--glass-bg);
    position: absolute;
}

.progress-circle:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

.progress-text {
    position: relative;
    z-index: 1;
    font-size: 24px;
    font-weight: 800;
    color: var(--university-primary);
    text-shadow: 0 1px 3px rgba(139, 90, 43, 0.2);
}

.progress-section p {
    font-size: 14px;
    color: var(--text-secondary);
    font-weight: 500;
    margin: 0;
}

.progress-section strong {
    color: var(--university-primary);
    font-weight: 700;
}

/* Professional Metrics Cards */
.metrics {
  margin: 32px 0;
}

.metric-card {
  background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(255, 255, 255, 0.98) 100%);
  backdrop-filter: blur(25px);
  border: 2px solid var(--glass-border);
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 12px 40px rgba(139, 90, 43, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: var(--transition);
  position: relative;
  overflow: hidden;
  min-height: 200px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  animation: fadeInUp 0.6s ease-out;
}

.metric-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--university-primary), var(--success-color), var(--university-accent));
  border-radius: 20px 20px 0 0;
}

.metric-card::after {
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

.metric-card:hover::after {
  opacity: 1;
  animation: shimmer 2s ease-in-out;
}

.metric-card:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 20px 60px rgba(139, 90, 43, 0.25), 0 8px 25px rgba(0, 0, 0, 0.15);
  border-color: var(--university-primary-light);
}

.metric-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, var(--success-color), #059669);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 24px;
  margin-bottom: 16px;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
  animation: pulse 3s ease-in-out infinite;
}

.metric-content h5 {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: 16px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.progress-display {
  position: relative;
  margin-bottom: 16px;
}

.progress-text-old {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  flex-direction: column;
  align-items: center;
}

.progress-percentage {
  font-size: 20px;
  font-weight: 800;
  color: var(--university-primary-dark);
}

.clearance-status-summary {
  margin-top: auto;
  padding-top: 16px;
  border-top: 1px solid var(--glass-border);
  width: 100%;
}

.clearance-status-summary h3 {
  font-size: 16px;
  font-weight: 700;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.clearance-status-summary .status-cleared {
  color: var(--success-color);
}

.clearance-status-summary .status-pending {
  color: var(--warning-color);
}

.clearance-status-summary small {
  display: block;
  margin-top: 8px;
  font-size: 12px;
}

.clearance-status-summary small a {
  color: var(--university-primary);
  text-decoration: none;
  font-weight: 600;
}

.clearance-status-summary small a:hover {
  text-decoration: underline;
}

/* Responsive Design for Status Cards */
@media (max-width: 768px) {
  .status-header {
    flex-direction: column;
    text-align: center;
    gap: 16px;
  }
  
  .status-details {
    margin-left: 0;
    justify-content: center;
  }
  
  .detail-item {
    min-width: 80px;
  }
  
  .clearance-status-card {
    padding: 20px 16px;
  }
  
  .metric-card {
    min-height: 180px;
    padding: 20px;
  }
  
  .metric-icon {
    width: 50px;
    height: 50px;
    font-size: 20px;
  }
  
  .progress-percentage {
    font-size: 18px;
  }
}
}

.clearance-status-value {
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: var(--transition);
}

.clearance-status-open {
  background: linear-gradient(135deg, var(--success-color), #059669);
  color: white;
  animation: pulse 2s ease-in-out infinite;
}

.clearance-status-closed {
  background: linear-gradient(135deg, var(--danger-color), #dc2626);
  color: white;
}

.clearance-details {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  font-size: 14px;
  color: var(--text-secondary);
}

.clearance-detail-item {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: rgba(139, 90, 43, 0.08);
  border-radius: 10px;
  font-weight: 500;
  transition: var(--transition);
}

.clearance-detail-item:hover {
  background: rgba(139, 90, 43, 0.15);
  transform: translateY(-1px);
}

.clearance-detail-item i {
  color: var(--university-primary);
  font-size: 12px;
}

.status-on {
  color: var(--success-color);
  font-weight: 600;
}

.status-off {
  color: var(--danger-color);
  font-weight: 600;
}

/* Professional Progress Card */
.progress-card {
  background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(255, 255, 255, 0.98) 100%);
  backdrop-filter: blur(25px);
  border: 2px solid var(--glass-border);
  border-radius: 20px;
  padding: 32px 28px;
  box-shadow: 0 12px 40px rgba(139, 90, 43, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: var(--transition);
  position: relative;
  overflow: hidden;
  text-align: center;
  min-height: 280px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.progress-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--university-primary), var(--university-accent), var(--success-color));
  border-radius: 20px 20px 0 0;
}

.progress-card::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(139, 90, 43, 0.03), transparent);
  transition: var(--transition);
  opacity: 0;
}

.progress-card:hover::after {
  opacity: 1;
  animation: rotateGlow 3s ease-in-out;
}

.progress-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 20px 60px rgba(139, 90, 43, 0.25), 0 8px 25px rgba(0, 0, 0, 0.15);
}

.progress-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--university-primary-dark);
  margin-bottom: 20px;
  text-transform: uppercase;
  letter-spacing: 1px;
  position: relative;
}

.progress-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 40px;
  height: 3px;
  background: linear-gradient(90deg, var(--university-primary), var(--university-accent));
  border-radius: 2px;
}

.progress-percentage {
  font-size: 3.5rem;
  font-weight: 900;
  color: var(--university-primary);
  margin: 16px 0;
  text-shadow: 0 2px 8px rgba(139, 90, 43, 0.2);
  position: relative;
}

.progress-percentage::after {
  content: '%';
  font-size: 2rem;
  color: var(--university-accent);
  margin-left: 4px;
}

/* Enhanced Progress Bar */
.professional-progress {
  height: 16px;
  border-radius: 25px;
  background: linear-gradient(135deg, #e2e8f0, #f1f5f9);
  overflow: hidden;
  box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.1);
  position: relative;
  margin: 24px 0;
  border: 1px solid rgba(139, 90, 43, 0.1);
}

.professional-progress::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, 
    rgba(139, 90, 43, 0.05) 25%, 
    transparent 25%, 
    transparent 50%, 
    rgba(139, 90, 43, 0.05) 50%, 
    rgba(139, 90, 43, 0.05) 75%, 
    transparent 75%, 
    transparent);
  background-size: 20px 20px;
  animation: progressStripes 2s linear infinite;
}

.professional-progress-bar {
  height: 100%;
  background: linear-gradient(135deg, var(--success-color), #38a169, var(--university-primary));
  border-radius: 25px;
  transition: width 2.5s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
}

.professional-progress-bar::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  background: linear-gradient(45deg, 
    rgba(255, 255, 255, 0.3) 25%, 
    transparent 25%, 
    transparent 50%, 
    rgba(255, 255, 255, 0.3) 50%, 
    rgba(255, 255, 255, 0.3) 75%, 
    transparent 75%, 
    transparent);
  background-size: 20px 20px;
  animation: progressStripes 1.5s linear infinite;
}

.professional-progress-bar:hover {
  box-shadow: 0 0 25px rgba(16, 185, 129, 0.6), 0 0 40px rgba(16, 185, 129, 0.4);
}

/* Status Display */
.clearance-status-display {
  margin-top: 20px;
  padding: 16px 20px;
  border-radius: 15px;
  font-weight: 700;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.clearance-status-display::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: var(--transition);
}

.clearance-status-display:hover::before {
  left: 100%;
}

.status-cleared {
  background: linear-gradient(135deg, var(--success-color), #059669);
  color: white;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
  animation: successPulse 3s ease-in-out infinite;
}

.status-pending {
  background: linear-gradient(135deg, var(--warning-color), #d97706);
  color: white;
  box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
  animation: pendingPulse 2s ease-in-out infinite;
}

.status-cleared:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

.status-pending:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
}

/* Quick Action Link */
.quick-action-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--university-primary);
  text-decoration: none;
  font-weight: 600;
  font-size: 13px;
  padding: 8px 12px;
  border-radius: 10px;
  background: rgba(139, 90, 43, 0.08);
  transition: var(--transition);
  margin-top: 12px;
}

.quick-action-link:hover {
  background: rgba(139, 90, 43, 0.15);
  color: var(--university-primary-dark);
  text-decoration: none;
  transform: translateY(-1px);
}

.quick-action-link i {
  font-size: 12px;
}

/* Enhanced Animations */
@keyframes progressStripes {
  0% { background-position: 0 0; }
  100% { background-position: 20px 0; }
}

@keyframes rotateGlow {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes successPulse {
  0%, 100% { 
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
  }
  50% { 
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5), 0 0 30px rgba(16, 185, 129, 0.3);
  }
}

@keyframes pendingPulse {
  0%, 100% { 
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
  }
  50% { 
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5), 0 0 30px rgba(245, 158, 11, 0.3);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .clearance-window {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    padding: 20px;
  }
  
  .clearance-details {
    width: 100%;
    justify-content: center;
  }
  
  .progress-card {
    min-height: 240px;
    padding: 24px 20px;
  }
  
  .progress-percentage {
    font-size: 2.8rem;
  }
}

/* Modern Navbar */
.modern-navbar {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(226, 232, 240, 0.5);
  border-radius: var(--border-radius);
  padding: 20px 30px;
  margin: 20px 0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: var(--transition);
}

.modern-navbar:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.navbar-left, .navbar-right {
  display: flex;
  align-items: center;
  gap: 15px;
}

.navbar-left {
  flex: 1;
}

.navbar-right {
  justify-content: flex-end;
}

/* Theme Toggle Button */
.btn-theme-toggle {
  background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
  color: white;
  border: none;
  border-radius: 25px;
  padding: 12px 20px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: var(--transition);
  box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3);
  font-size: 14px;
}

.btn-theme-toggle:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(139, 90, 43, 0.4);
  color: white;
  background: linear-gradient(135deg, var(--university-primary-dark), var(--university-primary));
}

/* Enhanced Logout Button */
.btn-logout {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
  border: none;
  border-radius: 25px;
  padding: 12px 20px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: var(--transition);
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
  text-decoration: none;
  font-size: 14px;
}

.btn-logout:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
  color: white;
  text-decoration: none;
  background: linear-gradient(135deg, #dc2626, #b91c1c);
}

/* Enhanced buttons */
.btn {
  border-radius: 12px;
  font-weight: 600;
  transition: var(--transition);
  box-shadow: var(--shadow-sm);
  position: relative;
  overflow: hidden;
}

.btn:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: var(--shadow-lg);
}

.btn-light {
  background: var(--glass-bg);
  border: 1px solid var(--glass-border);
  backdrop-filter: blur(20px);
  color: var(--text-primary);
}

.btn-light:hover {
  background: linear-gradient(135deg, var(--university-primary-light), rgba(255, 255, 255, 0.9));
  border-color: var(--university-primary);
  color: var(--university-primary-dark);
}

/* Enhanced table container */
.table-container {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--border-radius);
  padding: 24px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(139, 90, 43, 0.1);
  margin: 20px 0;
  position: relative;
  overflow: hidden;
}

.table-container:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Enhanced table responsiveness */
.table-responsive {
  border-radius: var(--border-radius);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  background: white;
  border: 1px solid rgba(139, 90, 43, 0.1);
}

@keyframes tableRowSlide {
  0% { 
    opacity: 0; 
    transform: translateX(-30px) scale(0.95);
  }
  100% { 
    opacity: 1; 
    transform: translateX(0) scale(1);
  }
}

@keyframes bounceIn {
  0% { opacity: 0; transform: scale(0.3); }
  50% { opacity: 1; transform: scale(1.05); }
  70% { transform: scale(0.9); }
  100% { opacity: 1; transform: scale(1); }
}

/* Responsive design */
@media (max-width: 768px) {
  .content {
    margin-left: 0;
    width: 100%;
    padding: 10px;
  }
  
  .modern-sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .modern-sidebar.active {
    transform: translateX(0);
  }
  
  .hero {
    flex-direction: column;
    text-align: center;
  }
}
</style>
</head>
<body>
<div class="layout">  

    <!-- LEFT SIDE BAR -->
    <?php include('sidebar.php'); ?>

    <!-- RIGHT MAIN AREA -->
    <div class="content container-fluid">

        <!-- navbar / top -->
        <div class="modern-navbar">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <div class="navbar-left">
                    <button id="theme-toggle" class="btn btn-theme-toggle">
                        <i class="fa fa-sun-o" id="theme-icon"></i>
                        <span id="theme-text">Light Mode</span>
                    </button>
                </div>
                <div class="navbar-right">
                    <a href="../login student/login.php" class="btn btn-logout">
                        <i class="fa fa-sign-out"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($_SESSION['success'])) { ?>
        <div class="alert alert-success mt-2"><?php echo e($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); } ?>
        <?php if (!empty($_SESSION['error'])) { ?>
        <div class="alert alert-danger mt-2"><?php echo e($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); } ?>

        <?php
        $notifications = [];
        $ns = $conn->prepare("SELECT id, subject, message, created_at, recipient_matric FROM notifications WHERE recipient_matric IS NULL OR recipient_matric = ? ORDER BY id DESC LIMIT 6");
        if ($ns) {
            $ns->bind_param('s', $matric_no);
            if ($ns->execute()) {
                $resn = $ns->get_result();
                while($rown = $resn->fetch_assoc()) { $notifications[] = $rown; }
            }
            $ns->close();
        }
        ?>

        <div class="row">
            <div class="col-12">
                <div class="hero <?php echo $allCleared ? 'hero-success' : ''; ?>">
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
                    
                    <div class="hero-content">
                        <div class="hero-name"><?php echo e($_SESSION['fullname'] ?? $student['fullname'] ?? $fullname); ?></div>
                        <div class="hero-details">
                            <div class="hero-detail-item">
                                <i class="fa fa-id-card"></i>
                                <?php echo e($matric_no); ?>
                            </div>
                            <div class="hero-detail-separator"></div>
                            <div class="hero-detail-item">
                                <i class="fa fa-university"></i>
                                <?php echo e($_SESSION['faculty'] ?? $faculty); ?>
                            </div>
                            <div class="hero-detail-separator"></div>
                            <div class="hero-detail-item">
                                <i class="fa fa-graduation-cap"></i>
                                <?php echo e($_SESSION['dept'] ?? $dept); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hero-actions">
                        <?php if ($allCleared): ?>
                            <a href="../letter.php" target="_blank" class="btn btn-light">
                                <i class="fa fa-download"></i>
                                Print Clearance Letter
                            </a>
                        <?php else: ?>
                            <?php if (!$hasRequestedThisYear): ?>
                                <?php if ($clearance_window_open): ?>
                                    <form method="post">
                                        <button type="submit" name="btn_request_clearance" class="btn btn-light">
                                            <i class="fa fa-paper-plane"></i>
                                            Request Clearance
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-light" disabled>
                                        <i class="fa fa-lock"></i>
                                        Requests Closed
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button type="button" class="btn btn-light" disabled>
                                    <i class="fa fa-check"></i>
                                    Requested for <?php echo $currentYear; ?>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="clearance-status-card">
                    <div class="status-header">
                        <div class="status-icon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <div class="status-content">
                            <h4>Clearance Window Status</h4>
                            <div class="status-badge-container">
                                <span class="window-status <?php echo ($clearance_window_open ? 'status-open' : 'status-closed'); ?>">
                                    <?php echo ($clearance_window_open ? 'Open' : 'Closed'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="status-details">
                            <div class="detail-item">
                                <span class="detail-label">Today:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($today); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Day Status:</span>
                                <span class="detail-value <?php echo ($today_open === 1 ? 'text-success' : 'text-warning'); ?>">
                                    <?php echo ($today_open === 1 ? 'Active' : 'Inactive'); ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Global:</span>
                                <span class="detail-value <?php echo ($global_open === '1' ? 'text-success' : 'text-warning'); ?>">
                                    <?php echo ($global_open === '1' ? 'Open' : 'Closed'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 metrics">
            <div class="col-md-3">
                <div class="progress-section">
                    <h3 class="mb-3">Overall Progress</h3>
                    <div class="progress-circle" style="--progress: <?php echo $progressPct; ?>">
                        <div class="progress-text"><?php echo $progressPct; ?>%</div>
                    </div>
                    <p class="mb-0">
                        <strong><?php echo $approvalsDone; ?></strong> of <strong><?php echo $approvalsTotal; ?></strong> departments cleared
                    </p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="metric-card departments-card">
                    <div class="metric-icon">
                        <i class="fa fa-building"></i>
                    </div>
                    <div class="metric-content">
                        <h5>Departments</h5>
                        <h3><?php echo $approvalsDone; ?><span class="metric-total">/ <?php echo $approvalsTotal; ?></span></h3>
                        <div class="metric-subtitle">Approved</div>
                        <div class="department-progress">
                            <div class="progress-bar-mini">
                                <div class="progress-fill" style="width: <?php echo $progressPct; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="metric-card status-card">
                    <div class="metric-icon">
                        <i class="fa fa-<?php echo $allCleared ? 'check-circle' : 'clock-o'; ?>"></i>
                    </div>
                    <div class="metric-content">
                        <h5>Current Status</h5>
                        <h3 class="<?php echo $allCleared ? 'text-success' : 'text-warning'; ?>">
                            <?php echo $allCleared ? 'Cleared' : 'Pending'; ?>
                        </h3>
                        <div class="metric-subtitle">
                            <?php echo $allCleared ? 'Ready for graduation' : 'Awaiting approvals'; ?>
                        </div>
                        <div class="status-indicator <?php echo $allCleared ? 'indicator-success' : 'indicator-warning'; ?>">
                            <div class="indicator-dot"></div>
                            <span><?php echo $allCleared ? 'Complete' : 'Processing'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="metric-card timeline-card">
                    <div class="metric-icon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div class="metric-content">
                        <h5>Academic Year</h5>
                        <h3><?php echo $currentYear; ?></h3>
                        <div class="metric-subtitle">Current Session</div>
                        <div class="timeline-status">
                            <?php if ($hasRequestedThisYear): ?>
                                <span class="timeline-badge requested">
                                    <i class="fa fa-check"></i> Requested
                                </span>
                            <?php else: ?>
                                <span class="timeline-badge not-requested">
                                    <i class="fa fa-exclamation"></i> Not Requested
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- approvals grid -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">Department Head</th>
                                <th class="text-center">Library</th>
                                <th class="text-center">Bookstore</th>
                                <th class="text-center">Dormitory</th>
                                <th class="text-center">Cafeteria</th>
                                <th class="text-center">Sport</th>
                                <th class="text-center">Dean</th>
                                <th class="text-center">Campus Police</th>
                                <th class="text-center">Registrar</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-center"><?php echo status_label($is_department_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_library_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_bookstore_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_dormitory_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_cafeteria_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_sport_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_dean_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_police_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_registrar_approved); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer include -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <?php include('../footer.php'); ?>
            </div>
        </div>

    </div><!-- END MAIN CONTENT -->

</div><!-- END FLEX CONTAINER -->

<script src="../js/jquery-2.1.1.js"></script>
<script src="../js/bootstrap.min.js"></script>

<script>
// Theme Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');
    const body = document.body;
    
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply saved theme
    if (currentTheme === 'dark') {
        body.classList.add('dark-mode');
        themeIcon.className = 'fa fa-moon-o';
        themeText.textContent = 'Dark Mode';
    }
    
    // Theme toggle event listener
    themeToggle.addEventListener('click', function() {
        body.classList.toggle('dark-mode');
        
        if (body.classList.contains('dark-mode')) {
            themeIcon.className = 'fa fa-moon-o';
            themeText.textContent = 'Dark Mode';
            localStorage.setItem('theme', 'dark');
        } else {
            themeIcon.className = 'fa fa-sun-o';
            themeText.textContent = 'Light Mode';
            localStorage.setItem('theme', 'light');
        }
    });
});
</script>
</body>
</html>