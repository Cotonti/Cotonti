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
 * @author Neocrome, Spartan, esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD License
 */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

require_once cot_incfile('page', 'module');
require_once cot_incfile('forums', 'module');
require_once cot_incfile('search', 'plug');
require_once cot_incfile('forms');

$rsq = cot_import('rsq', 'P', 'TXT', $cfg['plugin']['search']['maxsigns']);
$sq = cot_import('sq', 'G', 'TXT');
$sq = (!empty($sq)) ? $sq : $rsq;
$sq = preg_replace('/ +/', ' ', trim($sq));
$sq = $db->prep($sq);
$hl = urlencode(mb_strtoupper($sq));
$tab = cot_import('tab', 'G', 'ALP');
list($pg, $d) = cot_import_pagenav('d', $cfg['plugin']['search']['maxitems']);
$totalitems = array();
$pag_catauth = array();
$frm_catauth = array();

if ($d > 0 && !empty($sq))
{
	$rsearch = $_SESSION['search'];
}
else
{
	$rsearch['pag']['title'] = cot_import('rpagtitle', 'P', 'INT');
	$rsearch['pag']['desc'] = cot_import('rpagdesc', 'P', 'INT');
	$rsearch['pag']['text'] = cot_import('rpagtext', 'P', 'INT');
	$rsearch['pag']['file'] = cot_import('rpagfile', 'P', 'INT');
	$rsearch['pag']['sort'] = cot_import('rpagsort', 'P', 'TXT');
	$rsearch['pag']['sort'] = (empty($rsearch['pag']['sort'])) ? 'date' : $rsearch['pag']['sort'];
	$rsearch['pag']['sort2'] = $db->prep(cot_import('rpagsort2', 'P', 'TXT'));
	$rsearch['pag']['sort2'] = (empty($rsearch['pag']['sort2'])) ? 'DESC' : $rsearch['pag']['sort2'];
	$rsearch['pag']['sub'] = cot_import('rpagsub', 'P', 'ARR');

	$rsearch['frm']['title'] = cot_import('rfrmtitle', 'P', 'INT');
	$rsearch['frm']['text'] = cot_import('rfrmtext', 'P', 'INT');
	$rsearch['frm']['reply'] = cot_import('rfrmreply', 'P', 'INT');
	$rsearch['frm']['sort'] = cot_import('sea_frmsort', 'P', 'TXT');
	$rsearch['frm']['sort'] = (empty($rsearch['frm']['sort'])) ? 'updated' : $rsearch['frm']['sort'];
	$rsearch['frm']['sort2'] = $db->prep(cot_import('rfrmsort2', 'P', 'TXT'));
	$rsearch['frm']['sort2'] = (empty($rsearch['frm']['sort2'])) ? 'DESC' : $rsearch['frm']['sort2'];
	$rsearch['frm']['sub'] = cot_import('rfrmsub', 'P', 'ARR');

	if ($rsearch['pag']['title'] < 1 && $rsearch['pag']['desc'] < 1 && $rsearch['pag']['text'] < 1)
	{
		$rsearch['pag']['title'] = 1;
		$rsearch['pag']['desc'] = 1;
		$rsearch['pag']['text'] = 1;
	}
	if ($rsearch['frm']['title'] < 1 && $rsearch['frm']['text'] < 1)
	{
		$rsearch['frm']['title'] = 1;
		$rsearch['frm']['text'] = 1;
	}
	$rsearch['set']['user'] = cot_import('rsuser', 'P', 'INT');
	$rsearch['set']['limit'] = cot_import('rwithin', 'P', 'INT');
	$rsearch['set']['from'] = $sys['now_offset'] - 31536000;
	$rsearch['set']['to'] = $sys['now_offset'];
	switch($rsearch['set']['limit'])
	{
		case 1:
			$rsearch['set']['from'] = $sys['now_offset'] - 1209600;
		break;
		case 2:
			$rsearch['set']['from'] = $sys['now_offset'] - 2592000;
		break;
		case 3:
			$rsearch['set']['from'] = $sys['now_offset'] - 7776000;
		break;
		case 4:
			$rsearch['set']['from'] = $sys['now_offset'] - 31536000;
		break;
		case 5:
			$rsearch['set']['from'] = cot_import_date('rfrom');
			$rsearch['set']['to'] = cot_import_date('rto');
		break;
		default: break;
	}
	$_SESSION['search'] = $rsearch;
}

/* === Hook === */
foreach (cot_getextplugins('search.first') as $pl)
{
	include $pl;
}
/* ===== */

