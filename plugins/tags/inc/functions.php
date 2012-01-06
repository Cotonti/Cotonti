<?php
/**
 * Tags functions
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

/**
 * Parses search string into SQL query
 *
 * @param string $qs User input
 * @param array $join_columns Columns to be joined by on tag_item match in subquery
 * @return string
 */
function sed_tag_parse_query($qs, $join_columns)
{
	global $db_tag_references;
	if (is_string($join_columns))
	{
		$join_columns = array($join_columns);
	}
	$tokens1 = explode(';', $qs);
	$tokens1 = array_map('trim', $tokens1);
	$cnt1 = count($tokens1);
	for ($i = 0; $i < $cnt1; $i++)
	{
		$tokens2 = explode(',', $tokens1[$i]);
		$tokens2 = array_map('trim', $tokens2);
		$cnt2 = count($tokens2);
		for ($j = 0; $j < $cnt2; $j++)
		{
			$tag = sed_tag_prep($tokens2[$j]);
			if (!empty($tag))
			{

				if (mb_strpos($tag, '*') !== false)
				{
					$tag = str_replace('*', '%', $tag);
					$op = "LIKE '" . sed_sql_prep($tag) . "'";
				}
				else
				{
					$op = "= '" . sed_sql_prep($tag) . "'";
				}
				if ($j == 0)
				{
					$tokens2[$j] = 'r.tag ' . $op;
				}
				else
				{
					$join_conds = array();
					foreach ($join_columns as $col)
					{
						$join_conds[] = "r{$i}_{$j}.tag_item = $col"; 
					}
					$join_cond = implode(' OR ', $join_conds);
					$tokens2[$j] = "EXISTS (SELECT * FROM $db_tag_references AS r{$i}_{$j} WHERE ($join_cond) AND r{$i}_{$j}.tag $op)";
				}
			}
			else
			{
				return '';
			}
		}
		$tokens1[$i] = implode(' AND ', $tokens2);
	}
	$query = implode(' OR ', $tokens1);
	return $query;
}

/**
 * Global tag cloud and search form
 *
 * @param string $area Site area
 */
function sed_tag_search_form($area = 'all')
{
	global $d, $perpage, $lang, $tl, $qs, $t, $L, $cfg, $db_tag_references, $tc_styles;
	$limit = ($perpage > 0) ? "$d, $perpage" : NULL;
	$tcloud = sed_tag_cloud($area, $cfg['plugin']['tags']['order'], $limit);
	$tc_html = '<div class="tag_cloud">';
	foreach($tcloud as $tag => $cnt)
	{
		$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
		$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
		$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
		foreach($tc_styles as $key => $val)
		{
			if($cnt <= $key)
			{
				$dim = $val;
				break;
			}
		}
		$tc_html .= '<a href="'.sed_url('plug', array('e' => 'tags', 'a' => $area, 't' => $tag_u, 'tl' => $tl)).'" class="'.$dim.'">'.htmlspecialchars($tag_t).'</a> ';
	}
	$tc_html .= '</div>';
	$t->assign('TAGS_CLOUD_BODY', $tc_html);
	$t->parse('MAIN.TAGS_CLOUD');
	if ($perpage > 0)
	{
		$where = $area == 'all' ? '' : "WHERE tag_area = '$area'";
		$sql = sed_sql_query("SELECT COUNT(DISTINCT `tag`) FROM $db_tag_references $where");
		$totalitems = (int) sed_sql_result($sql, 0, 0);
		$pagnav = sed_pagination(sed_url('plug','e=tags&a='.$area), $d, $totalitems, $perpage);
		list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('plug','e=tags&a='.$area), $d, $totalitems, $perpage, TRUE);

		$t->assign(array(
			'TAGS_PAGEPREV' => $pagination_prev,
			'TAGS_PAGENEXT' => $pagination_next,
			'TAGS_PAGNAV' => $pagnav
		));
	}
}

/**
 * Search by tag in pages
 *
 * @param string $query User-entered query string
 */
