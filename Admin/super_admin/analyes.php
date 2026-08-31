<?php
session_start();
error_reporting(0);
include('../connect.php');

$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');
$currentTheme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
setcookie('lang', $currentLang, time() + (86400 * 30), "/");

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
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Analytics • Online Clearance</title>
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
    .row-list { display:grid; grid-template-columns: 1fr; gap:0.5rem; }
    .row-item { display:flex; justify-content:space-between; align-items:center; border:1px solid #e2e8f0; border-radius:10px; padding:0.65rem 0.75rem; }
    .row-item .label { font-weight:700; color:#9f6540; }
    .row-item .value { font-size:1.25rem; font-weight:800; }
    .bar-item { display:flex; align-items:center; gap:0.75rem; }
    .bar { flex:1; height:10px; background:#e2e8f0; border-radius:6px; position:relative; overflow:hidden; }
    .bar > div { height:100%; background:#9f6540; border-radius:6px; transition:width 1s ease; }
    .unit-row { display:flex; align-items:center; gap:0.75rem; border:1px solid #e2e8f0; border-radius:10px; padding:0.65rem 0.75rem; }
  </style>
</head>
<body data-theme="<?= htmlspecialchars($currentTheme) ?>">
  <nav class="navbar">
    <div class="container">
      <h1>
        <img src="../home/assets/images/team/logo.png" alt="Logo" class="logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex'">
        <span style="display:none; font-weight:800;" class="logo-fallback">🏢</span>
        Analytics Overview
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
      <h3 style="margin-bottom:0.75rem;">Analytics Overview</h3>
      <div class="row-list">
        <div class="row-item"><div class="label">Total Students</div><div class="value"><?= $total_students ?></div></div>
        <div class="row-item"><div class="label">Requested (2025)</div><div class="value"><?= $requested_year ?></div></div>
        <div class="row-item"><div class="label">Requested (All)</div><div class="value"><?= $requested_all ?></div></div>
        <div class="row-item"><div class="label">Not Requested</div><div class="value"><?= $not_requested ?></div></div>
      </div>
    </div>
  </div>

  <div class="section container">
    <div class="section-wrapper">
      <h3 style="margin-bottom:0.75rem;">Departments • Registered vs Requested</h3>
      <div class="row-list">
        <?php foreach($dept_stats as $d): $reg=intval($d['reg_count']); $req=intval($d['req_count']); $pctDept=$reg>0?round(($req/$reg)*100):0; ?>
          <div class="bar-item">
            <div style="min-width:140px; font-weight:600; color:#0f172a;"><?= htmlspecialchars($d['dept'] ?: '—') ?></div>
            <div class="bar"><div style="width:<?= $pctDept ?>%"></div></div>
            <div style="min-width:180px; color:#64748b; font-size:0.9rem;">Reg: <?= $reg ?> • Req: <?= $req ?> (<?= $pctDept ?>%)</div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($dept_stats)): ?>
          <div style="color:#64748b;">No department data</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="section container">
    <div class="section-wrapper">
      <h3 style="margin-bottom:0.75rem;">Approvals by Unit (Year 2025)</h3>
      <div class="row-list">
        <?php foreach($units_stats as $u): ?>
          <div class="unit-row">
            <svg width="64" height="64" viewBox="0 0 36 36">
              <circle cx="18" cy="18" r="16" fill="none" stroke="#e2e8f0" stroke-width="4" />
              <circle cx="18" cy="18" r="16" fill="none" stroke="#9f6540" stroke-width="4" stroke-linecap="round" stroke-dasharray="<?= intval($u['pct']) ?>,100" transform="rotate(-90 18 18)" />
              <text x="18" y="20" text-anchor="middle" font-size="7" fill="#0f172a" font-weight="800"><?= intval($u['pct']) ?>%</text>
            </svg>
            <div style="min-width:160px; font-weight:700; color:#9f6540;"><?= htmlspecialchars($u['label']) ?></div>
            <div style="font-size:1.1rem; font-weight:800;"><?= intval($u['approved']) ?> / <?= intval($u['total']) ?></div>
            <div style="color:#64748b; font-size:0.9rem;">Approved / Requested</div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($units_stats)): ?>
          <div style="color:#64748b;">No approval data</div>
        <?php endif; ?>
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

