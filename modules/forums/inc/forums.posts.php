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

$id = cot_import('id', 'G', 'INT'); // post id
$s = cot_import('s', 'G', 'TXT'); // section cat
$q = cot_import('q', 'G', 'INT'); // topic id
$p = cot_import('p', 'G', 'INT'); // post id
list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['forums']['maxpostsperpage']); // page
$quote = cot_import('quote', 'G', 'INT');

require_once cot_langfile('countries', 'core');
require_once cot_incfile('forms');

/* === Hook === */
foreach (cot_getextplugins('forums.posts.first') as $pl)
{
	include $pl;
}
/* ===== */
if ((!empty($n) && !empty($q)) || !empty($p) || !empty($id))
{
	if (!empty($q) && ($n == 'last' || ($n == 'unread' && $usr['id'] == 0)))
	{
		$sql_forums = $db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid FROM $db_forum_posts
			WHERE fp_topicid = $q ORDER by fp_id DESC LIMIT 1");
	}
	elseif ($n == 'unread' && !empty($q) && $usr['id'] > 0)
	{
		$sql_forums = $db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid
			FROM $db_forum_posts WHERE fp_topicid = $q AND fp_updated > " . $usr['lastvisit'] . " ORDER by fp_id ASC LIMIT 1");
		if ($sql_forums->rowCount() == 0)
		{
			$sql_forums = $db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid FROM $db_forum_posts
			WHERE fp_topicid = $q ORDER by fp_id DESC LIMIT 1");
		}
	}
	elseif (!empty($p) || !empty($id))
	{
		$p = ($p > 0) ? $p : $id;
		$sql_forums = $db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid, fp_creation FROM $db_forum_posts WHERE fp_id = $p LIMIT 1");
	}
	if (isset($sql_forums) && $row = $sql_forums->fetch())
	{
		$p = $row['fp_id'];
		$q = $row['fp_topicid'];
		$s = $row['fp_cat'];
		$fp_posterid = $row['fp_posterid'];
	}
}
elseif (!empty($q))
{
	$sql_forums = $db->query("SELECT ft_cat FROM $db_forum_topics WHERE ft_id = $q LIMIT 1");
	if ($row = $sql_forums->fetch())
	{
		$s = $row['ft_cat'];
	}
}

(empty($s)) && cot_die(true, true);
isset($structure['forums'][$s]) || cot_die(true, true);
list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', $s);

/* === Hook === */
foreach (cot_getextplugins('forums.posts.rights') as $pl)
{
	include $pl;
}
/* ===== */

cot_block($usr['auth_read']);

$sys['sublocation'] = $structure['forums'][$s]['title'];

