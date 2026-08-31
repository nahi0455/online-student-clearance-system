<?php
session_start();
error_reporting(0);
include('../connect.php');

$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');
$currentTheme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
setcookie('lang', $currentLang, time() + (86400 * 30), "/");

function t($key) {
    global $currentLang;
    $translations = [
        'en' => [
            'hrms_title' => 'Online Student Clearance System',
            'about_us' => 'About',
            'hrms_subtitle' => 'Manage student clearance, departments and approvals easily.',
            'home' => 'Home', 'Developer' => 'Developer', 'contact' => 'Contact', 'login' => 'Login',
            'overview' => 'Overview',
            'overview_desc' => 'This system coordinates one-by-one approvals across units so students can complete clearance quickly and transparently.',
            'units' => 'Units Involved',
            'units_desc' => 'Department Head, Library, Bookstore, Dormitory, Cafeteria, Sport Master, Student Dean, Campus Police, Registrar',
            'stats' => 'Live Stats',
            'students' => 'Students', 'departments' => 'Departments', 'admins' => 'Admins', 'sessions' => 'Recent Sessions',
            'features' => 'Key Features',
            'students_desc' => 'View student profiles and track clearance progress.',
            'approvals' => 'Approvals',
            'approvals_desc' => 'One-by-one approvals across departments and units.',
            'registrar' => 'Registrar',
            'registrar_desc' => 'Finalize clearance and issue the registrar stamp.',
            'search' => 'Search',
            'search_desc' => 'Find students by matric number, session, or department.'
        ],
        'om' => [
            'hrms_title' => 'Sistama Onlayinii Qulqullina Barattootaa',
            'about_us' => 'Sistama',
            'hrms_subtitle' => 'Qulqullina barattootaa, kutaa fi mirkaneessa salphaa to’achu.',
            'home' => 'Fuula Duraa', 'Developer' => 'Kutaalee', 'contact' => 'Nu Quunnamaa', 'login' => 'Seeni',
            'overview' => 'Gabaasa',
            'overview_desc' => 'Sistamni kun mirkaneessa kutaa kutaan to’atee barattoonni saffisaan qulqullina guutan ni taasisa.',
            'units' => 'Kutaa Hirmaatan',
            'units_desc' => 'Department Head, Library, Bookstore, Dormitory, Cafeteria, Sport, Dean, Police, Registrar',
            'stats' => 'Lakkoofsa Ijaarsaa',
            'students' => 'Barattoota', 'departments' => 'Kutaalee', 'admins' => 'Bulchitoota', 'sessions' => 'Sesheenota Itti Aanuu',
            'features' => 'Amaloota',
            'students_desc' => 'Odeeffannoo barataa fi haala qulqullinaa hordofi.',
            'approvals' => 'Mirkaneessawwan',
            'approvals_desc' => 'Mirkaneessa kutaa kutaan itti aanan.',
            'registrar' => 'Rijistara',
            'registrar_desc' => 'Qulqullina xumuraan raawwadhu, mallattoo rijistara kenni.',
            'search' => 'Barbaacha',
            'search_desc' => 'Barbaadi lakk. matrikii, sesheen, yookin kutaa.'
        ],
        'am' => [
            'hrms_title' => 'የተማሪ ንጽህና ስርዓት',
            'about_us' => 'ስለ ስርዓቱ',
            'hrms_subtitle' => 'የተማሪ ንጽህና፣ ክፍሎች እና ማረጋገጫዎችን በቀላሉ ያቀናብሩ.',
            'home' => 'መነሻ', 'Developer' => 'ክፍሎች', 'contact' => 'አግኙን', 'login' => 'ግባ',
            'overview' => 'አጠቃላይ መግለጫ',
            'overview_desc' => 'ይህ ስርዓት በክፍል ክፍል ማረጋገጫ ሂደት ተደርጎ ተማሪዎች ፈጣን እና ግልፅ ንጽህና እንዲጨርሱ ይረዳል።',
            'units' => 'የሚሳተፉ ክፍሎች',
            'units_desc' => 'ዳይሬክተር, ላይብረሪ, መጻፍ ቤት, መኝታ ቤት, ካፊቴሪያ, ስፖርት, ዲን, ፖሊስ, ሪጀስትራ',
            'stats' => 'በድር ላይ ስታቲስቲክስ',
            'students' => 'ተማሪዎች', 'departments' => 'ክፍሎች', 'admins' => 'አስተዳዳሪዎች', 'sessions' => 'ቅርብ ሴሽኖች',
            'features' => 'ባህሪዎች',
            'students_desc' => 'የተማሪ መግለጫና የንጽህና ሁኔታ አሳይ.',
            'approvals' => 'ማረጋገጫዎች',
            'approvals_desc' => 'በክፍል ክፍል የሚተካ ማረጋገጫ.',
            'registrar' => 'ሪጀስትራ',
            'registrar_desc' => 'ንጽህናን ያበቃ እና ማህተሙን ይሰጣል.',
            'search' => 'ፍለጋ',
            'search_desc' => 'ተማሪ፣ ክፍል ወይም ሴሽን ፈልግ.'
        ]
    ];
    if (!isset($translations[$currentLang]) || !isset($translations[$currentLang][$key])) {
        return $translations['en'][$key] ?? $key;
    }
    return $translations[$currentLang][$key];
}

