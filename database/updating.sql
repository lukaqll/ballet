ALTER TABLE `invoices` 
ADD COLUMN `paid_at` DATETIME NULL AFTER `updated_at`;
ALTER TABLE `invoices` 
ADD COLUMN `fee` DECIMAL(12,2) NULL DEFAULT 0 AFTER `value`;

CREATE TABLE `post_classes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_post` INT NOT NULL,
  `id_class` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_post_class_1_idx` (`id_post` ASC),
  INDEX `fk_post_class_2_idx` (`id_class` ASC),
  CONSTRAINT `fk_post_class_1`
    FOREIGN KEY (`id_post`)
    REFERENCES `posts` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_class_2`
    FOREIGN KEY (`id_class`)
    REFERENCES `classes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

ALTER TABLE `invoices` 
ADD COLUMN `manual` TINYINT NULL DEFAULT 0 AFTER `paid_at`;


CREATE TABLE `sales` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_students` INT NULL,
  `id_unit` INT NULL,
  `description` VARCHAR(255) NULL,
  `color` VARCHAR(60) NULL,
  `size` VARCHAR(45) NULL,
  `payment_method` VARCHAR(45) NULL,
  `paid_at` DATETIME NULL,
  `status` VARCHAR(45) NULL,
  `payment_status` VARCHAR(45) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sales_1_idx` (`id_students` ASC),
  INDEX `fk_sales_2_idx` (`id_unit` ASC),
  CONSTRAINT `fk_sales_1`
    FOREIGN KEY (`id_students`)
    REFERENCES `students` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sales_2`
    FOREIGN KEY (`id_unit`)
    REFERENCES `units` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

ALTER TABLE `sales` 
ADD COLUMN `price` DECIMAL(12,2) NULL AFTER `updated_at`,
ADD COLUMN `paid_price` DECIMAL(12,2) NULL AFTER `price`;
ALTER TABLE `sales` 
CHANGE COLUMN `price` `price` DECIMAL(12,2) NULL DEFAULT 0 ,
CHANGE COLUMN `paid_price` `paid_price` DECIMAL(12,2) NULL DEFAULT 0 ;
ALTER TABLE `sales` 
DROP FOREIGN KEY `fk_sales_1`;
ALTER TABLE `sales` 
CHANGE COLUMN `id_students` `id_student` INT(11) NULL DEFAULT NULL ;
ALTER TABLE `sales` 
ADD CONSTRAINT `fk_sales_1`
  FOREIGN KEY (`id_student`)
  REFERENCES `students` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


------------------

ALTER TABLE `classes` 
ADD COLUMN `full` TINYINT NULL DEFAULT 0 AFTER `team`;

-------------------

ALTER TABLE `classes` 
CHANGE COLUMN `name` `name` VARCHAR(225) NOT NULL ;
ALTER TABLE `classes` 
ADD COLUMN `team` VARCHAR(60) NULL AFTER `value`;

-----------------

ALTER TABLE `contracts` 
ADD COLUMN `id_class` INT NULL AFTER `id_student`,
ADD COLUMN `contractscol` VARCHAR(45) NULL AFTER `updated_at`,
ADD INDEX `fk_contract_class_idx` (`id_class` ASC);
;
ALTER TABLE `contracts` 
ADD CONSTRAINT `fk_contract_class`
  FOREIGN KEY (`id_class`)
  REFERENCES `classes` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

---------------

ALTER TABLE `invoices` 
CHANGE COLUMN `updated_at` `updated_at` DATETIME NULL DEFAULT NULL ;

ALTER TABLE `student_classes` 
ADD COLUMN `approved_at` DATETIME NULL AFTER `id_class`;

ALTER TABLE `users` 
ADD COLUMN `is_whatsapp` TINYINT NULL DEFAULT '0' AFTER `phone`,
ADD COLUMN `uf_orgao_exp` VARCHAR(45) NULL AFTER `orgao_exp`;

ALTER TABLE `students` 
CHANGE COLUMN `nick_name` `nick_name` VARCHAR(60) NULL ;


------------------

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

---------------------

CREATE TABLE `ellega78_app`.`invoice_adds` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT NOT NULL,
  `id_invoice` INT NULL,
  `value` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `month` DATE NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `FK_USER_idx` (`id_user` ASC),
  INDEX `FK_INVOICE_idx` (`id_invoice` ASC),
  CONSTRAINT `FK_USER_ADD`
    FOREIGN KEY (`id_user`)
    REFERENCES `ellega78_app`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_INVOICE_ADD`
    FOREIGN KEY (`id_invoice`)
    REFERENCES `ellega78_app`.`invoices` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

ALTER TABLE `ellega78_app`.`invoices` 
ADD COLUMN `added` DECIMAL(12,2) NULL DEFAULT 0 AFTER `manual`;

ALTER TABLE `ellega78_app`.`invoice_adds` 
ADD COLUMN `description` VARCHAR(45) NULL AFTER `id_invoice`;


INSERT INTO `ellega78_app`.`parameters` (`operation`, `attribute`, `value`) VALUES ('general-config', 'invoice_allow', '1');
INSERT INTO `ellega78_app`.`parameters` (`operation`, `attribute`, `value`) VALUES ('general-config', 'send_invoice_mail', '1');

----------

ALTER TABLE `ellega78_app`.`units` 
ADD COLUMN `due_day` INT NOT NULL DEFAULT 8 AFTER `name`;

ALTER TABLE `ellega78_app`.`users` 
ADD COLUMN `id_unit` INT NULL AFTER `signer_key`;

----------