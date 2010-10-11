<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.tags
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

defined('COT_CODE') or die('Wrong URL');

cot_poll_vote();
$poll_form=cot_poll_form($q, cot_url('forums', 'm=posts&q='.$q), '', 'forum');
$t->assign(array(
	"POLLS_TITLE" => cot_parse($poll_form['poll_text'], $cfg['module']['polls']['markup']),
	"POLLS_FORM" => $poll_form['poll_block'],
));

$t->parse("MAIN.POLLS_VIEW");

if ($poll_form['poll_alreadyvoted'])
{ 
	$extra = ($votecasted) ? $L['polls_votecasted'] : $L['polls_alreadyvoted'];
}
else
{ 
	$extra = $L['polls_notyetvoted'];
}
$t->assign("POLLS_EXTRATEXT", $extra);
$t->parse("MAIN.POLLS_EXTRA");

if (!empty($poll_form['poll_text']))
{
	$ft_title = $L['Poll'].": ".$ft_title;
}

$toptitle = $toppath.' ' . $cfg['separator'] . ' ' . $ft_title;
$toptitle .= ($usr['isadmin']) ? $R['frm_code_admin_mark'] : '';
$t->assign(array(
	"FORUMS_POSTS_PAGETITLE" => $toptitle,
	"FORUMS_POSTS_SHORTTITLE" => $ft_title
));
?>