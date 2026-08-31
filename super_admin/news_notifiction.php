<?php
session_start();
error_reporting(0);
include('../connect.php');

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS notifications (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  recipient_matric VARCHAR(32) DEFAULT NULL,\n  subject VARCHAR(200) NOT NULL,\n  message TEXT NOT NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$q = trim($_GET['q'] ?? '');
$matric = trim($_GET['matric'] ?? '');
$limit = intval($_GET['limit'] ?? 100);
if ($limit < 1 || $limit > 500) { $limit = 100; }

$rows = [];
if ($q !== '' && $matric !== '') {
  $like = '%' . $q . '%';
  $stmt = $conn->prepare("SELECT id, recipient_matric, subject, message, created_at FROM notifications WHERE recipient_matric = ? AND (subject LIKE ? OR message LIKE ?) ORDER BY id DESC LIMIT ?");
  if ($stmt) { $stmt->bind_param('sssi', $matric, $like, $like, $limit); if ($stmt->execute()) { $res = $stmt->get_result(); while($r = $res->fetch_assoc()) { $rows[] = $r; } } $stmt->close(); }
} elseif ($q !== '') {
  $like = '%' . $q . '%';
  $stmt = $conn->prepare("SELECT id, recipient_matric, subject, message, created_at FROM notifications WHERE subject LIKE ? OR message LIKE ? ORDER BY id DESC LIMIT ?");
  if ($stmt) { $stmt->bind_param('ssi', $like, $like, $limit); if ($stmt->execute()) { $res = $stmt->get_result(); while($r = $res->fetch_assoc()) { $rows[] = $r; } } $stmt->close(); }
} elseif ($matric !== '') {
  $stmt = $conn->prepare("SELECT id, recipient_matric, subject, message, created_at FROM notifications WHERE recipient_matric = ? ORDER BY id DESC LIMIT ?");
  if ($stmt) { $stmt->bind_param('si', $matric, $limit); if ($stmt->execute()) { $res = $stmt->get_result(); while($r = $res->fetch_assoc()) { $rows[] = $r; } } $stmt->close(); }
} else {
  $stmt = $conn->prepare("SELECT id, recipient_matric, subject, message, created_at FROM notifications ORDER BY id DESC LIMIT ?");
  if ($stmt) { $stmt->bind_param('i', $limit); if ($stmt->execute()) { $res = $stmt->get_result(); while($r = $res->fetch_assoc()) { $rows[] = $r; } } $stmt->close(); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>News Notifications | BULE HORA UNIVERSITY</title>
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

    .form-control { border: 2px solid #9f6540; border-radius: 8px; }
    .btn-primary { background: #9f6540 !important; border-color: #9f6540 !important; }
    .btn-secondary { background: #6c757d !important; border-color: #6c757d !important; color: white !important; }

    .table thead th { background: #f1f5f9; font-weight: 700; font-size: 13px; color: #374151; }
    .table td, .table th { vertical-align: middle; font-size: 13px; }
    .table-hover tbody tr:hover { background: #f0f7ff; }
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
            <a href="news_notifiction.php" class="nav-link active">
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
            <h1 class="m-0 text-dark">Recent Notifications</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="super_admin.php">Home</a></li>
              <li class="breadcrumb-item active">News Notifications</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        <!-- Search & Filter -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search mr-2"></i>Search Notifications</h3>
          </div>
          <div class="card-body">
            <form method="get" action="news_notifiction.php">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Recipient Matric</label>
                    <input type="text" name="matric" value="<?= htmlspecialchars($matric) ?>" placeholder="e.g. RU/0370" class="form-control">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Search Text</label>
                    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Subject or message" class="form-control">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>Limit</label>
                    <input type="number" name="limit" value="<?= htmlspecialchars($limit) ?>" min="1" max="500" class="form-control">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <button class="btn btn-secondary btn-block" type="submit"><i class="fas fa-search mr-1"></i>Find</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Notifications Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Notifications List (<?= count($rows) ?> results)</h3>
          </div>
          <div class="card-body">
            <?php if (empty($rows)): ?>
              <p class="text-muted text-center">No notifications found.</p>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 60px;">ID</th>
                      <th style="width: 140px;">Recipient</th>
                      <th>Subject</th>
                      <th>Message</th>
                      <th style="width: 160px;">Created At</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($rows as $n): ?>
                      <tr>
                        <td><?= intval($n['id']) ?></td>
                        <td><?= htmlspecialchars($n['recipient_matric'] ?: 'All') ?></td>
                        <td><strong><?= htmlspecialchars($n['subject']) ?></strong></td>
                        <td><?= htmlspecialchars($n['message']) ?></td>
                        <td><?= htmlspecialchars($n['created_at']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
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
