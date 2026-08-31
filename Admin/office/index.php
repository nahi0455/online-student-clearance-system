<?php
session_start();
include('../connect.php');
error_reporting(0);

// ✅ Access control: Only Library staff can access this page
if ($_SESSION['role'] != 'library_chief') {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['admin-username'];

// ✅ Fetch library officer info
$sql = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($conn, $sql);
$row_admin = mysqli_fetch_array($result);

// ✅ Get all students (no department filter)
$query_students = "SELECT * FROM students ORDER BY fullname ASC";
$result_students = mysqli_query($conn, $query_students);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Library Clearance | Chief of Circulation</title>
<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
          <i class="fas fa-bars"></i>
        </a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Library Clearance</a>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link text-center">
      <span class="brand-text font-weight-light">Chief of Circulation</span>
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
        <h4>Library Clearance Requests</h4>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h3 class="card-title">All Students</h3>
          </div>

          <div class="card-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Fullname</th>
                  <th>Photo</th>
                  <th>Matric No</th>
                  <th>Session</th>
                  <th>Library Status</th>
                  <th>Approve</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $sn = 1;
                while($row = mysqli_fetch_assoc($result_students)) {
                    $lib_status = ($row['is_library_approved']) ? "✅ Cleared" : "❌ Pending";

                    echo "<tr>
                        <td>{$sn}</td>
                        <td>{$row['fullname']}</td>
                        <td><img src='../{$row['photo']}' width='70' height='70'></td>
                        <td>{$row['matric_no']}</td>
                        <td>{$row['session']}</td>
                        <td>{$lib_status}</td>
                        <td>";

                    if (!$row['is_library_approved']) {
                        echo "<form method='POST' action='approve_library.php' style='display:inline-block;'>
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
</body>
</html>
