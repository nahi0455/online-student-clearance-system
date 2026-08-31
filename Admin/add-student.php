<?php
 session_start();
 error_reporting(E_ALL);
 ini_set('display_errors', 1);
 include('connect.php');
 include('../login admin/connect2.php');

$username=$_SESSION['admin-username'];
$sql = "select * from admin where username='$username'"; 
$result = $conn->query($sql);
$row1= mysqli_fetch_array($result);

date_default_timezone_set('Africa/Lagos');
$current_date = date('Y-m-d H:i:s');

 
if(isset($_POST["btnregister"]))
{
  $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
  $password_stud = substr(str_shuffle($permitted_chars), 0, 6);

  $fullname = mysqli_real_escape_string($conn,$_POST['txtfullname']);
  $matric_no = mysqli_real_escape_string($conn,$_POST['txtmatric_no']);
  $phone = mysqli_real_escape_string($conn,$_POST['txtphone']);
  $session = mysqli_real_escape_string($conn,$_POST['cmdsession']);
  $faculty = mysqli_real_escape_string($conn,$_POST['cmdfaculty']);
  $dept = mysqli_real_escape_string($conn,$_POST['cmddept']);

  // Check if matric number already exists
  $sql = "SELECT * FROM register where matric_no='$matric_no'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    $_SESSION['error'] = 'Matric No already exists';
  } else {
    // Insert new student record
    $query = "INSERT into `register` (fullname,matric_no,password,session,faculty,dept,phone,photo)
              VALUES ('$fullname','$matric_no','$password_stud','$session','$faculty','$dept','$phone','uploads/avatar_nick.png')";

    $result = mysqli_query($conn,$query);
    if($result){
      $_SESSION['matric_no']=$matric_no;

      // SMS sending code (optional - may not work without proper API setup)
      /*
      $username='rexrolex0@gmail.com';
      $password='admin123';
      $sender='AUTHUR-JAVI';
      $message  = 'Dear '.$fullname.', Your password for online clearance system is :'.$password_stud.' ';
      $api_url  = 'https://portal.nigeriabulksms.com/api/';

      $data = array('username'=>$username, 'password'=>$password, 'sender'=>$sender, 'message'=>$message, 'mobiles'=>$phone);
      $data = http_build_query($data);
      $request = $api_url.'?'.$data;
      $result  = file_get_contents($request);
      $result  = json_decode($result);
      */

      $_SESSION['success'] = 'Student Registration was successful. Password: ' . $password_stud;
    } else {
      $_SESSION['error'] = 'Problem registering student: ' . mysqli_error($conn);
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register Student|Dashboard</title>
 <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  
  <style>
/* Modern Admin User Creation Styling - Brown/Orange Theme */
:root {
  --primary-gradient: linear-gradient(135deg, #007bff 0%, #ccccff 100%);
  --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --success-gradient: linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%);
  --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
  --info-gradient: linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%);
  --glass-bg: rgba(255, 255, 255, 0.95);
  --glass-border: rgba(159, 101, 64, 0.3);
  --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.08);
  --shadow-hover: 0 8px 30px rgba(159, 101, 64, 0.2);
  --border-radius: 16px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body { 
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #ffffffff;
  min-height: 100vh;
  position: relative;
  color: #0f172a;
}

/* Animated background pattern */
body::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
  animation: backgroundFloat 15s ease-in-out infinite;
  pointer-events: none;
  z-index: -1;
}

@keyframes backgroundFloat {
  0%, 100% { opacity: 0.4; transform: scale(1) rotate(0deg); }
  50% { opacity: 0.7; transform: scale(1.1) rotate(2deg); }
}

/* Enhanced Cards */
.card { 
  border-radius: var(--border-radius);
  border: 1px solid #9f6540;
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  transition: var(--transition);
  animation: cardSlideIn 0.8s ease-out;
  overflow: hidden;
  position: relative;
  margin: 20px auto;
  max-width: 900px;
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: var(--transition);
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-hover);
}

.card:hover::before {
  left: 100%;
}

