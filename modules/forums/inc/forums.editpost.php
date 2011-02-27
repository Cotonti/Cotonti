<?php

/**
 * Forums posts display.
 *
 * @package forums
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2011 Cotonti Team
 * @license BSD License
 */
defined('COT_CODE') or die('Wrong URL');

$s = cot_import('s', 'G', 'TXT'); // section cat
$q = cot_import('q', 'G', 'INT');  // topic id
$p = cot_import('p', 'G', 'INT'); // post id

/* === Hook === */
foreach (cot_getextplugins('forums.editpost.first') as $pl)
{
	include $pl;
}
/* ===== */

cot_blockguests();
cot_check_xg();

isset($structure['forums'][$s]) || cot_die();

$sql_forums = $db->query("SELECT * FROM $db_forum_posts WHERE fp_id = ? and fp_topicid = ? and fp_cat = ?",
	array($p, $q, $s));
if ($row = $sql_forums->fetch())
{
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', $s);

	/* === Hook === */
	foreach (cot_getextplugins('forums.editpost.rights') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!$usr['isadmin'] && $row['fp_posterid'] != $usr['id'])
	{
		cot_log('Attempt to edit a post without rights', 'sec');
		cot_die();
	}
	cot_block($usr['auth_read']);
}
else
{
	cot_die();
}

$is_first_post = $p == $db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = ? ORDER BY fp_id ASC LIMIT 1", array($q))->fetchColumn();

$sql_forums = $db->query("SELECT ft_state, ft_mode, ft_title, ft_desc FROM $db_forum_topics WHERE ft_id = $q LIMIT 1");

if ($rowt = $sql_forums->fetch())
{
	if ($rowt['ft_state'] && !$usr['isadmin'])
	{
		cot_redirect(cot_url('message', 'msg=603', '', true));
	}
}
else
{
	cot_die();
}

if ($a == 'update')
{
	/* === Hook === */
	foreach (cot_getextplugins('forums.editpost.update.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rtext = cot_import('rtext', 'P', 'HTM');
	$rtopictitle = cot_import('rtopictitle', 'P', 'TXT', 255);
	$rtopicdesc = cot_import('rtopicdesc', 'P', 'TXT', 255);
	$rupdater = ($row['fp_posterid'] == $usr['id'] && ($sys['now_offset'] < $fp_updated + 300) && empty($fp_updater) ) ? '' : $usr['name'];

	if (!empty($rtopictitle) && mb_strlen($rtopictitle) < $cfg['forums']['mintitlelength'])
	{
		cot_error('forums_titletooshort', 'rtopictitle');
	}
	if (mb_strlen($rtext) < $cfg['forums']['minpostlength'])
	{
		cot_error('forums_messagetooshort', 'rtext');
	}

	if (!cot_error_found())
	{
		$db->update($db_forum_posts, array("fp_text" => $rtext, "fp_updated" => $sys['now_offset'], "fp_updater" => $rupdater), "fp_id=$p");

		if (!empty($rtopictitle) && $db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = $q ORDER BY fp_id ASC LIMIT 1")->fetchColumn() == $p)
		{
			if (mb_substr($rtopictitle, 0, 1) == "#")
			{
				$rtopictitle = str_replace('#', '', $rtopictitle);
			}
			$rtopicpreview = mb_substr(htmlspecialchars($rtext), 0, 128);
			$db->update($db_forum_topics, array("ft_title" => $rtopictitle, "ft_desc" => $rtopicdesc, "ft_preview" => $rtopicpreview), "ft_id = $q");
		}
	}

	/* === Hook === */
	foreach (cot_getextplugins('forums.editpost.update.done') as $pl)
	{
		include $pl;
	}
	/* ===== */

	cot_forums_sectionsetlast($fp_cat);

	if ($cache)
	{
		($cfg['cache_forums']) && $cache->page->clear('forums');
		($cfg['cache_index']) && $cache->page->clear('index');
	}

	cot_redirect(cot_url('forums', "m=posts&p=" . $p, '#' . $p, true));
}
require_once cot_incfile('forms');

$toptitle = cot_forums_buildpath($s) . " " . $cfg['separator'] . " " . cot_rc_link(cot_url('forums', "m=posts&p=" . $p, "#" . $p), (($rowt['ft_mode'] == 1) ? '# ' : '') . htmlspecialchars($rowt['ft_title']));
$toptitle .= $cfg['separator'] . " " . cot_rc_link(cot_url('forums', "m=editpost&s=$s&q=" . $q . "&p=" . $p . "&" . cot_xg()), $L['Edit']);
$toptitle .= ( $usr['isadmin']) ? $R['forums_code_admin_mark'] : '';

$sys['sublocation'] = $structure['forums'][$s]['title'];
$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => $structure['forums'][$s]['title'],
	'EDIT' => $L['Edit']
);
$out['subtitle'] = cot_title('title_forum_editpost', $title_params);
$out['head'] .= $R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('forums.editpost.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('forums', 'editpost', $structure['forums'][$s]['tpl']));
$t = new XTemplate($mskin);

cot_display_messages($t);

if ($db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = $q ORDER BY fp_id ASC LIMIT 1")->fetchColumn() == $p)
{
	$t->assign(array(
		'FORUMS_EDITPOST_TOPICTITTLE' => cot_inputbox('text', 'rtopictitle', htmlspecialchars($rowt['ft_title']), array('size' => 56, 'maxlength' => 255)),
		'FORUMS_EDITPOST_TOPICDESCRIPTION' => cot_inputbox('text', 'rtopicdesc', htmlspecialchars($rowt['ft_desc']), array('size' => 56, 'maxlength' => 255)),
	));
	$t->parse('MAIN.FORUMS_EDITPOST_FIRSTPOST');
}


$t->assign(array(
	'FORUMS_EDITPOST_PAGETITLE' => $toptitle,
	'FORUMS_EDITPOST_SUBTITLE' => $L['forums_postedby'] . ": <a href=\"users.php?m=details&id=" . $row['fp_posterid'] . "\">" . $row['fp_postername'] . "</a> @ " . cot_date('datetime_medium', $fp_updated + $usr['timezone'] * 3600),
	'FORUMS_EDITPOST_UPDATED' => cot_date('datetime_medium', $fp_updated + $usr['timezone'] * 3600),
	'FORUMS_EDITPOST_UPDATED_STAMP' => $fp_updated + $usr['timezone'] * 3600,
	'FORUMS_EDITPOST_SEND' => cot_url('forums', "m=editpost&a=update&s=" . $s . "&q=" . $q . "&p=" . $p . "&" . cot_xg()),
	'FORUMS_EDITPOST_TEXT' => cot_textarea('rtext', $row['fp_text'], 20, 56, '', 'input_textarea_editor')
));

/* === Hook === */
foreach (cot_getextplugins('forums.editpost.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';
?>
