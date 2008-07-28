<?PHP
/*
|****		Cotonti Engine					****|
|****		Copyright Cotonti 2008				****|
|****		http://www.cotonti.com/			****|
*/
/*
|****		Security Defines  Check			****|
*/
if (!defined('COTONTI_CORE')) { header("Location: /"); }
/*
|****		File Information					****|
*/
$file['name'] 		= "Config";
$file['path']		= "/data/";
$file['filename']	= "config.php";
$file['version']	= "0.0.1";
$file['updated']	= "04-08-08";
$file['type']		= "core";
/*
|****		MySQL Database Infomation			****|.
*/
$db_info['host'] 		= "localhost";
$db_info['username'] 	= "username";
$db_info['password'] 	= "password";
$db_info['database'] 	= "cotonti";
/*
|****		Settings						****|.
|****		authmode = 1(cookies) 2(sessions) 3(both)	****|.
|****			handles how users logins are handled	****|.
|****		ipcheck = TRUE(enabled) FALSE(disabled)	****|.
|****			Secuirty Validates the ip for the	****|.
|****			user logged in with the user trying to ****|.
|****			access the account			****|.
*/
$cfg['authmode'] 	= 3;
$cfg['ipcheck']		= TRUE;
/*
|****		Database Names					****|.
|****		To customize any table names			****|.
|****		Change the table name				****|.
|****		on the right in " "					****|.
*/

$db['logs'] 		= "cot_logs";
$db['users']		= "cot_users";
?>