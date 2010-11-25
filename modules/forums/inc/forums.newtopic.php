<?php

/**
 * Forums posts display.
 *
 * @package forums
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

$s = cot_import('s','G','ALP'); // saction cat

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

if (!$cfg['forums'][$s]['defstate'])
{
	cot_redirect(cot_url('message', "msg=602", '', true));
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
	
	$newtopictitle = cot_import('newtopictitle','P','TXT', 255);
	$newtopicdesc = cot_import('newtopicdesc','P','TXT', 255);
	$newprvtopic = (cot_import('newprvtopic','P','BOL') && $cfg['forums'][$s]['allowprvtopics']) ? 1 : 0;
	$newmsg = cot_import('newmsg','P','HTM');
	$newtopicpreview = mb_substr(htmlspecialchars($newmsg), 0, 128);
	
	if (strlen($newtopictitle) < 2)
	{
		cot_error('forums_titletooshort', 'newtopictitle');
	}
	if (strlen($newmsg) < 5)
	{
		cot_error('forums_messagetooshort', 'newmsg');
	}
	
	if (!$cot_error)
	{
		if (mb_substr($newtopictitle, 0 ,1) == "#")
		{
			$newtopictitle = str_replace('#', '', $newtopictitle);
		}
		
		$db->insert($db_forum_topics, array(
			'ft_state' => 0,
			'ft_mode' => (int)$newprvtopic,
			'ft_sticky' => 0,
			'ft_cat' => (int)$s,
			'ft_title' => $newtopictitle,
			'ft_desc' => $newtopicdesc,
			'ft_preview' => $newtopicpreview,
			'ft_creationdate' => (int)$sys['now_offset'],
			'ft_updated' => (int)$sys['now_offset'],
			'ft_postcount' => 1,
			'ft_viewcount' => 0,
			'ft_firstposterid' => (int)$usr['id'],
			'ft_firstpostername' => $usr['name'],
			'ft_lastposterid' => (int)$usr['id'],
			'ft_lastpostername' => $usr['name']
		));
		
		$q = $db->lastInsertId();
		
		$db->insert($db_forum_posts, array(
			'fp_topicid' => (int)$q,
			'fp_cat' => (int)$s,
			'fp_posterid' => (int)$usr['id'],
			'fp_postername' => $usr['name'],
			'fp_creation' => (int)$sys['now_offset'],
			'fp_updated' => (int)$sys['now_offset'],
			'fp_text' => $newmsg,
			'fp_posterip' => $usr['ip']
		));

		$p = $db->lastInsertId();
		
		$sql = $db->query("UPDATE $db_forum_stats SET fs_postcount=fs_postcount+1, fs_topiccount=fs_topiccount+1 WHERE fs_cat='$s'");
		
		if ($cfg['forums'][$s]['autoprune'] > 0)
		{
			cot_forums_prunetopics('updated', $s, $cfg['forums'][$s]['autoprune']);
		}
		
		if ($cfg['forums'][$s]['countposts'])
		{
			$sql = $db->query("UPDATE $db_users SET user_postcount=user_postcount+1 WHERE user_id='".$usr['id']."'");
		}
		
		if (!$newprvtopic)
		{
			cot_forums_sectionsetlast($s);
		}
		
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

$toptitle = cot_build_forumpath($s);
$toptitle .= ($usr['isadmin']) ? $R['forums_code_admin_mark'] : '';

$sys['sublocation'] = $structure['forums'][$s]['title'];
$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => htmlspecialchars($structure['forums'][$s]['title']),
	'NEWTOPIC' => $L['forums_newtopic']
);
$out['subtitle'] = cot_title('title_forum_newtopic', $title_params);
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
	"FORUMS_NEWTOPIC_PAGETITLE" => $toptitle ,
	"FORUMS_NEWTOPIC_SUBTITLE" => htmlspecialchars(cot_parse_autourls($structure['forums'][$s]['desc'])),
	"FORUMS_NEWTOPIC_SEND" => cot_url('forums', "m=newtopic&a=newtopic&s=".$s),
	"FORUMS_NEWTOPIC_TITLE" => cot_inputbox('text', 'newtopictitle', htmlspecialchars($newtopictitle), array('size' => 56, 'maxlength' => 255)),
	"FORUMS_NEWTOPIC_DESC" => cot_inputbox('text', 'newtopicdesc', htmlspecialchars($newtopicdesc), array('size' => 56, 'maxlength' => 255)),
	"FORUMS_NEWTOPIC_TEXT" => cot_textarea('newmsg', htmlspecialchars($newmsg), 20, 56, '', 'input_textarea_editor')
));

if ($cfg['forums'][$s]['allowprvtopics'])
{
	$t->assign("FORUMS_NEWTOPIC_ISPRIVATE", cot_checkbox($newprvtopic, newprvtopic));
	$t->parse("MAIN.PRIVATE");
}

/* === Hook === */
foreach (cot_getextplugins('forums.newtopic.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>
