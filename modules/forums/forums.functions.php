<?php
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
	global $sed_forums_str, $cfg, $db_forum_sections, $L;
	$pathcodes = explode('.', $sed_forums_str[$category]['path']);

	if($link)
	{
		if($cfg['homebreadcrumb'])
		{
			$tmp[] = '<a href="'.$cfg['mainurl'].'">'.htmlspecialchars($cfg['maintitle']).'</a>';
		}
		$tmp[] = '<a href="'.sed_url('forums').'">'.$L['Forums'].'</a>';
		foreach($pathcodes as $k => $x)
		{
			$tmp[] = '<a href="'.sed_url('forums', 'c='.$x, '#'.$x).'">'.htmlspecialchars($sed_forums_str[$x]['title']).'</a>';
		}
		if(is_array($master))
		{
			$tmp[] = '<a href="'.sed_url('forums', 'm=topics&s='.$master[0]).'">'.htmlspecialchars($master[1]).'</a>';
		}
		$tmp[] = '<a href="'.sed_url('forums', 'm=topics&s='.$sectionid).'">'.htmlspecialchars($title).'</a>';
	}
	else
	{
		foreach($pathcodes as $k => $x)
		{
			$tmp[]= htmlspecialchars($sed_forums_str[$x]['title']);
		}
		if(is_array($master))
		{
			$tmp[] = $master[1];
		}
		$tmp[] = htmlspecialchars($title);
	}

	return implode(' '.$cfg['separator'].' ', $tmp);
}

/*
 * ==================================== Forum Functions ==================================
 */

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
	global $cfg, $sys, $db_forum_topics, $db_forum_posts, $db_forum_sections, $db_polls, $L;

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
				{ sed_trash_put('forumpost', $L['Post']." #".$row['fp_id']." from topic #".$q, "p".$row['fp_id']."-q".$q, $row); }
			}

			$sql = sed_sql_query("DELETE FROM $db_forum_posts WHERE fp_topicid='$q'");
			$num += sed_sql_affectedrows();

			if ($cfg['trash_forum'])
			{
				$sql = sed_sql_query("SELECT * FROM $db_forum_topics WHERE ft_id='$q'");

				while ($row = sed_sql_fetchassoc($sql))
				{ sed_trash_put('forumtopic', $L['Topic']." #".$q." (no post left)", "q".$q, $row); }
			}

			$sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_id='$q'");
			$num1 += sed_sql_affectedrows();

			$sql = sed_sql_query("SELECT poll_id FROM $db_polls WHERE poll_type='forum' AND poll_code='$q' LIMIT 1");
			if ($row = sed_sql_fetcharray($sql))
			{
				$id=$row['poll_id'];
				global $db_polls_options, $db_polls_voters;
				$sql = sed_sql_query("DELETE FROM $db_polls WHERE poll_id=".$id);
				$sql = sed_sql_query("DELETE FROM $db_polls_options WHERE po_pollid=".$id);
				$sql = sed_sql_query("DELETE FROM $db_polls_voters WHERE pv_pollid=".$id);
			}
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
 * Changes last message for the section
 *
 * @param int $id Section ID
 */
function sed_forum_sectionsetlast($id)
{
	global $db_forum_topics, $db_forum_sections;
	$sql = sed_sql_query("SELECT ft_id, ft_lastposterid, ft_lastpostername, ft_updated, ft_title, ft_poll FROM $db_forum_topics WHERE ft_sectionid='$id' AND ft_movedto='0' and ft_mode='0' ORDER BY ft_updated DESC LIMIT 1");
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
		{ $row['fn_icon'] = "<img src=\"".$row['fn_icon']."\" alt=\"\" />"; }

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

/**
 * Renders forum section selection dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_sections($check, $name)
{
	global $db_forum_sections, $cfg;

	$sql = sed_sql_query("SELECT fs_id, fs_title, fs_category FROM $db_forum_sections WHERE 1 ORDER by fs_order ASC");
	$result = "<select name=\"$name\" size=\"1\">";
	while ($row = sed_sql_fetcharray($sql))
	{
		$selected = ($row['fs_id'] == $check) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"".$row['fs_id']."\" $selected>".htmlspecialchars(sed_cutstring($row['fs_category'], 24));
		$result .= ' '.$cfg['separator'].' '.htmlspecialchars(sed_cutstring($row['fs_title'], 32));
	}
	$result .= "</select>";
	return($result);
}

/**
 * Checks whether user is online
 *
 * @param int $id User ID
 * @return bool
 */
function sed_userisonline($id)
{
	global $sed_usersonline;

	$res = FALSE;
	if (is_array($sed_usersonline))
	{ $res = (in_array($id,$sed_usersonline)) ? TRUE : FALSE; }
	return ($res);
}

// Preload forum structure

if (!$sed_forums_str && !$cfg['disable_forums'])
{
	sed_load_forum_structure();
	$cfg['cache'] && $cot_cache->db_set('sed_forums_str', $sed_forums_str, 'system');
}

?>
