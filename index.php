<?php
// Redirect to new student dashboard location
header("Location: student/index.php");
exit();
?>size: 14px;
  margin-bottom: 4px;
}

.notif-message { 
  color: #718096;
  font-size: 13px;
  line-height: 1.4;
}

.notif-meta { 
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  color: #a0aec0;
  margin-top: 8px;
}

.notif-badge { 
  padding: 4px 8px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--info-color), #3182ce);
  color: white;
  font-size: 10px;
  font-weight: 600;
}

/* Modern Navbar */
.modern-navbar {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(226, 232, 240, 0.5);
  border-radius: var(--border-radius);
  padding: 20px 30px;
  margin: 20px 0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: var(--transition);
}

body.dark-mode .modern-navbar {
  background: rgba(30, 41, 59, 0.95);
  border: 1px solid rgba(71, 85, 105, 0.5);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.modern-navbar:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

body.dark-mode .modern-navbar:hover {
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
}

.navbar-left, .navbar-right {
  display: flex;
  align-items: center;
  gap: 15px;
}

.navbar-left {
  flex: 1;
}

.navbar-right {
  justify-content: flex-end;
}

/* Theme Toggle Button */
.btn-theme-toggle {
  background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
  color: white;
  border: none;
  border-radius: 25px;
  padding: 12px 20px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: var(--transition);
  box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3);
  font-size: 14px;
}

.btn-theme-toggle:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(139, 90, 43, 0.4);
  color: white;
  background: linear-gradient(135deg, var(--university-primary-dark), var(--university-primary));
}

.btn-theme-toggle:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(139, 90, 43, 0.3);
}

body.dark-mode .btn-theme-toggle {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

body.dark-mode .btn-theme-toggle:hover {
  box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
  background: linear-gradient(135deg, #d97706, #f59e0b);
}

/* Enhanced Logout Button */
.btn-logout {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
  border: none;
  border-radius: 25px;
  padding: 12px 20px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: var(--transition);
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
  text-decoration: none;
  font-size: 14px;
}

.btn-logout:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
  color: white;
  text-decoration: none;
  background: linear-gradient(135deg, #dc2626, #b91c1c);
}

.btn-logout:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3);
}

