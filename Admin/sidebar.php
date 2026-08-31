<div class="main-sidebar sidebar-dark-primary elevation-4" style="min-height:100vh;">
    <a href="index.php" class="brand-link">
        <img src="images/logo.png" alt="University Logo" class="brand-logo" onerror="this.style.display='none'">
        <span class="brand-text">BULE HORA UNIVERSITY</span>
    </a>
    <div class="sidebar">

        <!-- USER PANEL -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo $_SESSION['photo']; ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo $_SESSION['fullname']; ?></a>
            </div>
        </div>

        <nav>
            <ul class="nav nav-pills nav-sidebar flex-column">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link text-white">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>

             

                <!-- Admin Management -->
                <li class="nav-header">Admin Management</li>
                <li class="nav-item">
                    <a href="student-record.php" class="nav-link text-white">
                        <i class="fas fa-users me-2"></i> All Students
                    </a>
                </li>
                <li class="nav-item">
                    <a href="add-student.php" class="nav-link text-white">
                        <i class="fas fa-user-plus me-2"></i> Add Student
                    </a>
                </li>
                <li class="nav-item">
                    <a href="student-clearance.php" class="nav-link text-white">
                        <i class="fas fa-check-circle me-2"></i> Pending Clearances
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-record.php" class="nav-link text-white">
                        <i class="fas fa-user-shield me-2"></i> All Admins
                    </a>
                </li>
                <li class="nav-item">
                    <a href="add-admin.php" class="nav-link text-white">
                        <i class="fas fa-user-cog me-2"></i> Add Admin
                    </a>
                </li>
                <li class="nav-item">
                    <a href="changepassword.php" class="nav-link text-white">
                        <i class="fas fa-key me-2"></i> Change Password
                    </a>
                </li>

            <li class="nav-item">
              <a href="../logout.php" class="nav-link text-danger">
                <i class="nav-icon fas fa-power-off"></i>
                <p>Logout</p>
              </a>
            </li>
            </ul>
        </nav>

    </div>
</div>


