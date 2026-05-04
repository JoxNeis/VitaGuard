-- CREATE SCHEMA IF NOT EXISTS vitaguard;

-- USE vitaguard;
-- -- ERD Ver 0.1.1

-- -- Table Provinces
-- CREATE TABLE `provinces` (
--     `id` INT AUTO_INCREMENT PRIMARY KEY,
--     `name` VARCHAR(200) NOT NULL
-- );

-- -- Table Cities
-- CREATE TABLE `cities` (
--     `id` INT AUTO_INCREMENT PRIMARY KEY,
--     `province_id` INT NOT NULL,
--     `name` VARCHAR(200) NOT NULL,
--     CONSTRAINT `fk_cities_provinces` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
-- );

-- -- Table Districts
-- CREATE TABLE `districts` (
--     `id` INT AUTO_INCREMENT PRIMARY KEY,
--     `city_id` INT NOT NULL,
--     `name` VARCHAR(200) NOT NULL,
--     CONSTRAINT `fk_districts_cities` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
-- );

-- -- Table Users
-- CREATE TABLE `users` (
--     `username` VARCHAR(50) PRIMARY KEY,
--     `password_hashed` VARCHAR(255) NOT NULL,

--     `email` VARCHAR(100) NOT NULL UNIQUE,
--     `phone_number` VARCHAR(20),

--     `role` ENUM('member', 'doctor','facility_admin','admin') DEFAULT 'member',
--     `status` ENUM('active', 'suspended') DEFAULT 'active',

--     `last_login_at` TIMESTAMP NULL,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
--     `deleted_at` TIMESTAMP NULL DEFAULT NULL
-- );

-- -- Table Members
-- CREATE TABLE `members` (
--     `username` VARCHAR(50) PRIMARY KEY,

--     `first_name` VARCHAR(100) NOT NULL,
--     `middle_name` VARCHAR(100),
--     `last_name` VARCHAR(100) NOT NULL,

--     `gender` ENUM('male', 'female') NOT NULL,
--     `date_of_birth` DATE NOT NULL,

--     `address` VARCHAR(255) NOT NULL,
--     `district_id` INT NOT NULL,

--     `blood_type` ENUM(
--         'A+',
--         'A-',
--         'B+',
--         'B-',
--         'AB+',
--         'AB-',
--         'O+',
--         'O-'
--     ),
--     `weight_kg` DECIMAL(5, 2) NOT NULL,
--     `height_cm` DECIMAL(5, 2) NOT NULL,
--     `smoking_status` ENUM('never', 'former', 'current') NOT NULL,
--     `alcohol_consumption` ENUM(
--         'none',
--         'light',
--         'moderate',
--         'heavy'
--     ) NOT NULL,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
--     `deleted_at` TIMESTAMP NULL DEFAULT NULL,

--     CONSTRAINT `fk_members_users` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
--     CONSTRAINT `fk_members_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
-- );

-- -- Table Medical Histories
-- CREATE TABLE `medical_histories` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

--     `member_username` VARCHAR(50) NOT NULL,

--     `diagnosed_date` DATE NOT NULL,
--     `description` TEXT NOT NULL,
--     `inputted_by` VARCHAR(50) NOT NULL,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
--     `deleted_at` TIMESTAMP NULL DEFAULT NULL,

--     CONSTRAINT `fk_medical_histories_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
--     CONSTRAINT `fk_medical_histories_users` FOREIGN KEY (`inputted_by`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
-- );

-- -- Table Medicines
-- CREATE TABLE `medicines` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

--     `name` VARCHAR(100) NOT NULL,
--     `dosage_form` ENUM(
--         'tablet',
--         'capsule',
--         'syrup',
--         'injection',
--         'ointment') NOT NULL, 
--     `medicine_class` ENUM(
--         'over_the_counter',
--         'limited_otc',
--         'prescription',
--         'narcotic',
--         'psychotropic'
--         ) NOT NULL,
--     `description` TEXT NOT NULL,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

--     UNIQUE(`name`,`dosage_form`)
-- );

-- -- Table Allergens
-- CREATE TABLE `allergens` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `name` VARCHAR(100) NOT NULL UNIQUE
-- );

-- -- Table Member Allergies
-- CREATE TABLE `member_allergies` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

--     `member_username` VARCHAR(50) NOT NULL,

--     `allergen_id` BIGINT NOT NULL,
--     `severity` ENUM('mild', 'moderate', 'severe'),
--     `notes` TEXT,

