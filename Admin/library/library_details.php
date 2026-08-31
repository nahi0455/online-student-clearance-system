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
        html, body { height: 100%; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue", Arial, sans-serif; background: #0b1220; color: #e2e8f0; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { width: 100%; max-width: 720px; background: #0f172a; border: 1px solid rgba(148,163,184,.25); border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,.45); padding: 24px; }
        .card h2 { margin: 0 0 12px; font-size: 24px; font-weight: 700; color: #e2e8f0; }
        .card p { margin: 8px 0; }
        .card img { width: 160px; height: 160px; object-fit: cover; border-radius: 12px; border: 1px solid rgba(148,163,184,.25); }
        .actions { margin-top: 16px; display: flex; gap: 10px; }
        .back-btn { display: inline-block; padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(148,163,184,.35); color: #e2e8f0; text-decoration: none; }
        .back-btn:hover { border-color: #38bdf8; }
    </style>
</head>
<body>
<div class="card">
    <h2>Student Details</h2>

    <p><b>Photo:</b><br>
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

</body>
</html>
