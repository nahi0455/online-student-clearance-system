<?php
session_start();
error_reporting(0);
include('../connect.php');

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS system_settings (\n  setting_key VARCHAR(64) PRIMARY KEY,\n  setting_value VARCHAR(255) NOT NULL,\n  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS clearance_day_control (\n  date DATE PRIMARY KEY,\n  is_open TINYINT(1) NOT NULL DEFAULT 0,\n  start_time TIME NULL,\n  end_time TIME NULL,\n  note VARCHAR(255) NULL,\n  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS notifications (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  recipient_matric VARCHAR(32) DEFAULT NULL,\n  subject VARCHAR(200) NOT NULL,\n  message TEXT NOT NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'toggle_global') {
    $val = ($_POST['clearance_open'] ?? '0') === '1' ? '1' : '0';
    $stmt = $conn->prepare("INSERT INTO system_settings(setting_key, setting_value) VALUES('clearance_open', ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
    $stmt->bind_param('s', $val);
    if ($stmt->execute()) { $success = 'Global clearance request setting updated.'; } else { $error = 'Failed to update global setting.'; }
  } elseif ($action === 'toggle_day') {
    $date = trim($_POST['date'] ?? date('Y-m-d'));
    $is_open = ($_POST['is_open'] ?? '0') === '1' ? 1 : 0;
    $start_time = trim($_POST['start_time'] ?? '');
    $end_time = trim($_POST['end_time'] ?? '');
    $note = trim($_POST['note'] ?? '');
    $stmt = $conn->prepare("INSERT INTO clearance_day_control(date, is_open, start_time, end_time, note) VALUES(?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE is_open=VALUES(is_open), start_time=VALUES(start_time), end_time=VALUES(end_time), note=VALUES(note)");
    $stmt->bind_param('sisss', $date, $is_open, $start_time, $end_time, $note);
    if ($stmt->execute()) {
      $success = 'Clearance request day status updated.';
      $subj = ($is_open ? 'Clearance Day Open' : 'Clearance Day Closed') . ' (' . $date . ')';
      $timePart = ($start_time !== '' || $end_time !== '') ? (' Time: ' . ($start_time !== '' ? $start_time : '—') . ' - ' . ($end_time !== '' ? $end_time : '—')) : '';
      $msg = 'Clearance requests ' . ($is_open ? 'are open' : 'are closed') . ' for ' . $date . '.' . $timePart;
      if ($note !== '') { $msg .= ' ' . $note; }
      $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(NULL, ?, ?)");
      if ($nstmt) { $nstmt->bind_param('ss', $subj, $msg); $nstmt->execute(); $nstmt->close(); }
    } else { $error = 'Failed to update day status.'; }
  } elseif ($action === 'notify') {
    $recipient = trim($_POST['recipient_matric'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($subject === '' || $message === '') {
      $error = 'Subject and message are required.';
    } else {
      $valid = true;
      if ($recipient !== '') {
        $c = $conn->prepare("SELECT 1 FROM students WHERE matric_no=? LIMIT 1");
        if ($c) {
          $c->bind_param('s', $recipient);
          if (!$c->execute()) { $valid = false; }
          else { $r = $c->get_result(); if (!$r || $r->num_rows === 0) { $valid = false; } }
          $c->close();
        } else { $valid = false; }
      }
      if (!$valid && $recipient !== '') { $error = 'Student not found for given matric number.'; }
      else {
        $stmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
        $stmt->bind_param('sss', $recipient, $subject, $message);
        if ($stmt->execute()) { $success = $recipient !== '' ? ('Notification sent to ' . $recipient . '.') : 'Notification created.'; } else { $error = 'Failed to create notification.'; }
      }
    }
  }
}

function get_setting($conn, $key, $default='0') {
  $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key=?");
  $stmt->bind_param('s', $key);
  if ($stmt->execute()) { $res = $stmt->get_result(); if ($row = $res->fetch_assoc()) return $row['setting_value']; }
  return $default;
}

$global_open = get_setting($conn, 'clearance_open', '0');
$today_status = 0;
$today = date('Y-m-d');
$today_start = '';
$today_end = '';
$today_note = '';
$rs = mysqli_query($conn, "SELECT is_open, start_time, end_time, note FROM clearance_day_control WHERE date='" . mysqli_real_escape_string($conn, $today) . "'");
if ($rs && $row = mysqli_fetch_assoc($rs)) { $today_status = intval($row['is_open']); $today_start = $row['start_time'] ?? ''; $today_end = $row['end_time'] ?? ''; $today_note = $row['note'] ?? ''; }

$recent_notes = [];
$rs = mysqli_query($conn, "SELECT id, recipient_matric, subject, created_at FROM notifications ORDER BY id DESC LIMIT 10");
if ($rs) { while($row = mysqli_fetch_assoc($rs)) { $recent_notes[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifications | BULE HORA UNIVERSITY</title>
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

    .form-control { border: 2px solid #9f6540; border-radius: 8px; }
    .btn-primary { background: #9f6540 !important; border-color: #9f6540 !important; }
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
            <a href="notifiction.php" class="nav-link active">
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
            <h1 class="m-0 text-dark">Notifications & Control</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="super_admin.php">Home</a></li>
              <li class="breadcrumb-item active">Notifications</li>
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

        <div class="row">
          <!-- Global Clearance Control -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-globe mr-2"></i>Global Clearance</h3>
              </div>
              <div class="card-body">
                <p class="mb-2">Current Status: <strong class="text-<?= $global_open==='1' ? 'success' : 'danger' ?>"><?= $global_open==='1' ? 'OPEN' : 'CLOSED' ?></strong></p>
                <form method="post">
                  <input type="hidden" name="action" value="toggle_global">
                  <div class="form-group">
                    <label>Set Status</label>
                    <select name="clearance_open" class="form-control">
                      <option value="1" <?= $global_open==='1' ? 'selected' : '' ?>>Open</option>
                      <option value="0" <?= $global_open!=='1' ? 'selected' : '' ?>>Closed</option>
                    </select>
                  </div>
                  <button class="btn btn-primary btn-block" type="submit">Save</button>
                </form>
              </div>
            </div>
          </div>

          <!-- Today's Clearance Day -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-day mr-2"></i>Today's Control</h3>
              </div>
              <div class="card-body">
                <p class="mb-1">Date: <strong><?= htmlspecialchars($today) ?></strong></p>
                <p class="mb-1">Status: <strong class="text-<?= $today_status===1 ? 'success' : 'danger' ?>"><?= $today_status===1 ? 'ON' : 'OFF' ?></strong></p>
                <p class="mb-2">Time: <strong><?= ($today_start ? htmlspecialchars($today_start) : '—') ?> - <?= ($today_end ? htmlspecialchars($today_end) : '—') ?></strong></p>
                <?php if ($today_note): ?><p class="mb-2 text-muted small">Note: <?= htmlspecialchars($today_note) ?></p><?php endif; ?>
                <form method="post">
                  <input type="hidden" name="action" value="toggle_day">
                  <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($today) ?>" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <select name="is_open" class="form-control">
                      <option value="1" <?= $today_status===1 ? 'selected' : '' ?>>On</option>
                      <option value="0" <?= $today_status!==1 ? 'selected' : '' ?>>Off</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time" value="<?= htmlspecialchars($today_start) ?>" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="end_time" value="<?= htmlspecialchars($today_end) ?>" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Note</label>
                    <input type="text" name="note" placeholder="Optional" value="<?= htmlspecialchars($today_note) ?>" class="form-control">
                  </div>
                  <button class="btn btn-primary btn-block" type="submit">Update</button>
                </form>
              </div>
            </div>
          </div>

          <!-- Send Notification -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paper-plane mr-2"></i>Send Notification</h3>
              </div>
              <div class="card-body">
                <form method="post">
                  <input type="hidden" name="action" value="notify">
                  <div class="form-group">
                    <label>Student ID (optional)</label>
                    <input type="text" name="recipient_matric" placeholder="e.g. RU/0370" class="form-control">
                    <small class="form-text text-muted">Leave empty for general notification</small>
                  </div>
                  <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" required class="form-control" rows="4"></textarea>
                  </div>
                  <button class="btn btn-primary btn-block" type="submit">Send</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Notifications -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history mr-2"></i>Recent Notifications</h3>
          </div>
          <div class="card-body">
            <?php if (empty($recent_notes)): ?>
              <p class="text-muted">No notifications yet.</p>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Recipient</th>
                      <th>Subject</th>
                      <th>Created At</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($recent_notes as $n): ?>
                      <tr>
                        <td><?= intval($n['id']) ?></td>
                        <td><?= htmlspecialchars($n['recipient_matric'] ?: 'All') ?></td>
                        <td><?= htmlspecialchars($n['subject']) ?></td>
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
