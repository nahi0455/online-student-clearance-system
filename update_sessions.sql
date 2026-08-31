USE student_clearance;

-- Add more recent academic sessions
INSERT INTO tblsession (session) VALUES 
('2023/2024'),
('2024/2025'), 
('2025/2026')
ON DUPLICATE KEY UPDATE session=VALUES(session);