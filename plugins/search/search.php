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
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') && defined('SED_PLUG') or die('Wrong URL');

sed_require('page');
sed_require('forums');
sed_require('search', true);
sed_require_api('forms');

$rsq = sed_import('rsq', 'P', 'TXT', $cfg['plugin']['search']['maxsigns']);
$sq = sed_import('sq', 'G', 'TXT');
$sq = (!empty($sq)) ? $sq : $rsq;
$sq = preg_replace('/ +/', ' ', trim($sq));
$sq = sed_sql_prep($sq);
$hl = urlencode(mb_strtoupper($sq));
$tab = sed_import('tab', 'G', 'ALP');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$totalitems = array();
$pag_catauth = array();
$frm_catauth = array();

if ($d > 0 && !empty($sq))
{
	$rsearch = $_SESSION['search'];
}
else
{
	$rsearch['pag']['title'] = sed_import('rpagtitle', 'P', 'INT');
	$rsearch['pag']['desc'] = sed_import('rpagdesc', 'P', 'INT');
	$rsearch['pag']['text'] = sed_import('rpagtext', 'P', 'INT');
	$rsearch['pag']['file'] = sed_import('rpagfile', 'P', 'INT');
	$rsearch['pag']['sort'] = sed_import('rpagsort', 'P', 'TXT');
	$rsearch['pag']['sort'] = (empty($rsearch['pag']['sort'])) ? 'date' : $rsearch['pag']['sort'];
	$rsearch['pag']['sort2'] = sed_sql_prep(sed_import('rpagsort2', 'P', 'TXT'));
	$rsearch['pag']['sort2'] = (empty($rsearch['pag']['sort2'])) ? 'DESC' : $rsearch['pag']['sort2'];
	$rsearch['pag']['sub'] = sed_import('rpagsub', 'P', 'ARR');

	$rsearch['frm']['title'] = sed_import('rfrmtitle', 'P', 'INT');
	$rsearch['frm']['text'] = sed_import('rfrmtext', 'P', 'INT');
	$rsearch['frm']['reply'] = sed_import('rfrmreply', 'P', 'INT');
	$rsearch['frm']['sort'] = sed_import('sea_frmsort', 'P', 'TXT');
	$rsearch['frm']['sort'] = (empty($rsearch['frm']['sort'])) ? 'updated' : $rsearch['frm']['sort'];
	$rsearch['frm']['sort2'] = sed_sql_prep(sed_import('rfrmsort2', 'P', 'TXT'));
	$rsearch['frm']['sort2'] = (empty($rsearch['frm']['sort2'])) ? 'DESC' : $rsearch['frm']['sort2'];
	$rsearch['frm']['sub'] = sed_import('rfrmsub', 'P', 'ARR');

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
	$rsearch['set']['user'] = sed_import('rsuser', 'P', 'INT');
	$rsearch['set']['limit'] = sed_import('rwithin', 'P', 'INT');
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
			$from_year = sed_import('ryear_from', 'P', 'INT');
			$from_month = sed_import('rmonth_from', 'P', 'INT');
			$from_day = sed_import('rday_from', 'P', 'INT');
			$to_year = sed_import('ryear_to', 'P', 'INT');
			$to_month = sed_import('rmonth_to', 'P', 'INT');
			$to_day = sed_import('rday_to', 'P', 'INT');
			$rsearch['set']['from'] = mktime(0, 0, 0, $from_month, $from_day, $from_year) - $usr['timezone'] * 3600;
			$rsearch['set']['to'] = mktime(0, 0, 0, $to_month, $to_day, $to_year) - $usr['timezone'] * 3600;
		break;
		default: break;
	}
	$_SESSION['search'] = $rsearch;
}

