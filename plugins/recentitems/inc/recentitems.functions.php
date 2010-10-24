<?php
/**
 * Recent pages, topics in forums, users, comments
 *
 * @package recentitems
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die("Wrong URL.");

cot_require_api('extrafields');
cot_require_lang('recentitems', 'plug');

function cot_build_recentforums($template, $mode = 'recent', $maxperpage = 5, $d = 0, $titlelength = 0, $rightprescan = true)
{
	global $db, $totalrecent, $L, $cfg, $db_forum_sections, $db_forum_topics, $theme, $usr, $sys, $R;
	$recentitems = new XTemplate(cot_skinfile($template, true));
	if ($rightprescan)
	{
		// творим чудеса - читаем список разделов и к каким из них юзер имеет доступ
		$sql = $db->query("SELECT * FROM $db_forum_sections
			ORDER by fs_masterid DESC, fs_order ASC");
		unset($catsub);
		$catsub = array();
		$catsub[] = $cat;
		while ($fsn = $sql->fetch())
		{
			if (cot_auth('forums', $fsn['fs_id'], 'R'))
			{
				$catsub[] = $fsn['fs_id'];
				$forum_cats[$fsn['fs_id']] = $fsn;
			}
		}
		$incat = "AND ft_sectionid IN ('".implode("','", $catsub)."')";
	}

	//and ft_lastposterid!=".$usr['id']."

	if ($mode == 'recent')
	{
		$sql = $db->query("SELECT * FROM $db_forum_topics
			WHERE ft_movedto=0 AND ft_mode=0 ".$incat."
			ORDER by ft_updated DESC LIMIT $maxperpage");
		$totalrecent['topics'] = $maxperpage;
	}
	else
	{
		$where = "WHERE ft_updated >= $mode ".$incat;
		$sql = $db->query("SELECT COUNT(*) FROM $db_forum_topics ".$where);
		$totalrecent['topics'] = $sql->fetchColumn();
		$sql = $db->query("SELECT * FROM $db_forum_topics ".$where." ORDER by ft_updated desc LIMIT $d, ".$maxperpage);
	}
	$ft_num = 0;
	while ($row = $sql->fetch())
	{
		$row['ft_icon'] = 'posts';
		$row['ft_postisnew'] = FALSE;
		$row['ft_pages'] = '';
		$ft_num++;
		if ((int)$titlelength > 0)
		{
			if (cot_string_truncate($row['ft_title'], $titlelength, false))
			{
				$row['ft_title'] .= "...";
			}
		}
		$build_forum = cot_build_forums($row['ft_sectionid'], cot_cutstring($forum_cats[$row['ft_sectionid']]['fs_title'], 24), cot_cutstring($forum_cats[$row['ft_sectionid']]['fs_category'], 16));
		$build_forum_full = cot_build_forums($row['ft_sectionid'], cot_cutstring($forum_cats[$row['ft_sectionid']]['fs_title'], 24), cot_cutstring($forum_cats[$row['ft_sectionid']]['fs_category'], 16), true, array($forum_cats[$row['ft_sectionid']]['fs_masterid'], $forum_cats[$row['ft_sectionid']]['fs_mastername']));
		$build_forum_short = cot_rc_link(cot_url('forums', 'm=topics&s='.$row['ft_sectionid']), htmlspecialchars(cot_cutstring(stripslashes($forum_cats[$row['ft_sectionid']]['fs_title']), 16)));

		if ($row['ft_mode'] == 1)
		{
			$row['ft_title'] = "# ".$row['ft_title'];
		}

		if ($row['ft_movedto'] > 0)
		{
			$row['ft_url'] = cot_url('forums', 'm=posts&q='.$row['ft_movedto']);
			$row['ft_icon'] = $R['forums_icon_posts_moved'];
			$row['ft_title'] = $L['Moved'].": ".$row['ft_title'];
			$row['ft_lastpostername'] = $R['forums_code_post_empty'];
			$row['ft_postcount'] = $R['forums_code_post_empty'];
			$row['ft_replycount'] = $R['forums_code_post_empty'];
			$row['ft_viewcount'] = $R['forums_code_post_empty'];
			$row['ft_lastpostername'] = $R['forums_code_post_empty'];
			$row['ft_lastposturl'] = cot_rc_link(cot_url('forums', 'm=posts&q='.$row['ft_movedto'].'&n=last', '#bottom'), $R['icon_follow']) . ' '. $L['Moved'];
			$row['ft_timago'] = cot_build_timegap($row['ft_updated'], $sys['now_offset']);
		}
		else
		{
			$row['ft_url'] = cot_url('forums', 'm=posts&q=' . $row['ft_id']);
			$row['ft_lastposturl'] = ($usr['id'] > 0 && $row['ft_updated'] > $usr['lastvisit']) ?
                cot_rc_link(cot_url('forums', 'm=posts&q='.$row['ft_id'].'&n=unread', '#unread'), $R['icon_unread'], 'rel="nofollow"')
                : cot_rc_link(cot_url('forums', 'm=posts&q='.$row['ft_id'].'&n=last', '#bottom'), $R['icon_follow'], 'rel="nofollow"');
			$row['ft_lastposturl'] .= @date($cfg['formatmonthdayhourmin'], $row['ft_updated'] + $usr['timezone'] * 3600);
			$row['ft_timago'] = cot_build_timegap($row['ft_updated'], $sys['now_offset']);
			$row['ft_replycount'] = $row['ft_postcount'] - 1;

			if ($row['ft_updated'] > $usr['lastvisit'] && $usr['id'] > 0)
			{
				$row['ft_icon'] .= '_new';
				$row['ft_postisnew'] = TRUE;
			}

			if ($row['ft_postcount']>=$cfg['forums']['hottopictrigger'] && !$row['ft_state'] && !$row['ft_sticky'])
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

			$row['ft_icon'] = cot_rc('forums_icon_topic_t', array('icon' => $row['ft_icon'], 'title' => $L['recentitems_'.$row['ft_icon']]));
			$row['ft_lastpostername'] = cot_build_user($row['ft_lastposterid'], htmlspecialchars($row['ft_lastpostername']));
		}

		$row['ft_firstpostername'] = cot_build_user($row['ft_firstposterid'], htmlspecialchars($row['ft_firstpostername']));

		if ($row['ft_postcount'] > $cfg['forums']['maxtopicsperpage'])
		{
			$row['ft_maxpages'] = ceil($row['ft_postcount'] / $cfg['forums']['maxtopicsperpage']);
			$row['ft_pages'] = $L['Pages'].":";
		}

		$recentitems->assign(array(
			"FORUM_ROW_ID" => $row['ft_id'],
			"FORUM_ROW_STATE" => $row['ft_state'],
			"FORUM_ROW_ICON" => $row['ft_icon'],
			"FORUM_ROW_TITLE" => htmlspecialchars($row['ft_title']),
			"FORUM_ROW_PATH" => $build_forum,
			"FORUM_ROW_PATH_FULL" => $build_forum_full,
			"FORUM_ROW_PATH_SHORT" => $build_forum_short,
			"FORUM_ROW_DESC" => htmlspecialchars($row['ft_desc']),
			"FORUM_ROW_PREVIEW" => $row['ft_preview'].'...',
			"FORUM_ROW_CREATIONDATE" => @date($cfg['formatmonthdayhourmin'], $row['ft_creationdate'] + $usr['timezone'] * 3600),
			"FORUM_ROW_UPDATED" => $row['ft_lastposturl'],
			"FORUM_ROW_TIMEAGO" => $row['ft_timago'],
			"FORUM_ROW_POSTCOUNT" => $row['ft_postcount'],
			"FORUM_ROW_REPLYCOUNT" => $row['ft_replycount'],
			"FORUM_ROW_VIEWCOUNT" => $row['ft_viewcount'],
			"FORUM_ROW_FIRSTPOSTER" => $row['ft_firstpostername'],
			"FORUM_ROW_LASTPOSTER" => $row['ft_lastpostername'],
			"FORUM_ROW_URL" => $row['ft_url'],
			"FORUM_ROW_PAGES" => $row['ft_pages'],
			"FORUM_ROW_MAXPAGES" => $row['ft_maxpages'],
			"FORUM_ROW_NUM" => $ft_num,
			"FORUM_ROW_ODDEVEN" => cot_build_oddeven($ft_num),
			"FORUM_ROW" => $row
		));
		$recentitems->parse("MAIN.TOPICS_ROW");
	}

	if ($d == 0 && $ft_num == 0)
	{
		$recentitems->parse("MAIN.NO_TOPICS_FOUND");
	}

	$recentitems->parse("MAIN");
	if ($d == 0 || $ft_num > 0)
	{
		$res = $recentitems->text("MAIN");
	}
	else
	{
		$res = '';
	}
	return $res;
}

function cot_build_recentpages($template, $mode = 'recent', $maxperpage = 5, $d = 0, $titlelength = 0, $textlength = 0, $rightprescan = true, $cat = '')
{
	global $db, $cot_cat, $db_pages, $db_users, $sys, $cfg, $L, $pag, $cot_extrafields, $usr;
	$recentitems = new XTemplate(cot_skinfile($template, true));

	if ($rightprescan || $cat)
	{
		// творим чудеса - читаем список разделов и к каким из них юзер имеет доступ
		unset($sedsub);
		$catsub = array();
		if (!empty($cat))
		{
			$mtch = $cot_cat[$cat]['path'].".";
			$mtchlen = mb_strlen($mtch);
			$catsub[] = $cat;
		}
		foreach ($cot_cat as $i => $x)
		{
			if (cot_auth('page', $i, 'R') && (mb_substr($x['path'], 0, $mtchlen) == $mtch || empty($cat)))
			{
				$catsub[] = $i;
			}
		}
		$incat = "AND page_cat IN ('".implode("','", $catsub)."')";
	}

	if ($mode == 'recent')
	{
		$where = "WHERE page_state=0 AND page_cat <> 'system' ".$incat;
		$totalrecent['pages'] = $cfg['plugin']['recentitems']['maxpages'];
	}
	else
	{
		//and ft_lastposterid!=".$usr['id']."
		$where = "WHERE page_date >= $mode AND page_date <= ".(int)$sys['now_offset']." AND page_state=0 AND page_cat <> 'system' ".$incat;
		$sql = $db->query("SELECT COUNT(*) FROM $db_pages ".$where);
		$totalrecent['pages'] = $maxperpage;
	}

	$sql = $db->query("SELECT p.*, u.* FROM $db_pages AS p
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid ".$where." ORDER by page_date desc LIMIT $d, ".$maxperpage);

	$jj = 0;

	/* === Hook - Part1 === */
	$extp = cot_getextplugins('recentitems.recentpages.tags');
	/* ===== */
	while ($pag = $sql->fetch())
	{
		$jj++;
		$catpath = cot_build_catpath($pag['page_cat']);
		if ((int)$titlelength > 0)
		{
			$pag['page_title'] = (cot_string_truncate($pag['page_title'], $titlelength, false))."...";
		}
		$recentitems->assign(cot_generate_pagetags($pag, 'PAGE_ROW_', $textlength));
		$recentitems->assign(array(
			"PAGE_ROW_SHORTTITLE" => htmlspecialchars($pag['page_title']),
			"PAGE_ROW_OWNER" => cot_build_user($pag['page_ownerid'], htmlspecialchars($pag['user_name'])),
			"PAGE_ROW_ODDEVEN" => cot_build_oddeven($jj),
			"PAGE_ROW_NUM" => $jj
		));
		$recentitems->assign(cot_generate_usertags($pag, "PAGE_ROW_OWNER_"));

		/* === Hook - Part2 === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$recentitems->parse("MAIN.PAGE_ROW");
	}

	if ($d == 0 && $jj == 0)
	{
		$recentitems->parse("MAIN.NO_PAGES_FOUND");
	}

	$recentitems->parse("MAIN");
	if ($d == 0 || $jj > 0)
	{
		$res = $recentitems->text("MAIN");
	}
	else
	{
		$res='';
	}
	return $res;
}

?>