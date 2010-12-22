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

$id = cot_import('id', 'G', 'INT'); // post id
$s = cot_import('s', 'G', 'ALP'); // saction cat
$q = cot_import('q', 'G', 'INT'); // topic id
$p = cot_import('p', 'G', 'INT'); // post id
list($pg, $d) = cot_import_pagenav('d', $cfg['forums']['maxpostsperpage']); // page
$quote = cot_import('quote', 'G', 'INT');

require_once cot_langfile('countries', 'core');
require_once cot_incfile('forms');

/* === Hook === */
foreach (cot_getextplugins('forums.posts.first') as $pl)
{
	include $pl;
}
/* ===== */
if (!empty($n) || !empty($p) || !empty($id))
{
	if (!empty($q) && ($n == 'last' || ($n == 'unread' && $usr['id'] > 0)))
	{
		$sql = $db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid FROM $db_forum_posts WHERE fp_topicid='$q' ORDER by fp_id DESC LIMIT 1");
	}
	elseif ($n == 'unread' && !empty($q) && $usr['id'] > 0)
	{
		$sql = $db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid
			FROM $db_forum_posts WHERE fp_topicid='$q' AND fp_updated > " . $usr['lastvisit'] . " ORDER by fp_id ASC LIMIT 1");
	}
	elseif (!empty($p) || !empty($id))
	{
		$p = ($p > 0) ? $p : $id;
		$sql = $db->query("SELECT fp_topicid, fp_cat, fp_posterid FROM $db_forum_posts WHERE fp_id='$p' LIMIT 1");
	}
	if ($row = $sql->fetch())
	{
		$p = $row['fp_id'];
		$q = $row['fp_topicid'];
		$s = $row['fp_cat'];
		$fp_posterid = $row['fp_posterid'];
	}
}
elseif (!empty($q))
{
	$sql = $db->query("SELECT ft_cat FROM $db_forum_topics WHERE ft_id='$q' LIMIT 1");
	if ($row = $sql->fetch())
	{
		$s = $row['ft_cat'];
	}
}

