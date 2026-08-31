<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../Admin/connect.php');

// Check if super admin is logged in
if(!isset($_SESSION['admin-username']) || $_SESSION['role'] !== 'super_admin') {
    // For direct access without login (testing)
    if(!isset($_SESSION['admin-username'])) {
        $_SESSION['admin-username'] = 'superadmin';
        $_SESSION['role'] = 'super_admin';
        $_SESSION['fullname'] = 'Super Administrator';
    }
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS system_settings (\n  setting_key VARCHAR(64) PRIMARY KEY,\n  setting_value VARCHAR(255) NOT NULL,\n  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS clearance_day_control (\n  date DATE PRIMARY KEY,\n  is_open TINYINT(1) NOT NULL DEFAULT 0,\n  start_time TIME NULL,\n  end_time TIME NULL,\n  note VARCHAR(255) NULL,\n  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
if ($conn) {
  $checkCols = mysqli_query($conn, "SHOW COLUMNS FROM clearance_day_control LIKE 'start_time'");
  if ($checkCols && mysqli_num_rows($checkCols) === 0) {
    mysqli_query($conn, "ALTER TABLE clearance_day_control ADD COLUMN start_time TIME NULL, ADD COLUMN end_time TIME NULL, ADD COLUMN note VARCHAR(255) NULL");
  }
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS notifications (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  recipient_matric VARCHAR(32) DEFAULT NULL,\n  subject VARCHAR(200) NOT NULL,\n  message TEXT NOT NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$colReq = mysqli_query($conn, "SHOW COLUMNS FROM students LIKE 'request_year'");
if ($colReq && mysqli_num_rows($colReq) === 0) {
  mysqli_query($conn, "ALTER TABLE students ADD COLUMN request_year INT NULL");
}

$success = '';
$error = '';
$found_students = [];
$requested_year_students = [];
$filter_year = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'filter_year') {
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
    } else {
      $error = 'Enter a valid year.';
    }
  }
  elseif ($action === 'reset_request_year') {
    $matric = trim($_POST['matric_no'] ?? '');
    if ($matric === '') { $error = 'Matric number is required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE matric_no=?");
      if ($stmt) { $stmt->bind_param('s', $matric); if ($stmt->execute()) { $success = 'Request year reset for ' . htmlspecialchars($matric) . '.'; } else { $error = 'Failed to reset.'; } $stmt->close(); }
    }
  }
  elseif ($action === 'set_request_year') {
    $matric = trim($_POST['matric_no'] ?? '');
    $y = trim($_POST['year'] ?? '');
    if ($matric === '' || $y === '' || !ctype_digit($y)) { $error = 'Valid matric and year required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=? WHERE matric_no=?");
      if ($stmt) { $yy = intval($y); $stmt->bind_param('is', $yy, $matric); if ($stmt->execute()) { $success = 'Request year set to ' . $yy . ' for ' . htmlspecialchars($matric) . '.'; } else { $error = 'Failed to set year.'; } $stmt->close(); }
    }
  }
  elseif ($action === 'allow_notify') {
    $matric = trim($_POST['matric_no'] ?? '');
    if ($matric === '') { $error = 'Matric number is required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE matric_no=?");
      if ($stmt) { $stmt->bind_param('s', $matric); $stmt->execute(); $stmt->close(); }
      $subject = 'Clearance Re‑request Allowed';
      $msg = 'You may now submit a new clearance request. Watch for the next open day.';
      $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
      if ($nstmt) { $nstmt->bind_param('sss', $matric, $subject, $msg); if ($nstmt->execute()) { $success = 'Re‑request allowed and notification sent to ' . htmlspecialchars($matric) . '.'; } else { $error = 'Failed to notify student.'; } $nstmt->close(); }
    }
  }
}

// Analytics metrics
$curYear = intval(date('Y'));
$total_students = 0;
$requested_all = 0;
$requested_year = 0;
$not_requested = 0;
$dept_stats = [];

$q1 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM students");
if ($q1 && ($r = mysqli_fetch_assoc($q1))) { $total_students = intval($r['c']); }
$q2 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM students WHERE request_year IS NOT NULL");
if ($q2 && ($r = mysqli_fetch_assoc($q2))) { $requested_all = intval($r['c']); }
$q3 = mysqli_query($conn, "SELECT COUNT(*) AS c FROM students WHERE request_year = " . $curYear);
if ($q3 && ($r = mysqli_fetch_assoc($q3))) { $requested_year = intval($r['c']); }
$not_requested = max(0, $total_students - $requested_all);
$q4 = mysqli_query($conn, "SELECT dept, COUNT(*) AS reg_count, SUM(CASE WHEN request_year IS NOT NULL THEN 1 ELSE 0 END) AS req_count FROM students GROUP BY dept ORDER BY dept ASC");
if ($q4) { while($row = mysqli_fetch_assoc($q4)) { $dept_stats[] = $row; } }

// Approvals by unit
$units = [
  ['label' => 'Department', 'col' => 'is_department_approved'],
  ['label' => 'Library', 'col' => 'is_library_approved'],
  ['label' => 'Bookstore', 'col' => 'is_bookstore_approved'],
  ['label' => 'Dormitory', 'col' => 'is_dormitory_approved'],
  ['label' => 'Cafeteria', 'col' => 'is_cafeteria_approved'],
  ['label' => 'Sport', 'col' => 'is_sport_approved'],
  ['label' => 'Police', 'col' => 'is_police_approved'],
  ['label' => 'Registrar', 'col' => 'is_registrar_approved'],
];
$units_stats = [];
foreach ($units as $u) {
  $col = $u['col'];
  $csql = mysqli_query($conn, "SELECT COUNT(*) AS c FROM students WHERE request_year = " . $curYear . " AND " . $col . " = 1");
  $approved = 0;
  if ($csql && ($rr = mysqli_fetch_assoc($csql))) { $approved = intval($rr['c']); }
  $pct = ($requested_year > 0) ? round(($approved / $requested_year) * 100) : 0;
  $units_stats[] = ['label' => $u['label'], 'approved' => $approved, 'total' => $requested_year, 'pct' => $pct];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Super Admin Dashboard | BULE HORA UNIVERSITY</title>
  <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../Admin/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../Admin/dist/css/adminlte.min.css">
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; }

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
    .navbar-light .navbar-nav .nav-link:hover { background: rgba(255,255,255,0.2) !important; }

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

    .content-wrapper { margin-left: 250px !important; min-height: 100vh !important; background: #f4f6f9 !important; padding-top: 57px !important; }
    .main-header.navbar { margin-left: 250px !important; width: calc(100% - 250px) !important; position: fixed !important; top: 0 !important; right: 0 !important; z-index: 1037 !important; }

    .card { border-radius: 14px; border: 1px solid #9f6540; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.06); margin-bottom: 1.5rem; }
    .card-header { background: linear-gradient(135deg, #007bff 0%, #ccccff 100%) !important; border-radius: 14px 14px 0 0 !important; padding: 16px 20px; }
    .card-title { color: white !important; font-weight: 700 !important; font-size: 16px !important; margin: 0 !important; }

    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; border-radius: 10px; }
    .alert-danger  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; border-radius: 10px; }

    .stat-card { background: #fff; border: 1px solid #9f6540; border-radius: 12px; padding: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .stat-card .label { font-weight: 700; color: #9f6540; font-size: 14px; }
    .stat-card .value { font-size: 2rem; font-weight: 800; color: #0f172a; margin: 8px 0; }
    .stat-card .desc { color: #64748b; font-size: 13px; }

    .unit-card { display: flex; align-items: center; gap: 12px; background: #fff; border: 1px solid #9f6540; border-radius: 12px; padding: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }

    .table thead th { background: #f1f5f9; font-weight: 700; font-size: 13px; color: #374151; }
    .table td, .table th { vertical-align: middle; font-size: 13px; }
    .table-hover tbody tr:hover { background: #f0f7ff; }

    .btn-primary { background: #9f6540 !important; border-color: #9f6540 !important; }
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
            <a href="super_admin.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="Manage_Students.php" class="nav-link">
              <i class="nav-icon fas fa-user-graduate"></i><p>Manage Students</p>
            </a>
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
            <a href="../Admin/add-admin.php" class="nav-link">
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
            <h1 class="m-0 text-dark">Super Admin Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="super_admin.php">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
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

        <!-- Summary Stats -->
        <div class="row">
          <div class="col-lg-3 col-md-6">
            <div class="stat-card">
              <div class="label">Total Students</div>
              <div class="value"><?= $total_students ?></div>
              <div class="desc">All registered profiles</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card">
              <div class="label">Requested (<?= $curYear ?>)</div>
              <div class="value"><?= $requested_year ?></div>
              <div class="desc">This year</div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card">
              <div class="label">Requested (All)</div>
              <div class="value"><?= $not_requested ?></div>
              <div class="desc">Lifetime</div>
            </div>
            
            

          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card">
              <div class="label">Not Requested</div>
              <div class="value"><?= $requested_all ?></div>
              <div class="desc">Without request</div>
            </div>
          </div>
        </div>

        <!-- Unit Approvals -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-check-circle mr-2"></i>Approvals by Unit (Year <?= $curYear ?>)</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <?php foreach($units_stats as $u): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="unit-card">
                    <svg width="56" height="56" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="16" fill="none" stroke="#e2e8f0" stroke-width="4"/>
                      <circle cx="18" cy="18" r="16" fill="none" stroke="#9f6540" stroke-width="4" stroke-linecap="round"
                        stroke-dasharray="<?= intval($u['pct']) ?>,100" transform="rotate(-90 18 18)"/>
                      <text x="18" y="20" text-anchor="middle" font-size="7" fill="#0f172a" font-weight="800"><?= intval($u['pct']) ?>%</text>
                    </svg>
                    <div>
                      <div style="font-weight:700; color:#9f6540; font-size:14px;"><?= htmlspecialchars($u['label']) ?></div>
                      <div style="font-size:16px; font-weight:800;"><?= intval($u['approved']) ?> / <?= intval($u['total']) ?></div>
                      <div style="color:#64748b; font-size:12px;">Approved</div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Departments -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-building mr-2"></i>Departments — Registered vs Requested</h3>
          </div>
          <div class="card-body">
            <?php if (empty($dept_stats)): ?>
              <p class="text-muted">No department data available.</p>
            <?php else: foreach($dept_stats as $d):
              $reg = intval($d['reg_count']); $req = intval($d['req_count']);
              $pctDept = $reg > 0 ? round(($req / $reg) * 100) : 0; ?>
              <div class="d-flex align-items-center mb-2">
                <div style="min-width:160px; font-weight:600;"><?= htmlspecialchars($d['dept'] ?: '—') ?></div>
                <div class="flex-grow-1 mx-3">
                  <div style="height:10px; background:#e2e8f0; border-radius:6px; overflow:hidden;">
                    <div style="width:<?= $pctDept ?>%; height:100%; background:#9f6540; border-radius:6px;"></div>
                  </div>
                </div>
                <div style="min-width:200px; color:#64748b; font-size:13px;">Reg: <?= $reg ?> &bull; Req: <?= $req ?> (<?= $pctDept ?>%)</div>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>

        <!-- Filter by Year -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Requested Clearance by Year</h3>
          </div>
          <div class="card-body">
            <form method="post" action="super_admin.php" class="form-inline mb-3">
              <input type="hidden" name="action" value="filter_year">
              <div class="form-group mr-2">
                <label class="mr-2">Year</label>
                <input type="number" name="year" value="<?= htmlspecialchars($filter_year ?: date('Y')) ?>" class="form-control" min="2000" max="9999" style="width:120px">
              </div>
              <button class="btn btn-secondary" type="submit">View</button>
            </form>

            <?php if (!empty($requested_year_students)): ?>
              <p class="mb-2"><strong>Year:</strong> <?= htmlspecialchars($filter_year) ?> &nbsp; <strong>Count:</strong> <?= count($requested_year_students) ?></p>
              <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered table-sm">
                  <thead><tr><th>Fullname</th><th>Matric</th><th>Session</th><th>Faculty</th><th>Dept</th><th>Phone</th><th>Req. Year</th><th>Actions</th></tr></thead>
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
                          <form method="post" action="super_admin.php" style="display:inline-flex; gap:4px; flex-wrap:wrap;">
                            <input type="hidden" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>">
                            <button class="btn btn-warning btn-sm" type="submit" name="action" value="reset_request_year">Allow</button>
                            <button class="btn btn-info btn-sm" type="submit" name="action" value="allow_notify">Notify</button>
                            <input type="number" name="year" value="<?= htmlspecialchars(($filter_year ?: date('Y')) + 1) ?>" class="form-control form-control-sm" style="width:70px" min="2000" max="9999">
                            <button class="btn btn-success btn-sm" type="submit" name="action" value="set_request_year">Set</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">Use the Year filter to view students who requested clearance for that year.</p>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </section>
  </div>

</div>

<script src="../Admin/plugins/jquery/jquery.min.js"></script>
<script src="../Admin/dist/js/adminlte.min.js"></script>
</body>
</html>
