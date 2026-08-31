<?php
session_start();
error_reporting(0);
include('connect.php');
if(empty($_SESSION['matric_no'])) {   
    header("Location: login.php"); 
    exit;
}

$ID = $_SESSION["ID"];
$matric_no = $_SESSION["matric_no"];

$sql = "SELECT * FROM students WHERE matric_no='$matric_no'"; 
$result = $conn->query($sql);
$rowaccess = mysqli_fetch_array($result);

date_default_timezone_set('Africa/Lagos');
$current_date = date('Y-m-d H:i:s');

// Generate Scannable Barcode for certificate verification
$barcode_data = $rowaccess['matric_no'];

// Create a scannable barcode using a reliable service
function generateScannableBarcode($text) {
    // Use multiple barcode services for reliability
    $barcode_services = [
        "https://bwipjs-api.metafloor.com/?bcid=code128&text=" . urlencode($text) . "&scale=3&height=15&includetext=false&backgroundcolor=ffffff",
        "https://barcode.tec-it.com/barcode.ashx?data=" . urlencode($text) . "&code=Code128&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0",
        "https://api.qrserver.com/v1/create-barcode/?size=250x60&data=" . urlencode($text) . "&format=png&bgcolor=ffffff&color=000000&qzone=0&margin=0"
    ];
    
    // Simplified barcode container - centered, no extra text
    $barcode_html = '<div class="professional-barcode" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; background: white; border: none; padding: 0; margin: 0;">';
    
    // Try the first service (most reliable)
    $barcode_url = $barcode_services[0];
    $barcode_html .= '<img src="' . $barcode_url . '" alt="Scannable Barcode" style="max-width: 140px; max-height: 40px; display: block; border: none; background: white; margin: 0 auto;" class="scannable-barcode-img" onerror="this.onerror=null; this.src=\'' . $barcode_services[1] . '\'; setTimeout(() => { if(this.naturalWidth === 0) { this.src=\'' . $barcode_services[2] . '\'; } }, 2000);">';
    
    $barcode_html .= '</div>';
    
    return $barcode_html;
}

