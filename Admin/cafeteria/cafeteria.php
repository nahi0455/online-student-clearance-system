
<?php
session_start();
include('../connect.php'); // Fixed path to connect.php
error_reporting(0);

// ✅ Only cafeteria staff can access this page
if (empty($_SESSION['admin-username'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['admin-username'];

// ✅ Fetch login user info
$sql = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($conn, $sql);
$row_admin = mysqli_fetch_array($result);

// Student list - Join register and students tables to get complete information
$query_students = "
    SELECT 
        s.*,
        COALESCE(r.photo, s.photo) as photo,
        COALESCE(r.fullname, s.fullname) as fullname,
        COALESCE(r.matric_no, s.matric_no) as matric_no,
        COALESCE(r.dept, s.dept) as dept,
        COALESCE(r.faculty, s.faculty) as faculty,
        COALESCE(r.session, s.session) as session,
        COALESCE(r.phone, s.phone) as phone
    FROM students s
    LEFT JOIN register r ON s.matric_no = r.matric_no
    ORDER BY COALESCE(r.fullname, s.fullname) ASC
";
$result_students = mysqli_query($conn, $query_students);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>cafteria Dashboard</title>
<link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">

<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../dist/css/adminlte.min.css">

<!-- YOUR CUSTOM SIDEBAR CSS -->
<link rel="stylesheet" href="sidebar.css">

<style>
/* Modern Dynamic Admin Styling */
:root {
  --primary-gradient: linear-gradient(135deg,  #007bff 100%, #ccccff 0%);
  --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
  --info-gradient: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
  --glass-bg: rgba(255, 255, 255, 0.95);
  --glass-border: rgba(226, 232, 240, 0.5);
  --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.08);
  --shadow-hover: 0 8px 30px rgba(163, 158, 158, 0.12);
  --border-radius: 16px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body { 
  margin-left: 0px !important; 
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.content-wrapper { 
  background: #b9b9c4ff;
  min-height: 100vh;
  position: relative;
}

/* Animated background pattern */
.content-wrapper::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 20%, rgba(102, 126, 234, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.05) 0%, transparent 50%);
  animation: backgroundShift 10s ease-in-out infinite;
  pointer-events: none;
}

@keyframes backgroundShift {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.6; transform: scale(1.1); }
}

.container-fluid { 
  max-width: 1500px; 
  margin: 0 auto; 
  padding: 20px;
  position: relative;
  z-index: 2;
}

/* Enhanced Cards */
.card { 
  border-radius: var(--border-radius);
  border: 1px solid var(--glass-border);
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  box-shadow: var(--shadow-soft);
  transition: var(--transition);
  animation: fadeInUp 0.6s ease-out;
  overflow: hidden;
  position: relative;
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent,  #d1c3c3ff 100%, transparent);
  transition: var(--transition);
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-hover);
}

.card:hover::before {
  left: 100%;
}

@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.card-header { 
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  background: var(--primary-gradient) !important;
  border-bottom: 1px solid var(--glass-border);
  padding: 20px 24px;
  position: relative;
  overflow: hidden;
}

.card-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: var(--transition);
}

.card-header:hover::before {
  left: 100%;
}

.card-title {
  color: white !important;
  font-weight: 700 !important;
  font-size: 18px !important;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.brand-link {
  background: linear-gradient(135deg,  #007bff 100%, #ccccff 0%) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
  padding: 20px 15px !important;
  position: relative !important;
  overflow: hidden !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-direction: column !important;
  text-decoration: none !important;
}

.brand-link::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: var(--transition);
}

.brand-link:hover::before {
  left: 100%;
}

.brand-text {
  color: white !important;
  font-weight: 700 !important;
  font-size: 16px !important;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
  letter-spacing: 0.5px !important;
  text-align: center !important;
  line-height: 1.2 !important;
}

.brand-logo {
  width: 50px !important;
  height: 50px !important;
  border-radius: 50% !important;
  border: 2px solid rgba(255, 255, 255, 0.3) !important;
  margin-bottom: 8px !important;
  transition: var(--transition) !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
}

.brand-logo:hover {
  transform: scale(1.05) rotate(5deg) !important;
  border-color: rgba(255, 255, 255, 0.6) !important;
  box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3) !important;
}


