<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=search
Part=main
File=search
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Search standalone.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Spartan, Boss, esclkm, Cotonti Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
 */
defined('SED_CODE') && defined('SED_PLUG') or die('Wrong URL');

require_once sed_incfile('functions', 'page');
require_once sed_incfile('functions', 'forums');
require_once("plugins/search/inc/search.functions.php");

$rsq = sed_import('rsq','P','TXT',$cfg['plugin']['search']['maxsigns']);
$sq = sed_import('sq','G','TXT');
$sq = (!empty($sq)) ? $sq : $rsq;
$sq = preg_replace('/ +/', ' ', trim($sq));
$sq = sed_sql_prep($sq);
$hl = urlencode(mb_strtoupper($sq));
$tab = sed_import('tab','G','ALP');
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
	$rsearch['pag']['title'] = sed_import('rpagtitle','P','INT');
	$rsearch['pag']['desc'] = sed_import('rpagdesc','P','INT');
	$rsearch['pag']['text'] = sed_import('rpagtext','P','INT');
	$rsearch['pag']['file'] = sed_import('rpagfile','P','INT');
	$rsearch['pag']['sort'] = sed_import('rpagsort','P','INT');
	$rsearch['pag']['sort2'] = sed_sql_prep(sed_import('rpagsort2','P','TXT'));
	$rsearch['pag']['sub'] = sed_import('rpagsub','P','ARR');

	$rsearch['frm']['title'] = sed_import('rfrmtitle','P','INT');
	$rsearch['frm']['text'] = sed_import('rfrmtext','P','INT');
	$rsearch['frm']['reply'] = sed_import('rfrmreply','P','INT');
	$rsearch['frm']['sort'] = sed_import('sea_frmsort','P','INT');
	$rsearch['frm']['sort2'] = sed_sql_prep(sed_import('rfrmsort2','P','TXT'));
	$rsearch['frm']['sub'] = sed_import('rfrmsub','P','ARR');

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

	$rsearch['time']['limit'] = sed_import('rwithin','P','INT');
	$rsearch['time']['from'] = $sys['now_offset'];
	$rsearch['time']['to'] = $sys['now_offset'];
	switch($rsearch['time']['limit'])
	{
		case 1: $rsearch['time']['from'] = $sys['now_offset'] - 1209600;
			break;
		case 2: $rsearch['time']['from'] = $sys['now_offset'] - 2592000;
			break;
		case 3: $rsearch['time']['from'] = $sys['now_offset'] - 7776000;
			break;
		case 4: $rsearch['time']['from'] = $sys['now_offset'] - 31536000;
			break;
		case 5: $from_year = sed_import('ryear_from', 'P', 'INT');
			$from_month = sed_import('rmonth_from', 'P', 'INT');
			$from_day = sed_import('rday_from', 'P', 'INT');
			$to_year = sed_import('ryear_to', 'P', 'INT');
			$to_month = sed_import('rmonth_to', 'P', 'INT');
			$to_day = sed_import('rday_to', 'P', 'INT');
			$rsearch['time']['from'] = mktime(0,0,0,$from_month,$from_day,$from_year) - $usr['timezone'] * 3600;
			$rsearch['time']['to'] = mktime(0,0,0,$to_month,$to_day,$to_year) - $usr['timezone'] * 3600;
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
	$plugin_page_sec_list  = '<select multiple name="rpagsub[]" size="10" style="width:385px">';
	$plugin_page_sec_list .= '<option value="all"'.(($rsearch['pag']['sub'][0]=='all' || !is_array($rsearch['pag']['sub'])) ? ' selected="selected"':'').'>'.$L['plu_allcategories'].'</option>';
	foreach ($sed_cat as $cat => $x)
	{
		if($cat != 'all' && $cat != 'system' && sed_auth('page', $cat, 'R') && $x['group'] == 0)
		{
			$plugin_page_sec_list .= '<option value="'.$i.'"';
			if (count($rsearch['pag']['sub']) > 0)
			{
				$plugin_page_sec_list .= (in_array($cat, $rsearch['pag']['sub']) && $rsearch['pag']['sub'][0] != 'all') ? ' selected="selected"' : '';
			}
			$plugin_page_sec_list .= '>'.$x['tpath'].'</option>';
			$pag_catauth[] = sed_sql_prep($cat);
		}
	}
	$plugin_page_sec_list .= '</select>';

	// Result ordering list
	$plugin_page_res_sort  = '<select style="width:160px" name="rpagsort">';
	$plugin_page_res_sort .= '<option value="1"'.(($rsearch['pag']['sort'] == 1 || !isset($rsearch['pag']['sort'])) ? ' selected="selected"' : '').'>'.$L['plu_pag_res_sort1'].'</option>';
	$plugin_page_res_sort .= '<option value="2"'.(($rsearch['pag']['sort'] == 2) ? ' selected="selected"' : '').'>'.$L['plu_pag_res_sort2'].'</option>';
	$plugin_page_res_sort .= '<option value="3"'.(($rsearch['pag']['sort'] == 3) ? ' selected="selected"' : '').'>'.$L['plu_pag_res_sort3'].'</option>';
	$plugin_page_res_sort .= '</select>';

	$t->assign(array(
		'PLUGIN_PAGE_SEC_LIST' => $plugin_page_sec_list,
		'PLUGIN_PAGE_RES_SORT' => $plugin_page_res_sort,
		'PLUGIN_PAGE_RES_DESC' => '<input type="radio" name="rpagsort2" value="DESC" '.(($rsearch['pag']['sort2'] != 'ASC') ? ' checked="checked"' : '').' />',
		'PLUGIN_PAGE_RES_ASC' => '<input type="radio" name="rpagsort2" value="ASC" '.(($rsearch['pag']['sort2'] == 'ASC') ? ' checked="checked"' : '').' />',
		'PLUGIN_PAGE_SEARCH_NAMES' => '<input type="checkbox" name="rpagtitle" '.(($rsearch['pag']['title'] == 1 || count($rsearch['pag']['sub']) == 0) ? ' checked="checked"' : '').' value="1" />',
		'PLUGIN_PAGE_SEARCH_DESC' => '<input type="checkbox" name="rpagdesc" '.(($rsearch['pag']['desc'] == 1 || count($rsearch['pag']['sub']) == 0) ? ' checked="checked"' : '').' value="1" />',
		'PLUGIN_PAGE_SEARCH_TEXT' => '<input type="checkbox" name="rpagtext" '.(($rsearch['pag']['text'] == 1 || count($rsearch['pag']['sub']) == 0) ? ' checked="checked"' : '').' value="1" />',
		'PLUGIN_PAGE_SEARCH_FILE' => '<input type="checkbox" name="rpagfile" '.(($rsearch['pag']['file'] == 1) ? ' checked="checked"' : '').' value="1" />'
	));
	$t->parse('MAIN.PAGES_OPTIONS');
}

