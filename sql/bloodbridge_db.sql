CREATE DATABASE IF NOT EXISTS `bloodbridge_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bloodbridge_db`;

-- 1. users table
DROP TABLE IF EXISTS `blood_requests`;
DROP TABLE IF EXISTS `blood_inventory`;
DROP TABLE IF EXISTS `donations`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `alert_config`;

CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'technician', 'donor', 'patient') NOT NULL,
  `blood_type` ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') DEFAULT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `hospital_location` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. donations table
CREATE TABLE `donations` (
  `donation_id` INT AUTO_INCREMENT PRIMARY KEY,
  `donor_id` INT NOT NULL,
  `technician_id` INT NOT NULL,
  `blood_type` ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
  `units_ml` INT NOT NULL DEFAULT 450,
  `donation_date` DATE NOT NULL,
  `next_eligible_date` DATE GENERATED ALWAYS AS (DATE_ADD(`donation_date`, INTERVAL 56 DAY)) STORED,
  `notes` TEXT,
  FOREIGN KEY (`donor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`technician_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. blood_inventory table
CREATE TABLE `blood_inventory` (
  `unit_id` INT AUTO_INCREMENT PRIMARY KEY,
  `blood_type` ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
  `source_donation_id` INT DEFAULT NULL,
  `status` ENUM('available', 'reserved', 'dispatched', 'expired') NOT NULL DEFAULT 'available',
  `collected_date` DATE NOT NULL,
  `expiry_date` DATE GENERATED ALWAYS AS (DATE_ADD(`collected_date`, INTERVAL 35 DAY)) STORED,
  `stored_by` INT NOT NULL,
  FOREIGN KEY (`source_donation_id`) REFERENCES `donations` (`donation_id`) ON DELETE SET NULL,
  FOREIGN KEY (`stored_by`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. blood_requests table
CREATE TABLE `blood_requests` (
  `request_id` INT AUTO_INCREMENT PRIMARY KEY,
  `requester_name` VARCHAR(100) NOT NULL,
  `requester_contact` VARCHAR(100) NOT NULL,
  `blood_type` ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
  `units_needed` INT NOT NULL,
  `urgency` ENUM('normal', 'urgent', 'critical') NOT NULL DEFAULT 'normal',
  `status` ENUM('pending', 'approved', 'fulfilled', 'rejected') NOT NULL DEFAULT 'pending',
  `requested_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `processed_by` INT DEFAULT NULL,
  `patient_id` INT DEFAULT NULL,
  `notes` TEXT,
  FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. alert_config table
CREATE TABLE `alert_config` (
  `blood_type` ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL PRIMARY KEY,
  `critical_threshold` INT NOT NULL DEFAULT 5,
  `warning_threshold` INT NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Data

-- Config default alert thresholds
INSERT INTO `alert_config` (`blood_type`, `critical_threshold`, `warning_threshold`) VALUES
('A+', 5, 10),
('A-', 5, 10),
('B+', 5, 10),
('B-', 5, 10),
('AB+', 5, 10),
('AB-', 5, 10),
('O+', 5, 10),
('O-', 5, 10);

-- Insert Seed Users
-- admin / adminpassword
-- technician / techpassword
-- donor / donorpassword (blood type O+)
-- patient / patientpassword (blood type AB-)
INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `blood_type`, `phone`) VALUES
(1, 'Admin Eshan', 'admin@bloodbridge.com', '$2y$12$oRC2UmOu7H.PBhGALBucoOS84AcxcBM8Ly.S7JHuze3eCXT9eLsoO', 'admin', NULL, '01712345678'),
(2, 'Lab Tech Eshan', 'eshan@gmail.com', '$2y$12$oRC2UmOu7H.PBhGALBucoOS84AcxcBM8Ly.S7JHuze3eCXT9eLsoO', 'technician', NULL, '01812345678'),
(3, 'Donor Milton', 'milton@gmail.com', '$2y$12$oRC2UmOu7H.PBhGALBucoOS84AcxcBM8Ly.S7JHuze3eCXT9eLsoO', 'donor', 'O+', '01912345678'),
(4, 'Patient Tisha', 'tisha@gmail.com', '$2y$12$oRC2UmOu7H.PBhGALBucoOS84AcxcBM8Ly.S7JHuze3eCXT9eLsoO', 'patient', 'AB-', '01512345678');

-- Insert Seed Donations (to set donor Mir's eligibility/history)
INSERT INTO `donations` (`donation_id`, `donor_id`, `technician_id`, `blood_type`, `units_ml`, `donation_date`, `notes`) VALUES
(1, 3, 2, 'O+', 450, DATE_SUB(CURDATE(), INTERVAL 60 DAY), 'Regular donation, fully healthy');

-- Insert Blood Inventory
-- Let's put some O+ blood linked to Mir's donation. Expired? 60 days ago means collected 60 days ago -> expired.
INSERT INTO `blood_inventory` (`unit_id`, `blood_type`, `source_donation_id`, `status`, `collected_date`, `stored_by`) VALUES
(1, 'O+', 1, 'expired', DATE_SUB(CURDATE(), INTERVAL 60 DAY), 2);

-- Let's add some available blood units (O+, A+, AB-)
INSERT INTO `blood_inventory` (`unit_id`, `blood_type`, `source_donation_id`, `status`, `collected_date`, `stored_by`) VALUES
(2, 'O+', NULL, 'available', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 2),
(3, 'O+', NULL, 'available', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 2),
(4, 'A+', NULL, 'available', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 2),
(5, 'AB-', NULL, 'available', DATE_SUB(CURDATE(), INTERVAL 12 DAY), 2);

-- Insert Blood Requests
INSERT INTO `blood_requests` (`request_id`, `requester_name`, `requester_contact`, `blood_type`, `units_needed`, `urgency`, `status`, `patient_id`, `notes`) VALUES
(1, 'Patient Karim', '01512345678', 'AB-', 1, 'urgent', 'pending', 4, 'Scheduled surgery this week'),
(2, 'Dhaka Medical College', 'dmch@gov.bd', 'O+', 2, 'critical', 'pending', NULL, 'Emergency ICU case');