// Simple live stats from database
$students_count = 0; $departments_count = 0; $admins_count = 0; $recent_sessions = [];
$rs = mysqli_query($conn, "SELECT COUNT(*) c FROM register"); if ($rs) { $row = mysqli_fetch_assoc($rs); $students_count = intval($row['c']); }
$rs = mysqli_query($conn, "SELECT COUNT(DISTINCT dept) c FROM students"); if ($rs) { $row = mysqli_fetch_assoc($rs); $departments_count = intval($row['c']); }
$rs = mysqli_query($conn, "SELECT COUNT(*) c FROM admin"); if ($rs) { $row = mysqli_fetch_assoc($rs); $admins_count = intval($row['c']); }
$rs = mysqli_query($conn, "SELECT session FROM tblsession ORDER BY ID DESC LIMIT 3"); if ($rs) { while($row=mysqli_fetch_assoc($rs)){ $recent_sessions[] = $row['session']; } }
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars(t('hrms_title')) ?></title>
<link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#fff; color:#0f172a; }
.container { max-width:1200px; margin:0 auto; padding:0 1.5rem; }
.navbar { background: linear-gradient(135deg, #007bff 0%, #ccccff 100%); box-shadow:0 4px 20px rgba(0,0,0,0.1); padding:1.25rem 0; position:sticky; top:0; z-index:50; }
.navbar .container { display:flex; justify-content:space-between; align-items:center; }
.navbar h1 { font-size:1.25rem; color:#fff; font-weight:800; display:flex; align-items:center; gap:0.75rem; }
.logo-img { height:44px; width:44px; object-fit:cover; border-radius:50%; background:white; padding:0.2rem; border:2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.nav-links { display:flex; gap:0.75rem; align-items:center; }
.nav-links a, .nav-links select, .nav-links button { color:#fff; font-weight:600; }
.nav-links select { padding:0.45rem 0.6rem; border-radius:8px; border:none; cursor:pointer; font-weight:600; color:#0f766e; background:#fff; }
.nav-links button { background: rgba(255,255,255,0.22); border:0; padding:0.45rem; width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:white; }
.hero { background: linear-gradient(135deg, #ccccff 0%, #007bff 100%); color:white; padding:3rem 0; text-align:center; }
.section { max-width:1000px; margin:1rem auto; padding:1rem; }
.card { background:white; border-radius:12px; padding:1rem; box-shadow:0 4px 12px rgba(0,0,0,0.06); border:1px solid #9f6540; }
.grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:1rem; }
.stat { text-align:center; }
.stat h3 { margin:0.25rem 0; }
footer { background:#0f172a; color:#cbd5e1; padding:1.5rem 0; text-align:center; margin-top:1.5rem; }

.features-section { max-width:1200px; margin:-1rem auto 1rem; padding:0 1rem; }
.features-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:1rem; }
.feature-item { background:white; border-radius:12px; padding:1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border:1px solid #9f6540; cursor:pointer; }
.feature-icon { font-size:1.5rem; margin-bottom:0.5rem; display:inline-block; color:#9f6540; }
.feature-item:hover { transform: translateY(-5px); box-shadow: 5px 20px 24px #9f6540; transition: 0.3s ease-in-out; }

        /* Dark theme using body[data-theme="dark"] */
        body[data-theme="dark"] {
            background: linear-gradient(135deg, #0f1115 0%, #1a1d23 100%);
            color: #e9ecef;
        }
        body[data-theme="dark"] .navbar { background: linear-gradient(135deg, #0d5f5a 0%, #0f766e 100%); }
        body[data-theme="dark"] .hero { background: linear-gradient(135deg, #0d5f5a 0%, #0f766e 100%); }
        body[data-theme="dark"] .feature-item { background:#0f1724; color:#e2e8f0; border:1px solid #9f6540; box-shadow:none; }
        body[data-theme="dark"] .section { background:#0f1724; color:#f2b80a; }
        body[data-theme="dark"] .card {background:#0f1724; color:#e2e8f0; border:1px solid #9f6540; box-shadow:none; }

    /* Roles Section */
    .rules-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap:1.25rem; margin:1.5rem 0; }
    .rule-card { background:white; border-radius:14px; padding:1.5rem; box-shadow:0 4px 12px rgba(0,0,0,0.06); border:1px solid #9f6540; transition:all 0.3s ease; position:relative; overflow:hidden; }
    .rule-card:hover { transform:translateY(-6px); box-shadow:0 12px 30px rgba(159,101,64,0.25); }
    .rule-header { display:flex; align-items:center; margin-bottom:1rem; padding-bottom:0.75rem; border-bottom:2px solid rgba(159,101,64,0.15); }
    .rule-number { width:44px; height:44px; background:linear-gradient(135deg,#007bff,#ccccff); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:16px; margin-right:1rem; flex-shrink:0; }
    .rule-title h3 { font-size:1.1rem; font-weight:700; color:#007bff; margin:0 0 2px 0; }
    .rule-title .department { font-size:0.82rem; color:#64748b; }
    .criteria-item { display:flex; align-items:flex-start; margin-bottom:6px; }
    .criteria-item i { color:#9f6540; margin-right:8px; margin-top:3px; font-size:10px; }
    .criteria-text { font-size:0.9rem; color:#475569; line-height:1.5; }
    .approval-condition { background:linear-gradient(135deg,#10b981,#059669); color:white; padding:10px 14px; border-radius:10px; font-weight:600; font-size:0.88rem; display:flex; align-items:center; margin-top:1rem; }
    .approval-condition i { margin-right:8px; }
    .final-approval-card { background:white; border:2px solid #007bff; border-radius:14px; padding:2rem; text-align:center; margin:1.5rem 0; box-shadow:0 4px 20px rgba(0,123,255,0.15); }
    .final-approval-card h2 { color:#007bff; font-size:1.5rem; font-weight:800; margin-bottom:0.75rem; }
    .final-requirements { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:0.75rem; margin-top:1.25rem; }
    .final-requirement { background:rgba(0,123,255,0.07); padding:12px; border-radius:10px; border:1px solid rgba(0,123,255,0.2); font-size:0.9rem; font-weight:600; color:#0f172a; }
    .final-requirement i { color:#007bff; margin-right:6px; }
    .important-note { background:linear-gradient(135deg,#f59e0b,#d97706); color:white; padding:1.25rem 1.5rem; border-radius:12px; margin:1.5rem 0; }
    .important-note h3 { font-size:1rem; font-weight:700; margin-bottom:6px; }
    .important-note p { margin:0; font-size:0.9rem; line-height:1.5; }
    body[data-theme="dark"] .rule-card { background:#0f1724; color:#e2e8f0; border:1px solid #9f6540; }
    body[data-theme="dark"] .rule-title h3 { color:#93c5fd; }
    body[data-theme="dark"] .criteria-text { color:#94a3b8; }
    body[data-theme="dark"] .final-approval-card { background:#0f1724; color:#e2e8f0; }
    body[data-theme="dark"] .final-requirement { background:rgba(147,197,253,0.1); color:#e2e8f0; }
</style>

</head>

<body data-theme="<?= htmlspecialchars($currentTheme) ?>">

<nav class="navbar">
    <div class="container">
        <h1>
            <img src="assets/images/team/logo.png" alt="HRMS Logo" class="logo-img"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex'">
            <span style="display:none; font-weight:800;" class="logo-fallback">🏢</span>
            <?= htmlspecialchars(t('hrms_title')) ?>
        </h1>

        <div class="nav-links">
            <select id="langSelector" aria-label="Select Language">
                <option value="en" <?= $currentLang === 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
                <option value="om" <?= $currentLang === 'om' ? 'selected' : '' ?>>🇪🇹 Afaan Oromoo</option>
                <option value="am" <?= $currentLang === 'am' ? 'selected' : '' ?>>🇪🇹 አማርኛ</option>
            </select>

            <button id="themeToggle" title="Toggle theme" aria-pressed="false">🌞</button>

            <a href="index.php"><?= htmlspecialchars(t('home')) ?></a>
            <a href="history.php"><?= htmlspecialchars(t('history')) ?></a>
            <a href="about.php"><?= htmlspecialchars(t('about_us')) ?></a>
            <a href="contact.php"><?= htmlspecialchars(t('contact')) ?></a>
        </div>
    </div>
</nav>

<section class="hero">
  <div class="container">
    <h2><?= htmlspecialchars(t('about_us')) ?></h2>
    <p><?= htmlspecialchars(t('hrms_subtitle')) ?></p>
  </div>
</section>

<div class="features-section container">
  <div class="features-grid">
    <div class="feature-item">
      <div class="feature-icon">👥</div>
      <h3><?= htmlspecialchars(t('students')) ?></h3>
      <p><?= htmlspecialchars(t('students_desc')) ?></p>
    </div>
    <div class="feature-item">
      <div class="feature-icon">✅</div>
      <h3><?= htmlspecialchars(t('approvals')) ?></h3>
      <p><?= htmlspecialchars(t('approvals_desc')) ?></p>
    </div>
    <div class="feature-item">
      <div class="feature-icon">🎓</div>
      <h3><?= htmlspecialchars(t('registrar')) ?></h3>
      <p><?= htmlspecialchars(t('registrar_desc')) ?></p>
    </div>
    <div class="feature-item">
      <div class="feature-icon">🔍</div>
      <h3><?= htmlspecialchars(t('search')) ?></h3>
      <p><?= htmlspecialchars(t('search_desc')) ?></p>
    </div>
  </div>
</div>

<div class="section container">
  <div class="card">
    <h3><?= htmlspecialchars(t('overview')) ?></h3>
    <p><?= htmlspecialchars(t('overview_desc')) ?></p>
    <h3 style="margin-top:0.75rem;"><?= htmlspecialchars(t('units')) ?></h3>
    <p><?= htmlspecialchars(t('units_desc')) ?></p>
  </div>
</div>

<div class="section container">

  <!-- Important Notice -->
  <div class="important-note">
    <h3><i class="fa fa-exclamation-triangle"></i> Important Notice</h3>
    <p>A student must satisfy ALL departmental criteria before the final registrar stamp is issued. Each department has specific requirements that must be fulfilled for clearance approval.</p>
  </div>

  <!-- 9 Roles Grid -->
  <div class="rules-grid">

    <div class="rule-card">
      <div class="rule-header">
        <div class="rule-number">1</div>
        <div class="rule-title"><h3>Department Head</h3><div class="department">Academic Department</div></div>
      </div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">Student has completed all academic requirements</div></div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">Student must set profile photo</div></div>
      <div class="approval-condition"><i class="fa fa-check-circle"></i><span>Approve if: All academic obligations are fulfilled</span></div>
    </div>

    <div class="rule-card">
      <div class="rule-header">
        <div class="rule-number">2</div>
        <div class="rule-title"><h3>Library</h3><div class="department">Library Services</div></div>
      </div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">No borrowed books pending</div></div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">No overdue fines</div></div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">Library ID/account cleared</div></div>
      <div class="approval-condition"><i class="fa fa-check-circle"></i><span>Approve if: Library account balance = 0</span></div>
    </div>

    <div class="rule-card">
      <div class="rule-header">
        <div class="rule-number">3</div>
        <div class="rule-title"><h3>Bookstore</h3><div class="department">University Bookstore</div></div>
      </div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">All university-issued books/materials returned</div></div>
      <div class="approval-condition"><i class="fa fa-check-circle"></i><span>Approve if: No outstanding materials or payments</span></div>
    </div>

    <div class="rule-card">
      <div class="rule-header">
        <div class="rule-number">4</div>
        <div class="rule-title"><h3>Dormitory</h3><div class="department">Housing Services</div></div>
      </div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">Dorm room properly vacated</div></div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">No property damage</div></div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">All dormitory keys returned</div></div>
      <div class="approval-condition"><i class="fa fa-check-circle"></i><span>Approve if: Room inspection passed</span></div>
    </div>

    <div class="rule-card">
      <div class="rule-header">
        <div class="rule-number">5</div>
        <div class="rule-title"><h3>Cafeteria</h3><div class="department">Food Services</div></div>
      </div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">Cafeteria card/account settled</div></div>
      <div class="approval-condition"><i class="fa fa-check-circle"></i><span>Approve if: No outstanding cafeteria balance</span></div>
    </div>

    <div class="rule-card">
      <div class="rule-header">
        <div class="rule-number">6</div>
        <div class="rule-title"><h3>Sport Master</h3><div class="department">Sports Office</div></div>
      </div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">All sports equipment returned</div></div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">No unpaid sports-related penalties</div></div>
      <div class="approval-condition"><i class="fa fa-check-circle"></i><span>Approve if: No sports assets assigned to the student</span></div>
    </div>

    <div class="rule-card">
      <div class="rule-header">
        <div class="rule-number">7</div>
        <div class="rule-title"><h3>Student Dean</h3><div class="department">Student Affairs</div></div>
      </div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">No disciplinary cases</div></div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">All disciplinary penalties resolved</div></div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">Student conduct record cleared</div></div>
      <div class="approval-condition"><i class="fa fa-check-circle"></i><span>Approve if: Student has a clean disciplinary record</span></div>
    </div>

    <div class="rule-card">
      <div class="rule-header">
        <div class="rule-number">8</div>
        <div class="rule-title"><h3>Campus Police</h3><div class="department">Security Office</div></div>
      </div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">No security violations</div></div>
      <div class="criteria-item"><i class="fa fa-circle"></i><div class="criteria-text">No unresolved incident reports</div></div>
      <div class="approval-condition"><i class="fa fa-check-circle"></i><span>Approve if: No active security case</span></div>
    </div>

  </div>

  <!-- Role 9: Registrar Final Approval -->
  <div class="final-approval-card">
    <h2><i class="fa fa-stamp"></i> Registrar — Final Approval &amp; Stamp</h2>
    <p style="color:#475569; margin-bottom:0.5rem;">The registrar provides the final stamp only after ALL departmental clearances are completed and verified.</p>
    <div class="final-requirements">
      <div class="final-requirement"><i class="fa fa-check-square"></i>All 8 departments approved</div>
      <div class="final-requirement"><i class="fa fa-id-card"></i>Student identity verified</div>
      <div class="final-requirement"><i class="fa fa-sign-out"></i>Exit status confirmed</div>
    </div>
    <div class="approval-condition" style="margin-top:1.25rem; justify-content:center;">
      <i class="fa fa-trophy"></i>
      <span>Final Approval: All departmental clearances are completed</span>
    </div>
  </div>

</div>

<footer>
   <div style="margin-bottom:0.75rem; font-weight:700;">Bule Hora University - Computer Science and Engineering Department Project</div>
        <div style="font-size:0.95rem;">
            <a href="https://bhu.edu.et" target="_blank" style="color:#14b8a6;">www.bhu.edu.et</a>
            &nbsp;•&nbsp;Online Clearance student system &nbsp;•&nbsp; &copy; <?= date('Y') ?>
        </div>
    </div></footer>

<script>
  function setCookie(name, value, days) {
    const d = new Date(); d.setTime(d.getTime() + (days*24*60*60*1000));
    document.cookie = name + "=" + value + ";path=/;expires=" + d.toUTCString();
  }
  function getCookie(name) {
    const pairs = document.cookie.split(';').map(s => s.trim());
    for (let p of pairs) { if (!p) continue; const [k,v] = p.split('='); if (k === name) return v; }
    return null;
  }
  const body = document.body; const themeToggle = document.getElementById('themeToggle');
  (function initTheme(){ let theme = body.getAttribute('data-theme') || getCookie('theme') || 'light'; applyTheme(theme); })();
  function applyTheme(theme){ body.setAttribute('data-theme', theme); themeToggle.textContent = theme === 'dark' ? '🌙' : '🌞'; themeToggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false'); }
  themeToggle.addEventListener('click', function(){ const current = body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light'; const next = current === 'dark' ? 'light' : 'dark'; applyTheme(next); setCookie('theme', next, 365); });
  document.getElementById('langSelector').addEventListener('change', function(){ const url = new URL(window.location.href); url.searchParams.set('lang', this.value); window.location.href = url.toString(); });
</script>

</body>
</html>
