<?php

/**
 * Forums API
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL.');

// Requirements
require_once cot_langfile('forums', 'module');
require_once cot_incfile('forums', 'module', 'resources');
require_once cot_incfile('extrafields');

// Registering tables and fields
Cot::$db->registerTable('forum_posts');
Cot::$db->registerTable('forum_topics');
Cot::$db->registerTable('forum_stats');

cot_extrafields_register_table('forum_posts');
cot_extrafields_register_table('forum_topics');

/*
 * Topic modes
 * 0 - Normal. Available for all
 * 1 - Private. Only moderators and the starter of the topic can read and reply
 */
const COT_FORUMS_TOPIC_MODE_NORMAL = 0;
const COT_FORUMS_TOPIC_MODE_PRIVATE = 1;

// @todo constants for topic states
// LOCKED: ft_state == 1,  ft_sticky == 0
// STICKY: ft_state == 0,  ft_sticky == 1
// ANNOUNCEMENT: ft_state == 1,  ft_sticky == 1

/**
 * Builds forum category path
 *
 * @param string $cat Category code
 * @param bool $forumslink Include forums main link
 * @return array
 * @see cot_breadcrumbs()
 */
function cot_forums_buildpath($cat, $forumslink = true)
{
	$tmp = [];
	if ($forumslink) {
		$tmp[] = array(cot_url('forums'), Cot::$L['Forums']);
	}
	$pathcodes = !empty(Cot::$structure['forums'][$cat]['path']) ?
        explode('.', Cot::$structure['forums'][$cat]['path']) : [];

	foreach ($pathcodes as $k => $x) {
		if ($k == 0) {
			$tmp[] = [cot_url('forums', 'c=' . $x, '#' . $x), Cot::$structure['forums'][$x]['title']];

		} else {
			$tmp[] = [cot_url('forums', 'm=topics&s=' . $x), Cot::$structure['forums'][$x]['title']];
		}
	}

	return $tmp;
}

/**
 * Deletes (outdated) topics
 *
 * @param string $mode Selection criteria
 * @param string $section Section
 * @param int $param Selection parameter value
 * @return int
 * @global CotDB $db
 *
 * @todo To delete single topic we don't need section. Just TopicID
 */
function cot_forums_prunetopics($mode, $section, $param)
{
	global $cfg, $L, $Ls, $R; // For hooks include

    $topicsDeleted = 0;
	if (!is_int($param)) {
		$param = (int) $param;
	}

	switch ($mode) {
		case 'updated':
			$limit = Cot::$sys['now'] - ($param * 86400);
			$sql1 = Cot::$db->query(
                'SELECT * FROM ' . Cot::$db->forum_topics .
                    " WHERE ft_cat = :cat AND ft_updated < $limit AND ft_sticky = 0",
                ['cat' => $section]
            );
			break;

		case 'single':
			$sql1 = Cot::$db->query(
                'SELECT * FROM ' . Cot::$db->forum_topics . ' WHERE ft_cat = :cat  AND ft_id = :topicId',
                ['cat' => $section, 'topicId' => $param]
            );
			break;
	}

    if ($sql1->rowCount() < 1) {
        $sql1->closeCursor();
        return 0;
    }

    $posterIds = [];
    foreach ($sql1->fetchAll() as $topic) {
        $topicId = $topic['ft_id'];

        /** @todo For backward compatibility. Remove after 1.1.6 release  */
        $q = $topic['ft_id'];

        /* === Hook === */
        foreach (cot_getextplugins('forums.functions.prunetopics') as $pl) {
            include $pl;
        }
        /* ===== */

        $posts = Cot::$db->query(
            'SELECT * FROM ' . Cot::$db->forum_posts . ' WHERE fp_topicid = ?',
            $topic['ft_id']
        )->fetchAll();
        if (!empty($posts)) {
            foreach ($posts as $post) {
                foreach(Cot::$extrafields[Cot::$db->forum_posts] as $exfld) {
                    if (isset($post['fp_' . $exfld['field_name']])) {
                        cot_extrafield_unlinkfiles($post['fp_' . $exfld['field_name']], $exfld);
                    }
                }
            }
        }

        $topicPosterIds = Cot::$db->query(
            'SELECT DISTINCT (fp_posterid) FROM ' . Cot::$db->forum_posts . ' WHERE fp_topicid = :topicId',
            ['topicId' => $topic['ft_id']]
        )->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($topicPosterIds)) {
            $posterIds = array_merge($posterIds, $topicPosterIds);
        }

        Cot::$db->delete(Cot::$db->forum_posts, 'fp_topicid = ?', $topic['ft_id']);
        Cot::$db->delete(Cot::$db->forum_topics, 'ft_movedto = ?', $topic['ft_id']);

        foreach (Cot::$extrafields[Cot::$db->forum_topics] as $exfld) {
            if (isset($topic['ft_' . $exfld['field_name']])) {
                cot_extrafield_unlinkfiles($topic['ft_' . $exfld['field_name']], $exfld);
            }
        }

        $topicsDeleted += Cot::$db->delete(Cot::$db->forum_topics, 'ft_id = ?', $topic['ft_id']);
    }

    cot_forums_updateStructureCounters($section);

    // Decrease postcount for users
    if (!empty($posterIds)) {
        foreach ($posterIds as $posterId) {
            cot_forums_updateUserPostCount($posterId);
        }
    }

	return $topicsDeleted;
}

