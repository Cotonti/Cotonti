<?php

/**
 * Forums API
 *
 * @package forums
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL.');

// Requirements
require_once cot_langfile('forums', 'module');
require_once cot_incfile('forums', 'module', 'resources');

// Global variables
global $db_forum_posts, $db_forum_topics, $db_forum_stats, $db_x;
$db_forum_posts = (isset($db_forum_posts)) ? $db_forum_posts : $db_x . 'forum_posts';
$db_forum_topics = (isset($db_forum_topics)) ? $db_forum_topics : $db_x . 'forum_topics';
$db_forum_stats = (isset($db_forum_stats)) ? $db_forum_stats : $db_x . 'forum_stats';

/**
 * Builds forum category path
 *
 * @param string $cat Category code
 * @param bool $nolast Last link as simple text
 * @param bool $forumslink Show forums main link
 * @return string
 */
function cot_forums_buildpath($cat, $nolast = false, $forumslink = true)
{
	global $structure, $cfg, $L;
	$mask = 'link_catpath';
	$mask = str_replace('%1$s', '{$url}', $mask);
	$mask = str_replace('%2$s', '{$title}', $mask);
	if ($cfg['homebreadcrumb'] && $forumslink)
	{
		$tmp[] = cot_rc($mask, array(
			'url' => $cfg['mainurl'],
			'title' => htmlspecialchars($cfg['maintitle'])
			));
	}
	if ($forumslink)
	{
		$tmp[] = cot_rc($mask, array(
			'url' => cot_url('forums'),
			'title' => $L['Forums']
			));
	}
	$pathcodes = explode('.', $structure['forums'][$cat]['path']);
	$last = count($pathcodes) - 1;
	foreach ($pathcodes as $k => $x)
	{
		if ($k == 0)
		{
			$tmp[] = cot_rc($mask, array(
				'url' => cot_url('forums', 'c=' . $x, '#' . $x),
				'title' => htmlspecialchars($structure['forums'][$x]['title'])
				));
		}
		else
		{
			$tmp[] = ($k === $last && $nolast) ? htmlspecialchars($structure['forums'][$x]['title']) : cot_rc($mask, array(
					'url' => cot_url('forums', 'm=topics&s=' . $x),
					'title' => htmlspecialchars($structure['forums'][$x]['title'])
				));
		}
	}
	return is_array($tmp) ? implode(' ' . $cfg['separator'] . ' ', $tmp) : '';
}

/**
 * Deletes outdated topics
 *
 * @param string $mode Selection criteria
 * @param string $section Section
 * @param int $param Selection parameter value
 * @return int
 */
function cot_forums_prunetopics($mode, $section, $param)
{
	global $db, $cfg, $sys, $db_forum_topics, $db_forum_posts, $db_forum_stats, $L;

	$num = 0;
	$num1 = 0;
	if (!is_int($param))
	{
		$param = (int) $param;
	}

	switch ($mode)
	{
		case 'updated':
			$limit = $sys['now'] - ($param * 86400);
			$sql1 = $db->query("SELECT * FROM $db_forum_topics WHERE ft_cat=".$db->quote($section)." AND ft_updated < $limit AND ft_sticky='0'");
			break;

		case 'single':
			$sql1 = $db->query("SELECT * FROM $db_forum_topics WHERE ft_cat=".$db->quote($section)." AND ft_id=$param");
			break;
	}

	if ($sql1->rowCount() > 0)
	{
		foreach ($sql1->fetchAll() as $row1)
		{
			$q = $row1['ft_id'];

			/* === Hook === */
			foreach (cot_getextplugins('forums.functions.prunetopics') as $pl)
			{
				include $pl;
			}
			/* ===== */

			$sql = $db->delete($db_forum_posts, "fp_topicid=$q");
			$num += $db->affectedRows;
			$sql = $db->delete($db_forum_topics, "ft_id=$q");
			$num1 += $db->affectedRows;
		}

		$sql = $db->delete($db_forum_topics, "ft_movedto=$q");
		$sql = $db->query("UPDATE $db_forum_stats SET fs_topiccount=fs_topiccount-$num1, fs_postcount=fs_postcount-$num WHERE fs_cat=".$db->quote($section));
	}
	$num1 = ($num1 == '') ? '0' : $num1;
	return($num1);
}

