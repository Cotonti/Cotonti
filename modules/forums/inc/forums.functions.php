<?php
/**
 * Forums API
 *
 * @package forums
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

// Requirements
sed_require_lang('forums', 'module');
sed_require_rc('forums');

// Global variables
$GLOBALS['db_forum_posts']		= (isset($GLOBALS['db_forum_posts']))     ? $GLOBALS['db_forum_posts']     : $GLOBALS['db_x'] . 'forum_posts';
$GLOBALS['db_forum_sections'] 	= (isset($GLOBALS['db_forum_sections']))  ? $GLOBALS['db_forum_sections']  : $GLOBALS['db_x'] . 'forum_sections';
$GLOBALS['db_forum_structure']	= (isset($GLOBALS['db_forum_structure'])) ? $GLOBALS['db_forum_structure'] : $GLOBALS['db_x'] . 'forum_structure';
$GLOBALS['db_forum_topics'] 	= (isset($GLOBALS['db_forum_topics']))    ? $GLOBALS['db_forum_topics']    : $GLOBALS['db_x'] . 'forum_topics';

/**
 * Builds a javascript function for text insertion
 *
 * @param string $c1 Form name
 * @param string $c2 Field name
 * @return string
 */
function sed_build_addtxt($c1, $c2)
{
	$result = "
	function addtxt(text) {
		insertText(document, '$c1', '$c2', text);
	}
		";
	return($result);
}

/**
 * Returns forum thread path
 *
 * @param int $sectionid Section ID
 * @param string $title Thread title
 * @param string $category Category code
 * @param string $link Display as links
 * @param mixed $master Master section
 * @return string
 */
function sed_build_forums($sectionid, $title, $category, $link = TRUE, $master = false)
{
	global $sed_forums_str, $cfg, $db_forum_sections, $L, $q;
	$pathcodes = explode('.', $sed_forums_str[$category]['path']);

	if($link)
	{
		if($cfg['homebreadcrumb'])
		{
			$tmp[] = sed_rc('link_catpath', array(
				'url' => $cfg['mainurl'],
				'title' => htmlspecialchars($cfg['maintitle'])
			));
		}
		$tmp[] = sed_rc('link_catpath', array(
			'url' => sed_url('forums'),
			'title' => $L['Forums']
		));
		foreach($pathcodes as $k => $x)
		{
			$tmp[] = sed_rc('link_catpath', array(
				'url' => sed_url('forums', 'c='.$x, '#'.$x),
				'title' => htmlspecialchars($sed_forums_str[$x]['title'])
			));
		}
		if(is_array($master))
		{
			$tmp[] = sed_rc('link_catpath', array(
				'url' => sed_url('forums', 'm=topics&s='.$master[0]),
				'title' => htmlspecialchars($master[1])
			));
		}
		$tmp[] = empty($q) ? htmlspecialchars($title)
			: sed_rc('link_catpath', array(
			'url' => sed_url('forums', 'm=topics&s='.$sectionid),
			'title' =>  htmlspecialchars($title)
		));
	}
	else
	{
		foreach($pathcodes as $k => $x)
		{
			$tmp[]= htmlspecialchars($sed_forums_str[$x]['title']);
		}
		if(is_array($master))
		{
			$tmp[] = htmlspecialchars($master[1]);
		}
		$tmp[] = htmlspecialchars($title);
	}

	return implode(' '.$cfg['separator'].' ', $tmp);
}

/*
 * ==================================== Forum Functions ==================================
*/

/**
 * Removes a forum section and all its contents
 *
 * @param int $id Section ID
 * @return int Total number of records removed
 */
function sed_forum_deletesection($id)
{
	global $db_forum_topics, $db_forum_posts, $db_forum_sections, $db_auth;

	$sql = sed_sql_query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$id' ");
	$row = sed_sql_fetcharray($sql);

	if ($row['fs_masterid'] > 0)
	{
		$sqql = sed_sql_query("SELECT fs_masterid, fs_topiccount, fs_postcount FROM $db_forum_sections WHERE fs_id='$id' ");
		$roww = sed_sql_fetcharray($sqql);

		$sc_posts = $roww['fs_postcount'];
		$sc_topics = $roww['fs_topiccount'];

		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount-".$sc_posts." WHERE fs_id='".$roww['fs_masterid']."' ");
		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-".$sc_topics." WHERE fs_id='".$roww['fs_masterid']."' ");

		sed_forum_sectionsetlast($row['fs_masterid']);
	}

	$sql = sed_sql_query("DELETE FROM $db_forum_posts WHERE fp_sectionid='$id'");
	$num = sed_sql_affectedrows();
	$sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_sectionid='$id'");
	$num += sed_sql_affectedrows();
	$sql = sed_sql_query("DELETE FROM $db_forum_sections WHERE fs_id='$id'");
	$num += sed_sql_affectedrows();
	$num += sed_auth_remove_item('forums', $id);
	return $num;
}