/**
 * SQL condition to exclude private topics from the topics list
 * @return string
 */
function cot_forums_sqlExcludePrivateTopics($tableAlias = null)
{
    $authCategories = cot_authCategories('forums');

    if ($authCategories['adminAll']) {
        return '';
    }

    if ($tableAlias === null || $tableAlias === 'cot_forum_topics') {
        $tableAlias = Cot::$db->forum_topics . '.';
    } elseif ($tableAlias != '') {
        $tableAlias .= '.';
    }

    $sqlAdminCats = '';
    $sqlFirstPosterId = '';
    if (Cot::$usr['id'] > 0) {
        $sqlFirstPosterId = ' OR ' . Cot::$db->quoteC($tableAlias . 'ft_firstposterid') .' = ' . Cot::$usr['id'];
        if (!empty($authCategories['admin'])) {
            $categories = [];
            foreach ($authCategories['admin'] as $category) {
                $category = (string) $category;
                if ($category !== '') {
                    $categories[] = Cot::$db->quote($category);
                }
            }
            if (!empty($categories)) {
                $sqlAdminCats = ' OR ' . Cot::$db->quoteC($tableAlias . 'ft_cat')
                    . ' IN (' . implode(', ', $categories) . ')';
            }
        }
    }

    return '(' . Cot::$db->quoteC($tableAlias . 'ft_mode') . ' = ' . COT_FORUMS_TOPIC_MODE_NORMAL
        . $sqlFirstPosterId . $sqlAdminCats . ')';
}

/**
 * Recounts posts in a given topic
 *
 * @param int $topicId Topic ID
 * @param int|false|null $userId Poster User ID to update posts count. If 'NULL' posts counters will be updated for all
 *   posters from this topic. 'FALSE' - not update users posts counters
 */
function cot_forums_resyncTopic($topicId, $userId = null)
{
    $topicId = (int) $topicId;
    if ($topicId < 1) {
        return false;
    }

    $lastPost = [
        'posterId' => 0,
        'posterName' => '',
        'updated' => 0,
    ];
    $row = Cot::$db->query(
        'SELECT fp_posterid, fp_postername, fp_updated FROM ' . Cot::$db->forum_posts .
            ' WHERE fp_topicid=? ORDER BY fp_id DESC LIMIT 1',
        $topicId
    )->fetch();
    if ($row) {
        $lastPost = [
            'posterId' => (int) $row['fp_posterid'],
            'posterName' => $row['fp_postername'],
            'updated' => (int) $row['fp_updated'],
        ];
    }
    Cot::$db->query(
        'UPDATE ' . Cot::$db->forum_topics .
        ' SET ft_postcount = (SELECT COUNT(*) FROM ' . Cot::$db->forum_posts . ' WHERE fp_topicid = :topicId),' .
        ' ft_lastposterid = :posterId, ft_lastpostername = :posterName,  ft_updated = :updated ' .
        ' WHERE ft_id = :topicId',
        [
            'topicId' => $topicId,
            'posterId' => $lastPost['posterId'],
            'posterName' => $lastPost['posterName'],
            'updated' => $lastPost['updated'],
        ]
    );

    $topicPosterIds = null;
    if ($userId === null) {
        // Update posts count for all posters in this topic
        $topicPosterIds = Cot::$db->query(
            'SELECT DISTINCT (fp_posterid) FROM ' . Cot::$db->forum_posts . ' WHERE fp_topicid = ?',
            $topicId
        )->fetchAll(PDO::FETCH_COLUMN);
    } elseif ($userId > 0) {
        // Update posts count for this user only
        $topicPosterIds = [$userId];
    }
    if (!empty($topicPosterIds)) {
        foreach ($topicPosterIds as $posterId) {
            cot_forums_updateUserPostCount($posterId);
        }
    }

    return true;
}

