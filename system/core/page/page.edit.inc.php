<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * Edit page.
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['auth_read']);

$id = sed_import('id','G','INT');
$c = sed_import('c','G','TXT');

// Extra fields - getting
$extrafields = array();
$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='pages'");
while($row = sed_sql_fetchassoc($fieldsres)) $extrafields[] = $row;

if ($a=='update')
{
	$sql1 = sed_sql_query("SELECT page_cat, page_ownerid FROM $db_pages WHERE page_id='$id' LIMIT 1");
	sed_die(sed_sql_numrows($sql1)==0);
	$row1 = sed_sql_fetcharray($sql1);

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $row1['page_cat']);
	sed_block($usr['isadmin']);

	/* === Hook === */
	$extp = sed_getextplugins('page.edit.update.first');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$rpagekey = sed_import('rpagekey','P','TXT');
	$rpagealias = sed_import('rpagealias','P','ALP');
	$rpagetype = sed_import('rpagetype','P','INT');
	$rpagetitle = sed_import('rpagetitle','P','TXT');
	$rpagedesc = sed_import('rpagedesc','P','TXT');
	$rpagetext = sed_import('rpagetext','P','HTM');
	$rpageauthor = sed_import('rpageauthor','P','TXT');
	$rpageownerid = sed_import('rpageownerid','P','INT');
	$rpagefile = sed_import('rpagefile','P','INT');
	$rpageurl = sed_import('rpageurl','P','TXT');
	$rpagesize = sed_import('rpagesize','P','TXT');
	$rpagecount = sed_import('rpagecount','P','INT');
	$rpagefilecount = sed_import('rpagefilecount','P','INT');
	$rpagecat = sed_import('rpagecat','P','TXT');
	$rpagedatenow = sed_import('rpagedatenow','P','BOL');

	$ryear = sed_import('ryear','P','INT');
	$rmonth = sed_import('rmonth','P','INT');
	$rday = sed_import('rday','P','INT');
	$rhour = sed_import('rhour','P','INT');
	$rminute = sed_import('rminute','P','INT');

	$ryear_beg = sed_import('ryear_beg','P','INT');
	$rmonth_beg = sed_import('rmonth_beg','P','INT');
	$rday_beg = sed_import('rday_beg','P','INT');
	$rhour_beg = sed_import('rhour_beg','P','INT');
	$rminute_beg = sed_import('rminute_beg','P','INT');

	$ryear_exp = sed_import('ryear_exp','P','INT');
	$rmonth_exp = sed_import('rmonth_exp','P','INT');
	$rday_exp = sed_import('rday_exp','P','INT');
	$rhour_exp = sed_import('rhour_exp','P','INT');
	$rminute_exp = sed_import('rminute_exp','P','INT');

	$rpagedelete = sed_import('rpagedelete','P','BOL');

	$error_string .= (empty($rpagecat)) ? $L['pag_catmissing']."<br />" : '';
	$error_string .= (mb_strlen($rpagetitle)<2) ? $L['pag_titletooshort']."<br />" : '';

	if($rpagefile == 0 && !empty($rpageurl))
	{
		$rpagefile = 1;
	}

	// Extra fields
	if(count($extrafields)>0)
	foreach($extrafields as $row)
	{
		$import = sed_import('rpage'.$row['field_name'],'P','HTM');
		if($row['field_type']=="checkbox")
		{
			if ($import == "0") $import = 1;
			if ($import == "") $import = 0;
		}
		$rpageextrafields[] = $import;
	}
	if (empty($error_string) || $rpagedelete)
	{
		if ($rpagedelete)
		{
			$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$id' LIMIT 1");

			if ($row = sed_sql_fetchassoc($sql))
			{
				if ($cfg['trash_page'])
				{ sed_trash_put('page', $L['Page']." #".$id." ".$row['page_title'], $id, $row); }

				if ($row['page_state'] != 1)
				{ $sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".$row['page_cat']."' "); }


				$id2 = "p".$id;
				$sql = sed_sql_query("DELETE FROM $db_pages WHERE page_id='$id'");
				$sql = sed_sql_query("DELETE FROM $db_ratings WHERE rating_code='$id2'");
				$sql = sed_sql_query("DELETE FROM $db_rated WHERE rated_code='$id2'");
				$sql = sed_sql_query("DELETE FROM $db_com WHERE com_code='$id2'");
				sed_log("Deleted page #".$id,'adm');
				/* === Hook === */
				$extp = sed_getextplugins('page.edit.delete.done');
				if (is_array($extp))
				{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
				/* ===== */
				header("Location: " . SED_ABSOLUTE_URL . sed_url('list', "c=".$row1['page_cat'], '', true));
				exit;
			}
		}
		else
		{
			$rpagedate = ($rpagedatenow) ? $sys['now_offset'] : sed_mktime($rhour, $rminute, 0, $rmonth, $rday, $ryear) - $usr['timezone'] * 3600;
			$rpagebegin = sed_mktime($rhour_beg, $rminute_beg, 0, $rmonth_beg, $rday_beg, $ryear_beg) - $usr['timezone'] * 3600;
			$rpageexpire = sed_mktime($rhour_exp, $rminute_exp, 0, $rmonth_exp, $rday_exp, $ryear_exp) - $usr['timezone'] * 3600;
			$rpageexpire = ($rpageexpire<=$rpagebegin) ? $rpagebegin+31536000 : $rpageexpire;

			$rpagetype = ($usr['maingrp']!=5 && $rpagetype==2) ? 0 : $rpagetype;

			if (!empty($rpagealias))
			{
				$sql = sed_sql_query("SELECT page_id FROM $db_pages WHERE page_alias='".sed_sql_prep($rpagealias)."' AND page_id!='".$id."'");
				$rpagealias = (sed_sql_numrows($sql)>0) ? "alias".rand(1000,9999) : $rpagealias;
			}

			if($cfg['parser_cache'])
			{
				$rpagehtml = sed_parse(sed_cc($rpagetext), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], true, true);
			}
			else
			{
				$rpagehtml = '';
			}

			$sql = sed_sql_query("SELECT page_cat FROM $db_pages WHERE page_id='$id' ");
			$row = sed_sql_fetcharray($sql);

			if ($row['page_cat']!=$rpagecat && ($row['page_state'] == 0 || $row['page_state'] == 2))
			{
				$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".$row['page_cat']."' ");
				$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".$rpagecat."' ");
			}

			$ssql = "UPDATE $db_pages SET
			page_cat = '".sed_sql_prep($rpagecat)."',
				page_type = '".sed_sql_prep($rpagetype)."',
				page_key = '".sed_sql_prep($rpagekey)."',";
				if(count($extrafields)>0) foreach($extrafields as $i=>$row) $ssql .= "page_".$row['field_name']." = '".sed_sql_prep($rpageextrafields[$i])."',"; // Extra fields
		$ssql.="page_title = '".sed_sql_prep($rpagetitle)."',
				page_desc = '".sed_sql_prep($rpagedesc)."',
				page_text='".sed_sql_prep($rpagetext)."',
				page_html='".sed_sql_prep($rpagehtml)."',
				page_author = '".sed_sql_prep($rpageauthor)."',
			page_ownerid = '$rpageownerid',
			page_date = '$rpagedate',
			page_begin = '$rpagebegin',
			page_expire = '$rpageexpire',
			page_file = '".sed_sql_prep($rpagefile)."',
				page_url = '".sed_sql_prep($rpageurl)."',
				page_size = '".sed_sql_prep($rpagesize)."',
			page_count = '$rpagecount',
			page_filecount = '$rpagefilecount',
			page_alias = '".sed_sql_prep($rpagealias)."'
			WHERE page_id='$id'";
			$sql = sed_sql_query($ssql);

			/* === Hook === */
			$extp = sed_getextplugins('page.edit.update.done');
			if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
			/* ===== */

			sed_log("Edited page #".$id,'adm');
			header("Location: " . SED_ABSOLUTE_URL . sed_url('page', "id=".$id, '', true));
			exit;
		}
	}
}