@keyframes cardSlideIn {
  0% { opacity: 0; transform: translateY(30px) scale(0.95); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
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
  animation: titleGlow 3s ease-in-out infinite;
  margin: 0 !important;
}

.card-title::before {
  content: '🎓';
  margin-right: 10px;
  font-size: 28px;
  animation: iconFloat 2.5s ease-in-out infinite;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-rendering: optimizeLegibility;
  filter: none !important;
  transform-style: preserve-3d;
}

@keyframes iconFloat {
  0%, 100% { 
    transform: translateY(0px) scale(1) translateZ(0);
  }
  50% { 
    transform: translateY(-4px) scale(1.1) translateZ(0);
  }
}

@keyframes titleGlow {
  0%, 100% { filter: brightness(1); }
  50% { filter: brightness(1.1); }
}

/* Enhanced Form Controls */
.form-control { 
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  color: #374151;
  border: 2px solid #9f6540;
  border-radius: 12px;
  padding: 14px 18px;
  transition: var(--transition);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
  font-weight: 500;
  font-size: 15px;
}

.form-control:focus {
  border-color: #9f6540;
  box-shadow: 0 0 0 3px rgba(159, 101, 64, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
  outline: none;
  background: rgba(255, 255, 255, 1);
}

.form-control::placeholder { 
  color: #6b7280;
  font-weight: 500;
}

.form-group {
  margin-bottom: 20px;
  animation: formSlide 0.6s ease-out;
}

@keyframes formSlide {
  0% { opacity: 0; transform: translateX(-20px); }
  100% { opacity: 1; transform: translateX(0); }
}

.form-group label {
  font-weight: 700;
  color: #374151;
  margin-bottom: 10px;
  display: block;
  font-size: 15px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.form-group label i {
  margin-right: 8px;
  color: #9f6540;
  font-size: 16px;
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
  padding: 12px 24px !important;
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

.btn-primary {
  background: var(--success-gradient) !important;
  border: none !important;
  color: white !important;
  box-shadow: 0 6px 20px rgba(159, 101, 64, 0.4) !important;
}

.btn-primary:hover {
  box-shadow: 0 10px 30px rgba(159, 101, 64, 0.5) !important;
}

/* Enhanced Select Dropdowns */
select.form-control {
  cursor: pointer;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px 12px;
  padding-right: 40px;
  background-color: rgba(255, 255, 255, 0.95) !important;
  color: #374151 !important;
  font-weight: 500 !important;
  font-size: 15px !important;
}

select.form-control option {
  background-color: white !important;
  color: #374151 !important;
  padding: 10px !important;
  font-weight: 500 !important;
}

select.form-control option:checked,
select.form-control option:hover {
  background-color: #9f6540 !important;
  color: white !important;
}

select.form-control:focus {
  background-color: rgba(255, 255, 255, 1) !important;
  color: #374151 !important;
}

/* Additional select styling for better visibility */
select.form-control:not([multiple]) {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

select.form-control::-ms-expand {
  display: none;
}

/* Ensure placeholder and selected text are visible */
select.form-control option[value=""] {
  color: #6b7280 !important;
  font-style: italic;
}

select.form-control:invalid {
  color: #6b7280;
}

select.form-control:valid {
  color: #374151 !important;
  font-weight: 600 !important;
}

/* Force text visibility in select elements */
select.form-control,
select.form-control:focus,
select.form-control:active,
select.form-control:hover {
  color: #1f2937 !important;
  text-shadow: none !important;
  -webkit-text-fill-color: #1f2937 !important;
}

/* Override any conflicting styles */
select {
  color: #1f2937 !important;
  background-color: white !important;
}

/* Content Wrapper */
.content-wrapper {
  background: transparent !important;
  padding: 20px;
}

.container-fluid {
  max-width: 1200px;
  margin: 0 auto;
}

/* Breadcrumb */
.breadcrumb {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 12px 20px;
  border: 1px solid var(--glass-border);
}

.breadcrumb-item a {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.breadcrumb-item.active {
  color: #374151;
  font-weight: 600;
}

/* Professional Sidebar Styling */
.main-sidebar {
  background: linear-gradient(180deg,  #151618ff 0%,  #007bff 100%) !important;
  box-shadow: 4px 0 20px rgba(102, 126, 234, 0.15) !important;
  border-right: 1px solid rgba(255, 255, 255, 0.1) !important;
  transition: var(--transition) !important;
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

.sidebar {
  background: transparent !important;
  padding-top: 0px !important;
}

/* Enhanced User Panel */
.user-panel {
  background: rgba(255, 255, 255, 0.05) !important;
  border-radius: 12px !important;
  margin: 15px 10px !important;
  padding: 15px !important;
  border: 1px solid rgba(148, 163, 184, 0.1) !important;
  transition: var(--transition) !important;
  animation: userPanelGlow 3s ease-in-out infinite !important;
}

.user-panel:hover {
  background: rgba(255, 255, 255, 0.08) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2) !important;
}

@keyframes userPanelGlow {
  0%, 100% { box-shadow: 0 0 10px rgba(59, 130, 246, 0.1); }
  50% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.2); }
}

.user-panel .image img {
  border: 3px solid rgba(59, 130, 246, 0.3) !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
  transition: var(--transition) !important;
}

.user-panel .image img:hover {
  border-color: rgba(59, 130, 246, 0.6) !important;
  transform: scale(1.05) !important;
}

.user-panel .info a {
  color: #e2e8f0 !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  text-decoration: none !important;
  transition: var(--transition) !important;
}

.user-panel .info a:hover {
  color: #60a5fa !important;
  text-shadow: 0 0 10px rgba(96, 165, 250, 0.3) !important;
}

/* Enhanced Navigation */
.nav-sidebar {
  padding: 5px 10px 0 10px !important;
}

.nav-sidebar .nav-item {
  margin-bottom: 5px !important;
}

.nav-sidebar .nav-link {
  background: rgba(255, 255, 255, 0.03) !important;
  border-radius: 10px !important;
  margin: 2px 0 !important;
  padding: 12px 15px !important;
  color: #cbd5e1 !important;
  border: 1px solid transparent !important;
  transition: var(--transition) !important;
  position: relative !important;
  overflow: hidden !important;
}

.nav-sidebar .nav-link::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
  transition: var(--transition);
}

.nav-sidebar .nav-link:hover {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(29, 78, 216, 0.1)) !important;
  color: #60a5fa !important;
  border-color: rgba(59, 130, 246, 0.3) !important;
  transform: translateX(5px) !important;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2) !important;
}

.nav-sidebar .nav-link:hover::before {
  left: 100%;
}

.nav-sidebar .nav-link.active {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
  color: white !important;
  border-color: rgba(59, 130, 246, 0.5) !important;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3) !important;
}

.nav-sidebar .nav-link .nav-icon {
  margin-right: 10px !important;
  font-size: 16px !important;
  transition: var(--transition) !important;
}

.nav-sidebar .nav-link:hover .nav-icon {
  transform: scale(1.1) rotate(5deg) !important;
  color: #60a5fa !important;
}

.nav-sidebar .nav-link.active .nav-icon {
  color: white !important;
  text-shadow: 0 0 10px rgba(255, 255, 255, 0.3) !important;
}

.nav-sidebar .nav-link p {
  margin: 0 !important;
  font-weight: 500 !important;
  font-size: 13px !important;
  transition: var(--transition) !important;
}

/* Logout Link Special Styling */
.nav-sidebar .nav-link.text-danger {
  background: rgba(239, 68, 68, 0.1) !important;
  border-color: rgba(239, 68, 68, 0.2) !important;
}

.nav-sidebar .nav-link.text-danger:hover {
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.15)) !important;
  color: #fca5a5 !important;
  border-color: rgba(239, 68, 68, 0.4) !important;
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2) !important;
}