if (($tab == 'pag' || empty($tab))  && cot_module_active('page') && $cfg['plugin']['search']['pagesearch'])
{
	// Making the category list
	$pages_cat_list['all'] = $L['plu_allcategories'];
	foreach ($cot_cat as $cat => $x)
	{
		if ($cat != 'all' && $cat != 'system' && cot_auth('page', $cat, 'R') && $x['group'] == 0)
		{
			$pages_cat_list[$cat] = $x['tpath'];
			$pag_catauth[] = $db->prep($cat);
		}
	}
	if ($rsearch['pag']['sub'][0] == 'all' || !is_array($rsearch['pag']['sub']))
	{
		$rsearch['pag']['sub'] = array();
		$rsearch['pag']['sub'][] = 'all';
	}

	/* === Hook === */
	foreach (cot_getextplugins('search.page.catlist') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->assign(array(
		'PLUGIN_PAGE_SEC_LIST' => cot_selectbox($rsearch['pag']['sub'], 'rpagsub[]', array_keys($pages_cat_list), array_values($pages_cat_list), false, 'multiple="multiple" style="width:50%"'),
		'PLUGIN_PAGE_RES_SORT' => cot_selectbox($rsearch['pag']['sort'], 'rpagsort', array('date', 'title', 'count', 'cat'), array($L['plu_pag_res_sort1'], $L['plu_pag_res_sort2'], $L['plu_pag_res_sort3'], $L['plu_pag_res_sort4']), false),
		'PLUGIN_PAGE_RES_SORT_WAY' => cot_radiobox($rsearch['pag']['sort2'], 'rpagsort2', array('DESC', 'ASC'), array($L['plu_sort_desc'],  $L['plu_sort_asc'])),
		'PLUGIN_PAGE_SEARCH_NAMES' => cot_checkbox(($rsearch['pag']['title'] == 1 || count($rsearch['pag']['sub']) == 0), 'rpagtitle', $L['plu_pag_search_names']),
		'PLUGIN_PAGE_SEARCH_DESC' => cot_checkbox(($rsearch['pag']['desc'] == 1 || count($rsearch['pag']['sub']) == 0), 'rpagdesc', $L['plu_pag_search_desc']),
		'PLUGIN_PAGE_SEARCH_TEXT' => cot_checkbox(($rsearch['pag']['text'] == 1 || count($rsearch['pag']['sub']) == 0), 'rpagtext', $L['plu_pag_search_text']),
		'PLUGIN_PAGE_SEARCH_FILE' => cot_checkbox($rsearch['pag']['file'] == 1, 'rpagfile', $L['plu_pag_search_file'])
	));
	if ($tab == 'pag' || (empty($tab) && $cfg['plugin']['search']['extrafilters']))
	{
		$t->parse('MAIN.PAGES_OPTIONS');
	}
}

if (($tab == 'frm' || empty($tab)) && cot_module_active('forums') && $cfg['plugin']['search']['forumsearch'])
{
	$forum_cat_list['all'] = $L['plu_allsections'];
	foreach($structure['forums'] as $key => $val)
	{
		if (cot_auth('forums', $key, 'R'))
		{
			$forum_cat_list[$key] = $val['tpath'];
			$frm_catauth[] = $db->prep($key);
		}
	}

	if ($rsearch['frm']['sub'][0] == 'all' || !is_array($rsearch['frm']['sub']))
	{
		$rsearch['frm']['sub'] = array();
		$rsearch['frm']['sub'][] = 'all';
	}

	$t->assign(array(
		'PLUGIN_FORUM_SEC_LIST' => cot_selectbox($rsearch['frm']['sub'], 'rfrmsub[]', array_keys($forum_cat_list), array_values($forum_cat_list), false, 'multiple="multiple" style="width:50%"'),
		'PLUGIN_FORUM_RES_SORT' => cot_selectbox($rsearch['frm']['sort'], 'rfrmsort', array('updated', 'creationdate', 'title', 'postcount', 'viewcount', 'sectionid'), array($L['plu_frm_res_sort1'], $L['plu_frm_res_sort2'], $L['plu_frm_res_sort3'], $L['plu_frm_res_sort4'], $L['plu_frm_res_sort5'], $L['plu_frm_res_sort6']), false),
		'PLUGIN_FORUM_RES_SORT_WAY' => cot_radiobox($rsearch['frm']['sort2'], 'rfrmsort2', array('DESC', 'ASC'), array($L['plu_sort_desc'],  $L['plu_sort_asc'])),
		'PLUGIN_FORUM_SEARCH_NAMES' => cot_checkbox(($rsearch['frm']['title'] == 1 || count($rsearch['frm']['sub']) == 0), 'rfrmtitle', $L['plu_frm_search_names']),
		'PLUGIN_FORUM_SEARCH_POST' => cot_checkbox(($rsearch['frm']['text'] == 1 || count($rsearch['frm']['sub']) == 0), 'rfrmtext', $L['plu_frm_search_post']),
		'PLUGIN_FORUM_SEARCH_ANSW' => cot_checkbox(($rsearch['frm']['reply'] == 1 || count($rsearch['frm']['sub']) == 0), 'rfrmreply', $L['plu_frm_search_answ'])
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
	$rsearch['set']['user'] = trim($rsearch['set']['user']);
	if (!empty($rsearch['set']['user']))
	{
		$touser_src = explode(",", $rsearch['set']['user']);
		foreach ($touser_src as $k => $i)
		{
			$user_name=trim(cot_import($i, 'D', 'TXT'));
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
		if ($totalusers == 0)
		{
			cot_error($L['plu_usernotexist'].$R['code_error_separator'], 'rsuser');
		}
		$touser = ($totalusers > 0 && !cot_error_found()) ? 'IN ('.implode(',', $touser_ids).')' : '';
	}

	if (($tab == 'pag' || empty($tab)) && cot_module_active('page') && $cfg['plugin']['search']['pagesearch'] && !cot_error_found())
	{
		$where_and['cat'] = ($rsearch['pag']['sub'][0] != 'all' && count($rsearch['pag']['sub']) > 0) ?
			"page_cat IN ('".$db->prep(implode("','", $rsearch['pag']['sub']))."')" : "page_cat IN ('".implode("','", $pag_catauth)."')"; 
		$where_and['state'] = "page_state = '0'";
		$where_and['notcat'] = "page_cat <> 'system'";
		$where_and['date'] = "page_date <= ".(int)$sys['now_offset'];
		$where_and['date2'] = ($rsearch['set']['limit'] > 0) ? "page_date >= ".$rsearch['set']['from']." AND page_date <= ".$rsearch['set']['to'] : "";
		$where_and['file'] = ($rsearch['pag']['file'] == 1) ? "page_file = '1'" : "";
		$where_and['users'] = (!empty($touser)) ? "page_ownerid ".$touser_ids : "";

		$where_or['title'] = ($rsearch['pag']['title'] == 1) ? "page_title LIKE '".$db->prep($sqlsearch)."'" : "";
		$where_or['desc'] = (($rsearch['pag']['desc'] == 1)) ? "page_desc LIKE '".$db->prep($sqlsearch)."'" : "";
		$where_or['text'] = (($rsearch['pag']['text'] == 1)) ? "page_text LIKE '".$db->prep($sqlsearch)."'" : "";
		// String query for addition pages fields.
		foreach (explode(',', trim($cfg['plugin']['search']['addfields'])) as $addfields_el)
		{
			$addfields_el = trim($addfields_el);
			$where_or[$addfields_el] .= ((!empty($addfields_el))) ? $addfields_el." LIKE '".$sqlsearch."'" : "";
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

		$sql = $db->query("SELECT SQL_CALC_FOUND_ROWS p.* $search_join_columns
			FROM $db_pages AS p $search_join_condition
			WHERE $where
			ORDER BY page_".$rsearch['pag']['sort']." ".$rsearch['pag']['sort2']."
			LIMIT $d, ".$cfg['plugin']['search']['maxitems']
			. $search_union_query);

		$items = $sql->rowCount();
		$totalitems[] = $db->query('SELECT FOUND_ROWS()')->fetchColumn();
		$jj = 0;
		/* === Hook - Part 1 === */
		$extp = cot_getextplugins('search.page.loop');
		/* ===== */
		while ($row = $sql->fetch())
		{
			$page_url = empty($row['page_alias']) ? cot_url('page', 'id='.$row['page_id'].'&highlight='.$hl) : cot_url('page', 'al='.$row['page_alias'].'&highlight='.$hl);
			$t->assign(array(
				'PLUGIN_PR_CATEGORY' => cot_rc_link(cot_url('page', 'c='.$row['page_cat']), $cot_cat[$row['page_cat']]['tpath']),
				'PLUGIN_PR_TITLE' => cot_rc_link($page_url, htmlspecialchars($row['page_title'])),
				'PLUGIN_PR_TEXT' => cot_clear_mark($row['page_text'], $row['page_type'], $words),
				'PLUGIN_PR_TIME' => @date($cfg['dateformat'], $row['page_date'] + $usr['timezone'] * 3600),
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
		$where_and['cat'] = ($rsearch['frm']['sub'][0] != 'all' && count($rsearch['frm']['sub'])>0) ?
			"s.ft_cat IN ('".$db->prep(implode("','", $rsearch['frm']['sub']))."')" : "s.ft_cat IN ('".implode("','", $frm_catauth)."')";
		$where_and['reply'] = ($rsearch['frm']['reply'] == '1') ? "t.ft_postcount > 1" : "";
		$where_and['time'] = ($rsearch['set']['limit'] > 0) ? "p.fp_creation >= ".$rsearch['set']['from']." AND p.fp_updated <= ".$rsearch['set']['to'] : "";
		$where_and['user'] = (!empty($touser)) ? "p.fp_posterid ".$touser_ids : "";

		$where_or['title'] = ($rsearch['frm']['title'] == 1) ? "t.ft_title LIKE '".$db->prep($sqlsearch)."'" : "";
		$where_or['text'] = (($rsearch['frm']['text'] == 1)) ? "p.fp_text LIKE '".$db->prep($sqlsearch)."'" : "";

		$where_or = array_diff($where_or, array(''));
		count($where_or) || $where_or['title'] = "(t.ft_title LIKE '".$db->prep($sqlsearch)."'";
		$where_and['or'] = '('.implode(' OR ', $where_or).')';
		$where_and = array_diff($where_and, array(''));
		$where = implode(' AND ', $where_and);

		$maxitems = $cfg['plugin']['search']['maxitems'] - $items;
		$maxitems = ($maxitems < 0) ? 0 : $maxitems;

		$sql = $db->query("SELECT SQL_CALC_FOUND_ROWS p.*, t.*
			 	FROM $db_forum_posts p, $db_forum_topics t
				WHERE $where AND p.fp_topicid = t.ft_id
				GROUP BY t.ft_id ORDER BY ft_".$rsearch['frm']['sort']." ".$rsearch['frm']['sort2']."
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
					'PLUGIN_FR_CATEGORY' => cot_forums_buildpath($row['ft_cat']),
					'PLUGIN_FR_TITLE' => cot_rc_link($post_url, htmlspecialchars($row['ft_title'])),
					'PLUGIN_FR_TEXT' => cot_clear_mark($row['fp_text'], 0, $words),
					'PLUGIN_FR_TIME' => $row['ft_updated'] > 0 ? @date($cfg['dateformat'], $row['ft_updated'] + $usr['timezone'] * 3600) : @date($cfg['dateformat'], $row['fp_updated'] + $usr['timezone'] * 3600),
					'PLUGIN_FR_ODDEVEN' => cot_build_oddeven($jj),
					'PLUGIN_FR_NUM' => $jj,
				));
				$t->parse('MAIN.RESULTS.FORUMS.ITEM');
			}
			$jj++;
		}
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
	$pagenav = cot_pagenav('plug', array('e' => 'search', 'pre' => $sq, 'tab' => $tab), $d, array_sum($totalitems), $cfg['plugin']['search']['maxitems']);
}

// Search title
$plugin_title  = cot_rc_link(cot_url('plug', 'e=search'), $L['plu_title_all']);
if (!empty($tab))
{
	$plugin_title .= ' '.$cfg['separator'].' '. cot_rc_link(cot_url('plug', 'e=search&tab='.$tab), $L['plu_title_'.$tab.'tab']);
	$L['plu_title'] = $L['plu_title_'.$tab.'tab'];
}
$out['head'] .= $R['code_noindex'];
$out['subtitle'] = empty($sq) ? $L['plu_title'] : htmlspecialchars(strip_tags($sq)).' - '.$L['plu_result'];
$t->assign(array(
	'PLUGIN_SEARCH_ACTION' => cot_url('plug', 'e=search&tab='.$tab),
	'PLUGIN_SEARCH_TEXT' => cot_inputbox('text', 'rsq', htmlspecialchars($sq), 'size="32" maxlength="'.$cfg['plugin']['search']['maxsigns'].'"'),
	'PLUGIN_SEARCH_USER' => cot_inputbox('text', 'rsuser', htmlspecialchars($rsearch['set']['user']), 'class="userinput" size="32"'),
	'PLUGIN_SEARCH_DATE_SELECT' => cot_selectbox($rsearch['set']['limit'], 'rwithin', range(0, 5), array($L['plu_any_date'], $L['plu_last_2_weeks'], $L['plu_last_1_month'], $L['plu_last_3_month'], $L['plu_last_1_year'], $L['plu_need_datas']), false),
	'PLUGIN_SEARCH_DATE_FROM' => cot_selectbox_date($rsearch['set']['from'], 'short', 'rfrom', date('Y', $sys['now_offset'])+1),
	'PLUGIN_SEARCH_DATE_TO' => cot_selectbox_date($rsearch['set']['to'], 'short', 'rto', date('Y', $sys['now_offset'])+1),
	'PLUGIN_SEARCH_FOUND' => (array_sum($totalitems) > 0) ?  array_sum($totalitems) : '',
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