/* === Hook === */
$extp = sed_getextplugins('search.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if (($tab == 'pag' || empty($tab))  && !$cfg['disable_page'] && $cfg['plugin']['search']['pagesearch'])
{
	// Making the category list
	$pages_cat_list['all'] = $L['plu_allcategories'];
	foreach ($sed_cat as $cat => $x)
	{
		if ($cat != 'all' && $cat != 'system' && sed_auth('page', $cat, 'R') && $x['group'] == 0)
		{
			$pages_cat_list[$cat] = $x['tpath'];
			$pag_catauth[] = sed_sql_prep($cat);
		}
	}
	if ($rsearch['pag']['sub'][0] == 'all' || !is_array($rsearch['pag']['sub']))
	{
		$rsearch['pag']['sub'] = array();
		$rsearch['pag']['sub'][] = 'all';
	}

	$t->assign(array(
		'PLUGIN_PAGE_SEC_LIST' => sed_selectbox($rsearch['pag']['sub'], 'rpagsub[]', array_keys($pages_cat_list), array_values($pages_cat_list), false, 'multiple="multiple" style="width:50%"'),
		'PLUGIN_PAGE_RES_SORT' => sed_selectbox($rsearch['pag']['sort'], 'rpagsort', array('date', 'title', 'count', 'cat'), array($L['plu_pag_res_sort1'], $L['plu_pag_res_sort2'], $L['plu_pag_res_sort3'], $L['plu_pag_res_sort4']), false),
		'PLUGIN_PAGE_RES_SORT_WAY' => sed_radiobox($rsearch['pag']['sort2'], 'rpagsort2', array("DESC", "ASC"), array($L['plu_sort_desc'],  $L['plu_sort_asc'])),
		'PLUGIN_PAGE_SEARCH_NAMES' => sed_checkbox(($rsearch['pag']['title'] == 1 || count($rsearch['pag']['sub']) == 0), 'rpagtitle', $L['plu_pag_search_names']),
		'PLUGIN_PAGE_SEARCH_DESC' => sed_checkbox(($rsearch['pag']['desc'] == 1 || count($rsearch['pag']['sub']) == 0), 'rpagdesc', $L['plu_pag_search_desc']),
		'PLUGIN_PAGE_SEARCH_TEXT' => sed_checkbox(($rsearch['pag']['text'] == 1 || count($rsearch['pag']['sub']) == 0), 'rpagtext', $L['plu_pag_search_text']),
		'PLUGIN_PAGE_SEARCH_FILE' => sed_checkbox($rsearch['pag']['file'] == 1, 'rpagfile', $L['plu_pag_search_file'])
	));
	if ($tab == 'pag' || (empty($tab) && $cfg['plugin']['search']['extrafilters']))
	{
		$t->parse('MAIN.PAGES_OPTIONS');
	}
}

if (($tab == 'frm' || empty($tab)) && !$cfg['disable_forums'] && $cfg['plugin']['search']['forumsearch'])
{
	$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s
		LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
		ORDER by fn_path ASC, fs_order ASC");

	$forum_cat_list['all'] = $L['plu_allsections'];
	while ($row1 = sed_sql_fetcharray($sql1))
	{
		if (sed_auth('forums', $row1['fs_id'], 'R'))
		{
			$forum_cat_list[$row1['fs_id']] = sed_build_forums($row1['fs_id'], $row1['fs_title'], $row1['fs_category'], FALSE);
			$frm_catauth[] = sed_sql_prep($row1['fs_id']);
		}
	}
	if ($rsearch['frm']['sub'][0] == 'all' || !is_array($rsearch['frm']['sub']))
	{
		$rsearch['frm']['sub'] = array();
		$rsearch['frm']['sub'][] = 'all';
	}

	$t->assign(array(
		'PLUGIN_FORUM_SEC_LIST' => sed_selectbox($rsearch['frm']['sub'], 'rfrmsub[]', array_keys($forum_cat_list), array_values($forum_cat_list), false, 'multiple="multiple" style="width:50%"'),
		'PLUGIN_FORUM_RES_SORT' => sed_selectbox($rsearch['frm']['sort'], 'rfrmsort', array('updated', 'creationdate', 'title', 'postcount', 'viewcount', 'sectionid'), array($L['plu_frm_res_sort1'], $L['plu_frm_res_sort2'], $L['plu_frm_res_sort3'], $L['plu_frm_res_sort4'], $L['plu_frm_res_sort5'], $L['plu_frm_res_sort6']), false),
		'PLUGIN_FORUM_RES_SORT_WAY' => sed_radiobox($rsearch['frm']['sort2'], 'rfrmsort2', array("DESC", "ASC"), array($L['plu_sort_desc'],  $L['plu_sort_asc'])),
		'PLUGIN_FORUM_SEARCH_NAMES' => sed_checkbox(($rsearch['frm']['title'] == 1 || count($rsearch['frm']['sub']) == 0), 'rfrmtitle', $L['plu_frm_search_names']),
		'PLUGIN_FORUM_SEARCH_POST' => sed_checkbox(($rsearch['frm']['text'] == 1 || count($rsearch['frm']['sub']) == 0), 'rfrmtext', $L['plu_frm_search_post']),
		'PLUGIN_FORUM_SEARCH_ANSW' => sed_checkbox(($rsearch['frm']['reply'] == 1 || count($rsearch['frm']['sub']) == 0), 'rfrmreply', $L['plu_frm_search_answ'])
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
		sed_error($L['plu_querytooshort'].$R['code_error_separator'], '');
	}
	if (count($words) > $cfg['plugin']['search']['maxwords'])
	{
		sed_error($L['plu_toomanywords'].' '.$cfg['plugin']['search']['maxwords'].$R['code_error_separator']);
	}
	// Users LIST
	$rsearch['set']['user'] = trim($rsearch['set']['user']);
	if (!empty($rsearch['set']['user']))
	{
		$touser_src = explode(",", $rsearch['set']['user']);
		foreach ($touser_src as $k => $i)
		{
			$user_name=trim(sed_import($i, 'D', 'TXT'));
			if (!empty($user_name))
			{
				$touser_sql[] = "'".sed_sql_prep($user_name)."'";
			}
		}
		$touser_sql = '('.implode(',', $touser_sql).')';
		$sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_name IN $touser_sql");
		$totalusers = sed_sql_numrows($sql);
		while ($row = sed_sql_fetcharray($sql))
		{
			$touser_ids[] = $row['user_id'];
		}
		if ($totalusers == 0)
		{
			sed_error($L['plu_usernotexist'].$R['code_error_separator'], 'rsuser');
		}
		$touser = ($totalusers > 0 && !$cot_error) ? 'IN ('.implode(',', $touser_ids).')' : '';
	}

	if (($tab == 'pag' || empty($tab)) && !$cfg['disable_page'] && $cfg['plugin']['search']['pagesearch'] && !$cot_error)
	{
		$where = ($rsearch['pag']['sub'][0] != 'all' && count($rsearch['pag']['sub']) > 0) ?
			"AND page_cat IN ('".sed_sql_prep(implode("','", $rsearch['pag']['sub']))."')" : "AND page_cat IN ('".implode("','", $pag_catauth)."')";
		$where .= ($rsearch['set']['limit'] > 0) ? " AND page_date >= ".$rsearch['set']['from']." AND page_date <= ".$rsearch['set']['to'] : "";
		$where .= ($rsearch['pag']['file'] == 1) ? " AND page_file = '1'" : "";
		$where .= (!empty($touser)) ? " page_ownerid ".$touser_ids : "";

		$pagsql = ($rsearch['pag']['title'] == 1) ? "(page_title LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		$pagsql .= (!empty($pagsql) && ($rsearch['pag']['desc'] == 1)) ? " OR " : "(";
		$pagsql .= (($rsearch['pag']['desc'] == 1)) ? "page_desc LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		$pagsql .= (!empty($pagsql) && ($rsearch['pag']['text'] == 1)) ? " OR " : "(";
		$pagsql .= (($rsearch['pag']['text'] == 1)) ? "page_text LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		// String query for addition pages fields.
		$addfields = trim($cfg['plugin']['search']['addfields']);
		if (!empty($addfields))
		{
			$addfields_sql = '';
			foreach (explode(',', $addfields) as $addfields_el)
			{
				$addfields_el = trim($addfields_el);
				$addfields_sql .= ((!empty($addfields_el))) ? " OR ".$addfields_el." LIKE '".$sqlsearch."'" : "";
			}
		}
		$pagsql .= $addfields_sql.")";

		$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS * FROM $db_pages
					WHERE $pagsql $where AND page_state = '0' AND page_cat <> 'system'
					ORDER BY page_".$rsearch['pag']['sort']." ".$rsearch['pag']['sort2']." LIMIT $d, ".$cfg['plugin']['search']['maxitems']);
		$items = sed_sql_numrows($sql);
		$totalitems[] = sed_sql_foundrows();
		$jj = 0;
		while ($row = sed_sql_fetcharray($sql))
		{
			$page_url = empty($row['page_alias']) ? sed_url('page', 'id='.$row['page_id'].'&highlight='.$hl) : sed_url('page', 'al='.$row['page_alias'].'&highlight='.$hl);
			$t->assign(array(
				'PLUGIN_PR_CATEGORY' => sed_rc_link(sed_url('list', 'c='.$row['page_cat']), $sed_cat[$row['page_cat']]['tpath']),
				'PLUGIN_PR_TITLE' => sed_rc_link($page_url, htmlspecialchars($row['page_title'])),
				'PLUGIN_PR_TEXT' => sed_clear_mark($row['page_text'], $row['page_type'], $words),
				'PLUGIN_PR_TIME' => @date($cfg['dateformat'], $row['page_date'] + $usr['timezone'] * 3600),
				'PLUGIN_PR_ODDEVEN' => sed_build_oddeven($jj),
				'PLUGIN_PR_NUM' => $jj
			));
			$t->parse("MAIN.RESULTS.PAGES.ITEM");
			$jj++;
		}
		if ($jj > 0)
		{
			$t->parse('MAIN.RESULTS.PAGES');
		}
	}
	if (($tab == 'frm' || empty($tab)) && !$cfg['disable_forums'] && $cfg['plugin']['search']['forumsearch'] && !$cot_error)
	{
		$where = ($rsearch['frm']['sub'][0] != 'all' && count($rsearch['frm']['sub'])>0) ?
			"AND s.fs_id IN ('".sed_sql_prep(implode("','", $rsearch['frm']['sub']))."')" : "AND s.fs_id IN ('".implode("','", $frm_catauth)."')";
		$where .= ($rsearch['frm']['reply'] == '1') ? " AND t.ft_postcount > 1" : "";
		$where .= ($rsearch['set']['limit'] > 0) ? " AND p.fp_creation >= ".$rsearch['set']['from']." AND p.fp_updated <= ".$rsearch['set']['to'] : "";
		$where .= (!empty($touser)) ? "AND p.fp_posterid ".$touser_ids : "";
		$s_opt = ($rsearch['frm']['title'] == 1) ? "(t.ft_title LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		$s_opt .= (!empty($s_opt) && ($rsearch['frm']['text'] == 1)) ? " OR " : "(";
		$s_opt .= (($rsearch['frm']['text'] == 1)) ? "p.fp_text LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		$s_opt .= ")";
		$maxitems = $cfg['plugin']['search']['maxitems'] - $items;
		$maxitems = ($maxitems < 0) ? 0 : $maxitems;

		$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS p.*, t.*, s.*
			 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
				WHERE $s_opt $where AND p.fp_topicid = t.ft_id AND p.fp_sectionid = s.fs_id
				GROUP BY t.ft_id ORDER BY ft_".$rsearch['frm']['sort']." ".$rsearch['frm']['sort2']."
				LIMIT $d, $maxitems");
		$items = sed_sql_numrows($sql);
		$totalitems[] = sed_sql_foundrows();
		$jj = 0;
		while ($row = sed_sql_fetcharray($sql))
		{
			if ($row['ft_updated'] > 0)
			{
				$post_url = ($cfg['plugin']['search']['searchurl'] == 'Single') ? sed_url('forums', 'm=posts&id='.$row['fp_id'].'&highlight='.$hl) : sed_url('forums', 'm=posts&p='.$row['fp_id'].'&highlight='.$hl, '#'.$row['fp_id']);
				$t->assign(array(
					'PLUGIN_FR_CATEGORY' => sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'], TRUE),
					'PLUGIN_FR_TITLE' => sed_rc_link($post_url, htmlspecialchars($row['ft_title'])),
					'PLUGIN_FR_TEXT' => sed_clear_mark($row['fp_text'], 0, $words),
					'PLUGIN_FR_TIME' => $row['ft_updated'] > 0 ? @date($cfg['dateformat'], $row['ft_updated'] + $usr['timezone'] * 3600) : @date($cfg['dateformat'], $row['fp_updated'] + $usr['timezone'] * 3600),
					'PLUGIN_FR_ODDEVEN' => sed_build_oddeven($jj),
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
	$extp = sed_getextplugins('search.list');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (array_sum($totalitems) < 1)
	{
		sed_error($L['plu_noneresult'].$R['code_error_separator']);
	}
	if (!$cot_error)
	{
		$t->parse('MAIN.RESULTS');
	}
	$pagenav = sed_pagenav('plug', array('e' => 'search', 'pre' => $sq, 'tab' => $tab), $d, array_sum($totalitems), $cfg['plugin']['search']['maxitems']);
}

// Search title
$plugin_title  = sed_rc_link(sed_url('plug', 'e=search'), $L['plu_title_all']);
if (!empty($tab))
{
	$plugin_title .= ' '.$cfg['separator'].' '. sed_rc_link(sed_url('plug', 'e=search&tab='.$tab), $L['plu_title_'.$tab.'tab']);
	$L['plu_title'] = $L['plu_title_'.$tab.'tab'];
}
$out['head'] .= $R['code_noindex'];
$out['subtitle'] = empty($sq) ? $L['plu_title'] : htmlspecialchars(strip_tags($sq)).' - '.$L['plu_result'];
$t->assign(array(
	'PLUGIN_SEARCH_ACTION' => sed_url('plug', 'e=search&tab='.$tab),
	'PLUGIN_SEARCH_TEXT' => sed_inputbox('text', 'rsq', htmlspecialchars($sq), 'size="32" maxlength="'.$cfg['plugin']['search']['maxsigns'].'"'),
	'PLUGIN_SEARCH_USER' => sed_inputbox('text', 'rsuser', htmlspecialchars($rsearch['set']['user']), 'id="rsuser" size="32"'),
	'PLUGIN_SEARCH_DATE_SELECT' => sed_selectbox($rsearch['set']['limit'], 'rwithin', range(0, 5), array($L['plu_any_date'], $L['plu_last_2_weeks'], $L['plu_last_1_month'], $L['plu_last_3_month'], $L['plu_last_1_year'], $L['plu_need_datas']), false),
	'PLUGIN_SEARCH_DATE_FROM' => sed_selectbox_date($rsearch['set']['from'], 'short', '_from', date('Y', $sys['now_offset'])+1),
	'PLUGIN_SEARCH_DATE_TO' => sed_selectbox_date($rsearch['set']['to'], 'short', '_to', date('Y', $sys['now_offset'])+1),
	'PLUGIN_SEARCH_FOUND' => (array_sum($totalitems) > 0) ?  array_sum($totalitems) : '',
	'PLUGIN_PAGEPREV' => $pagenav['prev'],
	'PLUGIN_PAGENEXT' => $pagenav['next'],
	'PLUGIN_PAGENAV' => $pagenav['main'],
	'PLUGIN_ERROR' => sed_check_messages() ? sed_implode_messages() : ''
));

/* === Hook === */
$extp = sed_getextplugins('search.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

?>