-- Central CMI - Database Schema
-- MySQL 8.0+ (InnoDB, utf8mb4)

CREATE DATABASE IF NOT EXISTS `central_cmi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `central_cmi`;

-- Users
CREATE TABLE IF NOT EXISTS `User` (
  `UserID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `firstName` VARCHAR(100) NOT NULL,
  `lastName` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `birthdate` DATE NULL,
  `designation` VARCHAR(150) NULL,
  `position` ENUM('ICTC','RDC','SCC','TTC') NOT NULL,
  `agency` VARCHAR(200) NULL,
  `is_representative` TINYINT(1) NOT NULL DEFAULT 0,
  `is_secretariat` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `uk_user_username` (`username`),
  UNIQUE KEY `uk_user_email` (`email`),
  KEY `idx_user_position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email Notifications
CREATE TABLE IF NOT EXISTS `EmailNotification` (
  `NotificationID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `recipient` VARCHAR(50) NOT NULL DEFAULT 'all' COMMENT 'all, representatives, secretariat',
  `subject` VARCHAR(255) NOT NULL,
  `content` MEDIUMTEXT NOT NULL,
  `type` ENUM('deadline', 'approval', 'meeting', 'system', 'report', 'general') NOT NULL DEFAULT 'general',
  `priority` ENUM('high', 'medium', 'low') NOT NULL DEFAULT 'medium',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`NotificationID`),
  KEY `idx_emailnotification_created_by` (`created_by`),
  KEY `idx_emailnotification_type` (`type`),
  KEY `idx_emailnotification_priority` (`priority`),
  CONSTRAINT `fk_emailnotification_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `User` (`UserID`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notification Recipients (join table)
CREATE TABLE IF NOT EXISTS `NotificationRecipient` (
  `NotificationID` BIGINT UNSIGNED NOT NULL,
  `UserID` BIGINT UNSIGNED NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `received_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`NotificationID`, `UserID`),
  KEY `idx_notificationrecipient_user` (`UserID`),
  KEY `idx_notificationrecipient_read` (`is_read`),
  CONSTRAINT `fk_notificationrecipient_notification`
    FOREIGN KEY (`NotificationID`) REFERENCES `EmailNotification` (`NotificationID`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_notificationrecipient_user`
    FOREIGN KEY (`UserID`) REFERENCES `User` (`UserID`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activities
CREATE TABLE IF NOT EXISTS `Activity` (
  `ActivityID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `type` VARCHAR(50) NULL,
  `description` TEXT NULL,
  `reported_period_start` DATE NOT NULL,
  `reported_period_end` DATE NOT NULL,
  `location` VARCHAR(200) NULL,
  `participants_count` INT UNSIGNED NULL,
  `budget_amount` DECIMAL(12,2) NULL,
  `status` ENUM('not_started','in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `accomplishmentDetails` JSON NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ActivityID`),
  KEY `idx_activity_created_by` (`created_by`),
  KEY `idx_activity_period_start` (`reported_period_start`),
  KEY `idx_activity_period_end` (`reported_period_end`),
  KEY `idx_activity_type` (`type`),
  CONSTRAINT `fk_activity_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `User` (`UserID`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Common: Non-degree Trainings Conducted/ Facilitated
CREATE TABLE IF NOT EXISTS `NonDegreeTraining` (
  `TrainingID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ActivityID` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `date_venue` VARCHAR(255) NOT NULL,
  `num_participants` INT UNSIGNED NOT NULL DEFAULT 0,
  `expenditures` DECIMAL(12,2) NULL,
  `source_of_funds` VARCHAR(255) NULL,
  PRIMARY KEY (`TrainingID`),
  KEY `idx_training_activity` (`ActivityID`),
  CONSTRAINT `fk_training_activity`
    FOREIGN KEY (`ActivityID`) REFERENCES `Activity` (`ActivityID`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Common: Awards Received
CREATE TABLE IF NOT EXISTS `Award` (
  `AwardID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ActivityID` BIGINT UNSIGNED NOT NULL,
  `scope` ENUM('Local','Regional','National','International') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `recipient_agency` VARCHAR(255) NOT NULL,
  `sponsor` VARCHAR(255) NULL,
  `event_activity` VARCHAR(255) NULL,
  `place_of_award` VARCHAR(255) NULL,
  `date_of_award` DATE NULL,
  PRIMARY KEY (`AwardID`),
  KEY `idx_award_activity` (`ActivityID`),
  KEY `idx_award_scope` (`scope`),
  CONSTRAINT `fk_award_activity`
    FOREIGN KEY (`ActivityID`) REFERENCES `Activity` (`ActivityID`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Calendar Activities
CREATE TABLE IF NOT EXISTS `CalendarActivity` (
  `CalendarID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `date_start` DATETIME NOT NULL,
  `date_end` DATETIME NOT NULL,
  `details` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`CalendarID`),
  KEY `idx_calendar_created_by` (`created_by`),
  KEY `idx_calendar_date_start` (`date_start`),
  KEY `idx_calendar_date_end` (`date_end`),
  CONSTRAINT `fk_calendar_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `User` (`UserID`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reports
CREATE TABLE IF NOT EXISTS `Report` (
  `ReportID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `generated_by` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('individual','agency','cluster','yearly_consolidated') NOT NULL,
  `generated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReportID`),
  KEY `idx_report_generated_by` (`generated_by`),
  KEY `idx_report_type` (`type`),
  CONSTRAINT `fk_report_generated_by`
    FOREIGN KEY (`generated_by`) REFERENCES `User` (`UserID`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity ↔ Report (many-to-many)
CREATE TABLE IF NOT EXISTS `ActivityReport` (
  `ReportID` BIGINT UNSIGNED NOT NULL,
  `ActivityID` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`ReportID`, `ActivityID`),
  KEY `idx_activityreport_activity` (`ActivityID`),
  CONSTRAINT `fk_activityreport_report`
    FOREIGN KEY (`ReportID`) REFERENCES `Report` (`ReportID`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_activityreport_activity`
    FOREIGN KEY (`ActivityID`) REFERENCES `Activity` (`ActivityID`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Attachments (images/documents)
CREATE TABLE IF NOT EXISTS `ActivityAttachment` (
  `AttachmentID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ActivityID` BIGINT UNSIGNED NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(100) NULL,
  `file_size` INT UNSIGNED NULL,
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`AttachmentID`),
  KEY `idx_attachment_activity` (`ActivityID`),
  CONSTRAINT `fk_attachment_activity`
    FOREIGN KEY (`ActivityID`) REFERENCES `Activity` (`ActivityID`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


