ALTER TABLE `ballet`.`students` 
DROP FOREIGN KEY `fk_students_classes1`;
ALTER TABLE `ballet`.`students` 
DROP COLUMN `id_class`,
DROP INDEX `fk_students_classes1_idx` ;
;

CREATE TABLE `ballet`.`student_classes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_student` INT NOT NULL,
  `id_class` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_student_idx` (`id_student` ASC),
  INDEX `fk_class_idx` (`id_class` ASC),
  CONSTRAINT `fk_student`
    FOREIGN KEY (`id_student`)
    REFERENCES `ballet`.`students` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_class`
    FOREIGN KEY (`id_class`)
    REFERENCES `ballet`.`classes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
    
ALTER TABLE `ballet`.`students` 
ADD COLUMN `picture` VARCHAR(255) NULL AFTER `status`;
