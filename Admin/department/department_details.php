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
        body { font-family: Arial, sans-serif; margin: 20px; }
        .card { border: 1px solid #ddd; border-radius: 10px; padding: 20px; width: 400px; }
        .actions button { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .accept { background: #27ae60; color: white; }
        .reject { background: #e74c3c; color: white; }
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

    <form method="post">
<a href="bookstore.php?dept=<?php echo $_SESSION['department']; ?>">
    <button type="button">Back</button>
</a>
    </form>
</div>

</body>
</html>
