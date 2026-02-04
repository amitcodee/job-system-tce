-- Database: job-system
-- SQL schema for Job System

CREATE DATABASE IF NOT EXISTS `job-system`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `job-system`;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'job_seeker',
  dob DATE NULL,
  father_name VARCHAR(100) NULL,
  mother_name VARCHAR(100) NULL,
  address VARCHAR(255) NULL,
  languages_known VARCHAR(255) NULL,
  profile_summary TEXT NULL,
  resume VARCHAR(255) NULL,
  resume_path VARCHAR(255) NULL,
  reset_token VARCHAR(64) NULL,
  reset_expiry DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_phone (phone),
  KEY idx_users_role (role)
) ENGINE=InnoDB;

-- Jobs table
CREATE TABLE IF NOT EXISTS jobs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  type VARCHAR(100) NOT NULL,
  location VARCHAR(150) NOT NULL,
  salary VARCHAR(100) NULL,
  category VARCHAR(100) NOT NULL,
  company_name VARCHAR(150) NOT NULL,
  company_website VARCHAR(255) NULL,
  description LONGTEXT NOT NULL,
  posted_by INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_jobs_posted_by (posted_by),
  CONSTRAINT fk_jobs_posted_by FOREIGN KEY (posted_by)
    REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(200) NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_contact_messages_created_at (created_at)
) ENGINE=InnoDB;

-- Job applications table
CREATE TABLE IF NOT EXISTS job_applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_job_applications_job_user (job_id, user_id),
  KEY idx_job_applications_job_id (job_id),
  KEY idx_job_applications_user_id (user_id),
  CONSTRAINT fk_job_applications_job FOREIGN KEY (job_id)
    REFERENCES jobs (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_job_applications_user FOREIGN KEY (user_id)
    REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Placements table
CREATE TABLE IF NOT EXISTS placements (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  company_name VARCHAR(150) NOT NULL,
  profile VARCHAR(150) NOT NULL,
  remarks TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_placements_user_id (user_id),
  CONSTRAINT fk_placements_user FOREIGN KEY (user_id)
    REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;
