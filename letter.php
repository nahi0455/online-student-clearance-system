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

// Generate QR code inside the certificate
$student_id = $rowaccess['matric_no']; // dynamic student matric number
$qr_text = urlencode($student_id);
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?data=$qr_text&size=150x150";
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
    <title>Bule hora university | Clearance Certificate</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* Modern Clearance Certificate Styling */
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --pending-color: #cbd5e1;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --shadow-soft: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-certificate: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-gold: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        body {
            background: linear-gradient(-45deg, #f8fafc, #e2e8f0, #f1f5f9, #e5e7eb);
            background-size: 400% 400%;
            animation: backgroundShift 15s ease infinite;
            min-height: 100vh;
            font-family: 'Georgia', 'Times New Roman', serif;
            padding: 20px 0;
        }

        @keyframes backgroundShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .certificate-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            animation: slideInUp 1s ease-out;
        }

        @keyframes slideInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .certificate {
            background: #ffffff;
            border: 3px solid var(--primary-color);
            border-radius: 20px;
            padding: 40px 50px;
            box-shadow: var(--shadow-certificate);
            position: relative;
            overflow: hidden;
            animation: certificateGlow 3s ease-in-out infinite;
        }

        @keyframes certificateGlow {
            0%, 100% { box-shadow: var(--shadow-certificate); }
            50% { box-shadow: var(--shadow-certificate), 0 0 30px rgba(102, 126, 234, 0.2); }
        }

        /* Decorative border pattern */
        .certificate::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 2px solid var(--gradient-gold);
            border-image: var(--gradient-gold) 1;
            border-radius: 15px;
            pointer-events: none;
        }

        /* Corner decorations */
        .certificate::after {
            content: '';
            position: absolute;
            top: 25px;
            left: 25px;
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 50%;
            opacity: 0.1;
        }

        .corner-decoration {
            position: absolute;
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 50%;
            opacity: 0.1;
        }

        .corner-decoration.top-right { top: 25px; right: 25px; }
        .corner-decoration.bottom-left { bottom: 25px; left: 25px; }
        .corner-decoration.bottom-right { bottom: 25px; right: 25px; }

        .university-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            display: block;
            object-fit: contain;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            padding: 5px;
            background: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
            animation: logoFloat 3s ease-in-out infinite;
            transition: all 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.3);
            border-color: var(--gradient-gold);
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
        }

        .university-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            animation: logoRotate 4s linear infinite;
        }

        @keyframes logoRotate {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(5deg); }
            75% { transform: rotate(-5deg); }
            100% { transform: rotate(0deg); }
        }

        .university-name {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: textShimmer 3s ease-in-out infinite;
        }

        @keyframes textShimmer {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.2); }
        }

        .university-location {
            font-size: 16px;
            color: var(--text-secondary);
            font-style: italic;
            margin-bottom: 20px;
        }

        .certificate-title {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 30px;
            padding: 15px 0;
            border-top: 2px solid var(--border-color);
            border-bottom: 2px solid var(--border-color);
            position: relative;
        }

        .certificate-title::before,
        .certificate-title::after {
            content: '✦';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 20px;
        }

        .certificate-title::before { left: 20px; }
        .certificate-title::after { right: 20px; }

        .student-info-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
            margin: 30px 0;
            padding: 25px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            border-radius: 15px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .student-photo {
            width: 140px;
            height: 160px;
            border-radius: 15px;
            object-fit: cover;
            border: 4px solid var(--primary-color);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            animation: photoFloat 3s ease-in-out infinite;
        }

        @keyframes photoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .qr-code {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            border: 2px solid var(--border-color);
            padding: 5px;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .student-details {
            flex: 1;
            text-align: left;
        }

        .greeting-text {
            text-align: center;
            font-size: 18px;
            color: var(--text-primary);
            margin: 25px 0;
            line-height: 1.6;
        }

        .student-name {
            font-weight: 700;
            color: var(--primary-color);
            text-transform: uppercase;
        }

        .clearance-statement {
            text-align: center;
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 30px;
            font-style: italic;
        }

        .clearance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }

        .clearance-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            background: white;
            border-radius: 12px;
            border: 2px solid transparent;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .clearance-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: all 0.5s ease;
        }

        .clearance-item:hover::before {
            left: 100%;
        }

        .clearance-item.cleared {
            border-color: var(--success-color);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02));
        }

        .clearance-item.pending {
            border-color: var(--pending-color);
            background: linear-gradient(135deg, rgba(203, 213, 225, 0.05), rgba(203, 213, 225, 0.02));
        }

        .clearance-icon {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        .clearance-icon.cleared {
            color: var(--success-color);
            animation: checkPulse 2s ease-in-out infinite;
        }

        .clearance-icon.pending {
            color: var(--pending-color);
        }

        @keyframes checkPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .clearance-department {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 15px;
        }

        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
            padding: 25px;
            background: linear-gradient(135deg, rgba(248, 250, 252, 0.8), rgba(241, 245, 249, 0.8));
            border-radius: 15px;
            border: 1px solid var(--border-color);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 16px;
        }

        .issue-date {
            text-align: center;
            font-size: 16px;
            color: var(--text-secondary);
            margin: 25px 0;
            padding: 15px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }

        .signature-section {
            display: flex;
            justify-content: space-around;
            align-items: end;
            margin: 40px 0 30px;
            gap: 30px;
        }

        .signature-box {
            text-align: center;
            flex: 1;
            max-width: 200px;
        }

        .signature-line {
            width: 100%;
            height: 80px;
            border: 2px dashed var(--text-muted);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .signature-line:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .signature-line.stamp {
            border-style: dotted;
            background: linear-gradient(45deg, transparent 25%, rgba(102, 126, 234, 0.05) 25%, rgba(102, 126, 234, 0.05) 50%, transparent 50%);
            background-size: 20px 20px;
        }

        .signature-line.stamp-image {
            border: 3px solid var(--primary-color);
            border-style: double;
            background: white;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .signature-line.stamp-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, transparent 30%, rgba(102, 126, 234, 0.1) 70%);
            pointer-events: none;
        }

        .stamp-img {
            max-width: 100%;
            max-height: 70px;
            object-fit: contain;
            filter: contrast(1.2) brightness(0.95);
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .stamp-img:hover {
            filter: contrast(1.3) brightness(1);
            transform: scale(1.05);
        }
        
        /* Scannable Barcode Styling */
        .barcode-box {
            background: white !important;
            border: 3px solid var(--primary-color) !important;
            border-radius: 12px !important;
            padding: 0 !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2) !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 120px !important;
            height: 80px !important;
        }
        
        .professional-barcode {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            background: white !important;
            border: none !important;
            border-radius: 0 !important;
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
        }
        
        .scannable-barcode-img {
            max-width: 160px !important;
            max-height: 50px !important;
            display: block !important;
            margin: 0 auto !important;
            border: none !important;
            background: white !important;
            object-fit: contain !important;
        }

        .signature-line.date-box {
            border: 2px solid var(--primary-color);
            border-style: solid;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .date-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 100%;
        }

        .date-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .date-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 2px 0;
        }

        .date-time {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .signature-line.signature-image {
            border: 2px solid var(--primary-color);
            border-style: solid;
            background: white;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-img {
            max-width: 100%;
            max-height: 60px;
            object-fit: contain;
            filter: contrast(1.1) brightness(0.9);
            transition: all 0.3s ease;
        }

        .signature-img:hover {
            filter: contrast(1.2) brightness(1);
            transform: scale(1.02);
        }

        .signature-label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .print-section {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .print-button, .download-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            min-width: 160px;
            justify-content: center;
        }

        .print-button {
            background: var(--gradient-primary);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .download-button {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .download-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            color: white;
            text-decoration: none;
            background: linear-gradient(135deg, #059669, #047857);
        }

        /* Print Styles - SINGLE PAGE ONLY */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 12px !important;
                line-height: 1.3 !important;
            }
            
            .certificate-container {
                padding: 0 !important;
                margin: 0 !important;
                max-width: none !important;
                width: 100% !important;
                height: auto !important;
            }
            
            .certificate {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                animation: none !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin: 0 !important;
                padding: 15px !important;
                min-height: auto !important;
                height: auto !important;
                max-height: none !important;
                font-size: 11px !important;
                transform: scale(0.85) !important;
                transform-origin: top left !important;
                width: 118% !important;
            }
            
            .university-header {
                margin-bottom: 15px !important;
            }
            
            .brand-logo {
                width: 70px !important;
                height: 70px !important;
                animation: none !important;
                margin: 0 auto 10px !important;
                border: 2px solid #000 !important;
                display: block !important;
                object-fit: contain !important;
                border-radius: 50% !important;
                padding: 3px !important;
                background: white !important;
            }
            
            .university-logo {
                width: 60px !important;
                height: 60px !important;
                animation: none !important;
                margin: 0 auto 10px !important;
            }
            
            .university-name {
                font-size: 20px !important;
                margin-bottom: 5px !important;
            }
            
            .university-location {
                font-size: 12px !important;
                margin-bottom: 10px !important;
            }
            
            .certificate-title {
                font-size: 16px !important;
                margin-bottom: 15px !important;
                padding: 8px 0 !important;
            }
            
            .student-info-section {
                margin: 15px 0 !important;
                padding: 15px !important;
                gap: 15px !important;
            }
            
            .student-photo {
                width: 100px !important;
                height: 120px !important;
                animation: none !important;
            }
            
            .qr-code {
                width: 80px !important;
                height: 80px !important;
            }
            
            .greeting-text {
                font-size: 13px !important;
                margin: 10px 0 !important;
            }
            
            .clearance-statement {
                font-size: 12px !important;
                margin-bottom: 15px !important;
            }
            
            .clearance-grid {
                gap: 8px !important;
                margin: 15px 0 !important;
                page-break-inside: avoid !important;
                grid-template-columns: repeat(3, 1fr) !important;
            }
            
            .clearance-item {
                padding: 8px 12px !important;
                font-size: 11px !important;
                page-break-inside: avoid !important;
            }
            
            .student-details-grid {
                gap: 10px !important;
                margin: 15px 0 !important;
                padding: 15px !important;
                page-break-inside: avoid !important;
                grid-template-columns: repeat(3, 1fr) !important;
            }
            
            .detail-label {
                font-size: 9px !important;
            }
            
            .detail-value {
                font-size: 11px !important;
            }
            
            .issue-date {
                font-size: 12px !important;
                margin: 15px 0 !important;
                padding: 10px !important;
            }
            
            .signature-section {
                gap: 15px !important;
                margin: 20px 0 15px !important;
                page-break-inside: avoid !important;
            }
            
            /* Signature section fixes */
            .signature-section {
                gap: 15px !important;
                margin: 20px 0 15px !important;
                page-break-inside: avoid !important;
                display: flex !important;
                justify-content: space-around !important;
            }
            
            .signature-box {
                text-align: center !important;
                flex: 1 !important;
                max-width: 150px !important;
            }
            
            .signature-line {
                border: 2px solid #000 !important;
                color: #000 !important;
                height: 60px !important;
                font-size: 10px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                background: white !important;
                margin-bottom: 8px !important;
                padding: 3px !important;
                width: 100% !important;
            }
            
            /* Signature image */
            .signature-line.signature-image {
                border: 2px solid #000 !important;
                background: white !important;
                padding: 3px !important;
            }
            
            .signature-img {
                max-height: 45px !important;
                max-width: 100% !important;
                display: block !important;
                object-fit: contain !important;
                width: auto !important;
                height: auto !important;
            }
            
            /* Stamp image */
            .signature-line.stamp-image {
                border: 2px solid #000 !important;
                background: white !important;
                padding: 3px !important;
            }
            
            .stamp-img {
                max-height: 50px !important;
                max-width: 100% !important;
                display: block !important;
                object-fit: contain !important;
            }
            
            /* Scannable Barcode styling for print */
            .barcode-box {
                border: 2px solid #000 !important;
                background: white !important;
                padding: 0 !important;
                height: 60px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-height: 60px !important;
            }
            
            .professional-barcode {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 0 !important;
                background: white !important;
                border: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
            }
            
            .scannable-barcode-img {
                max-width: 140px !important;
                max-height: 40px !important;
                display: block !important;
                margin: 0 auto !important;
                border: none !important;mportant;
                background: white !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
            
            /* Date box */
            .signature-line.date-box {
                border: 2px solid #000 !important;
                background: white !important;
                padding: 5px !important;
                text-align: center !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            
            .date-content {
                gap: 2px !important;
                display: flex !important;
                flex-direction: column !important;
                width: 100% !important;
                text-align: center !important;
            }
            
            .date-label {
                font-size: 8px !important;
                color: #000 !important;
                font-weight: 600 !important;
                display: block !important;
            }
            
            .date-value {
                font-size: 10px !important;
                color: #000 !important;
                font-weight: 700 !important;
                display: block !important;
                margin: 2px 0 !important;
            }
            
            .date-time {
                font-size: 9px !important;
                color: #000 !important;
                font-weight: 500 !important;
                display: block !important;
            }
            
            .signature-label {
                font-size: 10px !important;
                color: #000 !important;
                font-weight: 600 !important;
                text-transform: uppercase !important;
                margin-top: 5px !important;
            }
            
            /* Force visibility of images and content */
            .signature-img, .stamp-img {
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            .date-content * {
                opacity: 1 !important;
                visibility: visible !important;
                color: #000 !important;
            }
            
            /* Additional image print fixes */
            img {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            /* Fallback text for images */
            .signature-img[src=""], .signature-img:not([src]) {
                display: none !important;
            }
            
            .signature-img[src=""]:after, .signature-img:not([src]):after {
                content: "Authorized Signature" !important;
                display: block !important;
                text-align: center !important;
                font-weight: bold !important;
                color: #000 !important;
            }
            
            .stamp-img[src=""], .stamp-img:not([src]) {
                display: none !important;
            }
            
            .stamp-img[src=""]:after, .stamp-img:not([src]):after {
                content: "Official Seal" !important;
                display: block !important;
                text-align: center !important;
                font-weight: bold !important;
                color: #000 !important;
            }
            
            .signature-label {
                font-size: 10px !important;
            }
            
            .print-section {
                display: none !important;
            }
            
            /* Force single page */
            @page {
                size: A4 !important;
                margin: 10mm !important;
            }
            
            /* Prevent page breaks */
            .certificate * {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            
            /* Ensure content fits */
            .certificate {
                max-height: 95vh !important;
                overflow: hidden !important;
            }
        }

        /* PDF Generation Specific Styles */
        .pdf-mode {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .pdf-mode .certificate-container {
            padding: 0 !important;
            margin: 0 !important;
            max-width: none !important;
            width: 100% !important;
            height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .pdf-mode .certificate-container {
            padding: 0 !important;
            margin: 0 auto !important;
            max-width: 210mm !important;
            width: 210mm !important;
            height: 297mm !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
        }
        
        .pdf-mode .certificate {
            box-shadow: none !important;
            animation: none !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin: 0 auto !important;
            padding: 10mm !important;
            font-size: 12px !important;
            width: 200mm !important;
            max-width: 200mm !important;
            height: 287mm !important;
            max-height: 287mm !important;
            border: 2px solid var(--primary-color) !important;
            border-radius: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            align-items: center !important;
            text-align: center !important;
            overflow: hidden !important;
        }
        
        .pdf-mode .university-header {
            margin-bottom: 8px !important;
            flex-shrink: 0 !important;
            width: 100% !important;
            text-align: center !important;
        }
        
        .pdf-mode .brand-logo {
            width: 60px !important;
            height: 60px !important;
            animation: none !important;
            margin: 0 auto 8px !important;
            border: 2px solid #000 !important;
            display: block !important;
            object-fit: contain !important;
            border-radius: 50% !important;
            padding: 2px !important;
            background: white !important;
        }
        
        .pdf-mode .university-logo {
            animation: none !important;
            width: 50px !important;
            height: 50px !important;
            margin: 0 auto 5px !important;
        }
        
        .pdf-mode .university-name {
            font-size: 20px !important;
            margin-bottom: 5px !important;
            text-align: center !important;
        }
        
        .pdf-mode .university-location {
            font-size: 10px !important;
            margin-bottom: 8px !important;
            text-align: center !important;
        }
        
        .pdf-mode .certificate-title {
            font-size: 16px !important;
            margin-bottom: 8px !important;
            padding: 5px 0 !important;
            flex-shrink: 0 !important;
            width: 100% !important;
            text-align: center !important;
        }
        
        .pdf-mode .student-info-section {
            margin: 8px 0 !important;
            padding: 10px !important;
            gap: 10px !important;
            flex-shrink: 0 !important;
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        
        .pdf-mode .student-details {
            text-align: center !important;
        }
        
        .pdf-mode .student-photo {
            width: 80px !important;
            height: 100px !important;
            animation: none !important;
        }
        
        .pdf-mode .qr-code {
            width: 60px !important;
            height: 60px !important;
        }
        
        .pdf-mode .greeting-text {
            font-size: 12px !important;
            margin: 8px 0 !important;
            line-height: 1.3 !important;
            text-align: center !important;
        }
        
        .pdf-mode .clearance-statement {
            font-size: 10px !important;
            margin-bottom: 8px !important;
            text-align: center !important;
        }
        
        .pdf-mode .clearance-grid {
            gap: 5px !important;
            margin: 8px 0 !important;
            grid-template-columns: repeat(3, 1fr) !important;
            flex-shrink: 0 !important;
            width: 100% !important;
            justify-items: center !important;
        }
        
        .pdf-mode .clearance-item {
            padding: 5px 8px !important;
            font-size: 9px !important;
            text-align: center !important;
            width: 100% !important;
        }
        
        .pdf-mode .student-details-grid {
            gap: 5px !important;
            margin: 8px 0 !important;
            padding: 8px !important;
            grid-template-columns: repeat(3, 1fr) !important;
            flex-shrink: 0 !important;
            width: 100% !important;
            justify-items: center !important;
        }
        
        .pdf-mode .detail-item {
            text-align: center !important;
        }
        
        .pdf-mode .detail-label {
            font-size: 7px !important;
            text-align: center !important;
        }
        
        .pdf-mode .detail-value {
            font-size: 10px !important;
            text-align: center !important;
        }
        
        .pdf-mode .issue-date {
            font-size: 10px !important;
            margin: 8px 0 !important;
            padding: 6px !important;
            flex-shrink: 0 !important;
            text-align: center !important;
            width: 100% !important;
        }
        
        .pdf-mode .signature-section {
            margin: 8px 0 5px !important;
            gap: 10px !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            flex-shrink: 0 !important;
            width: 100% !important;
        }
        
        .pdf-mode .signature-box {
            text-align: center !important;
            flex: 1 !important;
            max-width: 100px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }
        
        .pdf-mode .signature-line {
            height: 45px !important;
            border: 1px solid #000 !important;
            background: white !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-bottom: 5px !important;
            padding: 2px !important;
            width: 100% !important;
        }
        
        .pdf-mode .signature-img {
            max-height: 35px !important;
            max-width: 100% !important;
            display: block !important;
            object-fit: contain !important;
            margin: 0 auto !important;
        }
        
        .pdf-mode .stamp-img {
            max-height: 40px !important;
            max-width: 100% !important;
            display: block !important;
            object-fit: contain !important;
            margin: 0 auto !important;
        }
        
        .pdf-mode .professional-barcode {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            background: white !important;
            border: none !important;
            border-radius: 0 !important;
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
        }
        
        .pdf-mode .scannable-barcode-img {
            max-width: 120px !important;
            max-height: 30px !important;
            display: block !important;
            margin: 0 auto !important;
            border: none !important;
            background: white !important;
        }
        
        .pdf-mode .date-content {
            display: flex !important;
            flex-direction: column !important;
            gap: 1px !important;
            text-align: center !important;
            align-items: center !important;
            width: 100% !important;
        }
        
        .pdf-mode .date-label {
            font-size: 6px !important;
            color: #000 !important;
            font-weight: 600 !important;
            text-align: center !important;
        }
        
        .pdf-mode .date-value {
            font-size: 8px !important;
            color: #000 !important;
            font-weight: 700 !important;
            text-align: center !important;
        }
        
        .pdf-mode .date-time {
            font-size: 7px !important;
            color: #000 !important;
            font-weight: 500 !important;
            text-align: center !important;
        }
        
        .pdf-mode .signature-label {
            font-size: 8px !important;
            color: #000 !important;
            font-weight: 600 !important;
            text-align: center !important;
        }
        
        .pdf-mode .university-logo {
            animation: none !important;
            width: 50px !important;
            height: 50px !important;
            margin: 0 auto !important;
        }
        
        .pdf-mode .print-section {
            display: none !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .certificate {
                padding: 30px 25px;
            }
            
            .university-name {
                font-size: 24px;
            }
            
            .certificate-title {
                font-size: 20px;
                letter-spacing: 1px;
            }
            
            .student-info-section {
                flex-direction: column;
                gap: 20px;
            }
            
            .clearance-grid {
                grid-template-columns: 1fr;
            }
            
            .signature-section {
                flex-direction: column;
                gap: 20px;
            }
            
            .print-section {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .print-button, .download-button {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>

<body>
<div class="certificate-container">
    <div class="certificate">
        <!-- Corner Decorations -->
        <div class="corner-decoration top-right"></div>
        <div class="corner-decoration bottom-left"></div>
        <div class="corner-decoration bottom-right"></div>
        
        <!-- University Header -->
        <div class="university-header">
            <img src="images/logo.png" alt="University Logo" class="brand-logo" onerror="this.style.display='none'">
      
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
                <div class="signature-line signature-image">
                    <img src="images/signature.jpg" alt="Authorized Signature" class="signature-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span style="display: none;">Authorized Signature</span>
                </div>
                <div class="signature-label">Registrar</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line stamp stamp-image barcode-box">
                    <?php
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
                        $barcode_html = '<div class="professional-barcode" style="display: flex !important; align-items: center !important; justify-content: center !important; width: 100% !important; height: 100% !important; background: white !important; border: none !important; padding: 0 !important; margin: 0 !important;">';
                        
                        // Try the first service (most reliable)
                        $barcode_url = $barcode_services[0];
                        $barcode_html .= '<img src="' . $barcode_url . '" alt="Scannable Barcode" style="max-width: 180px !important; max-height: 50px !important; display: block !important; border: none !important; background: white !important; margin: 0 auto !important;" class="scannable-barcode-img" onerror="this.onerror=null; this.src=\'' . $barcode_services[1] . '\'; setTimeout(() => { if(this.naturalWidth === 0) { this.src=\'' . $barcode_services[2] . '\'; } }, 2000);">';
                        
                        $barcode_html .= '</div>';
                        
                        return $barcode_html;
                    }
                    
                    echo generateScannableBarcode($barcode_data);
                    ?>
                </div>
                <div class="signature-label">Barcode</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line date-box">
                    <div class="date-content">
                        <div class="date-label">Date Issued:</div>
                        <div class="date-value"><?php echo date('F j, Y'); ?></div>
                        <div class="date-time"><?php echo date('g:i A'); ?></div>
                    </div>
                </div>
                <div class="signature-label">Issue Date</div>
            </div>
        </div>

        <!-- Print Section -->
        <div class="print-section">
            <a href="#" class="print-button" onclick="printCertificate(); return false;">
                <i class="fa fa-print"></i>
                Print Certificate
            </a>
            <a href="#" class="download-button" onclick="downloadCertificate(); return false;">
                <i class="fa fa-download"></i>
                Download PDF
            </a>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="js/jquery-2.1.1.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<!-- Add html2pdf library for direct PDF download -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
// Direct PDF Download Function
function downloadCertificate() {
    try {
        // Show loading indicator
        const downloadBtn = document.querySelector('.download-button');
        const originalText = downloadBtn.innerHTML;
        downloadBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating PDF...';
        downloadBtn.style.pointerEvents = 'none';
        
        // Hide the print section temporarily
        const printSection = document.querySelector('.print-section');
        const originalDisplay = printSection.style.display;
        printSection.style.display = 'none';
        
        // Get the certificate element FIRST
        const element = document.querySelector('.certificate');
        
        // Apply PDF-specific styling
        document.body.classList.add('pdf-mode');
        
        // Ensure signature and barcode elements are loaded and visible
        const signatureImg = document.querySelector('.signature-img');
        const professionalBarcode = document.querySelector('.professional-barcode');
        const brandLogo = document.querySelector('.brand-logo');
        
        if (signatureImg) {
            signatureImg.style.display = 'block';
            signatureImg.style.visibility = 'visible';
            signatureImg.style.opacity = '1';
            signatureImg.style.maxHeight = '35px';
            signatureImg.style.maxWidth = '100%';
        }
        
        if (professionalBarcode) {
            professionalBarcode.style.display = 'flex';
            professionalBarcode.style.visibility = 'visible';
            professionalBarcode.style.opacity = '1';
            
            // Ensure barcode bars are visible
            const barcodeBars = professionalBarcode.querySelectorAll('.scannable-barcode-img div');
            barcodeBars.forEach(bar => {
                bar.style.background = '#000';
                bar.style.visibility = 'visible';
                bar.style.opacity = '1';
            });
            
            // Ensure barcode text is visible
            const barcodeText = professionalBarcode.querySelector('.barcode-number');
            if (barcodeText) {
                barcodeText.style.color = '#000';
                barcodeText.style.visibility = 'visible';
                barcodeText.style.opacity = '1';
            }
        }
        
        if (brandLogo) {
            brandLogo.style.display = 'block';
            brandLogo.style.visibility = 'visible';
            brandLogo.style.opacity = '1';
            brandLogo.style.maxHeight = '60px';
            brandLogo.style.maxWidth = '60px';
        }
        
        // Ensure date content is visible
        const dateElements = document.querySelectorAll('.date-content, .date-content *');
        dateElements.forEach(el => {
            el.style.display = 'block';
            el.style.visibility = 'visible';
            el.style.opacity = '1';
            el.style.color = '#000';
        });
        
        // Force signature section visibility
        const signatureSection = document.querySelector('.signature-section');
        if (signatureSection) {
            signatureSection.style.display = 'flex';
            signatureSection.style.visibility = 'visible';
        }
        
        const signatureBoxes = document.querySelectorAll('.signature-box');
        signatureBoxes.forEach(box => {
            box.style.display = 'block';
            box.style.visibility = 'visible';
        });
        
        const signatureLines = document.querySelectorAll('.signature-line');
        signatureLines.forEach(line => {
            line.style.display = 'flex';
            line.style.visibility = 'visible';
            line.style.alignItems = 'center';
            line.style.justifyContent = 'center';
        });
        
        // Create a centered wrapper for better positioning
        const originalParent = element.parentNode;
        const wrapper = document.createElement('div');
        wrapper.style.cssText = `
            width: 210mm;
            height: 297mm;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            background: white;
            position: relative;
        `;
        
        // Temporarily wrap the element
        originalParent.insertBefore(wrapper, element);
        wrapper.appendChild(element);
        
        // Update element reference to the wrapper
        const elementToCapture = wrapper;
        
        // Create filename with student info
        const studentName = '<?php echo preg_replace("/[^a-zA-Z0-9]/", "_", $rowaccess['fullname']); ?>';
        const matricNo = '<?php echo $rowaccess['matric_no']; ?>';
        const currentDate = new Date().toISOString().split('T')[0];
        const filename = `Clearance_Certificate_${matricNo}_${studentName}_${currentDate}.pdf`;
        
        // Wait a moment for styles to apply
        setTimeout(() => {
            // PDF options for exact A4 dimensions with centered content
            const opt = {
                margin: [0, 0, 0, 0], // No margins to allow full control
                filename: filename,
                image: { 
                    type: 'jpeg', 
                    quality: 0.95 
                },
                html2canvas: { 
                    scale: 1.5,
                    useCORS: true,
                    letterRendering: true,
                    allowTaint: true,
                    width: 794, // A4 width in pixels at 96 DPI (210mm)
                    height: 1123, // A4 height in pixels at 96 DPI (297mm)
                    scrollX: 0,
                    scrollY: 0,
                    backgroundColor: '#ffffff',
                    windowWidth: 794,
                    windowHeight: 1123,
                    logging: false,
                    removeContainer: false,
                    x: 0,
                    y: 0
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: [210, 297], // Exact A4 dimensions in mm
                    orientation: 'portrait',
                    compress: true
                },
                pagebreak: { 
                    mode: 'avoid-all'
                }
            };
            
            // Generate and download PDF
            html2pdf().set(opt).from(elementToCapture).toPdf().get('pdf').then(function (pdf) {
                // Ensure single page
                const totalPages = pdf.internal.getNumberOfPages();
                if (totalPages > 1) {
                    for (let i = 2; i <= totalPages; i++) {
                        pdf.deletePage(i);
                    }
                }
                return pdf;
            }).save().then(() => {
                // Restore original structure
                originalParent.insertBefore(element, wrapper);
                wrapper.remove();
                
                // Restore original state
                document.body.classList.remove('pdf-mode');
                printSection.style.display = originalDisplay;
                downloadBtn.innerHTML = originalText;
                downloadBtn.style.pointerEvents = 'auto';
                
                // Show success message
                showNotification('Certificate downloaded successfully!', 'success');
            }).catch((error) => {
                console.error('PDF generation error:', error);
                
                // Restore original structure
                if (wrapper.parentNode) {
                    originalParent.insertBefore(element, wrapper);
                    wrapper.remove();
                }
                
                // Restore original state
                document.body.classList.remove('pdf-mode');
                printSection.style.display = originalDisplay;
                downloadBtn.innerHTML = originalText;
                downloadBtn.style.pointerEvents = 'auto';
                
                // Try alternative method
                downloadCertificateAlternative();
            });
        }, 500); // Wait 500ms for styles to apply
        
    } catch (error) {
        console.error('Download error:', error);
        downloadCertificateAlternative();
    }
}

// Alternative download method with better single-page handling
function downloadCertificateAlternative() {
    try {
        const element = document.querySelector('.certificate');
        const studentName = '<?php echo preg_replace("/[^a-zA-Z0-9]/", "_", $rowaccess['fullname']); ?>';
        const matricNo = '<?php echo $rowaccess['matric_no']; ?>';
        const currentDate = new Date().toISOString().split('T')[0];
        const filename = `Clearance_Certificate_${matricNo}_${studentName}_${currentDate}.pdf`;
        
        // Apply PDF mode
        document.body.classList.add('pdf-mode');
        
        // Simpler options for single page
        const opt = {
            margin: 10,
            filename: filename,
            image: { type: 'jpeg', quality: 0.9 },
            html2canvas: { 
                scale: 1,
                useCORS: true,
                backgroundColor: '#ffffff',
                removeContainer: true
            },
            jsPDF: { 
                unit: 'mm', 
                format: 'a4', 
                orientation: 'portrait' 
            }
        };
        
        html2pdf().set(opt).from(element).save().then(() => {
            document.body.classList.remove('pdf-mode');
            showNotification('Certificate downloaded successfully!', 'success');
        }).catch(() => {
            document.body.classList.remove('pdf-mode');
            showNotification('Download completed with basic method', 'success');
        });
        
    } catch (error) {
        document.body.classList.remove('pdf-mode');
        showNotification('Please try the print option', 'error');
    }
}

// Fallback download function (opens print dialog)
function downloadCertificateFallback() {
    const printSection = document.querySelector('.print-section');
    if (printSection) {
        printSection.style.display = 'none';
    }
    
    window.print();
    
    setTimeout(() => {
        if (printSection) {
            printSection.style.display = 'flex';
        }
    }, 1000);
}

// Print function with same formatting as download
function printCertificate() {
    try {
        // Get the certificate element
        const element = document.querySelector('.certificate');
        
        // Apply PDF-specific styling
        document.body.classList.add('pdf-mode');
        
        // Hide print section
        const printSection = document.querySelector('.print-section');
        const originalDisplay = printSection.style.display;
        printSection.style.display = 'none';
        
        // Ensure signature and barcode elements are loaded and visible
        const signatureImg = document.querySelector('.signature-img');
        const professionalBarcode = document.querySelector('.professional-barcode');
        const brandLogo = document.querySelector('.brand-logo');
        
        if (signatureImg) {
            signatureImg.style.display = 'block';
            signatureImg.style.visibility = 'visible';
            signatureImg.style.opacity = '1';
            signatureImg.style.maxHeight = '35px';
            signatureImg.style.maxWidth = '100%';
        }
        
        if (professionalBarcode) {
            professionalBarcode.style.display = 'flex';
            professionalBarcode.style.visibility = 'visible';
            professionalBarcode.style.opacity = '1';
            
            // Ensure barcode bars are visible
            const barcodeBars = professionalBarcode.querySelectorAll('.scannable-barcode-img div');
            barcodeBars.forEach(bar => {
                bar.style.background = '#000';
                bar.style.visibility = 'visible';
                bar.style.opacity = '1';
            });
            
            // Ensure barcode text is visible
            const barcodeText = professionalBarcode.querySelector('.barcode-number');
            if (barcodeText) {
                barcodeText.style.color = '#000';
                barcodeText.style.visibility = 'visible';
                barcodeText.style.opacity = '1';
            }
        }
        
        if (brandLogo) {
            brandLogo.style.display = 'block';
            brandLogo.style.visibility = 'visible';
            brandLogo.style.opacity = '1';
            brandLogo.style.maxHeight = '60px';
            brandLogo.style.maxWidth = '60px';
        }
        
        // Ensure date content is visible
        const dateElements = document.querySelectorAll('.date-content, .date-content *');
        dateElements.forEach(el => {
            el.style.display = 'block';
            el.style.visibility = 'visible';
            el.style.opacity = '1';
            el.style.color = '#000';
        });
        
        // Force signature section visibility
        const signatureSection = document.querySelector('.signature-section');
        if (signatureSection) {
            signatureSection.style.display = 'flex';
            signatureSection.style.visibility = 'visible';
        }
        
        const signatureBoxes = document.querySelectorAll('.signature-box');
        signatureBoxes.forEach(box => {
            box.style.display = 'block';
            box.style.visibility = 'visible';
        });
        
        const signatureLines = document.querySelectorAll('.signature-line');
        signatureLines.forEach(line => {
            line.style.display = 'flex';
            line.style.visibility = 'visible';
            line.style.alignItems = 'center';
            line.style.justifyContent = 'center';
        });
        
        // Create a centered wrapper for better positioning (same as download)
        const originalParent = element.parentNode;
        const wrapper = document.createElement('div');
        wrapper.style.cssText = `
            width: 210mm;
            height: 297mm;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            background: white;
            position: relative;
            page-break-inside: avoid;
        `;
        
        // Temporarily wrap the element
        originalParent.insertBefore(wrapper, element);
        wrapper.appendChild(element);
        
        // Wait a moment for styles to apply, then print
        setTimeout(() => {
            // Trigger print dialog
            window.print();
            
            // Restore original structure after print dialog
            setTimeout(() => {
                // Restore original structure
                originalParent.insertBefore(element, wrapper);
                wrapper.remove();
                
                // Restore original state
                document.body.classList.remove('pdf-mode');
                printSection.style.display = originalDisplay;
            }, 1000);
        }, 500);
        
    } catch (error) {
        console.error('Print error:', error);
        // Fallback to simple print
        window.print();
    }
}

// Notification function
function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        max-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    if (type === 'success') {
        notification.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        notification.innerHTML = '<i class="fa fa-check-circle"></i> ' + message;
    } else {
        notification.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
        notification.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + message;
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Make functions globally available
window.downloadCertificate = downloadCertificate;
window.downloadCertificateFallback = downloadCertificateFallback;
window.printCertificate = printCertificate;

// Barcode fallback handler
function handleBarcodeError() {
    const barcodeImgs = document.querySelectorAll('.scannable-barcode-img');
    barcodeImgs.forEach(img => {
        img.addEventListener('error', function() {
            console.log('Barcode image failed to load, showing fallback');
            const fallback = this.parentNode.querySelector('.barcode-fallback');
            if (fallback) {
                this.style.display = 'none';
                fallback.style.display = 'block';
            }
        });
        
        // Check if image loaded after 3 seconds
        setTimeout(() => {
            if (img.naturalWidth === 0 || img.naturalHeight === 0) {
                console.log('Barcode image did not load properly, showing fallback');
                const fallback = img.parentNode.querySelector('.barcode-fallback');
                if (fallback) {
                    img.style.display = 'none';
                    fallback.style.display = 'block';
                }
            }
        }, 3000);
    });
}

// Document ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Certificate page loaded with direct PDF download');
    
    // Check if html2pdf is loaded
    if (typeof html2pdf === 'undefined') {
        console.warn('html2pdf library not loaded, using fallback method');
        // Replace download function with fallback
        window.downloadCertificate = downloadCertificateFallback;
    }
    
    // Add event listeners as backup
    const downloadBtn = document.querySelector('.download-button');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            downloadCertificate();
        });
    }
    
    const printBtn = document.querySelector('.print-button');
    if (printBtn) {
        printBtn.addEventListener('click', function(e) {
            e.preventDefault();
            printCertificate();
        });
    }
});
</script>

</body>
</html>
