<?php
// page.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Torrent Search UI</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3ece4;
            color: #444;
        }

        /* LAYOUT */
        .container {
            display: flex;
            height: 100%;
        }

        /* LEFT WARNING PANEL */
        .left-panel {
            width: 260px;
            background: #f7efe7;
            border-right: 2px solid #d9c0aa;
            padding: 20px;
        }

        .left-panel h3 {
            margin-top: 0;
            font-size: 18px;
            color: #5a3b1c;
        }

        .left-panel .warn-icon {
            width: 45px;
            display: block;
            margin: 10px auto;
        }

        .left-panel button {
            width: 100%;
            padding: 10px;
            background: #b07a50;
            border: none;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        .left-panel button:hover {
            background: #965f39;
        }

        .countdown-box {
            margin-top: 20px;
            padding: 10px;
            background: #fff6ee;
            border: 1px solid #d1b79b;
            border-radius: 5px;
            text-align: center;
        }

        .countdown {
            font-size: 22px;
            font-weight: bold;
            color: #cc0000;
        }

        /* MAIN CONTENT */
        .main {
            flex: 1;
            text-align: center;
            padding-bottom: 30px;
        }

        /* TOP BAR */
        .top-bar {
            background: #c7a082;
            padding: 8px 20px;
            display: flex;
            justify-content: space-between;
            color: #fff;
            font-size: 14px;
        }

        .red {
            color: #ff0000;
            font-weight: bold;
        }

        /* HEADER WARNING */
        .header-warning {
            background: #f7efe7;
            border-bottom: 2px solid #d1b79b;
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            justify-content: center;
        }

        .header-warning img {
            width: 40px;
        }

        .vpn-btn {
            background: #b07a50;
            color: #fff;
            border: none;
            padding: 8px 18px;
            cursor: pointer;
            border-radius: 4px;
        }

        .vpn-btn:hover {
            background: #965f39;
        }

        /* LOGO AREA */
        .logo-area {
            margin-top: 40px;
        }

        .logo {
            width: 200px;
            opacity: 0.85;
        }

        .title {
            font-size: 40px;
            margin: 10px 0;
            color: #5a3b1c;
        }

        /* NAV LINKS */
        .nav-links {
            margin: 20px 0;
            font-size: 18px;
        }

        .nav-links a {
            color: #734a26;
            text-decoration: none;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        /* SEARCH BOX */
        .search-box input {
            width: 350px;
            padding: 10px;
            border: 1px solid #c2b09a;
            border-radius: 4px;
        }

        .search-box button {
            padding: 10px 20px;
            background: #b07a50;
            border: none;
            color: white;
            border-radius: 4px;
            margin-left: 5px;
        }

        .search-box button:hover {
            background: #8e613d;
        }

        /* FILTERS */
        .filters {
            margin: 20px;
            font-size: 16px;
        }

        .filters label {
            margin: 0 10px;
        }

        /* FOOTER BUTTONS */
        .footer-buttons button {
            padding: 10px 20px;
            margin: 0 8px;
            background: #82603f;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .footer-buttons button:hover {
            background: #694c2f;
        }

        /* RESPONSIVE MOBILE */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left-panel {
                width: 100%;
                border-right: 0;
                border-bottom: 2px solid #d9c0aa;
            }

            .search-box input {
                width: 85%;
            }

            .nav-links {
                font-size: 16px;
            }

            .title {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

    <!-- TOP BAR -->
    <div class="top-bar">
        <div>Your IP: 197.xxx.xxx.xxx · Country: Ethiopia</div>
        <div>Offer ends in <span class="red" id="topTimer">01:01:28</span></div>
    </div>

<div class="container">

    <!-- LEFT PANEL -->
    <div class="left-panel">
        <h3 id="about">About</h3>
        <p>This platform streamlines student clearance across departments and units. It provides role-based dashboards for staff and an easy experience for students to track approvals.</p>
        <h3 id="contact" style="margin-top:25px;">Contact</h3>
        <ul style="text-align:left; font-size:14px; line-height:1.6;">
            <li>Email: info@university.edu</li>
            <li>Phone: +000-000-0000</li>
            <li>Address: University Campus, Admin Building</li>
        </ul>
    </div>

    <!-- MAIN PAGE -->
    <div class="main">
        <div class="logo-area">
            <img src="../images/logo.png" class="logo">
            <h1 class="title">Clearance Information</h1>
        </div>
        <div style="max-width:800px; margin:0 auto; text-align:left; line-height:1.7; font-size:16px;">
            <p>The online clearance process ensures academic and administrative obligations are fulfilled before final clearance. Each unit reviews and approves one-by-one.</p>
            <p>Units involved include: Department Head, Library, Bookstore, Dormitory, Cafeteria, Sport Master, Student Dean, Campus Police, and Registrar.</p>
            <p>Students log in to view status and print the final clearance letter once all approvals are complete.</p>
            <div style="margin-top:18px;">
                <a href="../login.php" style="padding:10px 16px; background:#2563eb; color:#fff; border-radius:6px; text-decoration:none;">Student Login</a>
                <a href="login.php" style="padding:10px 16px; background:#111827; color:#fff; border-radius:6px; text-decoration:none; margin-left:10px;">Admin Login</a>
            </div>
        </div>
    </div>

</div>

<!-- COUNTDOWN SCRIPT -->
<script></script>

</body>
</html>
