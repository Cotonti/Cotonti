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
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['polls']['maxpolls'] > 0)
{
	require_once cot_incfile('polls', 'module');

	cot_poll_vote();
	$indexpolls = new XTemplate(cot_tplfile(array('polls', 'index'), false));

	$sqlmode = ($cfg['polls']['mode'] == 'Recent polls') ? 'poll_creationdate DESC' :'RAND()';
	$res = 0;
	$sql_polls = $db->query("SELECT * FROM $db_polls WHERE poll_type='index' AND poll_state='0' ORDER by $sqlmode LIMIT ".$cfg['polls']['maxpolls']);

	/* === Hook - Part1 === */
	$extp = cot_getextplugins('polls.index.tags');
	/* ===== */
	foreach ($sql_polls->fetchAll() as $row_p)
	{
		$res++;
		$poll_form = cot_poll_form($row_p, cot_url('index', ''), 'index');
		$indexpolls->assign(array(
			'IPOLLS_ID' => $row_p['poll_id'],
			'IPOLLS_TITLE' => cot_parse($row_p['poll_text'], $cfg['polls']['markup']),
			'IPOLLS_URL' => cot_url('polls', 'id='.$row_p['poll_id']),
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

	$indexpolls->assign('IPOLLS_ALL', cot_url('polls', 'id=viewall'));

	if (!$res)
	{
		$indexpolls->assign('IPOLLS_ERROR', $L['None']);
		$indexpolls->parse('INDEXPOLLS.ERROR');
	}

	$indexpolls->parse('INDEXPOLLS');
	$t->assign('INDEX_POLLS', $indexpolls->text('INDEXPOLLS'));
}
