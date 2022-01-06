ALTER TABLE `users` 
ADD COLUMN `signer_key` VARCHAR(45) NULL AFTER `is_admin`;

CREATE TABLE `contracts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_student` INT NOT NULL,
  `key` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `kf_students_idx` (`id_student` ASC),
  CONSTRAINT `kf_students`
    FOREIGN KEY (`id_student`)
    REFERENCES `students` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


ALTER TABLE `users` 
ADD COLUMN `uf` VARCHAR(60) NULL AFTER `signer_key`,
ADD COLUMN `city` VARCHAR(60) NULL AFTER `uf`,
ADD COLUMN `district` VARCHAR(60) NULL AFTER `city`,
ADD COLUMN `street` VARCHAR(60) NULL AFTER `district`,
ADD COLUMN `address_number` VARCHAR(45) NULL AFTER `street`,
ADD COLUMN `address_complement` VARCHAR(45) NULL AFTER `address_number`,
ADD COLUMN `instagram` VARCHAR(45) NULL AFTER `picture`,
ADD COLUMN `rg` VARCHAR(45) NULL AFTER `is_admin`,
ADD COLUMN `orgao_exp` VARCHAR(60) NULL AFTER `rg`,
ADD COLUMN `profession` VARCHAR(60) NULL AFTER `orgao_exp`,
ADD COLUMN `birthdate` DATE NULL AFTER `profession`,
ADD COLUMN `cep` VARCHAR(45) NULL AFTER `birthdate`,
ADD COLUMN `know_by` VARCHAR(255) NULL AFTER `address_complement`;

CREATE TABLE `register_files` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT NOT NULL,
  `type` VARCHAR(45) NULL,
  `name` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_idx` (`id_user` ASC),
  CONSTRAINT `fk_user`
    FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

ALTER TABLE `students` 
ADD COLUMN `health_problem` VARCHAR(255) NULL AFTER `updated_at`,
ADD COLUMN `food_restriction` VARCHAR(255) NULL AFTER `health_problem`,
ADD COLUMN `in_school` TINYINT NULL AFTER `food_restriction`,
ADD COLUMN `school_time` VARCHAR(255) NULL AFTER `in_school`;

ALTER TABLE `post_src` 
CHANGE COLUMN `scr` `src` TEXT NOT NULL ;

------------

ALTER TABLE `students` 
DROP FOREIGN KEY `fk_students_classes1`;
ALTER TABLE `students` 
DROP COLUMN `id_class`,
DROP INDEX `fk_students_classes1_idx` ;
;

CREATE TABLE `student_classes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_student` INT NOT NULL,
  `id_class` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_student_idx` (`id_student` ASC),
  INDEX `fk_class_idx` (`id_class` ASC),
  CONSTRAINT `fk_student`
    FOREIGN KEY (`id_student`)
    REFERENCES `students` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_class`
    FOREIGN KEY (`id_class`)
    REFERENCES `classes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
    
ALTER TABLE `students` 
ADD COLUMN `picture` VARCHAR(255) NULL AFTER `status`;

ALTER TABLE `users` 
CHANGE COLUMN `phone` `phone` VARCHAR(45) NULL ,
CHANGE COLUMN `cpf` `cpf` VARCHAR(60) NULL ;

ALTER TABLE `password_recovery` 
ADD COLUMN `is_first` TINYINT NULL DEFAULT '0' AFTER `status`;

CREATE TABLE `parameters` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `operation` TEXT NOT NULL,
  `attribute` TEXT NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`id`));
