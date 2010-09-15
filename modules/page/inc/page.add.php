<?php
/**
 * Add page.
 *
 * @package page
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

cot_require_api('forms');

$id = cot_import('id', 'G', 'INT');
$r = cot_import('r', 'G', 'ALP');
$c = cot_import('c', 'G', 'ALP');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', 'any');

/* === Hook === */
foreach (cot_getextplugins('page.add.first') as $pl)
{
	include $pl;
}
/* ===== */
cot_block($usr['auth_write']);

if ($a == 'add')
{
	cot_shield_protect();

	/* === Hook === */
	foreach (cot_getextplugins('page.add.add.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rpage['cat'] = cot_import('rpagecat', 'P', 'TXT');
	$rpage['key'] = cot_import('rpagekey', 'P', 'TXT');
	$rpage['alias'] = cot_import('rpagealias', 'P', 'ALP');
	$rpage['type'] = $usr['isadmin'] ? cot_import('rpagetype', 'P', 'INT') : 0;
	$rpage['title'] = cot_import('rpagetitle', 'P', 'TXT');
	$rpage['desc'] = cot_import('rpagedesc', 'P', 'TXT');
	$rpage['text'] = cot_import('rpagetext', 'P', 'HTM');
	$rpage['author'] = cot_import('rpageauthor', 'P', 'TXT');
	$rpage['file'] = intval(cot_import('rpagefile', 'P', 'INT'));
	$rpage['url'] = cot_import('rpageurl', 'P', 'TXT');
	$rpage['size'] = cot_import('rpagesize', 'P', 'TXT');
	$rpage['file'] = ($rpage['file'] == 0 && !empty($rpage['url'])) ? 1 : $rpage['file'];
	$rpage['ownerid'] =	(int)$usr['id'];

	$rpage['date']	= (int)$sys['now_offset'];
	$rpage['begin'] = (int)cot_import_date('rpagebegin');
	$rpage['expire'] = (int)cot_import_date('rpageexpire');
	$rpage['expire'] = ($rpage['expire'] <= $rpage['begin']) ? $rpage['begin'] + 31536000 : $rpage['expire'];
		
	// Extra fields
	foreach ($cot_extrafields['pages'] as $row)
	{
		$rpage[$row['field_name']] = cot_import_extrafields('page', $row);
	}

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', $rpage['cat']);
	cot_block($usr['auth_write']);

	if (empty($rpage['cat'])) cot_error('pag_catmissing', 'rpagecat');
	if (mb_strlen($rpage['title']) < 2) cot_error('pag_titletooshort', 'rpagetitle');

	/* === Hook === */
	foreach (cot_getextplugins('page.add.add.error') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!$cot_error)
	{
		if (!empty($rpage['alias']))
		{
			$sql = cot_db_query("SELECT page_id FROM $db_pages WHERE page_alias='".cot_db_prep($rpage['alias'])."'");
			$rpage['alias'] = (cot_db_numrows($sql) > 0) ? $rpage['alias'].rand(1000, 9999) : $rpage['alias'];
		}

		$rpage['type'] = ($usr['maingrp'] != 5 && $rpage['type'] == 2) ? 0 : (int)$rpage['type'];

		$rpage['html'] = ($cfg['parser_cache'] && $rpage['type'] != 1) ? cot_parse(htmlspecialchars($rpage['text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], true, true) : '';

		if ($usr['isadmin'] && $cfg['autovalidate'])
		{
			$rpublish = cot_import('rpublish', 'P', 'ALP');
			if ($rpublish == 'OK')
			{
				$rpage['state'] = 0;
				cot_db_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".cot_db_prep($rpage['cat'])."' ");
			}
			else
			{
				$rpage['state'] = 1;
			}
		}
		else
		{
			$rpage['state'] = 1;
		}

		/* === Hook === */
		foreach (cot_getextplugins('page.add.add.query') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$sql = cot_db_insert($db_pages, $rpage, 'page_');
		$id = cot_db_insertid();
		$r_url = (!$rpage['state']) ? cot_url('page', "id=".$id, '', true) : cot_url('message', "msg=300", '', true);

		/* === Hook === */
		foreach (cot_getextplugins('page.add.add.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($rpage['state'] == 0 && $cot_cache)
		{
			if ($cfg['cache_page'])
			{
				$cot_cache->page->clear('page/' . str_replace('.', '/', $cot_cat[$rpage['cat']]['path']));
			}
			if ($cfg['cache_index'])
			{
				$cot_cache->page->clear('index');
			}
		}
		cot_shield_update(30, "r page");
		cot_redirect($r_url);
	}
	else
	{
		cot_redirect(cot_url('page', 'm=add'));
	}
}

if (empty($rpage['cat']) && !empty($c))
{
	$rpage['cat'] = $c;
	$usr['isadmin'] = cot_auth('page', $rpage['cat'], 'A');
}

$title_params = array(
	'TITLE' => $L['pagadd_subtitle'],
	'CATEGORY' => $cot_cat[$c]['title']
);

$out['subtitle'] = cot_title('title_page', $title_params);
$out['head'] .= $R['code_noindex'];
$sys['sublocation'] = $cot_cat[$c]['title'];
cot_online_update();

/* === Hook === */
foreach (cot_getextplugins('page.add.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'].'/header.php';

$mskin = cot_skinfile(array('page', 'add', $cot_cat[$rpage['cat']]['tpl']));
$t = new XTemplate($mskin);

// FIXME PFS dependency
//$pfs = cot_build_pfs($usr['id'], 'rpage', 'rpagetext',$L['Mypfs']);
//$pfs .= (cot_auth('pfs', 'a', 'A')) ? " &nbsp; ".cot_build_pfs(0, 'rpage', 'rpagetext', $L['SFS']) : '';
//$pfs_form_url_myfiles = (!$cfg['disable_pfs']) ? cot_build_pfs($usr['id'], 'rpage', 'rpageurl', $L['Mypfs']) : '';
//$pfs_form_url_myfiles .= (cot_auth('pfs', 'a', 'A')) ? ' '.cot_build_pfs(0, 'rpage', 'rpageurl', $L['SFS']) : '';

$pageadd_array = array(
	"PAGEADD_PAGETITLE" => $L['pagadd_title'],
	"PAGEADD_SUBTITLE" => $L['pagadd_subtitle'],
	"PAGEADD_ADMINEMAIL" => "mailto:".$cfg['adminemail'],
	"PAGEADD_FORM_SEND" => cot_url('page', 'm=add&a=add'),
	"PAGEADD_FORM_CAT" => cot_selectbox_categories($rpage['cat'], 'rpagecat'),
	"PAGEADD_FORM_CAT_SHORT" => cot_selectbox_categories($rpage['cat'], 'rpagecat', $c),
	"PAGEADD_FORM_KEY" => cot_inputbox('text', 'rpagekey', $rpage['key'], array('size' => '16', 'maxlength' => '16')),
	"PAGEADD_FORM_ALIAS" => cot_inputbox('text', 'rpagealias', $rpage['alias'], array('size' => '32', 'maxlength' => '255')),
	"PAGEADD_FORM_TITLE" => cot_inputbox('text', 'rpagetitle', $rpage['title'], array('size' => '64', 'maxlength' => '255')),
	"PAGEADD_FORM_DESC" => cot_inputbox('text', 'rpagedesc', $rpage['desc'], array('size' => '64', 'maxlength' => '255')),
	"PAGEADD_FORM_AUTHOR" => cot_inputbox('text', 'rpageauthor', $rpage['author'], array('size' => '16', 'maxlength' => '24')),
	"PAGEADD_FORM_OWNER" => cot_build_user($usr['id'], htmlspecialchars($usr['name'])),
	"PAGEADD_FORM_OWNERID" => $usr['id'],
	"PAGEADD_FORM_BEGIN" => cot_selectbox_date($sys['now_offset'], 'long', 'rpagebegin'),
	"PAGEADD_FORM_EXPIRE" => cot_selectbox_date($sys['now_offset'] + 31536000, 'long', 'rpageexpire'),
	"PAGEADD_FORM_FILE" => cot_selectbox($rpage['file'], 'rpagefile', range(0, 2), array($L['No'], $L['Yes'], $L['Members_only']), false),
	"PAGEADD_FORM_URL" => cot_inputbox('text', 'rpageurl', $rpage['url'], array('size' => '56', 'maxlength' => '255')),
	"PAGEADD_FORM_SIZE" => cot_inputbox('text', 'rpagesize', $rpage['size'], array('size' => '56', 'maxlength' => '255')),
	"PAGEADD_FORM_TEXT" => cot_textarea('rpagetext', $rpage['text'], 24, 120, '', 'input_textarea_editor'),
	"PAGEADD_FORM_MYPFS" => $pfs
);

if ($usr['isadmin'])
{
	$page_type_options = array(0 => $L['Default'], 1 => 'HTML');
	if ($usr['maingrp'] == 5 && $cfg['allowphp_pages'] && $cfg['allowphp_override'])
	{
		$page_type_options += array(2 => 'PHP');
	}
	$pageadd_array['PAGEADD_FORM_TYPE'] = cot_selectbox($rpage['type'], 'rpagetype', array_keys($page_type_options),
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
//	$pfs_js = (!$cfg['disable_pfs']) ? cot_build_pfs($usr['id'], "rpage", "rpage$field", $L['Mypfs']) : '';
//	$pageadd_array[$tag] = $pfs_js;
//}
//unset($match);
//preg_match_all("#\{(PAGEADD_FORM_PFS_([^\}]*?)_SITE)\}#", $tplskin, $match);
//$numtags = count($match[0]);
//for ($i = 0; $i < $numtags; $i++)
//{
//	$tag = $match[1][$i];
//	$field = strtolower($match[2][$i]);
//	$pfs_js = (cot_auth('pfs', 'a', 'A')) ? ' '.cot_build_pfs(0, "rpage", "rpage$field", $L['SFS']) : '';
//	$pageadd_array[$tag] = $pfs_js;
//}
$t->assign($pageadd_array);

// Extra fields
foreach($cot_extrafields['pages'] as $i => $row)
{
	$uname = strtoupper($row['field_name']);
	$t->assign('PAGEADD_FORM_'.$uname, cot_build_extrafields('page', $row, $rpage[$row['field_name']]));
	$t->assign('PAGEADD_FORM_'.$uname.'_TITLE', isset($L['page_'.$row['field_name'].'_title']) ?  $L['page_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Error and message handling
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('page.add.tags') as $pl)
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