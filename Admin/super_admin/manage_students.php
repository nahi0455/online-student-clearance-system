<?php
session_start();
error_reporting(0);
include('../connect.php');

$success = '';
$error = '';
$found_students = [];
$requested_year_students = [];
$filter_year = '';

// Session Management Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  
  // Session Management Actions
  if ($action === 'add_session') {
    $session_name = trim($_POST['session_name'] ?? '');
    if ($session_name === '') {
      $error = 'Session name is required.';
    } else {
      // Check if session already exists
      $check_stmt = $conn->prepare("SELECT ID FROM tblsession WHERE session = ?");
      if ($check_stmt) {
        $check_stmt->bind_param('s', $session_name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        if ($result->num_rows > 0) {
          $error = 'Session already exists.';
        } else {
          // Add new session
          $insert_stmt = $conn->prepare("INSERT INTO tblsession (session) VALUES (?)");
          if ($insert_stmt) {
            $insert_stmt->bind_param('s', $session_name);
            if ($insert_stmt->execute()) {
              $success = 'Session "' . htmlspecialchars($session_name) . '" added successfully.';
            } else {
              $error = 'Failed to add session.';
            }
            $insert_stmt->close();
          }
        }
        $check_stmt->close();
      }
    }
  } elseif ($action === 'update_session') {
    $session_id = intval($_POST['session_id'] ?? 0);
    $new_session_name = trim($_POST['new_session_name'] ?? '');
    if ($session_id <= 0 || $new_session_name === '') {
      $error = 'Invalid session data.';
    } else {
      $update_stmt = $conn->prepare("UPDATE tblsession SET session = ? WHERE ID = ?");
      if ($update_stmt) {
        $update_stmt->bind_param('si', $new_session_name, $session_id);
        if ($update_stmt->execute()) {
          $success = 'Session updated successfully.';
        } else {
          $error = 'Failed to update session.';
        }
        $update_stmt->close();
      }
    }
  } elseif ($action === 'delete_session') {
    $session_id = intval($_POST['session_id'] ?? 0);
    if ($session_id <= 0) {
      $error = 'Invalid session ID.';
    } else {
      // Check if session is being used by students
      $check_usage = $conn->prepare("SELECT COUNT(*) as count FROM students WHERE session = (SELECT session FROM tblsession WHERE ID = ?)");
      if ($check_usage) {
        $check_usage->bind_param('i', $session_id);
        $check_usage->execute();
        $usage_result = $check_usage->get_result();
        $usage_row = $usage_result->fetch_assoc();
        
        if ($usage_row['count'] > 0) {
          $error = 'Cannot delete session. It is being used by ' . $usage_row['count'] . ' student(s).';
        } else {
          $delete_stmt = $conn->prepare("DELETE FROM tblsession WHERE ID = ?");
          if ($delete_stmt) {
            $delete_stmt->bind_param('i', $session_id);
            if ($delete_stmt->execute()) {
              $success = 'Session deleted successfully.';
            } else {
              $error = 'Failed to delete session.';
            }
            $delete_stmt->close();
          }
        }
        $check_usage->close();
      }
    }
  } elseif ($action === 'toggle_session_status') {
    $session_id = intval($_POST['session_id'] ?? 0);
    if ($session_id <= 0) {
      $error = 'Invalid session ID.';
    } else {
      // Simply activate the selected session (don't deactivate others)
      $activate_stmt = $conn->prepare("UPDATE tblsession SET is_active = 1 WHERE ID = ?");
      if ($activate_stmt) {
        $activate_stmt->bind_param('i', $session_id);
        if ($activate_stmt->execute()) {
          $success = 'Session activated successfully.';
        } else {
          $error = 'Failed to activate session.';
        }
        $activate_stmt->close();
      }
    }
  } elseif ($action === 'deactivate_session') {
    $session_id = intval($_POST['session_id'] ?? 0);
    if ($session_id <= 0) {
      $error = 'Invalid session ID.';
    } else {
      $deactivate_stmt = $conn->prepare("UPDATE tblsession SET is_active = 0 WHERE ID = ?");
      if ($deactivate_stmt) {
        $deactivate_stmt->bind_param('i', $session_id);
        if ($deactivate_stmt->execute()) {
          $success = 'Session deactivated successfully.';
        } else {
          $error = 'Failed to deactivate session.';
        }
        $deactivate_stmt->close();
      }
    }
  } elseif ($action === 'search_student') {
    $q = trim($_POST['q'] ?? '');
    if ($q !== '') {
      $like = '%' . $q . '%';
      $stmt = $conn->prepare("SELECT fullname, matric_no, session, faculty, dept, phone, request_year FROM students WHERE matric_no = ? OR fullname LIKE ? ORDER BY ID DESC LIMIT 50");
      if ($stmt) {
        $stmt->bind_param('ss', $q, $like);
        if ($stmt->execute()) { $res = $stmt->get_result(); while($row = $res->fetch_assoc()) { $found_students[] = $row; } }
        $stmt->close();
      }
      if (empty($found_students)) { $error = 'No matching students found.'; }
    }
  } elseif ($action === 'update_student') {
    $matric = trim($_POST['matric_no'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $sessionVal = trim($_POST['session'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $dept = trim($_POST['dept'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $request_year = trim($_POST['request_year'] ?? '');
    if ($matric === '') { $error = 'Matric number is required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET fullname=?, session=?, faculty=?, dept=?, phone=?, request_year = NULLIF(?, '') WHERE matric_no=?");
      if ($stmt) {
        $stmt->bind_param('sssssss', $fullname, $sessionVal, $faculty, $dept, $phone, $request_year, $matric);
        if ($stmt->execute()) { $success = 'Student record updated.'; } else { $error = 'Failed to update student.'; }
        $stmt->close();
      } else { $error = 'Failed to prepare update.'; }
    }
  } elseif ($action === 'filter_year') {
    $y = trim($_POST['year'] ?? '');
    if ($y !== '' && ctype_digit($y)) {
      $filter_year = $y;
      $stmt = $conn->prepare("SELECT fullname, matric_no, session, faculty, dept, phone, request_year FROM students WHERE request_year = ? ORDER BY ID DESC LIMIT 500");
      if ($stmt) {
        $yy = intval($y);
        $stmt->bind_param('i', $yy);
        if ($stmt->execute()) { $res = $stmt->get_result(); while($row = $res->fetch_assoc()) { $requested_year_students[] = $row; } }
        $stmt->close();
      }
      if (empty($requested_year_students)) { $error = 'No requests found for year ' . htmlspecialchars($y); }
    } else { $error = 'Enter a valid year.'; }
  } elseif ($action === 'reset_request_year') {
    $matric = trim($_POST['matric_no'] ?? '');
    if ($matric === '') { $error = 'Matric number is required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE matric_no=?");
      if ($stmt) { $stmt->bind_param('s', $matric); if ($stmt->execute()) { $success = 'Request year reset for ' . htmlspecialchars($matric) . '.'; } else { $error = 'Failed to reset.'; } $stmt->close(); }
    }
  } elseif ($action === 'set_request_year') {
    $matric = trim($_POST['matric_no'] ?? '');
    $y = trim($_POST['year'] ?? '');
    if ($matric === '' || $y === '' || !ctype_digit($y)) { $error = 'Valid matric and year required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=? WHERE matric_no=?");
      if ($stmt) { $yy = intval($y); $stmt->bind_param('is', $yy, $matric); if ($stmt->execute()) { $success = 'Request year set to ' . $yy . ' for ' . htmlspecialchars($matric) . '.'; } else { $error = 'Failed to set year.'; } $stmt->close(); }
    }
  } elseif ($action === 'allow_notify') {
    $matric = trim($_POST['matric_no'] ?? '');
    $s = trim($_POST['session'] ?? '');
    if ($matric === '') { $error = 'Matric number is required.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE matric_no=?");
      if ($stmt) { $stmt->bind_param('s', $matric); $stmt->execute(); $stmt->close(); }
      $subject = 'Clearance Re‑request Allowed';
      $msg = 'You may submit a new clearance request.' . ($s !== '' ? (' Session: ' . $s . '.') : '') . ' Day will be announced by Super Admin.';
      $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
      if ($nstmt) { $nstmt->bind_param('sss', $matric, $subject, $msg); if ($nstmt->execute()) { $success = 'Re‑request allowed and notification sent to ' . htmlspecialchars($matric) . '.'; } else { $error = 'Failed to notify student.'; } $nstmt->close(); }
    }
  } elseif ($action === 'reset_session_all_notify') {
    $s = trim($_POST['session'] ?? '');
    if ($s === '') { $error = 'Session is required to reset and notify.'; }
    else {
      $list = [];
      $sel = $conn->prepare("SELECT matric_no FROM students WHERE session=? AND request_year IS NOT NULL");
      if ($sel) { $sel->bind_param('s', $s); if ($sel->execute()) { $res = $sel->get_result(); while($row = $res->fetch_assoc()) { $list[] = $row['matric_no']; } } $sel->close(); }
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE session=? AND request_year IS NOT NULL");
      if ($stmt) { $stmt->bind_param('s', $s); $stmt->execute(); $stmt->close(); }
      if (!empty($list)) {
        $subject = 'Clearance Re‑request Allowed';
        foreach($list as $m) {
          $msg = 'You may submit a new clearance request. Session: ' . $s . '. Day will be announced by Super Admin.';
          $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
          if ($nstmt) { $nstmt->bind_param('sss', $m, $subject, $msg); $nstmt->execute(); $nstmt->close(); }
        }
      }
      $success = 'All requests reset and notifications sent for session ' . htmlspecialchars($s) . '.';
    }
  }
  elseif ($action === 'reset_session_all') {
    $s = trim($_POST['session'] ?? '');
    if ($s === '') { $error = 'Session is required to reset.'; }
    else {
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE session=? AND request_year IS NOT NULL");
      if ($stmt) { $stmt->bind_param('s', $s); if ($stmt->execute()) { $success = 'All requests reset for session ' . htmlspecialchars($s) . '.'; } else { $error = 'Failed to reset session requests.'; } $stmt->close(); }
    }
  }
  elseif ($action === 'reset_year_all') {
    $y = trim($_POST['year'] ?? '');
    if ($y === '' || !ctype_digit($y)) { $error = 'Year is required to reset.'; }
    else {
      $yy = intval($y);
      $list = [];
      $sel = $conn->prepare("SELECT matric_no FROM students WHERE request_year=?");
      if ($sel) { $sel->bind_param('i', $yy); if ($sel->execute()) { $res = $sel->get_result(); while($row = $res->fetch_assoc()) { $list[] = $row['matric_no']; } } $sel->close(); }
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE request_year=?");
      if ($stmt) { $stmt->bind_param('i', $yy); $stmt->execute(); $stmt->close(); }
      if (!empty($list)) {
        $subject = 'Clearance Re‑request Allowed';
        foreach($list as $m) {
          $msg = 'You may submit a new clearance request. Year: ' . $yy . '. Day will be announced by Super Admin.';
          $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
          if ($nstmt) { $nstmt->bind_param('sss', $m, $subject, $msg); $nstmt->execute(); $nstmt->close(); }
        }
      }
      $success = 'All requests reset and notifications sent for year ' . htmlspecialchars($yy) . '.';
    }
  } elseif ($action === 'restart_for_new_year') {
    $y = trim($_POST['year'] ?? '');
    if ($y === '' || !ctype_digit($y)) { $error = 'Year is required to restart students.'; }
    else {
      $yy = intval($y);
      $list = [];
      $sel = $conn->prepare("SELECT matric_no, fullname FROM students WHERE request_year=?");
      if ($sel) { $sel->bind_param('i', $yy); if ($sel->execute()) { $res = $sel->get_result(); while($row = $res->fetch_assoc()) { $list[] = $row; } } $sel->close(); }
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE request_year=?");
      if ($stmt) { $stmt->bind_param('i', $yy); $stmt->execute(); $stmt->close(); }
      if (!empty($list)) {
        $subject = '🎓 New Academic Year - Clearance Request Available';
        foreach($list as $student) {
          $msg = 'Dear ' . $student['fullname'] . ',\n\nYour previous clearance request for year ' . $yy . ' has been processed. You are now eligible to submit a new clearance request for the upcoming academic year.\n\n📅 The new clearance request window will be announced by the Super Admin.\n\nThank you for your patience.\n\nBest regards,\nBulehora University Administration';
          $nstmt = $conn->prepare("INSERT INTO notifications(recipient_matric, subject, message) VALUES(?, ?, ?)");
          if ($nstmt) { $nstmt->bind_param('sss', $student['matric_no'], $subject, $msg); $nstmt->execute(); $nstmt->close(); }
        }
      }
      $success = '🎉 Successfully restarted ' . count($list) . ' students for new year clearance requests! All students have been notified about the new academic year opportunity.';
    }
  } elseif ($action === 'reset_year_silent') {
    $y = trim($_POST['year'] ?? '');
    if ($y === '' || !ctype_digit($y)) { $error = 'Year is required for silent reset.'; }
    else {
      $yy = intval($y);
      $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM students WHERE request_year=?");
      $count = 0;
      if ($count_stmt) { 
        $count_stmt->bind_param('i', $yy); 
        if ($count_stmt->execute()) { 
          $res = $count_stmt->get_result(); 
          $row = $res->fetch_assoc(); 
          $count = $row['count']; 
        } 
        $count_stmt->close(); 
      }
      $stmt = $conn->prepare("UPDATE students SET request_year=NULL WHERE request_year=?");
      if ($stmt) { $stmt->bind_param('i', $yy); $stmt->execute(); $stmt->close(); }
      $success = '⚠️ Silent reset completed: ' . $count . ' students cleared for year ' . htmlspecialchars($yy) . ' (no notifications sent).';
    }
  }
}

// Ensure is_active column exists in tblsession
$check_column = mysqli_query($conn, "SHOW COLUMNS FROM tblsession LIKE 'is_active'");
if (mysqli_num_rows($check_column) == 0) {
  mysqli_query($conn, "ALTER TABLE tblsession ADD COLUMN is_active TINYINT(1) DEFAULT 0");
}

$recent_students = [];
$rs = mysqli_query($conn, "SELECT fullname, matric_no, session, faculty, dept, phone, request_year FROM students ORDER BY ID DESC LIMIT 10");
if ($rs) { while($row = mysqli_fetch_assoc($rs)) { $recent_students[] = $row; } }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Students • Super Admin</title>
  <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">

  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <style>
/* Modern Super Admin Styling */
:root {
  --primary-gradient: linear-gradient(135deg, #007bff 100%, #ccccff 0%);
  --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
  --info-gradient: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
  --danger-gradient: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
  --glass-bg: rgba(255, 255, 255, 0.95);
  --glass-border: rgba(226, 232, 240, 0.5);
  --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.08);
  --shadow-hover: 0 8px 30px rgba(163, 158, 158, 0.12);
  --border-radius: 16px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* { margin:0; padding:0; box-sizing:border-box; }

body { 
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  color: #0f172a;
  position: relative;
}

/* Animated background pattern */
body::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
  animation: backgroundFloat 15s ease-in-out infinite;
  pointer-events: none;
  z-index: -1;
}

@keyframes backgroundFloat {
  0%, 100% { opacity: 0.4; transform: scale(1) rotate(0deg); }
  50% { opacity: 0.7; transform: scale(1.1) rotate(2deg); }
}

.container { 
  max-width: 1400px; 
  margin: 0 auto; 
  padding: 0 1.5rem; 
}

/* Enhanced Navbar */
.navbar { 
  background: var(--primary-gradient);
  box-shadow: 0 4px 20px rgba(0, 123, 255, 0.3);
  padding: 1.2rem 0; 
  position: sticky; 
  top: 0; 
  z-index: 50;
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.navbar .container { 
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
}

.navbar h1 { 
  font-size: 1.4rem; 
  color: #fff; 
  font-weight: 800; 
  display: flex; 
  align-items: center; 
  gap: 0.75rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.logo-img { 
  height: 50px; 
  width: 50px; 
  object-fit: cover; 
  border-radius: 50%; 
  background: white; 
  padding: 0.2rem; 
  border: 3px solid rgba(255,255,255,0.3); 
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  animation: logoFloat 3s ease-in-out infinite;
}

@keyframes logoFloat {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-3px); }
}

.nav-links { 
  display: flex; 
  gap: 1rem; 
  align-items: center; 
  position: relative; 
}

.nav-links button { 
  background: rgba(255,255,255,0.25); 
  border: 2px solid rgba(255,255,255,0.2);
  padding: 0.5rem; 
  width: 45px; 
  height: 45px; 
  border-radius: 12px; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  cursor: pointer; 
  color: white;
  transition: var(--transition);
  font-size: 18px;
}

.nav-links button:hover {
  background: rgba(255,255,255,0.35);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

.nav-links a { 
  position: relative; 
  padding: 0.6rem 1rem; 
  border-radius: 12px; 
  color: #fff; 
  font-weight: 600; 
  text-decoration: none;
  transition: var(--transition);
  border: 2px solid transparent;
}

.nav-links a:hover {
  background: rgba(255,255,255,0.15);
  border-color: rgba(255,255,255,0.3);
  transform: translateY(-2px);
  text-shadow: 0 0 10px rgba(255,255,255,0.5);
}

.nav-highlight { 
  position: absolute; 
  border-radius: 12px; 
  background: radial-gradient(120px 120px at 50% 50%, rgba(255,255,255,0.25), rgba(255,255,255,0.1)); 
  box-shadow: 0 6px 24px rgba(0,0,0,0.15); 
  pointer-events: none; 
  opacity: 0; 
  transform: translate3d(0,0,0); 
  transition: opacity 200ms ease, left 300ms cubic-bezier(0.22, 1, 0.36, 1), top 300ms cubic-bezier(0.22, 1, 0.36, 1), width 300ms cubic-bezier(0.22, 1, 0.36, 1), height 300ms cubic-bezier(0.22, 1, 0.36, 1); 
}

/* Enhanced Layout */
.layout { 
  display: grid; 
  grid-template-columns: 1fr; 
  gap: 2rem; 
  padding: 2rem 0; 
}

/* Enhanced Cards */
.card { 
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  border-radius: var(--border-radius);
  padding: 2rem;
  border: 1px solid var(--glass-border);
  box-shadow: var(--shadow-soft);
  transition: var(--transition);
  animation: fadeInUp 0.6s ease-out;
  position: relative;
  overflow: hidden;
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
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

.card h3 {
  color: #1f2937;
  font-weight: 700;
  font-size: 1.5rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.card h4 {
  color: #374151;
  font-weight: 600;
  font-size: 1.2rem;
  margin-bottom: 0.75rem;
}

.grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); 
  gap: 2rem; 
}

/* Enhanced Form Controls */
.form-group { 
  margin-bottom: 1.25rem; 
}

label { 
  font-weight: 600; 
  display: block; 
  margin-bottom: 0.5rem;
  color: #374151;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-control {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  color: #374151;
  border: 2px solid var(--glass-border);
  border-radius: 12px;
  padding: 12px 16px;
  transition: var(--transition);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
  font-weight: 500;
  width: 100%;
}

.form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1), inset 0 2px 4px rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
  outline: none;
  background: rgba(255, 255, 255, 1);
}

.form-control-sm {
  padding: 8px 12px;
  font-size: 0.875rem;
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
  padding: 12px 20px !important;
  border: none !important;
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

.btn-primary {
  background: var(--primary-gradient) !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3) !important;
}

.btn-success {
  background: var(--success-gradient) !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3) !important;
}

.btn-warning {
  background: var(--warning-gradient) !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(237, 137, 54, 0.3) !important;
}

.btn-info {
  background: var(--info-gradient) !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(66, 153, 225, 0.3) !important;
}

.btn-danger {
  background: var(--danger-gradient) !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3) !important;
}

.btn-secondary {
  background: linear-gradient(135deg, #6b7280, #4b5563) !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3) !important;
}

.btn-sm {
  padding: 8px 16px !important;
  font-size: 0.875rem !important;
}

/* Enhanced Tables */
.table {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--shadow-soft);
  border: none;
}

.table thead th {
  background: var(--primary-gradient) !important;
  color: white !important;
  font-weight: 600 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  font-size: 0.875rem !important;
  padding: 16px !important;
  border: none !important;
}

.table tbody tr {
  transition: var(--transition);
  border: none;
}

.table tbody tr:hover {
  background-color: rgba(102, 126, 234, 0.05) !important;
  transform: scale(1.01);
}

.table td {
  padding: 12px 16px !important;
  border-color: var(--glass-border) !important;
  vertical-align: middle;
}

/* Enhanced Alerts */
.alert {
  border-radius: 12px !important;
  border: none !important;
  box-shadow: var(--shadow-soft) !important;
  animation: alertSlide 0.5s ease-out !important;
  position: relative !important;
  overflow: hidden !important;
  padding: 1rem 1.25rem !important;
  margin-bottom: 1.5rem !important;
}

.alert::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: var(--transition);
}

.alert:hover::before {
  left: 100%;
}

@keyframes alertSlide {
  0% { opacity: 0; transform: translateY(-20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.alert-success {
  background: linear-gradient(135deg, rgba(72, 187, 120, 0.15), rgba(56, 161, 105, 0.1)) !important;
  border-left: 4px solid #48bb78 !important;
  color: #065f46 !important;
}

.alert-danger {
  background: linear-gradient(135deg, rgba(245, 101, 101, 0.15), rgba(229, 62, 62, 0.1)) !important;
  border-left: 4px solid #f56565 !important;
  color: #7f1d1d !important;
}

/* Enhanced Badge */
.badge {
  border-radius: 8px;
  padding: 6px 12px;
  font-weight: 600;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.badge-info {
  background: var(--info-gradient);
  color: white;
}

.badge-success {
  background: var(--success-gradient);
  color: white;
  box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
  animation: activePulse 2s infinite;
}

.badge-secondary {
  background: linear-gradient(135deg, #6b7280, #4b5563);
  color: white;
  opacity: 0.8;
}

@keyframes activePulse {
  0%, 100% { 
    transform: scale(1); 
    box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
  }
  50% { 
    transform: scale(1.05); 
    box-shadow: 0 4px 12px rgba(72, 187, 120, 0.5);
  }
}

/* Enhanced outline buttons */
.btn-outline-success {
  border: 2px solid #48bb78;
  color: #48bb78;
  background: transparent;
}

.btn-outline-success:hover {
  background: var(--success-gradient);
  color: white;
  border-color: #48bb78;
  transform: translateY(-1px);
}

.btn-outline-secondary {
  border: 2px solid #6b7280;
  color: #6b7280;
  background: transparent;
}

.btn-outline-secondary:hover {
  background: linear-gradient(135deg, #6b7280, #4b5563);
  color: white;
  border-color: #6b7280;
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .layout {
    padding: 1rem 0;
  }
  
  .card {
    padding: 1.5rem;
  }
  
  .grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .navbar h1 {
    font-size: 1.1rem;
  }
  
  .nav-links {
    gap: 0.5rem;
  }
  
  .nav-links a {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
  }
}

/* Loading Animation */
.loading-shimmer {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* 🎯 Collapsible Sections Styling */
.card-header-custom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 0 1rem 0;
  cursor: pointer;
  transition: var(--transition);
  border-bottom: 2px solid var(--glass-border);
  margin-bottom: 1.5rem;
}

.card-header-custom:hover {
  transform: translateY(-1px);
}

.card-header-custom h3 {
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #1f2937;
  font-weight: 700;
  font-size: 1.4rem;
}

.section-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: var(--transition);
}

.status-badge.active {
  background: var(--success-gradient);
  color: white;
  box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
}

.status-badge.inactive {
  background: linear-gradient(135deg, #6b7280, #4b5563);
  color: white;
  box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}

.toggle-btn {
  background: var(--primary-gradient);
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.toggle-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
}

.toggle-btn i {
  transition: var(--transition);
  font-size: 14px;
}

.toggle-btn.collapsed i {
  transform: rotate(180deg);
}

.collapsible-content {
  max-height: 2000px;
  overflow: hidden;
  transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
  opacity: 1;
}

.collapsible-content.collapsed {
  max-height: 0;
  opacity: 0;
  margin-bottom: 0;
}

.compact-form {
  background: linear-gradient(135deg, rgba(248, 250, 252, 0.8), rgba(241, 245, 249, 0.6));
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid var(--glass-border);
  backdrop-filter: blur(10px);
}

.sessions-table-container {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 12px;
  border: 1px solid var(--glass-border);
  overflow: hidden;
  backdrop-filter: blur(10px);
}

.table-header {
  padding: 1.25rem;
  border-bottom: 1px solid var(--glass-border);
  background: linear-gradient(135deg, rgba(248, 250, 252, 0.9), rgba(241, 245, 249, 0.7));
}

.table-content {
  padding: 1.25rem;
}

/* Enhanced Animation States */
.card.minimized {
  transform: scale(0.98);
  opacity: 0.7;
}

.card.active {
  transform: scale(1);
  opacity: 1;
}

/* Section Status Indicators */
.section-controls::before {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #48bb78;
  animation: statusPulse 2s infinite;
}

.section-controls.inactive::before {
  background: #6b7280;
  animation: none;
}

@keyframes statusPulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(0.9); }
}

/* 🎯 Enhanced Results Summary Styling */
.results-summary-enhanced {
  background: linear-gradient(135deg, rgba(72, 187, 120, 0.1), rgba(56, 161, 105, 0.05));
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid rgba(72, 187, 120, 0.2);
  backdrop-filter: blur(10px);
}

.summary-info {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.info-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.year-badge {
  background: var(--info-gradient);
  color: white;
  box-shadow: 0 2px 8px rgba(66, 153, 225, 0.3);
}

.count-badge {
  background: var(--success-gradient);
  color: white;
  box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
}

.bulk-actions {
  border-top: 1px solid rgba(72, 187, 120, 0.2);
  padding-top: 1.5rem;
}

.action-group h5 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  color: #48bb78;
  font-weight: 700;
}

.action-buttons {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.action-buttons .btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 10px 20px;
  font-size: 0.875rem;
  font-weight: 600;
  border-radius: 8px;
  transition: var(--transition);
}

.action-buttons .btn:hover {
  transform: translateY(-2px) scale(1.02);
}

/* Responsive Collapsible Design */
@media (max-width: 768px) {
  .card-header-custom {
    padding: 1rem 0 0.75rem 0;
  }
  
  .card-header-custom h3 {
    font-size: 1.2rem;
  }
  
  .section-controls {
    gap: 0.75rem;
  }
  
  .toggle-btn {
    width: 35px;
    height: 35px;
  }
  
  .compact-form {
    padding: 1rem;
  }
}
  </style>
  <script>
    function setAction(btn, action){ btn.form.action='Manage_Students.php'; btn.form.elements['action'].value=action; }
  </script>
</head>
<body data-theme="<?= htmlspecialchars(isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light') ?>">

  <nav class="navbar">
    <div class="container">
      <h1>
        <img src="../home/assets/images/team/logo.png" alt="Logo" class="logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex'">
        <span style="display:none; font-weight:800;" class="logo-fallback">🏢</span>
        Manage Students — Super Admin
      </h1>
      <div class="nav-links">
        <span class="nav-highlight"></span>
        <button id="themeToggle" title="Toggle theme" aria-pressed="false">🌞</button>
        <a href="super_admin.php">Control</a>
        <a href="Manage_Students.php">Manage Students</a>
        <a href="analyes.php">Analyes</a>
        <a href="notifiction.php">Notifiction</a>
        <a href="news_notifiction.php">News Notifications</a>
        <a href="../Admin/login.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container layout">
        <!-- 📊 Requested Clearance By Year Section -->
        <div class="card shadow-sm" id="requested-by-year">
          <div class="card-header-custom" onclick="toggleSection('clearance-by-year')">
            <h3><i class="fa fa-chart-bar"></i> Requested Clearance • By Year</h3>
            <div class="section-controls">
              <span class="status-badge active" id="clearance-by-year-status">Active</span>
              <button class="toggle-btn" id="clearance-by-year-toggle">
                <i class="fa fa-chevron-up"></i>
              </button>
            </div>
          </div>
          
          <div class="collapsible-content active" id="clearance-by-year-content">
            <p style="margin:0 0 1.5rem; color:#64748b; font-style:italic;">
              <i class="fa fa-info-circle"></i> Filter and manage student clearance requests by academic year
            </p>
            
            <!-- Compact Year Filter Form -->
            <div class="compact-form">
              <h4 style="margin-bottom:1rem; color:#4299e1; display:flex; align-items:center; gap:0.5rem;">
                <i class="fa fa-filter"></i> Filter by Year
              </h4>
              <form method="post" style="display:flex; gap:0.75rem; align-items:flex-end;">
                <input type="hidden" name="action" value="filter_year">
                <div class="form-group" style="flex:1; margin-bottom:0;">
                  <label><i class="fa fa-calendar"></i> Academic Year</label>
                  <input type="number" name="year" value="<?= htmlspecialchars($filter_year ?: date('Y')) ?>" class="form-control" min="2000" max="9999" placeholder="Enter year (e.g., <?= date('Y') ?>)">
                </div>
                <button class="btn btn-info" type="submit">
                  <i class="fa fa-search"></i> View Results
                </button>
              </form>
            </div>
            <?php if (!empty($requested_year_students)): ?>
              <!-- Enhanced Results Summary with Restart Options -->
              <div class="results-summary-enhanced">
                <div class="summary-info">
                  <div class="info-badge year-badge">
                    <i class="fa fa-calendar-check-o"></i>
                    <span>Year: <?= htmlspecialchars($filter_year) ?></span>
                  </div>
                  <div class="info-badge count-badge">
                    <i class="fa fa-users"></i>
                    <span><?= count($requested_year_students) ?> Students</span>
                  </div>
                </div>
                
                <div class="bulk-actions">
                  <div class="action-group">
                    <h5 style="margin:0 0 0.75rem 0; color:#48bb78; font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px;">
                      <i class="fa fa-refresh"></i> Restart Options
                    </h5>
                    <div class="action-buttons">
                      <form method="post" action="Manage_Students.php" onsubmit="return confirm('🔄 RESTART ALL STUDENTS for NEW YEAR?\n\nThis will:\n✅ Reset all <?= count($requested_year_students) ?> students from year <?= htmlspecialchars($filter_year) ?>\n✅ Allow them to request clearance for a new academic year\n✅ Send notification to all students\n\nContinue?');" style="display:inline;">
                        <input type="hidden" name="action" value="restart_for_new_year">
                        <input type="hidden" name="year" value="<?= htmlspecialchars($filter_year) ?>">
                        <button class="btn btn-success" type="submit" title="Reset all students to allow new year clearance requests">
                          <i class="fa fa-graduation-cap"></i> Restart All for New Year
                        </button>
                      </form>
                      
                      <form method="post" action="Manage_Students.php" onsubmit="return confirm('⚠️ SILENT RESET for year <?= htmlspecialchars($filter_year) ?>?\n\nThis will:\n❌ Reset all <?= count($requested_year_students) ?> students\n❌ NO notifications will be sent\n❌ Students won\'t know about the reset\n\nContinue?');" style="display:inline; margin-left:0.5rem;">
                        <input type="hidden" name="action" value="reset_year_silent">
                        <input type="hidden" name="year" value="<?= htmlspecialchars($filter_year) ?>">
                        <button class="btn btn-warning" type="submit" title="Reset without sending notifications">
                          <i class="fa fa-times-circle"></i> Silent Reset
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php if (!empty($requested_year_students)): ?>
            <table class="table table-striped table-hover">
              <thead><tr><th>Fullname</th><th>Matric</th><th>Session</th><th>Faculty</th><th>Dept</th><th>Phone</th><th>Request Year</th><th>Permission</th></tr></thead>
              <tbody>
                <?php foreach($requested_year_students as $s): ?>
                  <tr>
                    <td><?= htmlspecialchars($s['fullname'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['matric_no'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['session'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['faculty'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['dept'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['request_year'] ?? '') ?></td>
                    <td style="display:flex; gap:0.25rem;">
                      <form method="post" style="display:inline-flex; gap:0.25rem;">
                        <input type="hidden" name="action" value="reset_request_year">
                        <input type="hidden" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>">
                        <button class="btn btn-warning btn-sm" type="submit">Allow Re‑request</button>
                      </form>
                      <form method="post" style="display:inline-flex; gap:0.25rem;">
                        <input type="hidden" name="action" value="allow_notify">
                        <input type="hidden" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>">
                        <button class="btn btn-info btn-sm" type="submit">Allow & Notify</button>
                      </form>
                      <form method="post" style="display:inline-flex; gap:0.25rem;">
                        <input type="hidden" name="action" value="set_request_year">
                        <input type="hidden" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>">
                        <input type="number" name="year" value="<?= htmlspecialchars(($filter_year ?: date('Y')) + 1) ?>" class="form-control form-control-sm" style="width:90px" min="2000" max="9999">
                        <button class="btn btn-success btn-sm" type="submit">Set Year</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div style="color:#64748b;">Use the Year filter to view all students who requested clearance for that year.</div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
    <main>
      <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

      <div class="grid">
        <!-- 👥 Manage Students Section -->
        <div class="card shadow-sm">
          <div class="card-header-custom" onclick="toggleSection('manage-students')">
            <h3><i class="fa fa-users"></i> Manage Students</h3>
            <div class="section-controls">
              <span class="status-badge active" id="manage-students-status">Active</span>
              <button class="toggle-btn" id="manage-students-toggle">
                <i class="fa fa-chevron-up"></i>
              </button>
            </div>
          </div>
          
          <div class="collapsible-content active" id="manage-students-content">
            <!-- Compact Search Form -->
            <div class="compact-form">
              <form method="post" style="display:flex; gap:0.75rem; align-items:flex-end;" action="Manage_Students.php">
                <input type="hidden" name="action" value="search_student">
                <div class="form-group" style="flex:1; margin-bottom:0;">
                  <label><i class="fa fa-search"></i> Search Students</label>
                  <input type="text" name="q" placeholder="Enter Matric No or Student Name" class="form-control">
                </div>
                <button class="btn btn-info" type="submit">
                  <i class="fa fa-search"></i> Find
                </button>
              </form>
            </div>
          <?php if (!empty($found_students)): ?>
            <table class="table table-striped table-hover">
              <thead><tr><th>Fullname</th><th>Matric</th><th>Session</th><th>Faculty</th><th>Dept</th><th>Phone</th><th>Request Year</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach($found_students as $s): ?>
                  <tr>
                    <form method="post" action="Manage_Students.php">
                      <input type="hidden" name="action" value="update_student">
                      <td><input type="text" name="fullname" value="<?= htmlspecialchars($s['fullname'] ?? '') ?>" class="form-control form-control-sm"></td>
                      <td><input type="text" name="matric_no" value="<?= htmlspecialchars($s['matric_no'] ?? '') ?>" readonly class="form-control form-control-sm"></td>
                      <td><input type="text" name="session" value="<?= htmlspecialchars($s['session'] ?? '') ?>" class="form-control form-control-sm"></td>
                      <td><input type="text" name="faculty" value="<?= htmlspecialchars($s['faculty'] ?? '') ?>" class="form-control form-control-sm"></td>
                      <td><input type="text" name="dept" value="<?= htmlspecialchars($s['dept'] ?? '') ?>" class="form-control form-control-sm"></td>
                      <td><input type="text" name="phone" value="<?= htmlspecialchars($s['phone'] ?? '') ?>" class="form-control form-control-sm"></td>
                      <td><input type="text" name="request_year" value="<?= htmlspecialchars($s['request_year'] ?? '') ?>" placeholder="YYYY" class="form-control form-control-sm"></td>
                      <td style="display:flex; gap:0.25rem;">
                        <button class="btn btn-success btn-sm" type="submit">Save</button>
                        <button class="btn btn-warning btn-sm" type="submit" onclick="setAction(this,'reset_request_year')">Reset</button>
                      </td>
                    </form>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <h4 style="margin:0.5rem 0;">Recent Students</h4>
            <table class="table table-striped table-hover">
              <thead><tr><th>Fullname</th><th>Matric</th><th>Session</th><th>Faculty</th><th>Dept</th><th>Phone</th><th>Request Year</th></tr></thead>
              <tbody>
                <?php if (empty($recent_students)): ?>
                  <tr><td colspan="7" style="text-align:center; color:#64748b;">No students</td></tr>
                <?php else: foreach($recent_students as $s): ?>
                  <tr>
                    <td><?= htmlspecialchars($s['fullname'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['matric_no'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['session'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['faculty'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['dept'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['request_year'] ?? '') ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          <?php endif; ?>
          </div>
        </div>

        <!-- 📅 Session Management System -->
        <div class="card shadow-sm" id="session-management">
          <div class="card-header-custom" onclick="toggleSection('session-management')">
            <h3><i class="fa fa-calendar"></i> Session Management System</h3>
            <div class="section-controls">
              <span class="status-badge active" id="session-management-status">Active</span>
              <button class="toggle-btn" id="session-management-toggle">
                <i class="fa fa-chevron-up"></i>
              </button>
            </div>
          </div>
          
          <div class="collapsible-content active" id="session-management-content">
            <p style="margin:0 0 1.5rem; color:#64748b; font-style:italic;">
              <i class="fa fa-info-circle"></i> Manage academic sessions for student registration
            </p>
            


            <!-- Sessions Table -->
            <div class="sessions-table-container">
              <div class="table-header">
                <h4 style="margin:0; color:#007bff; display:flex; align-items:center; gap:0.5rem;">
                  <i class="fa fa-list"></i> Existing Sessions
                </h4>
              </div>
              <div class="table-content">
              <?php
              // Fetch all sessions with academic status
              $sessions_query = "SELECT *, COALESCE(is_active, 0) as is_active FROM tblsession ORDER BY is_active DESC, ID DESC";
              $sessions_result = mysqli_query($conn, $sessions_query);
              $sessions = [];
              if ($sessions_result) {
                while($row = mysqli_fetch_assoc($sessions_result)) {
                  $sessions[] = $row;
                }
              }
              ?>
              
              <?php if (!empty($sessions)): ?>
                <table class="table table-striped table-hover">
                  <thead style="background:#007bff; color:white;">
                    <tr>
                      <th style="border-radius:8px 0 0 0;">#</th>
                      <th>Session Name</th>
                      <th>Academic Status</th>
                      <th style="border-radius:0 8px 0 0;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($sessions as $index => $session): ?>
                      <tr id="session-row-<?= $session['ID'] ?>">
                        <td><?= $index + 1 ?></td>
                        <td>
                          <span class="session-display-<?= $session['ID'] ?>"><?= htmlspecialchars($session['session']) ?></span>
                          <div class="session-edit-<?= $session['ID'] ?>" style="display:none;">
                            <form method="post" style="display:flex; gap:0.5rem;" action="Manage_Students.php">
                              <input type="hidden" name="action" value="update_session">
                              <input type="hidden" name="session_id" value="<?= $session['ID'] ?>">
                              <input type="text" name="new_session_name" value="<?= htmlspecialchars($session['session']) ?>" class="form-control form-control-sm" required>
                              <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-save"></i> Save
                              </button>
                              <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit(<?= $session['ID'] ?>)">
                                <i class="fa fa-times"></i> Cancel
                              </button>
                            </form>
                          </div>
                        </td>
                        <td>
                          <?php if ($session['is_active'] == 1): ?>
                            <form method="post" style="display:inline;" action="Manage_Students.php" onsubmit="return confirm('Deactivate session <?= htmlspecialchars($session['session']) ?>?');">
                              <input type="hidden" name="action" value="deactivate_session">
                              <input type="hidden" name="session_id" value="<?= $session['ID'] ?>">
                              <button type="submit" class="btn btn-success btn-sm" title="Click to deactivate this session">
                                <i class="fa fa-check-circle"></i> Active
                              </button>
                            </form>
                          <?php else: ?>
                            <form method="post" style="display:inline;" action="Manage_Students.php" onsubmit="return confirm('Activate session <?= htmlspecialchars($session['session']) ?>?');">
                              <input type="hidden" name="action" value="toggle_session_status">
                              <input type="hidden" name="session_id" value="<?= $session['ID'] ?>">
                              <button type="submit" class="btn btn-secondary btn-sm" title="Click to activate this session">
                                <i class="fa fa-times-circle"></i> Inactive
                              </button>
                            </form>
                          <?php endif; ?>
                        </td>
                        <td>
                          <button class="btn btn-warning btn-sm session-edit-btn-<?= $session['ID'] ?>" onclick="editSession(<?= $session['ID'] ?>)">
                            <i class="fa fa-edit"></i> Edit
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div style="text-align:center; padding:2rem; color:#64748b;">
                  <i class="fa fa-calendar-o" style="font-size:3rem; margin-bottom:1rem; opacity:0.5;"></i>
                  <p>No sessions found. Add your first session above.</p>
                </div>
              <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="../js/jquery-2.1.1.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script>
    const themeToggle = document.getElementById('themeToggle');
    const bodyEl = document.body;
    function setCookie(name, value, days) {
      const d = new Date();
      d.setTime(d.getTime() + (days*24*60*60*1000));
      document.cookie = name + "=" + value + ";path=/;expires=" + d.toUTCString();
    }
    function getCookie(name) {
      const pairs = document.cookie.split(';').map(s => s.trim());
      for (let p of pairs) { if (!p) continue; const [k, v] = p.split('='); if (k === name) return v; }
      return null;
    }
    function applyTheme(theme) {
      bodyEl.setAttribute('data-theme', theme);
      document.documentElement.setAttribute('data-theme', theme);
      if (themeToggle) {
        themeToggle.textContent = theme === 'dark' ? '🌙' : '🌞';
        themeToggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
      }
    }
    (function initTheme(){
      let t = bodyEl.getAttribute('data-theme') || getCookie('theme') || 'light';
      applyTheme(t);
    })();
    if (themeToggle) {
      themeToggle.addEventListener('click', function(){
        const current = bodyEl.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        setCookie('theme', next, 365);
      });
    }
    const navLinks = document.querySelector('.nav-links');
    const highlight = document.querySelector('.nav-highlight');
    function moveHighlightTo(el){
      if (!navLinks || !highlight || !el) return;
      const cr = navLinks.getBoundingClientRect();
      const r = el.getBoundingClientRect();
      const left = r.left - cr.left;
      const top = r.top - cr.top;
      highlight.style.left = left + 'px';
      highlight.style.top = top + 'px';
      highlight.style.width = r.width + 'px';
      highlight.style.height = r.height + 'px';
      highlight.style.opacity = 1;
    }
    function hideHighlight(){ if (highlight) highlight.style.opacity = 0; }
    if (navLinks && highlight){
      navLinks.addEventListener('mouseleave', hideHighlight);
      const items = navLinks.querySelectorAll('a, button');
      items.forEach(function(it){
        it.addEventListener('mouseenter', function(){ moveHighlightTo(it); });
        it.addEventListener('mousemove', function(e){
          const r = it.getBoundingClientRect();
          const relX = e.clientX - r.left;
          const relY = e.clientY - r.top;
          const cx = Math.max(0, Math.min(1, relX / Math.max(1, r.width))) * 100;
          const cy = Math.max(0, Math.min(1, relY / Math.max(1, r.height))) * 100;
          highlight.style.background = 'radial-gradient(120px 120px at ' + cx + '% ' + cy + '%, rgba(255,255,255,0.28), rgba(255,255,255,0.12))';
        });
      });
    }

    // 🎯 Collapsible Sections Management
    function toggleSection(sectionId) {
      const content = document.getElementById(sectionId + '-content');
      const toggle = document.getElementById(sectionId + '-toggle');
      const status = document.getElementById(sectionId + '-status');
      const card = content.closest('.card');
      
      if (content.classList.contains('collapsed')) {
        // Expand section
        content.classList.remove('collapsed');
        toggle.classList.remove('collapsed');
        status.textContent = 'Active';
        status.classList.remove('inactive');
        status.classList.add('active');
        card.classList.remove('minimized');
        card.classList.add('active');
        
        // Save state
        localStorage.setItem(sectionId + '-state', 'active');
      } else {
        // Collapse section
        content.classList.add('collapsed');
        toggle.classList.add('collapsed');
        status.textContent = 'Inactive';
        status.classList.remove('active');
        status.classList.add('inactive');
        card.classList.add('minimized');
        card.classList.remove('active');
        
        // Save state
        localStorage.setItem(sectionId + '-state', 'inactive');
      }
    }

    // Load saved states on page load
    function loadSectionStates() {
      const sections = ['manage-students', 'session-management', 'clearance-by-year'];
      
      sections.forEach(sectionId => {
        const savedState = localStorage.getItem(sectionId + '-state');
        if (savedState === 'inactive') {
          toggleSection(sectionId);
        }
      });
    }

    // ✅ Session Management JavaScript Functions
    function editSession(sessionId) {
      // Hide display mode and show edit mode
      document.querySelector('.session-display-' + sessionId).style.display = 'none';
      document.querySelector('.session-edit-' + sessionId).style.display = 'block';
      document.querySelector('.session-edit-btn-' + sessionId).style.display = 'none';
      
      // Focus on the input field
      const input = document.querySelector('.session-edit-' + sessionId + ' input[name="new_session_name"]');
      if (input) {
        input.focus();
        input.select();
      }
    }

    function cancelEdit(sessionId) {
      // Show display mode and hide edit mode
      document.querySelector('.session-display-' + sessionId).style.display = 'inline';
      document.querySelector('.session-edit-' + sessionId).style.display = 'none';
      document.querySelector('.session-edit-btn-' + sessionId).style.display = 'inline-block';
    }

    // Enhanced form validation for session management
    document.addEventListener('DOMContentLoaded', function() {
      // Load section states
      loadSectionStates();
      
      // Add keyboard shortcuts for section management
      document.addEventListener('keydown', function(e) {
        // Ctrl + 1 to toggle Manage Students
        if (e.ctrlKey && e.key === '1') {
          e.preventDefault();
          toggleSection('manage-students');
        }
        // Ctrl + 2 to toggle Session Management
        if (e.ctrlKey && e.key === '2') {
          e.preventDefault();
          toggleSection('session-management');
        }
        // Ctrl + 3 to toggle Clearance by Year
        if (e.ctrlKey && e.key === '3') {
          e.preventDefault();
          toggleSection('clearance-by-year');
        }
        // Ctrl + 3 to toggle Clearance by Year
        if (e.ctrlKey && e.key === '3') {
          e.preventDefault();
          toggleSection('clearance-by-year');
        }
      });
      
      // Add smooth scroll to sections when toggled
      const sectionHeaders = document.querySelectorAll('.card-header-custom');
      sectionHeaders.forEach(header => {
        header.addEventListener('click', function() {
          setTimeout(() => {
            this.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 100);
        });
      });
      // Add session form validation
      const addSessionForm = document.querySelector('form[action="Manage_Students.php"] input[name="action"][value="add_session"]');
      if (addSessionForm) {
        const form = addSessionForm.closest('form');
        form.addEventListener('submit', function(e) {
          const sessionName = form.querySelector('input[name="session_name"]').value.trim();
          if (sessionName === '') {
            e.preventDefault();
            alert('⚠️ Please enter a session name');
            return false;
          }
          
          // Basic format validation (optional)
          if (!/^\d{4}\/\d{4}$/.test(sessionName) && !/^\d{4}-\d{4}$/.test(sessionName)) {
            const confirm = window.confirm('⚠️ Session format doesn\'t match typical format (YYYY/YYYY or YYYY-YYYY). Continue anyway?');
            if (!confirm) {
              e.preventDefault();
              return false;
            }
          }
        });
      }

      // Add keyboard shortcuts for session editing
      document.addEventListener('keydown', function(e) {
        // Escape key to cancel editing
        if (e.key === 'Escape') {
          const editForms = document.querySelectorAll('[class*="session-edit-"]');
          editForms.forEach(form => {
            if (form.style.display !== 'none') {
              const sessionId = form.className.match(/session-edit-(\d+)/)[1];
              cancelEdit(sessionId);
            }
          });
        }
      });

      // Auto-save on Enter key in edit mode
      const editInputs = document.querySelectorAll('.session-edit input[name="new_session_name"]');
      editInputs.forEach(input => {
        input.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            this.closest('form').submit();
          }
        });
      });
    });
  </script>
</body>
</html>
