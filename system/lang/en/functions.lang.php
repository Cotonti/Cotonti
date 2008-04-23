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
$file['name'] 		= "Functions Lang - EN";
$file['path']		= "/system/lang/en/";
$file['filename']	= "functions.lang.php";
$file['version']	= "0.0.1";
$file['updated']	= "04-08-08";
$file['type']		= "core";
/*
|****		Database						****|
*/
/*
|****		cot_sql_connect					****|
*/
$L['cot_sql_connect_error_connection'] = "Unable to Connect to MYSql Database<br />If problem persists, check your configuration settings.<br />MySQL error : %1\$s";
$L['cot_sql_connect_error_database'] = "Unable to Select the MYSql Database<br />If problem persists, check your configuration settings.<br />MySQL error : %1\$s";
/*
|****		cot_sql_query					****|
*/
$L['cot_sql_query_error_query'] = "MySQL error : %1\$s"
/*
|****		Main							****|
*/
/*
|****		cot_import						****|
*/
$L['cot_import_error_method'] 		= "Invalid import method %1\$s.";
$L['cot_import_error_filter']		= "Import filter failed for data type %1\$s. Data Imported: %2\$s.";
/*
|****		cot_filter						****|
*/
$L['cot_filter_error_type']		= "Unknown filter type %1\$s.";
?>