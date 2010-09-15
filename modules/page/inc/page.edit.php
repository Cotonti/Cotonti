<?php
/**
 * Edit page.
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
$c = cot_import('c', 'G', 'TXT');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', 'any');

/* === Hook === */
foreach (cot_getextplugins('page.edit.first') as $pl)
{
	include $pl;
}
/* ===== */

cot_block($usr['auth_read']);

if ($a == 'update')
{
	$sql1 = cot_db_query("SELECT page_cat, page_ownerid FROM $db_pages WHERE page_id='$id' LIMIT 1");
	cot_die(cot_db_numrows($sql1) == 0);
	$row1 = cot_db_fetcharray($sql1);

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', $row1['page_cat']);

	/* === Hook === */
	foreach (cot_getextplugins('page.edit.update.first') as $pl)
	{
		include $pl;
	}
	/* ===== */
	cot_block($usr['isadmin'] || $usr['auth_write'] && $usr['id'] == $row1['page_ownerid']);

	$rpage['key'] = cot_import('rpagekey', 'P', 'TXT');
	$rpage['alias'] = cot_import('rpagealias', 'P', 'ALP');
	$rpage['title'] = cot_import('rpagetitle', 'P', 'TXT');
	$rpage['desc'] = cot_import('rpagedesc', 'P', 'TXT');
	$rpage['text'] = cot_import('rpagetext', 'P', 'HTM');
	$rpage['author'] = cot_import('rpageauthor', 'P', 'TXT');
	$rpage['file'] = cot_import('rpagefile', 'P', 'INT');
	$rpage['url'] = cot_import('rpageurl', 'P', 'TXT');
	$rpage['size'] = cot_import('rpagesize', 'P', 'TXT');
	$rpage['file'] = ($rpage['file'] == 0 && !empty($rpage['url'])) ? 1 : $rpage['file'];

	$rpage['cat'] = cot_import('rpagecat', 'P', 'TXT');

	$rpagedatenow = cot_import('rpagedatenow', 'P', 'BOL');
	$rpage['date'] = ($rpagedatenow) ? $sys['now_offset'] : (int)cot_import_date('rpagedate');
	$rpage['begin'] = (int)cot_import_date('rpagebegin');
	$rpage['expire'] = (int)cot_import_date('rpageexpire');
	$rpage['expire'] = ($rpage['expire'] <= $rpage['begin']) ? $rpage['begin'] + 31536000 : $rpage['expire'];

	// Extra fields
	foreach ($cot_extrafields['pages'] as $row)
	{
		$rpage[$row['field_name']] = cot_import_extrafields('page', $row);
	}

	if ($usr['isadmin'])
	{
		$rpage['type'] = cot_import('rpagetype', 'P', 'INT');
		$rpage['count'] = cot_import('rpagecount', 'P', 'INT');
		$rpage['ownerid'] = cot_import('rpageownerid', 'P', 'INT');
		$rpage['filecount'] = cot_import('rpagefilecount', 'P', 'INT');
	}
	$rpagedelete = cot_import('rpagedelete', 'P', 'BOL');

	if (empty($rpage['cat'])) cot_error('pag_catmissing', 'rpagecat');
	if (mb_strlen($rpage['title']) < 2) cot_error('pag_titletooshort', 'rpagetitle');

	/* === Hook === */
	foreach (cot_getextplugins('page.edit.update.error') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if($rpagedelete)
	{
		$sql = cot_db_query("SELECT * FROM $db_pages WHERE page_id='$id' LIMIT 1");

		if ($row = cot_db_fetchassoc($sql))
		{
			if ($cfg['trash_page'])
			{
				cot_trash_put('page', $L['Page']." #".$id." ".$row['page_title'], $id, $row);
			}

			if ($row['page_state'] != 1)
			{
				$sql = cot_db_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".$row['page_cat']."' ");
			}

			$id2 = "p".$id;
			$sql = cot_db_delete($db_pages, "page_id='$id'");
			$sql = cot_db_delete($db_ratings, "rating_code='$id2'");
			$sql = cot_db_delete($db_rated, "rated_code='$id2'");
			cot_log("Deleted page #".$id,'adm');
			/* === Hook === */
			foreach (cot_getextplugins('page.edit.delete.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			if ($cot_cache)
			{
				if ($cfg['cache_page'])
				{
					$cot_cache->page->clear('page/' . str_replace('.', '/', $cot_cat[$row['page_cat']]['path']));
				}
				if ($cfg['cache_index'])
				{
					$cot_cache->page->clear('index');
				}
			}
			cot_redirect(cot_url('page', "c=".$row1['page_cat'], '', true));
		}
	}
	elseif (!$cot_error)
	{
		$rpage['type'] = ($usr['maingrp'] != 5 && $rpage['type'] == 2) ? 0 : $rpage['type'];

		if (!empty($rpage['alias']))
		{
			$sql = cot_db_query("SELECT page_id FROM $db_pages WHERE page_alias='".cot_db_prep($rpage['alias'])."' AND page_id!='".$id."'");
			$rpage['alias'] = (cot_db_numrows($sql) > 0) ? $rpage['alias'].rand(1000, 9999) : $rpage['alias'];
		}

		$rpage['html'] = ($cfg['parser_cache'] && $rpage['type'] != 1) ? cot_parse(htmlspecialchars($rpage['text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], true, true) : '';

		$sql = cot_db_query("SELECT page_cat, page_state FROM $db_pages WHERE page_id='$id' ");
		$row = cot_db_fetcharray($sql);

		if ($row['page_cat'] != $rpage['cat'] /*&& ($row['page_state'] == 0 || $row['page_state'] == 2)*/)
		{
			$sql = cot_db_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".cot_db_prep($row['page_cat'])."' ");
			//$sql = cot_db_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".cot_db_prep($rpage['cat)."' ");
		}

		//$usr['isadmin'] = cot_auth('page', $rpage['cat'], 'A');
		if ($usr['isadmin'] && $cfg['autovalidate'])
		{
			$rpublish = cot_import('rpublish', 'P', 'ALP');
			if ($rpublish == 'OK' )
			{
				$rpage['state'] = 0;
				if ($row['page_state'] == 1)
				{
					cot_db_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".cot_db_prep($rpage['cat'])."' ");
				}
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
		if ($rpage['state'] == 1 && $row['page_state'] != 1)
		{
			cot_db_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".cot_db_prep($rpage['cat'])."' ");
		}

		$sql = cot_db_update($db_pages, $rpage, "page_id='$id'", 'page_');
		/* === Hook === */
		foreach (cot_getextplugins('page.edit.update.done') as $pl)
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

		cot_log("Edited page #".$id,'adm');
		cot_redirect(cot_url('page', "id=".$id, '', true));
	}
	else
	{
		cot_redirect(cot_url('page', "m=edit&id=$id", '', true));
	}
}

$sql = cot_db_query("SELECT * FROM $db_pages WHERE page_id='$id' LIMIT 1");
cot_die(cot_db_numrows($sql) == 0);
$pag = cot_db_fetcharray($sql);

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', $pag['page_cat']);
cot_block($usr['isadmin'] || $usr['auth_write'] && $usr['id'] == $pag['page_ownerid']);

$title_params = array(
	'TITLE' => $L['paged_title'],
	'CATEGORY' => $cot_cat[$c]['title']
);
$out['subtitle'] = cot_title('title_page', $title_params);
$out['head'] .= $R['code_noindex'];
$sys['sublocation'] = $cot_cat[$c]['title'];
cot_online_update();

/* === Hook === */
foreach (cot_getextplugins('page.edit.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'].'/header.php';

$mskin = cot_skinfile(array('page', 'edit', $cot_cat[$pag['page_cat']]['tpl']));
$t = new XTemplate($mskin);

$page_type_options = array('0' => $L['Default'], '1' => 'HTML');
if ($usr['maingrp'] == 5 && $cfg['allowphp_pages'] && $cfg['allowphp_override'])
{
	$page_type_options += array('2' => 'PHP');
}

// FIXME PFS dependency
//$pfs = cot_build_pfs($usr['id'], 'update', 'rpagetext', $L['Mypfs']);
//$pfs .= (cot_auth('pfs', 'a', 'A')) ? " &nbsp; ".cot_build_pfs(0, 'update', 'rpagetext', $L['SFS']) : '';
//$pfs_form_url_myfiles = (!$cfg['disable_pfs']) ? cot_build_pfs($usr['id'], "update", "rpageurl", $L['Mypfs']) : '';
//$pfs_form_url_myfiles .= (cot_auth('pfs', 'a', 'A')) ? ' '.cot_build_pfs(0, 'update', 'rpageurl', $L['SFS']) : '';

$pageedit_array = array(
	"PAGEEDIT_PAGETITLE" => $L['paged_title'],
	"PAGEEDIT_SUBTITLE" => $L['paged_subtitle'],
	"PAGEEDIT_FORM_SEND" => cot_url('page', "m=edit&a=update&id=".$pag['page_id']),
	"PAGEEDIT_FORM_ID" => $pag['page_id'],
	"PAGEEDIT_FORM_STATE" => $pag['page_state'],
	"PAGEEDIT_FORM_CAT" => cot_selectbox_categories($pag['page_cat'], 'rpagecat'),
	"PAGEEDIT_FORM_CAT_SHORT" => cot_selectbox_categories($pag['page_cat'], 'rpagecat', $c),
	"PAGEEDIT_FORM_KEY" => cot_inputbox('text', 'rpagekey', $pag['page_key'], array('size' => '16', 'maxlength' => '16')),
	"PAGEEDIT_FORM_ALIAS" => cot_inputbox('text', 'rpagealias', $pag['page_alias'], array('size' => '32', 'maxlength' => '255')),
	"PAGEEDIT_FORM_TITLE" => cot_inputbox('text', 'rpagetitle', $pag['page_title'], array('size' => '64', 'maxlength' => '255')),
	"PAGEEDIT_FORM_DESC" => cot_inputbox('text', 'rpagedesc', $pag['page_desc'], array('size' => '64', 'maxlength' => '255')),
	"PAGEEDIT_FORM_AUTHOR" => cot_inputbox('text', 'rpageauthor', $pag['page_author'], array('size' => '24', 'maxlength' => '24')),
	"PAGEEDIT_FORM_DATE" => cot_selectbox_date($pag['page_date'],'long', 'rpagedate').' '.$usr['timetext'],
	"PAGEEDIT_FORM_DATENOW" => cot_checkbox(0, 'rpagedatenow'),
	"PAGEEDIT_FORM_BEGIN" => cot_selectbox_date($pag['page_begin'], 'long', 'rpagebegin').' '.$usr['timetext'],
	"PAGEEDIT_FORM_EXPIRE" => cot_selectbox_date($pag['page_expire'], 'long', 'rpageexpire').' '.$usr['timetext'],
	"PAGEEDIT_FORM_FILE" => cot_selectbox($pag['page_file'], 'rpagefile', range(0, 2), array($L['No'], $L['Yes'], $L['Members_only']), false),
	"PAGEEDIT_FORM_URL" => cot_inputbox('text', 'rpageurl', $pag['page_url'], array('size' => '56', 'maxlength' => '255')),
	"PAGEEDIT_FORM_SIZE" => cot_inputbox('text', 'rpagesize', $pag['page_size'], array('size' => '56', 'maxlength' => '255')),
	"PAGEEDIT_FORM_TEXT" => cot_textarea('rpagetext', $pag['page_text'], 24, 120, '', 'input_textarea_editor'),
	"PAGEEDIT_FORM_MYPFS" => $pfs,
	"PAGEEDIT_FORM_DELETE" => cot_radiobox(0, 'rpagedelete', array(1, 0), array($L['Yes'], $L['No']))
);
if ($usr['isadmin'])
{
	$pageedit_array = array(
		"PAGEEDIT_FORM_TYPE" => cot_selectbox($pag['page_type'], 'rpagetype', array_keys($page_type_options), array_values($page_type_options), false),
		"PAGEEDIT_FORM_OWNERID" => cot_inputbox('text', 'rpageownerid', $pag['page_ownerid'], array('size' => '24', 'maxlength' => '24')),
		"PAGEEDIT_FORM_PAGECOUNT" => cot_inputbox('text', 'rpagecount', $pag['page_count'], array('size' => '8', 'maxlength' => '8')),
		"PAGEEDIT_FORM_FILECOUNT" => cot_inputbox('text', 'rpagefilecount', $pag['page_filecount'], array('size' => '8', 'maxlength' => '8'))
	);
}
// FIXME PFS dependency
// PFS tags
//$tplskin = file_get_contents($mskin);
//preg_match_all("#\{(PAGEEDIT_FORM_PFS_([^\}]*?)_USER)\}#", $tplskin, $match);
//$numtags = count($match[0]);
//for($i = 0; $i < $numtags; $i++)
//{
//	$tag = $match[1][$i];
//	$field = strtolower($match[2][$i]);
//	$pfs_js = (!$cfg['disable_pfs']) ? cot_build_pfs($usr['id'], "update", "rpage$field", $L['Mypfs']) : '';
//	$pageedit_array[$tag] = $pfs_js;
//}
//unset($match);
//preg_match_all("#\{(PAGEEDIT_FORM_PFS_([^\}]*?)_SITE)\}#", $tplskin, $match);
//$numtags = count($match[0]);
//for($i = 0; $i < $numtags; $i++)
//{
//	$tag = $match[1][$i];
//	$field = strtolower($match[2][$i]);
//	$pfs_js = (cot_auth('pfs', 'a', 'A')) ? ' '.cot_build_pfs(0, "update", "rpage$field", $L['SFS']) : '';
//	$pageedit_array[$tag] = $pfs_js;
//}
$t->assign($pageedit_array);

// Extra fields
foreach($cot_extrafields['pages'] as $i => $row)
{
	$uname = strtoupper($row['field_name']);
	$t->assign('PAGEEDIT_FORM_'.$uname, cot_build_extrafields('page', $row, $rpage[$row['field_name']]));
	$t->assign('PAGEEDIT_FORM_'.$uname.'_TITLE', isset($L['page_'.$row['field_name'].'_title']) ?  $L['page_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Error and message handling
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('page.edit.tags') as $pl)
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