--     `inputted_by` VARCHAR(50) NOT NULL,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
--     `deleted_at` TIMESTAMP NULL DEFAULT NULL,

--     CONSTRAINT `fk_member_allergies_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
--     CONSTRAINT `fk_member_allergies_allergen` FOREIGN KEY (`allergen_id`) REFERENCES `allergens` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
--     CONSTRAINT `fk_member_allergies_inputted_by` FOREIGN KEY (`inputted_by`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
-- );

-- -- Table Doctors
-- CREATE TABLE `doctors` (
--     `username` VARCHAR(50) PRIMARY KEY,

--     `prefix_name` VARCHAR(20),
--     `first_name` VARCHAR(100) NOT NULL,
--     `middle_name` VARCHAR(100),
--     `last_name` VARCHAR(100) NOT NULL,
--     `suffix_name` VARCHAR(100),

--     `date_of_birth` DATE NOT NULL,

--     `address` VARCHAR(255) NOT NULL,
--     `district_id` INT NOT NULL,

--     `rating_avg` DECIMAL(3, 2),
--     `rating_count` INT DEFAULT 0,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
--     `deleted_at` TIMESTAMP NULL DEFAULT NULL,

--     CONSTRAINT `fk_doctors_users` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
--     CONSTRAINT `fk_doctors_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
-- );

-- -- Table Specialties
-- CREATE TABLE `specialties` (
--     `id` INT AUTO_INCREMENT PRIMARY KEY,
--     `name` VARCHAR(150) UNIQUE
-- );

-- -- Table Doctor Specialties
-- CREATE TABLE `doctor_specialties` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `username` VARCHAR(50),

--     `specialty_id` INT,

--     CONSTRAINT `fk_doctor_specialties_username` FOREIGN KEY (`username`) REFERENCES `doctors` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
--     CONSTRAINT `fk_doctor_specialties_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
--     UNIQUE (`username`, `specialty_id`)
-- );

-- -- Table Facilities
-- CREATE TABLE `facilities` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `name` VARCHAR(255) NOT NULL,

--     `address` VARCHAR(255) NOT NULL,
--     `district_id` INT,
--     `phone_number` VARCHAR(20),

--     `rating_avg` DECIMAL(3, 2),
--     `rating_count` INT DEFAULT 0,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
--     CONSTRAINT `fk_facilities_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT
-- );

-- -- Table Facility_Admins
-- CREATE TABLE `facility_admins`(
--     `username` VARCHAR(50) PRIMARY KEY,
--     `facility_id` BIGINT PRIMARY KEY,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
--     CONSTRAINT `fk_facility_admins_user` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE RESTRICT,
--     CONSTRAINT `fk_facility_admins_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE RESTRICT
-- );


-- -- Table Facilities Hours
-- CREATE TABLE `facility_hours` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `facility_id` BIGINT NOT NULL,

--     `day_of_week` ENUM(
--         'monday',
--         'tuesday',
--         'wednesday',
--         'thursday',
--         'friday',
--         'saturday',
--         'sunday'
--     ) NOT NULL,

--     `open_time` TIME NOT NULL,
--     `close_time` TIME NOT NULL,
    
--     `break_start_time` TIME,
--     `break_end_time` TIME,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,

--     CONSTRAINT `fk_facility_hours_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE,
    
--     CHECK (
--         `break_start_time` < `break_end_time`
--     ),
--     CHECK (`open_time` < `close_time`)
-- );

-- -- Table Practice Schedule
-- CREATE TABLE `schedules` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

--     `doctor_username` VARCHAR(50) NOT NULL,
--     `facility_id` BIGINT NOT NULL,
--     `notes` TEXT,

--     `day_of_week` ENUM(
--         'monday',
--         'tuesday',
--         'wednesday',
--         'thursday',
--         'friday',
--         'saturday',
--         'sunday'
--     ) NOT NULL,

--     `start_time` TIME NOT NULL,
--     `end_time` TIME NOT NULL,

--     `break_start_time` TIME,
--     `break_end_time` TIME,

--     `slot_duration_minutes` INT DEFAULT 30,
--     `max_patients` INT,

--     `consultation_fee` DECIMAL(10, 2),

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,

