CREATE DATABASE IF NOT EXISTS `photo-blog`
  CHARACTER SET `utf8`
  COLLATE `utf8_general_ci`;

CREATE TABLE `photo-blog`.`customer` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;
