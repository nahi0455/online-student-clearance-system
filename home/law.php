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

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Clearance Rules & Regulations | Student Portal</title>
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

        @keyframes slideInFromTop {
            0% { opacity: 0; transform: translateY(-50px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Fix sidebar background to be solid */
        .modern-sidebar {
            background: linear-gradient(180deg, #A0522D 0%, #8B5A2B 100%) !important;
            backdrop-filter: none !important;
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
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: var(--glass-shadow);
            animation: slideInFromTop 0.8s ease-out;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(139, 90, 43, 0.1), transparent);
            transition: var(--transition);
        }

        .page-header:hover::before {
            left: 100%;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--university-primary);
            margin-bottom: 16px;
            text-shadow: 0 2px 4px rgba(139, 90, 43, 0.2);
        }

        .page-header .subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0;
        }

        .rules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .rule-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 28px;
            box-shadow: var(--glass-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        .rule-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(139, 90, 43, 0.05), transparent);
            transition: var(--transition);
        }

        .rule-card:hover::before {
            left: 100%;
        }

        .rule-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 50px rgba(139, 90, 43, 0.25);
            border-color: var(--university-primary-light);
        }

        .rule-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--glass-border);
        }

        .rule-number {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 18px;
            margin-right: 16px;
            box-shadow: var(--shadow-md);
            animation: bounceIn 0.8s ease-out;
        }

        .rule-title {
            flex: 1;
        }

        .rule-title h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--university-primary);
            margin: 0 0 4px 0;
        }

        .rule-title .department {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .criteria-list {
            margin-bottom: 20px;
        }

        .criteria-list h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .criteria-list h4 i {
            margin-right: 8px;
            color: var(--info-color);
        }

        .criteria-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
            padding: 8px 0;
            transition: var(--transition);
        }

        .criteria-item:hover {
            transform: translateX(4px);
        }

        .criteria-item i {
            color: var(--university-accent);
            margin-right: 12px;
            margin-top: 2px;
            font-size: 12px;
        }

        .criteria-text {
            flex: 1;
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .approval-condition {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            box-shadow: var(--shadow-sm);
            animation: pulse 2s ease-in-out infinite;
        }

        .approval-condition i {
            margin-right: 12px;
            font-size: 18px;
        }

        .final-approval {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 3px solid var(--university-primary);
            border-radius: var(--border-radius);
            padding: 32px;
            text-align: center;
            box-shadow: var(--glass-shadow);
            margin-bottom: 32px;
            animation: bounceIn 1s ease-out;
            position: relative;
            overflow: hidden;
        }

        .final-approval::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(139, 90, 43, 0.1), transparent);
            transform: rotate(45deg);
            transition: var(--transition);
            opacity: 0;
        }

        .final-approval:hover::before {
            opacity: 1;
            animation: shimmer 2s ease-in-out;
        }

        @keyframes shimmer {
            0% { transform: rotate(45deg) translateX(-100%); }
            100% { transform: rotate(45deg) translateX(100%); }
        }

        .final-approval h2 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--university-primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .final-approval h2 i {
            margin-right: 12px;
            font-size: 2.2rem;
        }

        .final-approval .description {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .final-requirements {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .final-requirement {
            background: rgba(139, 90, 43, 0.1);
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--university-primary-light);
            transition: var(--transition);
        }

        .final-requirement:hover {
            background: rgba(139, 90, 43, 0.15);
            transform: translateY(-2px);
        }

        .final-requirement i {
            color: var(--university-primary);
            margin-right: 8px;
        }

        .important-note {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            color: white;
            padding: 24px;
            border-radius: var(--border-radius);
            margin-bottom: 32px;
            box-shadow: var(--shadow-md);
            animation: fadeInUp 0.8s ease-out;
        }

        .important-note h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .important-note h3 i {
            margin-right: 12px;
            font-size: 1.5rem;
        }

        .important-note p {
            margin: 0;
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
            
            .rules-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .final-requirements {
                grid-template-columns: 1fr;
            }
        }

        /* Staggered animations */
        .rule-card:nth-child(1) { animation-delay: 0.1s; }
        .rule-card:nth-child(2) { animation-delay: 0.2s; }
        .rule-card:nth-child(3) { animation-delay: 0.3s; }
        .rule-card:nth-child(4) { animation-delay: 0.4s; }
        .rule-card:nth-child(5) { animation-delay: 0.5s; }
        .rule-card:nth-child(6) { animation-delay: 0.6s; }
        .rule-card:nth-child(7) { animation-delay: 0.7s; }
        .rule-card:nth-child(8) { animation-delay: 0.8s; }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fa fa-gavel"></i> Student Clearance Rules & Regulations</h1>
            <p class="subtitle"> Complete Guide to Departmental Approval Criteria</p>
        </div>

        <!-- Important Note -->
        <div class="important-note">
            <h3><i class="fa fa-exclamation-triangle"></i> Important Notice</h3>
            <p>A student must satisfy ALL departmental criteria before the final registrar stamp is issued. Each department has specific requirements that must be fulfilled for clearance approval.</p>
        </div>

        <!-- Rules Grid -->
        <div class="rules-grid">
            <!-- Department Head -->
            <div class="rule-card">
                <div class="rule-header">
                    <div class="rule-number">1</div>
                    <div class="rule-title">
                        <h3>Department Head</h3>
                        <div class="department">Academic Department</div>
                    </div>
                </div>
                
                <div class="criteria-list">
                    <h4><i class="fa fa-list-ul"></i> Approval Criteria:</h4>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">Student has completed all academic requirements</div>
                    </div>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">Departmental property (if any) returned</div>
                    </div>
                </div>
                
                <div class="approval-condition">
                    <i class="fa fa-check-circle"></i>
                    <span>Approve if: All academic obligations are fulfilled</span>
                </div>
            </div>

            <!-- Library -->
            <div class="rule-card">
                <div class="rule-header">
                    <div class="rule-number">2</div>
                    <div class="rule-title">
                        <h3>Library</h3>
                        <div class="department">Library Services</div>
                    </div>
                </div>
                
                <div class="criteria-list">
                    <h4><i class="fa fa-list-ul"></i> Approval Criteria:</h4>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">No borrowed books pending</div>
                    </div>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">No overdue fines</div>
                    </div>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">Library ID/account cleared</div>
                    </div>
                </div>
                
                <div class="approval-condition">
                    <i class="fa fa-check-circle"></i>
                    <span>Approve if: Library account balance = 0</span>
                </div>
            </div>

            <!-- Bookstore -->
            <div class="rule-card">
                <div class="rule-header">
                    <div class="rule-number">3</div>
                    <div class="rule-title">
                        <h3>Bookstore</h3>
                        <div class="department">University Bookstore</div>
                    </div>
                </div>
                
                <div class="criteria-list">
                    <h4><i class="fa fa-list-ul"></i> Approval Criteria:</h4>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">All university-issued books/materials returned</div>
                    </div>
                </div>
                
                <div class="approval-condition">
                    <i class="fa fa-check-circle"></i>
                    <span>Approve if: No outstanding materials or payments</span>
                </div>
            </div>

            <!-- Dormitory -->
            <div class="rule-card">
                <div class="rule-header">
                    <div class="rule-number">4</div>
                    <div class="rule-title">
                        <h3>Dormitory</h3>
                        <div class="department">Housing Services</div>
                    </div>
                </div>
                
                <div class="criteria-list">
                    <h4><i class="fa fa-list-ul"></i> Approval Criteria:</h4>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">Dorm room properly vacated</div>
                    </div>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">No property damage</div>
                    </div>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">All dormitory keys returned</div>
                    </div>
                </div>
                
                <div class="approval-condition">
                    <i class="fa fa-check-circle"></i>
                    <span>Approve if: Room inspection passed</span>
                </div>
            </div>

            <!-- Cafeteria -->
            <div class="rule-card">
                <div class="rule-header">
                    <div class="rule-number">5</div>
                    <div class="rule-title">
                        <h3>Cafeteria</h3>
                        <div class="department">Food Services</div>
                    </div>
                </div>
                
                <div class="criteria-list">
                    <h4><i class="fa fa-list-ul"></i> Approval Criteria:</h4>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">Cafeteria card/account settled</div>
                    </div>
                </div>
                
                <div class="approval-condition">
                    <i class="fa fa-check-circle"></i>
                    <span>Approve if: No outstanding cafeteria balance</span>
                </div>
            </div>

            <!-- Sport Master -->
            <div class="rule-card">
                <div class="rule-header">
                    <div class="rule-number">6</div>
                    <div class="rule-title">
                        <h3>Sport Master</h3>
                        <div class="department">Sports Office</div>
                    </div>
                </div>
                
                <div class="criteria-list">
                    <h4><i class="fa fa-list-ul"></i> Approval Criteria:</h4>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">All sports equipment returned</div>
                    </div>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">No unpaid sports-related penalties</div>
                    </div>
                </div>
                
                <div class="approval-condition">
                    <i class="fa fa-check-circle"></i>
                    <span>Approve if: No sports assets assigned to the student</span>
                </div>
            </div>

            <!-- Student Dean -->
            <div class="rule-card">
                <div class="rule-header">
                    <div class="rule-number">7</div>
                    <div class="rule-title">
                        <h3>Student Dean</h3>
                        <div class="department">Student Affairs</div>
                    </div>
                </div>
                
                <div class="criteria-list">
                    <h4><i class="fa fa-list-ul"></i> Approval Criteria:</h4>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">No disciplinary cases</div>
                    </div>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">All disciplinary penalties resolved</div>
                    </div>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">Student conduct record cleared</div>
                    </div>
                </div>
                
                <div class="approval-condition">
                    <i class="fa fa-check-circle"></i>
                    <span>Approve if: Student has a clean disciplinary record</span>
                </div>
            </div>

            <!-- Campus Police -->
            <div class="rule-card">
                <div class="rule-header">
                    <div class="rule-number">8</div>
                    <div class="rule-title">
                        <h3>Campus Police</h3>
                        <div class="department">Security Office</div>
                    </div>
                </div>
                
                <div class="criteria-list">
                    <h4><i class="fa fa-list-ul"></i> Approval Criteria:</h4>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">No security violations</div>
                    </div>
                    <div class="criteria-item">
                        <i class="fa fa-circle"></i>
                        <div class="criteria-text">No unresolved incident reports</div>
                    </div>
                </div>
                
                <div class="approval-condition">
                    <i class="fa fa-check-circle"></i>
                    <span>Approve if: No active security case</span>
                </div>
            </div>
        </div>

        <!-- Final Approval Section -->
        <div class="final-approval">
            <h2><i class="fa fa-stamp"></i> Registrar - Final Approval & Stamp</h2>
            <p class="description">The registrar provides the final stamp only after ALL departmental clearances are completed and verified.</p>
            
            <div class="final-requirements">
                <div class="final-requirement">
                    <i class="fa fa-check-square"></i>
                    All 8 departments approved
                </div>
                <div class="final-requirement">
                    <i class="fa fa-id-card"></i>
                    Student identity verified
                </div>
                <div class="final-requirement">
                    <i class="fa fa-sign-out"></i>
                    Exit status confirmed
                </div>
            </div>
            
            <div class="approval-condition" style="margin-top: 24px; animation: pulse 3s ease-in-out infinite;">
                <i class="fa fa-trophy"></i>
                <span>Final Approval: All departmental clearances are completed</span>
            </div>
        </div>
    </div>

    <script src="../js/jquery-2.1.1.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    
    <script>
        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);

        // Observe all rule cards
        document.querySelectorAll('.rule-card').forEach(card => {
            observer.observe(card);
        });

        console.log(' Student Clearance Rules & Regulations page loaded successfully!');
    </script>
</body>
</html>
