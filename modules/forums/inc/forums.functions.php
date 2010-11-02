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

defined('COT_CODE') or die('Wrong URL.');

// Requirements
cot_require_lang('forums', 'module');
cot_require_rc('forums');

// Global variables
$GLOBALS['db_forum_posts']		= (isset($GLOBALS['db_forum_posts']))     ? $GLOBALS['db_forum_posts']     : $GLOBALS['db_x'] . 'forum_posts';
$GLOBALS['db_forum_sections'] 	= (isset($GLOBALS['db_forum_sections']))  ? $GLOBALS['db_forum_sections']  : $GLOBALS['db_x'] . 'forum_sections';
$GLOBALS['db_forum_structure']	= (isset($GLOBALS['db_forum_structure'])) ? $GLOBALS['db_forum_structure'] : $GLOBALS['db_x'] . 'forum_structure';
$GLOBALS['db_forum_topics'] 	= (isset($GLOBALS['db_forum_topics']))    ? $GLOBALS['db_forum_topics']    : $GLOBALS['db_x'] . 'forum_topics';
$GLOBALS['db_forum_stats']		= (isset($GLOBALS['db_forum_stats']))     ? $GLOBALS['db_forum_stats']     : $GLOBALS['db_x'] . 'forum_stats';

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
function cot_build_forums($sectionid, $title, $category, $link = TRUE, $master = false)
{
	global $db, $cot_forums_str, $cfg, $db_forum_sections, $L, $q;
	$pathcodes = explode('.', $cot_forums_str[$category]['path']);

	if($link)
	{
		if($cfg['homebreadcrumb'])
		{
			$tmp[] = cot_rc('link_catpath', array(
				'url' => $cfg['mainurl'],
				'title' => htmlspecialchars($cfg['maintitle'])
			));
		}
		$tmp[] = cot_rc('link_catpath', array(
			'url' => cot_url('forums'),
			'title' => $L['Forums']
		));
		foreach($pathcodes as $k => $x)
		{
			$tmp[] = cot_rc('link_catpath', array(
				'url' => cot_url('forums', 'c='.$x, '#'.$x),
				'title' => htmlspecialchars($cot_forums_str[$x]['title'])
			));
		}
		if(is_array($master))
		{
			$tmp[] = cot_rc('link_catpath', array(
				'url' => cot_url('forums', 'm=topics&s='.$master[0]),
				'title' => htmlspecialchars($master[1])
			));
		}
		$tmp[] = empty($q) ? htmlspecialchars($title)
			: cot_rc('link_catpath', array(
			'url' => cot_url('forums', 'm=topics&s='.$sectionid),
			'title' =>  htmlspecialchars($title)
		));
	}
	else
	{
		foreach($pathcodes as $k => $x)
		{
			$tmp[]= htmlspecialchars($cot_forums_str[$x]['title']);
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
function cot_forum_deletesection($id)
{
	global $db, $db_forum_topics, $db_forum_posts, $db_forum_sections, $db_auth;

	$sql = $db->query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$id' ");
	$row = $sql->fetch();

	if ($row['fs_masterid'] > 0)
	{
		$sqql = $db->query("SELECT fs_masterid, fs_topiccount, fs_postcount FROM $db_forum_sections WHERE fs_id='$id' ");
		$roww = $sqql->fetch();

		$sc_posts = $roww['fs_postcount'];
		$sc_topics = $roww['fs_topiccount'];

		$sql = $db->query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount-".$sc_posts." WHERE fs_id='".$roww['fs_masterid']."' ");
		$sql = $db->query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-".$sc_topics." WHERE fs_id='".$roww['fs_masterid']."' ");

		cot_forum_sectionsetlast($row['fs_masterid']);
	}

	$sql = $db->query("DELETE FROM $db_forum_posts WHERE fp_cat='$id'");
	$num = $db->affectedRows;
	$sql = $db->query("DELETE FROM $db_forum_topics WHERE ft_cat='$id'");
	$num += $db->affectedRows;
	$sql = $db->query("DELETE FROM $db_forum_sections WHERE fs_id='$id'");
	$num += $db->affectedRows;
	$num += cot_auth_remove_item('forums', $id);
	return $num;
}

/**
 * Gets details for forum section
 *
 * @param int $id Section ID
 * @return mixed
 */
function cot_forum_info($id)
{
	global $db, $db_forum_sections;

	$sql = $db->query("SELECT * FROM $db_forum_sections WHERE fs_id='$id'");
	if($res = $sql->fetch())
	{
		return ($res);
	}
	else
	{
		return ('');
	}
}

/**
 * Deletes outdated topics
 *
 * @param string $mode Selection criteria
 * @param int $section Section
 * @param int $param Selection parameter value
 * @return int
 */
function cot_forum_prunetopics($mode, $section, $param)
{
	global $db, $cfg, $sys, $db_forum_topics, $db_forum_posts, $db_forum_sections, $L;

	$num = 0;
	$num1 = 0;

	switch ($mode)
	{
		case 'updated':
			$limit = $sys['now'] - ($param*86400);
			$sql1 = $db->query("SELECT * FROM $db_forum_topics WHERE ft_cat='$section' AND ft_updated<'$limit' AND ft_sticky='0'");
			break;

		case 'single':
			$sql1 = $db->query("SELECT * FROM $db_forum_topics WHERE ft_cat='$section' AND ft_id='$param'");
			break;
	}

	if ($sql1->rowCount()>0)
	{
		while ($row1 = $sql1->fetch())
		{
			$q = $row1['ft_id'];

			/* === Hook === */
			foreach (cot_getextplugins('forums.functions.prunetopics') as $pl)
			{
				include $pl;
			}
			/* ===== */

			$sql = $db->query("DELETE FROM $db_forum_posts WHERE fp_topicid='$q'");
			$num += $db->affectedRows;
			$sql = $db->query("DELETE FROM $db_forum_topics WHERE ft_id='$q'");
			$num1 += $db->affectedRows;

		}

		$sql = $db->query("DELETE FROM $db_forum_topics WHERE ft_movedto='$q'");
		$sql = $db->query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-'$num1', fs_postcount=fs_postcount-'$num' WHERE fs_id='$section'");

		$sql = $db->query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$section' ");
		$row = $sql->fetch();

		$fs_masterid = $row['fs_masterid'];

		$sql = ($fs_masterid>0) ? $db->query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-'$num1', fs_postcount=fs_postcount-'$num' WHERE fs_id='$fs_masterid'") : '';
	}
	$num1 = ($num1=='') ? '0' : $num1;
	return($num1);
}

/**
 * Recounts all counters for a given section
 *
 * @param int $id Section ID
 */
function cot_forum_resync($id)
{
	global $db, $db_forum_topics, $db_forum_posts, $db_forum_sections;

	$sql = $db->query("SELECT COUNT(*) FROM $db_forum_sections WHERE fs_masterid='$id' ");
	$result = $sql->fetchColumn();

	if (!$result)
	{
		$sql = $db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_cat='$id'");
		$num = $sql->fetchColumn();
		$sql = $db->query("UPDATE $db_forum_sections SET fs_topiccount='$num' WHERE fs_id='$id'");
		$sql = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_cat='$id'");
		$num = $sql->fetchColumn();
		$sql = $db->query("UPDATE $db_forum_sections SET fs_postcount='$num' WHERE fs_id='$id'");
	}
	else
	{
		$sql = $db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_cat='$id'");
		$num = $sql->fetchColumn();
		$sql = $db->query("SELECT SUM(fs_topiccount) FROM $db_forum_sections WHERE fs_masterid='$id'");
		$num = $num + $sql->fetchColumn();
		$sql = $db->query("UPDATE $db_forum_sections SET fs_topiccount='$num' WHERE fs_id='$id'");
		$sql = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_cat='$id'");
		$num = $sql->fetchColumn();
		$sql = $db->query("SELECT SUM(fs_postcount) FROM $db_forum_sections WHERE fs_masterid='$id'");
		$num = $num + $sql->fetchColumn();
		$sql = $db->query("UPDATE $db_forum_sections SET fs_postcount='$num' WHERE fs_id='$id'");
	}
}

/**
 * Recounts posts in a given topic
 *
 * @param int $id Topic ID
 */
function cot_forum_resynctopic($id)
{
	global $db, $db_forum_topics, $db_forum_posts;

	$sql = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$id'");
	$num = $sql->fetchColumn();
	$sql = $db->query("UPDATE $db_forum_topics SET ft_postcount='$num' WHERE ft_id='$id'");

	$sql = $db->query("SELECT fp_posterid, fp_postername, fp_updated
    FROM $db_forum_posts
    WHERE fp_topicid='$id'
    ORDER BY fp_id DESC LIMIT 1");

	if ($row = $sql->fetch())
	{
		$sql = $db->query("UPDATE $db_forum_topics
        SET ft_lastposterid='".(int)$row['fp_posterid']."',
            ft_lastpostername='".$db->prep($row['fp_last_postername'])."',
            ft_updated='".(int)$row['fp_last_updated']."'
        WHERE ft_id='$id'");

	}
}

/**
 * Recalculates all counters in forums
 */
function cot_forum_resyncall()
{
	global $db, $db_forum_sections;

	$sql = $db->query("SELECT fs_id FROM $db_forum_sections");
	while ($row = $sql->fetch())
	{
		cot_forum_resync($row['fs_id']);
	}
}

/**
 * Changes last message for the section
 *
 * @param int $id Section ID
 */
function cot_forum_sectionsetlast($id)
{
	global $db, $db_forum_topics, $db_forum_sections;
	$sql = $db->query("SELECT ft_id, ft_lastposterid, ft_lastpostername, ft_updated, ft_title FROM $db_forum_topics WHERE ft_cat='$id' AND ft_movedto='0' and ft_mode='0' ORDER BY ft_updated DESC LIMIT 1");
	$row = $sql->fetch();
	$sql = $db->query("UPDATE $db_forum_sections SET fs_lt_id=".(int)$row['ft_id'].", fs_lt_title='".$db->prep($row['ft_title'])."', fs_lt_date=".(int)$row['ft_updated'].", fs_lt_posterid=".(int)$row['ft_lastposterid'].", fs_lt_postername='".$db->prep($row['ft_lastpostername'])."' WHERE fs_id='$id'");

	$sqll = $db->query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$id' ");
	$roww = $sqll->fetch();
	$fs_masterid = $roww['fs_masterid'];

	$sql = ($fs_masterid>0) ? $db->query("UPDATE $db_forum_sections SET fs_lt_id=".(int)$row['ft_id'].", fs_lt_title='".$db->prep($row['ft_title'])."', fs_lt_date=".(int)$row['ft_updated'].", fs_lt_posterid=".(int)$row['ft_lastposterid'].", fs_lt_postername='".$db->prep($row['ft_lastpostername'])."' WHERE fs_id='$fs_masterid'") : '';
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
	global $cfg,  $structure, $cot_extrafields, $usr, $sys;

	$new_elems = ($usr['id']>0 && $stat['fs_lt_date']>$usr['lastvisit'] && $stat['fs_lt_posterid']!=$usr['id']);

	$sections = array(
		$tag_prefix."CAT" => $cat,
		$tag_prefix."LOCKED" => $structure['forums'][$cat]['locked'],
		$tag_prefix."TITLE" => $structure['forums'][$cat]['title'],
		$tag_prefix."DESC" => cot_parse_autourls($structure['forums'][$cat]['desc']).($structure['forums'][$cat]['locked']) ? " ".$L['Locked'] : '',
		$tag_prefix."ICON" => $structure['forums'][$cat]['icon'],
		$tag_prefix."URL" => cot_url('forums', "m=topics&s=".$cat),
		$tag_prefix."SECTIONSURL" => cot_url('forums', "c=".$cat),
		$tag_prefix."NEWPOSTS" => $new_elems,
		$tag_prefix."CAT_DEFSTATE" => htmlspecialchars($cfg['forums'][$cat]['defstate'])
	);

	if ($stat['fs_lt_date'] > 0)
	{
		$sections += array(
			$tag_prefix."LASTPOSTDATE" => @date($cfg['formatmonthdayhourmin'], $stat['fs_lt_date'] + $usr['timezone'] * 3600),
			$tag_prefix."LASTPOSTER" => cot_build_user($stat['fs_lt_posterid'], htmlspecialchars($stat['fs_lt_postername'])),
			$tag_prefix."LASTPOST" => cot_rc_link($new_elems ? cot_url('forums', "m=posts&q=".$stat['fs_lt_id']."&n=unread", "#unread") : cot_url('forums', "m=posts&q=".$stat['fs_lt_id']."&n=last", "#bottom"), cot_cutstring($stat['fs_lt_title'], 32)),
			$tag_prefix."TIMEAGO" => cot_build_timegap($stat['fs_lt_date'], $sys['now_offset'])
		);
	}

	if (is_array($stat))
	{
		$sections += array(
			$tag_prefix."TOPICCOUNT" => $stat['topiccount'],
			$tag_prefix."POSTCOUNT" => $stat['postcount'],
			$tag_prefix."VIEWCOUNT" => $stat['viewcount'],
			$tag_prefix."VIEWCOUNT_SHORT" => ($stat['viewcount'] > 9999) ? floor($stat['viewcount']/1000)."k" : $stat['viewcount'],
		);
	}

	foreach ($cot_extrafields['structure'] as $row_c)
	{
		$uname = strtoupper($row_c['field_name']);
		$sections[$tag_prefix.$uname.'_TITLE'] = isset($L['structure_'.$row_c['field_name'].'_title']) ?  $L['structure_'.$row_c['field_name'].'_title'] : $row_c['field_description'];
		$sections[$tag_prefix.$uname] = cot_build_extrafields_data('structure', $row_c, $structure['forums'][$cat][$row_c['field_name']]);
	}

	return $sections;
}

?>
