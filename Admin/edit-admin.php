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
      
$username = $_SESSION["admin-username"];
$id=$_GET['id'];

date_default_timezone_set('Africa/Lagos');
$current_date = date('Y-m-d H:i:s');

$sql = "select * from admin where username='$username'"; 
$result = $conn->query($sql);
$rowaccess= mysqli_fetch_array($result);

if(isset($_POST["btnedit"]))
{

$fullname = mysqli_real_escape_string($conn,$_POST['txtfullname']);
$email = mysqli_real_escape_string($conn,$_POST['txtemail']);
$designation = mysqli_real_escape_string($conn,$_POST['cmddesignation']);
$role = mysqli_real_escape_string($conn, $_POST['role']);
$department = isset($_POST['department']) ? mysqli_real_escape_string($conn, $_POST['department']) : NULL;


$sql = " update admin set fullname='$fullname',email='$email', designation='$designation', role='$role', department='$department' where ID='$id'";
if (mysqli_query($conn, $sql)) {

header("Location: admin-record.php");
}else{
$_SESSION['error']='Editing Was Not Successful';

}
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit User Profile|Admin Dashboard</title>
 <link rel="icon" type="image/png" sizes="16x16" href="../images/favicon.png">
  <!-- Google Font: Source Sans Pro -->
   <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">

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
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<style>
/* CRITICAL: Professional Edit Admin Styling */
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
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
  background: #ffffffff !important;
  min-height: 100vh !important;
  color: #0f172a !important;
}

/* Enhanced Card */
.card { 
  border-radius: var(--border-radius) !important;
  border: 1px solid #9f6540 !important;
  background: var(--glass-bg) !important;
  backdrop-filter: blur(20px) !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06) !important;
  transition: var(--transition) !important;
  animation: cardSlideIn 0.8s ease-out !important;
  overflow: hidden !important;
  margin: 20px auto !important;
  max-width: 800px !important;
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
}

.card-title {
  color: white !important;
  font-weight: 700 !important;
  font-size: 18px !important;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
  margin: 0 !important;
}

.card-title::before {
  content: '✏️';
  margin-right: 10px;
  font-size: 24px;
}

/* Enhanced Form Controls */
.form-control { 
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(10px) !important;
  color: #374151 !important;
  border: 2px solid #9f6540 !important;
  border-radius: 12px !important;
  padding: 14px 18px !important;
  transition: var(--transition) !important;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05) !important;
  font-weight: 500 !important;
  font-size: 15px !important;
}

.form-control:focus {
  border-color: #9f6540 !important;
  box-shadow: 0 0 0 3px rgba(159, 101, 64, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.05) !important;
  transform: translateY(-2px) !important;
  outline: none !important;
  background: rgba(255, 255, 255, 1) !important;
}

.form-control::placeholder { 
  color: #6b7280 !important;
  font-weight: 500 !important;
}

.form-group {
  margin-bottom: 25px !important;
  animation: formSlide 0.6s ease-out !important;
}

@keyframes formSlide {
  0% { opacity: 0; transform: translateX(-20px); }
  100% { opacity: 1; transform: translateX(0); }
}

.form-group label {
  font-weight: 700 !important;
  color: #374151 !important;
  margin-bottom: 10px !important;
  display: block !important;
  font-size: 15px !important;
  text-transform: uppercase !important;
  letter-spacing: 0.8px !important;
}

.form-group label::before {
  content: '▸';
  margin-right: 8px;
  color: #9f6540;
  font-size: 16px;
}

/* Enhanced Select Dropdown */
select.form-control {
  cursor: pointer !important;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e") !important;
  background-position: right 12px center !important;
  background-repeat: no-repeat !important;
  background-size: 16px 12px !important;
  padding-right: 40px !important;
}

/* Enhanced Buttons */
.btn {
  border-radius: 12px !important;
  font-weight: 600 !important;
  transition: var(--transition) !important;
  box-shadow: var(--shadow-soft) !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  padding: 12px 32px !important;
  font-size: 15px !important;
}

.btn-primary {
  background: var(--success-gradient) !important;
  border: none !important;
  color: white !important;
  box-shadow: 0 6px 20px rgba(159, 101, 64, 0.4) !important;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #8b5a3c 0%, #7a4d33 100%) !important;
  transform: translateY(-3px) !important;
  box-shadow: 0 10px 30px rgba(159, 101, 64, 0.5) !important;
}

.btn-primary:active {
  transform: translateY(-1px) !important;
}

