-- Database: disaster_management_system

-- --------------------------------------------------------

--
-- Table structure for table `admins`
-- Description: Stores credentials and details for administrators who manage the system via the admin portal.
--

CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(200) NOT NULL,
  `role` VARCHAR(50) DEFAULT 'Admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
-- Description: Stores information about public mobile app users (citizens, tourists).
--

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(200) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL UNIQUE,
  `last_known_latitude` DECIMAL(10, 8),
  `last_known_longitude` DECIMAL(11, 8),
  `is_safe` BOOLEAN DEFAULT TRUE,
  `is_tourist` BOOLEAN DEFAULT FALSE,
  `device_token` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contacts`
-- Description: Stores emergency contacts linked to public users.
--

CREATE TABLE `emergency_contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `relationship` VARCHAR(50),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `responders`
-- Description: Stores login and professional details for emergency responders.
--

CREATE TABLE `responders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(200) NOT NULL,
  `team` VARCHAR(100),
  `assigned_zone_geometry` GEOMETRY,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `disasters`
-- Description: Details about active, past, or potential disaster events.
--

CREATE TABLE `disasters` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `type` ENUM('Flood', 'Earthquake', 'Hurricane', 'Wildfire', 'Tsunami', 'Other') NOT NULL,
  `status` ENUM('Prepare', 'Evacuate', 'All Clear', 'Inactive') DEFAULT 'Inactive',
  `affected_area_geometry` GEOMETRY NOT NULL,
  `created_by_admin_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `shelters`
-- Description: Information about safe shelters and help camps.
--

CREATE TABLE `shelters` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `location` POINT NOT NULL,
  `capacity` INT NOT NULL,
  `current_occupancy` INT DEFAULT 0,
  `available_supplies` JSON,
  `status` ENUM('Open', 'Full', 'Closed') DEFAULT 'Closed',
  `last_updated_by_responder_id` INT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`last_updated_by_responder_id`) REFERENCES `responders`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `responder_assignments`
-- Description: (NEW) Links responders to specific disasters and/or shelters for clear duty assignment.
--

CREATE TABLE `responder_assignments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `responder_id` INT NOT NULL,
  `disaster_id` INT NULL,
  `shelter_id` INT NULL,
  `assigned_by_admin_id` INT NOT NULL,
  `assignment_details` TEXT,
  `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`responder_id`) REFERENCES `responders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`disaster_id`) REFERENCES `disasters`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shelter_id`) REFERENCES `shelters`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_by_admin_id`) REFERENCES `admins`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `broadcast_messages`
-- Description: Logs all broadcast messages (push/SMS) sent by admins.
--

CREATE TABLE `broadcast_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_id` INT NOT NULL,
  `disaster_id` INT NULL,
  `title` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `target_audience` ENUM('All', 'Affected Zone', 'Tourists') NOT NULL,
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`),
  FOREIGN KEY (`disaster_id`) REFERENCES `disasters`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `field_reports`
-- Description: Reports submitted by responders from the field.
--

CREATE TABLE `field_reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `responder_id` INT NOT NULL,
  `disaster_id` INT NOT NULL,
  `report_content` TEXT NOT NULL,
  `location` POINT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`responder_id`) REFERENCES `responders`(`id`),
  FOREIGN KEY (`disaster_id`) REFERENCES `disasters`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `awareness_content`
-- Description: Stores educational materials (first aid, safety tips) for the app.
--

CREATE TABLE `awareness_content` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `type` ENUM('First Aid', 'Safety Tip', 'Evacuation Guide', 'Protocol') NOT NULL,
  `language` VARCHAR(10) DEFAULT 'en',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_locations`
-- Description: Stores historical location data of users for post-disaster analysis.
--

CREATE TABLE `user_locations` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `location` POINT NOT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `shelter_updates`
-- Description: A log of all status changes for shelters, providing a history of updates.
--

CREATE TABLE `shelter_updates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `shelter_id` INT NOT NULL,
  `responder_id` INT NOT NULL,
  `occupancy` INT NOT NULL,
  `status` ENUM('Open', 'Full', 'Closed') NOT NULL,
  `notes` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`shelter_id`) REFERENCES `shelters`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`responder_id`) REFERENCES `responders`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;