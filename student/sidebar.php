<?php
// Get student data directly from register table
$matric_no = $_SESSION['matric_no'] ?? '';
$student_name = $_SESSION['fullname'] ?? 'Student';
$dept = $_SESSION['dept'] ?? '';
$faculty = $_SESSION['faculty'] ?? '';
$student_photo = '../images/default-avatar.png'; // Default fallback

// Fetch photo directly from register table
if (!empty($matric_no)) {
    include_once('../connect.php');
    $stmt = $conn->prepare("SELECT photo, fullname FROM register WHERE matric_no = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $matric_no);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (!empty($row['photo'])) {
                // Fix photo path consistently
                $photo_path = $row['photo'];
                // Remove any existing ../ to normalize
                $photo_path = str_replace('../', '', $photo_path);
                // Add correct prefix for student directory
                $student_photo = '../' . $photo_path;
            }
            // Also get updated name from register table
            if (!empty($row['fullname'])) {
                $student_name = $row['fullname'];
            }
        }
        $stmt->close();
    }
}
?>

<div class="modern-sidebar">
    <!-- Brand Section -->
    <div class="sidebar-brand">
        <div class="brand-text">
            <h3>BULE HORA UNIVERSITY</h3>
            <p>Student Portal</p>
        </div>
    </div>

    <!-- Student Profile Section -->
    <div class="student-profile">
        <div class="profile-image-container">
            <?php 
            // Add cache busting parameter to force image refresh
            $image_url = htmlspecialchars($student_photo);
            if (strpos($image_url, '?') === false) {
                $image_url .= '?v=' . time();
            }
            ?>
            <img src="<?php echo $image_url; ?>" alt="Student Photo" class="profile-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="default-avatar" style="display: none;">
                <i class="fa fa-user"></i>
            </div>
            <div class="profile-status online"></div>
        </div>
        <div class="profile-info">
            <h4><?php echo htmlspecialchars($student_name); ?></h4>
            <p class="matric-no"><?php echo htmlspecialchars($matric_no); ?></p>
            <p class="department"><?php echo htmlspecialchars($dept); ?></p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa fa-dashboard"></i>
                    </div>
                    <span class="nav-text">Dashboard</span>
                    <div class="nav-indicator"></div>
                </a>
            </li>

             <li class="nav-item">
                <a href="law.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa fa-gavel"></i>
                    </div>
                    <span class="nav-text">University Law</span>
                    <div class="nav-indicator"></div>
                </a>
            </li>

            <li class="nav-item">
                <a href="profile.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa fa-user"></i>
                    </div>
                    <span class="nav-text">My Profile</span>
                    <div class="nav-indicator"></div>
                </a>
            </li>

            <li class="nav-item">
                <a href="Clearance_Status.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <span class="nav-text">Clearance Status</span>
                    <div class="nav-indicator"></div>
                </a>
            </li>


            <li class="nav-item">
                <a href="notifications.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa fa-bell"></i>
                    </div>
                    <span class="nav-text">Notifications</span>
                    <div class="nav-badge">3</div>
                    <div class="nav-indicator"></div>
                </a>
            </li>

            <li class="nav-item">
                <a href="support.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa fa-question-circle"></i>
                    </div>
                    <span class="nav-text">Help & Support</span>
                    <div class="nav-indicator"></div>
                </a>
            </li>
               
        </ul>
    </nav>

 

    <!-- Logout Section -->
    <div class="sidebar-footer">
        <a href="../login student/login.php" class="logout-btn">
            <div class="nav-icon">
                <i class="fa fa-sign-out"></i>
            </div>
            <span>Logout</span>
        </a>
    </div>
</div>

<style>
/* CSS Variables */
:root {
    --university-primary: #8B5A2B;
    --university-primary-dark: #A0522D;
    --university-primary-light: #D2B48C;
    --university-accent: #CD853F;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-radius: 16px;
    --shadow-sm: 0 2px 8px rgba(139, 90, 43, 0.1);
    --shadow-md: 0 4px 16px rgba(139, 90, 43, 0.15);
    --shadow-lg: 0 8px 30px rgba(139, 90, 43, 0.2);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Modern Student Sidebar Styling */
.modern-sidebar {
    width: 280px;
    min-height: 100vh;
    max-height: 100vh;
    background: linear-gradient(135deg, var(--university-primary-dark) 0%, var(--university-primary) 100%);
    color: white;
    padding: 0;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    box-shadow: var(--shadow-lg);
    overflow-y: auto;
    overflow-x: hidden;
    transition: var(--transition);
    display: flex;
    flex-direction: column;
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

.default-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    transition: var(--transition);
    animation: profilePulse 4s ease-in-out infinite;
}

.default-avatar:hover {
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
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.action-btn {
    width: 100%;
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
    font-size: 14px;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.action-btn:active {
    transform: translateY(0);
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
    text-decoration: none;
}

.logout-btn .nav-icon {
    margin-right: 12px;
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
}
</style>

<script>
// Quick Actions Functions
function printClearance() {
    const btn = event.target.closest('.action-btn');
    const originalIcon = btn.innerHTML;
    
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    
    setTimeout(() => {
        if (confirm('Open clearance certificate for printing?')) {
            window.open('../letter.php', '_blank');
        }
        btn.innerHTML = originalIcon;
    }, 1000);
}

function downloadPDF() {
    const btn = event.target.closest('.action-btn');
    const originalIcon = btn.innerHTML;
    
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    
    setTimeout(() => {
        // Simulate PDF download
        const link = document.createElement('a');
        link.href = '../letter.php';
        link.target = '_blank';
        link.click();
        
        btn.innerHTML = '<i class="fa fa-check"></i>';
        setTimeout(() => {
            btn.innerHTML = originalIcon;
        }, 2000);
    }, 1500);
}

function shareStatus() {
    const btn = event.target.closest('.action-btn');
    const originalIcon = btn.innerHTML;
    
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    
    setTimeout(() => {
        if (navigator.share) {
            navigator.share({
                title: 'My Clearance Status',
                text: 'Check out my clearance progress at Bule Hora University',
                url: window.location.href
            });
        } else {
            // Fallback for browsers without Web Share API
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Link copied to clipboard!');
            });
        }
        
        btn.innerHTML = '<i class="fa fa-check"></i>';
        setTimeout(() => {
            btn.innerHTML = originalIcon;
        }, 2000);
    }, 1000);
}

function refreshData() {
    const btn = event.target.closest('.action-btn');
    const originalIcon = btn.innerHTML;
    
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    
    setTimeout(() => {
        location.reload();
    }, 1500);
}

// Enhanced Navigation for Student Pages
document.addEventListener('DOMContentLoaded', function() {
    // Get current page
    const currentPage = window.location.pathname.split('/').pop();
    
    // Remove active class from all nav items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class based on current page
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        const linkPage = href.split('/').pop();
        
        if (currentPage === linkPage || 
            (currentPage === 'index.php' && href.includes('../index.php'))) {
            link.closest('.nav-item').classList.add('active');
        }
    });
    
    // Enhanced navigation interactions
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add click animation
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
        
        // Enhanced hover effects
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Update notification badge
    const updateNotificationBadge = () => {
        const badge = document.querySelector('.nav-badge');
        if (badge) {
            fetch('../get_notification_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(() => {
                    // Fallback
                    const count = Math.floor(Math.random() * 5) + 1;
                    badge.textContent = count;
                });
        }
    };
    
    updateNotificationBadge();
    setInterval(updateNotificationBadge, 30000);
});
</script>