/**
 * Gets details for forum section
 *
 * @param int $id Section ID
 * @return mixed
 */
function sed_forum_info($id)
{
	global $db_forum_sections;

	$sql = sed_sql_query("SELECT * FROM $db_forum_sections WHERE fs_id='$id'");
	if($res = sed_sql_fetcharray($sql))
	{
		return ($res);
	}
	else
	{
		return ('');
	}
}

/**
 * Moves outdated topics to trash
 *
 * @param string $mode Selection criteria
 * @param int $section Section
 * @param int $param Selection parameter value
 * @return int
 */
function sed_forum_prunetopics($mode, $section, $param)
{
	global $cfg, $sys, $db_forum_topics, $db_forum_posts, $db_forum_sections, $L;

	$num = 0;
	$num1 = 0;

	switch ($mode)
	{
		case 'updated':
			$limit = $sys['now'] - ($param*86400);
			$sql1 = sed_sql_query("SELECT * FROM $db_forum_topics WHERE ft_sectionid='$section' AND ft_updated<'$limit' AND ft_sticky='0'");
			break;

		case 'single':
			$sql1 = sed_sql_query("SELECT * FROM $db_forum_topics WHERE ft_sectionid='$section' AND ft_id='$param'");
			break;
	}

	if (sed_sql_numrows($sql1)>0)
	{
		while ($row1 = sed_sql_fetchassoc($sql1))
		{
			$q = $row1['ft_id'];

			if ($cfg['trash_forum'])
			{
				$sql = sed_sql_query("SELECT * FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id DESC");

				while ($row = sed_sql_fetchassoc($sql))
				{
					sed_trash_put('forumpost', $L['Post']." #".$row['fp_id']." from topic #".$q, "p".$row['fp_id']."-q".$q, $row);
				}
			}

			$sql = sed_sql_query("DELETE FROM $db_forum_posts WHERE fp_topicid='$q'");
			$num += sed_sql_affectedrows();

			if ($cfg['trash_forum'])
			{
				$sql = sed_sql_query("SELECT * FROM $db_forum_topics WHERE ft_id='$q'");

				while ($row = sed_sql_fetchassoc($sql))
				{
					sed_trash_put('forumtopic', $L['Topic']." #".$q." (no post left)", "q".$q, $row);
				}
			}

			$sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_id='$q'");
			$num1 += sed_sql_affectedrows();

			/* === Hook === */
			foreach (sed_getextplugins('forums.functions.prunetopics') as $pl)
			{
				include $pl;
			}
			/* ===== */

		}

		$sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_movedto='$q'");
		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-'$num1', fs_postcount=fs_postcount-'$num', fs_topiccount_pruned=fs_topiccount_pruned+'$num1', fs_postcount_pruned=fs_postcount_pruned+'$num' WHERE fs_id='$section'");

		$sql = sed_sql_query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$section' ");
		$row = sed_sql_fetcharray($sql);

		$fs_masterid = $row['fs_masterid'];

		$sql = ($fs_masterid>0) ? sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-'$num1', fs_postcount=fs_postcount-'$num', fs_topiccount_pruned=fs_topiccount_pruned+'$num1', fs_postcount_pruned=fs_postcount_pruned+'$num' WHERE fs_id='$fs_masterid'") : '';
	}
	$num1 = ($num1=='') ? '0' : $num1;
	return($num1);
}

/**
 * Recounts all counters for a given section
 *
 * @param int $id Section ID
 */
function sed_forum_resync($id)
{
	global $db_forum_topics, $db_forum_posts, $db_forum_sections;

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_sections WHERE fs_masterid='$id' ");
	$result = sed_sql_result($sql, 0, "COUNT(*)");

	if (!$result)
	{
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_sectionid='$id'");
		$num = sed_sql_result($sql,0,"COUNT(*)");
		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount='$num' WHERE fs_id='$id'");
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_sectionid='$id'");
		$num = sed_sql_result($sql, 0, "COUNT(*)");
		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount='$num' WHERE fs_id='$id'");
	}
	else
	{
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_sectionid='$id'");
		$num = sed_sql_result($sql, 0, "COUNT(*)");
		$sql = sed_sql_query("SELECT SUM(fs_topiccount) FROM $db_forum_sections WHERE fs_masterid='$id'");
		$num = $num + sed_sql_result($sql, 0, "SUM(fs_topiccount)");
		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount='$num' WHERE fs_id='$id'");
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_sectionid='$id'");
		$num = sed_sql_result($sql, 0, "COUNT(*)");
		$sql = sed_sql_query("SELECT SUM(fs_postcount) FROM $db_forum_sections WHERE fs_masterid='$id'");
		$num = $num + sed_sql_result($sql, 0, "SUM(fs_postcount)");
		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount='$num' WHERE fs_id='$id'");
	}
}