/* Enhanced Navbar - Brown/Orange Theme */
.main-header {
  background: linear-gradient(135deg, #007bff 0%, #ccccff 100%) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
  box-shadow: 0 4px 20px rgba(0, 123, 255, 0.3) !important;
}

.navbar-light .navbar-nav .nav-link {
  color: white !important;
  font-weight: 700 !important;
  transition: var(--transition) !important;
  padding: 8px 16px !important;
  border-radius: 8px !important;
  margin: 0 4px !important;
  background: rgba(255, 255, 255, 0.1) !important;
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
  text-decoration: none !important;
}

.navbar-light .navbar-nav .nav-link:hover {
  color: white !important;
  transform: translateY(-2px) !important;
  text-shadow: 0 0 10px rgba(255, 255, 255, 0.5) !important;
  background: rgba(255, 255, 255, 0.2) !important;
  border-color: rgba(255, 255, 255, 0.4) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
}

/* Form Animation States */
.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
.form-group:nth-child(5) { animation-delay: 0.5s; }
.form-group:nth-child(6) { animation-delay: 0.6s; }
.form-group:nth-child(7) { animation-delay: 0.7s; }
.form-group:nth-child(8) { animation-delay: 0.8s; }

/* Loading Animation */
.loading-shimmer {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* Responsive Design */
@media (max-width: 768px) {
  .card {
    margin: 10px;
    border-radius: 12px;
  }
  
  .card-header {
    padding: 15px 20px;
  }
  
  .form-control {
    padding: 10px 14px;
  }
  
  .btn {
    padding: 10px 20px !important;
  }
}

/* Enhanced Footer */
.main-footer {
  background: rgba(255, 255, 255, 0.1) !important;
  backdrop-filter: blur(10px) !important;
  border-top: 1px solid var(--glass-border) !important;
  color: #374151 !important;
}

/* Preview Card Animations */
@keyframes slideIn {
  0% {
    opacity: 0;
    transform: translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes bounce {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.2);
  }
}

#sessionPreview {
  box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2) !important;
  transition: all 0.3s ease !important;
}

