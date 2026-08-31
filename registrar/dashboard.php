
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link rel="stylesheet" href="../Admin/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../Admin/plugins/fontawesome-free/css/all.min.css">

<style>
body{background:#f5f7fa;}
.box{padding:20px;background:white;margin:10px;border-radius:10px;text-align:center;}
</style>

</head>
<body>

<h2 style="padding:20px;">
Welcome, <?php echo htmlspecialchars($row['fullname'] ?? $username); ?>
</h2>

<div style="display:flex;gap:10px;flex-wrap:wrap;padding:20px;">

<div class="box">Total Students<br><b><?php echo $total_students; ?></b></div>
<div class="box">Cleared<br><b><?php echo $cleared_students; ?></b></div>
<div class="box">Pending<br><b><?php echo $pending_students; ?></b></div>
<div class="box">Sessions<br><b><?php echo $total_sessions; ?></b></div>

</div>

<h3 style="padding:20px;">Recent Students</h3>

<table border="1" cellpadding="10" style="margin:20px;">
<tr>
<th>#</th>
<th>Name</th>
<th>Matric</th>
<th>Status</th>
</tr>

<?php foreach($recent_students as $i => $s): ?>
<tr>
<td><?php echo $i+1; ?></td>
<td><?php echo htmlspecialchars($s['fullname']); ?></td>
<td><?php echo htmlspecialchars($s['matric_no']); ?></td>
<td>
<?php echo ($s['is_registrar_approved'] == '1') ? "Cleared" : "Pending"; ?>
</td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>