/**
 * Recounts posts in a given topic
 *
 * @param int $id Topic ID
 */
function cot_forums_resynctopic($id)
{
	global $db, $db_forum_topics, $db_forum_posts;

	if (!is_int($id))
	{
		$id = (int) $id;
	}

	$num = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid=$id")->fetchColumn();
	$db->update($db_forum_topics, array("ft_postcount" => $num), "ft_id=$id");

	$sql = $db->query("SELECT fp_posterid, fp_postername, fp_updated FROM $db_forum_posts WHERE fp_topicid=$id ORDER BY fp_id DESC LIMIT 1");
	if ($row = $sql->fetch())
	{
		$db->update($db_forum_topics, array("ft_lastposterid" => (int)$row['fp_posterid'],
			"ft_lastpostername" => $row['fp_last_postername'],
			"ft_updated" => (int)$row['fp_last_updated']
			), "ft_id=$id");
	}
}

/**
 * Changes last message for the section
 *
 * @param string $cat Section cat
 * @param string $postcount Post count
 * @param string $topiccount Topic count
 * @param string $viewcount View count
 */
function cot_forums_sectionsetlast($cat, $postcount = '', $topiccount='', $viewcount='')
{
	global $db, $db_forum_topics, $db_forum_stats;
	$row = $db->query("SELECT ft_id, ft_lastposterid, ft_lastpostername, ft_updated, ft_title FROM $db_forum_topics
		WHERE ft_cat='".$db->prep($cat)."' AND ft_movedto='' AND ft_mode='0' ORDER BY ft_updated DESC LIMIT 1")->fetch();

	$i_postcount = ($postcount != '' && is_int($postcount)) ? $postcount : 1;
	$i_topiccount = ($topiccount != '' && is_int($topiccount)) ? $topiccount : 1;

	$postcount = (!empty($postcount)) ? ", fs_postcount = ".$postcount : '';
	$topiccount = (!empty($topiccount)) ? ", fs_topiccount = ".$topiccount : '';
	$viewcount = (!empty($viewcount)) ? ", fs_viewcount = ".$viewcount : '';

	$db->query("INSERT INTO $db_forum_stats
		(fs_cat, fs_lt_id, fs_lt_title, fs_lt_date, fs_lt_posterid, fs_lt_postername, fs_topiccount, fs_postcount,
			fs_viewcount)
		VALUES (".$db->quote($cat).", ".(int)$row['ft_id'].", ".$db->quote($row['ft_title']).", "
			.(int)$row['ft_updated'].", ".(int)$row['ft_lastposterid'].", ".$db->quote($row['ft_lastpostername'])
			.",$i_topiccount, $i_postcount, 0)
		ON DUPLICATE KEY UPDATE
			fs_lt_id = ".(int)$row['ft_id'].",  fs_lt_title = ".$db->quote($row['ft_title']).",
			fs_lt_date = ".(int)$row['ft_updated'].", fs_lt_posterid = ".(int)$row['ft_lastposterid'].",
			fs_lt_postername = ".$db->quote($row['ft_lastpostername'])." $postcount $topiccount $viewcount");

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
	global $cfg, $structure, $cot_extrafields, $usr, $sys, $L;

	$new_elems = ($usr['id'] > 0 && $stat['fs_lt_date'] > $usr['lastvisit'] && $stat['fs_lt_posterid'] != $usr['id']);

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
		$tag_prefix . 'CAT_DEFSTATE' => htmlspecialchars($cfg['forums'][$cat]['defstate']),
	);

	if (is_array($stat))
	{
		if ($stat['fs_lt_date'] > 0)
		{
			$sections += array(
				$tag_prefix . 'LASTPOSTDATE' => cot_date('datetime_short', $stat['fs_lt_date'] + $usr['timezone'] * 3600),
				$tag_prefix . 'LASTPOSTDATE_STAMP' => $stat['fs_lt_date'] + $usr['timezone'] * 3600,
				$tag_prefix . 'LASTPOSTER' => cot_build_user($stat['fs_lt_posterid'], htmlspecialchars($stat['fs_lt_postername'])),
				$tag_prefix . 'LASTPOST' => cot_rc_link($new_elems ? cot_url('forums', 'm=posts&q=' . $stat['fs_lt_id'] . '&n=unread', '#unread') : cot_url('forums', 'm=posts&q=' . $stat['fs_lt_id'] . '&n=last', '#bottom'), cot_cutstring($stat['fs_lt_title'], 32)),
				$tag_prefix . 'TIMEAGO' => cot_build_timegap($stat['fs_lt_date'], $sys['now_offset'])
			);
		}

		$sections += array(
			$tag_prefix . 'TOPICCOUNT' => $stat['topiccount'],
			$tag_prefix . 'POSTCOUNT' => $stat['postcount'],
			$tag_prefix . 'VIEWCOUNT' => $stat['viewcount'],
			$tag_prefix . 'VIEWCOUNT_SHORT' => ($stat['viewcount'] > 9999) ? floor($stat['viewcount'] / 1000) . 'k' : $stat['viewcount'],
		);
	}

	if (!is_array($stat) || !$stat['fs_lt_date'])
	{
		$sections += array(
			$tag_prefix . 'LASTPOSTDATE' => '',
			$tag_prefix . 'LASTPOSTER' => '',
			$tag_prefix . 'LASTPOST' => '',
			$tag_prefix . 'TIMEAGO' => '',
			$tag_prefix . 'TOPICCOUNT' => 0,
			$tag_prefix . 'POSTCOUNT' => 0,
			$tag_prefix . 'VIEWCOUNT' => 0,
			$tag_prefix . 'VIEWCOUNT_SHORT' => 0,
		);
	}

	foreach ($cot_extrafields['structure'] as $row_c)
	{
		$uname = strtoupper($row_c['field_name']);
		$sections[$tag_prefix . $uname . '_TITLE'] = isset($L['structure_' . $row_c['field_name'] . '_title']) ? $L['structure_' . $row_c['field_name'] . '_title'] : $row_c['field_description'];
		$sections[$tag_prefix . $uname] = cot_build_extrafields_data('structure', $row_c, $structure['forums'][$cat][$row_c['field_name']]);
	}

	return $sections;
}

/**
 * Recounts all counters for a given cat
 *
 * @param string $cat Cat code
 * @return int topiccount
 */
function cot_forums_sync($cat)
{
	global $db, $db_forum_topics, $db_forum_posts, $db_forum_stats;
	$num1 = $db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_cat=" . $db->quote($cat))->fetchColumn();
	$num = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_cat=" . $db->quote($cat))->fetchColumn();
	cot_forums_sectionsetlast($cat, $num, $num1);
	return (int)$num1;
}

/**
 * Update forums category
 *
 * @param string $oldcat Old Cat code
 * @param string $newcat New Cat code
 * @return bool
 */
function cot_forums_updatecat($oldcat, $newcat)
{
	global $db, $db_forum_topics, $db_forum_posts, $db_forum_stats;

	$upd = (bool)$db->update($db_forum_topics, array('ft_cat' => $newcat), 'ft_cat=' . $db->quote($oldcat));
	$upd &= (bool)$db->update($db_forum_posts, array('fp_cat' => $newcat), 'fp_cat=' . $db->quote($oldcat));
	$upd &= (bool)$db->update($db_forum_stats, array('fs_cat' => $newcat), 'fs_cat=' . $db->quote($oldcat));

	return $upd;
}

/**
 * Delete forums category
 *
 * @param string $oldcat Old Cat code
 * @param string $newcat New Cat code
 * @return bool
 */
function cot_forums_deletecat($cat)
{
	global $db_forum_topics, $db_forum_posts, $db_forum_stats, $db;
	$sql = $db->delete($db_forum_posts, 'fp_cat=' . $db->quote($cat));
	$sql = $db->delete($db_forum_topics, 'ft_cat=' . $db->quote($cat));
	$sql = $db->delete($db_forum_stats, 'fs_cat=' . $db->quote($cat));
}

?>