$sql = sed_sql_query("SELECT * FROM $db_pages WHERE page_id='$id' LIMIT 1");
sed_die(sed_sql_numrows($sql)==0);
$pag = sed_sql_fetcharray($sql);

$pag['page_date'] = sed_selectbox_date($pag['page_date'] + $usr['timezone'] * 3600,'long');
$pag['page_begin'] = sed_selectbox_date($pag['page_begin'] + $usr['timezone'] * 3600, 'long', '_beg');
$pag['page_expire'] = sed_selectbox_date($pag['page_expire'] + $usr['timezone'] * 3600, 'long', '_exp');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $pag['page_cat']);
sed_block($usr['isadmin']);

/* === Hook === */
$extp = sed_getextplugins('page.edit.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$page_form_delete = "<input type=\"radio\" class=\"radio\" name=\"rpagedelete\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rpagedelete\" value=\"0\" checked=\"checked\" />".$L['No'];
$page_form_categories = sed_selectbox_categories($pag['page_cat'], 'rpagecat');

$page_form_type = "<select name=\"rpagetype\" size=\"1\">";
$selected0 = ($pag['page_type']==0) ? "selected=\"selected\"" : '';
$selected1 = ($pag['page_type']==1) ? "selected=\"selected\"" : '';
$selected2 = ($pag['page_type']==2 && $usr['maingrp']==5) ? "selected=\"selected\"" : '';
$page_form_type .= "<option value=\"0\" $selected0>".$L['Default']."</option>";
$page_form_type .= "<option value=\"1\" $selected1>HTML</option>";
$page_form_type .= ($usr['maingrp']==5 && $cfg['allowphp_pages'] && $cfg['allowphp_override']) ? "<option value=\"2\" $selected2>PHP</option>" : '';
$page_form_type .= "</select>";

switch($pag['page_file'])
{
	case 1:
		$sel0 = '';
		$sel1 = ' selected="selected"';
		$sel2 = '';
	break;

	case 2:
		$sel0 = '';
		$sel1 = '';
		$sel2 = ' selected="selected"';
	break;

	default:
		$sel0 = ' selected="selected"';
		$sel1 = '';
		$sel2 = '';
	break;
}
$page_form_file = <<<HTM
<select name="rpagefile">
<option value="0"$sel0>{$L['No']}</option>
<option value="1"$sel1>{$L['Yes']}</option>
<option value="2"$sel2>{$L['Members_only']}</option>
</select>
HTM;

$bbcodes = ($cfg['parsebbcodepages']) ? sed_build_bbcodes('update', 'rpagetext', $L['BBcodes']) : '';
$smilies = ($cfg['parsesmiliespages']) ? sed_build_smilies('update', 'rpagetext', $L['Smilies']) : '';
$pfs = sed_build_pfs($usr['id'], 'update', 'rpagetext', $L['Mypfs']);
$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, 'update', 'rpagetext', $L['SFS']) : '';
$pfs_form_url_myfiles = (!$cfg['disable_pfs']) ? sed_build_pfs($usr['id'], "update", "rpageurl", $L['Mypfs']) : '';
$pfs_form_url_myfiles .= (sed_auth('pfs', 'a', 'A')) ? ' '.sed_build_pfs(0, 'update', 'rpageurl', $L['SFS']) : '';

