/* r1112 More updater requirements */
INSERT INTO `sed_updates` (`upd_param`, `upd_value`)
	VALUES ('branch', 'siena');

UPDATE `sed_updates` SET `upd_value` = '$Rev: 1112 $' WHERE `upd_param` ='revision';