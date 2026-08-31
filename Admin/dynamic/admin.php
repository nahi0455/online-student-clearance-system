<?php
session_start();
include('../connect.php');
error_reporting(0);

// Get department from URL or session
$dept = $_GET['dept'] ?? ($_SESSION['department'] ?? '');

if ($dept == '') {
    echo "Department not selected.";
    exit();
}

// Save into session
$_SESSION['department'] = $dept;

// Set auto-generated session data
$_SESSION['role'] = 'department_admin';
$_SESSION['admin-username'] = strtolower(str_replace(' ', '_', $dept)) . "_admin";
$_SESSION['fullname'] = $dept . " Admin";
$_SESSION['email'] = $_SESSION['admin-username'] . "@university.edu";
$_SESSION['photo'] = "uploads/default.png";

// Filters
$filter = $_GET['filter'] ?? '';
$selected_session = isset($_GET['session']) ? mysqli_real_escape_string($conn, $_GET['session']) : '';
$query_students = "SELECT * FROM students WHERE dept='$dept'";
if ($selected_session !== '') { $query_students .= " AND session='$selected_session'"; }
if ($filter == 'approved') { $query_students .= " AND is_department_approved = 1"; }
elseif ($filter == 'ready') { $query_students .= " AND is_department_approved = 0 AND is_library_approved = 1"; }
elseif ($filter == 'pending') { $query_students .= " AND is_department_approved = 0 AND is_library_approved = 0"; }
$query_students .= " ORDER BY fullname ASC";
$result_students = mysqli_query($conn, $query_students);
$sessions_result = mysqli_query($conn, "SELECT session FROM tblsession ORDER BY ID DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Dashboard | <?php echo $dept; ?> Department</title>
<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../dist/css/adminlte.min.css">
<link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">

<style>
/* Modern Dynamic Admin Styling */
:root {
  --primary-gradient: linear-gradient(135deg,  #007bff 100%, #ccccff 0%);
  --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
  --info-gradient: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
  --glass-bg: rgba(255, 255, 255, 0.95);
  --glass-border: rgba(226, 232, 240, 0.5);
  --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.08);
  --shadow-hover: 0 8px 30px rgba(163, 158, 158, 0.12);
  --border-radius: 16px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body { 
  margin-left: 0px !important; 
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.content-wrapper { 
  background: #b9b9c4ff;
  min-height: 100vh;
  position: relative;
}

/* Animated background pattern */
.content-wrapper::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 20%, rgba(102, 126, 234, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.05) 0%, transparent 50%);
  animation: backgroundShift 10s ease-in-out infinite;
  pointer-events: none;
}

@keyframes backgroundShift {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.6; transform: scale(1.1); }
}

.container-fluid { 
  max-width: 1500px; 
  margin: 0 auto; 
  padding: 20px;
  position: relative;
  z-index: 2;
}

/* Enhanced Cards */
.card { 
  border-radius: var(--border-radius);
  border: 1px solid var(--glass-border);
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  box-shadow: var(--shadow-soft);
  transition: var(--transition);
  animation: fadeInUp 0.6s ease-out;
  overflow: hidden;
  position: relative;
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: var(--transition);
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-hover);
}

.card:hover::before {
  left: 100%;
}

@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.card-header { 
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  background: var(--primary-gradient) !important;
  border-bottom: 1px solid var(--glass-border);
  padding: 20px 24px;
  position: relative;
  overflow: hidden;
}

.card-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: var(--transition);
}

.card-header:hover::before {
  left: 100%;
}

.card-title {
  color: white !important;
  font-weight: 700 !important;
  font-size: 18px !important;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  animation: titleGlow 3s ease-in-out infinite;
}

@keyframes titleGlow {
  0%, 100% { filter: brightness(1); }
  50% { filter: brightness(1.1); }
}

/* Enhanced Table */
.table { 
  color: #1f2937;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--shadow-soft);
}

.table td, .table th { 
  padding: 12px 16px !important; 
  vertical-align: middle;
  border-color: var(--glass-border) !important;
  transition: var(--transition);
}

