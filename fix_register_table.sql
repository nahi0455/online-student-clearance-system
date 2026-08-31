-- Fix register table structure to match students table
-- Add missing columns to register table

ALTER TABLE `register` 
ADD COLUMN `email` varchar(100) DEFAULT NULL AFTER `phone`,
ADD COLUMN `address` text DEFAULT NULL AFTER `email`,
ADD COLUMN `is_department_approved` tinyint(1) DEFAULT 0 AFTER `address`,
ADD COLUMN `is_library_approved` tinyint(1) DEFAULT 0 AFTER `is_department_approved`,
ADD COLUMN `is_bookstore_approved` tinyint(1) DEFAULT 0 AFTER `is_library_approved`,
ADD COLUMN `is_dormitory_approved` tinyint(1) DEFAULT 0 AFTER `is_bookstore_approved`,
ADD COLUMN `is_cafeteria_approved` tinyint(1) DEFAULT 0 AFTER `is_dormitory_approved`,
ADD COLUMN `is_sport_approved` tinyint(1) DEFAULT 0 AFTER `is_cafeteria_approved`,
ADD COLUMN `is_dean_approved` tinyint(1) DEFAULT 0 AFTER `is_sport_approved`,
ADD COLUMN `is_police_approved` tinyint(1) DEFAULT 0 AFTER `is_dean_approved`,
ADD COLUMN `is_registrar_approved` tinyint(1) DEFAULT 0 AFTER `is_police_approved`,
ADD COLUMN `request_year` int(11) DEFAULT NULL AFTER `is_registrar_approved`;

-- Copy approval data from students table to register table where matric_no matches
UPDATE `register` r 
INNER JOIN `students` s ON r.matric_no = s.matric_no 
SET 
    r.is_department_approved = s.is_department_approved,
    r.is_library_approved = s.is_library_approved,
    r.is_bookstore_approved = s.is_bookstore_approved,
    r.is_dormitory_approved = s.is_dormitory_approved,
    r.is_cafeteria_approved = s.is_cafeteria_approved,
    r.is_sport_approved = s.is_sport_approved,
    r.is_dean_approved = s.is_dean_approved,
    r.is_police_approved = s.is_police_approved,
    r.is_registrar_approved = s.is_registrar_approved,
    r.request_year = s.request_year;