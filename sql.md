```sql
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `book_usage` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `book_usage` ;

-- -----------------------------------------------------
-- Table `book_usage`.`books`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`books` (
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
AUTO_INCREMENT = 49303
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `book_usage`.`vendors`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`vendors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `vendor` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `book_usage`.`platforms`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`platforms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `vendor_id` INT(11) NOT NULL ,
  `platform` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `vendor_id` (`vendor_id` ASC) ,
  CONSTRAINT `platforms_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `book_usage`.`vendors` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `book_usage`.`books_platforms`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`books_platforms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `book_id` INT(11) NOT NULL ,
  `platform_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `book_id` (`book_id` ASC) ,
  INDEX `platform_id` (`platform_id` ASC) ,
  CONSTRAINT `books_platforms_book_id`
    FOREIGN KEY (`book_id` )
    REFERENCES `book_usage`.`books` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `books_platforms_platform_id`
    FOREIGN KEY (`platform_id` )
    REFERENCES `book_usage`.`platforms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 50730
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `book_usage`.`books_search`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`books_search` (
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
AUTO_INCREMENT = 49303
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `book_usage`.`books_vendors`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`books_vendors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `book_id` INT(11) NOT NULL ,
  `vendor_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `book_id` (`book_id` ASC, `vendor_id` ASC) ,
  INDEX `vendor_id` (`vendor_id` ASC) ,
  CONSTRAINT `books_vendors_book_id`
    FOREIGN KEY (`book_id` )
    REFERENCES `book_usage`.`books` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `books_vendors_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `book_usage`.`vendors` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 50730
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `book_usage`.`counter_br1`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`counter_br1` (
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
    REFERENCES `book_usage`.`books` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `counter_br1_platform_id`
    FOREIGN KEY (`platform_id` )
    REFERENCES `book_usage`.`platforms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `counter_br1_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `book_usage`.`vendors` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1142
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `book_usage`.`counter_br2`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`counter_br2` (
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
    REFERENCES `book_usage`.`books` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `counter_br2_platform_id`
    FOREIGN KEY (`platform_id` )
    REFERENCES `book_usage`.`platforms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `counter_br2_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `book_usage`.`vendors` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 97012
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `book_usage`.`temp_counter_br1`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`temp_counter_br1` (
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
-- Table `book_usage`.`temp_counter_br2`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `book_usage`.`temp_counter_br2` (
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

USE `book_usage` ;

-- -----------------------------------------------------
-- Placeholder table for view `book_usage`.`all_usage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `book_usage`.`all_usage` (`id` INT, `current_br1` INT, `current_br2` INT, `previous_br1` INT, `previous_br2` INT);

-- -----------------------------------------------------
-- Placeholder table for view `book_usage`.`counter_usage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `book_usage`.`counter_usage` (`book_id` INT, `vendor_id` INT, `platform_id` INT, `usage_year` INT, `counter_usage` INT, `usage_type` INT);

-- -----------------------------------------------------
-- Placeholder table for view `book_usage`.`current_br1`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `book_usage`.`current_br1` (`book_id` INT, `counter_br1` INT);

-- -----------------------------------------------------
-- Placeholder table for view `book_usage`.`current_br2`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `book_usage`.`current_br2` (`book_id` INT, `counter_br2` INT);

-- -----------------------------------------------------
-- Placeholder table for view `book_usage`.`overlap`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `book_usage`.`overlap` (`book_id` INT, `platforms` INT);

-- -----------------------------------------------------
-- Placeholder table for view `book_usage`.`platforms_vendors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `book_usage`.`platforms_vendors` (`id` INT, `platform_vendor` INT);

-- -----------------------------------------------------
-- Placeholder table for view `book_usage`.`previous_br1`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `book_usage`.`previous_br1` (`book_id` INT, `counter_br1` INT);

-- -----------------------------------------------------
-- Placeholder table for view `book_usage`.`previous_br2`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `book_usage`.`previous_br2` (`book_id` INT, `counter_br2` INT);

-- -----------------------------------------------------
-- Placeholder table for view `book_usage`.`unicode`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `book_usage`.`unicode` (`id` INT, `title` INT, `valid_utf8` INT);

-- -----------------------------------------------------
-- View `book_usage`.`all_usage`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `book_usage`.`all_usage`;
USE `book_usage`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_usage`.`all_usage` AS select `b`.`id` AS `id`,`cbr1`.`counter_br1` AS `current_br1`,`cbr2`.`counter_br2` AS `current_br2`,`pbr1`.`counter_br1` AS `previous_br1`,`pbr2`.`counter_br2` AS `previous_br2` from ((((`book_usage`.`books` `b` left join `book_usage`.`current_br1` `cbr1` on((`b`.`id` = `cbr1`.`book_id`))) left join `book_usage`.`current_br2` `cbr2` on((`b`.`id` = `cbr2`.`book_id`))) left join `book_usage`.`previous_br1` `pbr1` on((`b`.`id` = `pbr1`.`book_id`))) left join `book_usage`.`previous_br2` `pbr2` on((`b`.`id` = `pbr2`.`book_id`)));

-- -----------------------------------------------------
-- View `book_usage`.`counter_usage`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `book_usage`.`counter_usage`;
USE `book_usage`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_usage`.`counter_usage` AS (select `book_usage`.`counter_br1`.`book_id` AS `book_id`,`book_usage`.`counter_br1`.`vendor_id` AS `vendor_id`,`book_usage`.`counter_br1`.`platform_id` AS `platform_id`,`book_usage`.`counter_br1`.`usage_year` AS `usage_year`,`book_usage`.`counter_br1`.`counter_br1` AS `counter_usage`,'br1' AS `usage_type` from `book_usage`.`counter_br1`) union all (select `book_usage`.`counter_br2`.`book_id` AS `book_id`,`book_usage`.`counter_br2`.`vendor_id` AS `vendor_id`,`book_usage`.`counter_br2`.`platform_id` AS `platform_id`,`book_usage`.`counter_br2`.`usage_year` AS `usage_year`,`book_usage`.`counter_br2`.`counter_br2` AS `counter_usage`,'br2' AS `usage_type` from `book_usage`.`counter_br2`);

-- -----------------------------------------------------
-- View `book_usage`.`current_br1`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `book_usage`.`current_br1`;
USE `book_usage`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_usage`.`current_br1` AS select `book_usage`.`counter_br1`.`book_id` AS `book_id`,`book_usage`.`counter_br1`.`counter_br1` AS `counter_br1` from `book_usage`.`counter_br1` where (`book_usage`.`counter_br1`.`usage_year` = 2012);

-- -----------------------------------------------------
-- View `book_usage`.`current_br2`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `book_usage`.`current_br2`;
USE `book_usage`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_usage`.`current_br2` AS select `book_usage`.`counter_br2`.`book_id` AS `book_id`,`book_usage`.`counter_br2`.`counter_br2` AS `counter_br2` from `book_usage`.`counter_br2` where (`book_usage`.`counter_br2`.`usage_year` = 2012);

-- -----------------------------------------------------
-- View `book_usage`.`overlap`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `book_usage`.`overlap`;
USE `book_usage`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_usage`.`overlap` AS select `bp`.`book_id` AS `book_id`,concat(`p`.`platform`,' (',`v`.`vendor`,')') AS `platforms` from ((`book_usage`.`books_platforms` `bp` left join `book_usage`.`platforms` `p` on((`bp`.`platform_id` = `p`.`id`))) left join `book_usage`.`vendors` `v` on((`p`.`vendor_id` = `v`.`id`)));

-- -----------------------------------------------------
-- View `book_usage`.`platforms_vendors`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `book_usage`.`platforms_vendors`;
USE `book_usage`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_usage`.`platforms_vendors` AS (select `p`.`id` AS `id`,concat(`p`.`platform`,' (',`v`.`vendor`,')') AS `platform_vendor` from (`book_usage`.`platforms` `p` left join `book_usage`.`vendors` `v` on((`p`.`vendor_id` = `v`.`id`))));

-- -----------------------------------------------------
-- View `book_usage`.`previous_br1`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `book_usage`.`previous_br1`;
USE `book_usage`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_usage`.`previous_br1` AS select `book_usage`.`counter_br1`.`book_id` AS `book_id`,`book_usage`.`counter_br1`.`counter_br1` AS `counter_br1` from `book_usage`.`counter_br1` where (`book_usage`.`counter_br1`.`usage_year` = 2011);

-- -----------------------------------------------------
-- View `book_usage`.`previous_br2`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `book_usage`.`previous_br2`;
USE `book_usage`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_usage`.`previous_br2` AS select `book_usage`.`counter_br2`.`book_id` AS `book_id`,`book_usage`.`counter_br2`.`counter_br2` AS `counter_br2` from `book_usage`.`counter_br2` where (`book_usage`.`counter_br2`.`usage_year` = 2011);

-- -----------------------------------------------------
-- View `book_usage`.`unicode`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `book_usage`.`unicode`;
USE `book_usage`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_usage`.`unicode` AS select `book_usage`.`books`.`id` AS `id`,`book_usage`.`books`.`title` AS `title`,`book_usage`.`books`.`valid_utf8` AS `valid_utf8` from `book_usage`.`books` where ((length(`book_usage`.`books`.`title`) <> char_length(`book_usage`.`books`.`title`)) and ((`book_usage`.`books`.`valid_utf8` = 'N') or (`book_usage`.`books`.`valid_utf8` = 'Unknown')));


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
```
