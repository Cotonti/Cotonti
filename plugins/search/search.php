<?php

/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Search standalone.
 *
 * @package search
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD License
 */
defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

if (cot_module_active('page'))
{
	require_once cot_incfile('page', 'module');
}
if (cot_module_active('forums'))
{
	require_once cot_incfile('forums', 'module');
}
require_once cot_incfile('search', 'plug');
require_once cot_incfile('forms');


$sq = cot_import('sq', 'R', 'TXT');

$sq = $db->prep($sq);
$hl = urlencode(mb_strtoupper($sq));
$tab = cot_import('tab', 'R', 'ALP');
$cfg_maxitems = is_numeric($cfg['plugin']['search']['maxitems']) ? abs(floor($cfg['plugin']['search']['maxitems'])) : 50;
list($pg, $d, $durl) = cot_import_pagenav('d', $cfg_maxitems);
$totalitems = array();
$pag_catauth = array();
$frm_catauth = array();
$rs = $_REQUEST['rs'];

$rs['pagtitle'] = cot_import($rs['pagtitle'], 'D', 'INT');
$rs['pagdesc'] = cot_import($rs['pagdesc'], 'D', 'INT');
$rs['pagtext'] = cot_import($rs['pagtext'], 'D', 'INT');
$rs['pagfile'] = cot_import($rs['pagfile'], 'D', 'INT');
$rs['pagsort'] = cot_import($rs['pagsort'], 'D', 'ALP');
$rs['pagsort'] = (empty($rs['pagsort'])) ? 'date' : $rs['pagsort'];
$rs['pagsort2'] = (cot_import($rs['pagsort2'], 'D', 'ALP') == 'DESC') ? 'DESC' : 'ASC';
$rs['pagsub'] = cot_import($rs['pagsub'], 'D', 'ARR');
$rs['pagsubcat'] = cot_import($rs['pagsubcat'], 'D', 'BOL') ? 1 : 0;

$rs['frmtitle'] = cot_import($rs['frmtitle'], 'D', 'INT');
$rs['frmtext'] = cot_import($rs['frmtext'], 'D', 'INT');
$rs['frmreply'] = cot_import($rs['frmreply'], 'D', 'INT');
$rs['frmsort'] = cot_import($rs['frmsort'], 'D', 'ALP');
$rs['frmsort'] = (empty($rs['frmsort'])) ? 'updated' : $rs['frmsort'];
$rs['frmsort2'] = ((cot_import($rs['frmsort2'], 'D', 'ALP')) == 'DESC') ? 'DESC' : 'ASC';;
$rs['frmsub'] = cot_import($rs['frmsub'], 'D', 'ARR');
$rs['frmsubcat'] = cot_import($rs['frmsubcat'], 'D', 'BOL') ? 1 : 0;

if ($rs['pagtitle'] < 1 && $rs['pagdesc'] < 1 && $rs['pagtext'] < 1)
{
	$rs['pagtitle'] = 1;
	$rs['pagdesc'] = 1;
	$rs['pagtext'] = 1;
}
if ($rs['frmtitle'] < 1 && $rs['frmtext'] < 1)
{
	$rs['frmtitle'] = 1;
	$rs['frmtext'] = 1;
}
$rs['setuser'] = cot_import($rs['setuser'], 'D', 'INT');
$rs['setlimit'] = cot_import($rs['setlimit'], 'D', 'INT');
$rs['setfrom'] = $sys['now'] - 31536000;
$rs['setto'] = $sys['now'];
switch ($rs['setlimit'])
{
	case 1:
		$rs['setfrom'] = $sys['now'] - 1209600;
		break;
	case 2:
		$rs['setfrom'] = $sys['now'] - 2592000;
		break;
	case 3:
		$rs['setfrom'] = $sys['now'] - 7776000;
		break;
	case 4:
		$rs['setfrom'] = $sys['now'] - 31536000;
		break;
	case 5:
		$rs['setfrom'] = cot_import_date($rs['rfrom']);
		$rs['setto'] = cot_import_date($rs['rto']);
		break;
	default: break;
}

