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
            'home' => 'Home', 'careers' => 'Departments', 'about_us' => 'About the System', 'contact' => 'Contact', 'login' => 'Login',
            'Developer' => 'Developer',
            'developer_title' => 'Developer',
            'developer_subtitle' => 'System builder and maintainer',
            'profile' => 'Profile',
            'contact_me' => 'Contact Me',
            'tech_stack' => 'Tech Stack',
            'name' => 'Name',
            'email' => 'Email',
            'github' => 'GitHub',
            'website' => 'Website',
            'system' => 'System',
            'system_name' => 'Online Student Clearance System',
            'version' => 'Version',
            'stack_items' => 'PHP, MySQL, HTML, CSS, JavaScript'
        ],
        'om' => [
            'hrms_title' => 'Sistama Onlayinii Qulqullina Barattootaa',
            'home' => 'Fuula Duraa', 'careers' => 'Kutaalee', 'about_us' => 'Waa’ee Sistama', 'contact' => 'Nu Quunnamaa', 'login' => 'Seeni',
            'Developer' => 'Develeperaa',
            'developer_title' => 'Develeperaa',
            'developer_subtitle' => 'Ummataa fi tiksaa sistamaa',
            'profile' => 'Piroofaayilii',
            'contact_me' => 'Na Quunnamaa',
            'tech_stack' => 'Teeknooloojii',
            'name' => 'Maqaa',
            'email' => 'Imeelii',
            'github' => 'GitHub',
            'website' => 'Weebsaayitii',
            'system' => 'Sistama',
            'system_name' => 'Sistama Qulqullina Barattootaa',
            'version' => 'Vershini',
            'stack_items' => 'PHP, MySQL, HTML, CSS, JavaScript'
        ],
        'am' => [
            'hrms_title' => 'የተማሪ ንጽህና ስርዓት',
            'home' => 'መነሻ', 'careers' => 'ክፍሎች', 'about_us' => 'ስለ ስርዓቱ', 'contact' => 'አግኙን', 'login' => 'ግባ',
            'Developer' => 'ዲቨሎፐር',
            'developer_title' => 'ዲቨሎፐር',
            'developer_subtitle' => 'ስርዓቱን የሚገነባ እና የሚተካ',
            'profile' => 'መገለጫ',
            'contact_me' => 'አግኙኝ',
            'tech_stack' => 'ቴክኖሎጂ',
            'name' => 'ስም',
            'email' => 'ኢሜይል',
            'github' => 'GitHub',
            'website' => 'ድር ገጽ',
            'system' => 'ስርዓት',
            'system_name' => 'የተማሪ ንጽህና ስርዓት',
            'version' => 'እትም',
            'stack_items' => 'PHP, MySQL, HTML, CSS, JavaScript'
        ]
    ];
    if (!isset($translations[$currentLang]) || !isset($translations[$currentLang][$key])) {
        return $translations['en'][$key] ?? $key;
    }
    return $translations[$currentLang][$key];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars(t('hrms_title')) ?> • <?= htmlspecialchars(t('Developer')) ?></title>
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#fff; color:#0f172a; }
.container { max-width:1200px; margin:0 auto; padding:0 1.5rem; }
.navbar { background: linear-gradient(135deg, #9f6540 0%, #9f6540 100%); box-shadow:0 4px 20px rgba(0,0,0,0.1); padding:1.25rem 0; position:sticky; top:0; z-index:50; }
.navbar .container { display:flex; justify-content:space-between; align-items:center; }
.navbar h1 { font-size:1.25rem; color:#fff; font-weight:800; display:flex; align-items:center; gap:0.75rem; }
.logo-img { height:44px; width:44px; object-fit:cover; border-radius:50%; background:white; padding:0.2rem; border:2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.nav-links { display:flex; gap:0.75rem; align-items:center; }
.nav-links a, .nav-links select, .nav-links button { color:#fff; font-weight:600; }
.nav-links a { text-decoration:none; }
.nav-links select { padding:0.45rem 0.6rem; border-radius:8px; border:none; cursor:pointer; font-weight:600; color:#0f766e; background:#fff; }
.nav-links button { background: rgba(255,255,255,0.22); border:0; padding:0.45rem; width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:white; }
.hero { background: linear-gradient(135deg, #d3b9a5 0%, #f2b80a 100%); color:white; padding:3rem 0; text-align:center; }
.section { max-width:1000px; margin:1rem auto; padding:1rem; }
.grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:1rem; }
.card { background:white; border-radius:12px; padding:1rem; box-shadow:0 4px 12px rgba(0,0,0,0.06); border:1px solid #9f6540; }
.chip { display:inline-block; padding:0.35rem 0.6rem; border:1px solid #9f6540; border-radius:999px; margin:0.25rem; font-size:0.9rem; }
footer { background:#0f172a; color:#cbd5e1; padding:1.5rem 0; text-align:center; margin-top:1.5rem; }
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
    <h2><?= htmlspecialchars(t('developer_title')) ?></h2>
    <p><?= htmlspecialchars(t('developer_subtitle')) ?></p>
  </div>
</section>

<div class="section container">
  <div class="grid">
    <div class="card">
      <h3><?= htmlspecialchars(t('profile')) ?></h3>
      <p><strong><?= htmlspecialchars(t('name')) ?>:</strong> Developer</p>
      <p><strong><?= htmlspecialchars(t('system')) ?>:</strong> <?= htmlspecialchars(t('system_name')) ?></p>
      <p><strong><?= htmlspecialchars(t('version')) ?>:</strong> 1.0</p>
    </div>
    <div class="card">
      <h3><?= htmlspecialchars(t('contact_me')) ?></h3>
      <p>📧 <?= htmlspecialchars(t('email')) ?>: <a href="mailto:info@bhu.edu.et">info@bhu.edu.et</a></p>
      <p>🐙 <?= htmlspecialchars(t('github')) ?>: <a href="#" target="_blank">github.com/example</a></p>
      <p>🌐 <?= htmlspecialchars(t('website')) ?>: <a href="https://bhu.edu.et" target="_blank">bhu.edu.et</a></p>
    </div>
    <div class="card">
      <h3><?= htmlspecialchars(t('tech_stack')) ?></h3>
      <div>
        <span class="chip">PHP</span>
        <span class="chip">MySQL</span>
        <span class="chip">HTML</span>
        <span class="chip">CSS</span>
        <span class="chip">JavaScript</span>
      </div>
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
function setCookie(name, value, days) { const d = new Date(); d.setTime(d.getTime() + (days*24*60*60*1000)); document.cookie = name+"="+value+";path=/;expires="+d.toUTCString(); }
function getCookie(name) { const pairs = document.cookie.split(';').map(s => s.trim()); for (let p of pairs) { if (!p) continue; const [k,v] = p.split('='); if (k===name) return v; } return null; }
const themeToggle = document.getElementById('themeToggle');
const body = document.body;
function applyTheme(theme){ body.setAttribute('data-theme', theme); themeToggle.textContent = theme === 'dark' ? '🌙' : '🌞'; themeToggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false'); }
(function initTheme(){ const saved = getCookie('theme') || body.getAttribute('data-theme') || 'light'; applyTheme(saved); })();
themeToggle.addEventListener('click', function(){ const current = body.getAttribute('data-theme')==='dark'?'dark':'light'; const next = current==='dark'?'light':'dark'; applyTheme(next); setCookie('theme', next, 365); });
document.getElementById('langSelector').addEventListener('change', function(){ const url = new URL(window.location.href); url.searchParams.set('lang', this.value); window.location.href = url.toString(); });
</script>
</body>
</html>