/* Enhanced Table */
.table { 
  color: #1f2937;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--shadow-soft);
  background: white;
}

.table td, .table th { 
  padding: 12px 8px !important; 
  vertical-align: middle;
  border-color: rgba(159, 101, 64, 0.2) !important;
  transition: var(--transition);
  font-size: 13px;
}

.table thead th {
  background: linear-gradient(135deg, #0b0b0cff, #2267c7ff) !important;

  font-weight: 600 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  font-size: 12px !important;
  position: relative;
  border: none;
  padding: 15px 8px;
}

.table tbody tr {
  transition: var(--transition);
}

.table tbody tr:hover {
  background: rgba(159, 101, 64, 0.1) !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.table tbody td {
  padding: 12px 8px;
  vertical-align: middle;
  border-color: rgba(159, 101, 64, 0.2);
  font-size: 13px;
}

/* Profile Image Styling */
.img-circle {
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #8B5A2B;
  box-shadow: 0 2px 8px rgba(139, 90, 43, 0.3);
  transition: var(--transition);
}

.img-circle:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 15px rgba(139, 90, 43, 0.4);
}

.default-avatar-small {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #8B5A2B, #A0522D);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 24px;
  border: 2px solid #8B5A2B;
  box-shadow: 0 2px 8px rgba(139, 90, 43, 0.3);
  margin: 0 auto;
  transition: var(--transition);
}

.default-avatar-small:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 15px rgba(139, 90, 43, 0.4);
}

/* Enhanced Status Badges */
.status-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: var(--transition);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.status-cleared {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.status-pending {
  background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
  color: #212529;
  box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
}

.status-badge:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Enhanced User Images */
img.img-circle { 
  width: 60px; 
  height: 60px; 
  object-fit: cover; 
  border-radius: 50%;
  border: 3px solid rgba(102, 126, 234, 0.2);
  box-shadow: var(--shadow-soft);
  transition: var(--transition);
}

img.img-circle:hover {
  transform: scale(1.1);
  box-shadow: var(--shadow-hover);
  border-color: rgba(102, 126, 234, 0.5);
}

/* Enhanced Search and Form Controls */
#searchInput, .form-control { 
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  color: #374151;
  border: 2px solid var(--glass-border);
  border-radius: 12px;
  padding: 12px 16px;
  transition: var(--transition);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
  font-weight: 500;
}

#searchInput:focus, .form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1), inset 0 2px 4px rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
  outline: none;
  background: rgba(255, 255, 255, 1);
}

#searchInput::placeholder, .form-control::placeholder { 
  color: #6b7280;
  font-weight: 500;
}

/* Enhanced Buttons */
.btn {
  border-radius: 12px !important;
  font-weight: 600 !important;
  transition: var(--transition) !important;
  box-shadow: var(--shadow-soft) !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  position: relative !important;
  overflow: hidden !important;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: var(--transition);
}

.btn:hover {
  transform: translateY(-2px) !important;
  box-shadow: var(--shadow-hover) !important;
}

.btn:hover::before {
  left: 100%;
}

.btn-success {
  background: var(--success-gradient) !important;
  border: none !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3) !important;
}

.btn-success:hover {
  box-shadow: 0 8px 25px rgba(72, 187, 120, 0.4) !important;
}

.btn-warning {
  background: var(--warning-gradient) !important;
  border: none !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(237, 137, 54, 0.3) !important;
}

.btn-info {
  background: var(--info-gradient) !important;
  border: none !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(66, 153, 225, 0.3) !important;
}

.btn-secondary {
  background: linear-gradient(135deg, #6b7280, #4b5563) !important;
  border: none !important;
  color: white !important;
}

/* View Button Styling */
.view-btn {
  background: var(--info-gradient) !important;
  border: none !important;
  color: white !important;
  padding: 6px 12px !important;
  border-radius: 8px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  transition: var(--transition) !important;
  box-shadow: 0 4px 15px rgba(66, 153, 225, 0.3) !important;
}

.view-btn:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 8px 25px rgba(66, 153, 225, 0.4) !important;
}

@media (max-width: 768px) {
  .container-fluid { padding: 10px; }
  .table { font-size: 11px; }
  img.img-circle { width: 35px; height: 35px; }
  .table th, .table td { padding: 6px 8px !important; }
}

