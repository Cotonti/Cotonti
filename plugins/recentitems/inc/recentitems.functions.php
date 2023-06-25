<?php

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package RecentItems
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die("Wrong URL.");

require_once cot_incfile('extrafields');
require_once cot_langfile('recentitems', 'plug');

/**
 * @param string $template
 * @param string $mode
 * @param int $maxperpage
 * @param int $d
 * @param int $titlelength
 * @param bool $rightprescan Consider user rights
 * @return string
 */
function cot_build_recentforums(
    $template,
    $mode = 'recent',
    $maxperpage = 5,
    $d = 0,
    $titlelength = 0,
    $rightprescan = true
) {
	global $totalrecent;

    $where = [];

    $authCategories = cot_authCategories('forums');

    $recentitems = new XTemplate(cot_tplfile($template, 'plug'));

    if ($rightprescan) {
        if (empty($authCategories['read'])) {
            $recentitems->parse('MAIN.NO_TOPICS_FOUND');
            $recentitems->parse('MAIN');
            return $recentitems->text('MAIN');
        }
        if (!$authCategories['readAll']) {
            $sqlCategories = array_map(
                function ($value) {return Cot::$db->quote($value);},
                $authCategories['read']
            );
            $where['cat'] = 'ft_cat IN (' . implode(', ', $sqlCategories) . ')';
        }
    }

    // Exclude private topics
    $where['privateTopic'] = cot_forums_sqlExcludePrivateTopics();
    if ($where['privateTopic'] === '') {
        unset($where['privateTopic']);
    }

    /* === Hook === */
	foreach (cot_getextplugins('recentitems.recentforums.first') as $pl) {
		include $pl;
	}
	/* ===== */

    $where = array_diff($where, ['']);

	if ($mode == 'recent') {
        $where['movedTo'] = 'ft_movedto = 0';
        $sqlWhere = ' WHERE ' . implode(' AND ', $where);

        $totalrecent['topics'] = Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->forum_topics . $sqlWhere)
            ->fetchColumn();
        $sql = Cot::$db->query('SELECT * FROM ' . Cot::$db->forum_topics . $sqlWhere .
            " ORDER by ft_updated DESC LIMIT $maxperpage");
		//$totalrecent['topics'] = $maxperpage;

	} else {
        $where['updated'] = 'ft_updated >= :updated';
		$sqlWhere = ' WHERE ' . implode(' AND ', $where);

		$totalrecent['topics'] = Cot::$db->query(
            'SELECT COUNT(*) FROM ' . Cot::$db->forum_topics . $sqlWhere,
            ['updated' => $mode]
        )->fetchColumn();
		$sql = Cot::$db->query(
            'SELECT * FROM ' . Cot::$db->forum_topics . ' ' . $sqlWhere . " ORDER by ft_updated DESC LIMIT $d, " .
                $maxperpage,
            ['updated' => $mode]
        );
	}

	$ft_num = 0;
	while ($row = $sql->fetch()) {
		$row['ft_icon'] = 'posts';
		$row['ft_postisnew'] = false;
		$row['ft_pages'] = '';
		$ft_num++;
		if ((int) $titlelength > 0 && mb_strlen($row['ft_title']) > $titlelength) {
			$row['ft_title'] = cot_string_truncate($row['ft_title'], $titlelength, false). "...";
		}
		$build_forum = cot_breadcrumbs(cot_forums_buildpath($row['ft_cat'], false), false, false);
		$build_forum_short = cot_rc_link(cot_url('forums', 'm=topics&s=' . $row['ft_cat']),
            htmlspecialchars(Cot::$structure['forums'][$row['ft_cat']]['title']));

		if ($row['ft_mode'] == 1) {
			$row['ft_title'] = "# " . $row['ft_title'];
		}

		if ($row['ft_movedto'] > 0) {
			$row['ft_url'] = cot_url('forums', 'm=posts&q=' . $row['ft_movedto']);
			$row['ft_icon'] = Cot::$R['forums_icon_posts_moved'];
			$row['ft_title'] = Cot::$L['Moved'] . ": " . $row['ft_title'];
			$row['ft_lastpostername'] = Cot::$R['forums_code_post_empty'];
			$row['ft_postcount'] = Cot::$R['forums_code_post_empty'];
			$row['ft_replycount'] = Cot::$R['forums_code_post_empty'];
			$row['ft_viewcount'] = Cot::$R['forums_code_post_empty'];
			$row['ft_lastpostername'] = Cot::$R['forums_code_post_empty'];
			$row['ft_lastposturl'] = cot_url('forums', 'm=posts&q=' . $row['ft_movedto'] . '&n=last', '#bottom');
			$row['ft_lastpostlink'] = cot_rc_link($row['ft_lastposturl'], Cot::$R['icon_follow']) . ' ' . Cot::$L['Moved'];
			$row['ft_timeago'] = cot_build_timegap($row['ft_updated'], Cot::$sys['now']);
		} else {
			$row['ft_url'] = cot_url('forums', 'm=posts&q=' . $row['ft_id']);
			$row['ft_lastposturl'] = (Cot::$usr['id'] > 0 && $row['ft_updated'] > Cot::$usr['lastvisit']) ?
				cot_url('forums', 'm=posts&q=' . $row['ft_id'] . '&n=unread', '#unread') :
				cot_url('forums', 'm=posts&q=' . $row['ft_id'] . '&n=last', '#bottom');
			$row['ft_lastpostlink'] = (Cot::$usr['id'] > 0 && $row['ft_updated'] > Cot::$usr['lastvisit']) ?
				cot_rc_link($row['ft_lastposturl'], Cot::$R['icon_unread'], 'rel="nofollow"') :
				cot_rc_link($row['ft_lastposturl'], Cot::$R['icon_follow'], 'rel="nofollow"');
			$row['ft_lastpostlink'] .= cot_date('datetime_medium', $row['ft_updated']);
			$row['ft_timeago'] = cot_build_timegap($row['ft_updated'], Cot::$sys['now']);
			$row['ft_replycount'] = $row['ft_postcount'] - 1;

			if ($row['ft_updated'] > Cot::$usr['lastvisit'] && Cot::$usr['id'] > 0) {
				$row['ft_icon'] .= '_new';
				$row['ft_postisnew'] = true;
			}

			if ($row['ft_postcount'] >= Cot::$cfg['forums']['hottopictrigger'] && !$row['ft_state'] && !$row['ft_sticky']) {
				$row['ft_icon'] = ($row['ft_postisnew']) ? 'posts_new_hot' : 'posts_hot';
			} else {
				if ($row['ft_sticky']) {
					$row['ft_icon'] .= '_sticky';
				}

				if ($row['ft_state']) {
					$row['ft_icon'] .= '_locked';
				}
			}

			$row['ft_icon_type'] = $row['ft_icon'];
			$row['ft_icon'] = cot_rc('forums_icon_topic_t', [
                'icon' => $row['ft_icon'],
                'title' => Cot::$L['recentitems_' . $row['ft_icon']]
            ]);
			$row['ft_lastpostername'] = cot_build_user($row['ft_lastposterid'], $row['ft_lastpostername']);
		}

		$row['ft_firstpostername'] = cot_build_user($row['ft_firstposterid'], $row['ft_firstpostername']);

        $row['ft_maxpages'] = 0;
		if ($row['ft_postcount'] > Cot::$cfg['forums']['maxtopicsperpage'] && Cot::$cfg['forums']['maxtopicsperpage'] > 0) {
			$row['ft_maxpages'] = ceil($row['ft_postcount'] / Cot::$cfg['forums']['maxtopicsperpage']);
			$row['ft_pages'] = Cot::$L['Pages'] . ":";
		}

        $topicPreview = '';
        if (!empty($row['ft_preview'])) {
            $allowBBCodes = isset(Cot::$cfg['forums']['cat_' . $row['ft_cat']]) ?
                Cot::$cfg['forums']['cat_' . $row['ft_cat']]['allowbbcodes'] :
                Cot::$cfg['forums']['cat___default']['allowbbcodes'];
            $topicPreview = trim(cot_parse($row['ft_preview'], $allowBBCodes));
            if (!empty($topicPreview)) {
                $topicPreview .= '...';
            }
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
			'FORUM_ROW_PREVIEW' => $topicPreview,
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

	if ($d == 0 && $ft_num == 0) {
		$recentitems->parse('MAIN.NO_TOPICS_FOUND');
	}

	$recentitems->parse('MAIN');

	return ($d == 0 || $ft_num > 0) ? $recentitems->text('MAIN') : '';
}

/**
 * @param string $template
 * @param string|int $mode 'recent' or unix timestamp from which publications should be displayed
 * @param int $maxperpage
 * @param int $d
 * @param int $titlelength
 * @param int $textlength
 * @param bool $rightprescan
 * @param string $cat
 * @return string
 */
function cot_build_recentpages($template, $mode = 'recent', $maxperpage = 5, $d = 0, $titlelength = 0, $textlength = 0, $rightprescan = true, $cat = '')
{
	global $db_pages, $db_users;

	$recentitems = new XTemplate(cot_tplfile($template, 'plug'));

	// Load all cats and subcats in white list if set
	if (!empty(Cot::$cfg['plugin']['recentitems']['whitelist'])) {
		$whitelist = array();
		foreach (preg_split('#\r?\n#', Cot::$cfg['plugin']['recentitems']['whitelist']) as $c) {
			$whitelist = array_merge($whitelist, cot_structure_children('page', $c, true, true, $rightprescan));
		}
	} else {
		$whitelist = false;
	}

	// Load all cats and subcats in black list if set
	if (!empty(Cot::$cfg['plugin']['recentitems']['blacklist'])) {
		$blacklist = array();
		foreach (preg_split('#\r?\n#', Cot::$cfg['plugin']['recentitems']['blacklist']) as $c) {
			$blacklist = array_merge($blacklist, cot_structure_children('page', $c, true, true, $rightprescan));
		}
	} else {
		$blacklist = false;
	}

    $incat = '';
	if ($rightprescan || $cat) {
		// Get selected cats
		$catsub = cot_structure_children('page', $cat, true, true, $rightprescan);

		if ($whitelist) {
			// Must be both in selected and whitelist
			$catsub = array_intersect($catsub, $whitelist);

        } elseif ($blacklist) {
			// Must be in selected but not in blacklist
			$catsub = array_diff($catsub, $blacklist);
		}

        if (!empty($catsub)) {
            $incat = "AND page_cat IN ('" . implode("','", $catsub) . "')";
        }
	} elseif (!empty($whitelist)) {
		// Only cats from white list
		$incat = "AND page_cat IN ('" . implode("','", $whitelist) . "')";

	} elseif (!empty($blacklist)) {
		// All cats but not in black list
		$incat = "AND page_cat NOT IN ('" . implode("','", $blacklist) . "')";
	}

	if ($mode == 'recent') {
		$where = "WHERE page_state=0 AND page_begin <= " . Cot::$sys['now'] .
            " AND (page_expire = 0 OR page_expire > " . Cot::$sys['now']. ") AND page_cat <> 'system' " . $incat;
		$totalrecent['pages'] = Cot::$cfg['plugin']['recentitems']['maxpages'];

	} else {
		$where = "WHERE page_date >= $mode AND page_begin <= " . Cot::$sys['now'] .
            " AND (page_expire = 0 OR page_expire > " .
            Cot::$sys['now'] . ") AND page_state=0 AND page_cat <> 'system' " . $incat;
		$totalrecent['pages'] = Cot::$db->query("SELECT COUNT(*) FROM $db_pages " . $where)->fetchColumn();
	}

	$join_columns = '';
	$join_tables = '';

	/* === Hook === */
	foreach (cot_getextplugins('recentitems.recentpages.first') as $pl) {
		include $pl;
	}
	/* ===== */

	$sql = Cot::$db->query("SELECT p.*, u.* $join_columns
		FROM $db_pages AS p
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		$join_tables
		$where ORDER by page_date desc LIMIT $d, $maxperpage");

	$jj = 0;

	/* === Hook - Part1 === */
	$extp = cot_getextplugins('recentitems.recentpages.tags');
	/* ===== */
	foreach ($sql->fetchAll() as $pag) {
		$jj++;
		if ((int) $titlelength > 0 && mb_strlen($pag['page_title']) > $titlelength) {
			$pag['page_title'] = (cot_string_truncate($pag['page_title'], $titlelength, false)) . "...";
		}
		$recentitems->assign(cot_generate_pagetags($pag, 'PAGE_ROW_', $textlength));
		$recentitems->assign(array(
			'PAGE_ROW_SHORTTITLE' => htmlspecialchars($pag['page_title']),
			'PAGE_ROW_OWNER' => cot_build_user($pag['page_ownerid'], $pag['user_name']),
			'PAGE_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'PAGE_ROW_NUM' => $jj
		));
		$recentitems->assign(cot_generate_usertags($pag, 'PAGE_ROW_OWNER_'));

		/* === Hook - Part2 === */
		foreach ($extp as $pl) {
			include $pl;
		}
		/* ===== */

		$recentitems->parse('MAIN.PAGE_ROW');
	}

	if ($d == 0 && $jj == 0) {
		$recentitems->parse('MAIN.NO_PAGES_FOUND');
	}

	$recentitems->parse('MAIN');

	return ($d == 0 || $jj > 0) ? $recentitems->text('MAIN') : '';
}