/* === Hook === */
foreach (cot_getextplugins('search.first') as $pl)
{
	include $pl;
}
/* ===== */

if (($tab == 'pag' || empty($tab)) && cot_module_active('page') && $cfg['plugin']['search']['pagesearch'])
{
	// Making the category list
	$pages_cat_list['all'] = $L['plu_allcategories'];
	foreach ($structure['page'] as $cat => $x)
	{
		if ($cat != 'all' && $cat != 'system' && cot_auth('page', $cat, 'R') && $x['group'] == 0)
		{
			$pages_cat_list[$cat] = $x['tpath'];
			$pag_catauth[] = $db->prep($cat);
		}
	}
	if ($rs['pagsub'][0] == 'all' || !is_array($rs['pagsub']))
	{
		$rs['pagsub'] = array();
		$rs['pagsub'][] = 'all';
	}

	/* === Hook === */
	foreach (cot_getextplugins('search.page.catlist') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->assign(array(
		'PLUGIN_PAGE_SEC_LIST' => cot_selectbox($rs['pagsub'], 'rs[pagsub][]', array_keys($pages_cat_list), array_values($pages_cat_list), false, 'multiple="multiple" style="width:50%"'),
		'PLUGIN_PAGE_RES_SORT' => cot_selectbox($rs['pagsort'], 'rs[pagsort]', array('date', 'title', 'count', 'cat'), array($L['plu_pag_res_sort1'], $L['plu_pag_res_sort2'], $L['plu_pag_res_sort3'], $L['plu_pag_res_sort4']), false),
		'PLUGIN_PAGE_RES_SORT_WAY' => cot_radiobox($rs['pagsort2'], 'rs[pagsort2]', array('DESC', 'ASC'), array($L['plu_sort_desc'], $L['plu_sort_asc'])),
		'PLUGIN_PAGE_SEARCH_NAMES' => cot_checkbox(($rs['pagtitle'] == 1 || count($rs['pagsub']) == 0), 'rs[pagtitle]', $L['plu_pag_search_names']),
		'PLUGIN_PAGE_SEARCH_DESC' => cot_checkbox(($rs['pagdesc'] == 1 || count($rs['pagsub']) == 0), 'rs[pagdesc]', $L['plu_pag_search_desc']),
		'PLUGIN_PAGE_SEARCH_TEXT' => cot_checkbox(($rs['pagtext'] == 1 || count($rs['pagsub']) == 0), 'rs[pagtext]', $L['plu_pag_search_text']),
		'PLUGIN_PAGE_SEARCH_SUBCAT' => cot_checkbox($rs['pagsubcat'], 'rs[pagsubcat]', $L['plu_pag_set_subsec']),
		'PLUGIN_PAGE_SEARCH_FILE' => cot_checkbox($rs['pagfile'] == 1, 'rs[pagfile]', $L['plu_pag_search_file'])
	));
	if ($tab == 'pag' || (empty($tab) && $cfg['plugin']['search']['extrafilters']))
	{
		$t->parse('MAIN.PAGES_OPTIONS');
	}
}

if (($tab == 'frm' || empty($tab)) && cot_module_active('forums') && $cfg['plugin']['search']['forumsearch'])
{
	$forum_cat_list['all'] = $L['plu_allsections'];
	foreach ($structure['forums'] as $key => $val)
	{
		if (cot_auth('forums', $key, 'R'))
		{
			$forum_cat_list[$key] = $val['tpath'];
			$frm_catauth[] = $db->prep($key);
		}
	}

	if ($rs['frmsub'][0] == 'all' || !is_array($rs['frmsub']))
	{
		$rs['frmsub'] = array();
		$rs['frmsub'][] = 'all';
	}

	$t->assign(array(
		'PLUGIN_FORUM_SEC_LIST' => cot_selectbox($rs['frmsub'], 'rs[frmsub][]', array_keys($forum_cat_list), array_values($forum_cat_list), false, 'multiple="multiple" style="width:50%"'),
		'PLUGIN_FORUM_RES_SORT' => cot_selectbox($rs['frmsort'], 'rs[frmsort]', array('updated', 'creationdate', 'title', 'postcount', 'viewcount', 'sectionid'), array($L['plu_frm_res_sort1'], $L['plu_frm_res_sort2'], $L['plu_frm_res_sort3'], $L['plu_frm_res_sort4'], $L['plu_frm_res_sort5'], $L['plu_frm_res_sort6']), false),
		'PLUGIN_FORUM_RES_SORT_WAY' => cot_radiobox($rs['frmsort2'], 'rs[frmsort2]', array('DESC', 'ASC'), array($L['plu_sort_desc'], $L['plu_sort_asc'])),
		'PLUGIN_FORUM_SEARCH_NAMES' => cot_checkbox(($rs['frmtitle'] == 1 || count($rs['frmsub']) == 0), 'rs[frmtitle]', $L['plu_frm_search_names']),
		'PLUGIN_FORUM_SEARCH_POST' => cot_checkbox(($rs['frmtext'] == 1 || count($rs['frmsub']) == 0), 'rs[frmtext]', $L['plu_frm_search_post']),
		'PLUGIN_FORUM_SEARCH_ANSW' => cot_checkbox(($rs['frmreply'] == 1 || count($rs['frmsub']) == 0), 'rs[frmreply]', $L['plu_frm_search_answ']),
		'PLUGIN_FORUM_SEARCH_SUBCAT' => cot_checkbox($rs['frmsubcat'], 'rs[frmsubcat]', $L['plu_frm_set_subsec'])
	));
	if ($tab == 'frm' || (empty($tab) && $cfg['plugin']['search']['extrafilters']))
	{
		$t->parse('MAIN.FORUMS_OPTIONS');
	}
}

if (!empty($sq))
{
	$words = explode(' ', $sq);
	$sqlsearch = '%'.implode('%', $words).'%';
	if (mb_strlen($sq) < $cfg['plugin']['search']['minsigns'])
	{
		cot_error($L['plu_querytooshort'].$R['code_error_separator'], '');
	}
	if (count($words) > $cfg['plugin']['search']['maxwords'])
	{
		cot_error($L['plu_toomanywords'].' '.$cfg['plugin']['search']['maxwords'].$R['code_error_separator']);
	}
	// Users LIST
	$rs['setuser'] = trim($rs['setuser']);
	if (!empty($rs['setuser']))
	{
		$touser_src = explode(",", $rs['setuser']);
		foreach ($touser_src as $k => $i)
		{
			$user_name = trim(cot_import($i, 'D', 'TXT'));
			if (!empty($user_name))
			{
				$touser_sql[] = "'".$db->prep($user_name)."'";
			}
		}
		$touser_sql = '('.implode(',', $touser_sql).')';
		$sql = $db->query("SELECT user_id, user_name FROM $db_users WHERE user_name IN $touser_sql");
		$totalusers = $sql->rowCount();
		while ($row = $sql->fetch())
		{
			$touser_ids[] = $row['user_id'];
		}
		$sql->closeCursor();
		if ($totalusers == 0)
		{
			cot_error($L['plu_usernotexist'].$R['code_error_separator'], 'rs[setuser]');
		}
		$touser = ($totalusers > 0 && !cot_error_found()) ? 'IN ('.implode(',', $touser_ids).')' : '';
	}

	if (($tab == 'pag' || empty($tab)) && cot_module_active('page') && $cfg['plugin']['search']['pagesearch'] && !cot_error_found())
	{
		if ($rs['pagsub'][0] != 'all' && count($rs['pagsub']) > 0)
		{
			if ($rs['pagsubcat'])
			{
				$tempcat = array();
				foreach ($rs['pagsub'] as $scat)
				{
					$tempcat = array_merge(cot_structure_children('page', $scat), $tempcat);
				}
				$tempcat = array_unique($tempcat);
				$where_and['cat'] = "page_cat IN ('".implode("','", $tempcat)."')";
			}
			else
			{
				$tempcat = array();
				foreach ($rs['pagsub'] as $scat)
				{
					$tempcat[] = $db->prep($scat);
				}
				$where_and['cat'] = "page_cat IN ('".implode("','", $tempcat)."')";
			}
		}
		else
		{
			$where_and['cat'] = "page_cat IN ('".implode("','", $pag_catauth)."')";
		}
		$where_and['state'] = "page_state = '0'";
		$where_and['notcat'] = "page_cat <> 'system'";
		$where_and['date'] = "page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']})";
		$where_and['date2'] = ($rs['setlimit'] > 0) ? "page_date >= ".$rs['setfrom']." AND page_date <= ".$rs['setto'] : "";
		$where_and['file'] = ($rs['pagfile'] == 1) ? "page_file = '1'" : "";
		$where_and['users'] = (!empty($touser)) ? "page_ownerid ".$touser_ids : "";

		$where_or['title'] = ($rs['pagtitle'] == 1) ? "page_title LIKE '".$db->prep($sqlsearch)."'" : "";
		$where_or['desc'] = (($rs['pagdesc'] == 1)) ? "page_desc LIKE '".$db->prep($sqlsearch)."'" : "";
		$where_or['text'] = (($rs['pagtext'] == 1)) ? "page_text LIKE '".$db->prep($sqlsearch)."'" : "";
		// String query for addition pages fields.
		foreach (explode(',', trim($cfg['plugin']['search']['addfields'])) as $addfields_el)
		{
			$addfields_el = trim($addfields_el);
			$where_or[$addfields_el] .= ( (!empty($addfields_el))) ? $addfields_el." LIKE '".$sqlsearch."'" : "";
		}
		$where_or = array_diff($where_or, array(''));
		count($where_or) || $where_or['title'] = "page_title LIKE '".$db->prep($sqlsearch)."'";
		$where_and['or'] = '('.implode(' OR ', $where_or).')';
		$where_and = array_diff($where_and, array(''));
		$where = implode(' AND ', $where_and);

		/* === Hook === */
		foreach (cot_getextplugins('search.page.query') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if (!$db->fieldExists($db_pages, 'page_'.$rs['pagsort']))
		{
			$rs['pagsort'] = 'date';
		}

		$sql = $db->query("SELECT SQL_CALC_FOUND_ROWS p.* $search_join_columns
			FROM $db_pages AS p $search_join_condition
			WHERE $where
			ORDER BY page_".$rs['pagsort']." ".$rs['pagsort2']."
			LIMIT $d, ".$cfg_maxitems
				.$search_union_query);

		$items = $sql->rowCount();
		$totalitems[] = $db->query('SELECT FOUND_ROWS()')->fetchColumn();
		$jj = 0;
		/* === Hook - Part 1 === */
		$extp = cot_getextplugins('search.page.loop');
		/* ===== */
		foreach ($sql->fetchAll() as $row)
		{
			$url_cat = cot_url('page', 'c='.$row['page_cat']);
			$url_page = empty($row['page_alias']) ? cot_url('page', 'c='.$row['page_cat'].'&id='.$row['page_id'].'&highlight='.$hl) : cot_url('page', 'c='.$row['page_cat'].'&al='.$row['page_alias'].'&highlight='.$hl);
			$t->assign(cot_generate_pagetags($row, 'PLUGIN_PR_'));
			$t->assign(array(
				'PLUGIN_PR_CATEGORY' => cot_rc_link($url_cat, $structure['page'][$row['page_cat']]['tpath']),
				'PLUGIN_PR_CATEGORY_URL' => $url_cat,
				'PLUGIN_PR_TITLE' => cot_rc_link($url_page, htmlspecialchars($row['page_title'])),
				'PLUGIN_PR_TEXT' => cot_clear_mark($row['page_text'], $row['page_type'], $words),
				'PLUGIN_PR_TIME' => cot_date('datetime_medium', $row['page_date']),
				'PLUGIN_PR_TIMESTAMP' => $row['page_date'],
				'PLUGIN_PR_ODDEVEN' => cot_build_oddeven($jj),
				'PLUGIN_PR_NUM' => $jj
			));
			/* === Hook - Part 2 === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */
			$t->parse('MAIN.RESULTS.PAGES.ITEM');
			$jj++;
		}
		if ($jj > 0)
		{
			$t->parse('MAIN.RESULTS.PAGES');
		}
		unset($where_and, $where_or, $where);
	}
	if (($tab == 'frm' || empty($tab)) && cot_module_active('forums') && $cfg['plugin']['search']['forumsearch'] && !cot_error_found())
	{
		if ($rs['frmsub'][0] != 'all' && count($rs['frmsub']) > 0)
		{
			if ($rs['frmsubcat'])
			{
				$tempcat = array();
				foreach ($rs['frmsub'] as $scat)
				{
					$tempcat = array_merge(cot_structure_children('forums', $scat), $tempcat);
				}
				$tempcat = array_unique($tempcat);
				$where_and['cat'] = "page_cat IN ('".implode("','", $tempcat)."')";
			}
			else
			{
				$tempcat = array();
				foreach ($rs['frmsub'] as $scat)
				{
					$tempcat[] = $db->prep($scat);
				}
				$where_and['cat'] = "t.ft_cat IN ('".implode("','", $tempcat)."')";
			}
		}
		else
		{
			$where_and['cat'] = "t.ft_cat IN ('".implode("','", $frm_catauth)."')";
		}
		$where_and['reply'] = ($rs['frmreply'] == '1') ? "t.ft_postcount > 1" : "";
		$where_and['time'] = ($rs['setlimit'] > 0) ? "p.fp_creation >= ".$rs['setfrom']." AND p.fp_updated <= ".$rs['setto'] : "";
		$where_and['user'] = (!empty($touser)) ? "p.fp_posterid ".$touser_ids : "";

		$where_or['title'] = ($rs['frmtitle'] == 1) ? "t.ft_title LIKE '".$db->prep($sqlsearch)."'" : "";
		$where_or['text'] = (($rs['frmtext'] == 1)) ? "p.fp_text LIKE '".$db->prep($sqlsearch)."'" : "";

		$where_or = array_diff($where_or, array(''));
		count($where_or) || $where_or['title'] = "(t.ft_title LIKE '".$db->prep($sqlsearch)."'";
		$where_and['or'] = '('.implode(' OR ', $where_or).')';
		$where_and = array_diff($where_and, array(''));
		$where = implode(' AND ', $where_and);

		$maxitems = $cfg_maxitems - $items;
		$maxitems = ($maxitems < 0) ? 0 : $maxitems;

		if (!$db->fieldExists($db_forum_topics, "ft_{$rs['frmsort']}"))
		{
			$rs['frmsort'] = 'updated';
		}

		$sql = $db->query("SELECT SQL_CALC_FOUND_ROWS p.*, t.*
			 	FROM $db_forum_posts AS p, $db_forum_topics AS t
				WHERE $where AND p.fp_topicid = t.ft_id
				GROUP BY t.ft_id ORDER BY ft_".$rs['frmsort']." ".$rs['frmsort2']."
				LIMIT $d, $maxitems");
		$items = $sql->rowCount();
		$totalitems[] = $db->query('SELECT FOUND_ROWS()')->fetchColumn();
		$jj = 0;
		while ($row = $sql->fetch())
		{
			if ($row['ft_updated'] > 0)
			{
				$post_url = ($cfg['plugin']['search']['searchurl'] == 'Single') ? cot_url('forums', 'm=posts&id='.$row['fp_id'].'&highlight='.$hl) : cot_url('forums', 'm=posts&p='.$row['fp_id'].'&highlight='.$hl, '#'.$row['fp_id']);
				$t->assign(array(
					'PLUGIN_FR_CATEGORY' => cot_breadcrumbs(cot_forums_buildpath($row['ft_cat']), false),
					'PLUGIN_FR_TITLE' => cot_rc_link($post_url, htmlspecialchars($row['ft_title'])),
					'PLUGIN_FR_TITLE_URL' => $post_url,
					'PLUGIN_FR_TEXT' => cot_clear_mark($row['fp_text'], 0, $words),
					'PLUGIN_FR_TIME' => $row['ft_updated'] > 0 ? cot_date('datetime_medium', $row['ft_updated']) : cot_date('datetime_medium', $row['fp_updated']),
					'PLUGIN_FR_TIMESTAMP' => $row['ft_updated'] > 0 ? $row['ft_updated'] : $row['fp_updated'],
					'PLUGIN_FR_ODDEVEN' => cot_build_oddeven($jj),
					'PLUGIN_FR_NUM' => $jj,
				));
				$t->parse('MAIN.RESULTS.FORUMS.ITEM');
			}
			$jj++;
		}
		$sql->closeCursor();
		if ($jj > 0)
		{
			$t->parse('MAIN.RESULTS.FORUMS');
		}
	}

	/* === Hook === */
	foreach (cot_getextplugins('search.list') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (array_sum($totalitems) < 1)
	{
		cot_error($L['plu_noneresult'].$R['code_error_separator']);
	}
	if (!cot_error_found())
	{
		$t->parse('MAIN.RESULTS');
	}

	$rs_url_path = array();
	foreach ($rs as $k => $v)
	{
		if (is_array($v))
		{
			foreach ($v as $sk => $sv)
			{
				$rs_url_path['rs[' . $k . '][' . $sk . ']'] = $sv;
			}
		}
		else
		{
			$rs_url_path['rs[' . $k . ']'] = $v;
		}
	}
	$pagenav = cot_pagenav('plug', array('e' => 'search', 'sq' => $sq, 'tab' => $tab)+$rs_url_path, $d, array_sum($totalitems), $cfg_maxitems);
}

// Search title
$crumbs = array(array(cot_url('plug', 'e=search'), $L['plu_search']));
if (!empty($tab))
{
	$crumbs[] = array(cot_url('plug', 'e=search&tab='.$tab), $L['plu_tabs_'.$tab]);
}
$out['head'] .= $R['code_noindex'];
$search_subtitle = empty($tab) ? $L['plu_search'] : $L['plu_tabs_'.$tab].' - '.$L['plu_search'];
$out['subtitle'] = empty($sq) ? $search_subtitle : htmlspecialchars(strip_tags($sq)).' - '.$L['plu_result'];
$t->assign(array(
	'PLUGIN_TITLE' => cot_breadcrumbs($crumbs, $cfg['breadcrumb'], true),
	'PLUGIN_SEARCH_ACTION' => cot_url('plug', 'e=search&tab='.$tab),
	'PLUGIN_SEARCH_TEXT' => cot_inputbox('text', 'sq', htmlspecialchars($sq), 'size="32" maxlength="'.$cfg['plugin']['search']['maxsigns'].'"'),
	'PLUGIN_SEARCH_USER' => cot_inputbox('text', 'rs[setuser]', htmlspecialchars($rs['setuser']), 'class="userinput" size="32"'),
	'PLUGIN_SEARCH_DATE_SELECT' => cot_selectbox($rs['setlimit'], 'rs[setlimit]', range(0, 5), array($L['plu_any_date'], $L['plu_last_2_weeks'], $L['plu_last_1_month'], $L['plu_last_3_month'], $L['plu_last_1_year'], $L['plu_need_datas']), false),
	'PLUGIN_SEARCH_DATE_FROM' => cot_selectbox_date($rs['setfrom'], 'short', 'rs[rfrom]', cot_date('Y', $sys['now']) + 1),
	'PLUGIN_SEARCH_DATE_TO' => cot_selectbox_date($rs['setto'], 'short', 'rs[rto]', cot_date('Y', $sys['now']) + 1),
	'PLUGIN_SEARCH_FOUND' => (array_sum($totalitems) > 0) ? array_sum($totalitems) : '',
	'PLUGIN_PAGEPREV' => $pagenav['prev'],
	'PLUGIN_PAGENEXT' => $pagenav['next'],
	'PLUGIN_PAGENAV' => $pagenav['main']
));

cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('search.tags') as $pl)
{
	include $pl;
}
/* ===== */
?>