if (($tab == 'frm' || empty($tab)) && !$cfg['disable_forums'] && $cfg['plugin']['search']['forumsearch'])
{
	$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s
		LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
		ORDER by fn_path ASC, fs_order ASC");

	$plugin_forum_sec_list  = '<select multiple name="rfrmsub[]" size="10" style="width:385px">';
	$plugin_forum_sec_list .= '<option value="all"'.(($rsearch['frm']['sub'][0] == 'all' || count($rsearch['frm']['sub']) == 0) ? ' selected="selected"':'').'>'.$L['plu_allsections'].'</option>';
	while($row1 = mysql_fetch_array($sql1))
	{
		if(sed_auth('forums', $row1['fs_id'], 'R'))
		{
			$plugin_forum_sec_list .= '<option value="'.$row1['fs_id'].'"';
			if(count($rsearch['frm']['sub']) > 0)
			{
				$plugin_forum_sec_list .= (in_array($row1['fs_id'], $rsearch['frm']['sub']) && $rsearch['frm']['sub'][0] != 'all') ? ' selected="selected"' : '';
			}
			$plugin_forum_sec_list .= '>'.sed_build_forums($row1['fs_id'], $row1['fs_title'], $row1['fs_category'], FALSE).'</option>';
			$frm_catauth[] = sed_sql_prep($row1['fs_id']);
		}
	}
	$plugin_forum_sec_list .= '</select>';

	// Making the list for ordering
	$plugin_forum_res_sort  = '<select style="width:160px" name="rfrmsort">';
	$plugin_forum_res_sort .= '<option value="1"'.(($rsearch['frm']['sort'] == 1 || !isset($rsearch['frm']['sort'])) ? ' selected="selected"' : '').'>'.$L['plu_frm_res_sort1'].'</option>';
	$plugin_forum_res_sort .= '<option value="2"'.(($rsearch['frm']['sort'] == 2) ? ' selected="selected"' : '').'>'.$L['plu_frm_res_sort2'].'</option>';
	$plugin_forum_res_sort .= '<option value="3"'.(($rsearch['frm']['sort'] == 3) ? ' selected="selected"' : '').'>'.$L['plu_frm_res_sort3'].'</option>';
	$plugin_forum_res_sort .= '<option value="4"'.(($rsearch['frm']['sort'] == 4) ? ' selected="selected"' : '').'>'.$L['plu_frm_res_sort4'].'</option>';
	$plugin_forum_res_sort .= '<option value="5"'.(($rsearch['frm']['sort'] == 5) ? ' selected="selected"' : '').'>'.$L['plu_frm_res_sort5'].'</option>';
	$plugin_forum_res_sort .= '</select>';

	$t->assign(array(
		'PLUGIN_FORUM_SEC_LIST' => $plugin_forum_sec_list,
		'PLUGIN_FORUM_RES_SORT' => $plugin_forum_res_sort,
		'PLUGIN_FORUM_RES_DESC' => '<input type="radio" name="rfrmsort2" value="DESC" '.(($rsearch['frm']['sort2'] == 'ASC') ? '' : ' checked="checked"').' />',
		'PLUGIN_FORUM_RES_ASC' => '<input type="radio" name="rfrmsort2" value="ASC" '.(($rsearch['frm']['sort2'] == 'ASC') ? ' checked="checked"' : '').' />',
		'PLUGIN_FORUM_SEARCH_NAMES' => '<input type="checkbox" name="rfrmtitle" '.(($rsearch['frm']['title'] == 1 || count($rsearch['frm']['sub']) == 0) ? ' checked="checked"' : '').' value="1" />',
		'PLUGIN_FORUM_SEARCH_POST' => '<input type="checkbox" name="rfrmtext" '.(($rsearch['frm']['text'] == 1 || count($rsearch['frm']['sub']) == 0) ? ' checked="checked"' : '').' value="1" />',
		'PLUGIN_FORUM_SEARCH_ANSW' => '<input type="checkbox" name="rfrmreply" '.(($rsearch['frm']['reply'] == 1) ? ' checked="checked"' : '').' value="1" />'
	));
	$t->parse('MAIN.FORUMS_OPTIONS');
}