(empty($s)) && cot_die();
isset($structure['forums'][$s]) || cot_die();
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

	$db->query("SELECT ft_state FROM $db_forum_topics WHERE ft_id='$q'")->fetchColumn() && cot_die();

	$sql = $db->query("SELECT fp_id, fp_text, fp_posterid, fp_creation, fp_updated, fp_updater FROM $db_forum_posts WHERE fp_topicid='" . $q . "' ORDER BY fp_creation DESC LIMIT 1");
	if ($row = $sql->fetch())
	{
		if ($cfg['forums']['antibumpforums'] && ( ($usr['id'] == 0 && $row['fp_posterid'] == 0 &&
			$row['fp_posterip'] == $usr['ip']) || ($row['fp_posterid'] > 0 && $row['fp_posterid'] == $usr['id']) ))
		{
			cot_die();
		}
		$merge = (!$cfg['forums']['antibumpforums'] && $cfg['forums']['mergeforumposts'] && $row['fp_posterid'] == $usr['id']) ? true : false;
		$merge = ($merge && $cfg['forums']['mergetimeout'] > 0 && (($sys['now_offset'] - $row['fp_updated']) > ($cfg['forums']['mergetimeout'] * 3600))) ? false : $merge;
	}
	else
	{
		cot_die();
	}
	$newmsg = cot_import('newmsg', 'P', 'HTM');

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.newpost.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!cot_error_found() && !empty($newmsg))
	{
		if (!$merge)
		{
			$db->insert($db_forum_posts, array(
				'fp_topicid' => (int)$q,
				'fp_cat' => (int)$s,
				'fp_posterid' => (int)$usr['id'],
				'fp_postername' => $usr['name'],
				'fp_creation' => (int)$sys['now_offset'],
				'fp_updated' => (int)$sys['now_offset'],
				'fp_updater' => 0,
				'fp_text' => $newmsg,
				'fp_posterip' => $usr['ip']
			));
			$p = $db->lastInsertId();

			$sql = $db->query("UPDATE $db_forum_topics SET
				ft_postcount=ft_postcount+1, ft_updated='" . $sys['now_offset'] . "',
				ft_lastposterid='" . $usr['id'] . "', ft_lastpostername='" . $db->prep($usr['name']) . "'
				WHERE ft_id='$q'");

			$sql = $db->query("UPDATE $db_forum_stats SET fs_postcount=fs_postcount+1 WHERE fs_cat='$s'");
			if ($cfg['forums'][$s]['countposts'])
			{
				$sql = $db->query("UPDATE $db_users SET user_postcount=user_postcount+1 WHERE user_id='" . $usr['id'] . "'");
			}
		}
		else
		{
			$p = (int)$row['fp_id'];

			$gap_base = empty($row['fp_updated']) ? $row['fp_creation'] : $row['fp_updated'];
			$updated = sprintf($L['forums_mergetime'], cot_build_timegap($gap_base, $sys['now_offset']));

			$newmsg = $row['fp_text'] . cot_rc('forums_code_update', array('updated' => $updated)) . $newmsg;

			$rupdater = ($row['fp_posterid'] == $usr['id'] && ($sys['now_offset'] < $row['fp_updated'] + 300) && empty($row['fp_updater']) ) ? '' : $usr['name'];
			$db->update($db_forum_posts, array("fp_text" => $newmsg, "fp_updated" => $sys['now_offset'], 
				"fp_updater" => $rupdater, "fp_posterip" => $usr['ip']), "fp_id='" . $row['fp_id'] . "' LIMIT 1");

			$db->update($db_forum_topics, array("ft_updated" => $sys['now_offset']), "ft_id='$q'");
		}
		/* === Hook === */
		foreach (cot_getextplugins('forums.posts.newpost.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		cot_forums_sectionsetlast($s);

		if ($cache)
		{
			($cfg['cache_forums']) && $cache->page->clear('forums');
			($cfg['cache_index']) && $cache->page->clear('index');
		}

		cot_shield_update(30, "New post");
		cot_redirect(cot_url('forums', "m=posts&q=" . $q . "&n=last", '#bottom', true));
	}
}
elseif ($a == 'delete' && $usr['id'] > 0 && !empty($s) && !empty($q) && !empty($p) && ($usr['isadmin'] || $fp_posterid == $usr['id']))
{
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.delete.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$row = $db->query("SELECT * FROM $db_forum_posts WHERE fp_id='$p' AND fp_topicid='$q' AND fp_cat='$s' LIMIT 1")->fetch();
	is_array($row) || cot_die();

	$sql = $db->delete($db_forum_posts, "fp_id='$p' AND fp_topicid='$q' AND fp_cat='$s'");

	if ($cfg['forums'][$s]['countposts'])
	{
		$sql = $db->query("UPDATE $db_users SET user_postcount=user_postcount-1 WHERE user_id='" . $fp_posterid . "' AND user_postcount>0");
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

	if ($db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$q'")->fetchColumn() == 0)
	{
		$sql = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id='$q'");
		if ($row = $sql->fetch())
		{
			$sql = $db->delete($db_forum_topics, "ft_movedto='$q'");
			$sql = $db->delete($db_forum_topics, "ft_id='$q'");

			$sql = $db->query("UPDATE $db_forum_stats SET fs_topiccount=fs_topiccount-1, fs_postcount=fs_postcount-1 WHERE fs_cat='$s'");

			/* === Hook === */
			foreach (cot_getextplugins('forums.posts.emptytopicdel') as $pl)
			{
				include $pl;
			}
			/* ===== */

			cot_log("Delete topic #" . $q . " (no post left)", 'for');
			cot_forums_sectionsetlast($s);
		}
		cot_redirect(cot_url('forums', "m=topics&s=" . $s, '', true));
	}
	else
	{
		// There's at least 1 post left, let's resync
		$sql = $db->query("SELECT fp_id, fp_posterid, fp_postername, fp_updated FROM $db_forum_posts WHERE fp_topicid='$q' AND fp_cat='$s' ORDER BY fp_id DESC LIMIT 1");
		if ($row = $sql->fetch())
		{
			$sql = $db->query("UPDATE $db_forum_topics SET
				ft_postcount=ft_postcount-1, ft_lastposterid='" . (int)$row['fp_posterid'] . "',
				ft_lastpostername='" . $db->prep($row['fp_postername']) . "', ft_updated='" . (int)$row['fp_updated'] . "'
				WHERE ft_id='$q'");

			$sql = $db->query("UPDATE $db_forum_stats SET fs_postcount=fs_postcount-1 WHERE fs_id='$s'");

			cot_forums_sectionsetlast($s);

			cot_redirect(cot_url('forums', "m=posts&p=" . $row['fp_id'], '#' . $row['fp_id'], true));
		}
	}
}

$sql = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id='$q'");
if ($rowt = $sql->fetch())
{
	if ($rowt['ft_mode'] == 1 && !($usr['isadmin'] || $rowt['ft_firstposterid'] == $usr['id']))
	{
		cot_die();
	}
}
else
{
	cot_die();
}

$sql = $db->query("UPDATE $db_forum_topics SET ft_viewcount=ft_viewcount+1 WHERE ft_id='$q'");
$sql = $db->query("UPDATE $db_forum_stats SET fs_viewcount=fs_viewcount+1 WHERE fs_cat='$s'");

$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => $structure['forums'][$s]['title'],
	'TITLE' => $rowt['ft_title']
);
$out['subtitle'] = cot_title('title_forum_posts', $title_params);
$out['desc'] = htmlspecialchars(strip_tags($rowt['ft_desc']));

/* === Hook === */
foreach (cot_getextplugins('forums.posts.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('forums', 'posts', $structure['forums'][$s]['tpl']));
$t = new XTemplate($mskin);

$where['topicid'] = "fp_topicid='$q'";
if (!empty($id))
{
	$where['id'] = "fp_id='$id'";
}
$order = "fp_id ASC";
$join_columns = '';
$join_condition = '';

/* === Hook === */
foreach (cot_getextplugins('forums.posts.query') as $pl)
{
	include $pl;
}
/* ===== */
$where = array_diff($where, array(''));
$totalposts = $db->query("SELECT COUNT(*) FROM $db_forum_posts AS p $join_condition WHERE " . implode(" AND ", $where))->fetchColumn();
if (!empty($p))
{
	$postsbefore = $db->query("SELECT COUNT(*) FROM $db_forum_posts AS p $join_condition WHERE " . implode(" AND ", $where) . " AND fp_id < $p")->fetchColumn();
	$d = $cfg['forums']['maxpostsperpage'] * floor($postsbefore / $cfg['forums']['maxpostsperpage']);
}

$sql = $db->query("SELECT p.*, u.* $join_columns
	FROM $db_forum_posts AS p LEFT JOIN $db_users AS u ON u.user_id=p.fp_posterid $join_condition
	WHERE " . implode(" AND ", $where) . " ORDER BY $order LIMIT $d, " . $cfg['forums']['maxpostsperpage']);

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.posts.loop');
/* ===== */
$fp_num = 0;
while ($row = $sql->fetch())
{
	$row['fp_updated'] = @date($cfg['dateformat'], $row['fp_updated'] + $usr['timezone'] * 3600);
	$row['user_text'] = ($cfg['forums'][$s]['allowusertext']) ? $row['user_text'] : '';
	$fp_num++;

	$rowquote = ($usr['id'] > 0) ? cot_rc('forums_rowquote', array('url' => cot_url('forums', "m=posts&s=" . $s . "&q=" . $q . "&quote=" . $row['fp_id'] . "&n=last", "#np"))) : '';
	$rowedit = (($usr['isadmin'] || $row['fp_posterid'] == $usr['id']) && $usr['id'] > 0) ? cot_rc('forums_rowedit', array('url' => cot_url('forums', "m=editpost&s=" . $s . "&q=" . $q . "&p=" . $row['fp_id'] . "&" . cot_xg()))) : '';
	$rowdelete = ($usr['id'] > 0 && ($usr['isadmin'] || $row['fp_posterid'] == $usr['id'])) ? cot_rc('forums_rowdelete', array('url' => cot_url('forums', "m=posts&a=delete&" . cot_xg() . "&s=" . $s . "&q=" . $q . "&p=" . $row['fp_id']))) : '';

	if (!empty($row['fp_updater']))
	{
		$row['fp_updatedby'] = sprintf($L['forums_updatedby'], htmlspecialchars($row['fp_updater']), @date($cfg['dateformat'], $row['fp_updated'] + $usr['timezone'] * 3600), cot_build_timegap($row['fp_updated'], $sys['now_offset']));
	}

	$t->assign(cot_generate_usertags($row, "FORUMS_POSTS_ROW_USER"));
	$t->assign(array(
		"FORUMS_POSTS_ROW_ID" => $row['fp_id'],
		"FORUMS_POSTS_ROW_POSTID" => 'post_' . $row['fp_id'],
		"FORUMS_POSTS_ROW_IDURL" => cot_url('forums', "m=posts&id=" . $row['fp_id']),
		"FORUMS_POSTS_ROW_URL" => cot_url('forums', "m=posts&p=" . $row['fp_id'], "#" . $row['fp_id']),
		"FORUMS_POSTS_ROW_CREATION" => @date($cfg['dateformat'], $row['fp_creation'] + $usr['timezone'] * 3600),
		"FORUMS_POSTS_ROW_UPDATED" => $row['fp_updated'],
		"FORUMS_POSTS_ROW_UPDATER" => htmlspecialchars($row['fp_updater']),
		"FORUMS_POSTS_ROW_UPDATEDBY" => $row['fp_updatedby'],
		"FORUMS_POSTS_ROW_TEXT" => cot_parse($row['fp_text'], ($cfg['forums']['markup'] && $cfg['forums'][$s]['allowbbcodes'])),
		"FORUMS_POSTS_ROW_ANCHORLINK" => cot_rc('forums_code_post_anchor', array('id' => $row['fp_id'])),
		"FORUMS_POSTS_ROW_POSTERNAME" => cot_build_user($row['fp_posterid'], htmlspecialchars($row['fp_postername'])),
		"FORUMS_POSTS_ROW_POSTERID" => $row['fp_posterid'],
		"FORUMS_POSTS_ROW_POSTERIP" => ($usr['isadmin']) ? cot_build_ipsearch($row['fp_posterip']) : '',
		"FORUMS_POSTS_ROW_DELETE" => $rowdelete,
		"FORUMS_POSTS_ROW_EDIT" => $rowedit,
		"FORUMS_POSTS_ROW_QUOTE" => $rowquote,
		"FORUMS_POSTS_ROW_BOTTOM" => ($fp_num == $totalposts) ? $R['forums_code_bottom'] : (($usr['id'] > 0 && $n == 'unread' && $row['fp_creation'] > $usr['lastvisit'])) ? $R['forums_code_unread'] : '',
		"FORUMS_POSTS_ROW_ODDEVEN" => cot_build_oddeven($fp_num),
		"FORUMS_POSTS_ROW_NUM" => $fp_num,
		"FORUMS_POSTS_ROW_ORDER" => empty($id) ? $d + $fp_num : $id
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse("MAIN.FORUMS_POSTS_ROW");
}

$notlastpage = (($d + $cfg['forums']['maxpostsperpage']) < $totalposts) ? TRUE : FALSE;
$pagenav = cot_pagenav('forums', "m=posts&q=$q", $d, $totalposts, $cfg['forums']['maxpostsperpage']);

$jumpbox[cot_url('forums')] = $L['Forums'];
foreach ($structure['forums'] as $key => $val)
{
	if (cot_auth('forums', $key, 'R'))
	{
		($val['tpath'] == $s) || $movebox[$key] = $val['tpath'];
		$jumpbox[cot_url('forums', "m=topics&s=" . $key, '', true)] = $val['tpath'];
	}
}

if ($usr['isadmin'])
{
	$t->assign(array(
		"FORUMS_POSTS_MOVE_URL" => cot_url('forums', 'm=topics&a=move&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		"FORUMS_POSTS_BUMP_URL" => cot_url('forums', 'm=topics&a=bump&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		"FORUMS_POSTS_LOCK_URL" => cot_url('forums', 'm=topics&a=lock&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		"FORUMS_POSTS_STICKY_URL" => cot_url('forums', 'm=topics&a=sticky&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		"FORUMS_POSTS_ANNOUNCE_URL" => cot_url('forums', 'm=topics&a=announcement&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		"FORUMS_POSTS_PRIVATE_URL" => cot_url('forums', 'm=topics&a=private&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		"FORUMS_POSTS_CLEAR_URL" => cot_url('forums', 'm=topics&a=clear&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		"FORUMS_POSTS_DELETE_URL" => cot_url('forums', 'm=topics&a=delete&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		"FORUMS_POSTS_MOVEBOX_SELECT" => cot_selectbox('', 'ns', array_keys($movebox), array_values($movebox), false),
		"FORUMS_POSTS_MOVEBOX_KEEP" => cot_checkbox('0', 'ghost')
	));
	$t->parse("MAIN.FORUMS_POSTS_ADMIN");
}

$allowreplybox = ($cfg['forums']['antibumpforums'] && $row['fp_posterid'] > 0 && $row['fp_posterid'] == $usr['id'] && $usr['auth_write']) ? FALSE : TRUE;

if (!$notlastpage && !$rowt['ft_state'] && $usr['id'] > 0 && $allowreplybox && $usr['auth_write'])
{
	if ($quote > 0)
	{
		$sql4 = $db->query("SELECT fp_id, fp_text, fp_postername, fp_posterid FROM $db_forum_posts WHERE fp_topicid='$q' AND fp_cat='$s' AND fp_id='$quote' LIMIT 1");
		if ($row4 = $sql4->fetch())
		{
			$newmsg = cot_rc('forums_code_quote', array(
				'url' => cot_url('forums', 'm=posts&p=' . $row4['fp_id'] . '#' . $row4['fp_id']),
				'id' => $row4['fp_id'],
				'postername' => $row4['fp_postername'],
				'text' => str_ireplace(array($R['forums_code_quote_begin'], $R['forums_code_quote_close']), '', $row4['fp_text'])
				));
		}
	}

	$t->assign(array(
		"FORUMS_POSTS_NEWPOST_SEND" => cot_url('forums', "m=posts&a=newpost&s=" . $s . "&q=" . $q),
		"FORUMS_POSTS_NEWPOST_TEXT" => $R['forums_code_newpost_mark'] . cot_textarea('newmsg', $newmsg, 16, 56, '', 'input_textarea_editor')
	));

	cot_display_messages($t);

	/* === Hook  === */
	foreach (cot_getextplugins('forums.posts.newpost.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse("MAIN.FORUMS_POSTS_NEWPOST");
}
elseif ($rowt['ft_state'])
{
	$t->assign("FORUMS_POSTS_TOPICLOCKED_BODY", $L['forums_topiclocked']);
	$t->parse("MAIN.FORUMS_POSTS_TOPICLOCKED");
}
elseif (!$allowreplybox && !$notlastpage && !$rowt['ft_state'] && $usr['id'] > 0)
{
	$t->assign("FORUMS_POSTS_ANTIBUMP_BODY", $L['forums_antibump']);
	$t->parse("MAIN.FORUMS_POSTS_ANTIBUMP");
}

if ($rowt['ft_mode'] == 1)
{
	$t->parse("MAIN.FORUMS_POSTS_TOPICPRIVATE");
}


$rowt['ft_title'] = (($rowt['ft_mode'] == 1) ? "# " : '') . htmlspecialchars($rowt['ft_title']);

$toptitle = cot_forums_buildpath($s);
$toppath = $toptitle;
$toptitle .= ' ' . $cfg['separator'] . ' ' . $rowt['ft_title'];
$toptitle .= ( $usr['isadmin']) ? $R['forums_code_admin_mark'] : '';

$t->assign(array(
	"FORUMS_POSTS_ID" => $q,
	"FORUMS_POSTS_RSS" => cot_url('rss', "c=topics&id=$q"),
	"FORUMS_POSTS_PAGETITLE" => $toptitle,
	"FORUMS_POSTS_TOPICDESC" => htmlspecialchars($rowt['ft_desc']),
	"FORUMS_POSTS_SHORTTITLE" => $rowt['ft_title'],
	"FORUMS_POSTS_PATH" => $toppath,
	"FORUMS_POSTS_PAGES" => $pagenav['main'],
	"FORUMS_POSTS_PAGEPREV" => $pagenav['prev'],
	"FORUMS_POSTS_PAGENEXT" => $pagenav['next'],
	"FORUMS_POSTS_CURRENTPAGE" => $d / $cfg['forums']['maxpostsperpage'],
	"FORUMS_POSTS_TOTALPAGES" => ceil($totalposts / $cfg['forums']['maxpostsperpage']),
	"FORUMS_POSTS_JUMPBOX" => cot_selectbox($s, 'jumpbox', array_keys($jumpbox), array_values($jumpbox), false, 'onchange="redirect(this)"'),
));

/* === Hook  === */
foreach (cot_getextplugins('forums.posts.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_forums'])
{
	$cache->page->write();
}
?>
