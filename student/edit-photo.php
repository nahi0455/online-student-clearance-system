<?php
session_start();
error_reporting(1);
include('../connect.php');

// Debug: Log all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
}

if(empty($_SESSION['matric_no']))
    {   
    header("Location: ../login student/login.php"); 
    }
    else{
	}
      
$matric_no = $_SESSION["matric_no"];

date_default_timezone_set('Africa/Lagos');
$current_date = date('Y-m-d ');

		
// Get student data from register table for photo display
$sql = "SELECT * FROM register WHERE matric_no='$matric_no'"; 
$result = $conn->query($sql);
$rowaccess = mysqli_fetch_array($result);

// If no data in register table, fallback to students table
if (!$rowaccess) {
    $sql = "SELECT * FROM students WHERE matric_no='$matric_no'"; 
    $result = $conn->query($sql);
    $rowaccess = mysqli_fetch_array($result);
}

if(isset($_POST["btnedit"]))
{

$image= addslashes(file_get_contents($_FILES['userImage']['tmp_name']));
$image_name= addslashes($_FILES['userImage']['name']);
$image_size= getimagesize($_FILES['userImage']['tmp_name']);
move_uploaded_file($_FILES["userImage"]["tmp_name"],"../uploads/" . $_FILES["userImage"]["name"]);			
$location="uploads/" . $_FILES["userImage"]["name"];
			
// Update register table photo column (PRIMARY)
$sql = "UPDATE register SET photo='$location' WHERE matric_no='$matric_no'";
   
   if (mysqli_query($conn, $sql)) {
        // Also update students table for consistency
        $sql_students = "UPDATE students SET photo='$location' WHERE matric_no='$matric_no'";
        mysqli_query($conn, $sql_students);
        
        // Update session photo as well
        $_SESSION['photo'] = $location;
        
        header("Location: edit-photo.php");
   }else{
        $_SESSION['error']='Editing Was Not Successful';
   }
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Photo | Student Portal</title>
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
            padding: 24px 32px;
            margin-bottom: 24px;
            box-shadow: var(--glass-shadow);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--university-primary), var(--university-accent), var(--university-primary-dark));
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .page-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--university-primary-dark);
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 14px;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 8px 0 0 0;
            font-size: 12px;
        }

        .breadcrumb-item {
            color: var(--text-secondary);
        }

        .breadcrumb-item.active {
            color: var(--university-primary);
            font-weight: 600;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: var(--university-accent);
            font-weight: bold;
        }

        .photo-edit-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 32px;
            box-shadow: var(--glass-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .photo-edit-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(139, 90, 43, 0.2);
        }

        .photo-edit-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--university-primary), var(--success-color));
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .card-header {
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--glass-border);
        }

        .card-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--university-primary-dark);
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .card-subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            margin: 0;
        }

        .current-photo-display {
            text-align: center;
            margin-bottom: 32px;
        }

        .photo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .current-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--university-primary-light);
            box-shadow: 0 8px 25px rgba(139, 90, 43, 0.3);
            transition: var(--transition);
            animation: profilePulse 3s ease-in-out infinite;
        }

        .current-photo:hover {
            transform: scale(1.05);
            border-color: var(--university-primary);
            box-shadow: 0 12px 35px rgba(139, 90, 43, 0.4);
        }

        @keyframes profilePulse {
            0%, 100% { box-shadow: 0 8px 25px rgba(139, 90, 43, 0.3); }
            50% { box-shadow: 0 12px 35px rgba(139, 90, 43, 0.5); }
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transition: var(--transition);
        }

        .photo-container:hover .photo-overlay {
            opacity: 1;
        }

        .photo-overlay i {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .photo-overlay span {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .upload-form {
            max-width: 400px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .form-label i {
            color: var(--university-primary);
            width: 16px;
            text-align: center;
        }

        .file-input-container {
            position: relative;
            margin-bottom: 20px;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-display {
            background: linear-gradient(135deg, rgba(139, 90, 43, 0.05), rgba(139, 90, 43, 0.02));
            border: 2px dashed var(--glass-border);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
        }

        .file-input-display:hover {
            border-color: var(--university-primary-light);
            background: linear-gradient(135deg, rgba(139, 90, 43, 0.08), rgba(139, 90, 43, 0.04));
        }

        .file-input-display.dragover {
            border-color: var(--university-primary);
            background: linear-gradient(135deg, rgba(139, 90, 43, 0.12), rgba(139, 90, 43, 0.06));
        }

        .upload-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin: 0 auto 16px;
            transition: var(--transition);
        }

        .file-input-display:hover .upload-icon {
            transform: scale(1.1);
        }

        .upload-text {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .upload-hint {
            color: var(--text-secondary);
            font-size: 12px;
        }

        .photo-requirements {
            background: rgba(139, 90, 43, 0.05);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .photo-requirements h5 {
            font-size: 14px;
            font-weight: 600;
            color: var(--university-primary-dark);
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .requirements-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .requirements-list li {
            color: var(--text-secondary);
            font-size: 12px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .requirements-list li i {
            color: var(--success-color);
            width: 12px;
        }

        .btn-update {
            background: linear-gradient(135deg, var(--university-primary), var(--university-primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px 32px;
            font-weight: 600;
            font-size: 16px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(139, 90, 43, 0.3);
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .btn-update::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }

        .btn-update:hover::before {
            left: 100%;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 90, 43, 0.4);
            background: linear-gradient(135deg, var(--university-primary-dark), var(--university-primary));
            color: white;
        }

        .btn-update:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(107, 114, 128, 0.2);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
            background: linear-gradient(135deg, #4b5563, #374151);
            color: white;
            text-decoration: none;
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 16px 20px;
            margin-bottom: 20px;
            animation: slideInFromTop 0.5s ease-out;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
        }

        @keyframes slideInFromTop {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--university-primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 10px;
            background: rgba(139, 90, 43, 0.08);
            transition: var(--transition);
            margin-bottom: 20px;
        }

        .back-link:hover {
            background: rgba(139, 90, 43, 0.15);
            color: var(--university-primary-dark);
            text-decoration: none;
            transform: translateX(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            
            .photo-edit-card {
                padding: 24px 20px;
            }
            
            .current-photo {
                width: 150px;
                height: 150px;
            }
            
            .page-header {
                padding: 20px 16px;
            }
        }

        /* Loading Animation */
        .loading .btn-update {
            pointer-events: none;
        }

        .loading .btn-update::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <!-- Back Link -->
        <a href="profile.php" class="back-link">
            <i class="fa fa-arrow-left"></i>
            Back to Profile
        </a>

        <!-- Page Header -->
        <div class="page-header">
            <h2>
                <i class="fa fa-camera"></i>
                Edit Profile Photo
            </h2>
            <p>Update your profile picture to personalize your account</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="profile.php">Profile</a></li>
                    <li class="breadcrumb-item active">Edit Photo</li>
                </ol>
            </nav>
        </div>

        <!-- Messages -->
        <?php if(!empty($_SESSION['success'])) { ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php } ?>

        <?php if(!empty($_SESSION['error'])) { ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php } ?>

        <!-- Debug Info -->
        <?php if(isset($_POST["btnedit"])) { ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                Form submitted! Checking upload...
                <?php 
                if(isset($_FILES['userImage'])) {
                    echo "<br>File name: " . $_FILES['userImage']['name'];
                    echo "<br>File size: " . $_FILES['userImage']['size'];
                    echo "<br>File error: " . $_FILES['userImage']['error'];
                }
                ?>
            </div>
        <?php } ?>

        <!-- Photo Edit Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="photo-edit-card">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-user-circle"></i>
                            Profile Photo
                        </h3>
                        <p class="card-subtitle">Choose a clear, professional photo that represents you well</p>
                    </div>

                    <!-- Current Photo Display -->
                    <div class="current-photo-display">
                        <div class="photo-container">
                            <?php
                            // Get correct photo path
                            $photo_path = $rowaccess['photo'] ?? '';
                            
                            // Fix path for display
                            if (!empty($photo_path)) {
                                // Remove any existing ../ to normalize
                                $photo_path = str_replace('../', '', $photo_path);
                                // Add correct prefix for student directory
                                $display_path = '../' . $photo_path;
                            } else {
                                $display_path = '../images/default-avatar.png';
                            }
                            ?>
                            <img src="<?php echo $display_path; ?>" alt="Current Profile Photo" 
                                 class="current-photo" id="photo-preview"
                                 onerror="this.src='../images/default-avatar.png';">
                            <div class="photo-overlay">
                                <i class="fa fa-camera"></i>
                                <span>Click to Change</span>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form - SIMPLIFIED -->
                    <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa fa-image"></i>
                                Select New Photo
                            </label>
                            
                            <input name="userImage" type="file" class="form-control" 
                                   accept="image/*" onChange="display_img(this)" 
                                   style="margin-bottom: 20px;">
                        </div>

                        <!-- Photo Requirements -->
                        <div class="photo-requirements">
                            <h5>
                                <i class="fa fa-info-circle"></i>
                                Photo Requirements
                            </h5>
                            <ul class="requirements-list">
                                <li><i class="fa fa-check"></i> Maximum file size: 2MB</li>
                                <li><i class="fa fa-check"></i> Supported formats: JPG, PNG, GIF</li>
                                <li><i class="fa fa-check"></i> Recommended size: 300x300 pixels</li>
                                <li><i class="fa fa-check"></i> Clear, professional appearance</li>
                            </ul>
                        </div>

                        <button type="submit" name="btnedit" class="btn btn-update">
                            <i class="fa fa-save"></i>
                            Update Photo
                        </button>
                        
                        <!-- Debug link -->
                        <div class="text-center mt-2">
                            <small><a href="simple-photo-test.php" target="_blank" style="color: #666;">Try Simple Upload Test</a></small>
                        </div>
                    </form>

                    <!-- Additional Actions -->
                    <div class="text-center">
                        <a href="profile.php" class="btn-secondary">
                            <i class="fa fa-user"></i>
                            View Full Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/jquery-2.1.1.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    
    <script>
        // Enhanced image preview function
        function display_img(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    input.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, PNG, or GIF)');
                    input.value = '';
                    return;
                }
                
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#photo-preview').attr('src', e.target.result);
                    
                    // Update file display
                    const fileDisplay = document.getElementById('file-display');
                    fileDisplay.innerHTML = `
                        <div class="upload-icon" style="background: linear-gradient(135deg, var(--success-color), #059669);">
                            <i class="fa fa-check"></i>
                        </div>
                        <div class="upload-text" style="color: var(--success-color);">Photo selected: ${file.name}</div>
                        <div class="upload-hint">Click submit to save changes</div>
                    `;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form submission with loading state
        document.getElementById('photo-form').addEventListener('submit', function(e) {
            // Don't prevent default - let form submit normally
            const btn = document.getElementById('update-btn');
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';
            btn.disabled = true;
            // Form will submit normally
        });

        // Drag and drop functionality
        const fileDisplay = document.getElementById('file-display');
        const fileInput = document.getElementById('photo-input');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileDisplay.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileDisplay.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileDisplay.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            fileDisplay.classList.add('dragover');
        }

        function unhighlight(e) {
            fileDisplay.classList.remove('dragover');
        }

        fileDisplay.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                display_img(fileInput);
            }
        }

        // Click to select file
        fileDisplay.addEventListener('click', function() {
            fileInput.click();
        });
    </script>
</body>
</html>