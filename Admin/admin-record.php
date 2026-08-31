<?php
session_start();
error_reporting(0);
include('../connect.php');
if(strlen($_SESSION['admin-username'])=="")
    {   
    header("Location: login.php"); 
    }
    else{
	}
	$username=$_SESSION['admin-username'];
	
	
date_default_timezone_set('Africa/Lagos');
$current_date = date('Y-m-d');	
	
$sql = "select * from admin where username='$username'"; 
$result = $conn->query($sql);
$row= mysqli_fetch_array($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Record|Dashboard</title>
<link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  
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
</head>
<body class="hold-transition sidebar-mini layout-fixed">

  <script type="text/javascript">
		function Activate(fullname){
if(confirm("ARE YOU SURE YOU WISH TO ACTIVATE " + " " + fullname+ "FROM THE SYSTEM ?" ))
{
return  true;
}
else {return false;
}
	 
}

</script>

<script type="text/javascript">
		function Deactivate(fullname){
if(confirm("ARE YOU SURE YOU WISH TO DEACTIVATE " + " " + fullname+ "FROM THE SYSTEM  ?" ))
{
return  true;
}
else {return false;
}
	 
}

</script>

<style>
/* CRITICAL: Force Custom Styles - Override AdminLTE */
/* Modern Admin Record Styling */
:root {
  --primary-gradient: linear-gradient(135deg, #007bff 0%, #ccccff 100%);
  --success-gradient: linear-gradient(135deg, #111111ff 0%, #8b5a3c 100%);
  --glass-bg: rgba(255, 255, 255, 0.95);
  --glass-border: rgba(159, 101, 64, 0.3);
  --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.08);
  --shadow-hover: 0 8px 30px rgba(159, 101, 64, 0.2);
  --border-radius: 16px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body { 
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
  background: #ffffffff !important;
  min-height: 100vh !important;
  position: relative !important;
  color: #0f172a !important;
}

/* Enhanced Cards */
.card { 
  border-radius: var(--border-radius) !important;
  border: 1px solid #9f6540 !important;
  background: var(--glass-bg) !important;
  backdrop-filter: blur(20px) !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06) !important;
  transition: var(--transition) !important;
  animation: cardSlideIn 0.8s ease-out !important;
  overflow: hidden !important;
  position: relative !important;
  margin: 20px auto !important;
}

@keyframes cardSlideIn {
  0% { opacity: 0; transform: translateY(30px) scale(0.95); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

.card-header { 
  border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
  background: var(--primary-gradient) !important;
  border-bottom: 1px solid var(--glass-border) !important;
  padding: 20px 24px !important;
  position: relative !important;
  overflow: hidden !important;
}

.card-header h4 {
  color: white !important;
  font-weight: 700 !important;
  font-size: 18px !important;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
  margin: 0 !important;
}

.card-header h4::before {
  content: '👥';
  margin-right: 10px;
  font-size: 24px;
}

/* Enhanced Table */
.table {
  background: white !important;
  border-radius: 12px !important;
  overflow: hidden !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.table thead th {
  background: linear-gradient(135deg, #000000ff 0%, #1b48dfff 100%) !important;
  color: white !important;
  font-weight: 600 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  border: none !important;
  padding: 15px 10px !important;
}

.table tbody tr {
  transition: all 0.3s ease !important;
}

.table tbody tr:hover {
  background: rgba(159, 101, 64, 0.1) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.table tbody td {
  padding: 12px 10px !important;
  vertical-align: middle !important;
  border-color: rgba(159, 101, 64, 0.2) !important;
  overflow: visible !important;
  position: relative !important;
}

/* Enhanced Buttons */
.btn {
  border-radius: 8px !important;
  font-weight: 600 !important;
  transition: var(--transition) !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}

.btn-danger {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
  border: none !important;
}

.btn-danger:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
}

.btn-success {
  background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important;
  border: none !important;
}

.btn-success:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4) !important;
}

/* Content Wrapper */
.content-wrapper {
  background: transparent !important;
  padding: 20px !important;
}

/* Breadcrumb */
.breadcrumb {
  background: rgba(255, 255, 255, 0.1) !important;
  backdrop-filter: blur(10px) !important;
  border-radius: 12px !important;
  padding: 12px 20px !important;
  border: 1px solid var(--glass-border) !important;
}

.breadcrumb-item a {
  color: #667eea !important;
  text-decoration: none !important;
  font-weight: 500 !important;
}

.breadcrumb-item.active {
  color: #374151 !important;
  font-weight: 600 !important;
}

/* Enhanced Navbar */
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

/* DataTables Enhancement */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
  border: 2px solid #9f6540 !important;
  border-radius: 8px !important;
  padding: 8px 12px !important;
  transition: var(--transition) !important;
}

.dataTables_wrapper .dataTables_length select:focus,
.dataTables_wrapper .dataTables_filter input:focus {
  border-color: #007bff !important;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15) !important;
  outline: none !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
  border-radius: 8px !important;
  margin: 0 2px !important;
  transition: var(--transition) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
  border-color: #007bff !important;
  color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%) !important;
  border-color: #9f6540 !important;
  color: white !important;
}

/* Action Buttons in Table */
.table tbody td a {
  display: inline-block !important;
  padding: 6px 12px !important;
  border-radius: 6px !important;
  text-decoration: none !important;
  font-weight: 600 !important;
  font-size: 12px !important;
  transition: var(--transition) !important;
  margin: 2px !important;
}

.table tbody td a:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
}

/* Status Badges */
.badge {
  padding: 6px 12px !important;
  border-radius: 20px !important;
  font-weight: 600 !important;
  font-size: 11px !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}

.badge-success {
  background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important;
  box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3) !important;
}