.table thead th {
  background: linear-gradient(135deg, #374151, #2d62acff) !important;
  color: #fff !important;
  font-weight: 600 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  font-size: 12px !important;
  position: relative;
}

.table tbody tr {
  transition: var(--transition);
  animation: tableRowSlide 0.5s ease-out;
}

.table tbody tr:hover {
  background-color: rgba(102, 126, 234, 0.05) !important;
  transform: scale(1.01);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

@keyframes tableRowSlide {
  0% { opacity: 0; transform: translateX(-20px); }
  100% { opacity: 1; transform: translateX(0); }
}

/* Enhanced User Images */
img.img-circle { 
  width: 60px; 
  height: 60px; 
  object-fit: cover; 
  border-radius: 50%;
  border: 3px solid rgba(102, 126, 234, 0.2);
  box-shadow: var(--shadow-soft);
  transition: var(--transition);
  animation: profileFloat 3s ease-in-out infinite;
}

img.img-circle:hover {
  transform: scale(1.1);
  box-shadow: var(--shadow-hover);
  border-color: rgba(102, 126, 234, 0.5);
}

@keyframes profileFloat {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-3px); }
}

/* Enhanced Search and Form Controls */
#searchInput, .form-control { 
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  color: #374151;
  border: 2px solid var(--glass-border);
  border-radius: 12px;
  padding: 12px 16px;
  transition: var(--transition);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
  font-weight: 500;
}

#searchInput:focus, .form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1), inset 0 2px 4px rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
  outline: none;
  background: rgba(255, 255, 255, 1);
}

#searchInput::placeholder, .form-control::placeholder { 
  color: #6b7280;
  font-weight: 500;
}

/* Enhanced Buttons */
.btn {
  border-radius: 12px !important;
  font-weight: 600 !important;
  transition: var(--transition) !important;
  box-shadow: var(--shadow-soft) !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  position: relative !important;
  overflow: hidden !important;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: var(--transition);
}

.btn:hover {
  transform: translateY(-2px) !important;
  box-shadow: var(--shadow-hover) !important;
}

.btn:hover::before {
  left: 100%;
}

.btn-success {
  background: var(--success-gradient) !important;
  border: none !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3) !important;
}

.btn-success:hover {
  box-shadow: 0 8px 25px rgba(72, 187, 120, 0.4) !important;
}

.btn-warning {
  background: var(--warning-gradient) !important;
  border: none !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(237, 137, 54, 0.3) !important;
}

.btn-info {
  background: var(--info-gradient) !important;
  border: none !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(66, 153, 225, 0.3) !important;
}

