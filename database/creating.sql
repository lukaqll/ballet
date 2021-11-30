-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema ballet
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ballet
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ballet` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin ;
USE `ballet` ;

-- -----------------------------------------------------
-- Table `ballet`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(45) NOT NULL,
  `cpf` VARCHAR(60) NOT NULL,
  `status` VARCHAR(45) NOT NULL,
  `is_admin` TINYINT NULL DEFAULT '0',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ballet`.`units`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`units` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ballet`.`classes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`classes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_unit` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `value` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  INDEX `fk_classes_units1_idx` (`id_unit` ASC),
  CONSTRAINT `fk_classes_units1`
    FOREIGN KEY (`id_unit`)
    REFERENCES `ballet`.`units` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ballet`.`students`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`students` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT NOT NULL,
  `id_class` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `nick_name` VARCHAR(60) NOT NULL,
  `birthdate` DATE NOT NULL,
  `status` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_students_users_idx` (`id_user` ASC),
  INDEX `fk_students_classes1_idx` (`id_class` ASC),
  CONSTRAINT `fk_students_users`
    FOREIGN KEY (`id_user`)
    REFERENCES `ballet`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_students_classes1`
    FOREIGN KEY (`id_class`)
    REFERENCES `ballet`.`classes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ballet`.`class_times`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`class_times` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_class` INT NOT NULL,
  `weekday` INT NOT NULL,
  `start_at` TIME NOT NULL,
  `end_at` TIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_class_times_classes1_idx` (`id_class` ASC),
  CONSTRAINT `fk_class_times_classes1`
    FOREIGN KEY (`id_class`)
    REFERENCES `ballet`.`classes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ballet`.`invoices`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`invoices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT NOT NULL,
  `value` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `status` VARCHAR(45) NOT NULL,
  `reference` VARCHAR(255) NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_invoices_users1_idx` (`id_user` ASC),
  CONSTRAINT `fk_invoices_users1`
    FOREIGN KEY (`id_user`)
    REFERENCES `ballet`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ballet`.`invoice_payment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`invoice_payment` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_invoice` INT NOT NULL,
  `type` VARCHAR(60) NOT NULL,
  `value` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `reference` VARCHAR(255) NOT NULL,
  `status` VARCHAR(45) NOT NULL,
  `status_detail` VARCHAR(60) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_invoice_tickets_invoices1_idx` (`id_invoice` ASC),
  CONSTRAINT `fk_invoice_tickets_invoices1`
    FOREIGN KEY (`id_invoice`)
    REFERENCES `ballet`.`invoices` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ballet`.`posts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`posts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `status` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ballet`.`post_src`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`post_src` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_post` INT NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `src` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_post_src_posts1_idx` (`id_post` ASC),
  CONSTRAINT `fk_post_src_posts1`
    FOREIGN KEY (`id_post`)
    REFERENCES `ballet`.`posts` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ballet`.`password_recovery`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ballet`.`password_recovery` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `status` VARCHAR(45) NOT NULL,
  `picture` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_password_recovery_users1_idx` (`id_user` ASC),
  CONSTRAINT `fk_password_recovery_users1`
    FOREIGN KEY (`id_user`)
    REFERENCES `ballet`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
