<?PHP

/* ====================
[BEGIN_SED]
File=plugins/indexpolls/indexpolls.php
Version=125
Updated=2008-aug-29
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=indexpolls
Part=main
File=indexpolls
Hooks=index.tags
Tags=index.tpl:{PLUGIN_INDEXPOLLS}
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
 * Gets polls with AJAX
 *
 * @author oc
 * @param int $limit Number of polls
 * @param string $mask Output mask
 * @return string
 */
function sed_get_polls($limit, $mask)
{
	global $cfg, $L, $lang, $db_polls, $db_polls_voters, $db_polls_options, $usr, $plu_empty;

	if(file_exists($cfg['plugins_dir'].'/indexpolls/lang/indexpolls.'.$lang.'.lang.php'))
	{
		require $cfg['plugins_dir'].'/indexpolls/lang/indexpolls.'.$lang.'.lang.php';
	}
	else
	{
		require $cfg['plugins_dir'].'/indexpolls/lang/indexpolls.en.lang.php';
	}

	if($cfg['plugin']['indexpolls']['mode']=='Recent polls')
	{$sqlmode='poll_creationdate';}
	else if($cfg['plugin']['indexpolls']['mode']=='Random polls')
	{$sqlmode='RAND()';}

	$ii = 0;

	$sql_p = sed_sql_query("SELECT poll_id, poll_text FROM $db_polls WHERE 1 AND poll_state=0  AND poll_type=0 ORDER by $sqlmode DESC LIMIT $limit");
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
		{ $alreadyvoted = 0; $ii++; }


		$res .= "<h5>".sed_parse(sed_cc($row_p['poll_text']), 1, 1, 1)."</h5>";
		$res .= "<div id='b".$poll_id."'></div>
		<div id='p".$poll_id."'>";


		$res .= ($alreadyvoted) ? '<table>' : '';

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
				$res .= "<input type='radio' name='v' value='".$row['po_id']."' />&nbsp;".stripslashes($row['po_text'])."<br />";
			}
		}

		if (!$alreadyvoted)
		{ $res .= "<p style=\"text-align: center; \"><input type=\"submit\" onclick=\"vote(".$poll_id.");\" class=\"submit\" value=\"".$L_idx['voteit']."\" /></p>"; }

		$res .= ($alreadyvoted) ? '</table>' : '';

		$res .= "</div>";


		$res .= ($alreadyvoted) ? "<p style=\"text-align: center; \"><a href=\"javascript:polls('".$poll_id."')\">".$L['polls_viewresults']."</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:polls('viewall')\">".$L['polls_viewarchives']."</a></p>" : "<p style=\"text-align: center; \"><a href=\"javascript:res(".$poll_id.",0)\" id=\"a".$poll_id."\">".$L['polls_viewresults']."</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:polls('viewall')\">".$L['polls_viewarchives']."</a></p>";

		$res_all .= sprintf($mask, $res);
	}

	$res_all .= ($ii) ? "
<script type=\"text/javascript\">
//<![CDATA[
function vote(id)
	{

		var v = $('#p'+id+' > input[name=\"v\"]:checked').attr('value');

		$.ajax({
		type: 'GET',
		url: 'polls.php?',
		data: 'id='+id+'&a=send&vote='+v+'&mode=ajax',

		beforeSend: function(){
		if (!v) {
			alert('".$L_idx['vote_opt']."');
			return false;
		}
			$('#p'+id).addClass('loading');
		},


		success: function(msg){
		$('#p'+id).removeClass('loading');
		$('#p'+id).html(msg).hide().stop().fadeIn('slow');
		$('#a'+id).attr('href', 'javascript: polls('+id+');');
		anim();
			},
		error: function(msg){
		$('#p'+id).removeClass('loading');
		alert('".$L_idx['vote_failed']."');
			}

		});

		return false;

	}

function res(id,m)
	{

	if (!m)
		{

	$.ajax({
		type: 'GET',
		url: 'polls.php?',
		data: 'id='+id+'&mode=ajax',

		beforeSend: function(){
			$('#b'+id).html($('#p'+id).html()).hide();
			$('#p'+id).addClass('loading');
			},

		success: function(msg){
		$('#p'+id).removeClass('loading');
		$('#p'+id).html(msg).hide().stop().fadeIn('slow');
		$('#a'+id).html('".$L_idx['voteback']."<\/a>').attr('href', 'javascript: res('+id+',1);');
		anim();
			},
		error: function(msg){
		$('#p'+id).removeClass('loading');
		alert('".$L_idx['vote_failed']."');
			}

		});

		}

	if (m)
		{
		$('#p'+id).html($('#b'+id).html()).hide().stop().fadeIn('slow');
		$('#a'+id).html('".$L['polls_viewresults']."<\/a>').attr('href', 'javascript: res('+id+',0);');
		}

	}
//]]>
	</script>" : '';

	//		{ $res = $plu_empty; }

	return($res_all);
}


/* ============= */

if ($cfg['plugin']['indexpolls']['maxpolls']>0 && !$cfg['disable_polls'])
{ $latestpoll = sed_get_polls($cfg['plugin']['indexpolls']['maxpolls'], $cfg['plu_mask_polls']); }

$t->assign('PLUGIN_INDEXPOLLS', $latestpoll);

?>