/* Enhanced Toggle Button */
.sidebar-toggle {
  background: linear-gradient(135deg, #667eea, #764ba2) !important;
  border: 2px solid rgba(255, 255, 255, 0.2) !important;
  border-radius: 8px !important;
  width: 35px !important;
  height: 35px !important;
  color: white !important;
  font-size: 14px !important;
  cursor: pointer !important;
  transition: var(--transition) !important;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin-right: 10px !important;
}

.sidebar-toggle:hover {
  transform: translateY(-1px) scale(1.05) !important;
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
  background: linear-gradient(135deg, #764ba2, #667eea) !important;
  border-color: rgba(255, 255, 255, 0.3) !important;
}

.sidebar-toggle:active {
  transform: translateY(0) scale(0.98) !important;
  box-shadow: 0 2px 10px rgba(102, 126, 234, 0.2) !important;
}

/* Sidebar Overlay */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1040;
  display: none;
}

/* Enhanced Sidebar Scroll Styling - Full Height Professional */
.main-sidebar {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  height: 100vh !important;
  overflow-y: auto !important;
  z-index: 1050 !important;
}

.main-sidebar::-webkit-scrollbar {
  width: 8px;
}

.main-sidebar::-webkit-scrollbar-track {
  background: rgba(139, 90, 43, 0.1);
  border-radius: 4px;
}

.main-sidebar::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #8B5A2B, #A0522D);
  border-radius: 4px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(139, 90, 43, 0.3);
}

.main-sidebar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #A0522D, #8B5A2B);
  transform: scaleX(1.2);
  box-shadow: 0 4px 8px rgba(139, 90, 43, 0.4);
}

/* Smooth scrolling for sidebar */
.main-sidebar {
  scroll-behavior: smooth;
}

/* Ensure content wrapper accounts for fixed sidebar */
.content-wrapper {
  margin-left: 250px !important;
}

.sidebar-open .sidebar-overlay {
  display: block;
}

@media (max-width: 768px) {
  .sidebar-toggle {
    width: 30px !important;
    height: 30px !important;
    font-size: 12px !important;
    margin-right: 8px !important;
  }
}
</style>
</head>

<body>
<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="d-flex">
   <?php include 'sidebar.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>






<div class="content-wrapper" style="margin-left:260px; width: calc(100% - 260px);">
    <div class="content-header">
        <div class="container-fluid">
        </div>
    </div>

    


<section class="content">
<div class="container-fluid">

<div class="card">
<div class="card-header bg-primary text-white">
    <div class="d-flex align-items-center">
      <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
        <i class="fas fa-bars"></i>
      </button>
      <h3 class="card-title mb-0">Cafteria Service | Students list</h3>
    </div>
</div>

<div class="card-body">

<?php 
// Display success/error messages
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> ' . $_SESSION['success_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> ' . $_SESSION['error_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
    unset($_SESSION['error_message']);
}
?>

<div class="d-flex justify-content-end mb-2">
  <input type="text" id="searchInput" class="form-control" placeholder="Search student..." style="max-width:320px;">
</div>
<table class="table table-sm table-bordered table-striped table-hover" style="font-size:13px;">
<thead class="bg-primary text-white">
      <tr>
      <th style="width: 40px;">#</th>
      <th style="width: 120px;">Name</th>
      <th style="width: 60px;">Photo</th>
      <th style="width: 80px;">ID No</th>
      <th style="width: 70px;">Department</th>
      <th style="width: 60px;">Head</th>
      <th style="width: 50px;">Lib</th>
      <th style="width: 50px;">Book</th>
      <th style="width: 50px;">Dorm</th>
      <th style="width: 50px;">Cafe</th>
      <th style="width: 50px;">Sport</th>
      <th style="width: 50px;">Dean</th>
      <th style="width: 50px;">Police</th>
      <th style="width: 50px;">Reg</th>
      <th style="width: 60px;">View</th>
      <th style="width: 80px;">Action</th>
    </tr>

</thead>

