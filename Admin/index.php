<?php
session_start();
error_reporting(1);
include('../connect.php');
if(empty($_SESSION['admin-username']))
    {   
    header("Location: login.php"); 
    }
    else{
	}
      
$username = $_SESSION["admin-username"];
date_default_timezone_set('Africa/Lagos');
$current_date = date('Y-m-d');

 $sql = "select * from admin where username ='$username'"; 
$result = $conn->query($sql);
$row2 = mysqli_fetch_array($result);

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome to Admin Dashboard</title>
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
/* Modern Admin Dashboard Styling */
:root {
  --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  --info-gradient: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
  --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
  --glass-bg: rgba(255, 255, 255, 0.95);
  --glass-border: rgba(226, 232, 240, 0.5);
  --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.08);
  --shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
  --border-radius: 16px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body { 
  margin-left: 0px !important; 
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.content-wrapper { 
  background: linear-gradient(-45deg, #f8fafc, #e2e8f0, #f1f5f9, #ffffff);
  background-size: 400% 400%;
  animation: gradientShift 15s ease infinite;
  min-height: 100vh;
}

@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.container-fluid { 
  max-width: 1500px; 
  margin: 0 auto; 
  padding: 20px;
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
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
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
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
  border-bottom: 1px solid var(--glass-border);
  padding: 20px 24px;
}

/* Enhanced Info Boxes */
.info-box {
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  border: 1px solid var(--glass-border);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-soft);
  transition: var(--transition);
  overflow: hidden;
  position: relative;
  animation: slideInFromLeft 0.8s ease-out;
}

.info-box:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-hover);
}

.info-box-icon {
  border-radius: 12px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 24px !important;
  animation: iconPulse 2s ease-in-out infinite;
}

.info-box-icon.bg-info {
  background: var(--info-gradient) !important;
  box-shadow: 0 4px 15px rgba(66, 153, 225, 0.3) !important;
}

.info-box-icon.bg-success {
  background: var(--success-gradient) !important;
  box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3) !important;
}

.info-box-icon.bg-primary {
  background: var(--primary-gradient) !important;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
}

@keyframes iconPulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

@keyframes slideInFromLeft {
  0% { opacity: 0; transform: translateX(-30px); }
  100% { opacity: 1; transform: translateX(0); }
}

.info-box-content {
  padding: 20px !important;
}

.info-box-text {
  font-weight: 600 !important;
  color: #374151 !important;
  font-size: 14px !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}

.info-box-number {
  font-weight: 800 !important;
  color: #1f2937 !important;
  font-size: 28px !important;
}

/* Enhanced Table */
.table { 
  color: #1f2937;
  border-radius: var(--border-radius);
  overflow: hidden;
}

.table td, .table th { 
  padding: 12px 16px !important; 
  vertical-align: middle;
  border-color: var(--glass-border) !important;
}

.table thead th {
  background: linear-gradient(135deg, #374151, #4b5563) !important;
  color: #fff !important;
  font-weight: 600 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  font-size: 12px !important;
}

.table tbody tr {
  transition: var(--transition);
}

.table tbody tr:hover {
  background-color: rgba(102, 126, 234, 0.05) !important;
  transform: scale(1.01);
}

/* Enhanced User Images */
img.img-circle { 
  width: 50px; 
  height: 50px; 
  object-fit: cover; 
  border-radius: 50%;
  border: 3px solid rgba(102, 126, 234, 0.2);
  box-shadow: var(--shadow-soft);
  transition: var(--transition);
  animation: profileFloat 3s ease-in-out infinite;
}

img.img-circle:hover {
  transform: scale(1.1);
  box-shadow: var(--shadow-hover);
}

@keyframes profileFloat {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-3px); }
}

/* Enhanced Search Input */
#searchInput, .form-control { 
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  color: #374151;
  border: 2px solid var(--glass-border);
  border-radius: 12px;
  padding: 12px 16px;
  transition: var(--transition);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
}

#searchInput:focus, .form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1), inset 0 2px 4px rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
  outline: none;
}

#searchInput::placeholder, .form-control::placeholder { 
  color: #6b7280;
  font-weight: 500;
}

/* Enhanced Navbar */
.main-header {
  background: var(--glass-bg) !important;
  backdrop-filter: blur(20px) !important;
  border-bottom: 1px solid var(--glass-border) !important;
  box-shadow: var(--shadow-soft) !important;
}

.navbar-nav .nav-link {
  color: #374151 !important;
  font-weight: 600 !important;
  transition: var(--transition) !important;
  border-radius: 8px !important;
  margin: 0 4px !important;
}

.navbar-nav .nav-link:hover {
  background: rgba(102, 126, 234, 0.1) !important;
  color: #667eea !important;
  transform: translateY(-2px) !important;
}

/* Enhanced Buttons */
.btn {
  border-radius: 12px !important;
  font-weight: 600 !important;
  transition: var(--transition) !important;
  box-shadow: var(--shadow-soft) !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}

.btn:hover {
  transform: translateY(-2px) !important;
  box-shadow: var(--shadow-hover) !important;
}

.btn-navbar {
  background: var(--primary-gradient) !important;
  border: none !important;
  color: white !important;
}

