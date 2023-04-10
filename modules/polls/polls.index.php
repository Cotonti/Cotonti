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
 *
 * @var XTemplate $t
 */

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['polls']['maxpolls'] > 0) {
	require_once cot_incfile('polls', 'module');

	cot_poll_vote();

	$indexpolls = new XTemplate(cot_tplfile(array('polls', 'index'), false));

	$sqlmode = (Cot::$cfg['polls']['mode'] == 'Recent polls') ? 'poll_creationdate DESC' :'RAND()';
	$sql_polls = Cot::$db->query('SELECT * FROM ' . Cot::$db->quoteTableName(Cot::$db->polls) .
        " WHERE poll_type='index' AND poll_state=" . COT_POLL_ACTIVE .
        " ORDER by $sqlmode LIMIT " . Cot::$cfg['polls']['maxpolls']);

	/* === Hook - Part1 === */
	$extp = cot_getextplugins('polls.index.tags');
	/* ===== */
    $res = 0;
	foreach ($sql_polls->fetchAll() as $row_p) {
		$res++;
		$poll_form = cot_poll_form($row_p, cot_url('index', ''), 'index');
		$indexpolls->assign(array(
			'IPOLLS_ID' => $row_p['poll_id'],
			'IPOLLS_TITLE' => cot_parse($row_p['poll_text'], Cot::$cfg['polls']['markup']),
			'IPOLLS_URL' => cot_url('polls', 'id='.$row_p['poll_id']),
			'IPOLLS_FORM' => $poll_form['poll_block']
		));

		/* === Hook - Part2 === */
		foreach ($extp as $pl) {
			include $pl;
		}
		/* ===== */

		$indexpolls->parse('INDEXPOLLS.POLL');

	}

	$indexpolls->assign('IPOLLS_ALL', cot_url('polls'));

	if (!$res) {
		$indexpolls->assign('IPOLLS_ERROR', Cot::$L['None']);
		$indexpolls->parse('INDEXPOLLS.ERROR');
	}

	$indexpolls->parse('INDEXPOLLS');
	$t->assign('INDEX_POLLS', $indexpolls->text('INDEXPOLLS'));
}