.badge-danger {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
  box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3) !important;
}

.badge-warning {
  background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
  box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3) !important;
}

/* Loading Animation */
.loading-shimmer {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%) !important;
  background-size: 200% 100% !important;
  animation: shimmer 1.5s infinite !important;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* Responsive Design */
@media (max-width: 768px) {
  .card {
    margin: 10px !important;
    border-radius: 12px !important;
  }
  
  .card-header {
    padding: 15px 20px !important;
  }
  
  .table {
    font-size: 12px !important;
  }
  
  .btn {
    padding: 8px 16px !important;
    font-size: 12px !important;
  }
}

/* Enhanced Footer */
.main-footer {
  background: rgba(255, 255, 255, 0.1) !important;
  backdrop-filter: blur(10px) !important;
  border-top: 1px solid var(--glass-border) !important;
  color: #374151 !important;
}

/* Professional Sidebar Styling */
.main-sidebar {
  background: linear-gradient(180deg,  #151618ff 0%,  #007bff 100%) !important;
  box-shadow: 4px 0 20px rgba(102, 126, 234, 0.15) !important;
  border-right: 1px solid rgba(255, 255, 255, 0.1) !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
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
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
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
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
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
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
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
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
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

/* Enhanced Action Dropdown Styling */
.btn-group {
  position: relative !important;
  display: inline-flex !important;
  vertical-align: middle !important;
}

.btn-group .btn {
  position: relative !important;
  flex: 1 1 auto !important;
  margin: 0 !important;
}

.btn-group .btn-danger {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
  border: none !important;
  color: white !important;
  font-weight: 600 !important;
  padding: 8px 16px !important;
  border-radius: 8px 0 0 8px !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3) !important;
}

.btn-group .btn-danger:hover {
  background: linear-gradient(135deg, #c82333 0%, #bd2130 100%) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.5) !important;
}

.btn-group .dropdown-toggle {
  border-radius: 0 8px 8px 0 !important;
  padding: 8px 12px !important;
  border-left: 1px solid rgba(255, 255, 255, 0.2) !important;
}

.btn-group .dropdown-toggle::after {
  margin-left: 0 !important;
  vertical-align: middle !important;
  border-top: 0.4em solid !important;
  border-right: 0.4em solid transparent !important;
  border-left: 0.4em solid transparent !important;
  transition: transform 0.3s ease !important;
}

.btn-group.show .dropdown-toggle::after {
  transform: rotate(180deg) !important;
}

/* Enhanced Dropdown Menu */
.dropdown-menu {
  background: white !important;
  border: 1px solid rgba(159, 101, 64, 0.2) !important;
  border-radius: 12px !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
  padding: 8px !important;
  margin-top: 8px !important;
  min-width: 180px !important;
  animation: dropdownSlideIn 0.3s ease-out !important;
  overflow: visible !important;
  z-index: 9999 !important;
  position: absolute !important;
}

/* Fix table overflow to allow dropdown to show */
.table-responsive {
  overflow: visible !important;
}

.card-body {
  overflow: visible !important;
}

.dataTables_wrapper {
  overflow: visible !important;
}

.dataTables_wrapper .dataTables_scroll {
  overflow: visible !important;
}

.dataTables_wrapper .dataTables_scrollBody {
  overflow: visible !important;
}

/* Ensure btn-group has proper stacking context */
.btn-group {
  position: relative !important;
  display: inline-flex !important;
  vertical-align: middle !important;
  z-index: 1 !important;
}

.btn-group.show {
  z-index: 9998 !important;
}

@keyframes dropdownSlideIn {
  0% {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.dropdown-menu::before {
  content: '';
  position: absolute;
  top: -8px;
  right: 20px;
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-bottom: 8px solid white;
  filter: drop-shadow(0 -2px 2px rgba(0, 0, 0, 0.1));
}

.dropdown-item {
  padding: 10px 16px !important;
  border-radius: 8px !important;
  margin: 2px 0 !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  font-weight: 500 !important;
  font-size: 14px !important;
  color: #374151 !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  position: relative !important;
  overflow: hidden !important;
}

.dropdown-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%);
  transform: scaleY(0);
  transition: transform 0.3s ease;
}

.dropdown-item:hover::before {
  transform: scaleY(1);
}

.dropdown-item:hover {
  background: linear-gradient(135deg, rgba(159, 101, 64, 0.1), rgba(139, 90, 60, 0.05)) !important;
  color: #9f6540 !important;
  transform: translateX(5px) !important;
  padding-left: 20px !important;
}

.dropdown-item:active {
  background: linear-gradient(135deg, rgba(159, 101, 64, 0.2), rgba(139, 90, 60, 0.1)) !important;
  color: #8b5a3c !important;
}

/* Add icons to dropdown items */
.dropdown-item[href*="block-unblock-admin.php?id"]::before {
  content: '🚫';
  position: static;
  width: auto;
  height: auto;
  background: none;
  transform: none;
  font-size: 16px;
  margin-right: 8px;
}

.dropdown-item[href*="block-unblock-admin.php?uid"]::before {
  content: '✅';
  position: static;
  width: auto;
  height: auto;
  background: none;
  transform: none;
  font-size: 16px;
  margin-right: 8px;
}

.dropdown-item[href*="edit-admin.php"]::before {
  content: '✏️';
  position: static;
  width: auto;
  height: auto;
  background: none;
  transform: none;
  font-size: 16px;
  margin-right: 8px;
}

/* Deactivate Item Styling */
.dropdown-item[href*="block-unblock-admin.php?id"]:hover {
  background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(200, 35, 51, 0.05)) !important;
  color: #dc3545 !important;
}

/* Activate Item Styling */
.dropdown-item[href*="block-unblock-admin.php?uid"]:hover {
  background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(33, 136, 56, 0.05)) !important;
  color: #28a745 !important;
}