/**
 * Returns all section tags for coTemplate
 *
 * @param string $cat Forums structure cat code
 * @param string $tag_prefix Prefix for tags
 * @param array $stat Category statistics
 *
 * @return array
 */
function cot_generate_sectiontags($cat, $tag_prefix = '', $stat = NULL)
{
	global $cfg, $structure, $cot_extrafields, $usr, $sys, $L, $db_structure;

    $statLtDate = !empty($stat['fs_lt_date']) ? $stat['fs_lt_date'] : 0;
    $statLtPosterId  = !empty($stat['fs_lt_posterid']) ? $stat['fs_lt_posterid'] : 0;
    $usr['lastvisit'] = !empty($usr['lastvisit']) ? $usr['lastvisit'] : 0;

	$new_elems = ($usr['id'] > 0 && $statLtDate > $usr['lastvisit'] && $statLtPosterId != $usr['id']);

	$sections = array(
		$tag_prefix . 'CAT' => $cat,
		$tag_prefix . 'LOCKED' => $structure['forums'][$cat]['locked'],
		$tag_prefix . 'TITLE' => $structure['forums'][$cat]['title'],
		$tag_prefix . 'DESC' => cot_parse_autourls($structure['forums'][$cat]['desc']).(($structure['forums'][$cat]['locked'])? ' '.$L['Locked'] : ''),
		$tag_prefix . 'ICON' => empty($structure['forums'][$cat]['icon']) ? '' : cot_rc('img_structure_cat', array(
				'icon' => $structure['forums'][$cat]['icon'],
				'title' => htmlspecialchars($structure['forums'][$cat]['title']),
				'desc' => htmlspecialchars($structure['forums'][$cat]['desc'])
			)),
		$tag_prefix . 'URL' => cot_url('forums', 'm=topics&s=' . $cat),
		$tag_prefix . 'SECTIONSURL' => cot_url('forums', 'c=' . $cat),
		$tag_prefix . 'NEWPOSTS' => $new_elems,
		$tag_prefix . 'CAT_DEFSTATE' => htmlspecialchars($cfg['forums']['cat_' . $cat]['defstate']),
	);

	if (is_array($stat)) {
		if ($stat['fs_lt_date'] > 0) {
			$sections += array(
				$tag_prefix . 'LASTPOSTDATE' => cot_date('datetime_short', $stat['fs_lt_date']),
				$tag_prefix . 'LASTPOSTER' => cot_build_user($stat['fs_lt_posterid'], $stat['fs_lt_postername']),
				$tag_prefix . 'LASTPOST' => cot_rc_link($new_elems ? cot_url('forums', 'm=posts&q=' . $stat['fs_lt_id'] . '&n=unread', '#unread') : cot_url('forums', 'm=posts&q=' . $stat['fs_lt_id'] . '&n=last', '#bottom'), cot_cutstring($stat['fs_lt_title'], 32)),
				$tag_prefix . 'LASTPOST_URL' => $new_elems ? cot_url('forums', 'm=posts&q=' . $stat['fs_lt_id'] . '&n=unread', '#unread') : cot_url('forums', 'm=posts&q=' . $stat['fs_lt_id'] . '&n=last', '#bottom'),
				$tag_prefix . 'TIMEAGO' => cot_build_timegap($stat['fs_lt_date'], $sys['now'])
			);

		}

		$sections += array(
			$tag_prefix . 'TOPICCOUNT' => $stat['topiccount'],
            $tag_prefix . 'LASTPOSTDATE_STAMP' => $stat['fs_lt_date'],
			$tag_prefix . 'POSTCOUNT' => $stat['postcount'],
			$tag_prefix . 'VIEWCOUNT' => $stat['viewcount'],
			$tag_prefix . 'VIEWCOUNT_SHORT' => ($stat['viewcount'] > 9999) ? floor($stat['viewcount'] / 1000) . 'k' : $stat['viewcount'],
		);
	}

	if (!is_array($stat) || !$stat['fs_lt_date']) {
        $sections[$tag_prefix . 'LASTPOSTDATE'] = '';
        $sections[$tag_prefix . 'LASTPOSTER'] = '';
        $sections[$tag_prefix . 'LASTPOST'] = '';
        $sections[$tag_prefix . 'TIMEAGO'] = '';
        $sections[$tag_prefix . 'TOPICCOUNT'] = 0;
        $sections[$tag_prefix . 'POSTCOUNT'] = 0;
        $sections[$tag_prefix . 'VIEWCOUNT'] = 0;
        $sections[$tag_prefix . 'VIEWCOUNT_SHORT'] = 0;
	}

	if (!empty(Cot::$extrafields[Cot::$db->structure])) {
		foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
			$uname = strtoupper($exfld['field_name']);
            $exfld_title = cot_extrafield_title($exfld, 'structure_');
            
			$sections[$tag_prefix . $uname . '_TITLE'] = $exfld_title;
			$sections[$tag_prefix . $uname] = cot_build_extrafields_data('structure', $exfld,
				$structure['forums'][$cat][$exfld['field_name']]);
			$sections[$tag_prefix . $uname . '_VALUE'] = $structure['forums'][$cat][$exfld['field_name']];
		}
	}

	return $sections;
}