#sessionPreview:hover {
  transform: translateY(-3px) !important;
  box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3) !important;
}

#facultyPreview {
  box-shadow: 0 4px 15px rgba(159, 101, 64, 0.2) !important;
  transition: all 0.3s ease !important;
}

#facultyPreview:hover {
  transform: translateY(-3px) !important;
  box-shadow: 0 8px 25px rgba(159, 101, 64, 0.3) !important;
}

#departmentPreview {
  box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2) !important;
  transition: all 0.3s ease !important;
}

#departmentPreview:hover {
  transform: translateY(-3px) !important;
  box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3) !important;
}
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="../super_admin/super_admin.php" class="nav-link">Home</a>      </li>
      
    </ul>

  

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
 
      
    </ul>
  </nav>
  <!-- /.navbar -->

 <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
      <img src="images/logo.png" alt="University Logo" class="brand-logo" onerror="this.style.display='none'">
      <span class="brand-text">BULE HORA UNIVERSITY<br><small>Student Registration</small></span>
    </a>
       
    <div class="sidebar">
        <nav>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item">
                        <a href="../super_admin/super_admin.php" class="nav-link">
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item has-treeview menu-open">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-user-graduate"></i>
                    <p>Student Management
                    <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="student-record.php" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>All Students</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="add-student.php" class="nav-link active">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Add Student</p>
                        </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item">
                <a href="../logout.php" class="nav-link text-danger">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    <p>Logout</p>
                </a>
            </li>
        </ul>
        </nav>
    </div>
</aside>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">&nbsp;</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Register Student</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
        
		 <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Register Student </h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
               <form id="form" action="" method="post" class="">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Fullname </label>
                    <input type="text" class="form-control" name="txtfullname" id="exampleInputEmail1" size="77" value="<?php if (isset($_POST['txtfullname'])) echo $_POST['txtfullname']; ?>" placeholder="Enter Fullname">
                  </div>
				   <div class="form-group">
                    <label for="exampleInputEmail1">ID No. </label>
                    <input type="text" class="form-control" name="txtmatric_no" id="exampleInputEmail1" size="77" value="<?php if (isset($_POST['txtmatric_no'])) echo $_POST['txtmatric_no']; ?>" placeholder="Enter Matric No.">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail1">Phone No. </label>
                    <input type="text" class="form-control" name="txtphone" id="exampleInputEmail1" size="77" value="<?php if (isset($_POST['txtphone'])) echo $_POST['txtphone']; ?>" placeholder="Enter Phone">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputPassword1">Session</label>
                    <?php