if ($a == 'newpost' && !empty($s) && !empty($q))
{
	cot_shield_protect();

	$db->query("SELECT ft_state FROM $db_forum_topics WHERE ft_id = $q")->fetchColumn() && cot_die();

	$sql_forums = $db->query("SELECT fp_id, fp_text, fp_posterid, fp_creation, fp_updated, fp_updater FROM $db_forum_posts
		WHERE fp_topicid = $q ORDER BY fp_creation DESC LIMIT 1");
	if ($row = $sql_forums->fetch())
	{
		if ($cfg['forums']['antibumpforums'] && ( ($usr['id'] == 0 && $row['fp_posterid'] == 0 &&
			$row['fp_posterip'] == $usr['ip']) || ($row['fp_posterid'] > 0 && $row['fp_posterid'] == $usr['id']) ))
		{
			cot_die();
		}
		$merge = (!$cfg['forums']['antibumpforums'] && $cfg['forums']['mergeforumposts'] && $row['fp_posterid'] == $usr['id']) ? true : false;
		$merge = ($merge && $cfg['forums']['mergetimeout'] > 0 && (($sys['now'] - $row['fp_updated']) > ($cfg['forums']['mergetimeout'] * 3600))) ? false : $merge;
	}
	else
	{
		cot_die();
	}
	$rmsg = array();
	$rmsg['fp_text'] = cot_import('rmsgtext', 'P', 'HTM');
	$rmsg['fp_updated'] = (int)$sys['now'];
	$rmsg['fp_posterip'] = $usr['ip'];

	if (mb_strlen($rmsg['fp_text']) < $cfg['forums']['minpostlength'])
	{
		cot_error('forums_messagetooshort', 'rmsgtext');
		cot_redirect(cot_url('forums', "m=posts&q=$q&n=last", '#bottom', true));
	}

	// Extra fields
	foreach ($cot_extrafields[$db_forum_posts] as $exfld)
	{
		$rmsg['fp_'.$exfld['field_name']] = cot_import_extrafields('rmsg'.$exfld['field_name'], $exfld);
	}

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.newpost.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!cot_error_found())
	{
		if (!$merge)
		{
			$rmsg['fp_topicid'] = (int)$q;
			$rmsg['fp_cat'] = $s;
			$rmsg['fp_posterid'] = (int)$usr['id'];
			$rmsg['fp_postername'] = $usr['name'];
			$rmsg['fp_creation'] = (int)$sys['now'];
			$rmsg['fp_updater'] = 0;

			$db->insert($db_forum_posts, $rmsg);
			$p = $db->lastInsertId();

			$sql_forums = $db->query("UPDATE $db_forum_topics SET
				ft_postcount=ft_postcount+1, ft_updated=" . $sys['now'] . ",
				ft_lastposterid=" . $usr['id'] . ", ft_lastpostername=" . $db->quote($usr['name']) . " WHERE ft_id=$q");

			cot_forums_sectionsetlast($s, 'fs_postcount+1');

			if ($cfg['forums']['cat_' . $s]['countposts'])
			{
				$sql_forums = $db->query("UPDATE $db_users SET user_postcount=user_postcount+1 WHERE user_id=" . $usr['id']);
			}
		}
		else
		{
			$p = (int)$row['fp_id'];

			$gap_base = empty($row['fp_updated']) ? $row['fp_creation'] : $row['fp_updated'];
			$updated = sprintf($L['forums_mergetime'], cot_build_timegap($gap_base, $sys['now']));

			$rmsg['fp_text'] = $row['fp_text'] . cot_rc('forums_code_update', array('updated' => $updated)) . $rmsg['fp_text'];
			$rmsg['fp_updater'] = ($row['fp_posterid'] == $usr['id'] && ($sys['now'] < $row['fp_updated'] + 300) && empty($row['fp_updater']) ) ? '' : $usr['name'];

			$db->update($db_forum_posts, $rmsg, 'fp_id=' . $row['fp_id']);
			$db->update($db_forum_topics, array('ft_updated' => $sys['now']), "ft_id = $q");

			cot_forums_sectionsetlast($s);
		}

		cot_extrafield_movefiles();

		/* === Hook === */
		foreach (cot_getextplugins('forums.posts.newpost.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($cache)
		{
			($cfg['cache_forums']) && $cache->page->clear('forums');
			($cfg['cache_index']) && $cache->page->clear('index');
		}

		cot_shield_update(30, "New post");
		cot_redirect(cot_url('forums', "m=posts&q=$q&n=last", '#bottom', true));
	}
}
elseif ($a == 'delete' && $usr['id'] > 0 && !empty($s) && !empty($q) && !empty($p) && ($usr['isadmin'] || ($fp_posterid == $usr['id'] && ($cfg['forums']['edittimeout'] == '0' || $sys['now'] - $row['fp_creation'] < $cfg['forums']['edittimeout'] * 3600))))
{
	cot_check_xg();



	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.delete.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$row = $db->query("SELECT * FROM $db_forum_posts WHERE fp_id = ? AND fp_topicid = ? AND fp_cat = ? LIMIT 1",
		array($p, $q, $s))->fetch();
	is_array($row) || cot_die();

	// If the post is first in the topic, then delete entire topic or show an error
	$first_id = $db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = ? LIMIT 1", array($q))->fetchColumn();
	if ($p == $first_id)
	{
		if ($usr['isadmin'])
		{
			// Redirect to topic removal confirmation
			cot_redirect(str_replace('&amp;', '&', cot_confirm_url(cot_url('forums', 'm=topics&a=delete&s='.$s.'&q='.$q.'&x='.$sys['xk'], '', true), 'forums', 'forums_confirm_delete_topic')));
		}
		else
		{
			// Users can't delete topics
			cot_die();
		}
	}

	foreach($cot_extrafields[$db_forum_posts] as $exfld)
	{
		cot_extrafield_unlinkfiles($row['fp_'.$exfld['field_name']], $exfld);
	}

	$sql_forums = $db->delete($db_forum_posts, 'fp_id = ? AND fp_topicid = ? AND fp_cat = ?', array($p, $q, $s));

	if ($cfg['forums']['cat_' . $s]['countposts'])
	{
		$sql_forums = $db->query("UPDATE $db_users SET user_postcount=user_postcount-1 WHERE user_id='" . $fp_posterid . "' AND user_postcount>0");
	}

	cot_log("Deleted post #" . $p, 'for');

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.delete.done') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($cache)
	{
		($cfg['cache_forums']) && $cache->page->clear('forums');
		($cfg['cache_index']) && $cache->page->clear('index');
	}

	if ($db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid= $q")->fetchColumn() == 0)
	{
		$sql_forums = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id = $q");
		if ($row = $sql_forums->fetch())
		{
			$sql_forums = $db->delete($db_forum_topics, "ft_movedto = $q");
			$sql_forums = $db->delete($db_forum_topics, "ft_id = $q");

			foreach($cot_extrafields[$db_forum_topics] as $exfld)
			{
				cot_extrafield_unlinkfiles($row['ft_'.$exfld['field_name']], $exfld);
			}

			/* === Hook === */
			foreach (cot_getextplugins('forums.posts.emptytopicdel') as $pl)
			{
				include $pl;
			}
			/* ===== */

			cot_log('Delete topic #' . $q . " (no post left)", 'for');
			cot_forums_sectionsetlast($s, 'fs_postcount-1', 'fs_topiccount-1');
		}
		cot_redirect(cot_url('forums', 'm=topics&s=' . $s, '', true));
	}
	else
	{
		// There's at least 1 post left, let's resync
		$sql_forums = $db->query("SELECT fp_id, fp_posterid, fp_postername, fp_updated FROM $db_forum_posts
			WHERE fp_topicid = ? AND fp_cat = ? ORDER BY fp_id DESC LIMIT 1",
			array($q, $s));
		if ($row = $sql_forums->fetch())
		{
			$sql_forums = $db->query("UPDATE $db_forum_topics SET
				ft_postcount=ft_postcount-1, ft_lastposterid=" . (int)$row['fp_posterid'] . ",
				ft_lastpostername=" . $db->quote($row['fp_postername']) . ", ft_updated=" . (int)$row['fp_updated'] . "
				WHERE ft_id = $q");

			cot_forums_sectionsetlast($s, 'fs_postcount-1');

			cot_redirect(cot_url('forums', 'm=posts&p=' . $row['fp_id'], '#' . $row['fp_id'], true));
		}
	}
}

$sql_forums = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id= $q");
if ($rowt = $sql_forums->fetch())
{
	if ($rowt['ft_mode'] == 1 && !($usr['isadmin'] || $rowt['ft_firstposterid'] == $usr['id']))
	{
		cot_die();
	}
}
else
{
	cot_die(true, true);
}

$sql_forums = $db->query("UPDATE $db_forum_topics SET ft_viewcount=ft_viewcount+1 WHERE ft_id = $q");
$sql_forums = $db->query("UPDATE $db_forum_stats SET fs_viewcount=fs_viewcount+1 WHERE fs_cat = " . $db->quote($s));

$where['topicid'] = "fp_topicid = $q";
$order = 'fp_id ASC';
$join_columns = '';
$join_condition = '';

if (!empty($p) || !empty($id))
{
	$p_id = empty($p) ? $id : $p;
	$postsbefore = $db->query("SELECT COUNT(*) FROM $db_forum_posts AS p $join_condition WHERE " . implode(' AND ', $where) . " AND fp_id < $p_id")->fetchColumn();
	$d = $cfg['forums']['maxpostsperpage'] * floor($postsbefore / $cfg['forums']['maxpostsperpage']);
}

if (!empty($id))
{
	$where['id'] = "fp_id = $id";
}

/* === Hook === */
foreach (cot_getextplugins('forums.posts.query') as $pl)
{
	include $pl;
}
/* ===== */

$where = array_diff($where, array(''));
$totalposts = $db->query("SELECT COUNT(*) FROM $db_forum_posts AS p $join_condition WHERE " . implode(' AND ', $where))->fetchColumn();

// Disallow accessing non-existent pages
if ($totalposts > 0 && $d > $totalposts)
{
	cot_die_message(404);
}

$orderlimit = empty($id) ? " ORDER BY $order LIMIT $d, " . $cfg['forums']['maxpostsperpage'] : '';

$sql_forums = $db->query("SELECT p.*, u.* $join_columns
	FROM $db_forum_posts AS p LEFT JOIN $db_users AS u ON u.user_id=p.fp_posterid $join_condition
	WHERE " . implode(' AND ', $where) . $orderlimit);

$pg = floor($d / $cfg['forums']['maxpostsperpage']) + 1;
$durl = ($cfg['easypagenav']) ? $pg : $d;

$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => $structure['forums'][$s]['title'],
	'TITLE' => $rowt['ft_title']
);
$out['subtitle'] = cot_title($cfg['forums']['title_posts'], $title_params);
$out['desc'] = htmlspecialchars(strip_tags($rowt['ft_desc']));
$topicurl_params = array(
	'm' => 'posts',
	'q' => $q
);
if ($durl > 1)
{
	$topicurl_params['d'] = $durl;
}
$out['canonical_uri'] = cot_url('forums', $topicurl_params);

/* === Hook === */
foreach (cot_getextplugins('forums.posts.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('forums', 'posts', $structure['forums'][$s]['tpl']));
$t = new XTemplate($mskin);


/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.posts.loop');
/* ===== */
$fp_num = 0;
foreach ($sql_forums->fetchAll() as $row)
{
	$row['user_text'] = ($cfg['forums']['cat_' . $s]['allowusertext']) ? $row['user_text'] : '';
	$fp_num++;

	$rowquote_url = ($usr['id'] > 0) ? cot_url('forums', 'm=posts&s=' . $s . '&q=' . $q . '&quote=' . $row['fp_id'] . '&n=last', '#np') : '';
	$rowquote = ($usr['id'] > 0) ? cot_rc('forums_rowquote', array('url' => $rowquote_url)) : '';
	$rowedit_url = (($usr['isadmin'] || ($row['fp_posterid'] == $usr['id'] && ($cfg['forums']['edittimeout'] == '0' || $sys['now'] - $row['fp_creation'] < $cfg['forums']['edittimeout'] * 3600))) && $usr['id'] > 0) ? cot_url('forums', 'm=editpost&s=' . $s . '&q=' . $q . '&p=' . $row['fp_id'] . '&' . cot_xg()) : '';
	$rowedit = (($usr['isadmin'] || ($row['fp_posterid'] == $usr['id'] && ($cfg['forums']['edittimeout'] == '0' || $sys['now'] - $row['fp_creation'] < $cfg['forums']['edittimeout'] * 3600))) && $usr['id'] > 0) ? cot_rc('forums_rowedit', array('url' => $rowedit_url)) : '';
	$rowdelete_url = ($usr['id'] > 0 && ($usr['isadmin'] || ($row['fp_posterid'] == $usr['id'] && ($cfg['forums']['edittimeout'] == '0' || $sys['now'] - $row['fp_creation'] < $cfg['forums']['edittimeout'] * 3600)))) ? cot_confirm_url(cot_url('forums', 'm=posts&a=delete&' . cot_xg() . '&s=' . $s . '&q=' . $q . '&p=' . $row['fp_id']), 'forums', 'forums_confirm_delete_post') : '';
	$rowdelete = ($usr['id'] > 0 && ($usr['isadmin'] || ($row['fp_posterid'] == $usr['id'] && ($cfg['forums']['edittimeout'] == '0' || $sys['now'] - $row['fp_creation'] < $cfg['forums']['edittimeout'] * 3600)) && $fp_num > 1)) ? cot_rc('forums_rowdelete', array('url' => $rowdelete_url)) : '';

	if (!empty($row['fp_updater']))
	{
		$row['fp_updatedby'] = sprintf($L['forums_updatedby'], htmlspecialchars($row['fp_updater']), cot_date('datetime_medium', $row['fp_updated']), cot_build_timegap($row['fp_updated'], $sys['now']));
	}

	$t->assign(cot_generate_usertags($row, 'FORUMS_POSTS_ROW_USER'));
	$t->assign(array(
		'FORUMS_POSTS_ROW_ID' => $row['fp_id'],
		'FORUMS_POSTS_ROW_POSTID' => 'post_' . $row['fp_id'],
		'FORUMS_POSTS_ROW_IDURL' => cot_url('forums', 'm=posts&id=' . $row['fp_id']),
		'FORUMS_POSTS_ROW_URL' => cot_url('forums', 'm=posts&p=' . $row['fp_id'], "#" . $row['fp_id']),
		'FORUMS_POSTS_ROW_CREATION' => cot_date('datetime_medium', $row['fp_creation']),
		'FORUMS_POSTS_ROW_CREATION_STAMP' => $row['fp_creation'],
		'FORUMS_POSTS_ROW_UPDATED' => cot_date('datetime_medium', $row['fp_updated']),
		'FORUMS_POSTS_ROW_UPDATED_STAMP' => $row['fp_updated'],
		'FORUMS_POSTS_ROW_UPDATER' => htmlspecialchars($row['fp_updater']),
		'FORUMS_POSTS_ROW_UPDATEDBY' => $row['fp_updatedby'],
		'FORUMS_POSTS_ROW_TEXT' => cot_parse($row['fp_text'], ($cfg['forums']['markup'] && $cfg['forums']['cat_' . $s]['allowbbcodes'])),
		'FORUMS_POSTS_ROW_ANCHORLINK' => cot_rc('forums_code_post_anchor', array('id' => $row['fp_id'])),
		'FORUMS_POSTS_ROW_POSTERNAME' => cot_build_user($row['fp_posterid'], htmlspecialchars($row['fp_postername'])),
		'FORUMS_POSTS_ROW_POSTERID' => $row['fp_posterid'],
		'FORUMS_POSTS_ROW_POSTERIP' => ($usr['isadmin']) ? cot_build_ipsearch($row['fp_posterip']) : '',
		'FORUMS_POSTS_ROW_DELETE' => $rowdelete,
		'FORUMS_POSTS_ROW_DELETE_URL' => $rowdelete_url,
		'FORUMS_POSTS_ROW_EDIT' => $rowedit,
		'FORUMS_POSTS_ROW_EDIT_URL' => $rowedit_url,
		'FORUMS_POSTS_ROW_QUOTE' => $rowquote,
		'FORUMS_POSTS_ROW_QUOTE_URL' => $rowquote_url,
		'FORUMS_POSTS_ROW_BOTTOM' => ((empty($id) ? $d + $fp_num : $id) == $totalposts) ? $R['forums_code_bottom'] :
			(($usr['id'] > 0 && $n == 'unread' && $row['fp_creation'] > $usr['lastvisit']) ? $R['forums_code_unread'] : ''),
		'FORUMS_POSTS_ROW_ODDEVEN' => cot_build_oddeven($fp_num),
		'FORUMS_POSTS_ROW_NUM' => $fp_num,
		'FORUMS_POSTS_ROW_ORDER' => empty($id) ? $d + $fp_num : $id
	));

	foreach ($cot_extrafields[$db_forum_posts] as $exfld)
	{
		$tag = mb_strtoupper($exfld['field_name']);
		$t->assign(array(
			'FORUMS_POSTS_ROW_'.$tag.'_TITLE' => isset($L['forums_posts_'.$exfld['field_name'].'_title']) ?  $L['forums_posts_'.$exfld['field_name'].'_title'] : $exfld['field_description'],
			'FORUMS_POSTS_ROW_'.$tag => cot_build_extrafields_data('forums', $exfld, $row['fp_'.$exfld['field_name']], ($cfg['forums']['markup'] && $cfg['forums']['cat_' . $s]['allowbbcodes']))
		));
	}

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.FORUMS_POSTS_ROW');
}

$lastpage = (($d + $cfg['forums']['maxpostsperpage']) < $totalposts) ? FALSE : TRUE;
$pagenav = cot_pagenav('forums', "m=posts&q=$q", $d, $totalposts, $cfg['forums']['maxpostsperpage']);

$jumpbox[cot_url('forums')] = $L['Forums'];
foreach ($structure['forums'] as $key => $val)
{
	if (cot_auth('forums', $key, 'R'))
	{
		($val['tpath'] == $s) || $movebox[$key] = $val['tpath'];
		$jumpbox[cot_url('forums', 'm=topics&s=' . $key, '', true)] = $val['tpath'];
	}
}

if ($usr['isadmin'])
{
	$t->assign(array(
		'FORUMS_POSTS_MOVE_URL' => cot_url('forums', 'm=topics&a=move&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_BUMP_URL' => cot_url('forums', 'm=topics&a=bump&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_LOCK_URL' => cot_url('forums', 'm=topics&a=lock&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_STICKY_URL' => cot_url('forums', 'm=topics&a=sticky&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_ANNOUNCE_URL' => cot_url('forums', 'm=topics&a=announcement&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_PRIVATE_URL' => cot_url('forums', 'm=topics&a=private&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_CLEAR_URL' => cot_url('forums', 'm=topics&a=clear&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_DELETE_URL' => cot_confirm_url(cot_url('forums', 'm=topics&a=delete&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']), 'forums', 'forums_confirm_delete_topic'),
		'FORUMS_POSTS_MOVEBOX_SELECT' => cot_selectbox('', 'ns', array_keys($movebox), array_values($movebox), false),
		'FORUMS_POSTS_MOVEBOX_KEEP' => cot_checkbox('0', 'ghost')
	));
	$t->parse('MAIN.FORUMS_POSTS_ADMIN');
}

$allowreplybox = ($cfg['forums']['antibumpforums'] && $row['fp_posterid'] > 0 && $row['fp_posterid'] == $usr['id'] && $usr['auth_write']) ? FALSE : TRUE;

if (($cfg['forums']['enablereplyform'] || $lastpage) && !$rowt['ft_state'] && $usr['id'] > 0 && $allowreplybox && $usr['auth_write'])
{
	if ($quote > 0)
	{
		$sql_forums_quote = $db->query("SELECT fp_id, fp_text, fp_postername, fp_posterid, fp_creation FROM $db_forum_posts
			WHERE fp_topicid = ? AND fp_cat = ? AND fp_id = ? LIMIT 1",
			array($q, $s, $quote));
		if ($row4 = $sql_forums_quote->fetch())
		{
			$rmsg['fp_text'] = cot_rc('forums_code_quote', array(
				'url' => cot_url('forums', 'm=posts&p=' . $row4['fp_id'], '#' . $row4['fp_id'], $forums_quote_htmlspecialchars_bypass),
				'id' => $row4['fp_id'],
				'date' => cot_date('datetime_medium', $row4['fp_creation']),
				'postername' => $row4['fp_postername'],
				'text' => $row4['fp_text']
			));
		}
	}

		// Extra fields
	foreach($cot_extrafields[$db_forum_posts] as $exfld)
	{
		$uname = strtoupper($exfld['field_name']);
		$exfld_val = cot_build_extrafields('rmsg'.$exfld['field_name'], $exfld, $rmsg[$exfld['field_name']]);
		$exfld_title = isset($L['forums_posts_'.$exfld['field_name'].'_title']) ?  $L['forums_posts_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
		$t->assign(array(
			'FORUMS_POSTS_NEWPOST_'.$uname => $exfld_val,
			'FORUMS_POSTS_NEWPOST_'.$uname.'_TITLE' => $exfld_title,
			'FORUMS_POSTS_NEWPOST_EXTRAFLD' => $exfld_val,
			'FORUMS_POSTS_NEWPOST_EXTRAFLD_TITLE' => $exfld_title
			));
		$t->parse('MAIN.FORUMS_POSTS_NEWPOST.EXTRAFLD');
	}

	$t->assign(array(
		'FORUMS_POSTS_NEWPOST_SEND' => cot_url('forums', "m=posts&a=newpost&s=" . $s . "&q=" . $q),
		'FORUMS_POSTS_NEWPOST_TEXT' => $R['forums_code_newpost_mark'] . cot_textarea('rmsgtext', $rmsg['fp_text'], 16, 56, '', 'input_textarea_medieditor'),
            	'FORUMS_POSTS_NEWPOST_EDITTIMEOUT' => cot_build_timegap(0, $cfg['forums']['edittimeout'] * 3600)
	));

	cot_display_messages($t);

	/* === Hook  === */
	foreach (cot_getextplugins('forums.posts.newpost.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.FORUMS_POSTS_NEWPOST');
}
elseif ($rowt['ft_state'])
{
	$t->assign('FORUMS_POSTS_TOPICLOCKED_BODY', $L['forums_topiclocked']);
	$t->parse('MAIN.FORUMS_POSTS_TOPICLOCKED');
}
elseif (!$allowreplybox && ($cfg['forums']['enablereplyform'] || $lastpage) && !$rowt['ft_state'] && $usr['id'] > 0)
{
	$t->assign('FORUMS_POSTS_ANTIBUMP_BODY', $L['forums_antibump']);
	$t->parse('MAIN.FORUMS_POSTS_ANTIBUMP');
}

if ($rowt['ft_mode'] == 1)
{
	$t->parse('MAIN.FORUMS_POSTS_TOPICPRIVATE');
}


$rowt['ft_title'] = (($rowt['ft_mode'] == 1) ? '# ' : '') . $rowt['ft_title'];

$crumbs = cot_forums_buildpath($s);
$toppath = cot_breadcrumbs($crumbs, $cfg['homebreadcrumb']);
$crumbs[] = $rowt['ft_title'];
$toptitle = cot_breadcrumbs($crumbs, $cfg['homebreadcrumb'], true);
$toptitle .= ( $usr['isadmin']) ? $R['forums_code_admin_mark'] : '';

$t->assign(array(
	'FORUMS_POSTS_ID' => $q,
	'FORUMS_POSTS_RSS' => cot_url('rss', "c=topics&id=$q"),
	'FORUMS_POSTS_PAGETITLE' => $toptitle,
	'FORUMS_POSTS_TOPICDESC' => htmlspecialchars($rowt['ft_desc']),
	'FORUMS_POSTS_SHORTTITLE' => htmlspecialchars($rowt['ft_title']),
	'FORUMS_POSTS_CATTITLE' => htmlspecialchars($structure['forums'][$s]['title']),
	'FORUMS_POSTS_PATH' => $toppath,
	'FORUMS_POSTS_PAGES' => $pagenav['main'],
	'FORUMS_POSTS_PAGEPREV' => $pagenav['prev'],
	'FORUMS_POSTS_PAGENEXT' => $pagenav['next'],
	'FORUMS_POSTS_CURRENTPAGE' => $pagenav['current'],
	'FORUMS_POSTS_TOTALPAGES' => ceil($totalposts / $cfg['forums']['maxpostsperpage']),
	'FORUMS_POSTS_JUMPBOX' => cot_selectbox($s, 'jumpbox', array_keys($jumpbox), array_values($jumpbox), false, 'onchange="redirect(this)"'),
));

foreach ($cot_extrafields[$db_forum_topics] as $exfld)
{
	$tag = mb_strtoupper($exfld['field_name']);
	$t->assign(array(
		'FORUMS_POSTS_TOPIC_'.$tag.'_TITLE' => isset($L['forums_topics_'.$exfld['field_name'].'_title']) ?  $L['forums_topics_'.$exfld['field_name'].'_title'] : $exfld['field_description'],
		'FORUMS_POSTS_TOPIC_'.$tag => cot_build_extrafields_data('forums', $exfld, $rowt['ft_'.$exfld['field_name']], ($cfg['forums']['markup'] && $cfg['forums']['cat_' . $s]['allowbbcodes']))
	));
}

/* === Hook  === */
foreach (cot_getextplugins('forums.posts.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_forums'])
{
	$cache->page->write();
}
?>
