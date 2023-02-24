<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.tags
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
 * @var int $q topic ID
 * @var string $s section category code
 * @var array<string, bool|int|float|string|null> $rowt Topic row
 */

defined('COT_CODE') or die('Wrong URL');

cot_poll_vote();
$poll_form = cot_poll_form($q, cot_url('forums', ['m' => 'posts', 'q' => $q]), '', 'forum');
if ($poll_form) {
	$t->assign([
		'POLLS_TITLE' => cot_parse($poll_form['poll_text'], cot::$cfg['polls']['markup']),
		'POLLS_FORM' => $poll_form['poll_block'],
	]);

	$t->parse('MAIN.POLLS_VIEW');

	if ($poll_form['poll_alreadyvoted']) {
		$extra = cot::$L['polls_alreadyvoted'];
	} else {
		$extra = cot::$L['polls_notyetvoted'];
	}
	$t->assign('POLLS_EXTRATEXT', $extra);
	$t->parse('MAIN.POLLS_EXTRA');

	if (!empty($poll_form['poll_text'])) {
		$rowt['ft_title'] = cot::$L['Poll'] . ': ' . $rowt['ft_title'];
	}

	$crumbs = cot_forums_buildpath($s);
	$toppath = cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb']);
	$crumbs[] = $rowt['ft_title'];
	$toptitle = cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb'], true);

	$toptitle .= (cot::$usr['isadmin']) ? cot::$R['forums_code_admin_mark'] : '';

	$t->assign([
		'FORUMS_POSTS_PAGETITLE' => $toptitle,
		'FORUMS_POSTS_SHORTTITLE' => htmlspecialchars($rowt['ft_title'])
	]);
}