/**
 * Recounts posts in a given topic
 *
 * @param int $id Topic ID
 */
function sed_forum_resynctopic($id)
{
	global $db_forum_topics, $db_forum_posts;

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$id'");
	$num = sed_sql_result($sql, 0, "COUNT(*)");
	$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_postcount='$num' WHERE ft_id='$id'");

	$sql = sed_sql_query("SELECT fp_posterid, fp_postername, fp_updated
    FROM $db_forum_posts
    WHERE fp_topicid='$id'
    ORDER BY fp_id DESC LIMIT 1");

	if ($row = sed_sql_fetcharray($sql))
	{
		$sql = sed_sql_query("UPDATE $db_forum_topics
        SET ft_lastposterid='".(int)$row['fp_posterid']."',
            ft_lastpostername='".sed_sql_prep($row['fp_last_postername'])."',
            ft_updated='".(int)$row['fp_last_updated']."'
        WHERE ft_id='$id'");

	}
}

/**
 * Recalculates all counters in forums
 */
function sed_forum_resyncall()
{
	global $db_forum_sections;

	$sql = sed_sql_query("SELECT fs_id FROM $db_forum_sections");
	while ($row = sed_sql_fetcharray($sql))
	{
		sed_forum_resync($row['fs_id']);
	}
}

/**
 * Changes last message for the section
 *
 * @param int $id Section ID
 */
function sed_forum_sectionsetlast($id)
{
	global $db_forum_topics, $db_forum_sections;
	$sql = sed_sql_query("SELECT ft_id, ft_lastposterid, ft_lastpostername, ft_updated, ft_title FROM $db_forum_topics WHERE ft_sectionid='$id' AND ft_movedto='0' and ft_mode='0' ORDER BY ft_updated DESC LIMIT 1");
	$row = sed_sql_fetcharray($sql);
	$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_lt_id=".(int)$row['ft_id'].", fs_lt_title='".sed_sql_prep($row['ft_title'])."', fs_lt_date=".(int)$row['ft_updated'].", fs_lt_posterid=".(int)$row['ft_lastposterid'].", fs_lt_postername='".sed_sql_prep($row['ft_lastpostername'])."' WHERE fs_id='$id'");

	$sqll = sed_sql_query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$id' ");
	$roww = sed_sql_fetcharray($sqll);
	$fs_masterid = $roww['fs_masterid'];

	$sql = ($fs_masterid>0) ? sed_sql_query("UPDATE $db_forum_sections SET fs_lt_id=".(int)$row['ft_id'].", fs_lt_title='".sed_sql_prep($row['ft_title'])."', fs_lt_date=".(int)$row['ft_updated'].", fs_lt_posterid=".(int)$row['ft_lastposterid'].", fs_lt_postername='".sed_sql_prep($row['ft_lastpostername'])."' WHERE fs_id='$fs_masterid'") : '';
}

/**
 * Loads complete forum structure into array
 */
function sed_load_forum_structure()
{
	global $db_forum_structure, $cfg, $L, $sed_forums_str;

	$sed_forums_str = array();
	$sql = sed_sql_query("SELECT * FROM $db_forum_structure ORDER BY fn_path ASC");

	while ($row = sed_sql_fetcharray($sql))
	{
		if (!empty($row['fn_icon']))
		{
			$row['fn_icon'] = "<img src=\"".$row['fn_icon']."\" alt=\"\" />";
		}

		$path2 = mb_strrpos($row['fn_path'], '.');

		$row['fn_tpl'] = (empty($row['fn_tpl'])) ? $row['fn_code'] : $row['fn_tpl'];

		if ($path2>0)
		{
			$path1 = mb_substr($row['fn_path'],0,($path2));
			$path[$row['fn_path']] = $path[$path1].'.'.$row['fn_code'];
			$tpath[$row['fn_path']] = $tpath[$path1].' '.$cfg['separator'].' '.$row['fn_title'];
			$row['fn_tpl'] = ($row['fn_tpl']=='same_as_parent') ? $parent_tpl : $row['fn_tpl'];
		}
		else
		{
			$path[$row['fn_path']] = $row['fn_code'];
			$tpath[$row['fn_path']] = $row['fn_title'];
		}

		$parent_tpl = $row['fn_tpl'];

		$sed_forums_str[$row['fn_code']] = array (
			'path' => $path[$row['fn_path']],
			'tpath' => $tpath[$row['fn_path']],
			'rpath' => $row['fn_path'],
			'tpl' => $row['fn_tpl'],
			'title' => $row['fn_title'],
			'desc' => $row['fn_desc'],
			'icon' => $row['fn_icon'],
			'defstate' => $row['fn_defstate']
		);
	}
}

// Preload forum structure

if (!$sed_forums_str && !$cfg['disable_forums'])
{
	sed_load_forum_structure();
	$cot_cache && $cot_cache->db->store('sed_forums_str', $sed_forums_str, 'system');
}

?>
