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
 * Builds forum category path
 *
 * @param string $cat Category code
 * @param string $mask Format mask
 * @return string
 */
function cot_build_forumpath($cat, $mask = 'link_catpath')
{
	global $structure, $cfg, $L;
	$mask = str_replace('%1$s', '{$url}', $mask);
	$mask = str_replace('%2$s', '{$title}', $mask);
	if ($cfg['homebreadcrumb'])
	{
		$tmp[] = cot_rc($mask, array(
			'url' => $cfg['mainurl'],
			'title' => htmlspecialchars($cfg['maintitle'])
		));
	}
	$tmp[] = cot_rc($mask, array(
		'url' => cot_url('forums'),
		'title' => $L['Forums']
	));

	$pathcodes = explode('.', $$structure['forums'][$cat]['path']);
	$last = count($pathcodes) - 1;
	$list = defined('COT_LIST');
	foreach ($pathcodes as $k => $x)
	{
		if ($k == 0)
		{
			$tmp[] = cot_rc($mask, array(
				'url' => cot_url('forums', 'c='.$x, '#'.$x),
				'title' => htmlspecialchars($cot_forums_str[$x]['title'])
			));
		}
		else
		{
			$tmp[] = ($k === $last) ? htmlspecialchars($$structure['forums'][$x]['title'])
				: cot_rc($mask, array(
				'url' => cot_url('forums', 'm=topics&s='.$x),
				'title' => htmlspecialchars($$structure['forums'][$x]['title'])
			));
		}
	}
	return is_array($tmp) ? implode(' '.$cfg['separator'].' ', $tmp) : '';
}

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
 * Deletes outdated topics
 *
 * @param string $mode Selection criteria
 * @param string $section Section
 * @param int $param Selection parameter value
 * @return int
 */
function cot_forum_prunetopics($mode, $section, $param)
{
	global $db, $cfg, $sys, $db_forum_topics, $db_forum_posts, $db_forum_stats, $L;

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
		$sql = $db->query("UPDATE $db_forum_stats SET fs_topiccount=fs_topiccount-'$num1', fs_postcount=fs_postcount-'$num' WHERE fs_cat='$section'");
	}
	$num1 = ($num1=='') ? '0' : $num1;
	return($num1);
}

/**
 * Recounts all counters for a given section
 *
 * @param string $section Section Code
 */
function cot_forum_resync($section)
{
	global $db, $db_forum_topics, $db_forum_posts, $db_forum_stats;

	$sql = $db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_cat='$section'");
	$num1 = $sql->fetchColumn();
	$sql = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_cat='$section'");
	$num = $sql->fetchColumn();
	$sql = $db->query("UPDATE $db_forum_stats SET fs_postcount='$num', fs_topiccount='$num1' WHERE fs_cat='$section'");
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
 * @param string $cat Section cat
 */
function cot_forum_sectionsetlast($cat)
{
	global $db, $db_forum_topics, $db_forum_stats;
	$sql = $db->query("SELECT ft_id, ft_lastposterid, ft_lastpostername, ft_updated, ft_title FROM $db_forum_topics WHERE ft_cat='$cat' AND ft_movedto='0' and ft_mode='0' ORDER BY ft_updated DESC LIMIT 1");
	$row = $sql->fetch();
	$sql = $db->query("UPDATE $db_forum_sections SET fs_lt_id=".(int)$row['ft_id'].", fs_lt_title='".$db->prep($row['ft_title'])."', fs_lt_date=".(int)$row['ft_updated'].", fs_lt_posterid=".(int)$row['ft_lastposterid'].", fs_lt_postername='".$db->prep($row['ft_lastpostername'])."' WHERE fs_cat='$cat'");
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


/**
 * Strip quotes
 *
 * @param string $string String
 *
 * @return string
 */
function cot_stripquote($string)
{
	global $R;
	$startindex = mb_stripos($string, $R['forums_code_quote_begin']);
	while ($startindex >= 0)
	{
		$stopindex = mb_strpos($string, $R['forums_code_quote_close']);
		if ($stopindex > 0)
		{
			$fragment = mb_substr($string,$startindex,($stopindex-$startindex+8));
			$string = str_ireplace($fragment,'',$string);
			$stopindex = mb_stripos($string, $R['forums_code_quote_close']);
		}
		else
		{
			break;
		}
		$string = trim($string);
		$startindex = mb_stripos($string, $R['forums_code_quote_begin']);
	}
	return($string);
}

?>
