<?php
session_start();
error_reporting(1);
include('../connect.php');

$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');
$currentTheme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
setcookie('lang', $currentLang, time() + (86400 * 30), "/");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS contact_messages (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  name VARCHAR(100) NOT NULL,\n  email VARCHAR(100) NOT NULL,\n  subject VARCHAR(200) NOT NULL,\n  message TEXT NOT NULL,\n  ip_address VARCHAR(45) DEFAULT NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function t($key) {
    global $currentLang;
    $translations = [
        'en' => [
            'hrms_title' => 'Online Student Clearance System',
            'home' => 'Home',
            'history' => 'History',
            'about_us' => 'About',
            'contact' => 'Contact',
            'login' => 'Login',
            'contact_us' => 'Contact Us',
            'get_in_touch' => 'Get Support',
            'contact_intro' => 'We\'re here to help with the clearance process.',
            'all_fields_required' => 'All fields are required',
            'invalid_email' => 'Invalid email address',
            'message_sent_successfully' => 'Message sent successfully',
            'message_send_failed' => 'Could not send message',
            'address' => 'Address',
            'call_us' => 'Call Us',
            'email_us' => 'Email Us',
            'office_hours' => 'Office Hours',
            'monday_friday' => 'Mon–Fri',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
            'closed' => 'Closed',
            'services' => 'Services',
            'send_message' => 'Send Message',
            'visit_us' => 'Visit Us',
            'bule_hora_university' => 'Bule Hora University',
            'bule_hora_ethiopia' => 'Bule Hora, Ethiopia',
            'po_box' => 'P.O. Box',
            'all_rights_reserved' => 'All rights reserved'
        ],
        'om' => [
            'hrms_title' => 'Sistama Onlayinii Qulqullina Barattootaa',
            'home' => 'Fuula Duraa',
            'developer' => 'Kutaalee',
            'about_us' => 'Waa\'ee Sistama',
            'contact' => 'Nu Quunnamaa',
            'login' => 'Seeni',
            'contact_us' => 'Nu Quunnamaa',
            'get_in_touch' => 'Deeggarsa Argadhu',
            'contact_intro' => 'Gara adeemsa qulqullina barattootaa keessatti si gargaarru.',
            'all_fields_required' => 'Cufaa guutuu',
            'invalid_email' => 'Imeelii sirrii miti',
            'message_sent_successfully' => 'Ergaan ergameera',
            'message_send_failed' => 'Ergaan hin ergamne',
            'address' => 'Teessoo',
            'call_us' => 'Nu Bilbilaa',
            'email_us' => 'Nu Imeelii',
            'office_hours' => 'Sa\'aatii Hojii',
            'monday_friday' => 'Wiixata–Jimaata',
            'saturday' => 'Sanbata',
            'sunday' => 'Dilbata',
            'closed' => 'Cufame',
            'services' => 'Tajaajilawwan',
            'send_message' => 'Ergaa Ergi',
            'visit_us' => 'Nu Dhaabu',
            'bule_hora_university' => 'Yuuniversitii Buule Horaa',
            'bule_hora_ethiopia' => 'Bule Hora, Itoophiyaa',
            'po_box' => 'P.O. Box',
            'all_rights_reserved' => 'Mirga hundi kan eegame'
        ],
        'am' => [
            'hrms_title' => 'የተማሪ ንጽህና ስርዓት',
            'home' => 'መነሻ',
            'history' => 'ክፍሎች',
            'about_us' => 'ስለ ስርዓቱ',
            'contact' => 'አግኙን',
            'login' => 'ግባ',
            'contact_us' => 'ያግኙን',
            'get_in_touch' => 'ድጋፍ ያግኙ',
            'contact_intro' => 'በንጽህና ሂደት ለመርዳት እንልካለን።',
            'all_fields_required' => 'ሁሉንም መስኮቶች ሙሉ ያድርጉ',
            'invalid_email' => 'የኢሜይል አድራሻ የተሳሳተ ነው',
            'message_sent_successfully' => 'መልዕክት ተልኳል',
            'message_send_failed' => 'መልዕክት ማስረከብ አልተቻለም',
            'address' => 'አድራሻ',
            'call_us' => 'ይደውሉልን',
            'email_us' => 'ኢሜይል ይላኩልን',
            'office_hours' => 'የቢሮ ሰዓት',
            'monday_friday' => 'ሰኞ–አርብ',
            'saturday' => 'ቅዳሜ',
            'sunday' => 'እሑድ',
            'closed' => 'ዝግ',
            'services' => 'አገልግሎቶች',
            'send_message' => 'መልዕክት ላክ',
            'visit_us' => 'ይጎብኙን',
            'bule_hora_university' => 'ቡሌ ሆራ ዩኒቨርሲቲ',
            'bule_hora_ethiopia' => 'ቡሌ ሆራ፣ ኢትዮጵያ',
            'po_box' => 'ማህደር ፖስታ',
            'all_rights_reserved' => 'መብቶች ሁሉ የተጠበቁ ናቸው'
        ]
    ];
    if (!isset($translations[$currentLang]) || !isset($translations[$currentLang][$key])) {
        return $translations['en'][$key] ?? $key;
    }
    return $translations[$currentLang][$key];
}


// Handle contact form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = t('all_fields_required');
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = t('invalid_email');
    } else {
        // Save to database or send email
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("sssss", $name, $email, $subject, $message, $ip);
        
        if ($stmt->execute()) {
            $success = t('message_sent_successfully');
            $name = $email = $subject = $message = '';
        } else {
            $error = t('message_send_failed');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" data-theme="<?= $currentTheme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('contact_us') ?> - <?= t('hrms_title') ?></title>
        <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">

    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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
.nav-links a { text-decoration: none !important; }
.nav-links a:hover { text-decoration: none !important; }
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
.feature-item:hover { transform: translateY(-5px); box-shadow: 5px 20px 24px #04d361ff; transition: 0.3s ease-in-out; }
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

.contact-card {
    background: var(--card-bg);
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 2px 10px  #070707ff;
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
    min-height: 280px;
}

.contact-card:hover {
    transform: translateY(-5px);
    box-shadow: 5px 20px 50px #9f6540;
    transition: 0.3s ease-in-out;

}

.feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 5px 20px 24px #9f6540;
    transition: 0.3s ease-in-out;
}
    
    .contact-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #f3f3f3ff 0%, #1888c9ff 100%);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        margin-bottom: 1.25rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--card-bg);
        color: var(--text-primary);
    }
    
    .form-control:focus {
        outline: none;
        border-color: #9f6540;
        box-shadow: 0 0 0 3px rgba(159, 101, 64, 0.15);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 150px;
    }
    
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border: 1px solid #6ee7b7;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    
    [data-theme="dark"] .alert-success {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
        color: #a7f3d0;
    }
    
    [data-theme="dark"] .alert-danger {
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
        color: #fecaca;
    }
    
    .info-item {
        display: flex;
        align-items: start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .info-icon {
        width: 40px;
        height: 40px;
        background: rgba(20, 184, 166, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f766e;
        flex-shrink: 0;
    }
    
    [data-theme="dark"] .info-icon {
        background: rgba(20, 184, 166, 0.2);
        color: #14b8a6;
    }
    
    /* Grid System */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -0.75rem;
        margin-right: -0.75rem;
    }
    
    .g-4 > * {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .col-12 {
        width: 100%;
    }
    
    .col-6 {
        width: 50%;
    }
    
    .col-md-3 {
        width: 50%;
    }
    
    .col-md-6 {
        width: 100%;
    }
    
    .offset-md-3 {
        margin-left: 0;
    }
    
    @media (min-width: 768px) {
        .col-md-3 {
            width: 25%;
        }
        .col-md-6 {
            width: 50%;
        }
        .offset-md-3 {
            margin-left: 25%;
        }
    }
    
    .h-100 {
        height: 100%;
    }
     .about  { max-width:1200px; margin:-3rem auto 3rem; padding:0 1rem; }

    /* Contact grid like index features-grid */
    .contact-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin: 1rem 0; }
    .section-card { margin: 2rem 0; }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #cbd5e1;
            text-align: center;
            padding: 2rem 1rem;
            margin-top: 3rem;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .nav-links { gap: 0.5rem; flex-wrap: wrap; }
            .nav-links a { padding: 0.5rem 0.75rem; font-size: 0.85rem; }
        }
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
    <h2><?= htmlspecialchars(t('get_in_touch')) ?></h2>
    <p><?= htmlspecialchars(t('contact_intro')) ?></p>
  </div>
</section>

<div class="about py-5">
    <!-- Contact Information Cards - Same gap as index features grid -->
    <div class="contact-grid">
        <div class="contact-card h-100" data-aos="fade-up" data-aos-delay="100">
            <div class="contact-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <h3 class="h5 mb-3"><?= t('visit_us') ?></h3>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <strong><?= t('bule_hora_university') ?></strong><br>
                    <?= t('bule_hora_ethiopia') ?><br>
                    <?= t('po_box') ?>: 144
                </div>
            </div>
        </div>

        <div class="contact-card h-100" data-aos="fade-up" data-aos-delay="200">
            <div class="contact-icon">
                <i class="fas fa-phone"></i>
            </div>
            <h3 class="h5 mb-3"><?= t('call_us') ?></h3>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div>
                    <a href="tel:+251464430232" class="text-decoration-none">+251 464430232</a><br>
                    <a href="tel:+251464430232" class="text-decoration-none">+251 46 4430232</a>
                </div>
            </div>
        </div>

        <div class="contact-card h-100" data-aos="fade-up" data-aos-delay="300">
            <div class="contact-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <h3 class="h5 mb-3"><?= t('email_us') ?></h3>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-at"></i>
                </div>
                <div>
                    <a href="https://www.bhu.edu.et/" target="_blank" class="text-decoration-none">https://www.bhu.edu.et/</a><br>
                    <a href="mailto:main.registrar@bhu.edu.et" class="text-decoration-none">main.registrar@bhu.edu.et</a>
                </div>
            </div>
        </div>

        <div class="contact-card h-100" data-aos="fade-up" data-aos-delay="400">
            <div class="contact-icon">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="h5 mb-3"><?= t('office_hours') ?></h3>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <strong><?= t('monday_friday') ?>:</strong> 8:00 AM - 5:00 PM<br>
                    <strong><?= t('saturday') ?>:</strong> 8:00 AM - 12:00 PM<br>
                    <strong><?= t('sunday') ?>:</strong> <?= t('closed') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Message - placed under contact info cards -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="contact-card section-card" data-aos="fade-up">
                <h2 class="h3 mb-4 text-center"><?= t('send_message') ?></h2>
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name ?? ''); ?>" placeholder="John Doe" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="john@example.com" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject ?? ''); ?>" placeholder="How can we help you?" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" placeholder="Tell us more about your inquiry..." required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Map Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="contact-card section-card">
                <h2 class="h3 mb-4 text-center">Find Us on Map</h2>
                <div style="height: 450px; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div id="map" style="height: 100%; width: 100%;"></div>
                </div>
                <div class="text-center mt-3">
                    <a href="https://www.google.com/maps/search/Bule+Hora+University,+Ethiopia" 
                       target="_blank" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-2"></i>Open in Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Footer -->
    <footer style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 3rem 0 1.5rem; text-align: center; color: #94a3b8; border-top: 3px solid #0f766e; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #0f766e, #14b8a6, #0f766e); animation: shimmer 3s infinite;"></div>
        <div class="container">
            <div style="display: flex; justify-content: center; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap;">
                <a href="index.php" style="color: #94a3b8; text-decoration: none; transition: color 0.3s; font-weight: 500;" onmouseover="this.style.color='#14b8a6'" onmouseout="this.style.color='#94a3b8'"><?= t('home') ?></a>
                <a href="about.php" style="color: #94a3b8; text-decoration: none; transition: color 0.3s; font-weight: 500;" onmouseover="this.style.color='#14b8a6'" onmouseout="this.style.color='#94a3b8'"><?= t('about_us') ?></a>
                <a href="services.php" style="color: #94a3b8; text-decoration: none; transition: color 0.3s; font-weight: 500;" onmouseover="this.style.color='#14b8a6'" onmouseout="this.style.color='#94a3b8'"><?= t('services') ?></a>
                <a href="contact.php" style="color: #94a3b8; text-decoration: none; transition: color 0.3s; font-weight: 500;" onmouseover="this.style.color='#14b8a6'" onmouseout="this.style.color='#94a3b8'"><?= t('contact') ?></a>
                <a href="vacancies.php" style="color: #94a3b8; text-decoration: none; transition: color 0.3s; font-weight: 500;" onmouseover="this.style.color='#14b8a6'" onmouseout="this.style.color='#94a3b8'"><?= t('careers') ?></a>
            </div>
            <div style="display: flex; justify-content: center; gap: 1.5rem; margin-bottom: 2rem;">
                <a href="https://linkedin.com" target="_blank" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: #94a3b8; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#0077b5'; this.style.color='white'; this.style.transform='translateY(-3px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#94a3b8'; this.style.transform='translateY(0)'">
                    <i class="ti ti-brand-linkedin" style="font-size: 1.25rem;"></i>
                </a>
                <a href="https://github.com" target="_blank" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: #94a3b8; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#333'; this.style.color='white'; this.style.transform='translateY(-3px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#94a3b8'; this.style.transform='translateY(0)'">
                    <i class="ti ti-brand-github" style="font-size: 1.25rem;"></i>
                </a>
                <a href="mailto:info@bhu.edu.et" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: #94a3b8; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#0f766e'; this.style.color='white'; this.style.transform='translateY(-3px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#94a3b8'; this.style.transform='translateY(0)'">
                    <i class="ti ti-mail" style="font-size: 1.25rem;"></i>
                </a>
                <a href="https://bhu.edu.et" target="_blank" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: #94a3b8; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#14b8a6'; this.style.color='white'; this.style.transform='translateY(-3px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#94a3b8'; this.style.transform='translateY(0)'">
                    <i class="ti ti-world" style="font-size: 1.25rem;"></i>
                </a>
            </div>
            <p style="margin: 0; font-size: 0.9rem;">&copy; <?= date('Y') ?> Bule Hora University. <?= t('all_rights_reserved') ?></p>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; color: #64748b;">Online Student Clearance System</p>
        </div>
    </footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    AOS.init({ duration: 800, once: true, offset: 100 });
    document.getElementById('langSelector').addEventListener('change', function() { const url = new URL(window.location.href); url.searchParams.set('lang', this.value); window.location.href = url.toString(); });

    function setCookie(name, value, days) { const d = new Date(); d.setTime(d.getTime() + (days*24*60*60*1000)); document.cookie = name+"="+value+";path=/;expires="+d.toUTCString(); }
    function getCookie(name) { const pairs = document.cookie.split(';').map(s => s.trim()); for (let p of pairs) { if (!p) continue; const [k,v] = p.split('='); if (k===name) return v; } return null; }

    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const body = document.body;
    function applyTheme(theme){ html.setAttribute('data-theme', theme); body.setAttribute('data-theme', theme); themeToggle.innerHTML = theme === 'dark' ? '<i class="ti ti-moon"></i>' : '<i class="ti ti-sun"></i>'; }
    (function initTheme(){ const saved = getCookie('theme') || html.getAttribute('data-theme') || 'light'; applyTheme(saved); })();
    themeToggle.addEventListener('click', function(){ const current = html.getAttribute('data-theme')==='dark'?'dark':'light'; const next = current==='dark'?'light':'dark'; applyTheme(next); setCookie('theme', next, 365); });

    // Map initialization

    // Initialize map
    // Bule Hora coordinates (approximate)
    const buleHoraLat = 5.5867;
    const buleHoraLng = 38.2333;
    
    const map = L.map('map').setView([buleHoraLat, buleHoraLng], 13);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Custom marker icon
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); width: 40px; height: 40px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3);"><i class="fas fa-university" style="color: white; transform: rotate(45deg); font-size: 18px;"></i></div>',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });
    
    // Add marker
    const marker = L.marker([buleHoraLat, buleHoraLng], { icon: customIcon }).addTo(map);
    
    // Add popup
    marker.bindPopup(`
        <div style="text-align: center; padding: 10px;">
            <h6 style="margin: 0 0 8px 0; color: #0f766e; font-weight: bold;">
                <i class="fas fa-university me-2"></i>Bule Hora University
            </h6>
            <p style="margin: 0 0 8px 0; font-size: 14px; color: #64748b;">
                Bule Hora, Ethiopia<br>
                P.O. Box: 144
            </p>
            <a href="https://www.google.com/maps/dir/?api=1&destination=${buleHoraLat},${buleHoraLng}" 
               target="_blank" 
               style="display: inline-block; padding: 6px 12px; background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); color: white; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600;">
                <i class="fas fa-directions me-1"></i>Get Directions
            </a>
        </div>
    `).openPopup();
    
    // Add circle to show approximate area
    L.circle([buleHoraLat, buleHoraLng], {
        color: '#14b8a6',
        fillColor: '#14b8a6',
        fillOpacity: 0.1,
        radius: 500
    }).addTo(map);
</script>

<style>
    .custom-marker {
        background: transparent;
        border: none;
    }
    
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }
    
    .leaflet-popup-tip {
        background: white;
    }
    
    [data-theme="dark"] .leaflet-popup-content-wrapper {
        background: #070707ff;
        color: #e9ecef;
    }
    
    [data-theme="dark"] .leaflet-popup-tip {
        background: #080808ff;
    }
</script>
</body>
</html>
