<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../connect.php');

if(empty($_SESSION['admin-username']))
{   
    header("Location: login.php"); 
    exit();
}

$username = $_SESSION['admin-username'];

date_default_timezone_set('Africa/Lagos');
$current_date = date('Y-m-d');	

$sql = "select * from admin where username='$username'"; 
$result = $conn->query($sql);
$row = mysqli_fetch_array($result);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Record|Dashboard</title>
<link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../Admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  
  <link rel="stylesheet" href="../Admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../Admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  
  <link rel="stylesheet" href="../Admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../Admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../Admin/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../Admin/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../Admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../Admin/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../Admin/plugins/summernote/summernote-bs4.min.css">
  
  <style>
/* Modern Student Record Styling */
:root {
  --primary-gradient: linear-gradient(135deg, #007bff 0%, #ccccff 100%);
  --success-gradient: linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%);
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

.card-header h4 {
  color: white !important;
  font-weight: 700 !important;
  font-size: 18px !important;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  margin: 0 !important;
}

.card-header h4::before {
  content: '📊';
  margin-right: 10px;
  font-size: 24px;
}

/* Enhanced Table */
.table {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.table thead th {
  background: linear-gradient(135deg, #070707ff 0%, #403de7ff 100%);
  color: white;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border: none;
  padding: 15px 10px;
}

.table tbody tr {
  transition: all 0.3s ease;
}

.table tbody tr:hover {
  background: rgba(159, 101, 64, 0.1);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.table tbody td {
  padding: 12px 10px;
  vertical-align: middle;
  border-color: rgba(159, 101, 64, 0.2);
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

/* Content Wrapper */
.content-wrapper {
  background: transparent !important;
  padding: 20px;
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
</style>
  
 <script type="text/javascript">
function Activate(fullname){
if(confirm("ARE YOU SURE YOU WISH TO Activate " + " " + username+ " " + " ON THE LIST?"))
{
return  true;
}
else {return false;
}
	 
}
</script>
<script type="text/javascript">
function Deactivate(fullname){
if(confirm("ARE YOU SURE YOU WISH TO Deactivate " + " " + username+ " " + " ON THE LIST?"))
{
return  true;
}
else {return false;
}
	 
}

</script>
<script type="text/javascript">
		function deldata(fullname){
if(confirm("ARE YOU SURE YOU WISH TO DELETE " + " " + fullname+ "FROM THE DATABASE ?" ))
{
return  true;
}
else {return false;
}
	 
}

</script>
  <style type="text/css">
<!--
.style7 {vertical-align:middle; cursor:pointer; -webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none; border:1px solid transparent; padding:.375rem .75rem; line-height:1.5; border-radius:.25rem;transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out; display: inline-block; color: #212529; text-align: center;}
-->
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

 

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
 
      
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Enhanced Modern Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="dashboard.php" class="brand-link">
      <img src="../Admin/images/logo.png" alt="University Logo" class="brand-logo" onerror="this.style.display='none'">
      <span class="brand-text">BULE HORA UNIVERSITY<br><small>Student Records</small></span>
    </a>

    <div class="sidebar">
        <nav>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
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
                        <a href="student-record.php" class="nav-link active">
                            <i class="far fa-circle nav-icon"></i>
                            <p>All Students</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="add-student.php" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Add Student</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="changepassword.php" class="nav-link">
                    <i class="nav-icon fas fa-key"></i>
                    <p>Change Password</p>
                </a>
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
              <li class="breadcrumb-item active">Student Record</li>
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
          <table width="1204" height="227" border="0" align="center">
            <tr>
              <td width="1090" height="184"><div class="card">
                <div class="card-header">
                  <h4>Student Record </h4>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table width="85%" align="center" class="table table-bordered table-striped" id="example1">
                    <thead>
                    <th width="10%"><div align="center">Fullname</div></th>
							          <th width="7%"><div align="center">Photo</div></th>
                        <th width="5%"><div align="center">Phone</div></th>
                        <th width="5%"><div align="center">Matric No</div></th>
                        <th width="5%"><div align="center">Password</div></th>
                        <th width="6%"><div align="center">Faculty</div></th>
						           <th width="5%"><div align="center">Dept</div></th>
                       <th width="5%"><div align="center">Action</div></th>

				     						    </tr>
                    </thead>
                      <div align="center"></div>
                    
                    <tbody>
                                      <?php 
                                          $sql = "SELECT * FROM register order by ID ASC";
                                           $result = $conn->query($sql);
                                           while($row = $result->fetch_assoc()) { ?>
                      <tr class="gradeX">
                        <td height="104"><div align="center"><?php echo $row['fullname']; ?> </div></td>
					 <td><div align="center"><span class="controls"><img src="../<?php echo $row['photo'];?>"  width="91" height="73" border="2"/></span></div></td>
                        <td><div align="center"><?php echo $row['phone']; ?></div></td>
                        <td><div align="center"><?php echo $row['matric_no']; ?></div></td>
                        <td><div align="center"><?php echo $row['password']; ?></div></td>
                        <td><div align="center"><?php echo $row['faculty']; ?></div></td>
                        <td><div align="center"><?php echo $row['dept']; ?></div></td>
                        <td>     <div class="btn-group">
                    <button type="button" class="btn btn-danger btn-flat">Action</button>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                  </div>
                </td>
                    </tr>
					<?php } ?>
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
 <?php  include('../footer.php');   ?>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../Admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../Admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="../Admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../Admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../Admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../Admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="../Admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../Admin/dist/js/demo.js"></script>
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
  });
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
});
</script>
</body>
</html>
