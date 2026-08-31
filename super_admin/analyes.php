<?php
session_start();
error_reporting(0);
include('../connect.php');

$curYear = 2025;

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

$units = [
  ['label' => 'Department',  'col' => 'is_department_approved'],
  ['label' => 'Library',     'col' => 'is_library_approved'],
  ['label' => 'Bookstore',   'col' => 'is_bookstore_approved'],
  ['label' => 'Dormitory',   'col' => 'is_dormitory_approved'],
  ['label' => 'Cafeteria',   'col' => 'is_cafeteria_approved'],
  ['label' => 'Sport',       'col' => 'is_sport_approved'],
  ['label' => 'Police',      'col' => 'is_police_approved'],
  ['label' => 'Registrar',   'col' => 'is_registrar_approved'],
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
  <title>Analytics | BULE HORA UNIVERSITY</title>
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

    .stat-row { display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.65rem 0.9rem; margin-bottom: 0.5rem; }
    .stat-row .label { font-weight: 700; color: #9f6540; }
    .stat-row .value { font-size: 1.4rem; font-weight: 800; color: #0f172a; }

    .bar-item { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; }
    .bar { flex: 1; height: 10px; background: #e2e8f0; border-radius: 6px; overflow: hidden; }
    .bar > div { height: 100%; background: #9f6540; border-radius: 6px; }

    .unit-row { display: flex; align-items: center; gap: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.65rem 0.75rem; margin-bottom: 0.5rem; }
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
          <li class="nav-item">
            <a href="Manage_Students.php" class="nav-link">
              <i class="nav-icon fas fa-user-graduate"></i><p>Manage Students</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="analyes.php" class="nav-link active">
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
            <h1 class="m-0 text-dark">Analytics Overview</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="super_admin.php">Home</a></li>
              <li class="breadcrumb-item active">Analytics</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        <!-- Summary Stats -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Summary Statistics</h3>
          </div>
          <div class="card-body">
            <div class="stat-row"><div class="label">Total Students</div><div class="value"><?= $total_students ?></div></div>
            <div class="stat-row"><div class="label">Requested (All Time)</div><div class="value"><?= $not_requested ?></div></div>
            <div class="stat-row"><div class="label">Not Requested</div><div class="value"><?= $requested_all?></div></div>
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
              <div class="bar-item">
                <div style="min-width:160px; font-weight:600; color:#0f172a;"><?= htmlspecialchars($d['dept'] ?: '—') ?></div>
                <div class="bar"><div style="width:<?= $pctDept ?>%"></div></div>
                <div style="min-width:200px; color:#64748b; font-size:0.9rem;">Reg: <?= $reg ?> &bull; Req: <?= $req ?> (<?= $pctDept ?>%)</div>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>

        <!-- Unit Approvals -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-check-circle mr-2"></i>Approvals by Unit (Year <?= $curYear ?>)</h3>
          </div>
          <div class="card-body">
            <?php if (empty($units_stats)): ?>
              <p class="text-muted">No approval data available.</p>
            <?php else: foreach($units_stats as $u): ?>
              <div class="unit-row">
                <svg width="56" height="56" viewBox="0 0 36 36">
                  <circle cx="18" cy="18" r="16" fill="none" stroke="#e2e8f0" stroke-width="4"/>
                  <circle cx="18" cy="18" r="16" fill="none" stroke="#9f6540" stroke-width="4" stroke-linecap="round"
                    stroke-dasharray="<?= intval($u['pct']) ?>,100" transform="rotate(-90 18 18)"/>
                  <text x="18" y="20" text-anchor="middle" font-size="7" fill="#0f172a" font-weight="800"><?= intval($u['pct']) ?>%</text>
                </svg>
                <div style="min-width:140px; font-weight:700; color:#9f6540;"><?= htmlspecialchars($u['label']) ?></div>
                <div style="font-size:1.1rem; font-weight:800;"><?= intval($u['approved']) ?> / <?= intval($u['total']) ?></div>
                <div style="color:#64748b; font-size:0.9rem; margin-left:8px;">Approved / Requested</div>
              </div>
            <?php endforeach; endif; ?>
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
