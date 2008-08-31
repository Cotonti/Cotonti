<?PHP

/* ====================
[BEGIN_SED]
File=plugins/recentpolls/recentpolls.php
Version=125
Updated=2008-aug-29
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=recentpolls
Part=main
File=recentpolls
Hooks=index.tags
Tags=index.tpl:{PLUGIN_RECENTPOLLS}
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]

==================== */

/**
 * @package Seditio-N
 * @version 0.0.1
 * @copyright Partial copyright (c) 2008 Cotonti Team
 * @license BSD License
 */


if (!defined('SED_CODE')) { die('Wrong URL.'); }

/* ============ MASKS FOR THE HTML OUTPUTS =========== */

$cfg['plu_mask_polls'] =  "<div>%1\$s</div>";

$plu_empty = $L['None']."<br />";

/* ================== FUNCTIONS ================== */

/**
 * Gets latest polls with AJAX
 *
 * @author oc
 * @param int $limit Number of polls
 * @param string $mask Output mask
 * @return string
 */
function sed_get_recentpolls($limit, $mask)
{
	global $cfg, $L, $lang, $db_polls, $db_polls_voters, $db_polls_options, $usr, $plu_empty;

	if(file_exists($cfg['plugins_dir'].'/recentpolls/lang/recentpolls.'.$lang.'.lang.php'))
	{
		require $cfg['plugins_dir'].'/recentpolls/lang/recentpolls.'.$lang.'.lang.php';
	}
	else
	{
		require $cfg['plugins_dir'].'/recentpolls/lang/recentpolls.en.lang.php';
	}

	$sql_p = sed_sql_query("SELECT poll_id, poll_text FROM $db_polls WHERE 1 AND poll_state=0  AND poll_type=0 ORDER by poll_creationdate DESC LIMIT $limit");

	while ($row_p = sed_sql_fetcharray($sql_p))
	{
		unset($res);
		$poll_id = $row_p['poll_id'];

		if ($usr['id']>0)
		{ $sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$poll_id' AND (pv_userid='".$usr['id']."' OR pv_userip='".$usr['ip']."') LIMIT 1"); }
		else
		{ $sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$poll_id' AND pv_userip='".$usr['ip']."' LIMIT 1"); }

		if (sed_sql_numrows($sql2)>0)
		{
			$alreadyvoted = 1;
			$sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$poll_id'");
			$totalvotes = sed_sql_result($sql2,0,"SUM(po_count)");
		}
		else
		{ $alreadyvoted = 0; }


		$res .= (!$alreadyvoted) ? "
<style type=\"text/css\">
#poll-container$poll_id.loading {
  background: url('{$cfg['plugins_dir']}/recentpolls/img/spinner_bigger.gif') no-repeat center center;
}
</style>
<script type=\"text/javascript\">
function post$poll_id()
	{

		var id = $(\"input[@name='$poll_id.id']\").attr(\"value\");
		var a = $(\"input[@name='a']\").attr(\"value\");
		var vote = $(\"input[@name='$poll_id.vote']:checked\").attr(\"value\");


	$.ajax({
		type: 'GET',
		url: 'polls.php?',
		data: 'id='+id+'&a='+a+'&vote='+vote+'&mode=ajax',

		beforeSend: function(){
			if (!vote) {
			alert('".$L_idx['vote_opt']."');
			return false;
			}
			$('#poll-container$poll_id').addClass('loading');
			},

		success: function(msg){
		$('#poll-container$poll_id').removeClass('loading');
		$('#poll-container$poll_id').html(msg).hide().stop().fadeIn('slow');
		anim();
			},
		error: function(msg){
		$('#poll-container$poll_id').removeClass('loading');
		alert('".$L_idx['vote_failed']."');
			}

		});

		return false;

	}

	</script>" : '';


		$res .= "<h5>".sed_parse(sed_cc($row_p['poll_text']), 1, 1, 1)."</h5>";
		$res .= "<div id='poll-container$poll_id'>";


		$res .= ($alreadyvoted) ? '<table class="cells">' : '';

		$sql = sed_sql_query("SELECT po_id, po_text, po_count FROM $db_polls_options WHERE po_pollid='$poll_id' ORDER by po_id ASC");

		while ($row = sed_sql_fetcharray($sql))
		{
			if ($alreadyvoted)
			{
				$percentbar = floor(($row['po_count'] / $totalvotes) * 100);
				$res .= "<tr><td>".sed_parse(sed_cc($row['po_text']), 1, 1, 1)."</td><td width=\"100\"><div style=\"width:95%;\"><div class=\"bar_back\"><div class=\"bar_front\" style=\"width:".$percentbar."%;\"></div></div></div></td><td>$percentbar%</td><td>(".$row['po_count'].")</td></tr>";
			}
			else
			{
				$res .= "<input type='radio' name='$poll_id.vote' id='o".$row['po_id']."' value='".$row['po_id']."' /><label for='o".$row['po_id']."'> ".stripslashes($row['po_text'])."</label><br />";
			}
		}

		if (!$alreadyvoted)
		{
			$res .= "<input type=\"hidden\" name=\"$poll_id.id\" value=\"".$poll_id."\" />";
			$res .= "<input type=\"hidden\" name=\"a\" value=\"send\" />";
		}

		if (!$alreadyvoted)
		{ $res .= "<p style=\"text-align: center; \"><input type=\"submit\" onclick=\"post$poll_id();\" class=\"submit\" value=\"".$L_idx['voteit']."\" /></p>"; }

		$res .= ($alreadyvoted) ? '</table>' : '';

		$res .= "</div>";


		$res .= "<p style=\"text-align: center; \"><a href=\"javascript:polls('".$poll_id."')\">".$L['polls_viewresults']."</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:polls('viewall')\">".$L['polls_viewarchives']."</a></p>";

		$res_all .= sprintf($mask, $res);
	}

	//		{ $res = $plu_empty; }

	return($res_all);
}


/* ============= */

if ($cfg['plugin']['recentpolls']['maxpolls']>0 && !$cfg['disable_polls'])
{ $latestpoll = sed_get_recentpolls($cfg['plugin']['recentpolls']['maxpolls'], $cfg['plu_mask_polls']); }

$t->assign('PLUGIN_RECENTPOLLS', $latestpoll);

?>
