USE student_clearance;

-- Create a super admin user
INSERT INTO admin (username, password, designation, fullname, email, status, photo, role, department) 
VALUES ('superadmin', 'admin123', 'Super Administrator', 'Super Admin', 'superadmin@university.edu', 'Active', 'uploads/avatar_nick.png', 'super_admin', NULL)
ON DUPLICATE KEY UPDATE 
    password = VALUES(password),
    role = VALUES(role),
    status = VALUES(status);

-- Also update the existing admin to be super_admin if needed
UPDATE admin SET role = 'super_admin' WHERE username = 'admin' AND role != 'super_admin';