<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bule Hora University History</title>
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#f8fafc; color:#0f172a; }
        .container { max-width:1100px; margin:0 auto; padding:0 1.5rem; }

        /* Navbar */
        .navbar { background:linear-gradient(135deg,#007bff 0%,#ccccff 100%); box-shadow:0 4px 20px rgba(0,0,0,0.1); padding:1.25rem 0; position:sticky; top:0; z-index:50; }
        .navbar .container { display:flex; justify-content:space-between; align-items:center; }
        .navbar h1 { font-size:1.1rem; color:#fff; font-weight:800; display:flex; align-items:center; gap:0.75rem; }
        .logo-img { height:44px; width:44px; object-fit:cover; border-radius:50%; background:white; padding:0.2rem; border:2px solid rgba(255,255,255,0.3); }
        .nav-links { display:flex; gap:0.6rem; align-items:center; flex-wrap:wrap; }
        .nav-links a { color:#fff; font-weight:600; text-decoration:none; padding:0.4rem 0.75rem; border-radius:8px; transition:background 0.2s; }
        .nav-links a:hover { background:rgba(255,255,255,0.2); }
        .nav-links select { padding:0.45rem 0.6rem; border-radius:8px; border:none; cursor:pointer; font-weight:600; color:#0f766e; background:#fff; }
        .nav-links button { background:rgba(255,255,255,0.22); border:0; padding:0.45rem; width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:white; font-size:1rem; }

        /* Hero */
        .hero { background:linear-gradient(135deg,#ccccff 0%,#007bff 100%); color:white; padding:3rem 0; text-align:center; }
        .hero h2 { font-size:2rem; font-weight:800; margin-bottom:0.5rem; }
        .hero p { font-size:1rem; opacity:0.9; }

        /* Page body */
        .page-body { max-width:1100px; margin:2rem auto; padding:0 1.5rem 3rem; }
        .section-heading { font-size:1.3rem; font-weight:800; color:#0f172a; margin:2.5rem 0 1rem; display:flex; align-items:center; gap:0.6rem; }
        .section-heading::after { content:''; flex:1; height:2px; background:linear-gradient(90deg,#007bff,transparent); }

        /* Intro card */
        .intro-card { background:white; border-radius:14px; padding:2rem; border:1px solid #e2e8f0; box-shadow:0 4px 12px rgba(0,0,0,0.06); margin-bottom:2rem; display:flex; gap:1.5rem; align-items:center; }
        .intro-logo { width:110px; height:110px; border-radius:50%; flex-shrink:0; overflow:hidden; border:3px solid #007bff; background:#e0e7ff; display:flex; align-items:center; justify-content:center; font-size:3rem; }
        .intro-logo img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
        .intro-card p { font-size:1rem; line-height:1.8; color:#334155; }

        /* Stats */
        .stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:1rem; }
        .stat-card { background:white; border-radius:14px; padding:1.5rem 1rem; text-align:center; border:1px solid #e2e8f0; box-shadow:0 4px 12px rgba(0,0,0,0.05); transition:all 0.3s; }
        .stat-card:hover { transform:translateY(-4px); box-shadow:0 10px 25px rgba(0,123,255,0.15); border-color:#007bff; }
        .stat-card .icon { font-size:2rem; margin-bottom:0.5rem; }
        .stat-card .number { font-size:1.7rem; font-weight:800; color:#007bff; }
        .stat-card .label { font-size:0.82rem; color:#64748b; margin-top:4px; }

        /* Timeline */
        .timeline { position:relative; padding:0.5rem 0; }
        .timeline::before { content:''; position:absolute; left:50%; top:0; bottom:0; width:3px; background:linear-gradient(180deg,#007bff,#ccccff); transform:translateX(-50%); }
        .tl-item { display:flex; justify-content:flex-end; padding-right:calc(50% + 2rem); margin-bottom:1.75rem; position:relative; }
        .tl-item.right { justify-content:flex-start; padding-right:0; padding-left:calc(50% + 2rem); }
        .tl-dot { position:absolute; left:50%; top:1.1rem; width:14px; height:14px; background:#007bff; border-radius:50%; transform:translateX(-50%); border:3px solid white; box-shadow:0 0 0 3px #007bff; }
        .tl-card { background:white; border-radius:12px; padding:1.1rem 1.4rem; border:1px solid #e2e8f0; box-shadow:0 4px 12px rgba(0,0,0,0.05); max-width:400px; width:100%; transition:all 0.3s; }
        .tl-card:hover { transform:translateY(-4px); box-shadow:0 10px 25px rgba(0,123,255,0.15); border-color:#007bff; }
        .tl-year { display:inline-block; background:linear-gradient(135deg,#007bff,#ccccff); color:white; font-weight:800; font-size:0.8rem; padding:2px 12px; border-radius:20px; margin-bottom:0.5rem; }
        .tl-card h3 { font-size:1rem; font-weight:700; color:#007bff; margin-bottom:0.35rem; }
        .tl-card p { font-size:0.88rem; color:#475569; line-height:1.6; }

        /* VM cards */
        .vm-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1.1rem; }
        .vm-card { background:white; border-radius:14px; padding:1.5rem; border:1px solid #e2e8f0; box-shadow:0 4px 12px rgba(0,0,0,0.05); transition:all 0.3s; }
        .vm-card:hover { transform:translateY(-4px); box-shadow:0 10px 25px rgba(0,123,255,0.12); border-color:#007bff; }
        .vm-icon { font-size:2rem; margin-bottom:0.75rem; }
        .vm-card h3 { font-size:1rem; font-weight:700; color:#007bff; margin-bottom:0.5rem; }
        .vm-card p { font-size:0.88rem; color:#475569; line-height:1.6; }

        /* Dark mode */
        body[data-theme="dark"] { background:#0f1115; color:#e2e8f0; }
        body[data-theme="dark"] .navbar { background:linear-gradient(135deg,#0d5f5a 0%,#0f766e 100%); }
        body[data-theme="dark"] .hero { background:linear-gradient(135deg,#0d5f5a 0%,#0f766e 100%); }
        body[data-theme="dark"] .intro-card,
        body[data-theme="dark"] .stat-card,
        body[data-theme="dark"] .tl-card,
        body[data-theme="dark"] .vm-card { background:#0f1724; border-color:#9f6540; box-shadow:none; }
        body[data-theme="dark"] .intro-card p,
        body[data-theme="dark"] .tl-card p,
        body[data-theme="dark"] .vm-card p { color:#94a3b8; }
        body[data-theme="dark"] .section-heading { color:#e2e8f0; }
        body[data-theme="dark"] .stat-card .label { color:#94a3b8; }

        footer { background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%); color:#cbd5e1; padding:2rem 1rem; text-align:center; font-size:0.875rem; }

        @media(max-width:768px) {
            .timeline::before { left:16px; }
            .tl-item, .tl-item.right { padding-right:0; padding-left:3rem; justify-content:flex-start; }
            .tl-dot { left:16px; }
            .intro-card { flex-direction:column; text-align:center; }
        }
    </style>
</head>
<body>

<nav class="navbar">
  <div class="container">
    <h1>
      <img src="assets/images/team/logo.png" alt="Logo" class="logo-img" onerror="this.style.display='none'">
      
      Online Student Clearance System
    </h1>
    
    <div class="nav-links">
            <button id="themeToggle" title="Toggle theme" aria-pressed="false">🌞</button>
      <a href="index.php">Home</a>
      <a href="history.php">History</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <h2>🏛️ Bule Hora University History</h2>
    <p>A journey of academic excellence and community development</p>
  </div>
</section>

<div class="page-body">

    <!-- Intro -->
    <div class="intro-card" data-aos="fade-up">
        <div class="intro-logo">
            <img src="assets/images/team/logo.png" alt="BHU Logo" onerror="this.parentNode.innerHTML='🏛️'">
        </div>
        <p>
          Bule Hora University, established in 2011/2012 as a public university, is one of the newly
          established third-generation universities in Ethiopia. It is categorized as a comprehensive 
          university, focusing on teaching and learning, conducting research, and delivering community 
          services or engagement. Located in the southern part of Ethiopia, specifically in the West
           Guji Zone of the Oromia Regional State, Bule Hora University is situated in Bule Hora town,
            approximately 470 km south of Addis Ababa.
 The university offers 84 undergraduate programs, 75 master’s programs, and 19 Ph.D. programs.
  The student population has also increased substantially, with over 15,000 students currently enrolled:
   14,149 undergraduates, 138 post-basic students, 1,449 master’s students, and 156 Ph.D. students. This rapid growth in student numbers has led to a corresponding increase in faculty and staff across its two campuses.
Since its establishment, Bule Hora University has graduated more than 19,194 students. Recently, the university has implemented various reform measures to adapt to the fast-changing educational landscape and to meet both national and international educational standards. As a rapidly growing institution, Bule Hora University now comprises 7 colleges, 3 institutes, and 1 School of Law at its main campus.  </div>

    <!-- Stats -->
    <div class="section-heading" data-aos="fade-up"><i class="fas fa-chart-bar"></i> University at a Glance</div>
    <div class="stats-row">
        <div class="stat-card" data-aos="fade-up" data-aos-delay="50"><div class="icon">🎓</div><div class="number">2011</div><div class="label">Year Established (G.C.)</div></div>
        <div class="stat-card" data-aos="fade-up" data-aos-delay="100"><div class="icon">👨‍🎓</div><div class="number">20,000+</div><div class="label">Students Enrolled</div></div>
        <div class="stat-card" data-aos="fade-up" data-aos-delay="150"><div class="icon">👨‍🏫</div><div class="number">800+</div><div class="label">Academic Staff</div></div>
        <div class="stat-card" data-aos="fade-up" data-aos-delay="200"><div class="icon">🏫</div><div class="number">7</div><div class="label">Colleges & Faculties</div></div>
        <div class="stat-card" data-aos="fade-up" data-aos-delay="250"><div class="icon">📚</div><div class="number">50+</div><div class="label">Academic Programs</div></div>
    </div>

    <!-- Timeline -->
    <div class="section-heading" data-aos="fade-up"><i class="fas fa-history"></i> Historical Timeline</div>
    <div class="timeline">

        <div class="tl-item" data-aos="fade-right">
            <div class="tl-dot"></div>
            <div class="tl-card">
                <span class="tl-year">2011 G.C.</span>
                <h3>Foundation</h3>
                <p>Bule Hora University was officially established by the Ethiopian government to expand access to higher education in the West Guji Zone of Oromia.</p>
            </div>
        </div>

        <div class="tl-item right" data-aos="fade-left">
            <div class="tl-dot"></div>
            <div class="tl-card">
                <span class="tl-year">2012 G.C.</span>
                <h3>First Intake of Students</h3>
                <p>The university welcomed its first cohort of undergraduate students, beginning academic programs in science, technology, and social sciences.</p>
            </div>
        </div>

        <div class="tl-item" data-aos="fade-right">
            <div class="tl-dot"></div>
            <div class="tl-card">
                <span class="tl-year">2013 G.C.</span>
                <h3>Campus Development</h3>
                <p>Major infrastructure development began, including lecture halls, laboratories, library, dormitories, and administrative buildings.</p>
            </div>
        </div>

        <div class="tl-item right" data-aos="fade-left">
            <div class="tl-dot"></div>
            <div class="tl-card">
                <span class="tl-year">2014 G.C.</span>
                <h3>Expansion of Programs</h3>
                <p>New departments and colleges were added, expanding offerings in engineering, natural sciences, business, and health sciences.</p>
            </div>
        </div>

        <div class="tl-item" data-aos="fade-right">
            <div class="tl-dot"></div>
            <div class="tl-card">
                <span class="tl-year">2016 G.C.</span>
                <h3>Research & Community Engagement</h3>
                <p>The university launched research centers and community outreach programs, strengthening ties with local communities and contributing to regional development.</p>
            </div>
        </div>

        <div class="tl-item right" data-aos="fade-left">
            <div class="tl-dot"></div>
            <div class="tl-card">
                <span class="tl-year">2017 G.C.</span>
                <h3>Postgraduate Programs</h3>
                <p>Introduction of Masters programs in selected disciplines, marking a significant milestone in the university's academic growth.</p>
            </div>
        </div>

        <div class="tl-item" data-aos="fade-right">
            <div class="tl-dot"></div>
            <div class="tl-card">
                <span class="tl-year">2018 G.C.</span>
                <h3>Digital Transformation</h3>
                <p>The university embraced digital systems including online student clearance, e-learning platforms, and digital administrative management.</p>
            </div>
        </div>

        <div class="tl-item right" data-aos="fade-left">
            <div class="tl-dot"></div>
            <div class="tl-card">
                <span class="tl-year">2021 G.C.</span>
                <h3>Growing Excellence</h3>
                <p>Bule Hora University continues to grow with over 20,000 students, expanding research output, and strengthening national and international partnerships.</p>
            </div>
        </div>

    </div>

    <!-- Vision / Mission / Values -->
    <div class="section-heading" data-aos="fade-up"><i class="fas fa-bullseye"></i> Vision, Mission & Values</div>
    <div class="vm-grid">
        <div class="vm-card" data-aos="fade-up" data-aos-delay="50">
            <div class="vm-icon">🔭</div>
            <h3>Vision</h3>
            <p>To become a center of excellence in education, research, and community service that contributes to the sustainable development of Ethiopia and Africa.</p>
        </div>
        <div class="vm-card" data-aos="fade-up" data-aos-delay="100">
            <div class="vm-icon">🎯</div>
            <h3>Mission</h3>
            <p>To provide quality education, conduct relevant research, and engage with the community to produce competent, ethical, and innovative graduates.</p>
        </div>
        <div class="vm-card" data-aos="fade-up" data-aos-delay="150">
            <div class="vm-icon">⭐</div>
            <h3>Core Values</h3>
            <p>Excellence, integrity, innovation, inclusiveness, accountability, and commitment to community development guide everything we do.</p>
        </div>
        <div class="vm-card" data-aos="fade-up" data-aos-delay="200">
            <div class="vm-icon">📍</div>
            <h3>Location & Contact</h3>
            <p>Bule Hora, West Guji Zone, Oromia, Ethiopia. P.O. Box 144.<br>
            <a href="https://www.bhu.edu.et" target="_blank" style="color:#007bff;">www.bhu.edu.et</a> &nbsp;|&nbsp;
            <a href="mailto:main.registrar@bhu.edu.et" style="color:#007bff;">main.registrar@bhu.edu.et</a></p>
        </div>
    </div>

</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Bule Hora University. All rights reserved.</p>
    <p style="margin-top:0.4rem; color:#64748b;">Online Student Clearance System</p>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 80 });

    function setCookie(n, v, d) { const e = new Date(); e.setTime(e.getTime() + (d*24*60*60*1000)); document.cookie = n+"="+v+";path=/;expires="+e.toUTCString(); }
    function getCookie(n) { const p = document.cookie.split(';').map(s=>s.trim()); for(let x of p){ if(!x)continue; const[k,v]=x.split('='); if(k===n)return v; } return null; }

    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    function applyTheme(t) { body.setAttribute('data-theme', t); themeToggle.textContent = t==='dark' ? '🌙' : '🌞'; }
    (function(){ applyTheme(getCookie('theme') || 'light'); })();
    themeToggle.addEventListener('click', function() { const c = body.getAttribute('data-theme'); const n = c==='dark'?'light':'dark'; applyTheme(n); setCookie('theme', n, 365); });
</script>
</body>
</html>