if (!empty($sq))
{
	$error_string .= (mb_strlen($sq) < $cfg['plugin']['search']['minsigns']) ? $L['plu_querytooshort'].'<br />' : '';
	// Count query words
	$words = explode(' ', $sq);
	$error_string .= (count($words) > $cfg['plugin']['search']['maxwords']) ? $L['plu_toomanywords'].' '.$cfg['plugin']['search']['maxwords'].'<br />' : '';

	$sqlsearch = implode('%', $words);
	$sqlsearch = '%'.$sqlsearch.'%';
	if (($tab == 'pag' || empty($tab)) && !$cfg['disable_page'] && $cfg['plugin']['search']['pagesearch'] && empty($error_string))
	{
		$where = ($rsearch['pag']['sub'][0] != 'all' && count($rsearch['pag']['sub']) > 0) ?
			"AND page_cat IN ('".sed_sql_prep(implode("','", $rsearch['pag']['sub']))."')" : "AND page_cat IN ('".implode("','", $pag_catauth)."')";
		$where .= ($rsearch['time']['limit'] > 0) ? " AND page_date >= ".$rsearch['time']['from']." AND page_date <= ".$rsearch['time']['to'] : "";
		$where .= ($rsearch['pag']['file'] == 1) ? " AND page_file = '1'" : "";

		$pagsql = ($rsearch['pag']['title'] == 1) ? "(page_title LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		$pagsql .= (!empty($pagsql) && ($rsearch['pag']['desc'] == 1)) ? " OR " : "(";
		$pagsql .= (($rsearch['pag']['desc'] == 1)) ? "page_desc LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		$pagsql .= (!empty($pagsql) && ($rsearch['pag']['text'] == 1)) ? " OR " : "(";
		$pagsql .= (($rsearch['pag']['text'] == 1)) ? "page_text LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		// String query for addition pages fields.
		$addfields = trim($cfg['plugin']['search']['addfields']);
		if(!empty($addfields))
		{
			$addfields_sql = '';
			foreach(explode(',', $addfields) as $addfields_el)
			{
				$addfields_el = trim($addfields_el);
				$addfields_sql .= ((!empty($addfields_el))) ? " OR ".$addfields_el." LIKE '".$sqlsearch."'" : "";
			}
		}
		$pagsql .= $addfields_sql.")";

		switch ($rsearch['pag']['sort'])
		{
			case 2: $orderby = "page_title";
				break;
			case 3:	$orderby = "page_count";
				break;
			default: $orderby = "page_date";
				break;
		}

		$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS * FROM $db_pages
		   	 		WHERE $pagsql $where AND page_state = '0' AND page_cat <> 'system'
					ORDER BY $orderby ".$rsearch['pag']['sort2']." LIMIT $d, ".$cfg['plugin']['search']['maxitems']);
		$items = sed_sql_numrows($sql);
		$totalitems[] = sed_sql_foundrows();
		$jj = 0;
		while($row = mysql_fetch_array($sql))
		{
			$page_url = empty($row['page_alias']) ? sed_url('page', 'id='.$row['page_id'].'&highlight='.$hl) : sed_url('page', 'al='.$row['page_alias'].'&highlight='.$hl);
			$t->assign(array(
				'PLUGIN_PR_CATEGORY' => '<a href="'.sed_url('list', 'c='.$row['page_cat']).'">'.$sed_cat[$row['page_cat']]['tpath'].'</a>',
				'PLUGIN_PR_TITLE' => '<a href="'.$page_url.'">'.htmlspecialchars($row['page_title']).'</a>',
				'PLUGIN_PR_TEXT' => sed_clear_mark($row['page_text'], $row['page_type'], $words),
				'PLUGIN_PR_TIME' => @date($cfg['dateformat'], $row['page_date'] + $usr['timezone'] * 3600),
				'PLUGIN_PR_ODDEVEN' => sed_build_oddeven($jj),
				'PLUGIN_PR_NUM' => $jj,
			));
			$t->parse("MAIN.RESULTS.PAGES.ITEM");
			$jj++;
		}
		if($jj > 0)
		{
			$t->parse('MAIN.RESULTS.PAGES');
		}
	}
	if (($tab == 'frm' || empty($tab)) && !$cfg['disable_forums'] && $cfg['plugin']['search']['forumsearch'] && empty($error_string))
	{
		$where = ($rsearch['frm']['sub'][0] != 'all' && count($rsearch['frm']['sub'])>0) ?
			"AND s.fs_id IN ('".sed_sql_prep(implode("','", $rsearch['frm']['sub']))."')" : "AND s.fs_id IN ('".implode("','", $frm_catauth)."')";
		$where .= ($rsearch['frm']['reply'] == '1') ? " AND t.ft_postcount > 1" : "";
		$where .= ($rsearch['time']['limit'] > 0) ? " AND p.fp_creation >= ".$rsearch['time']['from']." AND p.fp_updated <= ".$rsearch['time']['to'] : "";

		switch ($rsearch['frm']['sort'])
		{
			case 2: $orderby = "ft_creationdate";
				break;
			case 3: $orderby = "ft_title";
				break;
			case 4: $orderby = "ft_postcount";
				break;
			case 5: $orderby = "ft_viewcount";
				break;
			default: $orderby = "ft_updated";
				break;
		}

		$s_opt = ($rsearch['frm']['title'] == 1) ? "(t.ft_title LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		$s_opt .= (!empty($s_opt) && ($rsearch['frm']['text'] == 1)) ? " OR " : "(";
		$s_opt .= (($rsearch['frm']['text'] == 1)) ? "p.fp_text LIKE '".sed_sql_prep($sqlsearch)."'" : "";
		$s_opt .=")";
		$maxitems = $cfg['plugin']['search']['maxitems'] - $items;
		$maxitems =($maxitems < 0) ? 0 : $maxitems;
		$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS p.*, t.*, s.*
			 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
				WHERE $s_opt $where AND p.fp_topicid = t.ft_id AND p.fp_sectionid = s.fs_id
				GROUP BY t.ft_id ORDER BY $orderby ".$rsearch['frm']['sort2']."
				LIMIT $d, $maxitems");
		$items = sed_sql_numrows($sql);
		$totalitems[] = sed_sql_foundrows();
		$jj = 0;
		while($row = mysql_fetch_array($sql))
		{
			if($row['ft_updated'] > 0)
			{
				$post_url = ($cfg['plugin']['search']['searchurl'] == 'Single') ? sed_url('forums', 'm=posts&id='.$row['fp_id'].'&highlight='.$hl) : sed_url('forums', 'm=posts&p='.$row['fp_id'].'&highlight='.$hl, '#'.$row['fp_id']);
				$t->assign(array(
					'PLUGIN_FR_CATEGORY' => sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'], TRUE),
					'PLUGIN_FR_TITLE' => "<a href='$post_url'>".htmlspecialchars($row['ft_title'])."</a>",
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

	$error_string .= (array_sum($totalitems) < 1) ? $L['plu_noneresult'].'<br />' : '';
	if(empty($error_string))
	{
		$t->parse('MAIN.RESULTS');
	}

	$pagenav = sed_pagenav('plug', array('e' => 'search', 'pre' => $sq, 'tab' => $tab), $d, array_sum($totalitems), $cfg['plugin']['search']['maxitems']);
}

$date_drop_down = '<select name="rwithin" >
	<option value="0"'.(($rsearch['time']['limit'] == 0 || !isset($rsearch['time']['limit'])) ? ' selected="selected"' : '').'>'.$L['plu_any_date'].'</option>
	<option value="1"'.(($rsearch['time']['limit'] == 1) ? ' selected="selected"' : '').'>'.$L['plu_last_2_weeks'].'</option>
	<option value="2"'.(($rsearch['time']['limit'] == 2) ? ' selected="selected"' : '').'>'.$L['plu_last_1_month'].'</option>
	<option value="3"'.(($rsearch['time']['limit'] == 3) ? ' selected="selected"' : '').'>'.$L['plu_last_3_month'].'</option>
	<option value="4"'.(($rsearch['time']['limit'] == 4) ? ' selected="selected"' : '').'>'.$L['plu_last_1_year'].'</option>
	<option value="5"'.(($rsearch['time']['limit'] == 5) ? ' selected="selected"' : '').'>'.$L['plu_need_datas'].'</option>
	</select>';

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
	'PLUGIN_TITLE' => $plugin_title,
	'PLUGIN_SEARCH_ACTION' => sed_url('plug', 'e=search&tab='.$tab),
	'PLUGIN_SEARCH_TEXT' => '<input type="text" name="rsq" value="'.htmlspecialchars($sq).'" size="32" maxlength="'.$cfg['plugin']['search']['maxsigns'].'" />',
	'PLUGIN_SEARCH_DATE_SELECT' => $date_drop_down,
	'PLUGIN_SEARCH_DATE_FROM' => sed_selectbox_date($sys['now_offset']+$usr['timezone'] * 3600 - 31536000, 'short', '_from', date('Y', $sys['now_offset'])),
	'PLUGIN_SEARCH_DATE_TO' => sed_selectbox_date($sys['now_offset']+$usr['timezone'] * 3600, 'short', '_to', date('Y', $sys['now_offset'])),
	'PLUGIN_SEARCH_FOUND' => (array_sum($totalitems) > 0) ?  array_sum($totalitems) : '',
	'PLUGIN_PAGEPREV' => $pagenav['prev'],
	'PLUGIN_PAGENEXT' => $pagenav['next'],
	'PLUGIN_PAGENAV' => $pagenav['main'],
	'PLUGIN_ERROR' => $error_string
));

/* === Hook === */
$extp = sed_getextplugins('search.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */
?>