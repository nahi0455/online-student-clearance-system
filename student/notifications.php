<?php
session_start();
error_reporting(1);
include('../connect.php');

// Access control
if (empty($_SESSION['matric_no'])) {
    header("Location: ../login student/login.php");
    exit();
}

$matric_no = $_SESSION['matric_no'];

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $notification_id = (int)$_POST['notification_id'];
    // In a real system, you'd update the notification status
    // For now, we'll just simulate it
}

// Get notifications (simulated data since we don't have a notifications table)
$notifications = [
    [
        'id' => 1,
        'title' => 'Clearance Request Approved',
        'message' => 'Your clearance request for the Library department has been approved.',
        'type' => 'success',
        'date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'read' => false,
        'icon' => 'check-circle'
    ],
    [
        'id' => 2,
        'title' => 'Document Submission Required',
        'message' => 'Please submit your final project report to the Department office by Friday.',
        'type' => 'warning',
        'date' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'read' => false,
        'icon' => 'exclamation-triangle'
    ],
    [
        'id' => 3,
        'title' => 'Fee Payment Reminder',
        'message' => 'Your tuition fee payment is due in 3 days. Please visit the Bursary office.',
        'type' => 'info',
        'date' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'read' => true,
        'icon' => 'info-circle'
    ],
    [
        'id' => 4,
        'title' => 'System Maintenance Notice',
        'message' => 'The student portal will be under maintenance on Sunday from 2:00 AM to 6:00 AM.',
        'type' => 'info',
        'date' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'read' => true,
        'icon' => 'cog'
    ],
    [
        'id' => 5,
        'title' => 'Clearance Status Update',
        'message' => 'Your Sports department clearance is now pending review. Expected completion: 2 business days.',
        'type' => 'info',
        'date' => date('Y-m-d H:i:s', strtotime('-1 week')),
        'read' => true,
        'icon' => 'clock-o'
    ]
];

