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

require_once cot_incfile('forms');

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
	$sql1 = $db->query("SELECT page_cat, page_ownerid FROM $db_pages WHERE page_id='$id' LIMIT 1");
	cot_die($sql1->rowCount() == 0);
	$row1 = $sql1->fetch();

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', $row1['page_cat']);

	/* === Hook === */
	foreach (cot_getextplugins('page.edit.update.first') as $pl)
	{
		include $pl;
	}
	/* ===== */
	cot_block($usr['isadmin'] || $usr['auth_write'] && $usr['id'] == $row1['page_ownerid']);

	$rpage['page_key'] = cot_import('rpagekey', 'P', 'TXT');
	$rpage['page_alias'] = cot_import('rpagealias', 'P', 'ALP');
	$rpage['page_title'] = cot_import('rpagetitle', 'P', 'TXT');
	$rpage['page_desc'] = cot_import('rpagedesc', 'P', 'TXT');
	$rpage['page_text'] = cot_import('rpagetext', 'P', 'HTM');
	$rpage['page_author'] = cot_import('rpageauthor', 'P', 'TXT');
	$rpage['page_file'] = cot_import('rpagefile', 'P', 'INT');
	$rpage['page_url'] = cot_import('rpageurl', 'P', 'TXT');
	$rpage['page_size'] = cot_import('rpagesize', 'P', 'TXT');
	$rpage['page_file'] = ($rpage['page_file'] == 0 && !empty($rpage['page_url'])) ? 1 : $rpage['page_file'];

	$rpage['page_cat'] = cot_import('rpagecat', 'P', 'TXT');

	$rpagedatenow = cot_import('rpagedatenow', 'P', 'BOL');
	$rpage['page_date'] = ($rpagedatenow) ? $sys['now_offset'] : (int)cot_import_date('rpagedate');
	$rpage['page_begin'] = (int)cot_import_date('rpagebegin');
	$rpage['page_expire'] = (int)cot_import_date('rpageexpire');
	$rpage['page_expire'] = ($rpage['page_expire'] <= $rpage['page_begin']) ? $rpage['page_begin'] + 31536000 : $rpage['page_expire'];

	// Extra fields
	foreach ($cot_extrafields['pages'] as $row)
	{
		$rpage[$row['field_name']] = cot_import_extrafields('rpage'.$row['field_name'], $row);
	}

	if ($usr['isadmin'])
	{
		$rpage['page_count'] = cot_import('rpagecount', 'P', 'INT');
		$rpage['page_ownerid'] = cot_import('rpageownerid', 'P', 'INT');
		$rpage['page_filecount'] = cot_import('rpagefilecount', 'P', 'INT');
	}
	$rpagedelete = cot_import('rpagedelete', 'P', 'BOL');

	if (empty($rpage['page_cat'])) cot_error('page_catmissing', 'rpagecat');
	if (mb_strlen($rpage['page_title']) < 2) cot_error('page_titletooshort', 'rpagetitle');

	/* === Hook === */
	foreach (cot_getextplugins('page.edit.update.error') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if($rpagedelete)
	{
		$sql = $db->query("SELECT * FROM $db_pages WHERE page_id='$id' LIMIT 1");

		if ($row = $sql->fetch())
		{
			if ($row['page_state'] != 1)
			{
				$sql = $db->query("UPDATE $db_structure SET structure_count=structure_count-1 WHERE structure_code='".$row['page_cat']."' ");
			}

			$id2 = "p".$id;
			$sql = $db->delete($db_pages, "page_id='$id'");
			$sql = $db->delete($db_ratings, "rating_code='$id2'");
			$sql = $db->delete($db_rated, "rated_code='$id2'");
			cot_log("Deleted page #".$id,'adm');
			/* === Hook === */
			foreach (cot_getextplugins('page.edit.delete.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			if ($cache)
			{
				if ($cfg['cache_page'])
				{
					$cache->page->clear('page/' . str_replace('.', '/', $cot_cat[$row['page_cat']]['path']));
				}
				if ($cfg['cache_index'])
				{
					$cache->page->clear('index');
				}
			}
			cot_redirect(cot_url('page', "c=".$row1['page_cat'], '', true));
		}
	}
	elseif (!$cot_error)
	{
		if (!empty($rpage['page_alias']))
		{
			$sql = $db->query("SELECT page_id FROM $db_pages WHERE page_alias='".$db->prep($rpage['page_alias'])."' AND page_id!='".$id."'");
			$rpage['page_alias'] = ($sql->rowCount() > 0) ? $rpage['page_alias'].rand(1000, 9999) : $rpage['page_alias'];
		}

		$sql = $db->query("SELECT page_cat, page_state FROM $db_pages WHERE page_id='$id' ");
		$row = $sql->fetch();

		if ($row['page_cat'] != $rpage['page_cat'] /*&& ($row['page_state'] == 0 || $row['page_state'] == 2)*/)
		{
			$sql = $db->query("UPDATE $db_structure SET structure_count=structure_count-1 WHERE structure_code='".$db->prep($row['page_cat'])."' ");
			//$sql = $db->query("UPDATE $db_structure SET structure_count=structure_count+1 WHERE structure_code='".$db->prep($rpage['page_cat)."' ");
		}

		//$usr['isadmin'] = cot_auth('page', $rpage['page_cat'], 'A');
		if ($usr['isadmin'] && $cfg['page']['autovalidate'])
		{
			$rpublish = cot_import('rpublish', 'P', 'ALP');
			if ($rpublish == 'OK' )
			{
				$rpage['page_state'] = 0;
				if ($row['page_state'] == 1)
				{
					$db->query("UPDATE $db_structure SET structure_count=structure_count+1 WHERE structure_code='".$db->prep($rpage['page_cat'])."' ");
				}
			}
			else
			{
				$rpage['page_state'] = 1;
			}
		}
		else
		{
			$rpage['page_state'] = 1;
		}
		if ($rpage['page_state'] == 1 && $row['page_state'] != 1)
		{
			$db->query("UPDATE $db_structure SET structure_count=structure_count-1 WHERE structure_code='".$db->prep($rpage['page_cat'])."' ");
		}

		$sql = $db->update($db_pages, $rpage, 'page_id=?', array($id));
		/* === Hook === */
		foreach (cot_getextplugins('page.edit.update.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($rpage['page_state'] == 0 && $cache)
		{
			if ($cfg['cache_page'])
			{
				$cache->page->clear('page/' . str_replace('.', '/', $cot_cat[$rpage['page_cat']]['path']));
			}
			if ($cfg['cache_index'])
			{
				$cache->page->clear('index');
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

$sql = $db->query("SELECT * FROM $db_pages WHERE page_id='$id' LIMIT 1");
cot_die($sql->rowCount() == 0);
$pag = $sql->fetch();

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', $pag['page_cat']);
cot_block($usr['isadmin'] || $usr['auth_write'] && $usr['id'] == $pag['page_ownerid']);

$title_params = array(
	'TITLE' => $L['page_edittitle'],
	'CATEGORY' => $cot_cat[$c]['title']
);
$out['subtitle'] = cot_title('title_page', $title_params);
$out['head'] .= $R['code_noindex'];
$sys['sublocation'] = $cot_cat[$c]['title'];

/* === Hook === */
foreach (cot_getextplugins('page.edit.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'].'/header.php';

$mskin = cot_skinfile(array('page', 'edit', $cot_cat[$pag['page_cat']]['tpl']));
$t = new XTemplate($mskin);

$pageedit_array = array(
	"PAGEEDIT_PAGETITLE" => $L['page_edittitle'],
	"PAGEEDIT_SUBTITLE" => $L['page_editsubtitle'],
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
	"PAGEEDIT_FORM_DELETE" => cot_radiobox(0, 'rpagedelete', array(1, 0), array($L['Yes'], $L['No']))
);
if ($usr['isadmin'])
{
	$pageedit_array += array(
		"PAGEEDIT_FORM_OWNERID" => cot_inputbox('text', 'rpageownerid', $pag['page_ownerid'], array('size' => '24', 'maxlength' => '24')),
		"PAGEEDIT_FORM_PAGECOUNT" => cot_inputbox('text', 'rpagecount', $pag['page_count'], array('size' => '8', 'maxlength' => '8')),
		"PAGEEDIT_FORM_FILECOUNT" => cot_inputbox('text', 'rpagefilecount', $pag['page_filecount'], array('size' => '8', 'maxlength' => '8'))
	);
}

$t->assign($pageedit_array);

// Extra fields
foreach($cot_extrafields['pages'] as $i => $row)
{
	$uname = strtoupper($row['field_name']);
	$t->assign('PAGEEDIT_FORM_'.$uname, cot_build_extrafields('rpage'.$row['field_name'], $row, $rpage[$row['field_name']]));
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
	if ($cfg['page']['autovalidate']) $usr_can_publish = TRUE;
	$t->parse('MAIN.ADMIN');
}

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'].'/footer.php';

?>