$title_tags[] = array('{TITLE}', '{CATEGORY}');
$title_tags[] = array('%1$s', '%1$s');
$title_data = array($L['paged_title'], $sed_cat[$c]['title']);
$out['subtitle'] = sed_title('title_page', $title_tags, $title_data);
$sys['sublocation'] = $sed_cat[$c]['title'];

/* === Hook === */
$extp = sed_getextplugins('page.edit.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('page', 'edit', $sed_cat[$pag['page_cat']]['tpl']));
$t = new XTemplate($mskin);

if (!empty($error_string))
{
	$t->assign("PAGEEDIT_ERROR_BODY",$error_string);
	$t->parse("MAIN.PAGEEDIT_ERROR");
}

$pageedit_array = array(
	"PAGEEDIT_PAGETITLE" => $L['paged_title'],
	"PAGEEDIT_SUBTITLE" => $L['paged_subtitle'],
	"PAGEEDIT_FORM_SEND" => sed_url('page', "m=edit&a=update&id=".$pag['page_id']."&r=".$r),
	"PAGEEDIT_FORM_ID" => $pag['page_id'],
	"PAGEEDIT_FORM_STATE" => $pag['page_state'],
	"PAGEEDIT_FORM_TYPE" => $page_form_type,
	"PAGEEDIT_FORM_CAT" => $page_form_categories,
	"PAGEEDIT_FORM_KEY" => "<input type=\"text\" class=\"text\" name=\"rpagekey\" value=\"".sed_cc($pag['page_key'])."\" size=\"16\" maxlength=\"16\" />",
	"PAGEEDIT_FORM_ALIAS" => "<input type=\"text\" class=\"text\" name=\"rpagealias\" value=\"".sed_cc($pag['page_alias'])."\" size=\"16\" maxlength=\"255\" />",
	"PAGEEDIT_FORM_TITLE" => "<input type=\"text\" class=\"text\" name=\"rpagetitle\" value=\"".sed_cc($pag['page_title'])."\" size=\"56\" maxlength=\"255\" />",
	"PAGEEDIT_FORM_DESC" => "<input type=\"text\" class=\"text\" name=\"rpagedesc\" value=\"".sed_cc($pag['page_desc'])."\" size=\"56\" maxlength=\"255\" />",
	"PAGEEDIT_FORM_AUTHOR" => "<input type=\"text\" class=\"text\" name=\"rpageauthor\" value=\"".sed_cc($pag['page_author'])."\" size=\"32\" maxlength=\"24\" />",
	"PAGEEDIT_FORM_OWNERID" => "<input type=\"text\" class=\"text\" name=\"rpageownerid\" value=\"".sed_cc($pag['page_ownerid'])."\" size=\"32\" maxlength=\"24\" />",
	"PAGEEDIT_FORM_DATE" => $pag['page_date']." ".$usr['timetext'],
	"PAGEEDIT_FORM_DATENOW" => "<input type=\"checkbox\" class=\"checkbox\" name=\"rpagedatenow\" value=\"1\" />",
	"PAGEEDIT_FORM_BEGIN" => $pag['page_begin']." ".$usr['timetext'],
	"PAGEEDIT_FORM_EXPIRE" => $pag['page_expire']." ".$usr['timetext'],
	"PAGEEDIT_FORM_FILE" => $page_form_file,
	"PAGEEDIT_FORM_URL" => "<input type=\"text\" class=\"text\" name=\"rpageurl\" value=\"".sed_cc($pag['page_url'])."\" size=\"56\" maxlength=\"255\" /> ",
	"PAGEEDIT_FORM_SIZE" => "<input type=\"text\" class=\"text\" name=\"rpagesize\" value=\"".sed_cc($pag['page_size'])."\" size=\"56\" maxlength=\"255\" />",
	"PAGEEDIT_FORM_PAGECOUNT" => "<input type=\"text\" class=\"text\" name=\"rpagecount\" value=\"".$pag['page_count']."\" size=\"8\" maxlength=\"8\" />",
	"PAGEEDIT_FORM_FILECOUNT" => "<input type=\"text\" class=\"text\" name=\"rpagefilecount\" value=\"".$pag['page_filecount']."\" size=\"8\" maxlength=\"8\" />",
	"PAGEEDIT_FORM_TEXT" => "<textarea class=\"editor\" name=\"rpagetext\" rows=\"24\" cols=\"120\">".sed_cc($pag['page_text'])."</textarea><br />".$bbcodes." ".$smilies,
	"PAGEEDIT_FORM_TEXTBOXER" => "<textarea class=\"editor\" name=\"rpagetext\" rows=\"24\" cols=\"120\">".sed_cc($pag['page_text'])."</textarea><br />".$bbcodes." ".$smilies,
	"PAGEEDIT_FORM_BBCODES" => $bbcodes,
	"PAGEEDIT_FORM_SMILIES" => $smilies,
	"PAGEEDIT_FORM_MYPFS" => $pfs,
	"PAGEEDIT_FORM_DELETE" => $page_form_delete
);

// PFS tags
$tplskin = file_get_contents($mskin);
preg_match_all("#\{(PAGEEDIT_FORM_PFS_([^\}]*?)_USER)\}#", $tplskin, $match);
$numtags = count($match[0]);
for($i = 0; $i<$numtags; $i++)
{
	$tag = $match[1][$i];
	$field = strtolower($match[2][$i]);
	$pfs_js = (!$cfg['disable_pfs']) ? sed_build_pfs($usr['id'], "update", "rpage$field", $L['Mypfs']) : '';
	$pageedit_array[$tag] = $pfs_js;
}
unset($match);
preg_match_all("#\{(PAGEEDIT_FORM_PFS_([^\}]*?)_SITE)\}#", $tplskin, $match);
$numtags = count($match[0]);
for($i = 0; $i<$numtags; $i++)
{
	$tag = $match[1][$i];
	$field = strtolower($match[2][$i]);
	$pfs_js = (sed_auth('pfs', 'a', 'A')) ? ' '.sed_build_pfs(0, "update", "rpage$field", $L['SFS']) : '';
	$pageedit_array[$tag] = $pfs_js;
}

// Extra fields
if(count($extrafields)>0)
foreach($extrafields as $i=>$row)
{
	isset($L['page_'.$row['field_name'].'_title']) ? $t->assign('PAGEEDIT_FORM_'.strtoupper($row['field_name']).'_TITLE', $L['page_'.$row['field_name'].'_title']) : $t->assign('PAGEEDIT_FORM_'.strtoupper($row['field_name']).'_TITLE', $row['field_description']);
	$t1 = "PAGEEDIT_FORM_".strtoupper($row['field_name']);
	$t2 = $row['field_html'];
	switch($row['field_type']) {
	case "input":
		$t2 = str_replace('<input ','<input name="rpage'.$row['field_name'].'" ', $t2);
		$t2 = str_replace('<input ','<input value="'.$pag['page_'.$row['field_name']].'" ', $t2); break;
	case "textarea":
		$t2 = str_replace('<textarea ','<textarea name="rpage'.$row['field_name'].'" ', $t2);
		$t2 = str_replace('</textarea>',$pag['page_'.$row['field_name']].'</textarea>', $t2); break;
	case "select":
		$t2 = str_replace('<select','<select name="rpage'.$row['field_name'].'"', $t2);
		$options = "";
		$opt_array = explode(",",$row['field_variants']);
		if(count($opt_array)!=0)
			foreach ($opt_array as $var)
			{
				$sel = $var == $pag['page_'.$row['field_name']] ? ' selected="selected"' : '';
				$options .= "<option value=\"$var\" $sel>$var</option>";

			}
		$t2 = str_replace("</select>","$options</select>",$t2); break;
	case "checkbox":
		$t2 = str_replace('<input','<input name="rpage'.$row['field_name'].'"', $t2);
		$sel = $pag['page_'.$row['field_name']]==1 ? ' checked' : '';
		$t2 = str_replace('<input ','<input value="'.$pag['page_'.$row['field_name']].'" '.$sel.' ', $t2); break;
	}
	$pageedit_array[$t1] = $t2;
}
$t->assign($pageedit_array);
/* === Hook === */
$extp = sed_getextplugins('page.edit.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>