/* Card Footer */
.card-footer {
  background: rgba(255, 255, 255, 0.5) !important;
  backdrop-filter: blur(10px) !important;
  border-top: 1px solid var(--glass-border) !important;
  padding: 20px 24px !important;
  border-radius: 0 0 var(--border-radius) var(--border-radius) !important;
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

/* Professional Sidebar Styling */
.main-sidebar {
  background: linear-gradient(180deg, #151618ff 0%, #007bff 100%) !important;
  box-shadow: 4px 0 20px rgba(102, 126, 234, 0.15) !important;
  border-right: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.brand-link {
  background: linear-gradient(135deg, #007bff 100%, #ccccff 0%) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
  padding: 20px 15px !important;
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

.nav-sidebar .nav-link .nav-icon {
  margin-right: 10px !important;
  font-size: 16px !important;
}

.nav-sidebar .nav-link p {
  margin: 0 !important;
  font-weight: 500 !important;
  font-size: 13px !important;
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

/* Enhanced Footer */
.main-footer {
  background: rgba(255, 255, 255, 0.1) !important;
  backdrop-filter: blur(10px) !important;
  border-top: 1px solid var(--glass-border) !important;
  color: #374151 !important;
}

/* Form Animation States */
.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }

/* Responsive Design */
@media (max-width: 768px) {
  .card {
    margin: 10px !important;
    border-radius: 12px !important;
  }
  
  .card-header {
    padding: 15px 20px !important;
  }
  
  .form-control {
    padding: 10px 14px !important;
  }
  
  .btn {
    padding: 10px 20px !important;
  }
}
</style>
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>      </li>
      <li class="nav-item d-none d-sm-inline-block">
        
        <a href="admin-record.php" class="nav-link">Home</a></li>
      
    </ul>

 

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
 
      
    </ul>
  </nav>

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
              <li class="breadcrumb-item"><a href="admin-record.php">Home</a></li>
              <li class="breadcrumb-item active">Edit User </li>
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
                <h3 class="card-title">Edit User Profile </h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
             <form  action="" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                 
				   <div class="form-group">
                    <label for="exampleInputEmail1">Fullname </label>
                    <input type="text" class="form-control" name="txtfullname" id="exampleInputEmail1" size="77" value="<?php echo $rowaccess['fullname'];   ?>" placeholder="Enter Fullname">
                  </div>
                 
				  <div class="form-group">
                    <label for="exampleInputPassword1">Email</label>
                    <input type="text" class="form-control" name="txtemail" id="exampleInputPassword1" size="77" value="<?php echo $rowaccess['email'];   ?>" placeholder="Enter Email">
                  </div>		                       
                </div>
                <!-- /.card-body -->
 
                <div class="card-footer">
                  <button type="submit" name="btnedit" class="btn btn-primary">Update</button>
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
// Role selection logic
document.getElementById('role').addEventListener('change', function () {
    const isHead = this.value === 'department_head';
    document.getElementById('facultyGroup').style.display = isHead ? 'block' : 'none';
    document.getElementById('departmentGroup').style.display = 'none';
});

// All faculties and their departments
const facultyDepartments = {
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
        "Computer Science and Engineering",
        "Information System",
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
        "Soil Resource & Water Shade Mgmt",
        "Eco-Tourism & Biodiversity",
        "Agri-Business & Value Chain"
    ],
    "College of Business & Economics": [
        "Accounting and Finance",
        "Economics",
        "Management",
        "Logistics & Supply Chain",
        "Marketing"
    ],
    "College of Social Science": [
        "Afan Oromo",
        "Oromo Folklore",
        "Civics",
        "Governance",
        "English",
        "Geography",
        "History & Heritage",
        "Tourism",
        "Journalism & Communication",
        "Social Anthropology",
        "Sociology & Social Work"
    ],
    "Institutes": [
        "Gadaa & Culture Studies",
        "Medicine",
        "Nursing",
        "Midwifery",
        "Public Health",
        "Anesthesia",
        "Clinical Pharmacy",
        "Medical Radiology Tech",
        "Medical Lab Tech",
        "Health Informatics",
        "Environmental Health",
        "Psychiatry"
    ]
};

// Show departments after selecting faculty
document.getElementById('faculty').addEventListener('change', function () {
    const faculty = this.value;
    const deptSelect = document.getElementById('department');

    deptSelect.innerHTML = '<option value="">🏢 Select Department</option>';

    if (faculty && facultyDepartments[faculty]) {
        facultyDepartments[faculty].forEach(function (dept) {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            deptSelect.appendChild(option);
        });

        document.getElementById('departmentGroup').style.display = 'block';
    } else {
        document.getElementById('departmentGroup').style.display = 'none';
    }
});
</script>

</body>
</html>