/* Enhanced Breadcrumb */
.breadcrumb {
  background: transparent !important;
  padding: 0 !important;
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

/* Enhanced Content Header */
.content-header {
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  border: 1px solid var(--glass-border);
  border-radius: var(--border-radius);
  margin: 20px;
  padding: 24px;
  box-shadow: var(--shadow-soft);
  animation: slideInFromTop 0.8s ease-out;
}

@keyframes slideInFromTop {
  0% { opacity: 0; transform: translateY(-30px); }
  100% { opacity: 1; transform: translateY(0); }
}

.content-header h1 {
  color: #1f2937 !important;
  font-weight: 700 !important;
  margin: 0 !important;
}

/* Enhanced Users List */
.users-list li {
  transition: var(--transition) !important;
  border-radius: 12px !important;
  padding: 12px !important;
  margin: 8px !important;
}

.users-list li:hover {
  background: rgba(102, 126, 234, 0.05) !important;
  transform: translateY(-2px) !important;
  box-shadow: var(--shadow-soft) !important;
}

.users-list-name {
  font-weight: 600 !important;
  color: #374151 !important;
}

.users-list-date {
  color: #6b7280 !important;
  font-size: 12px !important;
}

/* Enhanced Badge */
.badge {
  border-radius: 20px !important;
  padding: 6px 12px !important;
  font-weight: 600 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}

.badge-danger {
  background: var(--secondary-gradient) !important;
  box-shadow: 0 2px 8px rgba(240, 147, 251, 0.3) !important;
}

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
  .container-fluid {
    padding: 10px;
  }
  
  .content-header {
    margin: 10px;
    padding: 16px;
  }
  
  .info-box-number {
    font-size: 24px !important;
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
        <a href="#" class="nav-link">Home</a>      </li>
      
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
 
      
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
      <img src="../images/logo.png" alt=" Logo"  width="155" height="99" class="" style="opacity: .8">
	        <span class="brand-text font-weight-light"></span>    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../<?php echo $row2['photo'];    ?>" alt="User Image" width="220" height="192" class="img-circle elevation-2">        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $row2['fullname'];  ?></a>
        </div>
      </div>

     

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
         
		 <?php
			   include('sidebar.php');
			   
			   ?>
		 
		 
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  
  <?php
$query = "SELECT * FROM students "; 
$result = mysqli_query($conn, $query); 

if ($result) 
{ 
 // it return number of rows in the table. 
 $row_students = mysqli_num_rows($result); 
   
}
$query = "SELECT * FROM admin "; 
$result = mysqli_query($conn, $query); 

if ($result) 
{ 
 // it return number of rows in the table. 
 $row_users = mysqli_num_rows($result); 
   
}                  

 
  ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
		
          <!-- /.col -->
		   <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fa  fa-users" id="icon"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">No. Of Student(s) </span>
                <span class="info-box-number">
                  <?php  echo $row_students;   ?>
                  <small></small>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fa fa-user" id="icon"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">No. Of User(s) </span>
                <span class="info-box-number">
                  <?php  echo $row_students;   ?>
                  <small></small>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>


          <?php
 //Get total amt paid as fee
 $sql = "select SUM(amount) as tot_pay from payment"; 
 $result = $conn->query($sql);
 $rowpayment = mysqli_fetch_array($result);
 $tot_pay=$rowpayment['tot_pay'];
          ?>
          	  
		   <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-dollar-sign"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Amount Paid </span>
                <span class="info-box-number">NGN<?php echo number_format((float) $tot_pay ,2); ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-8">
            <!-- MAP & BOX PANE -->
            <!-- /.card -->
<div class="row">
              <div class="col-md-6">
                <!-- DIRECT CHAT -->
                <!--/.direct-chat -->
</div>
              <!-- /.col -->

              <div class="col-md-6">
                <!-- USERS LIST -->
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Latest Student(s) </h3>
                    <?php 
                 
    $query = "SELECT * FROM students "; 
       $result = mysqli_query($conn, $query); 
      
    if ($result) 
    { 
        // it return number of rows in the table. 
        $row_students = mysqli_num_rows($result); 
          
    }           
    ?>

	
	
	
	
                    <div class="card-tools">
                      <span class="badge badge-danger"><?php echo $row_students;  ?> New Student(s) </span>
                      <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                      </button>
                      <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>
				  <?php 
										
$sql = "SELECT * FROM students ORDER BY ID DESC LIMIT 8;";
$result = $conn->query($sql);
while($row_new_students = $result->fetch_assoc()) { 
?>
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <ul class="users-list clearfix">
                      <li>
                        <img src="../<?php echo $row_new_students['photo'];  ?>" alt="students Image">
                        <a class="users-list-name" href="#"><?php echo $row_new_students['fullname'];  ?></a>
                        <span class="users-list-date"><?php echo $row_new_students['matric_no'];  ?></span>
                      </li>
					   <?php    }  ?>
                    </ul>
					
                    <!-- /.users-list -->
                  </div>
				 
                  <!-- /.card-body -->
                 
                  <!-- /.card-footer -->
                </div>
                <!--/.card -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- TABLE: LATEST ORDERS -->
            <!-- /.card -->
</div>
          <!-- /.col -->

          <div class="col-md-4">
            <!-- Info Boxes Style 2 -->
            <!-- /.info-box -->
            <!-- /.info-box -->
            <!-- /.info-box -->
            <!-- /.info-box -->
<div class="card">
  <!-- /.card-header -->

              <!-- /.card-body -->
              <!-- /.footer -->
</div>
            <!-- /.card -->

            <!-- PRODUCT LIST -->
            <!-- /.card -->
</div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong><?php include('../footer.php');  ?></strong>
  
    <div class="float-right d-none d-sm-inline-block">
   
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="plugins/raphael/raphael.min.js"></script>
<script src="plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="plugins/jquery-mapael/maps/usa_states.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>

<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard2.js"></script>
</body>
</html>
