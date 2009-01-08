/* r241 Multiple choice in polls */
ALTER TABLE `sed_polls` ADD COLUMN `poll_multiple` tinyint(1) NOT NULL default '0';