// Try to get real notifications from database if table exists
try {
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE recipient_matric IS NULL OR recipient_matric = ? ORDER BY created_at DESC LIMIT 20");
    if ($stmt) {
        $stmt->bind_param('s', $matric_no);
        $stmt->execute();
        $result = $stmt->get_result();
        $real_notifications = [];
        while ($row = $result->fetch_assoc()) {
            $real_notifications[] = [
                'id' => $row['id'],
                'title' => $row['subject'],
                'message' => $row['message'],
                'type' => 'info',
                'date' => $row['created_at'],
                'read' => false,
                'icon' => 'bell'
            ];
        }
        if (!empty($real_notifications)) {
            $notifications = array_merge($real_notifications, $notifications);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Table doesn't exist, use simulated data
}

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    return date('M j, Y', strtotime($datetime));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Notifications | Student Portal</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
    
    <style>
        :root {
            --university-primary: #8B5A2B;
            --university-primary-dark: #A0522D;
            --university-primary-light: #D2B48C;
            --university-accent: #CD853F;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-radius: 16px;
            --shadow-sm: 0 2px 8px rgba(139, 90, 43, 0.1);
            --shadow-md: 0 4px 16px rgba(139, 90, 43, 0.15);
            --shadow-lg: 0 8px 30px rgba(139, 90, 43, 0.2);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(139, 90, 43, 0.1);
            --glass-shadow: 0 8px 32px rgba(139, 90, 43, 0.1);
        }

        body {
            background: linear-gradient(-45deg, var(--university-primary-light), #f8fafc, var(--university-accent), #ffffff);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            color: var(--text-primary);
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .main-content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }

        .page-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--glass-shadow);
            animation: slideInFromTop 0.8s ease-out;
        }

        .notification-filters {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 16px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 2px solid var(--glass-border);
            border-radius: 20px;
            background: transparent;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn.active {
            background: var(--university-primary);
            border-color: var(--university-primary);
            color: white;
        }

        .filter-btn:hover {
            border-color: var(--university-primary);
            color: var(--university-primary);
        }

        .notification-item {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: var(--glass-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .notification-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(139, 90, 43, 0.1), transparent);
            transition: var(--transition);
        }

        .notification-item:hover::before {
            left: 100%;
        }

        .notification-item:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 12px 40px rgba(139, 90, 43, 0.2);
        }

        .notification-item.unread {
            border-left: 4px solid var(--university-primary);
            background: linear-gradient(135deg, rgba(139, 90, 43, 0.05), var(--glass-bg));
        }

        .notification-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 12px;
        }

        .notification-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            flex-shrink: 0;
        }

        .notification-icon.success {
            background: linear-gradient(135deg, var(--success-color), #059669);
        }

        .notification-icon.warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }

        .notification-icon.info {
            background: linear-gradient(135deg, var(--info-color), #2563eb);
        }

        .notification-icon.danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .notification-message {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .notification-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .notification-time {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .notification-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 4px 8px;
            border: 1px solid var(--glass-border);
            border-radius: 6px;
            background: transparent;
            color: var(--text-secondary);
            font-size: 12px;
            cursor: pointer;
            transition: var(--transition);
        }

        .action-btn:hover {
            background: var(--university-primary);
            border-color: var(--university-primary);
            color: white;
        }

        .unread-badge {
            background: var(--university-primary);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .stat-number {
            font-size: 24px;
            font-weight: 800;
            color: var(--university-primary);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @keyframes slideInFromTop {
            0% { opacity: 0; transform: translateY(-30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            
            .notification-filters {
                flex-direction: column;
            }
            
            .notification-header {
                flex-direction: column;
                gap: 12px;
            }
            
            .notification-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">Notifications</h1>
                    <p class="text-muted mb-0">Stay updated with important announcements and updates</p>
                </div>
                <div>
                    <a href="../index.php" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($notifications, function($n) { return !$n['read']; })); ?></div>
                <div class="stat-label">Unread</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($notifications); ?></div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($notifications, function($n) { return $n['type'] === 'success'; })); ?></div>
                <div class="stat-label">Approvals</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($notifications, function($n) { return $n['type'] === 'warning'; })); ?></div>
                <div class="stat-label">Reminders</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="notification-filters">
            <button class="filter-btn active" onclick="filterNotifications('all')">
                <i class="fa fa-list"></i> All Notifications
            </button>
            <button class="filter-btn" onclick="filterNotifications('unread')">
                <i class="fa fa-circle"></i> Unread
            </button>
            <button class="filter-btn" onclick="filterNotifications('success')">
                <i class="fa fa-check"></i> Approvals
            </button>
            <button class="filter-btn" onclick="filterNotifications('warning')">
                <i class="fa fa-exclamation"></i> Reminders
            </button>
            <button class="filter-btn" onclick="filterNotifications('info')">
                <i class="fa fa-info"></i> Information
            </button>
        </div>

        <!-- Notifications List -->
        <div id="notifications-container">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <i class="fa fa-bell-slash"></i>
                    <h4>No Notifications</h4>
                    <p>You're all caught up! Check back later for new updates.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo !$notification['read'] ? 'unread' : ''; ?>" 
                         data-type="<?php echo $notification['type']; ?>" 
                         data-read="<?php echo $notification['read'] ? 'true' : 'false'; ?>">
                        <div class="notification-header">
                            <div class="notification-icon <?php echo $notification['type']; ?>">
                                <i class="fa fa-<?php echo $notification['icon']; ?>"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    <?php echo e($notification['title']); ?>
                                    <?php if (!$notification['read']): ?>
                                        <span class="unread-badge">New</span>
                                    <?php endif; ?>
                                </div>
                                <div class="notification-message">
                                    <?php echo e($notification['message']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="notification-meta">
                            <div class="notification-time">
                                <i class="fa fa-clock-o"></i>
                                <?php echo timeAgo($notification['date']); ?>
                            </div>
                            <div class="notification-actions">
                                <?php if (!$notification['read']): ?>
                                    <button class="action-btn" onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                        <i class="fa fa-check"></i> Mark Read
                                    </button>
                                <?php endif; ?>
                                <button class="action-btn" onclick="deleteNotification(<?php echo $notification['id']; ?>)">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/jquery-2.1.1.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script>
        // Add entrance animations
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification-item');
            notifications.forEach((notification, index) => {
                notification.style.animationDelay = `${index * 0.1}s`;
                notification.style.animation = 'fadeInUp 0.6s ease-out forwards';
            });
        });

        function filterNotifications(type) {
            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Filter notifications
            const notifications = document.querySelectorAll('.notification-item');
            notifications.forEach(notification => {
                const notificationType = notification.getAttribute('data-type');
                const isRead = notification.getAttribute('data-read') === 'true';
                
                let show = false;
                
                switch(type) {
                    case 'all':
                        show = true;
                        break;
                    case 'unread':
                        show = !isRead;
                        break;
                    default:
                        show = notificationType === type;
                        break;
                }
                
                if (show) {
                    notification.style.display = 'block';
                    notification.style.animation = 'fadeInUp 0.3s ease-out';
                } else {
                    notification.style.display = 'none';
                }
            });
        }

        function markAsRead(notificationId) {
            const notification = document.querySelector(`[data-id="${notificationId}"]`);
            if (notification) {
                notification.classList.remove('unread');
                notification.setAttribute('data-read', 'true');
                
                // Remove unread badge
                const badge = notification.querySelector('.unread-badge');
                if (badge) {
                    badge.remove();
                }
                
                // Remove mark read button
                const markReadBtn = notification.querySelector('.action-btn');
                if (markReadBtn && markReadBtn.textContent.includes('Mark Read')) {
                    markReadBtn.remove();
                }
                
                // Update stats
                updateStats();
            }
            
            // Show success message
            showToast('Notification marked as read', 'success');
        }

        function deleteNotification(notificationId) {
            if (confirm('Are you sure you want to delete this notification?')) {
                const notification = document.querySelector(`[data-id="${notificationId}"]`);
                if (notification) {
                    notification.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => {
                        notification.remove();
                        updateStats();
                    }, 300);
                }
                
                showToast('Notification deleted', 'info');
            }
        }

        function updateStats() {
            const allNotifications = document.querySelectorAll('.notification-item');
            const unreadNotifications = document.querySelectorAll('.notification-item.unread');
            
            // Update stat cards
            const statCards = document.querySelectorAll('.stat-number');
            if (statCards[0]) statCards[0].textContent = unreadNotifications.length;
            if (statCards[1]) statCards[1].textContent = allNotifications.length;
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} toast-notification`;
            toast.innerHTML = `<i class="fa fa-${type === 'success' ? 'check' : 'info'}"></i> ${message}`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                animation: slideInFromRight 0.3s ease-out;
                min-width: 300px;
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutToRight 0.3s ease-in';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Add CSS for toast animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInFromRight {
                0% { opacity: 0; transform: translateX(100%); }
                100% { opacity: 1; transform: translateX(0); }
            }
            @keyframes slideOutToRight {
                0% { opacity: 1; transform: translateX(0); }
                100% { opacity: 0; transform: translateX(100%); }
            }
            @keyframes fadeOut {
                0% { opacity: 1; transform: scale(1); }
                100% { opacity: 0; transform: scale(0.9); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>