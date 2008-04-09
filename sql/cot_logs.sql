CREATE TABLE `cot_logs` (
`log_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`log_date` INT( 11 ) NOT NULL ,
`log_ip` VARCHAR( 15 ) NOT NULL ,
`log_usrid` INT( 11 ) NOT NULL ,
`log_usrname` VARCHAR( 32 ) NOT NULL ,
`log_type` VARCHAR( 12 ) NOT NULL ,
`log_message` TEXT NOT NULL ,
`log_uri` TEXT NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_bin;