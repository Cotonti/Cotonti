/* 0.7.1 fixes missing registration in cot_updates table */
INSERT IGNORE INTO `cot_updates` (`upd_param`, `upd_value`) VALUES ('hits.ver', '0.7.1');