/* Edit Item Styling */
.dropdown-item[href*="edit-admin.php"]:hover {
  background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 86, 179, 0.05)) !important;
  color: #007bff !important;
}

/* Dropdown divider */
.dropdown-divider {
  height: 1px !important;
  margin: 8px 0 !important;
  background: linear-gradient(90deg, transparent, rgba(159, 101, 64, 0.2), transparent) !important;
  border: none !important;
}

/* Button Group Hover Effect */
.btn-group:hover .btn-danger {
  box-shadow: 0 4px 16px rgba(220, 53, 69, 0.4) !important;
}

/* Responsive Dropdown */
@media (max-width: 768px) {
  .dropdown-menu {
    min-width: 160px !important;
    font-size: 13px !important;
  }
  
  .dropdown-item {
    padding: 8px 12px !important;
    font-size: 13px !important;
  }
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

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
      <img src="images/logo.png" alt="University Logo" class="brand-logo" onerror="this.style.display='none'">
      <span class="brand-text">BULE HORA UNIVERSITY<br><small>Admin Records</small></span>
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
              <i class="nav-icon fas fa-user-shield"></i>
              <p>Admin Management
              <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="admin-record.php" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>All Admins</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="add-admin.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Admin</p>
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
              <li class="breadcrumb-item"><a href="../super_admin/super_admin.php">Home</a></li>
              <li class="breadcrumb-item active">Admin Record</li>
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
          <p>&nbsp;</p>
          <table width="1049" border="0" align="center">
            <tr>
              <td width="1043" height="400"><div class="card">
                <div class="card-header">
                  <h4>Admin Record </h4>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table width="106%" align="center" class="table table-bordered table-striped" id="example1">
                    <thead>
                    <tr> <th width="3%"><div align="center">#</div></th>
                        <th width="13%"><div align="center">Username</div></th>
							          <th width="8%"><div align="center">Photo</div></th>
                        <th width="7%"><div align="center">Password</div></th>
                        <th width="6%"><div align="center">Designation</div></th>
                        <th width="6%"><div align="center">fullname</div></th>
                        <th width="8%"><div align="center">Email</div></th>
						           <th width="8%"><div align="center">Status</div></th>
                       <th width="16%"><div align="center">Action</div></th>
                        
				      </tr>
                    </thead>
                      <div align="center"></div>
                    
                    <tbody>
                                         <?php 
                                          $sql = "SELECT * FROM admin order by username ASC";
                                           $result = $conn->query($sql); 
										$cnt=1;
                                           while($row = $result->fetch_assoc()) { ?>
                      <tr class="gradeX">
					  <td height="47"><div align="center"><?php echo $cnt; ?></div></td>
                        <td><div align="center"><?php echo $row['username']; ?></div></td>
				 <td><div align="center"><span class="controls"><img src="../<?php echo $row['photo'];?>"  width="50" height="43" border="2"/></span></div></td>
                    <td><div align="center"><?php echo $row['password']; ?></div></td>
                    <td><div align="center"><?php echo $row['designation']; ?></div></td>
                     <td><div align="center"><?php echo $row['fullname']; ?></div></td>
               <td><div align="center"><?php echo $row['email']; ?></div></td>
                        <td>
                          <div align="center">
                            <?php if (($row['status'])==(('Active')))  { ?>
                              <a href="block-unblock-admin.php?id=<?php echo $row['ID'];?>" 
                                 class="status-badge status-active" 
                                 onClick="return Deactivate('<?php echo $row['fullname']; ?> ');">
                                 <i class="fas fa-check-circle"></i> Active
                              </a>
                            <?php } else {?>
                              <a href="block-unblock-admin.php?uid=<?php echo $row['ID'];?>" 
                                 class="status-badge status-inactive" 
                                 onClick="return Activate('<?php echo $row['fullname']; ?> ');">
                                 <i class="fas fa-times-circle"></i> Inactive
                              </a>
                            <?php } ?>
                          </div>
                        </td>
                        <td>
                          <div align="center">
                            <a href="edit-admin.php?id=<?php echo $row['ID'];?>" class="btn-edit-admin">
                              <i class="fas fa-edit"></i> Edit
                            </a>
                          </div>
                        </td>
                    </tr>
					<?php $cnt=$cnt+1;} ?>
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
				  
                </div>
                <!-- /.card-body -->
              </div>
                <table width="392" border="0" align="right">
                  <tr>
                    <td width="386"></td>
                  </tr>
                </table>
                <p>&nbsp;</p>
              </td>
            </tr>
			
          </table>
          <p>
            <!-- /.card -->
          </p>
        </div>
          <!-- /.col -->
    </div>
        <!-- /.row -->
  </div>
      <!-- /.container-fluid --><!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      
    </div>
 <?php  include('footer.php');   ?>
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
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
    
    // Force table header colors after DataTables loads
    setTimeout(function() {
      $('.table thead th').css({
        'background': 'linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%)',
        'color': 'white',
        'font-weight': '600',
        'text-transform': 'uppercase',
        'letter-spacing': '0.5px',
        'border': 'none',
        'padding': '15px 10px'
      });
      
      $('.card-header').css({
        'background': 'linear-gradient(135deg, #007bff 0%, #ccccff 100%)',
        'border-bottom': '1px solid rgba(159, 101, 64, 0.3)',
        'padding': '20px 24px'
      });
      
      $('.main-header').css({
        'background': 'linear-gradient(135deg, #007bff 0%, #ccccff 100%)',
        'border-bottom': '1px solid rgba(255, 255, 255, 0.2)',
        'box-shadow': '0 4px 20px rgba(0, 123, 255, 0.3)'
      });
      
      $('.main-sidebar').css({
        'background': 'linear-gradient(180deg, #151618ff 0%, #007bff 100%)'
      });
      
      $('.brand-link').css({
        'background': 'linear-gradient(135deg, #007bff 100%, #ccccff 0%)'
      });
    }, 100);
    
    // Re-apply on any DataTables redraw
    $('#example1').on('draw.dt', function() {
      $('.table thead th').css({
        'background': 'linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%)',
        'color': 'white',
        'font-weight': '600',
        'text-transform': 'uppercase',
        'letter-spacing': '0.5px',
        'border': 'none',
        'padding': '15px 10px'
      });
    });
  });
