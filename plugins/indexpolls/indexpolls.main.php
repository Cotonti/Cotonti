<?php
/* ====================
[BEGIN_SED]
File=plugins/indexpolls/indexpolls.main.php
Version=125
Updated=2008-aug-29
Type=Plugin
Author=oc
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=indexpolls
Part=indexpolls
File=indexpolls.main
Hooks=polls.main
Tags=
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * @package Seditio-N
 * @version 0.0.1
 * @author oc
 * @copyright Partial copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

$mode = sed_import('mode','G','ALP');
if ($mode=='ajax')
	{


	$result .= '<script type="text/javascript">

	function anim(){
	$(".bar_front").each(function(){
	var percentage = $(this).attr("id");
	if ($(this).attr("id")!="")
		{ $(this).css({width: "0%"}).animate({width: percentage}, "slow"); }
	$(this).attr("id","");
	});}

	</script>';
	$result .= "<table>";

		$sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$id'");
		$totalvotes = sed_sql_result($sql2,0,"SUM(po_count)");

		$sql1 = sed_sql_query("SELECT po_id,po_text,po_count FROM $db_polls_options WHERE po_pollid='$id' ORDER by po_id ASC ");

	while ($row1 = sed_sql_fetcharray($sql1))
		{
		$po_id = $row1['po_id'];
		$po_count = $row1['po_count'];
		$percent = @round(100 * ($po_count / $totalvotes),1);
		$percentbar = floor($percent * 2.24);

		$row1['po_text'] = $row1['po_text'];

		$result .= "<tr><td>";
		$result .= stripslashes($row1['po_text']);
		$result .= "</td><td><div style=\"width:100px;\"><div class=\"bar_back\"><div class=\"bar_front\" id=\"$percent%\" style=\"width:0%;\"></div></div></div></td><td>$percent%</td><td>(".$po_count.")</td></tr>";
		}

	$result .= "</table>";

	sed_sendheaders();

	echo $result;

	exit;

	}

?>