--     CONSTRAINT `fk_schedules_doctor` FOREIGN KEY (`doctor_username`) REFERENCES `doctors` (`username`) ON DELETE CASCADE,
--     CONSTRAINT `fk_schedules_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE,

--     CHECK (
--         `break_start_time` < `break_end_time`
--     ),
--     CHECK (`start_time` < `end_time`)
-- );

-- -- Table Appointments
-- CREATE TABLE `appointments` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

--     `member_username` VARCHAR(50) NOT NULL,
--     `schedule_id` BIGINT NOT NULL,

--     `appointment_date` DATE NOT NULL,
--     `appointment_time` TIME NOT NULL,

--     `queue_order` INT NOT NULL,

--     `status` ENUM(
--         'pending',
--         'confirmed',
--         'completed',
--         'cancelled',
--         'no_show'
--     ) DEFAULT 'pending',

--     `notes` TEXT,

--     `check_in_time` DATETIME,
--     `completed_time` DATETIME,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
--     `deleted_at` TIMESTAMP NULL DEFAULT NULL,

--     CONSTRAINT `fk_appointments_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE,
--     CONSTRAINT `fk_appointments_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,

--     UNIQUE (
--         `schedule_id`,
--         `appointment_date`,
--         `appointment_time`
--     ),
--     UNIQUE (
--         `schedule_id`,
--         `appointment_date`,
--         `queue_order`
--     )
-- );

-- -- Table Online Sessions
-- CREATE TABLE `online_sessions` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `doctor_username` VARCHAR(50) NOT NULL,

--     `start_time` DATETIME NOT NULL,
--     `end_time` DATETIME,

--     `consultation_fee` DECIMAL(9, 2) NOT NULL,
--     `description` TEXT NOT NULL,

--     CONSTRAINT `fk_online_session_doctors` FOREIGN KEY (`doctor_username`) REFERENCES `doctors` (`username`) ON DELETE CASCADE
-- );

-- -- Table Consultations
-- CREATE TABLE `consultations` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `online_session_id` BIGINT NOT NULL,
--     `member_username` VARCHAR(50) NOT NULL,

--     `start_time` DATETIME NOT NULL,
--     `end_time` DATETIME,

--     `notes` TEXT,
--     `paid_at` DATETIME NULL,

--     CONSTRAINT `fk_consultations_session` FOREIGN KEY (`online_session_id`) REFERENCES `online_sessions` (`id`) ON DELETE CASCADE,
--     CONSTRAINT `fk_consultations_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE
-- );

-- -- Table Chat
-- CREATE TABLE `chats` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `consultation_id` BIGINT NOT NULL,

--     `message` TEXT NOT NULL,
--     `sender` VARCHAR(50) NOT NULL,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

--     CONSTRAINT `fk_chats_sender` FOREIGN KEY (`sender`) REFERENCES `users` (`username`) ON DELETE CASCADE,
--     CONSTRAINT `fk_chats_consultation` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE
-- );

-- -- Table Prescriptions
-- CREATE TABLE `prescriptions` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `doctor_username` VARCHAR(50) NOT NULL,
--     `member_username` VARCHAR(50) NOT NULL,

--     `appointment_id` BIGINT NULL,
--     `consultation_id` BIGINT NULL,

--     `notes` TEXT,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,

--     CONSTRAINT `fk_prescriptions_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
--     CONSTRAINT `fk_prescriptions_consultation` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE,
--     CONSTRAINT `fk_prescriptions_doctor` FOREIGN KEY (`doctor_username`) REFERENCES `doctors` (`username`) ON DELETE CASCADE,
--     CONSTRAINT `fk_prescriptions_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE,

--     CHECK (
--         `appointment_id` IS NOT NULL
--         OR `consultation_id` IS NOT NULL
--     )
-- );

-- -- Table Prescription Details
-- CREATE TABLE `prescription_details` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `prescription_id` BIGINT NOT NULL,

--     `medicine_id` BIGINT NOT NULL,
--     `quantity` INT NOT NULL,

--     `start` DATE NOT NULL,
--     `end` DATE NOT NULL,

--     `taken` DATETIME NULL,
--     `taken_at` BIGINT NULL,

