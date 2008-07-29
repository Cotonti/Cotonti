<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=system/functions/database.lib.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=Functions
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

function sed_sql_affectedrows()
	{ return (mysql_affected_rows()); }

/* ------------------ */

function sed_sql_close()
	{ return(mysql_close()); }

/* ------------------ */

function sed_sql_connect($host, $user, $pass, $db)
	{
	$connection = @mysql_connect($host, $user, $pass) or sed_diefatal('Could not connect to database !<br />Please check your settings in the file datas/config.php<br />'.'MySQL error : '.sed_sql_error());
	$select = @mysql_select_db($db, $connection) or sed_diefatal('Could not select the database !<br />Please check your settings in the file datas/config.php<br />'.'MySQL error : '.sed_sql_error());
	return(TRUE);
	}

/* ------------------ */

function sed_sql_errno()
	{ return(mysql_errno()); }

/* ------------------ */

function sed_sql_error()
	{ return(mysql_error()); }

/* ------------------ */

function sed_sql_fetcharray($res)
	{ return (mysql_fetch_array($res)); }

/* ------------------ */

function sed_sql_fetchassoc($res)
   { return (mysql_fetch_assoc($res)); }

/* ------------------ */

function sed_sql_fetchrow($res)
	{ return (mysql_fetch_row($res)); }

/* ------------------ */

function sed_sql_freeresult($res)
	{ return (mysql_free_result($res)); }

/* ------------------ */

function sed_sql_insertid()
	{ return (mysql_insert_id()); }

/* ------------------ */

function sed_sql_listtables($res)
	{ return (mysql_list_tables($res)); }

/* ------------------ */

function sed_sql_numrows($res)
	{ return (mysql_num_rows($res)); }

/* ------------------ */

function sed_sql_prep($res)
	{
	$res = mysql_real_escape_string($res);
	return($res);
	}

/* ------------------ */

function sed_sql_query($query)
	{
	global $sys, $cfg, $usr;
	$sys['qcount']++;
	$xtime = microtime();
	$result = mysql_query($query) OR sed_diefatal('SQL error : '.sed_sql_error());
	$ytime = microtime();
	$xtime = explode(' ',$xtime);
	$ytime = explode(' ',$ytime);
	$sys['tcount'] = $sys['tcount'] + $ytime[1] + $ytime[0] - $xtime[1] - $xtime[0];
	if ($cfg['devmode'])
		{
		$sys['devmode']['queries'][] = array ($sys['qcount'], $ytime[1] + $ytime[0] - $xtime[1] - $xtime[0], $query);
		$sys['devmode']['timeline'][] = $xtime[1] + $xtime[0] - $sys['starttime'];
		}
	return($result);
	}

/* ------------------ */

function sed_sql_result($res, $row, $col)
	{ return (mysql_result($res, $row, $col)); }

/* ------------------ */

function sed_sql_rowcount($table)
	{
	$sqltmp = sed_sql_query("SELECT COUNT(*) FROM $table");
	return(mysql_result($sqltmp, 0, "COUNT(*)"));
	}

?>