/**
 * Recounts all counters for a given category
 * Used in Admin/Structure/Resync All
 * Not updates `cot_structure` because it will be updated here: system/admin/admin.structure.php
 *
 * @param string $category Category code
 * @return int Topics Count
 */
function cot_forums_sync($category)
{
    if (empty($category) || empty(Cot::$structure['forums'][$category])) {
        return 0;
    }

    $topicWhere = 'ft_cat = :cat AND ft_movedto = 0 AND ft_mode = ' . COT_FORUMS_TOPIC_MODE_NORMAL;

    $data = Cot::$db->query(
        'SELECT COUNT(*) as topics_count, SUM(ft_viewcount) as views_count, ' .
        '(' .
            'SELECT COUNT(*) FROM ' . Cot::$db->forum_posts . ' WHERE fp_topicid IN ' .
            '(SELECT ft_id FROM ' . Cot::$db->forum_topics . " WHERE  $topicWhere)" .
        ') as posts_count ' .
        ' FROM ' . Cot::$db->forum_topics . " WHERE $topicWhere",
        ['cat' => $category]
    )->fetch();

    $lastTopic = Cot::$db->query(
        'SELECT ft_id, ft_lastposterid, ft_lastpostername, ft_updated, ft_title' .
        ' FROM ' . Cot::$db->forum_topics . " WHERE $topicWhere ORDER BY ft_updated DESC LIMIT 1",
        ['cat' => $category]
    )->fetch();
    if (!$lastTopic) {
        $lastTopic = [
            'ft_id' => 0,
            'ft_lastposterid' => 0,
            'ft_lastpostername' => '',
            'ft_updated' => 0,
            'ft_title' => '',
        ];
    }

    $statData = [
        'fs_lt_id' => (int) $lastTopic['ft_id'],
        'fs_lt_title' => $lastTopic['ft_title'],
        'fs_lt_date' => (int) $lastTopic['ft_updated'],
        'fs_lt_posterid' => (int) $lastTopic['ft_lastposterid'],
        'fs_lt_postername' => $lastTopic['ft_lastpostername'],
        'fs_topiccount' => (int) $data['topics_count'],
        'fs_postcount' => (int) $data['posts_count'],
        'fs_viewcount' => (int) $data['views_count'],
    ];

    $statExists = Cot::$db->query(
        'SELECT COUNT(*) FROM ' . Cot::$db->forum_stats . ' WHERE fs_cat = :cat',
        ['cat' => $category]
    )->fetchColumn();

    if (!$statExists) {
        $insertData = $statData;
        $insertData['fs_cat'] = $category;
        try {
            Cot::$db->insert(Cot::$db->forum_stats, $insertData);
        } catch (\Exception $e) {
            // May be record was just created by another process. Let's try to update
            Cot::$db->update(Cot::$db->forum_stats, $statData, 'fs_cat = :cat', ['cat' => $category]);
        }
    } else {
        Cot::$db->update(Cot::$db->forum_stats, $statData, 'fs_cat = :cat', ['cat' => $category]);
    }
    if (!empty(Cot::$cache)) {
        Cot::$cache->db->remove('cot_sections_act', 'system');
    }

    return (int) Cot::$db->query(
        'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->forum_topics) .
        ' WHERE ft_cat = ?',
        $category
    )->fetchColumn();
}

