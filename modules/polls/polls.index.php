<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags
Tags=index.tpl:{INDEX_POLLS}
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package polls
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['module']['polls']['maxpolls'] > 0)
{
	sed_require('polls');

	sed_poll_vote();

	$skin = sed_skinfile(array('polls', 'index'), false);
	$indexpolls = new XTemplate($skin);

	$sqlmode = ($cfg['module']['polls']['mode'] == 'Recent polls') ? 'poll_creationdate DESC' :'RAND()';
	$res = 0;
	$sql_p = sed_sql_query("SELECT * FROM $db_polls WHERE poll_type='index' AND poll_state='0' ORDER by $sqlmode LIMIT ".$cfg['module']['polls']['maxpolls']);

	/* === Hook - Part1 === */
	$extp = sed_getextplugins('index.polls.tags');
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
		$indexpolls->assign('IPOLLS_ALL', sed_url('polls', 'id=viewall'));
	}
	else
	{
		$indexpolls->assign('IPOLLS_ERROR', $L['None']);
		$indexpolls->parse('INDEXPOLLS.ERROR');
	}

	$indexpolls->parse('INDEXPOLLS');
	$t->assign('INDEX_POLLS', $indexpolls->text('INDEXPOLLS'));
}



?>