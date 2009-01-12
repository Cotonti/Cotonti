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
 * @version 0.0.2
 * @copyright Partial copyright (c) 2008 Cotonti Team
 * @license BSD License
 */


if (!defined('SED_CODE')) { die('Wrong URL.'); }

/* ============ MASKS FOR THE HTML OUTPUTS =========== */


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
function sed_get_polls($limit)
{
	global $cfg, $L, $lang, $db_polls, $db_polls_voters, $db_polls_options, $usr, $plu_empty;
	require_once(sed_langfile('indexpolls'));
	
	if($cfg['plugin']['indexpolls']['mode']=='Recent polls')
	{$sqlmode='poll_creationdate';}
	else if($cfg['plugin']['indexpolls']['mode']=='Random polls')
	{$sqlmode='RAND()';}

	$sql_p = sed_sql_query("SELECT poll_id FROM $db_polls WHERE poll_type=0 ORDER by $sqlmode DESC LIMIT $limit");
	while ($row_p = sed_sql_fetcharray($sql_p))
	{
		unset($res);
		$poll_id = $row_p['poll_id'];
		
		list($polltitle, $poll_form)=sed_poll_form($poll_id, sed_url('index', ""), 'indexpolls');

		$res .= "<h5>".$polltitle."</h5>";
		$res .= "<div id='p".$poll_id."'>";


		$res .= $poll_form;
		$res .= "</div><hr />";


		$res_all .= $res;
	}

	$res_all .= "<p style=\"text-align: center; \"><a href=\"".sed_url('polls', 'id=viewall')."\">".$L['polls_viewarchives']."</a></p>";

	return($res_all);
}


/* ============= */

if ($cfg['plugin']['indexpolls']['maxpolls']>0 && !$cfg['disable_polls'])
{ 
require_once($cfg['system_dir'].'/core/polls/polls.functions.php');
sed_poll_vote();
$latestpoll = sed_get_polls($cfg['plugin']['indexpolls']['maxpolls']); }

$t->assign('PLUGIN_INDEXPOLLS', $latestpoll);

?>