/**
 * Recalculate and update structure counters
 *
 * @param string $category Category code
 * @return void
 */
function cot_forums_updateStructureCounters($category)
{
    if (empty($category) || empty(Cot::$structure['forums'][$category])) {
        return;
    }

    $count = cot_forums_sync($category);
    Cot::$db->query(
        'UPDATE ' . \Cot::$db->quoteTableName(\Cot::$db->structure) . ' SET structure_count = ' . $count .
        " WHERE structure_area='forums' AND structure_code = :category",
        ['category' => $category]
    );

    if (\Cot::$cache) {
        \Cot::$cache->db->remove('structure', 'system');
        if (\Cot::$cfg['cache_forums']) {
            //\Cot::$cache->page->clearByUri(cot_url('forums', ['m' => 'topics', 's' => $category]));
            \Cot::$cache->static->clearByUri(cot_url('forums'));
        }
        if (\Cot::$cfg['cache_index']) {
            \Cot::$cache->static->clear('index');
        }
    }
}

/**
 * Update user posts count
 * @param int $id User Id
 * @return void
 */
function cot_forums_updateUserPostCount($id)
{
    $id = (int) $id;
    if ($id < 1) {
        return;
    }

    $excludeCats = [];
    if (!empty(Cot::$structure['forums'])) {
        foreach (array_keys(Cot::$structure['forums'] )as $cat) {
            if (!Cot::$cfg['forums']['cat_' . $cat]['countposts']) {
                $excludeCats[] = Cot::$db->quote($cat);
            }
        }
    }

    $countPostsDisabled = '';
    if (!empty($excludeCats)) {
        $countPostsDisabled = ' OR ft_cat IN (' . implode(', ', $excludeCats) . ')';
    }

    $sql = 'UPDATE ' . Cot::$db->users . ' SET user_postcount = (' .
        'SELECT COUNT(*) FROM ' . Cot::$db->forum_posts .
        ' WHERE fp_posterid = :posterId ' .
        'AND fp_topicid NOT IN (SELECT ft_id FROM ' . Cot::$db->forum_topics .' WHERE ft_mode = ' .
            COT_FORUMS_TOPIC_MODE_PRIVATE . $countPostsDisabled . ')' .
    ') ' .
    'WHERE user_id = :posterId';

    Cot::$db->query($sql, [':posterId' => $id]);
}

/**
 * Update forums category
 *
 * @param string $oldcat Old Cat code
 * @param string $newcat New Cat code
 * @return bool
 * @global CotDB $db
 */
function cot_forums_updatecat($oldcat, $newcat)
{
	global $db, $db_forum_topics, $db_forum_posts, $db_forum_stats;

	$upd = (bool)$db->update($db_forum_topics, array('ft_cat' => $newcat), 'ft_cat=' . $db->quote($oldcat));
	$upd &= (bool)$db->update($db_forum_posts, array('fp_cat' => $newcat), 'fp_cat=' . $db->quote($oldcat));
	$upd &= (bool)$db->update($db_forum_stats, array('fs_cat' => $newcat), 'fs_cat=' . $db->quote($oldcat));

    if (!empty(Cot::$cache)) {
        Cot::$cache->db->remove('cot_sections_act', 'system');
    }

	return $upd;
}

/**
 * Delete forums category
 *
 * @param string $cat Category code
 */
function cot_forums_deletecat($cat)
{
	global $db_forum_topics, $db_forum_posts, $db_forum_stats, $db;
	$sql = Cot::$db->delete($db_forum_posts, 'fp_cat=' . Cot::$db->quote($cat));
	$sql = Cot::$db->delete($db_forum_topics, 'ft_cat=' . Cot::$db->quote($cat));
	$sql = Cot::$db->delete($db_forum_stats, 'fs_cat=' . Cot::$db->quote($cat));

    if (!empty(Cot::$cache)) {
        Cot::$cache->db->remove('cot_sections_act', 'system');
    }
}

$minimaxieditor = null;
if (Cot::$cfg['forums']['markup'] == 1) {
  $minimaxieditor = Cot::$cfg['forums']['minimaxieditor'];
}