<tbody>
<?php 
$sn = 1;
while($row = mysqli_fetch_assoc($result_students)) {

    // Get complete student data for this row - EXACT SAME METHOD AS STUDENT PAGES
    $current_student = [];
    $student_stmt = $conn->prepare("SELECT * FROM students WHERE matric_no = ? LIMIT 1");
    if ($student_stmt) {
        $student_stmt->bind_param('s', $row['matric_no']);
        $student_stmt->execute();
        $student_res = $student_stmt->get_result();
        $current_student = $student_res->fetch_assoc() ?: [];
        $student_stmt->close();
    }
    
    // If no data in students table, try register table as fallback - SAME AS STUDENT PAGES
    if (empty($current_student)) {
        $register_stmt = $conn->prepare("SELECT * FROM register WHERE matric_no = ? LIMIT 1");
        if ($register_stmt) {
            $register_stmt->bind_param('s', $row['matric_no']);
            $register_stmt->execute();
            $register_res = $register_stmt->get_result();
            $current_student = $register_res->fetch_assoc() ?: [];
            $register_stmt->close();
        }
    }

    echo "<tr>
        <td>{$sn}</td>
        <td>{$row['fullname']}</td>
        <td>";
        
    // EXACT SAME METHOD AS STUDENT PORTAL HERO SECTION - Get student photo from register table first, then students table as fallback
    $student_photo = '';
    
    // First try register table (primary source for photos)
    $photo_stmt = $conn->prepare("SELECT photo FROM register WHERE matric_no = ? LIMIT 1");
    if ($photo_stmt) {
        $photo_stmt->bind_param('s', $row['matric_no']);
        $photo_stmt->execute();
        $photo_result = $photo_stmt->get_result();
        if ($photo_row = $photo_result->fetch_assoc()) {
            if (!empty($photo_row['photo'])) {
                $student_photo = $photo_row['photo'];
            }
        }
        $photo_stmt->close();
    }
    
    // If no photo in register table, try students table as fallback
    if (empty($student_photo) && !empty($current_student['photo'])) {
        $student_photo = $current_student['photo'];
    }
    
    // Display photo with proper path handling and fallback
    if (!empty($student_photo)) {
        // Remove any existing ../ to normalize the path
        $photo_path = str_replace('../', '', $student_photo);
        
        // Try multiple possible locations for the photo
        $possible_paths = [
            '../' . $photo_path,           // Admin/uploads/ (one level up)
            '../../' . $photo_path,        // Root uploads/ (two levels up)
        ];
        
        $found_photo = false;
        $final_photo_url = '';
        
        foreach ($possible_paths as $test_path) {
            if (file_exists($test_path)) {
                $found_photo = true;
                $final_photo_url = $test_path;
                break;
            }
        }
        
        if ($found_photo) {
            // Add cache busting parameter to force image refresh
            $image_url = htmlspecialchars($final_photo_url);
            if (strpos($image_url, '?') === false) {
                $image_url .= '?v=' . time();
            }
            
            echo "<img src='{$image_url}' alt='Student Photo' width='60' height='60' class='img-circle' onerror=\"this.style.display='none'; this.nextElementSibling.style.display='flex';\">";
            echo "<div class='default-avatar-small' style='display: none;'>
                    <i class='fa fa-user'></i>
                  </div>";
        } else {
            // File doesn't exist in any location, show default avatar
            echo "<div class='default-avatar-small' title='Photo not found: {$photo_path}'>
                    <i class='fa fa-user'></i>
                  </div>";
        }
    } else {
        // No photo path in database, show default avatar
        echo "<div class='default-avatar-small' title='No photo uploaded'>
                <i class='fa fa-user'></i>
              </div>";
    }
    
    echo "</td>
        <td>{$row['matric_no']}</td>
        <td>{$row['dept']}</td>

        <td>".($row['is_department_approved'] ? '<span class="status-badge status-cleared">Cleared</span>' : '<span class="status-badge status-pending">Pending</span>')."</td>
        <td>".($row['is_library_approved'] ? '<span class="status-badge status-cleared">Cleared</span>' : '<span class="status-badge status-pending">Pending</span>')."</td>
        <td>".($row['is_bookstore_approved'] ? '<span class="status-badge status-cleared">Cleared</span>' : '<span class="status-badge status-pending">Pending</span>')."</td>
        <td>".($row['is_dormitory_approved'] ? '<span class="status-badge status-cleared">Cleared</span>' : '<span class="status-badge status-pending">Pending</span>')."</td>
        <td>".($row['is_cafeteria_approved'] ? '<span class="status-badge status-cleared">Cleared</span>' : '<span class="status-badge status-pending">Pending</span>')."</td>
        <td>".($row['is_sport_approved'] ? '<span class="status-badge status-cleared">Cleared</span>' : '<span class="status-badge status-pending">Pending</span>')."</td>
        <td>".($row['is_dean_approved'] ? '<span class="status-badge status-cleared">Cleared</span>' : '<span class="status-badge status-pending">Pending</span>')."</td>
        <td>".($row['is_police_approved'] ? '<span class="status-badge status-cleared">Cleared</span>' : '<span class="status-badge status-pending">Pending</span>')."</td>
        <td>".($row['is_registrar_approved'] ? '<span class="status-badge status-cleared">Cleared</span>' : '<span class="status-badge status-pending">Pending</span>')."</td>

                          <td>
                             <a href='../leave_details.php?id=".$row['ID']."'>
                                <button class='view-btn'>View</button>
                             </a>
                          </td>        
                          <td>";

                      // Approval button for cafeteria
                      if (!$row['is_dormitory_approved']) {
                          echo "<button class='btn btn-warning btn-sm' disabled>
                                wait Dorm
                                </button>";
                      } elseif (!$row['is_cafeteria_approved']) {
                          echo "<form method='POST' action='approve_cafeteria.php' style='display:inline-block;'>
                                  <input type='hidden' name='student_id' value='{$row['ID']}'>
                                  <button type='submit' name='btnapprove' class='btn btn-success btn-sm'>
                                    Approve
                                  </button>
                                </form>";
                      } else {
                          echo "<button class='btn btn-secondary btn-sm' disabled>Finish</button>";
                      }

                      echo "</td></tr>";
                      $sn++;
                  }
                  ?>
