/* https://github.com/Cotonti/Cotonti/issues/1544 */
ALTER TABLE `cot_contact`
  CHANGE `contact_reply` `contact_reply` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  CHANGE `contact_authorid` `contact_authorid` INT NULL DEFAULT '0',
  CHANGE `contact_subject` `contact_subject` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '';