<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.
[BEGIN_SED]
File=plugins/tags/tags.php
Version=0.0.2
Updated=2008-dec-19
Type=Plugin
Author=Trustmaster
Description=Tag search
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=search
File=tags
Hooks=standalone
Tags=
Order=
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

$qs = sed_import('t', 'G', 'TXT');
if(empty($qs)) $qs = sed_import('t', 'P', 'TXT');

$tl = sed_import('tl', 'G', 'BOL');
if($tl) $qs = strtr($qs, $sed_translitb);

if($a == 'pages')
{
	$t->assign(array(
	'TAGS_ACTION' => sed_url('plug', 'e=tags&a=pages'),
	'TAGS_HINT' => $L['tags_Query_hint'],
	'TAGS_QUERY' => sed_cc($qs)
	));
	if(empty($qs))
	{
		// Global tag cloud and search form
		$tcloud = sed_tag_cloud('pages', $cfg['plugin']['tags']['order']);
		$tc_html = '<ul class="tag_cloud">';
		foreach($tcloud as $tag => $cnt)
		{
			$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
			$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			$tc_html .= '<li value="'.$cnt.'"><a href="'.sed_url('plug', 'e=tags&a=pages&t='.$tag_u.$tl).'">'.sed_cc($tag_t).'</a> </li>';
		}
		$tc_html .= '</ul><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/jquery.tagcloud.js"></script><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/set.js"></script>';
		$t->assign('TAGS_CLOUD_BODY', $tc_html);
		$t->parse('MAIN.TAGS_CLOUD');
	}
	else
	{
		// Search results
		$query = sed_tag_parse_query($qs);
		$d = sed_import('d', 'G', 'INT');
		if(empty($d))
		{
			$d = 0;
		}
		if(!empty($query))
		{
			$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*)
			FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
			ON r.tag_item = p.page_id
			WHERE r.tag_area = 'pages' AND ($query)"), 0, 0);
			$sql = sed_sql_query("SELECT p.page_id, p.page_alias, p.page_title, p.page_cat
			FROM $db_tag_references AS r LEFT JOIN $db_pages AS p
			ON r.tag_item = p.page_id
			WHERE r.tag_area = 'pages' AND ($query)
			LIMIT $d, {$cfg['maxrowsperpage']}");
			$t->assign('TAGS_RESULT_TITLE', $L['Search_results']);
			while($row = sed_sql_fetchassoc($sql))
			{
				$tags = sed_tag_list($row['page_id']);
				$tag_list = '';
				foreach($tags as $tag)
				{
					$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
					$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
					$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
					$tag_list .= '<a href="'.sed_url('plug', 'e=tags&a=pages&t='.$tag_u.$tl).'">'.sed_cc($tag_t).'</a> ';
				}
				$t->assign(array(
				'TAGS_RESULT_ROW_URL' => empty($row['page_alias']) ? sed_url('page', 'id='.$row['page_id']) : sed_url('page', 'al='.$row['page_alias']),
				'TAGS_RESULT_ROW_TITLE' => sed_cc($row['page_title']),
				'TAGS_RESULT_ROW_PATH' => sed_build_catpath($row['page_cat'], '<a href="'.sed_url('list', 'c=%1$s').'">%2$s</a>'),
				'TAGS_RESULT_ROW_TAGS' => $tag_list
				));
				$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_ROW');
			}
			sed_sql_freeresult($sql);
			$pagnav = sed_pagination(sed_url('plug','e=tags&a=pages&t='.urlencode($qs)), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('plug','e=tags&a=pages&t='.urlencode($qs)), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

			$t->assign(array(
			'TAGS_PAGEPREV' => $pagination_prev,
			'TAGS_PAGENEXT' => $pagination_next,
			'TAGS_PAGNAV' => $pagnav
			));
			$t->parse('MAIN.TAGS_RESULT');
		}
	}
}
elseif($a == 'forums')
{
	$t->assign(array(
	'TAGS_ACTION' => sed_url('plug', 'e=tags&a=forums'),
	'TAGS_HINT' => $L['Query_hint'],
	'TAGS_QUERY' => sed_cc($qs)
	));
	if(empty($qs))
	{
		// Global tag cloud and search form
		$tcloud = sed_tag_cloud('forums', $cfg['plugin']['tags']['order']);
		$tc_html = '<ul class="tag_cloud">';
		foreach($tcloud as $tag => $cnt)
		{
			$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
			$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
			$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
			$tc_html .= '<li value="'.$cnt.'"><a href="'.sed_url('plug', 'e=tags&a=forums&t='.$tag_u.$tl).'">'.sed_cc($tag_t).'</a> </li>';
		}
		$tc_html .= '</ul><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/jquery.tagcloud.js"></script><script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/set.js"></script>';
		$t->assign('TAGS_CLOUD_BODY', $tc_html);
		$t->parse('MAIN.TAGS_CLOUD');
	}
	else
	{
		// Search results
		$query = sed_tag_parse_query($qs);
		$d = sed_import('d', 'G', 'INT');
		if(empty($d))
		{
			$d = 0;
		}
		if(!empty($query))
		{
			$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*)
			FROM $db_tag_references AS r LEFT JOIN $db_forum_topics AS t
			ON r.tag_item = t.ft_id
			WHERE r.tag_area = 'forums' AND ($query)"), 0, 0);
			$sql = sed_sql_query("SELECT t.ft_id, t.ft_sectionid, t.ft_title, s.fs_id, s.fs_masterid, s.fs_mastername, s.fs_title, s.fs_category
			FROM $db_tag_references AS r LEFT JOIN $db_forum_topics AS t
			ON r.tag_item = t.ft_id
			LEFT JOIN $db_forum_sections AS s
			ON t.ft_sectionid = s.fs_id
			WHERE r.tag_area = 'forums' AND ($query)
			LIMIT $d, {$cfg['maxrowsperpage']}");
			$t->assign('TAGS_RESULT_TITLE', $L['Search_results']);
			while($row = sed_sql_fetchassoc($sql))
			{
				$tags = sed_tag_list($row['ft_id'], 'forums');
				$tag_list = '';
				foreach($tags as $tag)
				{
					$tag_t = $cfg['plugin']['tags']['title'] ? sed_tag_title($tag) : $tag;
					$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
					$tl = $lang != 'en' && $tag_u != urlencode($tag) ? '&tl=1' : '';
					$tag_list .= '<a href="'.sed_url('plug', 'e=tags&a=forums&t='.$tag_u.$tl).'">'.sed_cc($tag_t).'</a> ';
				}
				$t->assign(array(
				'TAGS_RESULT_ROW_URL' => sed_url('forums', 'm=topics&q='.$row['ft_id']),
				'TAGS_RESULT_ROW_TITLE' => sed_cc($row['ft_title']),
				'TAGS_RESULT_ROW_PATH' => sed_build_forums($row['fs_id'], sed_cutstring($row['fs_title'],24), sed_cutstring($row['fs_category'],16), true, array($row['fs_masterid'],$row['fs_mastername'])),
				'TAGS_RESULT_ROW_TAGS' => $tag_list
				));
				$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_ROW');
			}
			sed_sql_freeresult($sql);
			$pagnav = sed_pagination(sed_url('plug','e=tags&a=forums&t='.urlencode($qs)), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('plug','e=tags&a=forums&t='.urlencode($qs)), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

			$t->assign(array(
			'TAGS_PAGEPREV' => $pagination_prev,
			'TAGS_PAGENEXT' => $pagination_next,
			'TAGS_PAGNAV' => $pagnav
			));
			$t->parse('MAIN.TAGS_RESULT');
		}
	}
}
else
{
	/* == Hook for the plugins == */
	$extp = sed_getextplugins('tags.search.custom');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */
}

/**
 * Parses search string into SQL query
 *
 * @param string $qs User input
 * @return string
 */
function sed_tag_parse_query($qs)
{
	$tokens1 = explode(';', $qs);
	$cnt1 = count($tokens1);
	for($i = 0; $i < $cnt1; $i++)
	{
		$tokens2 = explode(',', $tokens1[$i]);
		$cnt2 = count($tokens2);
		for($j = 0; $j < $cnt2; $j++)
		{
			$tag = sed_tag_prep($tokens2[$j]);
			if(!empty($tag))
			{
				if(mb_strstr($tag, '*'))
				{
					$tag = str_replace('*', '%', $tag);
					$tokens2[$j] = "r.tag LIKE '$tag'";
				}
				else
				{
					$tokens2[$j] = "r.tag = '$tag'";
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
?>