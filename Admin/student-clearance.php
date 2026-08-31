<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('connect.php');

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
  <title>Student Clearance|Dashboard</title>
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
  
  <style>
/* Modern Student Clearance Styling */
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
  content: '✅';
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
  background: linear-gradient(135deg, #9f6540 0%, #8b5a3c 100%);
  color: white;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border: none;
  padding: 15px 8px;
  font-size: 12px;
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
  padding: 12px 8px;
  vertical-align: middle;
  border-color: rgba(159, 101, 64, 0.2);
  font-size: 13px;
}

/* Enhanced Badges */
.badge {
  font-size: 11px;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.badge-success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.badge-warning {
  background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
  color: #212529;
  box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
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

.main-sidebar {
  background-color: #1F2A44 !important;
  box-shadow: none !important;
  border-right: 1px solid rgba(148, 163, 184, 0.15) !important;
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

/* Modern Sidebar Styling */
.brand-section {
    padding: 16px 20px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.15);
}

.brand-logo {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 255, 255, 0.08);
    color: #F5F7FA;
    font-size: 18px;
}

.brand-info {
    display: flex;
    flex-direction: column;
}

.brand-text {
    font-size: 16px;
    font-weight: 700;
    color: #F5F7FA;
    margin-bottom: 2px;
}

.brand-dept {
    font-size: 12px;
    color: #C7CDD6;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.sidebar-nav {
    padding: 8px 0 0 0;
}

.nav-header {
    padding: 12px 20px;
    margin-bottom: 4px;
}

.nav-header-text {
    font-size: 11px;
    font-weight: 600;
    color: #C7CDD6;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

.nav-item {
    margin-bottom: 2px;
    position: relative;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 10px 24px;
    color: #F5F7FA;
    text-decoration: none;
    border-radius: 8px;
    margin-right: 12px;
    transition: color .2s ease, background-color .2s ease;
    position: relative;
    overflow: hidden;
    font-weight: 500;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background: #2E5AAC;
    color: #F5F7FA;
    box-shadow: none;
    border-left-color: #8B5A2B;
}

.nav-link.active {
    background: #2E5AAC;
    color: #F5F7FA;
    box-shadow: none;
    border-left-color: #8B5A2B;
}

.nav-icon {
    width: 20px;
    font-size: 16px;
    margin-right: 12px;
    transition: color .2s ease;
    color: #C7CDD6;
}

.nav-link:hover .nav-icon {
    color: #ffffff;
}

.nav-link.active .nav-icon {
    color: #ffffff;
}

.nav-text {
    flex: 1;
    font-size: 14px;
}

.nav-arrow {
    font-size: 12px;
    transition: all 0.3s ease;
}

.nav-item.menu-open .nav-arrow {
    transform: rotate(-90deg);
}

.nav-treeview {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    background: rgba(248, 250, 252, 0.08);
    margin: 0 16px 0 0;
    border-radius: 0 15px 15px 0;
}

.nav-item.menu-open .nav-treeview {
    max-height: 300px;
}

.nav-treeview .nav-link {
    padding: 10px 24px 10px 56px;
    font-size: 13px;
    margin-right: 0;
    border-radius: 0;
}

.nav-treeview .nav-icon {
    width: 16px;
    font-size: 12px;
    margin-right: 10px;
    opacity: 0.9;
}

.logout-item .nav-link {
    color: #dc2626;
    margin-top: 8px;
    border-top: 1px solid rgba(226, 232, 240, 0.5);
    padding-top: 12px;
}

.logout-item .nav-link:hover {
    background: rgba(220, 38, 38, 0.1);
    color: #dc2626;
}
</style>
  
 <script type="text/javascript">
function clear_student(matric_no){
if(confirm("ARE YOU SURE YOU WISH TO CLEAR STUDENT WITH MATRIC NO. " + " " + matric_no + " " + " FOR NYSC/GRADUATION ?"))
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
        <a href="#" class="nav-link">Home</a>     
         </li>
      
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

  <!-- Enhanced Modern Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <div class="brand-section">
        <a href="index.php" class="brand-link">
            <div class="brand-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="brand-info">
                <span class="brand-text">Admin Portal</span>
                <span class="brand-dept">BULE HORA UNIVERSITY</span>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <div class="nav-header">
            <span class="nav-header-text">MAIN NAVIGATION</span>
        </div>
        
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                    <div class="nav-ripple"></div>
                </a>
            </li>

            <li class="nav-item has-treeview menu-open">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-user-graduate"></i>
                    <span class="nav-text">Student Management</span>
                    <i class="right fas fa-angle-left nav-arrow"></i>
                    <div class="nav-ripple"></div>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="student-record.php" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <span class="nav-text">All Students</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="add-student.php" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <span class="nav-text">Add Student</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="student-clearance.php" class="nav-link active">
                            <i class="far fa-circle nav-icon"></i>
                            <span class="nav-text">Pending Clearances</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="changepassword.php" class="nav-link">
                    <i class="nav-icon fas fa-key"></i>
                    <span class="nav-text">Change Password</span>
                    <div class="nav-ripple"></div>
                </a>
            </li>

            <li class="nav-item logout-item">
                <a href="../logout.php" class="nav-link">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    <span class="nav-text">Logout</span>
                    <div class="nav-ripple"></div>
                </a>
            </li>
        </ul>
    </nav>
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
              <li class="breadcrumb-item active">Student Clearance</li>
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
                  <h4>Student Clearance Status</h4>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table width="85%" align="center" class="table table-bordered table-striped" id="example1">
 <thead>
  <tr align="center">
    <th>Fullname</th>
    <th>Photo</th>
    <th>Matric No</th>
    <th>Department Head</th>
    <th>Library</th>
    <th>Book Store</th>
    <th>Dormitory</th>
    <th>Cafeteria</th>
    <th>Sport Master</th>
    <th>Student Dean</th>
    <th>Campus Police</th>
    <th>Registrar</th>
     <th>finish

  </tr>
</thead>

                      <div align="center"></div>
                    
 <tbody>
<?php 
$sql = "SELECT * FROM students ORDER BY ID ASC";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) { ?>
<tr align="center">
  <td><?php echo $row['fullname']; ?></td>
  <td><img src="../<?php echo $row['photo']; ?>" width="70" height="70" class="img-circle"></td>
  <td><?php echo $row['matric_no']; ?></td>
  <td><?php echo $row['dept']; ?></td>

  <!-- Department Head -->
  <td>
    <?php if ($row['is_department_approved'] == 1) { ?>
      <span class="badge badge-success">Cleared</span>
    <?php } else { ?>
      <span class="badge badge-warning">Pending</span>
    <?php } ?>
  </td>

  <!-- Library -->
  <td>
    <?php if ($row['is_library_approved'] == 1) { ?>
      <span class="badge badge-success">Cleared</span>
    <?php } else { ?>
      <span class="badge badge-warning">Pending</span>
    <?php } ?>
  </td>

  <!-- Book Store -->
  <td>
    <?php if ($row['is_bookstore_approved'] == 1) { ?>
      <span class="badge badge-success">Cleared</span>
    <?php } else { ?>
      <span class="badge badge-warning">Pending</span>
    <?php } ?>
  </td>

  <!-- Dormitory -->
  <td>
    <?php if ($row['is_dormitory_approved'] == 1) { ?>
      <span class="badge badge-success">Cleared</span>
    <?php } else { ?>
      <span class="badge badge-warning">Pending</span>
    <?php } ?>
  </td>

  <!-- Cafeteria -->
  <td>
    <?php if ($row['is_cafeteria_approved'] == 1) { ?>
      <span class="badge badge-success">Cleared</span>
    <?php } else { ?>
      <span class="badge badge-warning">Pending</span>
    <?php } ?>
  </td>

  <!-- Sport Master -->
  <td>
    <?php if ($row['is_sport_approved'] == 1) { ?>
      <span class="badge badge-success">Cleared</span>
    <?php } else { ?>
      <span class="badge badge-warning">Pending</span>
    <?php } ?>
  </td>

  <!-- Student Dean -->
  <td>
    <?php if ($row['is_dean_approved'] == 1) { ?>
      <span class="badge badge-success">Cleared</span>
    <?php } else { ?>
      <span class="badge badge-warning">Pending</span>
    <?php } ?>
  </td>

  <!-- Campus Police -->
  <td>
    <?php if ($row['is_police_approved'] == 1) { ?>
      <span class="badge badge-success">Cleared</span>
    <?php } else { ?>
      <span class="badge badge-warning">Pending</span>
    <?php } ?>
  </td>

  <!-- Registrar -->
  <td>
    <?php if ($row['is_registrar_approved'] == 1) { ?>
      <span class="badge badge-success">complete
    <?php } else { ?>
      <span class="badge badge-warning">Pending</span>
    <?php } ?>
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