//Our select statement. This will retrieve the data that we want.
$sql = "SELECT * FROM tblsession";
//Prepare the select statement.
$stmt = $dbh->prepare($sql);
//Execute the statement.
$stmt->execute();
//Retrieve the rows using fetchAll.
$sessions = $stmt->fetchAll();
?>
      <select name="cmdsession" id="sessionSelect" class="form-control" required="" style="color: #1f2937 !important; -webkit-text-fill-color: #1f2937 !important;">
        <option value="">Select Academic Session</option>
    <?php foreach($sessions as $row_session): ?>
        <option value="<?= $row_session['session']; ?>"><?= $row_session['session']; ?></option>
    <?php endforeach; ?>
</select>
                  
                    <!-- Session Preview Card -->
                    <div id="sessionPreview" style="display:none; margin-top: 15px; padding: 15px; border-radius: 12px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(109, 40, 217, 0.05)); border: 2px solid #8b5cf6; animation: slideIn 0.3s ease;">
                      <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 40px; animation: bounce 0.5s ease;">📅</div>
                        <div style="flex: 1;">
                          <div style="font-weight: 700; color: #8b5cf6; font-size: 16px; margin-bottom: 5px;">Selected Session:</div>
                          <div id="sessionText" style="font-weight: 600; color: #374151; font-size: 18px;"></div>
                          <div style="font-size: 13px; color: #6b7280; margin-top: 5px;">Academic year for student enrollment</div>
                        </div>
                      </div>
                    </div>
                  
                  </div>
 <div class="form-group">
    <label>Faculty</label>
    <select name="cmdfaculty" id="faculty" class="form-control" required style="color: #1f2937 !important; -webkit-text-fill-color: #1f2937 !important;">
        <option value="">Select Faculty</option>
        <option value="Engineering & Technology">⚙️ Engineering & Technology</option>
        <option value="Informatics">💻 Informatics</option>
        <option value="College of Natural Science">🔬 College of Natural Science</option>
        <option value="College of Agricultural Science">🌱 College of Agricultural Science</option>
        <option value="College of Business & Economics">💼 College of Business & Economics</option>
        <option value="College of Social Science">👥 College of Social Science</option>
        <option value="Institutes">🏥 Institutes</option>
    </select>
    
    <!-- Faculty Preview Card -->
    <div id="facultyPreview" style="display:none; margin-top: 15px; padding: 15px; border-radius: 12px; background: linear-gradient(135deg, rgba(159, 101, 64, 0.1), rgba(139, 90, 60, 0.05)); border: 2px solid #9f6540; animation: slideIn 0.3s ease;">
      <div style="display: flex; align-items: center; gap: 15px;">
        <div id="facultyIcon" style="font-size: 40px; animation: bounce 0.5s ease;"></div>
        <div style="flex: 1;">
          <div style="font-weight: 700; color: #9f6540; font-size: 16px; margin-bottom: 5px;">Selected Faculty:</div>
          <div id="facultyText" style="font-weight: 600; color: #374151; font-size: 18px;"></div>
          <div id="facultyDescription" style="font-size: 13px; color: #6b7280; margin-top: 5px;"></div>
        </div>
      </div>
    </div>
</div>

<div class="form-group">
    <label>Department</label>
    <select name="cmddept" id="department" class="form-control" required style="color: #1f2937 !important; -webkit-text-fill-color: #1f2937 !important;">
        <option value="">Select Department</option>
    </select>
    
    <!-- Department Preview Card -->
    <div id="departmentPreview" style="display:none; margin-top: 15px; padding: 15px; border-radius: 12px; background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(33, 136, 56, 0.05)); border: 2px solid #28a745; animation: slideIn 0.3s ease;">
      <div style="display: flex; align-items: center; gap: 15px;">
        <div style="font-size: 40px; animation: bounce 0.5s ease;">🏢</div>
        <div style="flex: 1;">
          <div style="font-weight: 700; color: #28a745; font-size: 16px; margin-bottom: 5px;">Selected Department:</div>
          <div id="departmentText" style="font-weight: 600; color: #374151; font-size: 18px;"></div>
          <div style="font-size: 13px; color: #6b7280; margin-top: 5px;">Academic department under <span id="departmentFaculty" style="font-weight: 600;"></span></div>
        </div>
      </div>
    </div>
