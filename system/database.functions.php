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
$file['name'] 		= "Database - MySQL";
$file['path']		= "/system/";
$file['filename']	= "database.functions.php";
$file['version']	= "0.0.1";
$file['updated']	= "04-08-08";
$file['type']		= "core";
/*
|****		Functions						****|
*/
/*
|****		cot_sql_affectedrows				****|
*/
function cot_sql_affectedrows()
	{ return(mysql_affected_rows()); }
/*
|****		cot_sql_close					****|
*/
function cot_sql_close()
	{ return(mysql_close()); }
/*
|****		cot_sql_connect					****|
*/
function cot_sql_connect($host, $user, $pass, $db)
	{
		$connection = @mysql_connect($host, $user, $pass) or cot_die(sprintf($L['cot_sql_connect_error_connection'], cot_sql_error()), "fatal");
		$select = @mysql_select_db($db, $connection) or cot_die(sprintf($L['cot_sql_connect_error_database'], cot_sql_error()), "fatal");
		return(TRUE);
	}
/*
|****		cot_sql_errno					****|
*/
function cot_sql_errno()
	{ return(mysql_errno()); }
/*
|****		cot_sql_error					****|
*/
function cot_sql_error()
	{ return(mysql_error()); }
/*
|****		cot_sql_fetcharray				****|
*/
function cot_sql_fetcharray($data)
	{ return(mysql_fetch_array($data)); }
/*
|****		cot_sql_fetchassoc				****|
*/
function cot_sql_fetchassoc($data)
	{ return(mysql_fetch_assoc($data)); }
/*
|****		cot_sql_fetchrow					****|
*/
function cot_sql_fetchrow($data)
	{ return(mysql_fetch_row($data)); }
/*
|****		cot_sql_freeresult				****|
*/
function cot_sql_freeresult($data)
	{ return(mysql_free_result($data)); }
/*
|****		cot_sql_insertid					****|
*/
function cot_sql_insertid()
	{ return(mysql_insert_id()); }
/*
|****		cot_sql_listtables					****|
*/
function cot_sql_listtables($data)
	{ return(mysql_list_tables($data)); }
/*
|****		cot_sql_numrows					****|
*/
function cot_sql_numrows($data)
	{ return(mysql_num_rows($data)); }
/*
|****		cot_sql_prep					****|
*/
function cot_sql_prep($data)
	{
	return(mysql_real_escape_string($data););
	}
/*
|****		cot_sql_query					****|
*/
function cot_sql_query($query)
	{
	global $sys, $cfg;
	$sys['querycount']++;
	$xtime = microtime();
	$datault = mysql_query($query) OR cot_die(sprintf($L['cot_sql_query_error_sql'], cot_sql_error()), "fatal");
	$ytime = microtime();
	$xtime = explode(' ',$xtime);
	$ytime = explode(' ',$ytime);
	$sys['timecount'] = $sys['timecount'] + $ytime[1] + $ytime[0] - $xtime[1] - $xtime[0];
	if ($cfg['devmode'])
		{
		$sys['devmode']['queries'][] = array ($sys['qcount'], $ytime[1] + $ytime[0] - $xtime[1] - $xtime[0], $query);
		$sys['devmode']['timeline'][] = $xtime[1] + $xtime[0] - $sys['starttime'];
		}
	return($datault);
	}
/*
|****		cot_sql_result					****|
*/
function cot_sql_result($data, $row, $col)
	{ return (mysql_result($data, $row, $col)); }
/*
|****		cot_sql_rowcount				****|
*/
function cot_sql_rowcount($table)
	{
	$sqltmp = cot_sql_query("SELECT COUNT(*) FROM $table");
	return(mysql_result($sqltmp, 0, "COUNT(*)"));
	}
?>