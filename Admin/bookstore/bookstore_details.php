<?php 
/* Local Database*/
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_clearance";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Approve / Reject action
if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];
    $sql = "UPDATE students SET status='$action' WHERE id=$id";
    $conn->query($sql);
    header("Location: bookstore.php");
    exit;
}

// ✅ FIX: Get student record
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Student ID missing in URL");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM students WHERE id=$id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Student not found");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Details</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        html, body { height: 100%; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #ffffff; color: #0f172a; }
        .admin { background: linear-gradient(135deg, #007bff 0%, #ccccff 100%); box-shadow: 0 4px 20px #007bff; padding: 1.25rem 0; position: sticky; top: 0; z-index: 50; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }
        .admin h1 { font-size: 1.25rem; color: #fff; font-weight:800; display:flex; align-items:center; gap:0.75rem; }
        .page { padding: 1.25rem 0; display:flex; justify-content:center; }
        .card { width: 100%; max-width: 840px; background: #ffffff; border: 1px solid #9f6540; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); padding: 24px; }
        .card h2 { margin: 0 0 12px; font-size: 24px; font-weight: 700; color: #0f172a; }
        .card p { margin: 8px 0; }
        .card img { width: 160px; height: 160px; object-fit: cover; border-radius: 12px; border: 1px solid #e2e8f0; }
        .actions { margin-top: 16px; display: flex; gap: 10px; }
        .back-btn { display: inline-block; padding: 10px 14px; border-radius: 10px; background:#9f6540; color: #fff; text-decoration: none; border:none; }
        .back-btn:hover { filter: brightness(1.08); }
    </style>
</head>
<body>
<div class="admin">
  <div class="container">
    <h1>Cafeteria • Student Details</h1>
  </div>
</div>
<div class="page">
<div class="card">
    <h2>Student Details</h2>

    <p><b></b><br>
        <img src="../<?php echo $row['photo']; ?>" width="150">
    </p>

    <p><b>Fullname:</b> <?php echo $row['fullname']; ?></p>
    <p><b>Matric No:</b> <?php echo $row['matric_no']; ?></p>
    <p><b>Session:</b> <?php echo $row['session']; ?></p>
    <p><b>Faculty:</b> <?php echo $row['faculty']; ?></p>
    <p><b>Dept:</b> <?php echo $row['dept']; ?></p>
    <p><b>Phone:</b> <?php echo $row['phone']; ?></p>

    <div class="actions">
        <a href="javascript:history.back()" class="back-btn">Back</a>
    </div>
</div>
</div>
</body>
</html>