</div>
<script>
const departments = {
  "Engineering & Technology": [
    "Civil Engineering",
    "Electrical Engineering",
    "Mechanical Engineering",
    "Chemical Engineering",
    "Hydraulic Engineering",
    "Construction Technology",
    "Industrial Engineering",
    "Surveying",
    "Architecture",
    "Irrigation",
    "Mining",
    "Environmental Engineering"
  ],
  "Informatics": [
    "Information Science",
    "Information Technology",
    "Computer Science",
    "Information System",
    "Computer Science and Engineering",
    "Software Engineering"
  ],
  "College of Natural Science": [
    "Applied Mathematics",
    "Applied Physics",
    "Applied Chemistry",
    "Applied Biology",
    "Statistics",
    "Applied Geology",
    "Sport Science",
    "Industrial Chemistry",
    "Biotechnology",
    "Environmental Science"
  ],
  "College of Agricultural Science": [
    "Veterinary Medicine",
    "Animals Science",
    "Range Ecology and Biodiversity",
    "Plant Science",
    "Natural Resource Management",
    "Agro Economics",
    "Agroforestry",
    "Horticulture",
    "Rural Development",
    "Soil Resource and Water Shade Management",
    "Eco-Tourism and Biodiversity Conservation",
    "Agri-Business and Value Chain Management"
  ],
  "College of Business & Economics": [
    "Accounting and Finance",
    "Economics",
    "Management",
    "Logistic and Supply Chain Management",
    "Marketing"
  ],
  "College of Social Science": [
    "Afan Oromo",
    "Oromo Folklore",
    "Civics",
    "Governance",
    "English",
    "Geography",
    "History and Heritage",
    "Tourism",
    "Journalism and Communication",
    "Social Anthropology",
    "Sociology and Social Work"
  ],
  "Institutes": [
    "Gadaa and Culture Studies",
    "Medicine",
    "Nursing",
    "Midwifery",
    "Public Health",
    "Anesthesia",
    "Clinical Pharmacy",
    "Medical Radiology Technology",
    "Medical Laboratory Technology",
    "Health Informatics",
    "Environmental Health",
    "Psychiatry"
  ]
};

// Session selection preview
document.getElementById('sessionSelect').addEventListener('change', function () {
    const session = this.value;
    const sessionPreview = document.getElementById('sessionPreview');
    
    if (session) {
        document.getElementById('sessionText').textContent = session;
        sessionPreview.style.display = 'block';
    } else {
        sessionPreview.style.display = 'none';
    }
});

document.getElementById('faculty').addEventListener('change', function () {
    const faculty = this.value;
    const deptSelect = document.getElementById('department');

    deptSelect.innerHTML = '<option value="">Select Department</option>';

    if (departments[faculty]) {
        departments[faculty].forEach(function (dept) {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            deptSelect.appendChild(option);
        });
    }
    
    // Faculty preview data
    const facultyData = {
        'Engineering & Technology': {
            icon: '⚙️',
            description: 'Technical and engineering disciplines including civil, electrical, and mechanical engineering'
        },
        'Informatics': {
            icon: '💻',
            description: 'Computer science, information technology, and software engineering programs'
        },
        'College of Natural Science': {
            icon: '🔬',
            description: 'Mathematics, physics, chemistry, biology, and environmental sciences'
        },
        'College of Agricultural Science': {
            icon: '🌱',
            description: 'Agriculture, veterinary medicine, natural resources, and rural development'
        },
        'College of Business & Economics': {
            icon: '💼',
            description: 'Business administration, economics, accounting, and management studies'
        },
        'College of Social Science': {
            icon: '👥',
            description: 'Social sciences, languages, journalism, and cultural studies'
        },
        'Institutes': {
            icon: '🏥',
            description: 'Specialized institutes including medicine, nursing, and health sciences'
        }
    };
    
    // Update faculty preview
    const facultyPreview = document.getElementById('facultyPreview');
    if (faculty && facultyData[faculty]) {
        document.getElementById('facultyIcon').textContent = facultyData[faculty].icon;
        document.getElementById('facultyText').textContent = faculty;
        document.getElementById('facultyDescription').textContent = facultyData[faculty].description;
        facultyPreview.style.display = 'block';
    } else {
        facultyPreview.style.display = 'none';
    }
    
    // Hide department preview when faculty changes
    document.getElementById('departmentPreview').style.display = 'none';
});