.btn-secondary {
  background: linear-gradient(135deg, #6b7280, #4b5563) !important;
  border: none !important;
  color: white !important;
}

.btn-outline-secondary {
  border: 2px solid var(--glass-border) !important;
  color: #6b7280 !important;
  background: rgba(255, 255, 255, 0.8) !important;
}

.btn-outline-secondary:hover {
  background: rgba(107, 114, 128, 0.1) !important;
  border-color: #6b7280 !important;
  color: #374151 !important;
}

/* Enhanced Table Responsiveness */
.table-responsive {
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-soft);
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.table-responsive::-webkit-scrollbar {
  height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
  background: var(--primary-gradient);
  border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
  background: var(--secondary-gradient);
}

/* Compact table styling */
.table th, .table td {
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  max-width: 150px;
}

.table th:first-child, .table td:first-child {
  position: sticky;
  left: 0;
  background: inherit;
  z-index: 10;
}

/* Professional Sidebar Styling */
.main-sidebar {
  background: linear-gradient(180deg,  #151618ff 0%,  #007bff 100%) !important;
  box-shadow: 4px 0 20px rgba(102, 126, 234, 0.15) !important;
  border-right: 1px solid rgba(255, 255, 255, 0.1) !important;
  transition: var(--transition) !important;
}

.brand-link {
  background: linear-gradient(135deg,  #007bff 100%, #ccccff 0%) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
  padding: 20px 15px !important;
  position: relative !important;
  overflow: hidden !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-direction: column !important;
  text-decoration: none !important;
}

.brand-text {
  color: white !important;
  font-weight: 700 !important;
  font-size: 16px !important;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
  letter-spacing: 0.5px !important;
  text-align: center !important;
  line-height: 1.2 !important;
}

.brand-logo {
  width: 50px !important;
  height: 50px !important;
  border-radius: 50% !important;
  border: 2px solid rgba(255, 255, 255, 0.3) !important;
  margin-bottom: 8px !important;
  transition: var(--transition) !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
}

/* Sidebar Toggle Button */
.sidebar-toggle {
  background: linear-gradient(135deg, #667eea, #764ba2) !important;
  border: 2px solid rgba(255, 255, 255, 0.2) !important;
  border-radius: 8px !important;
  width: 35px !important;
  height: 35px !important;
  color: white !important;
  font-size: 14px !important;
  cursor: pointer !important;
  transition: var(--transition) !important;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin-right: 10px !important;
}

.sidebar-toggle:hover {
  transform: translateY(-1px) scale(1.05) !important;
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
  background: linear-gradient(135deg, #764ba2, #667eea) !important;
  border-color: rgba(255, 255, 255, 0.3) !important;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
  .container-fluid {
    padding: 10px;
  }
  
  .table {
    font-size: 11px;
  }
  
  img.img-circle {
    width: 35px;
    height: 35px;
  }
  
  .d-flex {
    flex-direction: column;
    align-items: stretch;
  }
  
  .table th, .table td {
    padding: 6px 8px !important;
  }
}
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Include the main admin sidebar -->
  <?php include('../sidebar.php'); ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
      </div>
    </div>

    <section class="content">
      <div class="container-fluid" style="padding-top: 0;">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <div class="d-flex align-items-center">
              <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
              </button>
              <h3 class="card-title mb-0">Students Under <?php echo $dept; ?> Department</h3>
            </div>
          </div>

          <div class="card-body">
            <?php if (isset($_GET['view']) && $_GET['view']=='session') { ?>
            <div class="d-flex justify-content-between mb-2">
              <div class="d-flex">
                <select id="sessionSelect" class="form-control" style="max-width:220px; margin-right:8px;">
                  <option value="">All Sessions</option>
                  <?php while($srow = mysqli_fetch_assoc($sessions_result)) { $sessionVal = $srow['session']; ?>
                  <option value="<?php echo htmlspecialchars($sessionVal); ?>" <?php echo ($selected_session === $sessionVal ? 'selected' : ''); ?>><?php echo htmlspecialchars($sessionVal); ?></option>
                  <?php } ?>
                </select>
                <button id="clearSession" class="btn btn-outline-secondary">Clear</button>
              </div>
              <input type="text" id="searchInput" class="form-control" placeholder="Search student..." style="max-width:320px;">
            </div>
            <?php } else { ?>
            <div class="d-flex justify-content-end mb-2">
              <input type="text" id="searchInput" class="form-control" placeholder="Search student..." style="max-width:320px;">
            </div>
            <?php } ?>

            <div class="table-responsive">
              <table class="table table-sm table-bordered table-striped table-hover" style="font-size:12px; min-width: 100%;">
                <thead class="bg-primary text-white">
                  <tr>
                    <th style="width: 40px;">#</th>
                    <th style="width: 120px;">Name</th>
                    <th style="width: 60px;">Photo</th>
                    <th style="width: 80px;">ID</th>
                    <th style="width: 70px;">Session</th>
                    <th style="width: 60px;">Dept</th>
                    <th style="width: 50px;">Lib</th>
                    <th style="width: 50px;">Book</th>
                    <th style="width: 50px;">Dorm</th>
                    <th style="width: 50px;">Cafe</th>
                    <th style="width: 50px;">Sport</th>
                    <th style="width: 50px;">Dean</th>
                    <th style="width: 50px;">Police</th>
                    <th style="width: 50px;">Reg</th>
                    <th style="width: 60px;">View</th>
                    <th style="width: 80px;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $sn = 1;
                  while($row = mysqli_fetch_assoc($result_students)) {
                      echo "<tr>
                          <td>{$sn}</td>
                          <td>{$row['fullname']}</td>
                          <td><img src='../{$row['photo']}' width='60' height='60' class='img-circle'></td>
                          <td>{$row['matric_no']}</td>
                          <td>{$row['session']}</td>
                          <td>".($row['is_department_approved'] ? '✅' : '❌')."</td>
                          <td>".($row['is_library_approved'] ? '✅' : '❌')."</td>
                          <td>".($row['is_bookstore_approved'] ? '✅' : '❌')."</td>
                          <td>".($row['is_dormitory_approved'] ? '✅' : '❌')."</td>
                          <td>".($row['is_cafeteria_approved'] ? '✅' : '❌')."</td>
                          <td>".($row['is_sport_approved'] ? '✅' : '❌')."</td>
                          <td>".($row['is_dean_approved'] ? '✅' : '❌')."</td>
                          <td>".($row['is_police_approved'] ? '✅' : '❌')."</td>
                          <td>".($row['is_registrar_approved'] ? '✅' : '❌')."</td>
                          <td>
                             <a href='leave_details.php?id=".$row['ID']."'>
                                <button class='btn btn-info btn-sm'>View</button>
                             </a>
                          </td>        
                          <td>";

                      // Approval button for department
                      if (!$row['is_library_approved']) {
                          echo "<button class='btn btn-warning btn-sm' disabled>
                                  Awaiting Library
                                </button>";
                      } elseif (!$row['is_department_approved']) {
                          echo "<form method='POST' action='approve_department.php' style='display:inline-block;'>
                                  <input type='hidden' name='student_id' value='{$row['ID']}'>
                                  <button type='submit' name='btnapprove' class='btn btn-success btn-sm'>
                                    Approve
                                  </button>
                                </form>";
                      } else {
                          echo "<button class='btn btn-secondary btn-sm' disabled>Approved</button>";
                      }

                      echo "</td></tr>";
                      $sn++;
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer text-center">
    <strong>&copy; <?php echo date('Y'); ?> University Clearance System.</strong>
  </footer>
</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Search functionality
  const searchInput = document.getElementById('searchInput');
  const rows = document.querySelectorAll('table tbody tr');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const v = this.value.trim().toLowerCase();
      rows.forEach(row => {
        const t = row.innerText.toLowerCase();
        row.style.display = (v === '' || t.includes(v)) ? '' : 'none';
      });
    });
  }
  
  // Session functionality
  const sessionSelect = document.getElementById('sessionSelect');
  const clearSession = document.getElementById('clearSession');
  if (sessionSelect) {
    sessionSelect.addEventListener('change', function () {
      const v = this.value;
      const urlBase = 'admin.php?dept=<?php echo $dept; ?>&view=session';
      window.location.href = v ? urlBase + '&session=' + encodeURIComponent(v) : urlBase;
    });
  }
  if (clearSession) {
    clearSession.addEventListener('click', function () {
      const urlBase = 'admin.php?dept=<?php echo $dept; ?>&view=session';
      window.location.href = urlBase;
    });
  }

  // Enhanced Sidebar toggle functionality
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const body = document.body;
  
  // Load sidebar state from localStorage
  const sidebarState = localStorage.getItem('sidebarCollapsed');
  if (sidebarState === 'true') {
    body.classList.add('sidebar-collapse');
  }
  
  // Toggle sidebar function
  function toggleSidebar() {
    body.classList.toggle('sidebar-collapse');
    body.classList.toggle('sidebar-open');
    
    // Save state to localStorage
    const isCollapsed = body.classList.contains('sidebar-collapse');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
    
    // Update toggle button icon with animation
    if (sidebarToggle) {
      const icon = sidebarToggle.querySelector('i');
      icon.style.transform = 'rotate(180deg)';
      setTimeout(() => {
        icon.className = isCollapsed ? 'fas fa-bars' : 'fas fa-times';
        icon.style.transform = 'rotate(0deg)';
      }, 150);
    }
  }
  
  // Desktop toggle
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleSidebar);
  }
  
  // Mobile overlay click
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
      body.classList.remove('sidebar-open');
    });
  }
  
  // Keyboard shortcut (Ctrl + B)
  document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'b') {
      e.preventDefault();
      toggleSidebar();
    }
  });
  
  // Auto-collapse on mobile
  function handleResize() {
    if (window.innerWidth <= 768) {
      body.classList.add('sidebar-collapse');
      body.classList.remove('sidebar-open');
    } else {
      // Restore desktop state
      const sidebarState = localStorage.getItem('sidebarCollapsed');
      if (sidebarState !== 'true') {
        body.classList.remove('sidebar-collapse');
      }
    }
  }
  
  window.addEventListener('resize', handleResize);
  handleResize(); // Initial check
});
</script>
</body>
</html>