</script>

<style>
/* FORCE OVERRIDE - Load after AdminLTE to prevent style conflicts */
/* This ensures styles persist after AdminLTE JavaScript loads */

/* Enhanced Action Dropdown - FORCED STYLES */
.btn-group {
  position: relative !important;
  display: inline-flex !important;
  vertical-align: middle !important;
}

.btn-group .btn-danger {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
  border: none !important;
  color: white !important;
  font-weight: 600 !important;
  padding: 8px 16px !important;
  border-radius: 8px 0 0 8px !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3) !important;
}

.btn-group .btn-danger:hover,
.btn-group .btn-danger:focus,
.btn-group .btn-danger:active {
  background: linear-gradient(135deg, #c82333 0%, #bd2130 100%) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.5) !important;
  color: white !important;
  border: none !important;
}

.btn-group .dropdown-toggle {
  border-radius: 0 8px 8px 0 !important;
  padding: 8px 12px !important;
  border-left: 1px solid rgba(255, 255, 255, 0.2) !important;
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
  color: white !important;
}

.btn-group .dropdown-toggle:hover,
.btn-group .dropdown-toggle:focus,
.btn-group .dropdown-toggle:active {
  background: linear-gradient(135deg, #c82333 0%, #bd2130 100%) !important;
  color: white !important;
  border-left: 1px solid rgba(255, 255, 255, 0.2) !important;
}

.btn-group .dropdown-toggle::after {
  margin-left: 0 !important;
  vertical-align: middle !important;
  border-top: 0.4em solid !important;
  border-right: 0.4em solid transparent !important;
  border-left: 0.4em solid transparent !important;
  transition: transform 0.3s ease !important;
}

.btn-group.show .dropdown-toggle::after {
  transform: rotate(180deg) !important;
}

/* Enhanced Dropdown Menu - FORCED */
.dropdown-menu {
  background: white !important;
  border: 1px solid rgba(159, 101, 64, 0.2) !important;
  border-radius: 12px !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
  padding: 8px !important;
  margin-top: 8px !important;
  min-width: 180px !important;
  animation: dropdownSlideIn 0.3s ease-out !important;
  overflow: visible !important;
  z-index: 9999 !important;
  position: absolute !important;
}

/* Fix table overflow to allow dropdown to show */
.table-responsive {
  overflow: visible !important;
}

.card-body {
  overflow: visible !important;
}

.dataTables_wrapper {
  overflow: visible !important;
}

.dataTables_wrapper .dataTables_scroll {
  overflow: visible !important;
}

.dataTables_wrapper .dataTables_scrollBody {
  overflow: visible !important;
}

/* Ensure btn-group has proper stacking context */
.btn-group {
  position: relative !important;
  display: inline-flex !important;
  vertical-align: middle !important;
  z-index: 1 !important;
}

.btn-group.show {
  z-index: 9998 !important;
}

/* Ensure table cells don't clip dropdown */
.table tbody td {
  padding: 12px 10px !important;
  vertical-align: middle !important;
  border-color: rgba(159, 101, 64, 0.2) !important;
  overflow: visible !important;
  position: relative !important;
}

@keyframes dropdownSlideIn {
  0% {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.dropdown-menu::before {
  content: '';
  position: absolute;
  top: -8px;
  right: 20px;
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-bottom: 8px solid white;
  filter: drop-shadow(0 -2px 2px rgba(0, 0, 0, 0.1));
}

.dropdown-item {
  padding: 10px 16px !important;
  border-radius: 8px !important;
  margin: 2px 0 !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  font-weight: 500 !important;
  font-size: 14px !important;
  color: #374151 !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  position: relative !important;
  overflow: hidden !important;
  background: transparent !important;
}

.dropdown-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%);
  transform: scaleY(0);
  transition: transform 0.3s ease;
}

.dropdown-item:hover::before {
  transform: scaleY(1);
}

.dropdown-item:hover,
.dropdown-item:focus {
  background: linear-gradient(135deg, rgba(159, 101, 64, 0.1), rgba(139, 90, 60, 0.05)) !important;
  color: #9f6540 !important;
  transform: translateX(5px) !important;
  padding-left: 20px !important;
  text-decoration: none !important;
}

.dropdown-item:active {
  background: linear-gradient(135deg, rgba(159, 101, 64, 0.2), rgba(139, 90, 60, 0.1)) !important;
  color: #8b5a3c !important;
}

/* Deactivate Item Styling */
.dropdown-item[href*="block-unblock-admin.php?id"]:hover,
.dropdown-item[href*="block-unblock-admin.php?id"]:focus {
  background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(200, 35, 51, 0.05)) !important;
  color: #dc3545 !important;
}

/* Activate Item Styling */
.dropdown-item[href*="block-unblock-admin.php?uid"]:hover,
.dropdown-item[href*="block-unblock-admin.php?uid"]:focus {
  background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(33, 136, 56, 0.05)) !important;
  color: #28a745 !important;
}

