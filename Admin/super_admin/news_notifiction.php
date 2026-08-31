<?php
session_start();
error_reporting(0);
include('../connect.php');

$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');
$currentTheme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
setcookie('lang', $currentLang, time() + (86400 * 30), "/");

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
<!doctype html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recent Notifications • Online Clearance</title>
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
    .grid { display:grid; grid-template-columns: 1fr; gap:1rem; }
    .form-row { display:flex; gap:0.5rem; align-items:flex-end; margin-bottom:0.75rem; }
    .form-group { flex:1; }
    label { font-weight:600; display:block; margin-bottom:0.25rem; }
    input[type="text"], input[type="number"] { width:100%; padding:0.6rem; border:2px solid #9f6540; border-radius:8px; background:#fff; }
    .btn { display:inline-block; padding:0.6rem 1.2rem; background:#9f6540; color:#fff; border-radius:10px; font-weight:700; border:none; cursor:pointer; }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #e2e8f0; padding:0.5rem; text-align:left; }
    th { background:#f8f1ea; }
  </style>
</head>
<body data-theme="<?= htmlspecialchars($currentTheme) ?>">
  <nav class="navbar">
    <div class="container">
      <h1>
        <img src="../home/assets/images/team/logo.png" alt="Logo" class="logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex'">
        <span style="display:none; font-weight:800;" class="logo-fallback">🏢</span>
        Recent Notifications
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
    <div class="section-wrapper">
      <form method="get" class="form-row">
        <div class="form-group">
          <label>Recipient Matric</label>
          <input type="text" name="matric" value="<?= htmlspecialchars($matric) ?>" placeholder="e.g. RU/0370">
        </div>
        <div class="form-group">
          <label>Search</label>
          <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Subject or message">
        </div>
        <div class="form-group" style="max-width:160px;">
          <label>Limit</label>
          <input type="number" name="limit" value="<?= htmlspecialchars($limit) ?>" min="1" max="500">
        </div>
        <div>
          <button class="btn" type="submit">Find</button>
        </div>
      </form>

      <table>
        <thead><tr><th>ID</th><th>Matric</th><th>Subject</th><th>Message</th><th>Created</th></tr></thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="5" style="text-align:center; color:#64748b;">No notifications</td></tr>
          <?php else: foreach($rows as $n): ?>
            <tr>
              <td><?= intval($n['id']) ?></td>
              <td><?= htmlspecialchars($n['recipient_matric'] ?? '') ?></td>
              <td><?= htmlspecialchars($n['subject']) ?></td>
              <td><?= htmlspecialchars($n['message']) ?></td>
              <td><?= htmlspecialchars($n['created_at']) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
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
   