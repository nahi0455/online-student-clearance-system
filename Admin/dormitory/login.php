<?php
session_start();
include('connect.php');
error_reporting(0);

if (isset($_POST['btnlogin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['txtusername']);
    $password = mysqli_real_escape_string($conn, $_POST['txtpassword']);

    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password' AND status='Active'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        // ✅ Store all important session data
        $_SESSION['admin-username'] = $row['username'];
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];            // role added
        $_SESSION['department'] = $row['department']; // department added
        $_SESSION['photo'] = $row['photo'];

        // ✅ Redirect based on role (optional)
        if ($row['role'] == 'super_admin') {
            header("Location: dormitory.php");
        } elseif ($row['role'] == 'dormitory') {
            header("Location: dormitory.php");
        } else {
            header("Location: office/index.php");
        }
        exit();

    } else {
        $_SESSION['error'] = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="login_style.css">
</head>
<body>

<div class="login-box">
  <h2>Admin Login</h2>

  <?php if(!empty($_SESSION['error'])) { ?>
      <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
      <?php unset($_SESSION['error']); } ?>

  <form method="post" action="">
    <div class="user-box">
      <input type="text" name="txtusername" required="">
      <label>Username</label>
    </div>
    <div class="user-box">
      <input type="password" name="txtpassword" required="">
      <label>Password</label>
    </div>
    <button type="submit" name="btnlogin">Login</button>
  </form>
</div>

</body>
</html>
