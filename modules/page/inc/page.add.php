<?php
/**
 * Add page.
 *
 * @package page
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

$id = sed_import('id', 'G', 'INT');
$r = sed_import('r', 'G', 'ALP');
$c = sed_import('c', 'G', 'ALP');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');

/* === Hook === */
foreach (sed_getextplugins('page.add.first') as $pl)
{
	include $pl;
}
/* ===== */
sed_block($usr['auth_write']);

if ($a == 'add')
{
	sed_shield_protect();

	/* === Hook === */
	foreach (sed_getextplugins('page.add.add.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$newpagecat = sed_import('newpagecat', 'P', 'TXT');

	$usr['isadmin'] = sed_auth('page', $newpagecat, 'A');

	$newpagekey = sed_import('newpagekey', 'P', 'TXT');
	$newpagealias = sed_import('newpagealias', 'P', 'ALP');
	$newpagetype = $usr['isadmin'] ? sed_import('newpagetype', 'P', 'INT') : 0;
	$newpagetitle = sed_import('newpagetitle', 'P', 'TXT');
	$newpagedesc = sed_import('newpagedesc', 'P', 'TXT');
	$newpagetext = sed_import('newpagetext', 'P', 'HTM');
	$newpageauthor = sed_import('newpageauthor', 'P', 'TXT');
	$newpagefile = sed_import('newpagefile', 'P', 'INT');
	$newpageurl = sed_import('newpageurl', 'P', 'TXT');
	$newpagesize = sed_import('newpagesize', 'P', 'TXT');
	$newpageyear_beg = sed_import('ryear_beg', 'P', 'INT');
	$newpagemonth_beg = sed_import('rmonth_beg', 'P', 'INT');
	$newpageday_beg = sed_import('rday_beg', 'P', 'INT');
	$newpagehour_beg = sed_import('rhour_beg', 'P', 'INT');
	$newpageminute_beg = sed_import('rminute_beg', 'P', 'INT');
	$newpageyear_exp = sed_import('ryear_exp', 'P', 'INT');
	$newpagemonth_exp = sed_import('rmonth_exp', 'P', 'INT');
	$newpageday_exp = sed_import('rday_exp', 'P', 'INT');
	$newpagehour_exp = sed_import('rhour_exp', 'P', 'INT');
	$newpageminute_exp = sed_import('rminute_exp', 'P', 'INT');

	$newpagebegin = sed_mktime($newpagehour_beg, $newpageminute_beg, 0, $newpagemonth_beg, $newpageday_beg, $newpageyear_beg) - $usr['timezone'] * 3600;
	$newpageexpire = sed_mktime($newpagehour_exp, $newpageminute_exp, 0, $newpagemonth_exp, $newpageday_exp, $newpageyear_exp) - $usr['timezone'] * 3600;
	$newpageexpire = ($newpageexpire<=$newpagebegin) ? $newpagebegin + 31536000 : $newpageexpire;

	// Extra fields
	foreach ($sed_extrafields['pages'] as $row)
	{
		$import = sed_import('newpage'.$row['field_name'], 'P', 'HTM');
		if ($row['field_type'] == 'checkbox' && !is_null($import))
		{
			$import = $import != '';
		}
		$newpageextrafields[$row['field_name']] = $import;
	}

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $newpagecat);
	sed_block($usr['auth_write']);

	if (empty($newpagecat)) sed_error('pag_catmissing', 'newpagecat');
	if (mb_strlen($newpagetitle) < 2) sed_error('pag_titletooshort', 'newpagetitle');

	if ($newpagefile == 0 && !empty($newpageurl))
	{
		$newpagefile = 1;
	}

	if (!$cot_error)
	{
		if (!empty($newpagealias))
		{
			$sql = sed_sql_query("SELECT page_id FROM $db_pages WHERE page_alias='".sed_sql_prep($newpagealias)."'");
			$newpagealias = (sed_sql_numrows($sql) > 0) ? "alias".rand(1000, 9999) : $newpagealias;
		}

		$newpagetype = ($usr['maingrp'] != 5 && $newpagetype == 2) ? 0 : $newpagetype;

		if ($cfg['parser_cache'] && $newpagetype != 1)
		{
			$newpagehtml = sed_parse(htmlspecialchars($newpagetext), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], true, true);
		}
		else
		{
			$newpagehtml = '';
		}

		if ($usr['isadmin'] && $cfg['autovalidate'])
		{
			$rpublish = sed_import('rpublish', 'P', 'ALP');
			if ($rpublish == 'OK')
			{
				$page_state = 0;
				sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".sed_sql_prep($newpagecat)."' ");
			}
			else
			{
				$page_state = 1;
			}
		}
		else
		{
			$page_state = 1;
		}

		/* === Hook === */
		foreach (sed_getextplugins('page.add.add.query') as $pl)
		{
			include $pl;
		}
		/* ===== */

		// Extra fields
		if(count($extrafields) > 0)
		{
			foreach($extrafields as $i => $row)
			{
				if(!is_null($newpageextrafields[$i]))
				{
					$ssql_extra_columns .= 'page_'.$row['field_name'].', ';
					$ssql_extra_values .= "'".sed_sql_prep($newpageextrafields[$i])."', ";
				}
			}
		}

		$ssql = "INSERT into $db_pages
			(page_state,
			page_type,
			page_cat,
			page_key,
			page_title,
			page_desc,
			page_text,
			page_html,
			page_author,
			page_ownerid,
			page_date,
			page_begin,
			page_expire,
			page_file,
			page_url,
			page_size,
			$ssql_extra_columns
			page_alias)
			VALUES
			(".(int)$page_state.",
			".(int)$newpagetype.",
			'".sed_sql_prep($newpagecat)."',
			'".sed_sql_prep($newpagekey)."',
			'".sed_sql_prep($newpagetitle)."',
			'".sed_sql_prep($newpagedesc)."',
			'".sed_sql_prep($newpagetext)."',
			'".sed_sql_prep($newpagehtml)."',
			'".sed_sql_prep($newpageauthor)."',
			".(int)$usr['id'].",
			".(int)$sys['now_offset'].",
			".(int)$newpagebegin.",
			".(int)$newpageexpire.",
			".intval($newpagefile).",
			'".sed_sql_prep($newpageurl)."',
			'".sed_sql_prep($newpagesize)."',
			$ssql_extra_values
			'".sed_sql_prep($newpagealias)."')";
		$sql = sed_sql_query($ssql);

		$id = sed_sql_insertid();
		$r_url = (!$page_state) ? sed_url('page', "id=".$id, '', true) : sed_url('message', "msg=300", '', true);

		/* === Hook === */
		foreach (sed_getextplugins('page.add.add.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($page_state == 0 && $cot_cache)
		{
			if ($cfg['cache_page'])
			{
				$cot_cache->page->clear('page/' . str_replace('.', '/', $sed_cat[$newpagecat]['path']));
			}
			if ($cfg['cache_index'])
			{
				$cot_cache->page->clear('index');
			}
		}

		sed_shield_update(30, "New page");
		sed_redirect($r_url);
	}
	else
	{
		sed_redirect(sed_url('page', 'm=add'));
	}
}

if (empty($newpagecat) && !empty($c))
{
	$newpagecat = $c;
	$usr['isadmin'] = sed_auth('page', $newpagecat, 'A');
}

$title_params = array(
	'TITLE' => $L['pagadd_subtitle'],
	'CATEGORY' => $sed_cat[$c]['title']
);
$out['subtitle'] = sed_title('title_page', $title_params);
$out['head'] .= $R['code_noindex'];
$sys['sublocation'] = $sed_cat[$c]['title'];
sed_online_update();

/* === Hook === */
foreach (sed_getextplugins('page.add.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'].'/header.php';

$mskin = sed_skinfile(array('page', 'add', $sed_cat[$newpagecat]['tpl']));
$t = new XTemplate($mskin);

sed_require_api('forms');

$pageadd_form_file = sed_selectbox($newpagefile, 'newpagefile', range(0, 2),
	array($L['No'], $L['Yes'], $L['Members_only']), false);

$newpage_form_begin = sed_selectbox_date($sys['now_offset']+$usr['timezone'] * 3600, 'long', '_beg');
$newpage_form_expire = sed_selectbox_date($sys['now_offset']+$usr['timezone'] * 3600 + 31536000, 'long', '_exp');

// FIXME PFS dependency
//$pfs = sed_build_pfs($usr['id'], 'newpage', 'newpagetext',$L['Mypfs']);
//$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, 'newpage', 'newpagetext', $L['SFS']) : '';
//$pfs_form_url_myfiles = (!$cfg['disable_pfs']) ? sed_build_pfs($usr['id'], 'newpage', 'newpageurl', $L['Mypfs']) : '';
//$pfs_form_url_myfiles .= (sed_auth('pfs', 'a', 'A')) ? ' '.sed_build_pfs(0, 'newpage', 'newpageurl', $L['SFS']) : '';

$pageadd_array = array(
	"PAGEADD_PAGETITLE" => $L['pagadd_title'],
	"PAGEADD_SUBTITLE" => $L['pagadd_subtitle'],
	"PAGEADD_ADMINEMAIL" => "mailto:".$cfg['adminemail'],
	"PAGEADD_FORM_SEND" => sed_url('page', 'm=add&a=add'),
	"PAGEADD_FORM_CAT" => sed_selectbox_categories($newpagecat, 'newpagecat'),
	"PAGEADD_FORM_CAT_SHORT" => sed_selectbox_categories($newpagecat, 'newpagecat', $c),
	"PAGEADD_FORM_KEY" => sed_inputbox('text', 'newpagekey', $newpagekey, array('size' => '16', 'maxlength' => '16')),
	"PAGEADD_FORM_ALIAS" => sed_inputbox('text', 'newpagealias', $newpagealias, array('size' => '32', 'maxlength' => '255')),
	"PAGEADD_FORM_TITLE" => sed_inputbox('text', 'newpagetitle', $newpagetitle, array('size' => '64', 'maxlength' => '255')),
	"PAGEADD_FORM_DESC" => sed_inputbox('text', 'newpagedesc', $newpagedesc, array('size' => '64', 'maxlength' => '255')),
	"PAGEADD_FORM_AUTHOR" => sed_inputbox('text', 'newpageauthor', $newpageauthor, array('size' => '16', 'maxlength' => '24')),
	"PAGEADD_FORM_OWNER" => sed_build_user($usr['id'], htmlspecialchars($usr['name'])),
	"PAGEADD_FORM_OWNERID" => $usr['id'],
	"PAGEADD_FORM_BEGIN" => $newpage_form_begin,
	"PAGEADD_FORM_EXPIRE" => $newpage_form_expire,
	"PAGEADD_FORM_FILE" => $pageadd_form_file,
	"PAGEADD_FORM_URL" => sed_inputbox('text', 'newpageurl', $newpageurl, array('size' => '56', 'maxlength' => '255')),
	"PAGEADD_FORM_SIZE" => sed_inputbox('text', 'newpagesize', $newpagesize, array('size' => '56', 'maxlength' => '255')),
	"PAGEADD_FORM_TEXT" => sed_textarea('newpagetext', $newpagetext, 24, 120, '', 'input_textarea_editor'),
	"PAGEADD_FORM_TEXTBOXER" => sed_textarea('newpagetext', $newpagetext, 24, 120, '', 'input_textarea_editor'),
	"PAGEADD_FORM_MYPFS" => $pfs
);

if ($usr['isadmin'])
{
	$page_type_options = array(0 => $L['Default'], 1 => 'HTML');
	if ($usr['maingrp'] == 5 && $cfg['allowphp_pages'] && $cfg['allowphp_override'])
	{
		$page_type_options += array(2 => 'PHP');
	}
	$pageadd_array['PAGEADD_FORM_TYPE'] = sed_selectbox($newpagetype, 'newpagetype', array_keys($page_type_options),
		array_values($page_type_options), false);
}

// FIXME PFS dependency
// PFS tags
//$tplskin = file_get_contents($mskin);
//preg_match_all("#\{(PAGEADD_FORM_PFS_([^\}]*?)_USER)\}#", $tplskin, $match);
//$numtags = count($match[0]);
//for ($i = 0; $i < $numtags; $i++)
//{
//	$tag = $match[1][$i];
//	$field = strtolower($match[2][$i]);
//	$pfs_js = (!$cfg['disable_pfs']) ? sed_build_pfs($usr['id'], "newpage", "newpage$field", $L['Mypfs']) : '';
//	$pageadd_array[$tag] = $pfs_js;
//}
//unset($match);
//preg_match_all("#\{(PAGEADD_FORM_PFS_([^\}]*?)_SITE)\}#", $tplskin, $match);
//$numtags = count($match[0]);
//for ($i = 0; $i < $numtags; $i++)
//{
//	$tag = $match[1][$i];
//	$field = strtolower($match[2][$i]);
//	$pfs_js = (sed_auth('pfs', 'a', 'A')) ? ' '.sed_build_pfs(0, "newpage", "newpage$field", $L['SFS']) : '';
//	$pageadd_array[$tag] = $pfs_js;
//}
$t->assign($pageadd_array);

// Extra fields
foreach($sed_extrafields['pages'] as $i => $row)
{
	$uname = strtoupper($row['field_name']);
	$t->assign('PAGEADD_FORM_'.$uname, sed_build_extrafields('page', $row, sed_import_buffered('newpage'.$row['field_name'], $newpageextrafields[$row['field_name']]), true));
	$t->assign('PAGEADD_FORM_'.$uname.'_TITLE', isset($L['page_'.$row['field_name'].'_title']) ?  $L['page_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Error and message handling
if (sed_check_messages())
{
	$t->assign('PAGEADD_ERROR_BODY', sed_implode_messages());
	$t->parse('MAIN.PAGEADD_ERROR');
	sed_clear_messages();
}

/* === Hook === */
foreach (sed_getextplugins('page.add.tags') as $pl)
{
	include $pl;
}
/* ===== */

if ($usr['isadmin'])
{
	if ($cfg['autovalidate']) $usr_can_publish = TRUE;
	$t->parse('MAIN.ADMIN');
}

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'].'/footer.php';

?>