CREATE DATABASE IF NOT EXISTS contact_manager
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE contact_manager;

CREATE TABLE IF NOT EXISTS customers (
  id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100)  NOT NULL,
  email      VARCHAR(150)  NOT NULL,
  phone      VARCHAR(20)   NULL,
  company    VARCHAR(100)  NULL,
  notes      TEXT          NULL,
  created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_customers_email (email),
  KEY idx_customers_name (name)
) ENGINE=InnoDB;