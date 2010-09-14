/* r1105 New versioning for automatic updater */
CREATE TABLE `cot_updates` (
  `upd_param` VARCHAR(255) NOT NULL,
  `upd_value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`upd_param`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `cot_updates` (`upd_param`, `upd_value`)
	VALUES ('revision', '$Rev: 1105 $');