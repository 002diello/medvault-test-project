CREATE DATABASE IF NOT EXISTS `medic_vault_db`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `medic_vault_db`;

DROP TABLE IF EXISTS `patient_records`;

CREATE TABLE `patient_records` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `illness_history` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `staff_credentials`;

CREATE TABLE `staff_credentials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `auth_key_hash` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `patient_records` (`name`, `illness_history`) VALUES
('John Doe', 'DIAGNOSIS: Stage-2 Carcinoma. TREATMENT: Chemotherapy cycle 1. STATUS: Critical.'),
('Jane Smith', 'DIAGNOSIS: Stage-2 Carcinoma. TREATMENT: Radiation therapy. STATUS: Stable.'),
('Robert Thorne', 'DIAGNOSIS: Acute Type-2 Diabetes. TREATMENT: Insulin regimen. STATUS: Managed.'),
('Siti Aminah', 'DIAGNOSIS: Acute Type-2 Diabetes. TREATMENT: Metformin regimen. STATUS: Monitored.');