function sed_tag_search_pages($query)
{
	global $t, $L, $lang, $cfg, $usr, $qs, $d, $db_tag_references, $db_pages, $db_users, $db_extra_fields, $o, $row, $sed_cat;
	$query = sed_tag_parse_query($query, 'p.page_id');
	if (empty($query))
	{
		return;
	}
	$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*)
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
			ON r.tag_item = p.page_id
		WHERE r.tag_area = 'pages' AND ($query) AND p.page_state = 0"), 0, 0);
	switch ($o)
	{
		case 'title':
			$order = 'ORDER BY `page_title`';
			break;
		case 'date':
			$order = 'ORDER BY `page_date` DESC';
			break;
		case 'category':
			$order = 'ORDER BY `page_cat`';
			break;
		default:
			$order = '';
	}
	$sql = sed_sql_query("SELECT p.*, u.*
		FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
			ON r.tag_item = p.page_id
		LEFT JOIN $db_users AS u
			ON u.user_id = p.page_ownerid
		WHERE r.tag_area = 'pages' AND ($query) AND p.page_id IS NOT NULL AND p.page_state = 0
		$order
		LIMIT $d, {$cfg['maxrowsperpage']}");
	$t->assign('TAGS_RESULT_TITLE', $L['tags_Found_in_pages']);
	if (sed_sql_numrows($sql) > 0)
	{
		while($row = sed_sql_fetchassoc($sql))
		{
			$tags = sed_tag_list($row['page_id']);
			$tag_list = '';
			$tag_i = 0;
			foreach($tags as $tag)
			{
				$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
				$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
				$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
				if ($tag_i > 0) $tag_list .= ', ';
				$tag_list .= '<a href="'.sed_url('plug', array('e' => 'tags', 'a' => 'pages', 't' => $tag_u, 'tl' => $tl)).'">'.htmlspecialchars($tag_t).'</a>';
				$tag_i++;
			}
			$t->assign(array(
				'TAGS_RESULT_ROW_URL' => empty($row['page_alias']) ? sed_url('page', 'id='.$row['page_id']) : sed_url('page', 'al='.$row['page_alias']),
				'TAGS_RESULT_ROW_TITLE' => htmlspecialchars($row['page_title']),
				'TAGS_RESULT_ROW_PATH' => sed_build_catpath($row['page_cat'], '<a href="%1$s">%2$s</a>'),
				'TAGS_RESULT_ROW_TAGS' => $tag_list,
				'TAGS_RESULT_ROW_ID' => $row['page_id'],
				'TAGS_RESULT_ROW_CAT' => $row['page_cat'],
				'TAGS_RESULT_ROW_CATURL' => sed_url('list', 'c=' . $row['page_cat']),
				'TAGS_RESULT_ROW_CATTITLE' => $sed_cat[$row['page_cat']]['title'],
				'TAGS_RESULT_ROW_CATDESC' => $sed_cat[$row['page_cat']]['desc'],
				'TAGS_RESULT_ROW_CATICON' => $sed_cat[$row['page_cat']]['icon'],
				'TAGS_RESULT_ROW_KEY' => $row['page_key'],
				'TAGS_RESULT_ROW_DESC' => $row['page_desc'],
				'TAGS_RESULT_ROW_AUTHOR' => $row['page_author'],
				'TAGS_RESULT_ROW_OWNER' => sed_build_user($row['page_ownerid'], htmlspecialchars($row['user_name'])),
				'TAGS_RESULT_ROW_AVATAR' => sed_build_userimage($row['user_avatar'], 'avatar'),
				'TAGS_RESULT_ROW_DATE' => @date($cfg['dateformat'], $row['page_date'] + $usr['timezone'] * 3600),
				'TAGS_RESULT_ROW_BEGIN' => @date($cfg['dateformat'], $row['page_begin'] + $usr['timezone'] * 3600),
				'TAGS_RESULT_ROW_EXPIRE' => @date($cfg['dateformat'], $row['page_expire'] + $usr['timezone'] * 3600),
				'TAGS_RESULT_ROW_ALIAS' => $row['page_alias']
			));
			
			switch($row['page_type'])
			{
				case '1':
					$t->assign('TAGS_RESULT_ROW_TEXT', $row['page_text']);
					break;
				default:
					$text = sed_parse(htmlspecialchars($row['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], true);
					$text = sed_post_parse($text, 'pages');
					$t->assign('TAGS_RESULT_ROW_TEXT', $text);
				break;
			}

			// Extra fields
			$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='pages'");
			while($row_e = sed_sql_fetchassoc($fieldsres))
			{
				$uname = strtoupper($row_e['field_name']);
				$t->assign('TAGS_RESULT_ROW_'.$uname, sed_build_extrafields_data('page', $row_e['field_type'], $row_e['field_name'], $row['page_'.$row_e['field_name']]));
				isset($L['page_'.$row_e['field_name'].'_title']) ? $t->assign('TAGS_RESULT_ROW_'.$uname.'_TITLE', $L['page_'.$row_e['field_name'].'_title']) : $t->assign('TAGS_RESULT_ROW_'.$uname.'_TITLE', $row_e['field_description']);
			}
			$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_ROW');
		}
		sed_sql_freeresult($sql);
		$qs_u = sed_urlencode($qs, $cfg['plugin']['tags']['translit']);
		$tl = $lang != 'en' && $qs_u != $qs ? 1 : null;
		$pagnav = sed_pagination(sed_url('plug', array('e' => 'tags', 'a' => 'pages', 't' => $qs_u, 'tl' => $tl)), $d, $totalitems, $cfg['maxrowsperpage']);
		list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('plug', array('e' => 'tags', 'a' => 'pages', 't' => $qs_u, 'tl' => $tl)), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

		$t->assign(array(
			'TAGS_PAGEPREV' => $pagination_prev,
			'TAGS_PAGENEXT' => $pagination_next,
			'TAGS_PAGNAV' => $pagnav
		));
	}
	else
	{
		$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_NONE');
	}
	$t->parse('MAIN.TAGS_RESULT');
}

/**
 * Search by tag in forums
 *
 * @param string $query User-entered search query
 */
function sed_tag_search_forums($query)
{
	global $t, $L, $lang, $cfg, $usr, $qs, $d, $db_tag_references, $db_forum_topics, $db_forum_sections, $o, $row;
	$query = sed_tag_parse_query($query, 't.ft_id');
	if (empty($query))
	{
		return;
	}
	$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*)
		FROM $db_tag_references AS r LEFT JOIN $db_forum_topics AS t
			ON r.tag_item = t.ft_id
		WHERE r.tag_area = 'forums' AND ($query)"), 0, 0);
	switch ($o)
	{
		case 'title':
			$order = 'ORDER BY `ft_title`';
			break;
		case 'date':
			$order = 'ORDER BY `ft_updated` DESC';
			break;
		case 'category':
			$order = 'ORDER BY `ft_sectionid`';
			break;
		default:
			$order = '';
	}
	$sql = sed_sql_query("SELECT t.ft_id, t.ft_sectionid, t.ft_title, s.fs_id, s.fs_masterid, s.fs_mastername, s.fs_title, s.fs_category
		FROM $db_tag_references AS r LEFT JOIN $db_forum_topics AS t
			ON r.tag_item = t.ft_id
		LEFT JOIN $db_forum_sections AS s
			ON t.ft_sectionid = s.fs_id
		WHERE r.tag_area = 'forums' AND ($query) AND t.ft_id IS NOT NULL
		$order
		LIMIT $d, {$cfg['maxrowsperpage']}");
	$t->assign('TAGS_RESULT_TITLE', $L['tags_Found_in_forums']);
	if (sed_sql_numrows($sql) > 0)
	{
		while($row = sed_sql_fetchassoc($sql))
		{
			$tags = sed_tag_list($row['ft_id'], 'forums');
			$tag_list = '';
			$tag_i = 0;
			foreach($tags as $tag)
			{
				$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
				$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
				$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
				if ($tag_i > 0) $tag_list .= ', ';
				$tag_list .= '<a href="'.sed_url('plug', array('e' => 'tags', 'a' => 'forums', 't' => $tag_u, 'tl' => $tl)).'">'.htmlspecialchars($tag_t).'</a>';
				$tag_i++;
			}
			$master = ($row['fs_masterid'] > 0) ? array($row['fs_masterid'],$row['fs_mastername']) : false;
			$t->assign(array(
				'TAGS_RESULT_ROW_URL' => sed_url('forums', 'm=posts&q='.$row['ft_id']),
				'TAGS_RESULT_ROW_TITLE' => htmlspecialchars($row['ft_title']),
				'TAGS_RESULT_ROW_PATH' => sed_build_forums($row['fs_id'], sed_cutstring($row['fs_title'],24), sed_cutstring($row['fs_category'],16), true, $master),
				'TAGS_RESULT_ROW_TAGS' => $tag_list
			));
			$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_ROW');
		}
		sed_sql_freeresult($sql);
		$qs_u = sed_urlencode($qs, $cfg['plugin']['tags']['translit']);
		$tl = $lang != 'en' && $qs_u != $qs ? 1 : null;
		$pagnav = sed_pagination(sed_url('plug', array('e' => 'tags', 'a' => 'forums', 't' => $qs_u, 'tl' => $tl)), $d, $totalitems, $cfg['maxrowsperpage']);
		list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('plug', array('e' => 'tags', 'a' => 'forums', 't' => $qs_u, 'tl' => $tl)), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

		$t->assign(array(
			'TAGS_PAGEPREV' => $pagination_prev,
			'TAGS_PAGENEXT' => $pagination_next,
			'TAGS_PAGNAV' => $pagnav
		));
	}
	else
	{
		$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_NONE');
	}
	$t->parse('MAIN.TAGS_RESULT');
}

?>
