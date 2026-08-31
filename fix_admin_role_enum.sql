USE student_clearance;

-- Add super_admin to the role ENUM in admin table
ALTER TABLE admin 
MODIFY COLUMN role ENUM(
    'department_head',
    'library',
    'bookstore',
    'dormitory',
    'cafeteria',
    'sport',
    'dean',
    'police',
    'registrar',
    'super_admin'
) NOT NULL DEFAULT 'department_head';

-- Now create/update the super admin user
INSERT INTO admin (username, password, designation, fullname, email, status, photo, role, department) 
VALUES ('superadmin', 'admin123', 'Super Administrator', 'Super Admin', 'superadmin@university.edu', 'Active', 'uploads/avatar_nick.png', 'super_admin', NULL)
ON DUPLICATE KEY UPDATE 
    password = VALUES(password),
    role = VALUES(role),
    status = VALUES(status),
    designation = VALUES(designation);

-- Also update existing admin to super_admin if needed
UPDATE admin SET role = 'super_admin' WHERE username = 'admin' LIMIT 1;