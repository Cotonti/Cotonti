<?php

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package recentitems
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */
defined('COT_CODE') or die("Wrong URL.");

require_once cot_incfile('extrafields');
require_once cot_langfile('recentitems', 'plug');

function cot_build_recentforums($template, $mode = 'recent', $maxperpage = 5, $d = 0, $titlelength = 0, $rightprescan = true)
{
	global $db, $L, $cfg, $db_forum_topics, $theme, $usr, $sys, $R, $structure;
	global $totalrecent;
	$recentitems = new XTemplate(cot_tplfile($template, 'plug'));

	if ($rightprescan)
	{
		$catsub = cot_structure_children('forums', '', true, true, $rightprescan);
		$incat = "AND ft_cat IN ('" . implode("','", $catsub) . "')";
	}
	/* === Hook === */
	foreach (cot_getextplugins('recentitems.recentforums.first') as $pl)
	{
		include $pl;
	}
	/* ===== */
	if ($mode == 'recent')
	{
		$sql = $db->query("SELECT * FROM $db_forum_topics
			WHERE (ft_movedto IS NULL OR ft_movedto = '') AND ft_mode=0 " . $incat . "
			ORDER by ft_updated DESC LIMIT $maxperpage");
		$totalrecent['topics'] = $maxperpage;
	}
	else
	{
		$where = "WHERE ft_updated >= $mode " . $incat;
		$totalrecent['topics'] = $db->query("SELECT COUNT(*) FROM $db_forum_topics " . $where)->fetchColumn();
		$sql = $db->query("SELECT * FROM $db_forum_topics " . $where . " ORDER by ft_updated desc LIMIT $d, " . $maxperpage);
	}
	$ft_num = 0;
	while ($row = $sql->fetch())
	{
		$row['ft_icon'] = 'posts';
		$row['ft_postisnew'] = FALSE;
		$row['ft_pages'] = '';
		$ft_num++;
		if ((int)$titlelength > 0 && mb_strlen($row['ft_title']) > $titlelength)
		{
			$row['ft_title'] = cot_string_truncate($row['ft_title'], $titlelength, false). "...";
		}
		$build_forum = cot_breadcrumbs(cot_forums_buildpath($row['ft_cat'], false), false);
		$build_forum_short = cot_rc_link(cot_url('forums', 'm=topics&s=' . $row['ft_cat']), htmlspecialchars($structure['forums'][$row['ft_cat']]['title']));

		if ($row['ft_mode'] == 1)
		{
			$row['ft_title'] = "# " . $row['ft_title'];
		}

		if ($row['ft_movedto'] > 0)
		{
			$row['ft_url'] = cot_url('forums', 'm=posts&q=' . $row['ft_movedto']);
			$row['ft_icon'] = $R['forums_icon_posts_moved'];
			$row['ft_title'] = $L['Moved'] . ": " . $row['ft_title'];
			$row['ft_lastpostername'] = $R['forums_code_post_empty'];
			$row['ft_postcount'] = $R['forums_code_post_empty'];
			$row['ft_replycount'] = $R['forums_code_post_empty'];
			$row['ft_viewcount'] = $R['forums_code_post_empty'];
			$row['ft_lastpostername'] = $R['forums_code_post_empty'];
			$row['ft_lastposturl'] = cot_url('forums', 'm=posts&q=' . $row['ft_movedto'] . '&n=last', '#bottom');
			$row['ft_lastpostlink'] = cot_rc_link($row['ft_lastposturl'], $R['icon_follow']) . ' ' . $L['Moved'];
			$row['ft_timeago'] = cot_build_timegap($row['ft_updated'], $sys['now']);
		}
		else
		{
			$row['ft_url'] = cot_url('forums', 'm=posts&q=' . $row['ft_id']);
			$row['ft_lastposturl'] = ($usr['id'] > 0 && $row['ft_updated'] > $usr['lastvisit']) ?
				cot_url('forums', 'm=posts&q=' . $row['ft_id'] . '&n=unread', '#unread') :
				cot_url('forums', 'm=posts&q=' . $row['ft_id'] . '&n=last', '#bottom');
			$row['ft_lastpostlink'] = ($usr['id'] > 0 && $row['ft_updated'] > $usr['lastvisit']) ?
				cot_rc_link($row['ft_lastposturl'], $R['icon_unread'], 'rel="nofollow"') :
				cot_rc_link($row['ft_lastposturl'], $R['icon_follow'], 'rel="nofollow"');
			$row['ft_lastpostlink'] .= cot_date('datetime_medium', $row['ft_updated']);
			$row['ft_timeago'] = cot_build_timegap($row['ft_updated'], $sys['now']);
			$row['ft_replycount'] = $row['ft_postcount'] - 1;

			if ($row['ft_updated'] > $usr['lastvisit'] && $usr['id'] > 0)
			{
				$row['ft_icon'] .= '_new';
				$row['ft_postisnew'] = TRUE;
			}

			if ($row['ft_postcount'] >= $cfg['forums']['hottopictrigger'] && !$row['ft_state'] && !$row['ft_sticky'])
			{
				$row['ft_icon'] = ($row['ft_postisnew']) ? 'posts_new_hot' : 'posts_hot';
			}
			else
			{
				if ($row['ft_sticky'])
				{
					$row['ft_icon'] .= '_sticky';
				}

				if ($row['ft_state'])
				{
					$row['ft_icon'] .= '_locked';
				}
			}

			$row['ft_icon_type'] = $row['ft_icon'];
			$row['ft_icon'] = cot_rc('forums_icon_topic_t', array('icon' => $row['ft_icon'], 'title' => $L['recentitems_' . $row['ft_icon']]));
			$row['ft_lastpostername'] = cot_build_user($row['ft_lastposterid'], htmlspecialchars($row['ft_lastpostername']));
		}

		$row['ft_firstpostername'] = cot_build_user($row['ft_firstposterid'], htmlspecialchars($row['ft_firstpostername']));

		if ($row['ft_postcount'] > $cfg['forums']['maxtopicsperpage'] && $cfg['forums']['maxtopicsperpage'] > 0)
		{
			$row['ft_maxpages'] = ceil($row['ft_postcount'] / $cfg['forums']['maxtopicsperpage']);
			$row['ft_pages'] = $L['Pages'] . ":";
		}

		$recentitems->assign(array(
			'FORUM_ROW_ID' => $row['ft_id'],
			'FORUM_ROW_STATE' => $row['ft_state'],
			'FORUM_ROW_ICON' => $row['ft_icon'],
			'FORUM_ROW_ICON_TYPE' => $row['ft_icon_type'],
			'FORUM_ROW_TITLE' => htmlspecialchars($row['ft_title']),
			'FORUM_ROW_PATH' => $build_forum,
			'FORUM_ROW_PATH_SHORT' => $build_forum_short,
			'FORUM_ROW_DESC' => htmlspecialchars($row['ft_desc']),
			'FORUM_ROW_PREVIEW' => $row['ft_preview'] . '...',
			'FORUM_ROW_CREATIONDATE' => cot_date('datetime_short', $row['ft_creationdate']),
			'FORUM_ROW_CREATIONDATE_STAMP' => $row['ft_creationdate'],
			'FORUM_ROW_UPDATED' => $row['ft_lastpostlink'],
			'FORUM_ROW_UPDATED_STAMP' => $row['ft_updated'],
			'FORUM_ROW_TIMEAGO' => $row['ft_timeago'],
			'FORUM_ROW_POSTCOUNT' => $row['ft_postcount'],
			'FORUM_ROW_REPLYCOUNT' => $row['ft_replycount'],
			'FORUM_ROW_VIEWCOUNT' => $row['ft_viewcount'],
			'FORUM_ROW_FIRSTPOSTER' => $row['ft_firstpostername'],
			'FORUM_ROW_LASTPOSTER' => $row['ft_lastpostername'],
			'FORUM_ROW_LASTPOSTURL' => $row['ft_lastposturl'],
			'FORUM_ROW_URL' => $row['ft_url'],
			'FORUM_ROW_PAGES' => $row['ft_pages'],
			'FORUM_ROW_MAXPAGES' => $row['ft_maxpages'],
			'FORUM_ROW_NUM' => $ft_num,
			'FORUM_ROW_ODDEVEN' => cot_build_oddeven($ft_num),
			'FORUM_ROW' => $row
		));
		$recentitems->parse('MAIN.TOPICS_ROW');
	}
	$sql->closeCursor();

	if ($d == 0 && $ft_num == 0)
	{
		$recentitems->parse('MAIN.NO_TOPICS_FOUND');
	}

	$recentitems->parse('MAIN');

	return ($d == 0 || $ft_num > 0) ? $recentitems->text('MAIN') : '';
}

function cot_build_recentpages($template, $mode = 'recent', $maxperpage = 5, $d = 0, $titlelength = 0, $textlength = 0, $rightprescan = true, $cat = '')
{
	global $db, $structure, $db_pages, $db_users, $sys, $cfg, $L, $cot_extrafields, $usr;
	$recentitems = new XTemplate(cot_tplfile($template, 'plug'));

	// Load all cats and subcats in white list if set
	if (!empty($cfg['plugin']['recentitems']['whitelist']))
	{
		$whitelist = array();
		foreach (preg_split('#\r?\n#', $cfg['plugin']['recentitems']['whitelist']) as $c)
		{
			$whitelist = array_merge($whitelist, cot_structure_children('page', $c, true, true, $rightprescan));
		}
	}
	else
	{
		$whitelist = false;
	}

	// Load all cats and subcats in black list if set
	if (!empty($cfg['plugin']['recentitems']['blacklist']))
	{
		$blacklist = array();
		foreach (preg_split('#\r?\n#', $cfg['plugin']['recentitems']['blacklist']) as $c)
		{
			$blacklist = array_merge($blacklist, cot_structure_children('page', $c, true, true, $rightprescan));
		}
	}
	else
	{
		$blacklist = false;
	}

	if ($rightprescan || $cat)
	{
		// Get selected cats
		$catsub = cot_structure_children('page', $cat, true, true, $rightprescan);
		if ($whitelist)
		{
			// Must be both in selected and whitelist
			$catsub = array_intersect($catsub, $whitelist);
		}
		elseif ($blacklist)
		{
			// Must be in selected but not in blacklist
			$catsub = array_diff($catsub, $blacklist);
		}
		$incat = "AND page_cat IN ('" . implode("','", $catsub) . "')";
	}
	elseif ($whitelist)
	{
		// Only cats from white list
		$incat = "AND page_cat IN ('" . implode("','", $whitelist) . "')";
	}
	elseif ($blacklist)
	{
		// All cats but not in black list
		$incat = "AND page_cat NOT IN ('" . implode("','", $blacklist) . "')";
	}

	if ($mode == 'recent')
	{
		$where = "WHERE page_state=0 AND page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']}) AND page_cat <> 'system' " . $incat;
		$totalrecent['pages'] = $cfg['plugin']['recentitems']['maxpages'];
	}
	else
	{
		$where = "WHERE page_date >= $mode AND page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']}) AND page_state=0 AND page_cat <> 'system' " . $incat;
		$totalrecent['pages'] = $db->query("SELECT COUNT(*) FROM $db_pages " . $where)->fetchColumn();
	}

	$join_columns = '';
	$join_tables = '';

	/* === Hook === */
	foreach (cot_getextplugins('recentitems.recentpages.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql = $db->query("SELECT p.*, u.* $join_columns
		FROM $db_pages AS p
			LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		$join_tables
		$where ORDER by page_date desc LIMIT $d, $maxperpage");

	$jj = 0;

	/* === Hook - Part1 === */
	$extp = cot_getextplugins('recentitems.recentpages.tags');
	/* ===== */
	foreach ($sql->fetchAll() as $pag)
	{
		$jj++;
		if ((int)$titlelength > 0 && mb_strlen($pag['page_title']) > $titlelength)
		{
			$pag['page_title'] = (cot_string_truncate($pag['page_title'], $titlelength, false)) . "...";
		}
		$recentitems->assign(cot_generate_pagetags($pag, 'PAGE_ROW_', $textlength));
		$recentitems->assign(array(
			'PAGE_ROW_SHORTTITLE' => htmlspecialchars($pag['page_title']),
			'PAGE_ROW_OWNER' => cot_build_user($pag['page_ownerid'], htmlspecialchars($pag['user_name'])),
			'PAGE_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'PAGE_ROW_NUM' => $jj
		));
		$recentitems->assign(cot_generate_usertags($pag, 'PAGE_ROW_OWNER_'));

		/* === Hook - Part2 === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$recentitems->parse('MAIN.PAGE_ROW');
	}

	if ($d == 0 && $jj == 0)
	{
		$recentitems->parse('MAIN.NO_PAGES_FOUND');
	}

	$recentitems->parse('MAIN');
	return ($d == 0 || $jj > 0) ? $recentitems->text('MAIN') : '';
}

?>