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
	
		list($polltext, $polldate, $totalvotes, $polloptions, $polloptions_bar, $polloptions_per, $polloptions_count, $pollbutton, $alreadyvoted)=sed_new_poll($id, true, 100);
//	$result .= "<table class=\"cells\">";

	$option_count = (count($polloptions) ? count($polloptions) : 0);
	
	for($i = 0; $i < $option_count; $i++) {
		$result .= "<tr><td>";
		$result .= stripslashes($polloptions[$i]);
		$result .= "</td><td><div style=\"width:100px;\"><div class=\"bar_back\"><div class=\"bar_front\" id=\"".$polloptions_per[$i]."%\" style=\"width:0%;\"></div></div></div></td><td>".$polloptions_per[$i]."%</td><td>(".$polloptions_count[$i].")</td></tr>";
	}

	$result .= "</table>";

	sed_sendheaders();

	echo $result;

	exit;

}

?>