body.dark-mode .btn-logout {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

body.dark-mode .btn-logout:hover {
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
  background: linear-gradient(135deg, #dc2626, #b91c1c);
}

/* Enhanced buttons with advanced animations */
.btn {
  border-radius: 12px;
  font-weight: 600;
  transition: var(--transition);
  box-shadow: var(--shadow-sm);
  position: relative;
  overflow: hidden;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: var(--transition);
}

.btn:hover::before {
  left: 100%;
}

.btn:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: var(--shadow-lg);
}

.btn:active {
  transform: translateY(0) scale(0.98);
  animation: none;
}

.btn-light {
  background: var(--glass-bg);
  border: 1px solid var(--glass-border);
  backdrop-filter: blur(20px);
  color: var(--text-primary);
}

.btn-light:hover {
  background: linear-gradient(135deg, var(--university-primary-light), rgba(255, 255, 255, 0.9));
  border-color: var(--university-primary);
  color: var(--university-primary-dark);
}

body.dark-mode .btn-light {
  background: var(--glass-bg);
  border: 1px solid var(--glass-border);
  color: var(--text-primary);
}

body.dark-mode .btn-light:hover {
  background: linear-gradient(135deg, var(--university-primary), rgba(30, 41, 59, 0.9));
  border-color: var(--university-primary-light);
  color: var(--university-primary-light);
}

.btn-outline-secondary {
  border-color: var(--border-color);
  color: var(--text-secondary);
}

body.dark-mode .btn-outline-secondary {
  border-color: var(--border-color);
  color: var(--text-secondary);
}

.btn-outline-secondary:hover {
  background: rgba(139, 90, 43, 0.1);
  border-color: var(--university-primary-light);
  color: var(--university-primary-dark);
  transform: translateY(-3px) scale(1.05) rotate(1deg);
}

body.dark-mode .btn-outline-secondary:hover {
  background: rgba(71, 85, 105, 0.2);
  border-color: var(--university-primary);
  color: var(--university-primary-light);
  transform: translateY(-3px) scale(1.05) rotate(-1deg);
}

/* Responsive design */
@media (max-width: 768px) {
  .notifications-tray { 
    right: 16px;
    left: 16px;
    width: auto;
    bottom: 16px;
  }
  
  .content {
    margin-left: 0;
    width: 100%;
    padding: 10px;
  }
  
  .modern-sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .modern-sidebar.active {
    transform: translateX(0);
  }
  
  .hero {
    flex-direction: column;
    text-align: center;
  }
}

/* Loading animations and micro-interactions */
.loading-shimmer {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

/* Enhanced page entrance animations */
.page-enter {
  animation: pageEnter 1s ease-out;
}

@keyframes pageEnter {
  0% { 
    opacity: 0; 
    transform: translateY(50px) scale(0.95);
    filter: blur(5px);
  }
  100% { 
    opacity: 1; 
    transform: translateY(0) scale(1);
    filter: blur(0);
  }
}

/* Staggered animation for multiple elements */
.stagger-animation:nth-child(1) { animation-delay: 0.1s; }
.stagger-animation:nth-child(2) { animation-delay: 0.2s; }
.stagger-animation:nth-child(3) { animation-delay: 0.3s; }
.stagger-animation:nth-child(4) { animation-delay: 0.4s; }
.stagger-animation:nth-child(5) { animation-delay: 0.5s; }

/* Interactive hover effects for text */
.interactive-text {
  transition: var(--transition);
  cursor: pointer;
}

.interactive-text:hover {
  color: var(--university-primary);
  transform: scale(1.05);
  text-shadow: 0 2px 8px rgba(139, 90, 43, 0.3);
}

/* Enhanced focus states */
*:focus {
  outline: 2px solid var(--university-primary);
  outline-offset: 2px;
  border-radius: 4px;
}

/* Smooth scroll behavior */
html {
  scroll-behavior: smooth;
}

/* Enhanced selection colors */
::selection {
  background: var(--university-primary-light);
  color: var(--university-primary-dark);
}

::-moz-selection {
  background: var(--university-primary-light);
  color: var(--university-primary-dark);
}

/* Parallax effect for background elements */
.parallax-bg {
  background-attachment: fixed;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

/* Enhanced tooltip animations */
[data-tooltip] {
  position: relative;
  cursor: pointer;
}

[data-tooltip]:hover::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  background: var(--university-primary-dark);
  color: white;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 12px;
  white-space: nowrap;
  z-index: 1000;
  animation: tooltipFadeIn 0.3s ease-out;
}

@keyframes tooltipFadeIn {
  0% { opacity: 0; transform: translateX(-50%) translateY(10px); }
  100% { opacity: 1; transform: translateX(-50%) translateY(0); }
}

/* Enhanced card stack effect */
.card-stack {
  position: relative;
}

.card-stack::before,
.card-stack::after {
  content: '';
  position: absolute;
  top: 4px;
  left: 4px;
  right: -4px;
  bottom: -4px;
  background: var(--glass-bg);
  border-radius: var(--border-radius);
  z-index: -1;
  opacity: 0.5;
  transition: var(--transition);
}

.card-stack::after {
  top: 8px;
  left: 8px;
  right: -8px;
  bottom: -8px;
  opacity: 0.3;
}

.card-stack:hover::before,
.card-stack:hover::after {
  opacity: 0.8;
  transform: rotate(1deg);
}

/* Magnetic button effect */
.magnetic-btn {
  transition: var(--transition);
  cursor: pointer;
}

.magnetic-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(139, 90, 43, 0.3);
}

/* Breathing animation for important elements */
.breathing {
  animation: breathing 4s ease-in-out infinite;
}

@keyframes breathing {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.02); }
}
/* Modern Student Sidebar Styling */
.modern-sidebar {
    width: 280px;
    min-height: 100vh;
    background: linear-gradient(180deg, var(--university-primary-dark) 0%, var(--university-primary) 100%);
    color: white;
    padding: 0;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    box-shadow: var(--shadow-lg);
    overflow-y: auto;
    transition: var(--transition);
}

/* Brand Section */
.sidebar-brand {
    padding: 24px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    background: rgba(0, 0, 0, 0.1);
}

.brand-logo-container {
    margin-bottom: 12px;
}

.brand-logo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: var(--transition);
    animation: profilePulse 3s ease-in-out infinite;
}

.brand-logo:hover {
    transform: scale(1.1);
    border-color: rgba(255, 255, 255, 0.8);
}

.brand-text h3 {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.brand-text p {
    font-size: 12px;
    margin: 4px 0 0 0;
    color: rgba(255, 255, 255, 0.8);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Student Profile Section */
.student-profile {
    padding: 24px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    position: relative;
}

.profile-image-container {
    position: relative;
    display: inline-block;
    margin-bottom: 16px;
}

.profile-image {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.3);
    transition: var(--transition);
    animation: profilePulse 4s ease-in-out infinite;
}

.profile-image:hover {
    transform: scale(1.1);
    border-color: rgba(255, 255, 255, 0.8);
}

.profile-status {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
}

.profile-status.online {
    background: #10b981;
    animation: pulse 2s ease-in-out infinite;
}

.profile-info h4 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 4px 0;
    color: white;
}

