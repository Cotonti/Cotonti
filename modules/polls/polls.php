<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package Cotonti
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

sed_dieifdisabled($cfg['disable_polls']);

// Environment setup
define('SED_POLLS', TRUE);
$location = 'Polls';

/* === Hook === */
$extp = sed_getextplugins('polls.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('polls', 'a');
sed_block($usr['auth_read']);

$mode = sed_import('mode', 'G', 'ALP');

if ($mode == 'ajax')
{
	$skin = sed_import('poll_skin', 'G', 'TXT');
	$id = sed_import('poll_id', 'P', 'INT');
	sed_sendheaders();
	sed_poll_vote();
	list($polltitle, $poll_form) = sed_poll_form($id, '', $skin);
	echo $poll_form;
	exit;
}

$id = sed_import('id', 'G', 'ALP', 8);
$vote = sed_import('vote', 'G', 'TXT');
if (!empty($vote))
{
	$vote = explode(" ", $vote);
}
if (empty($vote))
{
	$vote = sed_import('vote', 'P', 'ARR');
}

$ratings = sed_import('ratings', 'G', 'BOL');

$out['subtitle'] = $L['Polls'];

sed_online_update();

/* === Hook === */
$extp = sed_getextplugins('polls.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate(sed_skinfile('polls'));

if (sed_check_messages())
{
	$t->assign('POLLS_EXTRATEXT', sed_implode_messages());
	$t->parse('MAIN.POLLS_EXTRA');
	sed_clear_messages();
}
elseif ((int)$id > 0)
{
	$id = sed_import($id, 'D', 'INT');
	if ((int) sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_polls WHERE poll_id=$id AND poll_type='index' "), 0, 0) != 1)
	{
		sed_redirect(sed_url('message', 'msg=404', '', TRUE));
	}
	sed_poll_vote();
	$poll_form = sed_poll_form($id);

	$t->assign(array(
		"POLLS_TITLE" => sed_parse(htmlspecialchars($poll_form['poll_text']), 1, 1, 1),
		"POLLS_FORM" => $poll_form['poll_block'],
		"POLLS_VIEWALL" => "<a href=\"".sed_url('polls', 'id=viewall')."\">".$L['polls_viewarchives']."</a>" // TODO to resourse
	));

	/* === Hook === */
	$extp = sed_getextplugins('polls.view.tags');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse("MAIN.POLLS_VIEW");

	$extra = $L['polls_notyetvoted'];
	if ($alreadyvoted)
	{
		$extra = ($votecasted) ? $L['polls_votecasted'] : $L['polls_alreadyvoted'];
	}

	$t->assign(array(
		"POLLS_EXTRATEXT" => $extra,
	));

	$t->parse("MAIN.POLLS_EXTRA");
}
else
{
	$jj = 0;
	$sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_state = 0 AND poll_type = 'index' ORDER BY poll_id DESC");

	/* === Hook - Part1 === */
	$extp = sed_getextplugins('polls.viewall.tags');
	/* ===== */
	while ($row = sed_sql_fetcharray($sql))
	{
		$jj++;
		$t->assign(array(
			"POLL_DATE" => date($cfg['formatyearmonthday'], $row['poll_creationdate'] + $usr['timezone'] * 3600),
			"POLL_HREF" => sed_url('polls', 'id='.$row['poll_id']),
			"POLL_TEXT" => sed_parse(htmlspecialchars($row['poll_text']), 1, 1, 1),
			"POLL_NUM" => $jj,
			"POLL_ODDEVEN" => sed_build_oddeven($jj)
		));

		/* === Hook - Part2 === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse("MAIN.POLLS_VIEWALL.POLL_ROW");
	}

	if ($jj == 0)
	{
		$t->parse("MAIN.POLLS_VIEWALL.POLL_NONE");
	}
	$t->parse("MAIN.POLLS_VIEWALL");
}

/* === Hook === */
$extp = sed_getextplugins('polls.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");
require_once $cfg['system_dir'] . '/footer.php';

?>