--     `instructions` VARCHAR(255), 

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
--     CONSTRAINT `fk_prescription_details_prescription` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE,
--     CONSTRAINT `fk_prescription_details_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE RESTRICT,
--     CONSTRAINT `fk_prescription_details_facility` FOREIGN KEY (`taken_at`) REFERENCES `facilities` (`id`) ON DELETE RESTRICT
-- );


-- -- Table Article Topics
-- CREATE TABLE `article_topics`(
--     `id` INT AUTO_INCREMENT PRIMARY KEY,
--     `name` VARCHAR(100) NOT NULL UNIQUE
-- );

-- -- Table Articles
-- CREATE TABLE `articles` (
--     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
--     `created_by` VARCHAR(50) NOT NULL,
--     `topic_id` INT NOT NULL,

--     `content` TEXT NOT NULL,

--     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
--     `deleted_at` TIMESTAMP NULL DEFAULT NULL,

    
--     CONSTRAINT `fk_article_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`username`) ON DELETE RESTRICT,
--     CONSTRAINT `fk_article_topics` FOREIGN KEY (`topic_id`) REFERENCES `article_topics` (`id`) ON DELETE RESTRICT
-- );

-- -- TODO: Table Articles Images


-- -- Indexes
-- CREATE INDEX `idx_schedules_doctor` ON `schedules` (`doctor_username`);

-- CREATE INDEX `idx_schedules_facility` ON `schedules` (`facility_id`);

-- CREATE INDEX `idx_schedules_day` ON `schedules` (`day_of_week`);

-- CREATE INDEX `idx_appointments_member` ON `appointments` (`member_username`);

-- CREATE INDEX `idx_appointments_schedule` ON `appointments` (`schedule_id`);

-- CREATE INDEX `idx_appointments_date` ON `appointments` (`appointment_date`);

-- CREATE INDEX `idx_members_district` ON `members` (`district_id`);

-- CREATE INDEX `idx_medical_histories_member` ON `medical_histories` (`member_username`);

-- CREATE INDEX `idx_member_allergies_member` ON `member_allergies` (`member_username`);

-- CREATE INDEX `idx_member_allergies_allergen` ON `member_allergies` (`allergen_id`);

-- CREATE INDEX `idx_facility_hours_facility` ON `facility_hours` (`facility_id`);

-- CREATE INDEX `idx_prescriptions_member` ON `prescriptions` (`member_username`);

-- CREATE INDEX `idx_prescriptions_doctor` ON `prescriptions` (`doctor_username`);

CREATE SCHEMA IF NOT EXISTS vitaguard;

USE vitaguard;
-- ERD Ver 0.1.1

-- Table Provinces
CREATE TABLE `provinces` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL
);