.profile-info .matric-no {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.8);
    margin: 0 0 2px 0;
    font-weight: 500;
}

.profile-info .department {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.6);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Navigation Menu */
.sidebar-nav {
    padding: 20px 0;
}

.nav-menu {
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-item {
    margin: 0 12px 8px 12px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    border-radius: 12px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: var(--transition);
}

.nav-link:hover::before {
    left: 100%;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.nav-item.active .nav-link {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.nav-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
    transition: var(--transition);
}

.nav-link:hover .nav-icon {
    transform: scale(1.2);
}

.nav-text {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
}

.nav-badge {
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 8px;
    animation: pulse 2s ease-in-out infinite;
}

.nav-indicator {
    width: 4px;
    height: 0;
    background: white;
    border-radius: 2px;
    transition: var(--transition);
    margin-left: 8px;
}

.nav-item.active .nav-indicator {
    height: 20px;
}

/* Quick Actions */
.quick-actions {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.quick-actions h5 {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0 0 16px 0;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.action-btn {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    position: relative;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Sidebar Footer */
.sidebar-footer {
    padding: 20px;
    margin-top: auto;
}

.logout-btn {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    border-radius: 12px;
    transition: var(--transition);
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.logout-btn:hover {
    background: rgba(239, 68, 68, 0.3);
    color: white;
    transform: translateX(4px);
}

.logout-btn .nav-icon {
    margin-right: 12px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .modern-sidebar.active {
        transform: translateX(0);
    }
}

/* Scrollbar Styling */
.modern-sidebar::-webkit-scrollbar {
    width: 6px;
}

.modern-sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.modern-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.modern-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Animations */
@keyframes profilePulse {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
    50% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Tooltip Styles */
[data-tooltip] {
    position: relative;
}

[data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: var(--university-primary-dark);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    animation: tooltipFadeIn 0.3s ease-out;
}

@keyframes tooltipFadeIn {
    0% { opacity: 0; transform: translateX(-50%) translateY(10px); }
    100% { opacity: 1; transform: translateX(-50%) translateY(0); }
}sss
</style>
</head>
<body>
<div class="layout">  

    <!-- LEFT SIDE BAR -->
    <?php include('student/sidebar.php'); ?>


    <!-- RIGHT MAIN AREA -->
    <div class="content container-fluid">

        <!-- navbar / top -->
        <div class="modern-navbar">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <div class="navbar-left">
                    <button id="theme-toggle" class="btn btn-theme-toggle">
                        <i class="fa fa-sun-o" id="theme-icon"></i>
                        <span id="theme-text">Light Mode</span>
                    </button>
                </div>
                <div class="navbar-right">
                    <a href="login student/login.php" class="btn btn-logout">
                        <i class="fa fa-sign-out"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($_SESSION['success'])) { ?>
        <div class="alert alert-success mt-2"><?php echo e($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); } ?>
        <?php if (!empty($_SESSION['error'])) { ?>
        <div class="alert alert-danger mt-2"><?php echo e($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); } ?>

        <?php
        $notifications = [];
        $ns = $conn->prepare("SELECT id, subject, message, created_at, recipient_matric FROM notifications WHERE recipient_matric IS NULL OR recipient_matric = ? ORDER BY id DESC LIMIT 6");
        if ($ns) {
            $ns->bind_param('s', $matric_no);
            if ($ns->execute()) {
                $resn = $ns->get_result();
                while($rown = $resn->fetch_assoc()) { $notifications[] = $rown; }
            }
            $ns->close();
        }
        ?>
        <div class="notifications-tray">
            <div class="notifications-header">Notifications <a href="#" style="font-size:12px;color:#6b7280;text-decoration:none" onclick="this.closest('.notifications-tray').style.display='none';return false;">Hide</a></div>
            <?php if (empty($notifications)) { ?>
                <div style="padding:12px; color:#64748b;">No notifications</div>
            <?php } else { foreach($notifications as $n) { 
                $isDirect = ($n['recipient_matric'] && $n['recipient_matric'] === $matric_no);
                $avatar = $isDirect ? $photo : 'Admin/images/logo.svg';
            ?>
                <div class="notif-item">
                    <img class="notif-avatar" src="<?php echo e($avatar); ?>" alt="avatar" onerror="this.src='Admin/dist/img/user1-128x128.jpg'">
                    <div class="notif-body">
                        <div class="notif-subject"><?php echo e($n['subject']); ?></div>
                        <div class="notif-message"><?php echo e($n['message']); ?></div>
                        <div class="notif-meta">
                            <span><?php echo e($n['created_at']); ?></span>
                            <span class="notif-badge"><?php echo $isDirect ? 'Direct' : 'General'; ?></span>
                        </div>
                    </div>
                </div>
            <?php } } ?>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="hero">
                    <img src="<?php echo e($photo); ?>" alt="Profile">
                    <div>
                        <div style="font-size:18px;font-weight:700"><?php echo e($fullname); ?></div>
                        <div style="font-size:13px;opacity:.9">Matric: <?php echo e($matric_no); ?> • Faculty: <?php echo e($faculty); ?> • Dept: <?php echo e($dept); ?></div>
                    </div>
                    <div style="margin-left:auto">
                        <?php if ($allCleared): ?>
                            <a href="letter.php" target="_blank" class="btn btn-light">Print Clearance Letter</a>
                        <?php else: ?>
                            <?php if (!$hasRequestedThisYear): ?>
                                <?php if ($clearance_window_open): ?>
                                    <form method="post" style="display:inline;">
                                        <button type="submit" name="btn_request_clearance" class="btn btn-light">Request Clearance</button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-light" disabled>Requests Closed</button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button type="button" class="btn btn-light" disabled>Requested for <?php echo $currentYear; ?></button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="ibox p-3" style="display:flex; justify-content:space-between; align-items:center;">
                    <div><strong>Clearance Window</strong>: <?php echo ($clearance_window_open ? 'Open' : 'Closed'); ?></div>
                    <div>Today: <?php echo htmlspecialchars($today); ?> • Day Status: <?php echo ($today_open === 1 ? 'On' : 'Off'); ?> • Global: <?php echo ($global_open === '1' ? 'Open' : 'Closed'); ?></div>
                </div>
            </div>
        </div>

        <div class="row g-3 metrics">
           
            <div class="col-md-3">
                <div class="ibox status">
                    <h5>Clearance Progress</h5>
                    <h3><?php echo $progressPct; ?>%</h3>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $progressPct; ?>%" aria-valuenow="<?php echo $progressPct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h3 class="mt-2">
                    <?php if ($allCleared): ?>
                        <span style="color:green"><i class="fa fa-check-circle"></i> Cleared</span>
                    <?php else: ?>
                        <span style="color:orange"><i class="fa fa-times-circle"></i> Pending</span>
                    <?php endif; ?>
                </h3>
                <?php if ($allCleared): ?>
                    <small><a href="letter.php" target="_blank">Print Clearance Letter</a></small>
                <?php endif; ?>
                </div>
            </div>
        </div>
    
        <!-- approvals grid -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Department Head</th>
                                <th class="text-center">Library</th>
                                <th class="text-center">Bookstore</th>
                                <th class="text-center">Dormitory</th>
                                <th class="text-center">Cafeteria</th>
                                <th class="text-center">Sport</th>
                                <th class="text-center">Dean</th>
                                <th class="text-center">Campus Police</th>
                                <th class="text-center">Registrar</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-center"><?php echo status_label($is_department_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_library_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_bookstore_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_dormitory_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_cafeteria_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_sport_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_dean_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_police_approved); ?></td>
                                <td class="text-center"><?php echo status_label($is_registrar_approved); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer include -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <?php include('footer.php'); ?>
            </div>
        </div>

    </div><!-- END MAIN CONTENT -->

</div><!-- END FLEX CONTAINER -->

<script src="js/jquery-2.1.1.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
// Theme Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');
    const body = document.body;
    
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply saved theme
    if (currentTheme === 'dark') {
        body.classList.add('dark-mode');
        themeIcon.className = 'fa fa-moon-o';
        themeText.textContent = 'Dark Mode';
    }
    
    // Theme toggle event listener
    themeToggle.addEventListener('click', function() {
        body.classList.toggle('dark-mode');
        
        if (body.classList.contains('dark-mode')) {
            themeIcon.className = 'fa fa-moon-o';
            themeText.textContent = 'Dark Mode';
            localStorage.setItem('theme', 'dark');
        } else {
            themeIcon.className = 'fa fa-sun-o';
            themeText.textContent = 'Light Mode';
            localStorage.setItem('theme', 'light');
        }
    });
    
    // Auto theme based on time (optional)
    const autoTheme = () => {
        const hour = new Date().getHours();
        const isDayTime = hour >= 6 && hour < 18;
        
        if (!localStorage.getItem('theme')) {
            if (isDayTime) {
                body.classList.remove('dark-mode');
                themeIcon.className = 'fa fa-sun-o';
                themeText.textContent = 'Light Mode';
            } else {
                body.classList.add('dark-mode');
                themeIcon.className = 'fa fa-moon-o';
                themeText.textContent = 'Dark Mode';
            }
        }
    };
    
    // Uncomment to enable auto theme switching
    // autoTheme();
});
</script>
</body>
</html>

