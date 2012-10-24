<?php

/**
 * Forums posts display.
 *
 * @package forums
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) 2008-2012 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

$s = cot_import('s','G','TXT'); // section cat

cot_blockguests();
cot_die(empty($s));

/* === Hook === */
foreach (cot_getextplugins('forums.newtopic.first') as $pl)
{
	include $pl;
}
/* ===== */

isset($structure['forums'][$s]) || cot_die();

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', $s);
/* === Hook === */
foreach (cot_getextplugins('forums.newtopic.rights') as $pl)
{
	include $pl;
}
/* ===== */
cot_block($usr['auth_write']);

if ($structure['forums'][$s]['locked'])
{
	cot_die_message(602, true);
}

if ($a == 'newtopic')
{
	cot_shield_protect();

	/* === Hook === */
	foreach (cot_getextplugins('forums.newtopic.newtopic.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rmsg['fp_text'] = cot_import('rmsgtext','P','HTM');

	$rtopic['ft_title'] = cot_import('rtopictitle','P','TXT', 255);
	$rtopic['ft_desc'] = cot_import('rtopicdesc','P','TXT', 255);
	$rtopic['ft_mode'] = (int)(cot_import('rtopicmode','P','BOL') && $cfg['forums']['cat_' . $s]['allowprvtopics']) ? 1 : 0;
	$rtopic['ft_preview'] = mb_substr(htmlspecialchars($rmsg['fp_text']), 0, 128);

	if (mb_strlen($rtopic['ft_title']) < $cfg['forums']['mintitlelength'])
	{
		cot_error('forums_titletooshort', 'newtopictitle');
	}
	if (mb_strlen($rmsg['fp_text']) < $cfg['forums']['minpostlength'])
	{
		cot_error('forums_messagetooshort', 'rmsgtext');
	}
	foreach ($cot_extrafields[$db_forum_topics] as $exfld)
	{
		$rtopic['ft_'.$exfld['field_name']] = cot_import_extrafields('rtopic'.$exfld['field_name'], $exfld);
	}
	foreach ($cot_extrafields[$db_forum_posts] as $exfld)
	{
		$rmsg['fp_'.$exfld['field_name']] = cot_import_extrafields('rmsg'.$exfld['field_name'], $exfld);
	}

	if (!cot_error_found())
	{
		if (mb_substr($rtopic['ft_title'], 0 ,1) == "#")
		{
			$rtopic['ft_title'] = str_replace('#', '', $rtopic['ft_title']);
		}

		$rtopic['ft_state'] = 0;
		$rtopic['ft_sticky'] = 0;
		$rtopic['ft_cat'] = $s;
		$rtopic['ft_creationdate'] = (int)$sys['now'];
		$rtopic['ft_updated'] = (int)$sys['now'];
		$rtopic['ft_postcount'] = 1;
		$rtopic['ft_viewcount'] = 0;
		$rtopic['ft_firstposterid'] = (int)$usr['id'];
		$rtopic['ft_firstpostername'] = $usr['name'];
		$rtopic['ft_lastposterid'] = (int)$usr['id'];
		$rtopic['ft_lastpostername'] = $usr['name'];

		$db->insert($db_forum_topics, $rtopic);

		$q = $db->lastInsertId();

		$rmsg['fp_cat'] = $s;
		$rmsg['fp_topicid'] = (int)$q;
		$rmsg['fp_posterid'] = (int)$usr['id'];
		$rmsg['fp_postername'] = $usr['name'];
		$rmsg['fp_creation'] = (int)$sys['now'];
		$rmsg['fp_updated'] = (int)$sys['now'];
		$rmsg['fp_posterip'] = $usr['ip'];

		$db->insert($db_forum_posts, $rmsg);

		$p = $db->lastInsertId();

		if ($cfg['forums']['cat_' . $s]['autoprune'] > 0)
		{
			cot_forums_prunetopics('updated', $s, $cfg['forums']['cat_' . $s]['autoprune']);
		}

		if ($cfg['forums']['cat_' . $s]['countposts'])
		{
			$sql_forums = $db->query("UPDATE $db_users SET user_postcount=user_postcount+1 WHERE user_id='".$usr['id']."'");
		}

		if (!$rtopic['ft_mode'])
		{
			cot_forums_sectionsetlast($s, "fs_postcount+1", "fs_topiccount+1");
		}

		cot_extrafield_movefiles();

		/* === Hook === */
		foreach (cot_getextplugins('forums.newtopic.newtopic.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($cache)
		{
			($cfg['cache_forums']) && $cache->page->clear('forums');
			($cfg['cache_index']) && $cache->page->clear('index');
		}

		cot_shield_update(45, "New topic");
		cot_redirect(cot_url('forums', "m=posts&q=$q&n=last", '#bottom', true));
	}
}

$toptitle = cot_breadcrumbs(cot_forums_buildpath($s), $cfg['homebreadcrumb']);
$toptitle .= ($usr['isadmin']) ? $R['forums_code_admin_mark'] : '';

$sys['sublocation'] = $structure['forums'][$s]['title'];
$out['subtitle'] = $L['forums_newtopic'];
$out['head'] .= $R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('forums.newtopic.main') as $pl)
{
	include $pl;
}
/* ===== */
require_once cot_incfile('forms');
require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('forums', 'newtopic', $structure['forums'][$s]['tpl']));
$t = new XTemplate($mskin);

cot_display_messages($t);

$t->assign(array(
	'FORUMS_NEWTOPIC_PAGETITLE' => $toptitle ,
	'FORUMS_NEWTOPIC_SUBTITLE' => htmlspecialchars(cot_parse_autourls($structure['forums'][$s]['desc'])),
	'FORUMS_NEWTOPIC_SEND' => cot_url('forums', "m=newtopic&a=newtopic&s=".$s),
	'FORUMS_NEWTOPIC_TITLE' => cot_inputbox('text', 'rtopictitle', $rtopic['ft_title'], array('size' => 56, 'maxlength' => 255)),
	'FORUMS_NEWTOPIC_DESC' => cot_inputbox('text', 'rtopicdesc', $rtopic['ft_desc'], array('size' => 56, 'maxlength' => 255)),
	'FORUMS_NEWTOPIC_TEXT' => cot_textarea('rmsgtext', $rmsg['fp_text'], 20, 56, '', 'input_textarea_medieditor'),
	'FORUMS_NEWTOPIC_EDITTIMEOUT' => cot_build_timegap(0, $cfg['forums']['edittimeout'] * 3600)
));

// Extra fields
foreach($cot_extrafields[$db_forum_posts] as $exfld)
{
	$uname = strtoupper($exfld['field_name']);
	$exfld_val = cot_build_extrafields('rmsg'.$exfld['field_name'], $exfld, $rmsg[$exfld['field_name']]);
	$exfld_title = isset($L['forums_posts_'.$exfld['field_name'].'_title']) ?  $L['forums_posts_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
	$t->assign(array(
		'FORUMS_NEWTOPIC_'.$uname => $exfld_val,
		'FORUMS_NEWTOPIC_'.$uname.'_TITLE' => $exfld_title,
		'FORUMS_NEWTOPIC_EXTRAFLD' => $exfld_val,
		'FORUMS_NEWTOPIC_EXTRAFLD_TITLE' => $exfld_title
	));
	$t->parse('MAIN.EXTRAFLD');
}

// Extra fields
foreach($cot_extrafields[$db_forum_topics] as $exfld)
{
	$uname = strtoupper($exfld['field_name']);
	$exfld_val = cot_build_extrafields('rtopic'.$exfld['field_name'], $exfld, $rtopic[$exfld['field_name']]);
	$exfld_title = isset($L['forums_topics_'.$exfld['field_name'].'_title']) ?  $L['forums_topics_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
	$t->assign(array(
		'FORUMS_NEWTOPIC_TOPIC_'.$uname => $exfld_val,
		'FORUMS_NEWTOPIC_TOPIC_'.$uname.'_TITLE' => $exfld_title,
		'FORUMS_NEWTOPIC_TOPIC_EXTRAFLD' => $exfld_val,
		'FORUMS_NEWTOPIC_TOPIC_EXTRAFLD_TITLE' => $exfld_title
	));
	$t->parse('MAIN.TOPIC_EXTRAFLD');
}

if ($cfg['forums']['cat_' . $s]['allowprvtopics'])
{
	$t->assign('FORUMS_NEWTOPIC_ISPRIVATE', cot_checkbox($rtopic['ft_mode'], 'rtopicmode'));
	$t->parse('MAIN.PRIVATE');
}

/* === Hook === */
foreach (cot_getextplugins('forums.newtopic.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';

?>
