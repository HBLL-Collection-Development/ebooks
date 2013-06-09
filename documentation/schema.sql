SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `books`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `books` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `author` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `publisher` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `doi` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `oclc` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `isbn` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `issn` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `call_num` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `response_code` TINYINT(3) NULL DEFAULT NULL ,
  `valid_utf8` ENUM('Y','N','Unknown') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'Unknown' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `vendors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `vendor` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `platforms`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `platforms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `vendor_id` INT(11) NOT NULL ,
  `platform` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `vendor_id` (`vendor_id` ASC) ,
  CONSTRAINT `platforms_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `vendors` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `books_platforms`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `books_platforms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `book_id` INT(11) NOT NULL ,
  `platform_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `book_id` (`book_id` ASC) ,
  INDEX `platform_id` (`platform_id` ASC) ,
  CONSTRAINT `books_platforms_book_id`
    FOREIGN KEY (`book_id` )
    REFERENCES `books` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `books_platforms_platform_id`
    FOREIGN KEY (`platform_id` )
    REFERENCES `platforms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `books_search`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `books_search` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `author` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `publisher` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `doi` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `oclc` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `isbn` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `issn` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `call_num` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `response_code` TINYINT(3) NULL DEFAULT NULL ,
  `valid_utf8` ENUM('Y','N','Unknown') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'Unknown' ,
  PRIMARY KEY (`id`) ,
  FULLTEXT INDEX `title` (`title` ASC) ,
  FULLTEXT INDEX `isbn` (`isbn` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `books_vendors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `books_vendors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `book_id` INT(11) NOT NULL ,
  `vendor_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `book_id` (`book_id` ASC, `vendor_id` ASC) ,
  INDEX `vendor_id` (`vendor_id` ASC) ,
  CONSTRAINT `books_vendors_book_id`
    FOREIGN KEY (`book_id` )
    REFERENCES `books` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `books_vendors_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `vendors` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `counter_br1`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `counter_br1` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `book_id` INT(11) NOT NULL ,
  `vendor_id` INT(11) NOT NULL ,
  `platform_id` INT(11) NOT NULL ,
  `usage_year` INT(11) NOT NULL ,
  `counter_br1` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `book_id` (`book_id` ASC) ,
  INDEX `vendor_id` (`vendor_id` ASC) ,
  INDEX `platform_id` (`platform_id` ASC) ,
  INDEX `year` (`usage_year` ASC) ,
  CONSTRAINT `counter_br1_book_id`
    FOREIGN KEY (`book_id` )
    REFERENCES `books` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `counter_br1_platform_id`
    FOREIGN KEY (`platform_id` )
    REFERENCES `platforms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `counter_br1_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `vendors` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `counter_br2`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `counter_br2` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `book_id` INT(11) NOT NULL ,
  `vendor_id` INT(11) NOT NULL ,
  `platform_id` INT(11) NOT NULL ,
  `usage_year` INT(11) NOT NULL ,
  `counter_br2` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `book_id` (`book_id` ASC) ,
  INDEX `vendor_id` (`vendor_id` ASC) ,
  INDEX `platform_id` (`platform_id` ASC) ,
  INDEX `year` (`usage_year` ASC) ,
  CONSTRAINT `counter_br2_book_id`
    FOREIGN KEY (`book_id` )
    REFERENCES `books` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `counter_br2_platform_id`
    FOREIGN KEY (`platform_id` )
    REFERENCES `platforms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `counter_br2_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `vendors` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `temp_counter_br1`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `temp_counter_br1` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `publisher` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `platform` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `doi` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `proprietary_identifier` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `isbn` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `issn` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `counter_br1` INT(11) NULL DEFAULT NULL ,
  `usage_year` INT(4) NULL DEFAULT NULL ,
  `vendor` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `temp_counter_br2`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `temp_counter_br2` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `publisher` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `platform` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `doi` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `proprietary_identifier` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `isbn` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `issn` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `counter_br2` INT(11) NULL DEFAULT NULL ,
  `usage_year` INT(4) NULL DEFAULT NULL ,
  `vendor` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

-- -----------------------------------------------------
-- View `counter_usage`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `counter_usage` AS (select `counter_br1`.`book_id` AS `book_id`,`counter_br1`.`vendor_id` AS `vendor_id`,`counter_br1`.`platform_id` AS `platform_id`,`counter_br1`.`usage_year` AS `usage_year`,`counter_br1`.`counter_br1` AS `counter_usage`,'br1' AS `usage_type` from `counter_br1`) union all (select `counter_br2`.`book_id` AS `book_id`,`counter_br2`.`vendor_id` AS `vendor_id`,`counter_br2`.`platform_id` AS `platform_id`,`counter_br2`.`usage_year` AS `usage_year`,`counter_br2`.`counter_br2` AS `counter_usage`,'br2' AS `usage_type` from `counter_br2`);

-- -----------------------------------------------------
-- View `current_br1`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `current_br1` AS select `counter_br1`.`book_id` AS `book_id`,`counter_br1`.`counter_br1` AS `counter_br1` from `counter_br1` where (`counter_br1`.`usage_year` = 2012);

-- -----------------------------------------------------
-- View `current_br2`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `current_br2` AS select `counter_br2`.`book_id` AS `book_id`,`counter_br2`.`counter_br2` AS `counter_br2` from `counter_br2` where (`counter_br2`.`usage_year` = 2012);

-- -----------------------------------------------------
-- View `previous_br1`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `previous_br1` AS select `counter_br1`.`book_id` AS `book_id`,`counter_br1`.`counter_br1` AS `counter_br1` from `counter_br1` where (`counter_br1`.`usage_year` = 2011);

-- -----------------------------------------------------
-- View `previous_br2`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `previous_br2` AS select `counter_br2`.`book_id` AS `book_id`,`counter_br2`.`counter_br2` AS `counter_br2` from `counter_br2` where (`counter_br2`.`usage_year` = 2011);

-- -----------------------------------------------------
-- View `all_usage`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `all_usage` AS select `b`.`id` AS `id`,`cbr1`.`counter_br1` AS `current_br1`,`cbr2`.`counter_br2` AS `current_br2`,`pbr1`.`counter_br1` AS `previous_br1`,`pbr2`.`counter_br2` AS `previous_br2` from ((((`books` `b` left join `current_br1` `cbr1` on((`b`.`id` = `cbr1`.`book_id`))) left join `current_br2` `cbr2` on((`b`.`id` = `cbr2`.`book_id`))) left join `previous_br1` `pbr1` on((`b`.`id` = `pbr1`.`book_id`))) left join `previous_br2` `pbr2` on((`b`.`id` = `pbr2`.`book_id`)));

-- -----------------------------------------------------
-- View `overlap`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `overlap` AS select `bp`.`book_id` AS `book_id`,concat(`p`.`platform`,' (',`v`.`vendor`,')') AS `platforms` from ((`books_platforms` `bp` left join `platforms` `p` on((`bp`.`platform_id` = `p`.`id`))) left join `vendors` `v` on((`p`.`vendor_id` = `v`.`id`)));

-- -----------------------------------------------------
-- View `platforms_vendors`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `platforms_vendors` AS (select `p`.`id` AS `id`,concat(`p`.`platform`,' (',`v`.`vendor`,')') AS `platform_vendor` from (`platforms` `p` left join `vendors` `v` on((`p`.`vendor_id` = `v`.`id`))));

-- -----------------------------------------------------
-- View `unicode`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `unicode` AS select `books`.`id` AS `id`,`books`.`title` AS `title`,`books`.`valid_utf8` AS `valid_utf8` from `books` where ((length(`books`.`title`) <> char_length(`books`.`title`)) and ((`books`.`valid_utf8` = 'N') or (`books`.`valid_utf8` = 'Unknown')));

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
