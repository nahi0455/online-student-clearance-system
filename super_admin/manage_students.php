<?php
session_start();
error_reporting(0);
include('../connect.php');

$success = '';
$error = '';
$found_students = [];
$requested_year_students = [];
$filter_year = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'search_student') {
    $q = trim($_POST['q'] ?? '');
    if ($q !== '') {
      $like = '%' . $q . '%';
      $stmt = $conn->prepare("SELECT fullname, matric_no, session, faculty, dept, phone, request_year FROM students WHERE matric_no = ? OR fullname LIKE ? ORDER BY ID DESC LIMIT 50");
      if ($stmt) {
        $stmt->bind_param('ss', $q, $like);
        if ($stmt->execute()) { $res = $stmt->get_result(); while($row = $res->fetch_assoc()) { $found_students[] = $row; } }
        $stmt->close();
      }
      if (empty($found_students)) { $error = 'No matching students found.'; }
    }
  } elseif ($action === 'update_student') {
    $matric = trim($_POST['matric_no'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $sessionVal = trim($_POST['session'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $dept = trim($_POST['dept'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $request_year = trim($_POST['request_year'] ?? '');
    if ($matric === '') { $error = 'Matric number is required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET fullname=?, session=?, faculty=?, dept=?, phone=?, request_year = NULLIF(?, '') WHERE matric_no=?");
      if ($stmt) {
        $stmt->bind_param('sssssss', $fullname, $sessionVal, $faculty, $dept, $phone, $request_year, $matric);
        if ($stmt->execute()) { $success = 'Student record updated.'; } else { $error = 'Failed to update student.'; }
        $stmt->close();
      } else { $error = 'Failed to prepare update.'; }
    }
  } elseif ($action === 'filter_year') {
    $y = trim($_POST['year'] ?? '');
    if ($y !== '' && ctype_digit($y)) {
      $filter_year = $y;
      $stmt = $conn->prepare("SELECT fullname, matric_no, session, faculty, dept, phone, request_year FROM students WHERE request_year = ? ORDER BY ID DESC LIMIT 500");
      if ($stmt) {
        $yy = intval($y);
        $stmt->bind_param('i', $yy);
        if ($stmt->execute()) { $res = $stmt->get_result(); while($row = $res->fetch_assoc()) { $requested_year_students[] = $row; } }
        $stmt->close();
      }
      if (empty($requested_year_students)) { $error = 'No requests found for year ' . htmlspecialchars($y); }
    } else { $error = 'Enter a valid year.'; }
  } elseif ($action === 'reset_request_year') {
    $matric = trim($_POST['matric_no'] ?? '');
    if ($matric === '') { $error = 'Matric number is required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE matric_no=?");
      if ($stmt) { $stmt->bind_param('s', $matric); if ($stmt->execute()) { $success = 'Request year reset for ' . htmlspecialchars($matric) . '.'; } else { $error = 'Failed to reset.'; } $stmt->close(); }
    }
  } elseif ($action === 'set_request_year') {
    $matric = trim($_POST['matric_no'] ?? '');
    $y = trim($_POST['year'] ?? '');
    if ($matric === '' || $y === '' || !ctype_digit($y)) { $error = 'Valid matric and year required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=? WHERE matric_no=?");
      if ($stmt) { $yy = intval($y); $stmt->bind_param('is', $yy, $matric); if ($stmt->execute()) { $success = 'Request year set to ' . $yy . ' for ' . htmlspecialchars($matric) . '.'; } else { $error = 'Failed to set year.'; } $stmt->close(); }
    }
  } elseif ($action === 'allow_notify') {
    $matric = trim($_POST['matric_no'] ?? '');
    $s = trim($_POST['session'] ?? '');
    if ($matric === '') { $error = 'Matric number is required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE matric_no=?");
      if ($stmt) { $stmt->bind_param('s', $matric); $stmt->execute(); $stmt->close(); }
      $subject = 'Clearance Re‑request Allowed';
      $msg = 'You may submit a new clearance request.' . ($s !== '' ? (' Session: ' . $s . '.') : '') . ' Day will be announced by Super Admin.';
      $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
      if ($nstmt) { $nstmt->bind_param('sss', $matric, $subject, $msg); if ($nstmt->execute()) { $success = 'Re‑request allowed and notification sent to ' . htmlspecialchars($matric) . '.'; } else { $error = 'Failed to notify student.'; } $nstmt->close(); }
    }
  } elseif ($action === 'reset_session_all_notify') {
    $s = trim($_POST['session'] ?? '');
    if ($s === '') { $error = 'Session is required to reset and notify.'; }
    else {
      $list = [];
      $sel = $conn->prepare("SELECT matric_no FROM students WHERE session=? AND request_year IS NOT NULL");
      if ($sel) { $sel->bind_param('s', $s); if ($sel->execute()) { $res = $sel->get_result(); while($row = $res->fetch_assoc()) { $list[] = $row['matric_no']; } } $sel->close(); }
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE session=? AND request_year IS NOT NULL");
      if ($stmt) { $stmt->bind_param('s', $s); $stmt->execute(); $stmt->close(); }
      if (!empty($list)) {
        $subject = 'Clearance Re‑request Allowed';
        foreach($list as $m) {
          $msg = 'You may submit a new clearance request. Session: ' . $s . '. Day will be announced by Super Admin.';
          $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
          if ($nstmt) { $nstmt->bind_param('sss', $m, $subject, $msg); $nstmt->execute(); $nstmt->close(); }
        }
      }
      $success = 'All requests reset and notifications sent for session ' . htmlspecialchars($s) . '.';
    }
  } elseif ($action === 'reset_session_all') {
    $s = trim($_POST['session'] ?? '');
    if ($s === '') { $error = 'Session is required to reset.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE session=? AND request_year IS NOT NULL");
      if ($stmt) { $stmt->bind_param('s', $s); if ($stmt->execute()) { $success = 'All requests reset for session ' . htmlspecialchars($s) . '.'; } else { $error = 'Failed to reset session requests.'; } $stmt->close(); }
    }
  } elseif ($action === 'reset_year_all') {
    $y = trim($_POST['year'] ?? '');
    if ($y === '' || !ctype_digit($y)) { $error = 'Year is required to reset.'; }
    else {
      $yy = intval($y);
      $list = [];
      $sel = $conn->prepare("SELECT matric_no FROM students WHERE request_year=?");
      if ($sel) { $sel->bind_param('i', $yy); if ($sel->execute()) { $res = $sel->get_result(); while($row = $res->fetch_assoc()) { $list[] = $row['matric_no']; } } $sel->close(); }
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE request_year=?");
      if ($stmt) { $stmt->bind_param('i', $yy); $stmt->execute(); $stmt->close(); }
      if (!empty($list)) {
        $subject = 'Clearance Re‑request Allowed';
        foreach($list as $m) {
          $msg = 'You may submit a new clearance request. Year: ' . $yy . '. Day will be announced by Super Admin.';
          $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
          if ($nstmt) { $nstmt->bind_param('sss', $m, $subject, $msg); $nstmt->execute(); $nstmt->close(); }
        }
      }
      $success = 'All requests reset and notifications sent for year ' . htmlspecialchars($yy) . '.';
    }
  }
}

$recent_students = [];
$rs = mysqli_query($conn, "SELECT fullname, matric_no, session, faculty, dept, phone, request_year FROM students ORDER BY ID DESC LIMIT 10");
if ($rs) { while($row = mysqli_fetch_assoc($rs)) { $recent_students[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Students | BULE HORA UNIVERSITY</title>
  <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../Admin/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../Admin/dist/css/adminlte.min.css">
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; }

    /* Navbar */
    .main-header {
      background: linear-gradient(135deg, #007bff 0%, #ccccff 100%) !important;
      border-bottom: 1px solid rgba(255,255,255,0.2) !important;
      box-shadow: 0 4px 20px rgba(0,123,255,0.3) !important;
    }
    .navbar-light .navbar-nav .nav-link {
      color: white !important; font-weight: 700 !important;
      padding: 8px 16px !important; border-radius: 8px !important;
      margin: 0 4px !important; background: rgba(255,255,255,0.1) !important;
      border: 1px solid rgba(255,255,255,0.2) !important;
    }
    .navbar-light .navbar-nav .nav-link:hover {
      background: rgba(255,255,255,0.2) !important;
    }

    /* Sidebar */
    .main-sidebar {
      background: linear-gradient(180deg, #151618ff 0%, #007bff 100%) !important;
      display: block !important; width: 250px !important;
      transform: translateX(0) !important; visibility: visible !important;
      left: 0 !important; position: fixed !important;
      top: 0 !important; height: 100vh !important;
      z-index: 1038 !important; overflow-y: auto !important;
    }
    .brand-link {
      background: linear-gradient(135deg, #007bff 0%, #ccccff 100%) !important;
      border-bottom: 1px solid rgba(255,255,255,0.2) !important;
      padding: 20px 15px !important; display: flex !important;
      align-items: center !important; justify-content: center !important;
      flex-direction: column !important; text-decoration: none !important;
    }
    .brand-text { color: white !important; font-weight: 700 !important; font-size: 15px !important; text-align: center !important; line-height: 1.3 !important; }
    .brand-logo { width: 50px !important; height: 50px !important; border-radius: 50% !important; border: 2px solid rgba(255,255,255,0.3) !important; margin-bottom: 8px !important; }
    .sidebar { background: transparent !important; padding-top: 0 !important; overflow-y: auto !important; height: calc(100vh - 90px) !important; }
    .nav-sidebar .nav-link { color: #cbd5e1 !important; border-radius: 10px !important; margin: 2px 0 !important; padding: 12px 15px !important; border: 1px solid transparent !important; transition: all 0.3s ease !important; }
    .nav-sidebar .nav-link:hover { background: rgba(59,130,246,0.15) !important; color: #60a5fa !important; transform: translateX(5px) !important; }
    .nav-sidebar .nav-link.active { background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important; color: white !important; }
    .nav-sidebar .nav-link .nav-icon { margin-right: 10px !important; }
    .nav-sidebar .nav-link.text-danger { background: rgba(239,68,68,0.1) !important; }
    .nav-sidebar .nav-link.text-danger:hover { background: rgba(239,68,68,0.2) !important; color: #fca5a5 !important; }

    /* Layout */
    .content-wrapper { margin-left: 250px !important; min-height: 100vh !important; background: #f4f6f9 !important; padding-top: 57px !important; }
    .main-header.navbar { margin-left: 250px !important; width: calc(100% - 250px) !important; position: fixed !important; top: 0 !important; right: 0 !important; z-index: 1037 !important; }

    /* Cards */
    .card { border-radius: 14px; border: 1px solid #9f6540; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.06); margin-bottom: 1.5rem; }
    .card-header { background: linear-gradient(135deg, #007bff 0%, #ccccff 100%) !important; border-radius: 14px 14px 0 0 !important; padding: 16px 20px; }
    .card-title { color: white !important; font-weight: 700 !important; font-size: 16px !important; margin: 0 !important; }

    /* Alerts */
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; border-radius: 10px; }
    .alert-danger  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; border-radius: 10px; }

    /* Table */
    .table thead th { background: #f1f5f9; font-weight: 700; font-size: 13px; color: #374151; }
    .table td, .table th { vertical-align: middle; font-size: 13px; }
    .table-hover tbody tr:hover { background: #f0f7ff; }

    /* Buttons */
    .btn-primary   { background: #9f6540 !important; border-color: #9f6540 !important; }
    .btn-secondary { background: #6c757d !important; border-color: #6c757d !important; color: white !important; }
  </style>
</head>
<body class="hold-transition layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item d-none d-sm-inline-block">
        <a href="super_admin.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="super_admin.php" class="brand-link">
      <img src="../Admin/images/logo.png" alt="Logo" class="brand-logo" onerror="this.style.display='none'">
      <span class="brand-text">BULE HORA UNIVERSITY<br><small>Super Admin</small></span>
    </a>
    <div class="sidebar">
      <nav>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="super_admin.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
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
                <a href="../admin/student-record.php" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>All Students</p>
                        </a>
                    </li>
                    <li class="nav-item">

                    
                  <a href="../admin/add-student.php" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Add Student</p>
                        </a>
                    </li>
                </ul>
            </li>

          <li class="nav-item">
            <a href="analyes.php" class="nav-link">
              <i class="nav-icon fas fa-chart-bar"></i><p>Analytics</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="notifiction.php" class="nav-link">
              <i class="nav-icon fas fa-bell"></i><p>Notifications</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="news_notifiction.php" class="nav-link">
              <i class="nav-icon fas fa-newspaper"></i><p>News Notifications</p>
            </a>
          </li>
          <li class="nav-item">
                            <a href="../admin/admin-record.php" class="nav-link">

              <i class="nav-icon fas fa-user-shield"></i><p>Admin Management</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../Admin/login.php" class="nav-link text-danger">
              <i class="nav-icon fas fa-sign-out-alt"></i><p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Manage Students</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="super_admin.php">Home</a></li>
              <li class="breadcrumb-item active">Manage Students</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Search & Edit Students -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search mr-2"></i>Search & Manage Students</h3>
          </div>
          <div class="card-body">
            <form method="post" action="Manage_Students.php" class="form-inline mb-3">
              <input type="hidden" name="action" value="search_student">
              <div class="form-group mr-2">
                <input type="text" name="q" placeholder="Matric No or Name" class="form-control">
              </div>
              <button class="btn btn-secondary" type="submit"><i class="fas fa-search mr-1"></i>Find</button>
            </form>

            <?php if (!empty($found_students)): ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                  <thead><tr><th>Fullname</th><th>Matric</th><th>Session</th><th>Faculty</th><th>Dept</th><th>Phone</th><th>Req. Year</th><th>Actions</th></tr></thead>
                  <tbody>
                    <?php foreach($found_students as $s): ?>
                      <tr>
                        <form method="post" action="Manage_Students.php">
                          <input type="hidden" name="action" value="update_student">
                          <td><input type="text" name="fullname" value="<?= htmlspecialchars($s['fullname'] ?? '') ?>" class="form-control form-control-sm"></td>
                          <td><input type="text" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>" readonly class="form-control form-control-sm"></td>
                          <td><input type="text" name="session" value="<?= htmlspecialchars($s['session'] ?? '') ?>" class="form-control form-control-sm"></td>
                          <td><input type="text" name="faculty" value="<?= htmlspecialchars($s['faculty'] ?? '') ?>" class="form-control form-control-sm"></td>
                          <td><input type="text" name="dept" value="<?= htmlspecialchars($s['dept'] ?? '') ?>" class="form-control form-control-sm"></td>
                          <td><input type="text" name="phone" value="<?= htmlspecialchars($s['phone'] ?? '') ?>" class="form-control form-control-sm"></td>
                          <td><input type="text" name="request_year" value="<?= htmlspecialchars($s['request_year'] ?? '') ?>" placeholder="YYYY" class="form-control form-control-sm" style="width:80px"></td>
                          <td>
                            <button class="btn btn-success btn-sm" type="submit">Save</button>
                            <button class="btn btn-warning btn-sm" type="submit" onclick="this.form.elements['action'].value='reset_request_year'">Reset</button>
                          </td>
                        </form>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <h5 class="mb-2">Recent Students</h5>
              <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                  <thead><tr><th>Fullname</th><th>Matric</th><th>Session</th><th>Faculty</th><th>Dept</th><th>Phone</th><th>Req. Year</th></tr></thead>
                  <tbody>
                    <?php if (empty($recent_students)): ?>
                      <tr><td colspan="7" class="text-center text-muted">No students found</td></tr>
                    <?php else: foreach($recent_students as $s): ?>
                      <tr>
                        <td><?= htmlspecialchars($s['fullname'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['matric_no'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['session'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['faculty'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['dept'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['request_year'] ?? '') ?></td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Filter by Year -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Requested Clearance by Year</h3>
          </div>
          <div class="card-body">
            <form method="post" action="Manage_Students.php" class="form-inline mb-3">
              <input type="hidden" name="action" value="filter_year">
              <div class="form-group mr-2">
                <label class="mr-2">Year</label>
                <input type="number" name="year" value="<?= htmlspecialchars($filter_year ?: date('Y')) ?>" class="form-control" min="2000" max="9999" style="width:120px">
              </div>
              <button class="btn btn-secondary" type="submit">View</button>
            </form>

            <?php if (!empty($requested_year_students)): ?>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span><strong>Year:</strong> <?= htmlspecialchars($filter_year) ?> &nbsp; <strong>Count:</strong> <?= count($requested_year_students) ?></span>
                <form method="post" action="Manage_Students.php" onsubmit="return confirm('Reset all requests for this year and notify students?');" style="margin:0;">
                  <input type="hidden" name="action" value="reset_year_all">
                  <input type="hidden" name="year" value="<?= htmlspecialchars($filter_year) ?>">
                  <button class="btn btn-danger btn-sm" type="submit">Reset &amp; Notify All</button>
                </form>
              </div>
              <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                  <thead><tr><th>Fullname</th><th>Matric</th><th>Session</th><th>Faculty</th><th>Dept</th><th>Phone</th><th>Req. Year</th><th>Permission</th></tr></thead>
                  <tbody>
                    <?php foreach($requested_year_students as $s): ?>
                      <tr>
                        <td><?= htmlspecialchars($s['fullname'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['matric_no'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['session'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['faculty'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['dept'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['request_year'] ?? '') ?></td>
                        <td>
                          <form method="post" action="Manage_Students.php" style="display:inline-flex; gap:4px; flex-wrap:wrap;">
                            <input type="hidden" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>">
                            <input type="hidden" name="session" value="<?= htmlspecialchars($s['session'] ?? '') ?>">
                            <button class="btn btn-warning btn-sm" type="submit" name="action" value="reset_request_year">Allow Re-request</button>
                            <button class="btn btn-info btn-sm" type="submit" name="action" value="allow_notify">Allow &amp; Notify</button>
                            <input type="number" name="year" value="<?= htmlspecialchars(($filter_year ?: date('Y')) + 1) ?>" class="form-control form-control-sm" style="width:80px" min="2000" max="9999">
                            <button class="btn btn-success btn-sm" type="submit" name="action" value="set_request_year">Set Year</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">Use the Year filter to view all students who requested clearance for that year.</p>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->

<script src="../js/jquery-2.1.1.js"></script>
<script src="../Admin/plugins/jquery/jquery.min.js"></script>
<script src="../Admin/dist/js/adminlte.min.js"></script>
</body>
</html>
