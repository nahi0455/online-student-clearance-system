<?php
session_start();
include('../connect.php');
error_reporting(0);

// ✅ Access control: Only logged-in admin users
if (empty($_SESSION['admin-username'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['admin-username'];
$dept = isset($_SESSION['department']) ? $_SESSION['department'] : '';
if ($dept === '') {
    $stmt = $conn->prepare("SELECT department FROM admin WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $rowd = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    if ($rowd && !empty($rowd['department'])) {
        $dept = $rowd['department'];
        $_SESSION['department'] = $dept;
    }
}


// ✅ Fetch department head info (optional, for sidebar or profile)
$sql = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($conn, $sql);
$row_admin = mysqli_fetch_array($result);

// ✅ Get students only in this department
$query_students = "SELECT * FROM students WHERE dept='$dept' ORDER BY fullname ASC";
$result_students = mysqli_query($conn, $query_students);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Department Dashboard | <?php echo $dept; ?> Department</title>
<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../dist/css/adminlte.min.css">
<style>
  .table td, .table th {
    padding: 4px 6px !important;
    vertical-align: middle !important;
  }
  img.img-circle {
    width: 45px;
    height: 45px;
    object-fit: cover;
  }
  .card-body {
    padding: 10px !important;
  }
  .card {
    margin: 10px;
  }
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Department Dashboard</a>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link text-center">
      <span class="brand-text font-weight-light">Dept: <?php echo $dept; ?></span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../<?php echo $_SESSION['photo']; ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['fullname']; ?></a>
        </div>
      </div>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h4><?php echo $dept; ?> Department Clearance List</h4>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h3 class="card-title">Students Under <?php echo $dept; ?> Department</h3>
          </div>

          <div class="card-body">
<table class="table table-sm table-bordered table-striped table-hover" style="font-size:13px;">
  <thead class="bg-primary text-white">
    <tr>
      <th>#</th>
      <th>Fullname</th>
      <th>Photo</th>
      <th>Matric No</th>
      <th>Session</th>
      <th>Department Head</th>
      <th>Library</th>
      <th>Bookstore</th>
      <th>Dormitory</th>
      <th>Student Cafeteria</th>
      <th>Sport Master</th>
      <th>Student Dean</th>
      <th>Campus Police</th>
      <th>Registrar Stamp</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
<?php 
$sn = 1;
while($row = mysqli_fetch_assoc($result_students)) {

    echo "<tr>
        <td>{$sn}</td>
        <td>{$row['fullname']}</td>
        <td><img src='../{$row['photo']}' width='60' height='60' class='img-circle'></td>
        <td>{$row['matric_no']}</td>
        <td>{$row['session']}</td>

        <td>".($row['is_department_approved'] ? '✅' : '❌')."</td>
        <td>".($row['is_library_approved'] ? '✅' : '❌')."</td>
        <td>".($row['is_bookstore_approved'] ? '✅' : '❌')."</td>
        <td>".($row['is_dormitory_approved'] ? '✅' : '❌')."</td>
        <td>".($row['is_cafeteria_approved'] ? '✅' : '❌')."</td>
        <td>".($row['is_sport_approved'] ? '✅' : '❌')."</td>
        <td>".($row['is_dean_approved'] ? '✅' : '❌')."</td>
        <td>".($row['is_police_approved'] ? '✅' : '❌')."</td>
        <td>".($row['is_registrar_approved'] ? '✅' : '❌')."</td>

        <td>".(($row['is_department_approved']) ? '✅ Approved' : '❌ Pending')."</td>
        <td>";

    // Approval button
    if (!$row['is_department_approved']) {
        echo "<form method='POST' action='approve.php' style='display:inline-block;'>
                <input type='hidden' name='student_id' value='{$row['ID']}'>
                <button type='submit' name='btnapprove' class='btn btn-success btn-sm'>
                  Approve
                </button>
              </form>";
    } else {
        echo "<button class='btn btn-secondary btn-sm' disabled>Approved</button>";
    }

    echo "</td></tr>";
    $sn++;
}
?>
 
</tbody>

            </table>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer text-center">
    <strong>&copy; <?php echo date('Y'); ?> University Clearance System.</strong>
  </footer>
</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.js"></script>
</bo
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    header("Location: department_details.php?id=".$id);
    exit();
}
