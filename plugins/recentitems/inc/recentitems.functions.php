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
 * @param ?int $timeBack Unix timestamp from which publications should be displayed
 * @param int $maxEntriesPerPage
 * @param int $d
 * @param int $titleLength
 * @param bool $rightprescan Consider user rights
 * @return string
 */
function cot_build_recentforums(
    $template,
    $timeBack = null,
    $maxEntriesPerPage = 5,
    $d = 0,
    $titleLength = 0,
    $rightprescan = true
) {
	global $totalrecent;

    $where = [];
    $params = [];

    $recentitems = new XTemplate(cot_tplfile($template, 'plug'));

    if ($rightprescan) {
        $authCategories = cot_authCategories('forums');
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
    $where['privateTopic'] = cot_forums_sqlExcludePrivateTopics('t');
    if ($where['privateTopic'] === '') {
        unset($where['privateTopic']);
    }

    if ($timeBack) {
        $timeBack = (int) $timeBack;
    }
    if ($timeBack) {
        $where['period'] = "ft_updated >= {$timeBack}";
    } else {
        $where['movedTo'] = 'ft_movedto = 0';
    }

    $joinColumns = [];
    $joinTables = [];

    /* === Hook === */
	foreach (cot_getextplugins('recentitems.recentforums.query') as $pl) {
		include $pl;
	}
	/* ===== */

    $sqlJoinColumns = '';
    if (!empty($joinColumns)) {
        $sqlJoinColumns = ', ' . implode(', ', $joinColumns);
    }

    $sqlJoinTables = '';
    if (!empty($joinTables)) {
        $sqlJoinTables = "\n " . implode("\n ", $joinTables) . "\n ";
    }

    $sqlWhere = '';
    if (!empty($where)) {
        $sqlWhere = 'WHERE (' . implode(') AND (', $where) . ')';
    }

    $totalrecent['topics'] = Cot::$db->query(
        'SELECT COUNT(*) FROM ' . Cot::$db->forum_topics . " AS t  $sqlJoinTables $sqlWhere",
        $params
    )->fetchColumn();

    if (empty($where['period']) && empty($maxEntriesPerPage)) {
        $maxEntriesPerPage = 5;
    }

    $query = "SELECT * $sqlJoinColumns FROM " . Cot::$db->forum_topics . ' AS t '
        . " $sqlJoinTables $sqlWhere ORDER by ft_updated DESC LIMIT $d, $maxEntriesPerPage";

    $recentTopics = Cot::$db->query($query, $params)->fetchAll();

    if (empty($recentTopics)) {
        if ($d === 0) {
            $recentitems->parse('MAIN.NO_TOPICS_FOUND');
            return $recentitems->text('MAIN');
        }
        return '';
    }

    $usersIds = [];
    foreach ($recentTopics as $row) {
        if (!empty($row['ft_firstposterid']) && !in_array($row['ft_firstposterid'], $usersIds)) {
            $usersIds[] = $row['ft_firstposterid'];
        }
        if (!empty($row['ft_lastposterid']) && !in_array($row['ft_lastposterid'], $usersIds)) {
            $usersIds[] = $row['ft_lastposterid'];
        }
    }

    $users = [];
    if (!empty($usersIds)) {
        $sql = Cot::$db->query(
            'SELECT * FROM ' . Cot::$db->quoteTableName(Cot::$db->users)
            . ' WHERE user_id IN (' . implode(',', $usersIds) . ')'
        );
        $result = $sql->fetchAll();
        if (!empty($result)) {
            foreach ($result as $row) {
                $users[$row['user_id']] = $row;
            }
        }
    }

	$ft_num = 0;
    foreach ($recentTopics as $row) {
		$row['ft_icon'] = 'posts';
		$row['ft_postisnew'] = false;
		$row['ft_pages'] = '';
		$ft_num++;
		if ((int) $titleLength > 0 && mb_strlen($row['ft_title']) > $titleLength) {
			$row['ft_title'] = cot_string_truncate($row['ft_title'], $titleLength, false). "...";
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

			$row['ft_icon'] = cot_rc('forums_icon_topic_t', [
                'icon' => $row['ft_icon'],
                'title' => Cot::$L['recentitems_' . $row['ft_icon']]
            ]);
			$row['ft_lastpostername'] = cot_build_user($row['ft_lastposterid'], $row['ft_lastpostername']);
		}

        $row['ft_icon_type'] = $row['ft_icon'];
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

        $recentitems->assign([
            'FORUM_ROW_ID' => $row['ft_id'],
            'FORUM_ROW_STATE' => $row['ft_state'],
            'FORUM_ROW_ICON' => $row['ft_icon'],
            'FORUM_ROW_ICON_TYPE' => $row['ft_icon_type'],
            'FORUM_ROW_TITLE' => htmlspecialchars($row['ft_title']),
            'FORUM_ROW_PATH' => $build_forum,
            'FORUM_ROW_PATH_SHORT' => $build_forum_short,
            'FORUM_ROW_DESCRIPTION' => htmlspecialchars($row['ft_desc']),
            'FORUM_ROW_PREVIEW' => $topicPreview,
            'FORUM_ROW_CREATED' => cot_date('datetime_short', $row['ft_creationdate']),
            'FORUM_ROW_CREATED_STAMP' => $row['ft_creationdate'],
            'FORUM_ROW_UPDATED' => $row['ft_lastpostlink'],
            'FORUM_ROW_UPDATED_STAMP' => $row['ft_updated'],
            'FORUM_ROW_TIME_AGO' => $row['ft_timeago'],
            'FORUM_ROW_POSTS_COUNT' => $row['ft_postcount'],
            'FORUM_ROW_REPLY_COUNT' => $row['ft_replycount'],
            'FORUM_ROW_VIEWS_COUNT' => $row['ft_viewcount'],
            'FORUM_ROW_FIRST_POSTER' => $row['ft_firstpostername'],
            'FORUM_ROW_LAST_POSTER' => $row['ft_lastpostername'],
            'FORUM_ROW_LAST_POST_URL' => $row['ft_lastposturl'],
            'FORUM_ROW_URL' => $row['ft_url'],
            'FORUM_ROW_PAGES' => $row['ft_pages'],
            'FORUM_ROW_MAX_PAGES' => $row['ft_maxpages'],
            'FORUM_ROW_NUM' => $ft_num,
            'FORUM_ROW_ODDEVEN' => cot_build_oddeven($ft_num),
            'FORUM_ROW' => $row,

            // @deprecated in 0.9.24
            'FORUM_ROW_FIRSTPOSTER' => $row['ft_firstpostername'],
            'FORUM_ROW_LASTPOSTER' => $row['ft_lastpostername'],
            'FORUM_ROW_CREATIONDATE' => cot_date('datetime_short', $row['ft_creationdate']),
            'FORUM_ROW_CREATIONDATE_STAMP' => $row['ft_creationdate'],
            'FORUM_ROW_TIMEAGO' => $row['ft_timeago'],
            'FORUM_ROW_POSTCOUNT' => $row['ft_postcount'],
            'FORUM_ROW_REPLYCOUNT' => $row['ft_replycount'],
            'FORUM_ROW_VIEWCOUNT' => $row['ft_viewcount'],
            'FORUM_ROW_LASTPOSTURL' => $row['ft_lastposturl'],
            'FORUM_ROW_MAXPAGES' => $row['ft_maxpages'],
            'FORUM_ROW_DESC' => htmlspecialchars($row['ft_desc']),
        ]);

        $userData = (!empty($row['ft_firstposterid']) && isset($users[$row['ft_firstposterid']]))
            ? cot_generate_usertags($users[$row['ft_firstposterid']], 'FORUM_ROW_FIRST_POSTER_')
            : cot_generate_usertags([], 'FORUM_ROW_FIRST_POSTER_');
        $recentitems->assign($userData);

        $userData = (
            !empty($row['ft_lastposterid'])
            && isset($users[$row['ft_lastposterid']])
            && (int) $row['ft_movedto'] === 0
        )
            ? cot_generate_usertags($users[$row['ft_lastposterid']], 'FORUM_ROW_LAST_POSTER_')
            : cot_generate_usertags([], 'FORUM_ROW_LAST_POSTER_');
        $recentitems->assign($userData);

		$recentitems->parse('MAIN.TOPICS_ROW');
	}

	$recentitems->parse('MAIN');

	return $recentitems->text('MAIN');
}

/**
 * @param string $template
 * @param ?int $timeBack Unix timestamp from which publications should be displayed
 * @param int $maxEntriesPerPage
 * @param int $d
 * @param int $titleLength
 * @param int $textLength
 * @param bool $rightprescan
 * @param string $cat
 * @return string
 */
function cot_build_recentpages(
    $template,
    $timeBack = null,
    $maxEntriesPerPage = 5,
    $d = 0,
    $titleLength = 0,
    $textLength = 0,
    $rightprescan = true,
    $cat = ''
) {
    global $totalrecent;

	$recentItems = new XTemplate(cot_tplfile($template, 'plug'));

    $where = [];
    $params = [];

    $where['state'] = 'page_state = ' . COT_PAGE_STATE_PUBLISHED;
    $where['begin'] = 'page_begin <= ' . Cot::$sys['now'];
    $where['notExpire'] = '(page_expire = 0 OR page_expire > ' . Cot::$sys['now'] . ')';

    if (!empty(Cot::$structure['page']['system'])) {
        $systemCats = cot_structure_children('page', 'system');
        if (!empty($systemCats)) {
            $where['notSystem'] = "page_cat NOT IN ('" . implode("','", $systemCats) . "')";
        }
    }

	// Load all cats and subcats in white list if set
	if (!empty(Cot::$cfg['plugin']['recentitems']['whitelist'])) {
		$whitelist = [];
		foreach (preg_split('#\r?\n#', Cot::$cfg['plugin']['recentitems']['whitelist']) as $c) {
			$whitelist = array_merge(
                $whitelist,
                cot_structure_children('page', $c, true, true, $rightprescan)
            );
		}
	} else {
		$whitelist = false;
	}

	// Load all cats and subcats in black list if set
	if (!empty(Cot::$cfg['plugin']['recentitems']['blacklist'])) {
		$blacklist = [];
		foreach (preg_split('#\r?\n#', Cot::$cfg['plugin']['recentitems']['blacklist']) as $c) {
			$blacklist = array_merge(
                $blacklist,
                cot_structure_children('page', $c, true, true, $rightprescan)
            );
		}
	} else {
		$blacklist = false;
	}

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
            $where['category'] = "p.page_cat IN ('" . implode("','", $catsub) . "')";
        }
	} elseif (!empty($whitelist)) {
		// Only cats from white list
        $where['category'] = "p.page_cat IN ('" . implode("','", $whitelist) . "')";

	} elseif (!empty($blacklist)) {
		// All cats but not in black list
        $where['category'] = "p.page_cat NOT IN ('" . implode("','", $blacklist) . "')";
	}

    $order = !empty(Cot::$cfg['plugin']['recentitems']['pagesOrder'])
        && in_array(Cot::$cfg['plugin']['recentitems']['pagesOrder'], ['date', 'begin', 'updated'])
        ? Cot::$cfg['plugin']['recentitems']['pagesOrder']
        : 'date';

    if ($timeBack) {
        $timeBack = (int) $timeBack;
    }
    if ($timeBack) {
        $where['period'] = "p.page_{$order} >= {$timeBack}";
    }

	$joinColumns = [];
	$joinTables = [];

	/* === Hook === */
	foreach (cot_getextplugins('recentitems.recentpages.query') as $pl) {
		include $pl;
	}
	/* ===== */

    $sqlJoinColumns = '';
    if (!empty($joinColumns)) {
        $sqlJoinColumns = ', ' . implode(', ', $joinColumns);
    }

    $sqlJoinTables = '';
    if (!empty($joinTables)) {
        $sqlJoinTables = "\n " . implode("\n ", $joinTables) . "\n ";
    }

    $sqlWhere = '';
    if (!empty($where)) {
        $sqlWhere = 'WHERE (' . implode(') AND (', $where) . ')';
    }

    $totalrecent['pages'] = Cot::$db->query(
        'SELECT COUNT(*) FROM ' . Cot::$db->pages . " AS p $sqlJoinTables $sqlWhere",
        $params
    )->fetchColumn();

    if (empty($where['period']) && empty($maxEntriesPerPage)) {
        $maxEntriesPerPage = 5;
    }

    $query = "SELECT p.*, u.* $sqlJoinColumns FROM " . Cot::$db->pages . ' AS p '
        . ' LEFT JOIN ' . Cot::$db->users . ' AS u ON u.user_id = p.page_ownerid '
        . " $sqlJoinTables $sqlWhere ORDER by p.page_{$order} desc LIMIT $d, $maxEntriesPerPage";

	$sql = Cot::$db->query($query, $params);

    $titleLength = (int) $titleLength;

	/* === Hook - Part1 === */
	$extp = cot_getextplugins('recentitems.recentpages.tags');
	/* ===== */
    $jj = 0;
    while ($pag = $sql->fetch()) {
		$jj++;
		if ($titleLength > 0 && mb_strlen($pag['page_title']) > $titleLength) {
			$pag['page_title'] = (cot_string_truncate($pag['page_title'], $titleLength, false)) . "...";
		}
		$recentItems->assign(cot_generate_pagetags($pag, 'PAGE_ROW_', $textLength));
		$recentItems->assign([
			'PAGE_ROW_TITLE' => htmlspecialchars($pag['page_title']),
			'PAGE_ROW_OWNER' => cot_build_user($pag['page_ownerid'], $pag['user_name']),
			'PAGE_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'PAGE_ROW_NUM' => $jj,

            // @deprecated in 0.9.24
            'PAGE_ROW_SHORTTITLE' => htmlspecialchars($pag['page_title']),
            // /@deprecated
		]);
		$recentItems->assign(cot_generate_usertags($pag, 'PAGE_ROW_OWNER_'));

		/* === Hook - Part2 === */
		foreach ($extp as $pl) {
			include $pl;
		}
		/* ===== */

		$recentItems->parse('MAIN.PAGE_ROW');
	}
    $sql->closeCursor();

	if ($d == 0 && $jj == 0) {
		$recentItems->parse('MAIN.NO_PAGES_FOUND');
	}

	$recentItems->parse('MAIN');

	return ($d == 0 || $jj > 0) ? $recentItems->text('MAIN') : '';
}
