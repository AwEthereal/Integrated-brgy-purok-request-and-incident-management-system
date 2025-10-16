-- Clear all requests and reports while keeping users
-- Run this in your MySQL client or phpMyAdmin

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Delete all requests
TRUNCATE TABLE requests;

-- Delete all incident reports
TRUNCATE TABLE incident_reports;

-- Delete purok change requests (if exists)
TRUNCATE TABLE purok_change_requests;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify deletion
SELECT COUNT(*) as request_count FROM requests;
SELECT COUNT(*) as incident_count FROM incident_reports;
SELECT COUNT(*) as user_count FROM users;