/* Edit Item Styling */
.dropdown-item[href*="edit-admin.php"]:hover,
.dropdown-item[href*="edit-admin.php"]:focus {
  background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 86, 179, 0.05)) !important;
  color: #007bff !important;
}

/* Button Group Hover Effect */
.btn-group:hover .btn-danger {
  box-shadow: 0 4px 16px rgba(220, 53, 69, 0.4) !important;
}

/* Table styling persistence */
.table thead th {
  background: linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%) !important;
  color: white !important;
  font-weight: 600 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  border: none !important;
  padding: 15px 10px !important;
}

.table tbody tr:hover {
  background: rgba(159, 101, 64, 0.1) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.card-header {
  background: linear-gradient(135deg, #007bff 0%, #ccccff 100%) !important;
  border-bottom: 1px solid rgba(159, 101, 64, 0.3) !important;
  padding: 20px 24px !important;
}

.card-header h4 {
  color: white !important;
  font-weight: 700 !important;
  font-size: 18px !important;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
  margin: 0 !important;
}

/* Navbar persistence */
.main-header {
  background: linear-gradient(135deg, #007bff 0%, #ccccff 100%) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
  box-shadow: 0 4px 20px rgba(0, 123, 255, 0.3) !important;
}

.navbar-light .navbar-nav .nav-link {
  color: white !important;
  font-weight: 700 !important;
  background: rgba(255, 255, 255, 0.1) !important;
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
  border-radius: 8px !important;
  padding: 8px 16px !important;
  margin: 0 4px !important;
}

.navbar-light .navbar-nav .nav-link:hover {
  background: rgba(255, 255, 255, 0.2) !important;
  border-color: rgba(255, 255, 255, 0.4) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
}

/* Sidebar persistence */
.main-sidebar {
  background: linear-gradient(180deg, #151618ff 0%, #007bff 100%) !important;
}

.brand-link {
  background: linear-gradient(135deg, #007bff 100%, #ccccff 0%) !important;
}

.nav-sidebar .nav-link:hover {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(29, 78, 216, 0.1)) !important;
  color: #60a5fa !important;
  border-color: rgba(59, 130, 246, 0.3) !important;
  transform: translateX(5px) !important;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2) !important;
}

.nav-sidebar .nav-link.active {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
  color: white !important;
  border-color: rgba(59, 130, 246, 0.5) !important;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3) !important;
}

/* Clickable Status Badges */
.status-badge {
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  padding: 10px 20px !important;
  border-radius: 25px !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  text-decoration: none !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  cursor: pointer !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}

.status-badge i {
  font-size: 16px !important;
  transition: transform 0.3s ease !important;
}

.status-badge:hover i {
  transform: scale(1.2) rotate(10deg) !important;
}

/* Active Status Badge */
.status-active {
  background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important;
  color: white !important;
  border: 2px solid #28a745 !important;
}

.status-active:hover {
  background: linear-gradient(135deg, #218838 0%, #1e7e34 100%) !important;
  transform: translateY(-3px) scale(1.05) !important;
  box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4) !important;
  color: white !important;
}

.status-active:active {
  transform: translateY(-1px) scale(1.02) !important;
}

/* Inactive Status Badge */
.status-inactive {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
  color: white !important;
  border: 2px solid #dc3545 !important;
}

.status-inactive:hover {
  background: linear-gradient(135deg, #c82333 0%, #bd2130 100%) !important;
  transform: translateY(-3px) scale(1.05) !important;
  box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
  color: white !important;
}

.status-inactive:active {
  transform: translateY(-1px) scale(1.02) !important;
}

/* Edit Button Styling */
.btn-edit-admin {
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  padding: 10px 20px !important;
  border-radius: 25px !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  text-decoration: none !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  cursor: pointer !important;
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
  color: white !important;
  border: 2px solid #007bff !important;
  box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3) !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}

.btn-edit-admin i {
  font-size: 16px !important;
  transition: transform 0.3s ease !important;
}

.btn-edit-admin:hover {
  background: linear-gradient(135deg, #0056b3 0%, #004085 100%) !important;
  transform: translateY(-3px) scale(1.05) !important;
  box-shadow: 0 6px 20px rgba(0, 123, 255, 0.5) !important;
  color: white !important;
}

.btn-edit-admin:hover i {
  transform: scale(1.2) rotate(-10deg) !important;
}

.btn-edit-admin:active {
  transform: translateY(-1px) scale(1.02) !important;
}
</style>

</body>
</html>
