
<?php
// page.php - Standalone fixed version

// --- Basic config & inputs ---
$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');
$currentTheme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';

// Persist language selection (simple)
setcookie('lang', $currentLang, time() + (86400 * 30), "/"); // 30 days

// --- Simple translation function (extend translations here) ---
function t($key) {
    global $currentLang;

$translations = [
    'en' => [
        'hrms_title' => 'Online Student Clearance System',
        'hrms_subtitle' => 'Manage student clearance, departments and approvals easily.',
        'login_to_system' => 'Login to Clearance System',
        'home' => 'Home',
        'history' => 'History',
        'about_us' => 'About',
        'contact' => 'Contact',
        'login' => 'Login',

        'employee_management' => 'Student Clearance',
        'employee_management_desc' => 'Track student records and clearance status.',

        'report_generation' => 'Report Generation',
        'report_generation_desc' => 'Generate clearance reports and analytics.',

        'secure_access' => 'Secure Access',
        'secure_access_desc' => 'Role-based access for students and staff.',

        'quick_search' => 'Quick Search',
        'quick_search_desc' => 'Search students, departments or clearance records.',

        'vacancy_management' => 'Department Management',
        'vacancy_management_desc' => 'Manage clearance departments such as Library, Registrar, Finance, Dormitory, ICT.',

        'employee_self_service' => 'Student Self Service',
        'employee_self_service_desc' => 'Students can view status and request clearance.',

        'welcome_to_hrms' => 'Welcome to the Student Clearance System',
        'welcome_desc' => 'A modern solution for student clearance management.',
        'welcome_desc2' => 'Simple, fast and secure.',

        'meet_our_team' => 'Our Clearance Staff',
        'get_in_touch' => 'Get Support',
        'services' => 'Services',
        'our_services' => 'Clearance Services'
    ],

    'om' => [ // Afaan Oromoo (corrected)
        'hrms_title' => 'Sistama Onlayinii Qulqullina Barattootaa',
        'hrms_subtitle' => 'Qulqullina barattootaa, kutaa fi mirkaneessa to\'achuu salphaa.',
        'login_to_system' => 'Sistama Qulqullinaa Seeni',
        'home' => 'Fuula Duraa',
        'history' => 'seennaa',
        'about_us' => 'Waa’ee Sistama',
        'contact' => 'Nu Quunnamaa',
        'login' => 'Seeni',

        'employee_management' => 'Qulqullina Barattootaa',
        'employee_management_desc' => 'Odeeffannoo barattootaa fi haala qulqullinaa hordofuu.',

        'report_generation' => 'Gabaasa Uumuu',
        'report_generation_desc' => 'Gabaasota qulqullinaa saffisaan uumuu.',

        'secure_access' => 'Seensa Nageenya Qabu',
        'secure_access_desc' => 'Barattoota fi hojjettootaaf seensa ramadamee.',

        'quick_search' => 'Barbaacha Saffisaa',
        'quick_search_desc' => 'Barattoota, kutaa ykn galmee qulqullinaa barbaadi.',

        'vacancy_management' => 'Bulchiinsa Kutaalee Qulqullinaa',
        'vacancy_management_desc' => 'Library, Registrar, Finance, Dormitory, ICT fi kkf to\'achuu.',

        'employee_self_service' => 'Tajaajila Barataa',
        'employee_self_service_desc' => 'Barattoonni haala qulqullinaa isaanii ilaalu danda\'u.',

        'welcome_to_hrms' => 'Baga Gara Sistama Qulqullina Barattootaa Dhuftan',
        'welcome_desc' => 'Sistama ammayyaa qulqullina barattootaa.',
        'welcome_desc2' => 'Saffisaa, amansiisaa fi salphaa.',

        'meet_our_team' => 'Hojjettoota Qulqullinaa Keenya',
        'get_in_touch' => 'Nu Quunnamaa',
        'services' => 'Tajaajilawwan',
        'our_services' => 'Tajaajilawwan Qulqullinaa'
    ],

    'am' => [ // Amharic (corrected)
        'hrms_title' => 'የተማሪ ንጽህና ማረጋገጫ ስርዓት',
        'hrms_subtitle' => 'የተማሪ ንጽህና፣ ክፍሎች እና ማረጋገጫዎችን በቀላሉ ያቀናብሩ.',
        'login_to_system' => 'ወደ ንጽህና ስርዓት ግባ',
        'home' => 'መነሻ',
        'history' => 'ታሪከ',
        'about_us' => 'ስለ ስርዓቱ',
        'contact' => 'አግኙን',
        'login' => 'ግባ',

        'employee_management' => 'የተማሪ ንጽህና አስተዳደር',
        'employee_management_desc' => 'የተማሪዎች መረጃ እና የንጽህና ሁኔታ ይከታተሉ.',

        'report_generation' => 'ዘገባ ፍጠር',
        'report_generation_desc' => 'የንጽህና ዘገባዎችን በፍጥነት ይፍጠሩ.',

        'secure_access' => 'የደህንነት መዳረሻ',
        'secure_access_desc' => 'ለተማሪዎችና ሰራተኞች ተመድቦ የተሰጠ መዳረሻ.',

        'quick_search' => 'ፈጣን ፍለጋ',
        'quick_search_desc' => 'ተማሪዎችን፣ ክፍሎችን ወይም የንጽህና መዝገቦችን ፈልጉ.',

        'vacancy_management' => 'የክፍል አስተዳደር',
        'vacancy_management_desc' => 'Library, Registrar, Finance, Dormitory, ICT ያሉ ክፍሎችን ያቀናብሩ.',

        'employee_self_service' => 'የተማሪ ራስ አገልግሎት',
        'employee_self_service_desc' => 'ተማሪዎች የንጽህና ሁኔታቸውን ማየት ይችላሉ.',

        'welcome_to_hrms' => 'ወደ ተማሪ ንጽህና ስርዓት በደህና መጡ',
        'welcome_desc' => 'ዘመናዊ የተማሪ ንጽህና መፍትሄ.',
        'welcome_desc2' => 'ቀላል፣ ፈጣን እና የተጠበቀ.',

        'meet_our_team' => 'የንጽህና ባለሞያዎቻችን',
        'get_in_touch' => 'አግኙን',
        'services' => 'አገልግሎቶች',
        'our_services' => 'የንጽህና አገልግሎቶች'
    ]
];


    if (!isset($translations[$currentLang]) || !isset($translations[$currentLang][$key])) {
        // fallback to english if missing
        return $translations['en'][$key] ?? $key;
    }
    return $translations[$currentLang][$key];
}
?>
<!doctype html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(t('hrms_title')) ?> Clearance System</title>
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">

    <!-- Tabler icons CDN (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">

    <style>
        /* Reset & base */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #ffffffff; transition: background 0.3s ease, color 0.3s ease; color: #0f172a; }
        a { color: inherit; text-decoration: none; }

        /* NAV */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
        .navbar { background: linear-gradient(135deg, #007bff

 0%, #ccccff
100%); box-shadow: 0 4px 20px #007bff; padding: 1.25rem 0; position: sticky; top: 0; z-index: 50; }
        .navbar .container { display:flex; justify-content:space-between; align-items:center; }
        .navbar h1 { font-size: 1.25rem; color: #fff; font-weight:800; display:flex; align-items:center; gap:0.75rem; }
        .logo-img { height:44px; width:44px; object-fit:cover; border-radius:50%; background:white; padding:0.2rem; border:2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .nav-links { display:flex; gap:0.75rem; align-items:center; }
        .nav-links a, .nav-links select, .nav-links button { color: #fff; font-weight: 600; }

        .nav-links select { padding: 0.45rem 0.6rem; border-radius:8px; border: none; cursor: pointer; font-weight:600; color:#0f766e; background: #fff; }
        .nav-links button { background: rgba(255,255,255,0.22); border: 0; padding:0.45rem; width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:white; }

        /* Hero */
        .hero-section { background: linear-gradient(135deg, #ccccff 0%, #007bff 100%); color: white; padding:4.5rem 1rem; text-align:center; }
        .hero-title { font-size:2rem; margin-bottom:0.75rem; line-height:1.1; }
        .hero-subtitle { font-size:1rem; margin-bottom:1.25rem; color: rgba(255,255,255,0.95); }
        .btn { display:inline-block; padding:0.6rem 1.2rem; background:#ffffffff ; color:#0a0a0aff; border-radius:10px; font-weight:700; margin:0.25rem; }

        /* Features grid */
          .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: 5px 20px 24px #ffffffff;
    }
        .features-section { max-width:1200px; margin:-2rem auto 2rem; padding:0 1rem; }
        .features-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:1rem; }
        .feature-item { background:white; border-radius:12px; padding:1rem; box-shadow:0 4px 12px  #0a0a0aff; border:1px solid #9f6540; cursor:pointer; }
        .feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 5px 20px 24px #9f6540;
    transition: 0.3s ease-in-out;
}

        .feature-icon { font-size:1.5rem; margin-bottom:0.5rem; display:inline-block; color:#9f6540; }

        /* Content */
        .content-section { max-width:1200px; margin:1rem auto; padding:1rem; }
        .section-wrapper { background:white; padding:1.25rem; border-radius:12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }

        /* Footer */
        footer { background: #0f172a; color:#cbd5e1; padding:1.5rem 0; margin-top:1.5rem; text-align:center; }

        /* Quick responsive */
        @media (max-width:768px) {
            .hero-title { font-size:1.5rem; }
            .nav-links { gap:0.5rem; flex-wrap:wrap; }
            .features-grid { grid-template-columns: 1fr; }
        }

        /* Dark theme using body[data-theme="dark"] */
        body[data-theme="dark"] {
            background: linear-gradient(135deg, #0f1115 0%, #1a1d23 100%);
            color: #e9ecef;
        }
        body[data-theme="dark"] .navbar { background: linear-gradient(135deg, #0d5f5a 0%, #0f766e 100%); }
        body[data-theme="dark"] .hero-section { background: linear-gradient(135deg, #0d5f5a 0%, #0f766e 100%); }
        body[data-theme="dark"] .feature-item { background:#0f1724; color:#e2e8f0; border:1px solid #9f6540; box-shadow:none; }
        body[data-theme="dark"] .footer { background:#071024; color:#f2b80a; }
        body[data-theme="dark"] .section-wrapper {background:#0f1724; color:#f2b80a; border:1px solid #9f6540; box-shadow:none; }

    </style>
</head>
<body data-theme="<?= htmlspecialchars($currentTheme) ?>">
    <div class="contact-card:hover {
">
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

<section class="hero-section">
    <div class="hero-content container">
        <h2 class="hero-title"><?= htmlspecialchars(t('hrms_title')) ?></h2>
        <p class="hero-subtitle"><?= htmlspecialchars(t('hrms_subtitle')) ?></p>
        <div>
            <a class="btn" href="../login student/login.php"><?= htmlspecialchars(t('Login to Student')) ?></a>
            <a class="btn" href="../login admin/login.php"><?= htmlspecialchars(t('Login to Admin')) ?></a>
        </div>
    </div>
</section>

<div class="features-section container">
    <div class="features-grid">
        <div class="feature-item">
            <div class="feature-icon">👥</div>
            <h3>Students</h3>
            <p>View student profiles and track clearance progress.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">✅</div>
            <h3>Approvals</h3>
            <p>One-by-one approvals across departments and units.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🎓</div>
            <h3>Registrar</h3>
            <p>Finalize clearance and issue the registrar stamp.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🔍</div>
            <h3>Search</h3>
            <p>Find students by matric number, session, or department.</p>
        </div>
    </div>
</div>

<div class="content-section container">
    <div class="section-wrapper">
        <h2><?= htmlspecialchars(t('welcome_to_hrms')) ?></h2>
        <p><?= htmlspecialchars(t('welcome_desc')) ?></p>
        <p><?= htmlspecialchars(t('welcome_desc2')) ?></p>

        <div style="margin-top:1rem; display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:0.75rem;">
            <a class="feature-item" href="../login admin/login.php"><strong>🎓 Student Registrar</strong><div style="font-size:0.85rem; color: #64748b; margin-top:6px;">Student clearance registration</div></a>
            <a class="feature-item" href="history.php"><strong>🏛️ Bule Hora University History</strong><div style="font-size:0.85rem; color: #64748b; margin-top:6px;">Learn about our university's heritage</div></a>
            <a class="feature-item" href="about.php"><strong>ℹ️ <?= htmlspecialchars(t('about_us')) ?></strong><div style="font-size:0.85rem; color: #64748b; margin-top:6px;"><?= htmlspecialchars(t('meet_our_team')) ?></div></a>
            <a class="feature-item" href="contact.php"><strong>📧 <?= htmlspecialchars(t('contact')) ?></strong><div style="font-size:0.85rem; color: #64748b; margin-top:6px;"><?= htmlspecialchars(t('get_in_touch')) ?></div></a>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        <div style="margin-bottom:0.75rem; font-weight:700;">Bule Hora University - Computer Science and Engineering Department Project</div>
        <div style="font-size:0.95rem;">
            <a href="https://bhu.edu.et" target="_blank" style="color:#14b8a6;">www.bhu.edu.et</a>
            &nbsp;•&nbsp;Online Clearance student system &nbsp;•&nbsp; &copy; <?= date('Y') ?>
        </div>
    </div>
        </div>

</footer>

<script>
    // Language selection
    document.getElementById('langSelector').addEventListener('change', function() {
        const lang = this.value;
        // add existing query params if needed; simple redirect with lang param
        const url = new URL(window.location.href);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    });

    // Theme toggle (persists in cookie)
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

    function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + (days*24*60*60*1000));
        document.cookie = name + "=" + value + ";path=/;expires=" + d.toUTCString();
    }

    function getCookie(name) {
        const pairs = document.cookie.split(';').map(s => s.trim());
        for (let p of pairs) {
            if (!p) continue;
            const [k, v] = p.split('=');
            if (k === name) return v;
        }
        return null;
    }

    function applyTheme(theme) {
        body.setAttribute('data-theme', theme);
        document.documentElement.setAttribute('data-theme', theme);
        themeToggle.textContent = theme === 'dark' ? '🌙' : '🌞';
        themeToggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
    }

    // Initial theme from body attribute (server rendered)
    (function initTheme() {
        let theme = body.getAttribute('data-theme') || getCookie('theme') || 'light';
        applyTheme(theme);
    })();

    themeToggle.addEventListener('click', function() {
        const current = body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        setCookie('theme', next, 365);
    });
</script>

</body>
</html>