</tbody>
</table>

</div></div>

</div>
</section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Search functionality
  const searchInput = document.getElementById('searchInput');
  const rows = document.querySelectorAll('table tbody tr');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const v = this.value.trim().toLowerCase();
      rows.forEach(row => {
        const t = row.innerText.toLowerCase();
        row.style.display = (v === '' || t.includes(v)) ? '' : 'none';
      });
    });
  }

  // Enhanced Sidebar toggle functionality
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const body = document.body;
  
  // Load sidebar state from localStorage
  const sidebarState = localStorage.getItem('sidebarCollapsed');
  if (sidebarState === 'true') {
    body.classList.add('sidebar-collapse');
  }
  
  // Toggle sidebar function
  function toggleSidebar() {
    body.classList.toggle('sidebar-collapse');
    body.classList.toggle('sidebar-open');
    
    // Save state to localStorage
    const isCollapsed = body.classList.contains('sidebar-collapse');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
    
    // Update toggle button icon with animation
    if (sidebarToggle) {
      const icon = sidebarToggle.querySelector('i');
      icon.style.transform = 'rotate(180deg)';
      setTimeout(() => {
        icon.className = isCollapsed ? 'fas fa-bars' : 'fas fa-times';
        icon.style.transform = 'rotate(0deg)';
      }, 150);
    }
  }
  
  // Desktop toggle
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleSidebar);
  }
  
  // Mobile overlay click
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
      body.classList.remove('sidebar-open');
    });
  }
  
  // Keyboard shortcut (Ctrl + B)
  document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'b') {
      e.preventDefault();
      toggleSidebar();
    }
  });
  
  // Auto-collapse on mobile
  function handleResize() {
    if (window.innerWidth <= 768) {
      body.classList.add('sidebar-collapse');
      body.classList.remove('sidebar-open');
    } else {
      // Restore desktop state
      const sidebarState = localStorage.getItem('sidebarCollapsed');
      if (sidebarState !== 'true') {
        body.classList.remove('sidebar-collapse');
      }
    }
  }
  
  window.addEventListener('resize', handleResize);
  handleResize(); // Initial check
});

// Dropdown functionality (if needed)
if (document.querySelector('.dropdown-btn')) {
  document.querySelector('.dropdown-btn').onclick = function() {
    var content = document.querySelector('.dropdown-content');
    content.style.display = (content.style.display === "block") ? "none" : "block";
  };
}
</script>
</body>
</html>






        
 














              