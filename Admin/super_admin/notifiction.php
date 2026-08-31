<?php
session_start();
error_reporting(0);
include('../connect.php');

$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');
$currentTheme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
setcookie('lang', $currentLang, time() + (86400 * 30), "/");

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
      if (!$valid) { $error = 'Student not found for given matric number.'; }
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
<!doctype html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Notifications • Online Clearance</title>
  <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">

  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #ffffffff; transition: background 0.3s ease, color 0.3s ease; color: #0f172a; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
    .navbar { background: linear-gradient(135deg, #007bff 0%, #ccccff 100%); box-shadow: 0 4px 20px #007bff; padding: 1.25rem 0; position: sticky; top: 0; z-index: 50; }
    .navbar .container { display:flex; justify-content:space-between; align-items:center; }
    .navbar h1 { font-size: 1.25rem; color: #fff; font-weight:800; display:flex; align-items:center; gap:0.75rem; }
    .logo-img { height:44px; width:44px; object-fit:cover; border-radius:50%; background:white; padding:0.2rem; border:2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    .nav-links { display:flex; gap:0.9rem; align-items:center; position:relative; }
    .nav-links button { background: rgba(255,255,255,0.22); border: 0; padding:0.45rem; width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:white; }
    .nav-links a { position:relative; padding:0.35rem 0.6rem; border-radius:8px; color:#fff; font-weight:600; text-decoration:none; }
    .nav-highlight { position:absolute; top:0; left:0; width:0; height:0; border-radius:10px; background:radial-gradient(120px 120px at 50% 50%, rgba(255,255,255,0.25), rgba(255,255,255,0.1)); box-shadow:0 6px 24px rgba(0,0,0,0.15); pointer-events:none; opacity:0; transform:translate3d(0,0,0); transition: opacity 200ms ease, left 300ms cubic-bezier(0.22, 1, 0.36, 1), top 300ms cubic-bezier(0.22, 1, 0.36, 1), width 300ms cubic-bezier(0.22, 1, 0.36, 1), height 300ms cubic-bezier(0.22, 1, 0.36, 1); }
    .section { max-width:1200px; margin:1rem auto; padding:1rem; }
    .section-wrapper { background:white; padding:1.25rem; border-radius:12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border:1px solid #9f6540; }
    .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1rem; }
    .form-group { margin-bottom:0.75rem; }
    label { font-weight:600; display:block; margin-bottom:0.25rem; }
    input[type="text"], input[type="date"], select, textarea { width:100%; padding:0.6rem; border:2px solid #9f6540; border-radius:8px; background:#fff; }
    textarea { min-height:120px; }
    .btn { display:inline-block; padding:0.6rem 1.2rem; background:#9f6540; color:#fff; border-radius:10px; font-weight:700; border:none; cursor:pointer; }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #e2e8f0; padding:0.5rem; text-align:left; }
    th { background:#f8f1ea; }
    .alert { padding:0.6rem 0.8rem; border-radius:10px; margin-bottom:0.75rem; }
    .alert-success { background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; }
    .alert-danger { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
  </style>
</head>
<body data-theme="<?= htmlspecialchars($currentTheme) ?>">
  <nav class="navbar">
    <div class="container">
      <h1>
        <img src="../home/assets/images/team/logo.png" alt="Logo" class="logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex'">
        <span style="display:none; font-weight:800;" class="logo-fallback">🏢</span>
        Admin Notifications
      </h1>
      <div class="nav-links">
        <span class="nav-highlight"></span>
        <button id="themeToggle" title="Toggle theme" aria-pressed="false">🌞</button>
        <a href="super_admin.php">Control</a>
        <a href="Manage_Students.php">Manage Students</a>
        <a href="analyes.php">Analyes</a>
        <a href="notifiction.php">Notifiction</a>
        <a href="news_notifiction.php">News Notifications</a>
        <a href="../Admin/login.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="section container">
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="section-wrapper">
      <div class="grid">
        <div class="card" style="border:1px solid #9f6540; border-radius:12px; padding:1rem;">
          <h3>Global Clearance Requests</h3>
          <p style="margin:0.25rem 0 0.5rem;">Current: <strong><?= $global_open==='1' ? 'OPEN' : 'CLOSED' ?></strong></p>
          <form method="post">
            <input type="hidden" name="action" value="toggle_global">
            <div class="form-group">
              <label>Set Status</label>
              <select name="clearance_open">
                <option value="1" <?= $global_open==='1' ? 'selected' : '' ?>>Open</option>
                <option value="0" <?= $global_open!=='1' ? 'selected' : '' ?>>Closed</option>
              </select>
            </div>
            <button class="btn" type="submit">Save</button>
          </form>
        </div>

        <div class="card" style="border:1px solid #9f6540; border-radius:12px; padding:1rem;">
          <h3>Today Clearance Day Control</h3>
          <p style="margin:0.25rem 0 0.5rem;">Today (<?= htmlspecialchars($today) ?>): <strong><?= $today_status===1 ? 'ON' : 'OFF' ?></strong></p>
          <p style="margin:0.25rem 0 0.5rem;">Time: <strong><?= ($today_start ? htmlspecialchars($today_start) : '—') ?> - <?= ($today_end ? htmlspecialchars($today_end) : '—') ?></strong></p>
          <?php if ($today_note): ?><p style="margin:0.25rem 0 0.5rem;">Note: <?= htmlspecialchars($today_note) ?></p><?php endif; ?>
          <form method="post" style="margin-bottom:0.75rem;">
            <input type="hidden" name="action" value="toggle_day">
            <div class="form-group">
              <label>Date</label>
              <input type="date" name="date" value="<?= htmlspecialchars($today) ?>">
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="is_open">
                <option value="1" <?= $today_status===1 ? 'selected' : '' ?>>On</option>
                <option value="0" <?= $today_status!==1 ? 'selected' : '' ?>>Off</option>
              </select>
            </div>
            <div class="form-group">
              <label>Start Time</label>
              <input type="time" name="start_time" value="<?= htmlspecialchars($today_start) ?>">
            </div>
            <div class="form-group">
              <label>End Time</label>
              <input type="time" name="end_time" value="<?= htmlspecialchars($today_end) ?>">
            </div>
            <div class="form-group">
              <label>Announcement Note</label>
              <input type="text" name="note" placeholder="Optional note" value="<?= htmlspecialchars($today_note) ?>">
            </div>
            <button class="btn" type="submit">Update Day</button>
          </form>
        </div>

        <div class="card" style="border:1px solid #9f6540; border-radius:12px; padding:1rem;">
          <h3>Send Notification to Student</h3>
          <form method="post">
            <input type="hidden" name="action" value="notify">
            <div class="form-group">
              <label>ID No (optional for specific student, leave empty for general)</label>
              <input type="text" name="recipient_matric" placeholder="e.g. RU/0370">
            </div>
            <div class="form-group">
              <label>Subject</label>
              <input type="text" name="subject" required>
            </div>
            <div class="form-group">
              <label>Message</label>
              <textarea name="message" required></textarea>
            </div>
            <button class="btn" type="submit">Send</button>
          </form>
        </div>
      </div>
    </div>
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
    let t = bodyEl.getAttribute('data-theme') || (document.cookie.indexOf('theme=')>-1 ? document.cookie.split('theme=')[1].split(';')[0] : 'light');
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
    body[data-theme="dark"] { background: linear-gradient(135deg, #0f1115 0%, #1a1d23 100%); color: #e9ecef; }
    body[data-theme="dark"] .navbar { background: linear-gradient(135deg, #0d5f5a 0%, #0f766e 100%); box-shadow:none; }
    body[data-theme="dark"] .section-wrapper { background:#0f1724; color:#e2e8f0; border:1px solid #9f6540; box-shadow:none; }
    body[data-theme="dark"] table { color:#e2e8f0; }
    body[data-theme="dark"] th { background:#0f1724; }
