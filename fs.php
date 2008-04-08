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
define("COTONTI_FILESYSTEM", TRUE);
/*
|****		File Information			****|
*/
$file['name'] 		= "File System";
$file['path']		= "/";
$file['filename']	= "fs.php";
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
require("system/core/fs/fs.inc.php");
?>