<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags
Tags=index.tpl:{PLUGIN_INDEXPOLLS}
[END_COT_EXT]
==================== */

/**
 * Polls (recent or random) on index with jQuery
 *
 * @package indexpolls
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

sed_require('polls');

/* ================== FUNCTIONS ================== */
/**
 * Gets polls with AJAX
 *
 * @param int $limit Number of polls
 * @return string
 */
function sed_get_polls($limit)
{
	global $cfg, $L, $lang, $db_polls, $db_polls_voters, $db_polls_options, $usr, $plu_empty;
	$skin = sed_skinfile('indexpolls', true);
	$indexpolls = new XTemplate($skin);
	$sqlmode = ($cfg['plugin']['indexpolls']['mode'] == 'Recent polls') ? 'poll_creationdate DESC' :'RAND()';
	$res = 0;
	$sql_p = sed_sql_query("SELECT * FROM $db_polls WHERE poll_type='index' AND poll_state='0' ORDER by $sqlmode LIMIT $limit");

	/* === Hook - Part1 === */
	$extp = sed_getextplugins('indexpolls.get_polls.tags');
	/* ===== */
	while ($row_p = sed_sql_fetcharray($sql_p))
	{
		$res++;
		$poll_id = $row_p['poll_id'];

		$poll_form = sed_poll_form($row_p, sed_url('index', ''), 'indexpolls');
		$pollurl = sed_url('polls', 'id='.$poll_id);

		$indexpolls->assign(array(
			'IPOLLS_ID' => $poll_id,
			'IPOLLS_TITLE' => sed_parse(htmlspecialchars($row['poll_text']), 1, 1, 1),
			'IPOLLS_URL' => $pollurl,
			'IPOLLS_FORM' => $poll_form['poll_block']
		));

		/* === Hook - Part2 === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$indexpolls->parse('INDEXPOLLS.POLL');

	}
	if ($res)
	{
		$indexpolls->assign('IPOLLS_ALL', sed_rc_link(sed_url('polls', 'id=viewall'), $L['polls_viewarchives']));
	}
	else
	{
		$indexpolls->assign('IPOLLS_ERROR', $L['None']);
		$indexpolls->parse('INDEXPOLLS.ERROR');
	}

	$indexpolls->parse('INDEXPOLLS');
	return($indexpolls->text('INDEXPOLLS'));
}

if ($cfg['plugin']['indexpolls']['maxpolls'] > 0 && !$cfg['disable_polls'])
{
	require_once sed_langfile('indexpolls', 'plug');
	require_once sed_langfile('polls', 'module');
	sed_require('polls');
	sed_poll_vote();
	$latestpoll = sed_get_polls($cfg['plugin']['indexpolls']['maxpolls']);
}

$t->assign('PLUGIN_INDEXPOLLS', $latestpoll);

?>