-- Table Cities
CREATE TABLE `cities` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `province_id` INT NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    CONSTRAINT `fk_cities_provinces` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table Districts
CREATE TABLE `districts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `city_id` INT NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    CONSTRAINT `fk_districts_cities` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table Users
CREATE TABLE `users` (
    `username` VARCHAR(50) PRIMARY KEY,
    `password_hashed` VARCHAR(255) NOT NULL,

    `email` VARCHAR(100) NOT NULL UNIQUE,
    `phone_number` VARCHAR(20),

    `role` ENUM('member', 'doctor', 'facility_admin', 'admin') DEFAULT 'member',
    `status` ENUM('active', 'suspended') DEFAULT 'active',

    `last_login_at` TIMESTAMP NULL,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL
);

-- Table Members
CREATE TABLE `members` (
    `username` VARCHAR(50) PRIMARY KEY,

    `first_name` VARCHAR(100) NOT NULL,
    `middle_name` VARCHAR(100),
    `last_name` VARCHAR(100) NOT NULL,

    `gender` ENUM('male', 'female') NOT NULL,
    `date_of_birth` DATE NOT NULL,

    `address` VARCHAR(255) NOT NULL,
    `district_id` INT NOT NULL,

    `blood_type` ENUM(
        'A+',
        'A-',
        'B+',
        'B-',
        'AB+',
        'AB-',
        'O+',
        'O-'
    ),
    `weight_kg` DECIMAL(5, 2) NOT NULL,
    `height_cm` DECIMAL(5, 2) NOT NULL,
    `smoking_status` ENUM('never', 'former', 'current') NOT NULL,
    `alcohol_consumption` ENUM(
        'none',
        'light',
        'moderate',
        'heavy'
    ) NOT NULL,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT `fk_members_users` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_members_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Table Medical Histories
CREATE TABLE `medical_histories` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

    `member_username` VARCHAR(50) NOT NULL,

    `diagnosed_date` DATE NOT NULL,
    `description` TEXT NOT NULL,
    `inputted_by` VARCHAR(50) NOT NULL,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT `fk_medical_histories_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_medical_histories_users` FOREIGN KEY (`inputted_by`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table Medicines
CREATE TABLE `medicines` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

    `name` VARCHAR(100) NOT NULL,
    `dosage_form` ENUM(
        'tablet',
        'capsule',
        'syrup',
        'injection',
        'ointment'
    ) NOT NULL,
    `medicine_class` ENUM(
        'over_the_counter',
        'limited_otc',
        'prescription',
        'narcotic',
        'psychotropic'
    ) NOT NULL,
    `description` TEXT NOT NULL,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE (`name`, `dosage_form`)
);

-- Table Allergens
CREATE TABLE `allergens` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE
);

-- Table Member Allergies
CREATE TABLE `member_allergies` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

    `member_username` VARCHAR(50) NOT NULL,

    `allergen_id` BIGINT NOT NULL,
    `severity` ENUM('mild', 'moderate', 'severe'),
    `notes` TEXT,

    `inputted_by` VARCHAR(50) NOT NULL,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT `fk_member_allergies_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_member_allergies_allergen` FOREIGN KEY (`allergen_id`) REFERENCES `allergens` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_member_allergies_inputted_by` FOREIGN KEY (`inputted_by`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table Doctors
CREATE TABLE `doctors` (
    `username` VARCHAR(50) PRIMARY KEY,

    `prefix_name` VARCHAR(20),
    `first_name` VARCHAR(100) NOT NULL,
    `middle_name` VARCHAR(100),
    `last_name` VARCHAR(100) NOT NULL,
    `suffix_name` VARCHAR(100),

    `date_of_birth` DATE NOT NULL,

    `address` VARCHAR(255) NOT NULL,
    `district_id` INT NOT NULL,

    `rating_avg` DECIMAL(3, 2),
    `rating_count` INT DEFAULT 0,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT `fk_doctors_users` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_doctors_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Table Specialties
CREATE TABLE `specialties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) UNIQUE
);

-- Table Doctor Specialties
CREATE TABLE `doctor_specialties` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,

    `specialty_id` INT NOT NULL,

    CONSTRAINT `fk_doctor_specialties_username` FOREIGN KEY (`username`) REFERENCES `doctors` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_doctor_specialties_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE (`username`, `specialty_id`)
);

-- Table Facilities
CREATE TABLE `facilities` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,

    `address` VARCHAR(255) NOT NULL,
    `district_id` INT,
    `phone_number` VARCHAR(20),

    `rating_avg` DECIMAL(3, 2),
    `rating_count` INT DEFAULT 0,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `fk_facilities_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Table Facility Admins
-- FIX: dua inline PRIMARY KEY → composite PRIMARY KEY
CREATE TABLE `facility_admins` (
    `username`    VARCHAR(50) NOT NULL,
    `facility_id` BIGINT      NOT NULL,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`username`, `facility_id`),

    CONSTRAINT `fk_facility_admins_user`     FOREIGN KEY (`username`)    REFERENCES `users`      (`username`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_facility_admins_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`)       ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Table Facility Hours
CREATE TABLE `facility_hours` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `facility_id` BIGINT NOT NULL,

    `day_of_week` ENUM(
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday'
    ) NOT NULL,

    `open_time` TIME NOT NULL,
    `close_time` TIME NOT NULL,

    `break_start_time` TIME,
    `break_end_time` TIME,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `fk_facility_hours_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

    CHECK (`open_time` < `close_time`),
    CHECK (`break_start_time` < `break_end_time`),
    CHECK (`break_start_time` >= `open_time` AND `break_end_time` <= `close_time`)
);

-- Table Schedules (Practice Schedule)
CREATE TABLE `schedules` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

    `doctor_username` VARCHAR(50) NOT NULL,
    `facility_id` BIGINT NOT NULL,
    `notes` TEXT,

    `day_of_week` ENUM(
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday'
    ) NOT NULL,

    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,

    `break_start_time` TIME,
    `break_end_time` TIME,

    `slot_duration_minutes` INT DEFAULT 30,
    `max_patients` INT,

    `consultation_fee` DECIMAL(10, 2),

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `fk_schedules_doctor` FOREIGN KEY (`doctor_username`) REFERENCES `doctors` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_schedules_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

    CHECK (`start_time` < `end_time`),
    CHECK (`break_start_time` < `break_end_time`)
);

-- Table Appointments
CREATE TABLE `appointments` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,

    `member_username` VARCHAR(50) NOT NULL,
    `schedule_id` BIGINT NOT NULL,

    `appointment_date` DATE NOT NULL,
    `appointment_time` TIME NOT NULL,

    `queue_order` INT NOT NULL,

    `status` ENUM(
        'pending',
        'confirmed',
        'completed',
        'cancelled',
        'no_show'
    ) DEFAULT 'pending',

    `notes` TEXT,

    `check_in_time` DATETIME,
    `completed_time` DATETIME,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT `fk_appointments_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_appointments_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,

    UNIQUE (`schedule_id`, `appointment_date`, `appointment_time`),
    UNIQUE (`schedule_id`, `appointment_date`, `queue_order`)
);

-- Table Online Sessions
CREATE TABLE `online_sessions` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `doctor_username` VARCHAR(50) NOT NULL,

    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME,

    `consultation_fee` DECIMAL(9, 2) NOT NULL,
    `description` TEXT NOT NULL,

    CONSTRAINT `fk_online_session_doctors` FOREIGN KEY (`doctor_username`) REFERENCES `doctors` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table Consultations
CREATE TABLE `consultations` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `online_session_id` BIGINT NOT NULL,
    `member_username` VARCHAR(50) NOT NULL,

    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME,

    `notes` TEXT,
    `paid_at` DATETIME NULL,

    CONSTRAINT `fk_consultations_session` FOREIGN KEY (`online_session_id`) REFERENCES `online_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_consultations_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table Chats
CREATE TABLE `chats` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `consultation_id` BIGINT NOT NULL,

    `message` TEXT NOT NULL,
    `sender` VARCHAR(50) NOT NULL,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `fk_chats_sender` FOREIGN KEY (`sender`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_chats_consultation` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table Prescriptions
CREATE TABLE `prescriptions` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `doctor_username` VARCHAR(50) NOT NULL,
    `member_username` VARCHAR(50) NOT NULL,

    `appointment_id` BIGINT NULL,
    `consultation_id` BIGINT NULL,

    `notes` TEXT,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `fk_prescriptions_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_prescriptions_consultation` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_prescriptions_doctor` FOREIGN KEY (`doctor_username`) REFERENCES `doctors` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_prescriptions_member` FOREIGN KEY (`member_username`) REFERENCES `members` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,

    CHECK (
        `appointment_id` IS NOT NULL
        OR `consultation_id` IS NOT NULL
    )
);

-- Table Prescription Details
-- FIX: rename taken_at → dispensed_at_facility_id agar tidak ambigu dengan timestamp
CREATE TABLE `prescription_details` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `prescription_id` BIGINT NOT NULL,

    `medicine_id` BIGINT NOT NULL,
    `quantity` INT NOT NULL,

    `start` DATE NOT NULL,
    `end` DATE NOT NULL,

    `taken` DATETIME NULL,
    `dispensed_at_facility_id` BIGINT NULL,

    `instructions` VARCHAR(255),

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `fk_prescription_details_prescription` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_prescription_details_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_prescription_details_facility` FOREIGN KEY (`dispensed_at_facility_id`) REFERENCES `facilities` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Table Article Topics
CREATE TABLE `article_topics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE
);

-- Table Articles
CREATE TABLE `articles` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `created_by` VARCHAR(50) NOT NULL,
    `topic_id` INT NOT NULL,

    `content` TEXT NOT NULL,

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT `fk_article_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`username`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_article_topics` FOREIGN KEY (`topic_id`) REFERENCES `article_topics` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- TODO: Table Article Images


-- Indexes
CREATE INDEX `idx_schedules_doctor`   ON `schedules` (`doctor_username`);
CREATE INDEX `idx_schedules_facility` ON `schedules` (`facility_id`);
CREATE INDEX `idx_schedules_day`      ON `schedules` (`day_of_week`);

CREATE INDEX `idx_appointments_member`   ON `appointments` (`member_username`);
CREATE INDEX `idx_appointments_schedule` ON `appointments` (`schedule_id`);
CREATE INDEX `idx_appointments_date`     ON `appointments` (`appointment_date`);

CREATE INDEX `idx_members_district` ON `members` (`district_id`);

CREATE INDEX `idx_medical_histories_member` ON `medical_histories` (`member_username`);

CREATE INDEX `idx_member_allergies_member`  ON `member_allergies` (`member_username`);
CREATE INDEX `idx_member_allergies_allergen` ON `member_allergies` (`allergen_id`);

CREATE INDEX `idx_facility_hours_facility` ON `facility_hours` (`facility_id`);

CREATE INDEX `idx_prescriptions_member` ON `prescriptions` (`member_username`);
CREATE INDEX `idx_prescriptions_doctor` ON `prescriptions` (`doctor_username`);