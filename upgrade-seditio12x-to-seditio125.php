<?php
/**
 * SQL upgrade tool seditio12x-to-seditio125
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);

$dbprefix_sed = 'sed_';

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/database.'.$cfg['sqldb'].'.php');

echo "<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />
<style type=\"text/css\">
<!--
body 		{ background-color:#45444A; margin:32px; font-family:Verdana,Arial,Helvetica; color:#EEEEEE; font-size:12px; }
a 			{ text-decoration: none; color:#AFCCE5; }
a:hover 	{ text-decoration: none; color:#000000; background-color:#AFCCE5; }
h1			{ color: #FFFFFF; font-size:140%; font-weight:bold; margin:1.5em .7em .2em 0em; }
h2			{ color: #FFCC00; font-size:120%; font-weight:bold; margin:1.5em .7em .7em 0em; }
h3			{ color: #FFFFFF; font-size:110%; font-weight:bold; margin:1.5em .7em .7em 0em; }
h4			{ color: #FFFFFF; font-size:100%; font-weight:bold; margin:1.5em .7em .7em 0em; }
h5			{ color: #FFFFFF; font-size:90%; font-weight:bold; margin:1.5em .7em .7em 0em; }
.success	{ color:#73E373; font-weight:bold; }
.mixed		{ color:#6D9FE3; font-weight:bold; }
.failure	{ color:#FF0000; font-weight:bold; }
.fatal		{ color:#FFCC00; font-weight:bold; }
.yes		{ color:#94C280; font-weight:bold; }
.part		{ color:#C0C0FF; font-weight:bold; }
.no			{ color:#BC7474; font-weight:bold; }
ul 			{ list-style-type:square; margin:6px; }

-->
</style>

<h1><a href=\"upgrade-seditio12x-to-seditio125.php\">Seditio v120/121 -> Seditio v125 : UPGRADE</a></h1>
PHP/MySQL Website engines - <a href=\"http://www.neocrome.net/\">http://www.neocrome.net</a><br />
&nbsp;<br />
";

if (!sed_sql_connect($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $cfg['mysqldb']))
{
	echo "Fatal error, could not connect to the database !";
	die();
}

unset($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $cfg['mysqldb']);
unset($out, $title, $comments);
$step = $_GET['step'];
$step = ($step < 1) ? 1 : $step;
$cnt_in = 0;
$cnt_up = 0;


function sed_check_die()
{
	echo "<br /><span class=\"fatal\"Fatal error, can't go on, existing.</style>";
	die();
}

function sed_check_success($extra)
{
	$res = "<br />";
	$res .= nl2br($extra)." &nbsp; ";
	$res .= " &nbsp; <span class=\"success\">Success</span> ";
	return ($res);
}

function sed_check_mixed($extra)
{
	$res = "<br />";
	$res .= nl2br($extra)." &nbsp; ";
	$res .= " &nbsp; <span class=\"mixed\">Success</span> ";
	return ($res);
}

function sed_check_failed($extra)
{
	$res = "<br />";
	$res .= nl2br($extra)." &nbsp; ";
	$res .= " &nbsp; <span class=\"failure\">Failure !</span> ";
	return ($res);
}

function sed_buildnxtstep($nxtstep)
{
	$res = "<form action=\"upgrade-seditio12x-to-seditio125.php?step=$nxtstep\" method=\"post\" name=\"nextstep\">";
	$res .= "<input type=\"submit\" value=\"&nbsp; &nbsp; Next  step ! (".$nxtstep.")&nbsp; &nbsp;\"></form>";
	return ($res);
}

function sed_query_chk($query)
{
	if ($sql = sed_sql_query($query))
	{
		$affected = sed_sql_affectedrows($sql);
		if ($affected == 0)
		{
			return (sed_check_mixed($query." (".$affected.")"));
		}
		else
		{
			return (sed_check_success($query." (".$affected.")"));
		}
	}
	else
	{
		return (sed_check_failed($query));
	}
}
// ===========================

switch($step)
{
	case '1':
		$title = "Ready ?";
		$comments = "This tool will handle the upgrade of the SQL database from Seditio version 120 or 121 to version 125.<br />";
		$comments .= "The process is split into many steps, in case one is failling, roll back to your previous SQL backup, and ask for help in the Neocrome forums.<br />Give as much details as possible about the problem, you'll get help faster...<br />&nbsp;<br />NEVER run this upgrade tool more than once ! As soon as it fails, you must go back to a complete SQL backup !<br />&nbsp;<br />";
		$comments .= "Make REALLY sure that you have a backup of your database with phpMyAdmin !";
		$next = sed_buildnxtstep(2);
	break;

	case '2':
		$title = "Upgrading the forums";
		$comments = "Adding a new colum ft_desc";

		$out .= sed_query_chk("ALTER TABLE ".$dbprefix_sed."forum_topics ADD COLUMN ft_desc varchar(64) NOT NULL default '' AFTER ft_title;");

		$next = sed_buildnxtstep(3);
	break;

	case '3':
		$title = "Configuration";
		$comments = "...";

		$out .= sed_query_chk("INSERT INTO ".$dbprefix_sed."config (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES ('core', 'main', '05', 'clustermode', 3, 0, '', '')");

		$next = sed_buildnxtstep(4);
	break;

	case '4':
		$title = "Pages and lists";
		$comments = "...";

		$out .= sed_query_chk("ALTER TABLE ".$dbprefix_sed."pages ADD COLUMN page_comcount mediumint(8) unsigned default '0' AFTER page_rating;");

		$sql = sed_sql_query("SELECT DISTINCT com_code, COUNT(*) FROM ".$dbprefix_sed."com WHERE com_code LIKE 'p%' GROUP BY com_code ASC");

		while($row = sed_sql_fetcharray($sql))
		{
			$row['page_id'] = mb_substr($row['com_code'], 1, 10);
			$out .= sed_query_chk("UPDATE ".$dbprefix_sed."pages SET page_comcount=".$row['COUNT(*)']." WHERE page_id=".$row['page_id']);
		}

		$next = sed_buildnxtstep(5);
	break;

	case '5':
		$title = "Setting the version number for Seditio";
		$comments = "This is the last step of the SQL upgrade.";

		$out .= sed_query_chk("UPDATE ".$dbprefix_sed."stats SET stat_value='125' WHERE stat_name='version';");
		$next = "<strong>Done !<br />&nbsp;<br /><strong><u>Now DELETE this upgrade tool from your web root to avoid security issues !</u></strong><br />&nbsp;<br /><a href=\"index.php\">Once done, click here to go to the home page...</a>";
	break;

	default:
		die ('Wrong URL !');
	break;
}

echo "<h2>Step ".$step." : ".$title."</h2>";
echo "<div style=\"padding:8px; font-size:90%; background-color:#4D4C52;\">".$comments."</div>";
echo "<div style=\"padding:8px; font-size:90%;\">".$out."</div>";
echo "<div style=\"padding:16px;\">".$next."</div>";

@ob_end_flush();
@ob_end_flush();

?>