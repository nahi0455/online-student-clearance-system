<?php
session_start();
error_reporting(0);
include('../connect.php');

$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');
$currentTheme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
setcookie('lang', $currentLang, time() + (86400 * 30), "/");

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
    $recipient_unit = trim($_POST['recipient_unit'] ?? '');
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
      // Unit recipient does not require student validation
      if (!$valid) {
        $error = 'Student not found for given matric number.';
      } else {
        $target = $recipient;
        if ($target === '' && $recipient_unit !== '') { $target = 'UNIT:' . strtoupper($recipient_unit); }
        $stmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
        $stmt->bind_param('sss', $target, $subject, $message);
        if ($stmt->execute()) {
          if ($recipient !== '') { $success = 'Notification sent to ' . $recipient . '.'; }
          elseif ($recipient_unit !== '') { $success = 'Notification sent to unit ' . strtoupper($recipient_unit) . '.'; }
          else { $success = 'Notification created.'; }
        } else { $error = 'Failed to create notification.'; }
      }
    }
  }
  elseif ($action === 'search_student') {
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
  }
  elseif ($action === 'update_student') {
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
  }
  elseif ($action === 'filter_year') {
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
      $subject = 'Clearance Year Updated';
      $msg = 'Your clearance request year has been set to ' . intval($y) . '. You will be able to request again in that year when the window opens.';
      $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
      if ($nstmt) { $nstmt->bind_param('sss', $matric, $subject, $msg); $nstmt->execute(); $nstmt->close(); }
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

$recent_students = [];
$rs = mysqli_query($conn, "SELECT fullname, matric_no, session, faculty, dept, phone, request_year FROM students ORDER BY ID DESC LIMIT 10");
if ($rs) { while($row = mysqli_fetch_assoc($rs)) { $recent_students[] = $row; } }

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

// Approvals by unit (within current year requests)
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
$total_req_year = $requested_year;
$units_stats = [];
foreach ($units as $u) {
  $col = $u['col'];
  $label = $u['label'];
  $csql = mysqli_query($conn, "SELECT COUNT(*) AS c FROM students WHERE request_year = " . $curYear . " AND " . $col . " = 1");
  $approved = 0;
  if ($csql && ($rr = mysqli_fetch_assoc($csql))) { $approved = intval($rr['c']); }
  $pct = ($total_req_year > 0) ? round(($approved / $total_req_year) * 100) : 0;
  $units_stats[] = ['label' => $label, 'approved' => $approved, 'total' => $total_req_year, 'pct' => $pct];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Super Admin • Online Student Clearance System</title>
<link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">

  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#ffffffff; transition: background 0.3s ease, color 0.3s ease; color:#0f172a; }
    .container { max-width:1200px; margin:0 auto; padding:0 1.5rem; }
    .navbar { background: linear-gradient(135deg, #007bff 0%, #ccccff 100%); box-shadow: 0 4px 20px #007bff; padding: 1.0rem 0; position: sticky; top: 0; z-index: 50; }
    .navbar .container { display:flex; justify-content:space-between; align-items:center; }
    .navbar h1 { font-size:1.25rem; color:#fff; font-weight:800; display:flex; align-items:center; gap:0.75rem; }
    .logo-img { height:44px; width:44px; object-fit:cover; border-radius:50%; background:white; padding:0.2rem; border:2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    .nav-links { display:flex; gap:0.9rem; align-items:center; position:relative; }
    .nav-links button { background: rgba(255,255,255,0.22); border: 0; padding:0.45rem; width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:white; }
    .nav-links a { position:relative; padding:0.35rem 0.6rem; border-radius:8px; color:#fff; font-weight:600; text-decoration:none; }
    .nav-highlight { position:absolute; top:0; left:0; width:0; height:0; border-radius:10px; background:radial-gradient(120px 120px at 50% 50%, rgba(255,255,255,0.25), rgba(255,255,255,0.1)); box-shadow:0 6px 24px rgba(0,0,0,0.15); pointer-events:none; opacity:0; transform:translate3d(0,0,0); transition: opacity 200ms ease, left 300ms cubic-bezier(0.22, 1, 0.36, 1), top 300ms cubic-bezier(0.22, 1, 0.36, 1), width 300ms cubic-bezier(0.22, 1, 0.36, 1), height 300ms cubic-bezier(0.22, 1, 0.36, 1); }
    .nav-links a, .nav-links select, .nav-links button { color:#fff; font-weight:600; text-decoration:none; }
    .layout { display:grid; grid-template-columns: 220px 1fr; gap:1rem; padding:1rem 0; }
    .sidebar { background:#ffffff; border:1px solid #9f6540; border-radius:12px; padding:1rem; }
    .sidebar a { display:block; padding:0.5rem 0.75rem; border-radius:8px; color:#0f172a; text-decoration:none; margin-bottom:0.35rem; border:1px solid #9f6540; }
    .sidebar a:hover { background:#f7ede5; }
    .card { background:#ffffff; border-radius:12px; padding:1rem; border:1px solid #9f6540; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:1rem; }
    .form-group { margin-bottom:0.75rem; }
    label { font-weight:600; display:block; margin-bottom:0.25rem; }
    input[type="text"], input[type="date"], select, textarea { width:100%; padding:0.6rem; border:2px solid #9f6540; border-radius:8px; background:#fff; }
    textarea { min-height:120px; }
    .btn-primary { background:#9f6540; border-color:#9f6540; }
    .alert { padding:0.6rem 0.8rem; border-radius:10px; margin-bottom:0.75rem; }
    .alert-success { background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; }
    .alert-danger { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #e2e8f0; padding:0.5rem; text-align:left; }
    th { background:#f8f1ea; }
    /* Night mode */
    body[data-theme="dark"] { background: linear-gradient(135deg, #0f1115 0%, #1a1d23 100%); color: #e9ecef; }
    body[data-theme="dark"] .navbar { background: linear-gradient(135deg, #0d5f5a 0%, #0f766e 100%); box-shadow:none; }
    body[data-theme="dark"] .card { background:#0f1724; color:#e2e8f0; border:1px solid #9f6540; box-shadow:none; }
    body[data-theme="dark"] .sidebar { background:#0f1724; color:#e2e8f0; border:1px solid #9f6540; }
    body[data-theme="dark"] table { color:#e2e8f0; }
    body[data-theme="dark"] th { background:#0f1724; }
  </style>
</head>
<body data-theme="<?= htmlspecialchars($currentTheme) ?>">
  <nav class="navbar">
    <div class="container">
      <h1>
        <img src="../home/assets/images/team/logo.png" alt="Logo" class="logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex'">
        <span style="display:none; font-weight:800;" class="logo-fallback">🏢</span>Super Admin
      </h1>
      <div class="nav-links">
        <span class="nav-highlight"></span>
        <button id="themeToggle" title="Toggle theme" aria-pressed="false">🌞</button>
        <a href="super_admin.php">Control</a>
        <a href="../add-student.php">Student</a>
        <a href="../add-admin.php">Admin</a>
        <a href="Manage_Students.php">Manage Students</a>
        <a href="analyes.php">Analyes</a>
        <a href="notifiction.php">Notifiction</a>
        <a href="news_notifiction.php">News Notifications</a>
        <a href="../Admin/login.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container layout">
    <main>
      <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

      <div class="card shadow-sm" id="unit-approvals">
        <h3>Approvals by Unit (Year <?= $curYear ?>)</h3>
        <p style="margin:0.25rem 0 0.75rem; color:#64748b;">Based on students who requested this year</p>
        <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:0.75rem;">
          <?php foreach($units_stats as $u): ?>
            <div class="stat-card" style="background:#ffffff; border:1px solid #9f6540; border-radius:12px; padding:0.9rem; box-shadow:0 4px 12px rgba(0,0,0,0.06); display:flex; align-items:center; gap:0.75rem;">
              <svg width="64" height="64" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="16" fill="none" stroke="#e2e8f0" stroke-width="4" />
                <circle cx="18" cy="18" r="16" fill="none" stroke="#9f6540" stroke-width="4" stroke-linecap="round"
                        stroke-dasharray="<?= intval($u['pct']) ?>,100" transform="rotate(-90 18 18)" />
                <text x="18" y="20" text-anchor="middle" font-size="7" fill="#0f172a" font-weight="800"><?= intval($u['pct']) ?>%</text>
              </svg>
              <div>
                <div style="font-weight:700; color:#9f6540;"><?= htmlspecialchars($u['label']) ?></div>
                <div style="font-size:1.2rem; font-weight:800;"><?= intval($u['approved']) ?> / <?= intval($u['total']) ?></div>
                <div style="color:#64748b; font-size:0.9rem;">Approved / Requested</div>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (empty($units_stats)): ?>
            <div style="color:#64748b;">No approval data</div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card shadow-sm" style="margin-bottom:1rem;">
        <h3 style="margin-bottom:0.75rem;">Analytics Overview</h3>
        <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:0.75rem; align-items:center;">
          <div class="stat-card" style="background:#ffffff; border:1px solid #9f6540; border-radius:12px; padding:0.9rem; box-shadow:0 4px 12px rgba(0,0,0,0.06);">
            <div style="font-weight:700; color:#9f6540;">Total Students</div>
            <div style="font-size:1.6rem; font-weight:800;" data-count="<?= $total_students ?>">0</div>
            <div style="color:#64748b; font-size:0.9rem;">All registered profiles</div>
          </div>
          <div class="stat-card" style="background:#ffffff; border:1px solid #9f6540; border-radius:12px; padding:0.9rem; box-shadow:0 4px 12px rgba(0,0,0,0.06);">
            <div style="font-weight:700; color:#9f6540;">Requested (This Year)</div>
            <div style="font-size:1.6rem; font-weight:800;" data-count="<?= $requested_year ?>">0</div>
            <div style="color:#64748b; font-size:0.9rem;">Year <?= $curYear ?></div>
          </div>
          <div class="stat-card" style="background:#ffffff; border:1px solid #9f6540; border-radius:12px; padding:0.9rem; box-shadow:0 4px 12px rgba(0,0,0,0.06);">
            <div style="font-weight:700; color:#9f6540;">Requested (All)</div>
            <div style="font-size:1.6rem; font-weight:800;" data-count="<?= $requested_all ?>">0</div>
            <div style="color:#64748b; font-size:0.9rem;">Lifetime</div>
          </div>
          <div class="stat-card" style="background:#ffffff; border:1px solid #9f6540; border-radius:12px; padding:0.9rem; box-shadow:0 4px 12px rgba(0,0,0,0.06);">
            <div style="font-weight:700; color:#9f6540;">Not Requested</div>
            <div style="font-size:1.6rem; font-weight:800;" data-count="<?= $not_requested ?>">0</div>
            <div style="color:#64748b; font-size:0.9rem;">Registered without request</div>
          </div>
          <div class="donut-card" style="display:flex; align-items:center; gap:0.75rem; background:#ffffff; border:1px solid #9f6540; border-radius:12px; padding:0.9rem; box-shadow:0 4px 12px rgba(0,0,0,0.06);">
            <?php $pct = ($total_students>0) ? round(($requested_all/$total_students)*100) : 0; ?>
            <svg width="84" height="84" viewBox="0 0 36 36">
              <circle cx="18" cy="18" r="16" fill="none" stroke="#e2e8f0" stroke-width="4" />
              <circle cx="18" cy="18" r="16" fill="none" stroke="#9f6540" stroke-width="4" stroke-linecap="round"
                      stroke-dasharray="<?= $pct ?>,100" transform="rotate(-90 18 18)" />
              <text x="18" y="20" text-anchor="middle" font-size="8" fill="#0f172a" font-weight="800"><?= $pct ?>%</text>
            </svg>
            <div>
              <div style="font-weight:700; color:#9f6540;">Requests Coverage</div>
              <div style="color:#64748b; font-size:0.9rem;">Requested / Total</div>
            </div>
          </div>
        </div>
        
        <div style="margin-top:0.75rem;">
          <h4 style="margin-bottom:0.5rem;">Departments • Registered vs Requested</h4>
          <div style="display:grid; grid-template-columns:1fr; gap:0.4rem;">
            <?php foreach($dept_stats as $d): $reg=intval($d['reg_count']); $req=intval($d['req_count']); $pctDept=$reg>0?round(($req/$reg)*100):0; ?>
              <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="min-width:140px; font-weight:600; color:#0f172a;"><?= htmlspecialchars($d['dept'] ?: '—') ?></div>
                <div style="flex:1; height:10px; background:#e2e8f0; border-radius:6px; position:relative; overflow:hidden;">
                  <div style="width:<?= $pctDept ?>%; height:100%; background:#9f6540; border-radius:6px; transition:width 1s ease;"></div>
                </div>
                <div style="min-width:140px; color:#64748b; font-size:0.9rem;">Reg: <?= $reg ?> • Req: <?= $req ?> (<?= $pctDept ?>%)</div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($dept_stats)): ?>
              <div style="color:#64748b;">No department data</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="grid">
        <div class="card shadow-sm" id="requested-by-year">
          <h3>Requested Clearance • By Year</h3>
          <form method="post" style="margin-bottom:0.75rem; display:flex; gap:0.5rem; align-items:flex-end;">
            <input type="hidden" name="action" value="filter_year">
            <div class="form-group">
              <label>Year</label>
              <input type="number" name="year" value="<?= htmlspecialchars($filter_year ?: date('Y')) ?>" class="form-control" min="2000" max="9999">
            </div>
            <button class="btn btn-secondary" type="submit">View</button>
          </form>
          <?php if (!empty($requested_year_students)): ?>
            <table class="table table-striped table-hover">
              <thead><tr><th>Fullname</th><th>Matric</th><th>Session</th><th>Faculty</th><th>Dept</th><th>Phone</th><th>Request Year</th><th>Permission</th></tr></thead>
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
                    <td style="display:flex; gap:0.25rem;">
                      <form method="post" style="display:inline-flex; gap:0.25rem;">
                        <input type="hidden" name="action" value="reset_request_year">
                        <input type="hidden" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>">
                        <button class="btn btn-warning btn-sm" type="submit">Allow Re‑request</button>
                      </form>
                      <form method="post" style="display:inline-flex; gap:0.25rem;">
                        <input type="hidden" name="action" value="allow_notify">
                        <input type="hidden" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>">
                        <button class="btn btn-info btn-sm" type="submit">Allow & Notify</button>
                      </form>
                      <form method="post" style="display:inline-flex; gap:0.25rem;">
                        <input type="hidden" name="action" value="set_request_year">
                        <input type="hidden" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>">
                        <input type="number" name="year" value="<?= htmlspecialchars(($filter_year ?: date('Y')) + 1) ?>" class="form-control form-control-sm" style="width:90px" min="2000" max="9999">
                        <button class="btn btn-success btn-sm" type="submit">Set Year</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div style="color:#64748b;">Use the Year filter to view all students who requested clearance for that year.</div>
          <?php endif; ?>
        </div>
      </div>
</main>
  </div>
<script>
  const themeToggle = document.getElementById('themeToggle');
  const bodyEl = document.body;
  function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days*24*60*60*1000));
    document.cookie = name + "=" + value + ";path=/;expires=" + d.toUTCString();
  }
  function getCookie(name) {
    const pairs = document.cookie.split(';').map(s => s.trim());
    for (let p of pairs) { if (!p) continue; const [k, v] = p.split('='); if (k === name) return v; }
    return null;
  }
  function applyTheme(theme) {
    bodyEl.setAttribute('data-theme', theme);
    document.documentElement.setAttribute('data-theme', theme);
    if (themeToggle) {
      themeToggle.textContent = theme === 'dark' ? '🌙' : '🌞';
      themeToggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
    }
  }
  (function initTheme(){
    let t = bodyEl.getAttribute('data-theme') || getCookie('theme') || 'light';
    applyTheme(t);
  })();
  if (themeToggle) {
    themeToggle.addEventListener('click', function(){
      const current = bodyEl.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      const next = current === 'dark' ? 'light' : 'dark';
      applyTheme(next);
      setCookie('theme', next, 365);
    });
  }
  const navLinks = document.querySelector('.nav-links');
  const highlight = document.querySelector('.nav-highlight');
  function moveHighlightTo(el){
    if (!navLinks || !highlight || !el) return;
    const cr = navLinks.getBoundingClientRect();
    const r = el.getBoundingClientRect();
    const left = r.left - cr.left;
    const top = r.top - cr.top;
    highlight.style.left = left + 'px';
    highlight.style.top = top + 'px';
    highlight.style.width = r.width + 'px';
    highlight.style.height = r.height + 'px';
    highlight.style.opacity = 1;
  }
  function hideHighlight(){ if (highlight) highlight.style.opacity = 0; }
  if (navLinks && highlight){
    navLinks.addEventListener('mouseleave', hideHighlight);
    const items = navLinks.querySelectorAll('a, button');
    items.forEach(function(it){
      it.addEventListener('mouseenter', function(){ moveHighlightTo(it); });
      it.addEventListener('mousemove', function(e){
        const r = it.getBoundingClientRect();
        const relX = e.clientX - r.left;
        const relY = e.clientY - r.top;
        const cx = Math.max(0, Math.min(1, relX / Math.max(1, r.width))) * 100;
        const cy = Math.max(0, Math.min(1, relY / Math.max(1, r.height))) * 100;
        highlight.style.background = 'radial-gradient(120px 120px at ' + cx + '% ' + cy + '%, rgba(255,255,255,0.28), rgba(255,255,255,0.12))';
      });
    });
  }
</script>
</body>
</html>
