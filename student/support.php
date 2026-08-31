<?php
session_start();
error_reporting(1);
include('../connect.php');

if (empty($_SESSION['matric_no'])) {
    header("Location: ../login student/login.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ticket'])) {
    $subject = trim($_POST['subject']);
    $description = trim($_POST['description']);
    
    if (!empty($subject) && !empty($description)) {
        $message = 'Support ticket submitted successfully!';
    } else {
        $error = 'Please fill in all required fields.';
    }
}

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Help & Support | Student Portal</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(-45deg, #D2B48C, #f8fafc, #CD853F, #ffffff); background-size: 400% 400%; animation: gradientShift 15s ease infinite; }
        @keyframes gradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        
        /* Fix sidebar background to be solid like profile/documents pages */
        .modern-sidebar {
            background: linear-gradient(180deg, #A0522D 0%, #8B5A2B 100%) !important;
            backdrop-filter: none !important;
        }
        
        .main-content { margin-left: 280px; padding: 20px; min-height: 100vh; }
        .support-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(139, 90, 43, 0.1); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 8px 32px rgba(139, 90, 43, 0.1); }
        .contact-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .contact-card { background: rgba(255, 255, 255, 0.95); border: 1px solid rgba(139, 90, 43, 0.1); border-radius: 16px; padding: 24px; text-align: center; }
        .contact-icon { width: 64px; height: 64px; background: linear-gradient(135deg, #8B5A2B, #A0522D); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white; font-size: 24px; }
        .btn-primary { background: linear-gradient(135deg, #8B5A2B, #A0522D); border: none; border-radius: 12px; padding: 12px 24px; }
        .form-control { border: 2px solid rgba(139, 90, 43, 0.1); border-radius: 12px; padding: 12px 16px; }
        .alert-success { background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 12px; padding: 16px 20px; }
        .alert-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border-radius: 12px; padding: 16px 20px; }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <div class="support-card">
            <h1><i class="fa fa-question-circle"></i> Help & Support</h1>
            <p class="text-muted">Get assistance with your clearance process and university services</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo e($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="contact-grid">
            <div class="contact-card">
                <div class="contact-icon"><i class="fa fa-phone"></i></div>
                <h5>Phone Support</h5>
                <p class="text-muted">Call us for immediate assistance</p>
                <strong>tel:+251464430232</strong>
            </div>
            <div class="contact-card">
                <div class="contact-icon"><i class="fa fa-envelope"></i></div>
                <h5>Email Support</h5>
                <p class="text-muted">Send us your questions</p>
                <strong>main.registrar@bhu.edu.et</strong>
            </div>
            <div class="contact-card">
                <div class="contact-icon"><i class="fa fa-clock-o"></i></div>
                <h5>Office Hours</h5>
                <p class="text-muted">Visit us in person</p>
                <strong>
Mon–Fri: 8:00 AM - 5:00 PM
Saturday: 8:00 AM - 12:00 PM
Sunday: Closed</strong>
            </div>
            <div class="contact-card">
                <div class="contact-icon"><i class="fa fa-map-marker"></i></div>
                <h5>Location</h5>
                <p class="text-muted">Student Services Office</p>
                <strong>Main Campus, registrar Building</strong>
            </div>
        </div>

        <div class="support-card">
            <h3><i class="fa fa-ticket"></i> Submit Support Ticket</h3>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <input type="text" class="form-control" id="subject" name="subject" required placeholder="Brief description of your issue">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category">
                                <option value="clearance">Clearance Issues</option>
                                <option value="technical">Technical Support</option>
                                <option value="account">Account Issues</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required placeholder="Please provide detailed information about your issue..."></textarea>
                </div>
                <button type="submit" name="submit_ticket" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Submit Ticket</button>
            </form>
        </div>

        <div class="support-card">
            <h3><i class="fa fa-question-circle"></i> Frequently Asked Questions</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5><strong>Q: How do I check my clearance status?</strong></h5>
                    <p>A: Visit the "Clearance Status" page from the sidebar menu to see all department approvals.</p>
                    <h5><strong>Q: What documents do I need?</strong></h5>
                    <p>A: Required documents include student ID, library clearance form, and fee payment receipts.</p>
                    <h5><strong>Q: How long does clearance take?</strong></h5>
                    <p>A: The process typically takes 3-5 business days once all documents are submitted.</p>
                </div>
                <div class="col-md-6">
                    <h5><strong>Q: Can I download my certificate?</strong></h5>
                    <p>A: Yes, once all departments approve, you can download your certificate from the dashboard.</p>
                    <h5><strong>Q: What if my clearance is rejected?</strong></h5>
                    <p>A: Contact the specific department to understand the reason and required steps.</p>
                    <h5><strong>Q: How do I reset my password?</strong></h5>
                    <p>A: Reset your password from the "My Profile" page by entering your current password.</p>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/jquery-2.1.1.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>
