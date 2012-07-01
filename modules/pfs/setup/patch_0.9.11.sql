/* 0.9.11 Migrate from Kibibytes to Bytes and Kilobytes */
ALTER TABLE `cot_pfs` MODIFY COLUMN `pfs_size` INT(11) UNSIGNED NOT NULL DEFAULT '0';