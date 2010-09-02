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

defined('SED_CODE') or die('Wrong URL');

$id = sed_import('id', 'G', 'INT');
$c = sed_import('c', 'G', 'TXT');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');

/* === Hook === */
foreach (sed_getextplugins('page.edit.first') as $pl)
{
	include $pl;
}
/* ===== */

sed_block($usr['auth_read']);

if ($a == 'update')
{
	$sql1 = sed_sql_query("SELECT page_cat, page_ownerid FROM $db_pages WHERE page_id='$id' LIMIT 1");
	sed_die(sed_sql_numrows($sql1) == 0);
	$row1 = sed_sql_fetcharray($sql1);

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $row1['page_cat']);

	/* === Hook === */
	foreach (sed_getextplugins('page.edit.update.first') as $pl)
	{
		include $pl;
	}
	/* ===== */
	sed_block($usr['isadmin'] || $usr['auth_write'] && $usr['id'] == $row1['page_ownerid']);

	$rpage['key'] = sed_import('rpagekey', 'P', 'TXT');
	$rpage['alias'] = sed_import('rpagealias', 'P', 'ALP');
	$rpage['title'] = sed_import('rpagetitle', 'P', 'TXT');
	$rpage['desc'] = sed_import('rpagedesc', 'P', 'TXT');
	$rpage['text'] = sed_import('rpagetext', 'P', 'HTM');
	$rpage['author'] = sed_import('rpageauthor', 'P', 'TXT');
	$rpage['file'] = sed_import('rpagefile', 'P', 'INT');
	$rpage['url'] = sed_import('rpageurl', 'P', 'TXT');
	$rpage['size'] = sed_import('rpagesize', 'P', 'TXT');
	$rpage['file'] = ($rpage['file'] == 0 && !empty($rpage['url'])) ? 1 : $rpage['file'];

	$rpage['cat'] = sed_import('rpagecat', 'P', 'TXT');

	$rpagedatenow = sed_import('rpagedatenow', 'P', 'BOL');
	$rpage['date'] = ($rpagedatenow) ? $sys['now_offset'] : (int)sed_import_date('rpagedate');
	$rpage['begin'] = (int)sed_import_date('rpagebegin');
	$rpage['expire'] = (int)sed_import_date('rpageexpire');
	$rpage['expire'] = ($rpage['expire'] <= $rpage['begin']) ? $rpage['begin'] + 31536000 : $rpage['expire'];

	// Extra fields
	foreach ($sed_extrafields['pages'] as $row)
	{
		$rpage[$row['field_name']] = sed_import_extrafields('page', $row);
	}

	if ($usr['isadmin'])
	{
		$rpage['type'] = sed_import('rpagetype', 'P', 'INT');
		$rpage['count'] = sed_import('rpagecount', 'P', 'INT');
		$rpage['ownerid'] = sed_import('rpageownerid', 'P', 'INT');
		$rpage['filecount'] = sed_import('rpagefilecount', 'P', 'INT');
	}
	$rpagedelete = sed_import('rpagedelete', 'P', 'BOL');

	if (empty($rpage['cat'])) sed_error('pag_catmissing', 'rpagecat');
	if (mb_strlen($rpage['title']) < 2) sed_error('pag_titletooshort', 'rpagetitle');

	/* === Hook === */
	foreach (sed_getextplugins('page.edit.update.error') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if($rpagedelete)
	{
		$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$id' LIMIT 1");

		if ($row = sed_sql_fetchassoc($sql))
		{
			if ($cfg['trash_page'])
			{
				sed_trash_put('page', $L['Page']." #".$id." ".$row['page_title'], $id, $row);
			}

			if ($row['page_state'] != 1)
			{
				$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".$row['page_cat']."' ");
			}

			$id2 = "p".$id;
			$sql = sed_sql_delete($db_pages, "page_id='$id'");
			$sql = sed_sql_delete($db_ratings, "rating_code='$id2'");
			$sql = sed_sql_delete($db_rated, "rated_code='$id2'");
			sed_log("Deleted page #".$id,'adm');
			/* === Hook === */
			foreach (sed_getextplugins('page.edit.delete.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			if ($cot_cache)
			{
				if ($cfg['cache_page'])
				{
					$cot_cache->page->clear('page/' . str_replace('.', '/', $sed_cat[$row['page_cat']]['path']));
				}
				if ($cfg['cache_index'])
				{
					$cot_cache->page->clear('index');
				}
			}
			sed_redirect(sed_url('list', "c=".$row1['page_cat'], '', true));
		}
	}
	elseif (!$cot_error)
	{
		$rpage['type'] = ($usr['maingrp'] != 5 && $rpage['type'] == 2) ? 0 : $rpage['type'];

		if (!empty($rpage['alias']))
		{
			$sql = sed_sql_query("SELECT page_id FROM $db_pages WHERE page_alias='".sed_sql_prep($rpage['alias'])."' AND page_id!='".$id."'");
			$rpage['alias'] = (sed_sql_numrows($sql) > 0) ? $rpage['alias'].rand(1000, 9999) : $rpage['alias'];
		}

		$rpage['html'] = ($cfg['parser_cache'] && $rpage['type'] != 1) ? sed_parse(htmlspecialchars($rpage['text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], true, true) : '';

		$sql = sed_sql_query("SELECT page_cat, page_state FROM $db_pages WHERE page_id='$id' ");
		$row = sed_sql_fetcharray($sql);

		if ($row['page_cat'] != $rpage['cat'] /*&& ($row['page_state'] == 0 || $row['page_state'] == 2)*/)
		{
			$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".sed_sql_prep($row['page_cat'])."' ");
			//$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".sed_sql_prep($rpage['cat)."' ");
		}

		//$usr['isadmin'] = sed_auth('page', $rpage['cat'], 'A');
		if ($usr['isadmin'] && $cfg['autovalidate'])
		{
			$rpublish = sed_import('rpublish', 'P', 'ALP');
			if ($rpublish == 'OK' )
			{
				$rpage['state'] = 0;
				if ($row['page_state'] == 1)
				{
					sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".sed_sql_prep($rpage['cat'])."' ");
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
			sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".sed_sql_prep($rpage['cat'])."' ");
		}

		$sql = sed_sql_update($db_pages, $rpage, "page_id='$id'", 'page_');
		/* === Hook === */
		foreach (sed_getextplugins('page.edit.update.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($rpage['state'] == 0 && $cot_cache)
		{
			if ($cfg['cache_page'])
			{
				$cot_cache->page->clear('page/' . str_replace('.', '/', $sed_cat[$rpage['cat']]['path']));
			}
			if ($cfg['cache_index'])
			{
				$cot_cache->page->clear('index');
			}
		}

		sed_log("Edited page #".$id,'adm');
		sed_redirect(sed_url('page', "id=".$id, '', true));
	}
	else
	{
		sed_redirect(sed_url('page', "m=edit&id=$id", '', true));
	}
}

$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$id' LIMIT 1");
sed_die(sed_sql_numrows($sql) == 0);
$pag = sed_sql_fetcharray($sql);

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $pag['page_cat']);
sed_block($usr['isadmin'] || $usr['auth_write'] && $usr['id'] == $pag['page_ownerid']);

$title_params = array(
	'TITLE' => $L['paged_title'],
	'CATEGORY' => $sed_cat[$c]['title']
);
$out['subtitle'] = sed_title('title_page', $title_params);
$out['head'] .= $R['code_noindex'];
$sys['sublocation'] = $sed_cat[$c]['title'];
sed_online_update();

/* === Hook === */
foreach (sed_getextplugins('page.edit.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'].'/header.php';

$mskin = sed_skinfile(array('page', 'edit', $sed_cat[$pag['page_cat']]['tpl']));
$t = new XTemplate($mskin);

sed_require_api('forms');

$page_type_options = array('0' => $L['Default'], '1' => 'HTML');
if ($usr['maingrp'] == 5 && $cfg['allowphp_pages'] && $cfg['allowphp_override'])
{
	$page_type_options += array('2' => 'PHP');
}

// FIXME PFS dependency
//$pfs = sed_build_pfs($usr['id'], 'update', 'rpagetext', $L['Mypfs']);
//$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, 'update', 'rpagetext', $L['SFS']) : '';
//$pfs_form_url_myfiles = (!$cfg['disable_pfs']) ? sed_build_pfs($usr['id'], "update", "rpageurl", $L['Mypfs']) : '';
//$pfs_form_url_myfiles .= (sed_auth('pfs', 'a', 'A')) ? ' '.sed_build_pfs(0, 'update', 'rpageurl', $L['SFS']) : '';

$pageedit_array = array(
	"PAGEEDIT_PAGETITLE" => $L['paged_title'],
	"PAGEEDIT_SUBTITLE" => $L['paged_subtitle'],
	"PAGEEDIT_FORM_SEND" => sed_url('page', "m=edit&a=update&id=".$pag['page_id']."&r=".$r),
	"PAGEEDIT_FORM_ID" => $pag['page_id'],
	"PAGEEDIT_FORM_STATE" => $pag['page_state'],
	"PAGEEDIT_FORM_CAT" => sed_selectbox_categories($pag['page_cat'], 'rpagecat'),
	"PAGEEDIT_FORM_CAT_SHORT" => sed_selectbox_categories($pag['page_cat'], 'rpagecat', $c),
	"PAGEEDIT_FORM_KEY" => sed_inputbox('text', 'rpagekey', $pag['page_key'], array('size' => '16', 'maxlength' => '16')),
	"PAGEEDIT_FORM_ALIAS" => sed_inputbox('text', 'rpagealias', $pag['page_alias'], array('size' => '32', 'maxlength' => '255')),
	"PAGEEDIT_FORM_TITLE" => sed_inputbox('text', 'rpagetitle', $pag['page_title'], array('size' => '64', 'maxlength' => '255')),
	"PAGEEDIT_FORM_DESC" => sed_inputbox('text', 'rpagedesc', $pag['page_desc'], array('size' => '64', 'maxlength' => '255')),
	"PAGEEDIT_FORM_AUTHOR" => sed_inputbox('text', 'rpageauthor', $pag['page_author'], array('size' => '24', 'maxlength' => '24')),
	"PAGEEDIT_FORM_DATE" => sed_selectbox_date($pag['page_date'],'long', 'rpagedate').' '.$usr['timetext'],
	"PAGEEDIT_FORM_DATENOW" => sed_checkbox(0, 'rpagedatenow'),
	"PAGEEDIT_FORM_BEGIN" => sed_selectbox_date($pag['page_begin'], 'long', 'rpagebegin').' '.$usr['timetext'],
	"PAGEEDIT_FORM_EXPIRE" => sed_selectbox_date($pag['page_expire'], 'long', 'rpageexpire').' '.$usr['timetext'],
	"PAGEEDIT_FORM_FILE" => sed_selectbox($pag['page_file'], 'rpagefile', range(0, 2), array($L['No'], $L['Yes'], $L['Members_only']), false),
	"PAGEEDIT_FORM_URL" => sed_inputbox('text', 'rpageurl', $pag['page_url'], array('size' => '56', 'maxlength' => '255')),
	"PAGEEDIT_FORM_SIZE" => sed_inputbox('text', 'rpagesize', $pag['page_size'], array('size' => '56', 'maxlength' => '255')),
	"PAGEEDIT_FORM_TEXT" => sed_textarea('rpagetext', $pag['page_text'], 24, 120, '', 'input_textarea_editor'),
	"PAGEEDIT_FORM_MYPFS" => $pfs,
	"PAGEEDIT_FORM_DELETE" => sed_radiobox(0, 'rpagedelete', array(1, 0), array($L['Yes'], $L['No']))
);
if ($usr['isadmin'])
{
	$pageedit_array = array(
		"PAGEEDIT_FORM_TYPE" => sed_selectbox($pag['page_type'], 'rpagetype', array_keys($page_type_options), array_values($page_type_options), false),
		"PAGEEDIT_FORM_OWNERID" => sed_inputbox('text', 'rpageownerid', $pag['page_ownerid'], array('size' => '24', 'maxlength' => '24')),
		"PAGEEDIT_FORM_PAGECOUNT" => sed_inputbox('text', 'rpagecount', $pag['page_count'], array('size' => '8', 'maxlength' => '8')),
		"PAGEEDIT_FORM_FILECOUNT" => sed_inputbox('text', 'rpagefilecount', $pag['page_filecount'], array('size' => '8', 'maxlength' => '8'))
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
//	$pfs_js = (!$cfg['disable_pfs']) ? sed_build_pfs($usr['id'], "update", "rpage$field", $L['Mypfs']) : '';
//	$pageedit_array[$tag] = $pfs_js;
//}
//unset($match);
//preg_match_all("#\{(PAGEEDIT_FORM_PFS_([^\}]*?)_SITE)\}#", $tplskin, $match);
//$numtags = count($match[0]);
//for($i = 0; $i < $numtags; $i++)
//{
//	$tag = $match[1][$i];
//	$field = strtolower($match[2][$i]);
//	$pfs_js = (sed_auth('pfs', 'a', 'A')) ? ' '.sed_build_pfs(0, "update", "rpage$field", $L['SFS']) : '';
//	$pageedit_array[$tag] = $pfs_js;
//}
$t->assign($pageedit_array);

// Extra fields
foreach($sed_extrafields['pages'] as $i => $row)
{
	$uname = strtoupper($row['field_name']);
	$t->assign('PAGEEDIT_FORM_'.$uname, sed_build_extrafields('page', $row, $rpage[$row['field_name']]));
	$t->assign('PAGEEDIT_FORM_'.$uname.'_TITLE', isset($L['page_'.$row['field_name'].'_title']) ?  $L['page_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Error and message handling
if (sed_check_messages())
{
	$t->assign('PAGEEDIT_ERROR_BODY', sed_implode_messages());
	$t->parse('MAIN.PAGEEDIT_ERROR');
	sed_clear_messages();
}

/* === Hook === */
foreach (sed_getextplugins('page.edit.tags') as $pl)
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