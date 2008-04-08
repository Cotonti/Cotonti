<?PHP
/*
|****		Cotonti Engine			****|
|****		Copyright Cotonti 2008		****|
|****		http://www.cotonti.com/	****|
*/
/*
|****		Security Defines			****|
*/
define("COTONTI_CORE", TRUE);
define("COTONTI_ADMIN", TRUE);
/*
|****		File Information			****|
*/
$file['name'] 		= "Admin";
$file['path']		= "/";
$file['filename']	= "admin.php";
$file['version']	= "0.0.1";
$file['updated']	= "04-08-08";
$file['type']		= "core";
/*
|****		Requires/Includes		****|
*/
require("system/functions.php");
require("system/xtemplate.php");
require("data/config.php");
require("system/common.php");
require("system/core/admin/admin.inc.php");
?>