// Department selection preview
document.getElementById('department').addEventListener('change', function () {
    const department = this.value;
    const faculty = document.getElementById('faculty').value;
    
    const departmentPreview = document.getElementById('departmentPreview');
    if (department) {
        document.getElementById('departmentText').textContent = department;
        document.getElementById('departmentFaculty').textContent = faculty;
        departmentPreview.style.display = 'block';
    } else {
        departmentPreview.style.display = 'none';
    }
});
</script>

                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" name="btnregister" class="btn btn-primary">Register Student</button>
                </div>
              </form>
            </div>
		
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col --><!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)--><!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <?php include('../footer.php');  ?>
    <div class="float-right d-none d-sm-inline-block">
      
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
	
<link rel="stylesheet" href="popup_style.css">
<?php if(!empty($_SESSION['success'])) {  ?>
<div class="popup popup--icon -success js_success-popup popup--visible">
  <div class="popup__background"></div>
  <div class="popup__content">
    <h3 class="popup__content__title">
      <strong>Success</strong> 
    </h1>
    <p><?php echo $_SESSION['success']; ?></p>
    <p>
      <button class="button button--success" data-for="js_success-popup">Close</button>
    </p>
  </div>
</div>
<?php unset($_SESSION["success"]);  
} ?>
<?php if(!empty($_SESSION['error'])) {  ?>
<div class="popup popup--icon -error js_error-popup popup--visible">
  <div class="popup__background"></div>
  <div class="popup__content">
    <h3 class="popup__content__title">
      <strong>Error</strong> 
    </h1>
    <p><?php echo $_SESSION['error']; ?></p>
    <p>
      <button class="button button--error" data-for="js_error-popup">Close</button>
    </p>
  </div>
</div>
<?php unset($_SESSION["error"]);  } ?>
    <script>
      var addButtonTrigger = function addButtonTrigger(el) {
  el.addEventListener('click', function () {
    var popupEl = document.querySelector('.' + el.dataset.for);
    popupEl.classList.toggle('popup--visible');
  });
};

Array.from(document.querySelectorAll('button[data-for]')).
forEach(addButtonTrigger);
    </script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Treeview functionality
    const treeviewItems = document.querySelectorAll('.has-treeview > .nav-link');
    
    treeviewItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.nav-item');
            const isOpen = parent.classList.contains('menu-open');
            
            // Close all other treeview items
            document.querySelectorAll('.nav-item.menu-open').forEach(openItem => {
                if (openItem !== parent) {
                    openItem.classList.remove('menu-open');
                }
            });
            
            // Toggle current item
            parent.classList.toggle('menu-open', !isOpen);
        });
    });

    // Ripple effect on nav links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const ripple = this.querySelector('.nav-ripple');
            if (ripple) {
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                
                setTimeout(() => {
                    ripple.style.animation = '';
                }, 600);
            }
        });
    });

    // Active state management
    const currentPage = window.location.pathname.split('/').pop();
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.closest('.nav-item').classList.add('active');
            // If it's in a treeview, open the parent
            const treeviewParent = link.closest('.nav-treeview');
            if (treeviewParent) {
                treeviewParent.closest('.nav-item').classList.add('menu-open');
            }
        }
    });

    // Fix select dropdown visibility
    const selectElements = document.querySelectorAll('select.form-control');
    selectElements.forEach(select => {
        // Force text color on change
        select.addEventListener('change', function() {
            this.style.color = '#1f2937';
            this.style.webkitTextFillColor = '#1f2937';
            this.style.fontWeight = '600';
            console.log('Selected:', this.value); // Debug log
        });
        
        // Set initial styling if there's a selected value
        if (select.value && select.value !== '') {
            select.style.color = '#1f2937';
            select.style.webkitTextFillColor = '#1f2937';
            select.style.fontWeight = '600';
        }
        
        // Force styling on focus and blur
        select.addEventListener('focus', function() {
            this.style.color = '#1f2937';
            this.style.webkitTextFillColor = '#1f2937';
        });
        
        select.addEventListener('blur', function() {
            this.style.color = '#1f2937';
            this.style.webkitTextFillColor = '#1f2937';
        });
    });
});
</script>
</body>
</html>