// approvals
$is_department_approved = isset($rowaccess['is_department_approved']) ? (string)$rowaccess['is_department_approved'] : "0";
$is_library_approved    = isset($rowaccess['is_library_approved']) ? (string)$rowaccess['is_library_approved'] : "0";
$is_bookstore_approved  = isset($rowaccess['is_bookstore_approved']) ? (string)$rowaccess['is_bookstore_approved'] : "0";
$is_cafeteria_approved  = isset($rowaccess['is_cafeteria_approved']) ? (string)$rowaccess['is_cafeteria_approved'] : "0";
$is_sport_approved      = isset($rowaccess['is_sport_approved']) ? (string)$rowaccess['is_sport_approved'] : "0";
$is_dean_approved       = isset($rowaccess['is_dean_approved']) ? (string)$rowaccess['is_dean_approved'] : "0";
$is_police_approved     = isset($rowaccess['is_police_approved']) ? (string)$rowaccess['is_police_approved'] : "0";
$is_registrar_approved  = isset($rowaccess['is_registrar_approved']) ? (string)$rowaccess['is_registrar_approved'] : "0";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Certificate - <?php echo htmlspecialchars($rowaccess['fullname']); ?></title>
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    
    <style>
        /* Professional Print-Only Certificate Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        body {
            font-family: 'Times New Roman', serif;
            background: white;
            color: #000;
            font-size: 12px;
            line-height: 1.4;
            padding: 0;
            margin: 0;
        }

        .print-certificate {
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            background: white;
            border: 3px solid #000;
            position: relative;
            overflow: hidden;
        }

        /* Decorative border */
        .print-certificate::before {
            content: '';
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 2px solid #8B5A2B;
            pointer-events: none;
        }

        /* Corner decorations */
        .corner-decoration {
            position: absolute;
            width: 15px;
            height: 15px;
            background: #8B5A2B;
            transform: rotate(45deg);
        }

        .corner-decoration.top-left { top: 15mm; left: 15mm; }
        .corner-decoration.top-right { top: 15mm; right: 15mm; }
        .corner-decoration.bottom-left { bottom: 15mm; left: 15mm; }
        .corner-decoration.bottom-right { bottom: 15mm; right: 15mm; }

        .university-header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 10px;
            display: block;
            object-fit: contain;
            border-radius: 50%;
            border: 2px solid #000;
            padding: 3px;
            background: white;
        }

        .university-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            background: #8B5A2B;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .university-name {
            font-size: 22px;
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .university-location {
            font-size: 14px;
            color: #333;
            font-style: italic;
            margin-bottom: 15px;
        }

        .certificate-title {
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            padding: 10px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            position: relative;
        }

        .certificate-title::before,
        .certificate-title::after {
            content: '✦';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #8B5A2B;
            font-size: 16px;
        }

        .certificate-title::before { left: 20px; }
        .certificate-title::after { right: 20px; }

        .student-info-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            padding: 15px;
            background: rgba(139, 90, 43, 0.05);
            border: 1px solid #8B5A2B;
        }

        .student-photo {
            width: 100px;
            height: 120px;
            border: 3px solid #000;
            object-fit: cover;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            border: 2px solid #000;
            padding: 3px;
            background: white;
        }

        .student-details {
            flex: 1;
            text-align: left;
        }

        .greeting-text {
            text-align: center;
            font-size: 14px;
            color: #000;
            margin: 15px 0;
            line-height: 1.5;
        }

        .student-name {
            font-weight: 700;
            color: #8B5A2B;
            text-transform: uppercase;
        }

        .clearance-statement {
            text-align: center;
            font-size: 13px;
            color: #333;
            margin-bottom: 15px;
            font-style: italic;
        }

        .clearance-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin: 15px 0;
        }

        .clearance-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: white;
            border: 2px solid #000;
            font-size: 11px;
        }

        .clearance-item.cleared {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.1);
        }

        .clearance-item.pending {
            border-color: #6b7280;
            background: rgba(107, 114, 128, 0.1);
        }

        .clearance-icon {
            font-size: 14px;
            width: 16px;
            text-align: center;
        }

        .clearance-icon.cleared {
            color: #10b981;
        }

        .clearance-icon.pending {
            color: #6b7280;
        }

        .clearance-department {
            font-weight: 600;
            color: #000;
            font-size: 11px;
        }

        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 15px 0;
            padding: 15px;
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid #000;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-weight: 700;
            color: #000;
            font-size: 11px;
        }

        .issue-date {
            text-align: center;
            font-size: 13px;
            color: #333;
            margin: 15px 0;
            padding: 10px;
            background: rgba(139, 90, 43, 0.05);
            border-left: 4px solid #8B5A2B;
        }

        .signature-section {
            display: flex;
            justify-content: space-around;
            align-items: end;
            margin: 25px 0 20px;
            gap: 20px;
        }

        .signature-box {
            text-align: center;
            flex: 1;
            max-width: 150px;
        }

        .signature-line {
            width: 100%;
            height: 60px;
            border: 2px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #000;
            margin-bottom: 8px;
            background: white;
            padding: 0;
        }

        .signature-img, .stamp-img {
            max-width: 100%;
            max-height: 50px;
            object-fit: contain;
            display: block;
        }

        .date-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
            width: 100%;
            text-align: center;
        }

        .date-label {
            font-size: 8px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
        }

        .date-value {
            font-size: 11px;
            font-weight: 700;
            color: #000;
        }

        .date-time {
            font-size: 9px;
            color: #333;
            font-weight: 500;
        }

        .signature-label {
            font-weight: 600;
            color: #000;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Print page settings */
        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .print-certificate {
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-certificate">
        <!-- Corner Decorations -->
        <div class="corner-decoration top-left"></div>
        <div class="corner-decoration top-right"></div>
        <div class="corner-decoration bottom-left"></div>
        <div class="corner-decoration bottom-right"></div>
        
        <!-- University Header -->
        <div class="university-header">
            <img src="images/logo.png" alt="University Logo" class="brand-logo" onerror="this.style.display='none'">
            <div class="university-logo">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <h1 class="university-name">Bule hora University</h1>
            <div class="university-location">Bule hora University, clearance system</div>
        </div>

        <!-- Certificate Title -->
        <div class="certificate-title">Student Clearance Certificate</div>

        <!-- Student Information Section -->
        <div class="student-info-section">
            <img src="<?php echo $rowaccess['photo'] ?: 'images/default-avatar.png'; ?>" 
                 alt="Student Photo" 
                 class="student-photo">
            
            <div class="student-details">
                <div class="greeting-text">
                    This is to certify that <span class="student-name"><?php echo htmlspecialchars($rowaccess['fullname']); ?></span> 
                    has successfully completed all clearance requirements and has been cleared by all relevant departments.
                </div>
            </div>
            
            <img src="<?php echo $qr_url; ?>" alt="Verification QR Code" class="qr-code">
        </div>

        <!-- Clearance Statement -->
        <div class="clearance-statement">
            The following departments have verified and approved the student's clearance:
        </div>

        <!-- Clearance Grid -->
        <div class="clearance-grid">
            <div class="clearance-item <?php echo ($is_department_approved=="1") ? 'cleared' : 'pending'; ?>">
                <i class="fa fa-check-circle clearance-icon <?php echo ($is_department_approved=="1") ? 'cleared' : 'pending'; ?>"></i>
                <span class="clearance-department">Department Head</span>
            </div>
            
            <div class="clearance-item <?php echo ($is_library_approved=="1") ? 'cleared' : 'pending'; ?>">
                <i class="fa fa-check-circle clearance-icon <?php echo ($is_library_approved=="1") ? 'cleared' : 'pending'; ?>"></i>
                <span class="clearance-department">University Library</span>
            </div>
            
            <div class="clearance-item <?php echo ($is_bookstore_approved=="1") ? 'cleared' : 'pending'; ?>">
                <i class="fa fa-check-circle clearance-icon <?php echo ($is_bookstore_approved=="1") ? 'cleared' : 'pending'; ?>"></i>
                <span class="clearance-department">University Bookstore</span>
            </div>
            
            <div class="clearance-item <?php echo (isset($rowaccess['is_dormitory_approved']) && $rowaccess['is_dormitory_approved']=="1") ? 'cleared' : 'pending'; ?>">
                <i class="fa fa-check-circle clearance-icon <?php echo (isset($rowaccess['is_dormitory_approved']) && $rowaccess['is_dormitory_approved']=="1") ? 'cleared' : 'pending'; ?>"></i>
                <span class="clearance-department">Student Dormitory</span>
            </div>
            
            <div class="clearance-item <?php echo ($is_cafeteria_approved=="1") ? 'cleared' : 'pending'; ?>">
                <i class="fa fa-check-circle clearance-icon <?php echo ($is_cafeteria_approved=="1") ? 'cleared' : 'pending'; ?>"></i>
                <span class="clearance-department">Student Cafeteria</span>
            </div>
            
            <div class="clearance-item <?php echo ($is_sport_approved=="1") ? 'cleared' : 'pending'; ?>">
                <i class="fa fa-check-circle clearance-icon <?php echo ($is_sport_approved=="1") ? 'cleared' : 'pending'; ?>"></i>
                <span class="clearance-department">Sports Department</span>
            </div>
            
            <div class="clearance-item <?php echo ($is_dean_approved=="1") ? 'cleared' : 'pending'; ?>">
                <i class="fa fa-check-circle clearance-icon <?php echo ($is_dean_approved=="1") ? 'cleared' : 'pending'; ?>"></i>
                <span class="clearance-department">Dean of Students</span>
            </div>
            
            <div class="clearance-item <?php echo ($is_police_approved=="1") ? 'cleared' : 'pending'; ?>">
                <i class="fa fa-check-circle clearance-icon <?php echo ($is_police_approved=="1") ? 'cleared' : 'pending'; ?>"></i>
                <span class="clearance-department">Campus Security</span>
            </div>
            
            <div class="clearance-item <?php echo ($is_registrar_approved=="1") ? 'cleared' : 'pending'; ?>">
                <i class="fa fa-check-circle clearance-icon <?php echo ($is_registrar_approved=="1") ? 'cleared' : 'pending'; ?>"></i>
                <span class="clearance-department">Registrar's Office</span>
            </div>
        </div>

        <!-- Student Details Grid -->
        <div class="student-details-grid">
            <div class="detail-item">
                <span class="detail-label">Full Name</span>
                <span class="detail-value"><?php echo htmlspecialchars($rowaccess['fullname']); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">ID Number</span>
                <span class="detail-value"><?php echo htmlspecialchars($rowaccess['matric_no']); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Faculty</span>
                <span class="detail-value"><?php echo htmlspecialchars($rowaccess['faculty']); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Department</span>
                <span class="detail-value"><?php echo htmlspecialchars($rowaccess['dept']); ?></span>
            </div>
            
            <?php if(isset($rowaccess['session'])): ?>
            <div class="detail-item">
                <span class="detail-label">Academic Session</span>
                <span class="detail-value"><?php echo htmlspecialchars($rowaccess['session']); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="detail-item">
                <span class="detail-label">Certificate ID</span>
                <span class="detail-value"><?php echo strtoupper(substr(md5($rowaccess['matric_no'] . $current_date), 0, 8)); ?></span>
            </div>
        </div>

        <!-- Issue Date -->
        <div class="issue-date">
            <i class="fa fa-calendar"></i>
            Issued on <?php echo date('F j, Y', strtotime($current_date)); ?> at Bule hora University
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <img src="images/signature.jpg" alt="Authorized Signature" class="signature-img" onerror="this.innerHTML='<span>Authorized Signature</span>';">
                </div>
                <div class="signature-label">Registrar</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line">
                    <?php echo generateScannableBarcode($barcode_data); ?>
                </div>
                <div class="signature-label">Barcode</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line">
                    <div class="date-content">
                        <div class="date-label">Date Issued:</div>
                        <div class="date-value"><?php echo date('F j, Y'); ?></div>
                        <div class="date-time"><?php echo date('g:i A'); ?></div>
                    </div>
                </div>
                <div class="signature-label">Issue Date</div>
            </div>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
        
        // Close window after printing (optional)
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>