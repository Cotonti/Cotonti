<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * Add page.
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

$id = sed_import('id','G','INT');
$r = sed_import('r','G','ALP');
$c = sed_import('c','G','ALP');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');

/* === Hook === */
$extp = sed_getextplugins('page.add.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */
sed_block($usr['auth_write']);


// Extra fields - getting
$extrafields = array(); $number_of_extrafields = 0;
$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='pages'");
while($row = sed_sql_fetchassoc($fieldsres)) { $extrafields[] = $row; $number_of_extrafields++; }

if ($a=='add')
{
	sed_shield_protect();

	/* === Hook === */
	$extp = sed_getextplugins('page.add.add.first');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$newpagecat = sed_import('newpagecat','P','TXT');
	$newpagekey = sed_import('newpagekey','P','TXT');
	$newpagealias = sed_import('newpagealias','P','ALP');
	$newpagetitle = sed_import('newpagetitle','P','TXT');
	$newpagedesc = sed_import('newpagedesc','P','TXT');
	$newpagetext = sed_import('newpagetext','P','HTM');
	$newpageauthor = sed_import('newpageauthor','P','TXT');
	$newpagefile = sed_import('newpagefile','P','INT');
	$newpageurl = sed_import('newpageurl','P','TXT');
	$newpagesize = sed_import('newpagesize','P','TXT');
	$newpageyear_beg = sed_import('ryear_beg','P','INT');
	$newpagemonth_beg = sed_import('rmonth_beg','P','INT');
	$newpageday_beg = sed_import('rday_beg','P','INT');
	$newpagehour_beg = sed_import('rhour_beg','P','INT');
	$newpageminute_beg = sed_import('rminute_beg','P','INT');
	$newpageyear_exp = sed_import('ryear_exp','P','INT');
	$newpagemonth_exp = sed_import('rmonth_exp','P','INT');
	$newpageday_exp = sed_import('rday_exp','P','INT');
	$newpagehour_exp = sed_import('rhour_exp','P','INT');
	$newpageminute_exp = sed_import('rminute_exp','P','INT');

	$newpagebegin = sed_mktime($newpagehour_beg, $newpageminute_beg, 0, $newpagemonth_beg, $newpageday_beg, $newpageyear_beg) - $usr['timezone'] * 3600;
	$newpageexpire = sed_mktime($newpagehour_exp, $newpageminute_exp, 0, $newpagemonth_exp, $newpageday_exp, $newpageyear_exp) - $usr['timezone'] * 3600;
	$newpageexpire = ($newpageexpire<=$newpagebegin) ? $newpagebegin+31536000 : $newpageexpire;

	// Extra fields
	if($number_of_extrafields > 0)
	foreach($extrafields as $row)
	{
		$import = sed_import('newpage'.$row['field_name'],'P','HTM');
		if($row['field_type']=="checkbox")
		{
			if ($import == "0" OR $import == "on") $import = 1;
			else $import = 0;
		}
		$newpageextrafields[] = $import;
	}
	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $newpagecat);
	sed_block($usr['auth_write']);

	$error_string .= (empty($newpagecat)) ? $L['pag_catmissing']."<br />" : '';
	$error_string .= (mb_strlen($newpagetitle)<2) ? $L['pag_titletooshort']."<br />" : '';

	if($newpagefile == 0 && !empty($newpageurl))
	{
		$newpagefile = 1;
	}

	if (empty($error_string))
	{
		if (!empty($newpagealias))
		{
			$sql = sed_sql_query("SELECT page_id FROM $db_pages WHERE page_alias='".sed_sql_prep($newpagealias)."'");
			$newpagealias = (sed_sql_numrows($sql)>0) ? "alias".rand(1000,9999) : $newpagealias;
		}

		if($cfg['parser_cache'])
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
			if ($rpublish == 'OK' )
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
		$extp = sed_getextplugins('page.add.add.query');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		$ssql = "INSERT into $db_pages
		(page_state,
		page_type,
		page_cat,
		page_key,";
		if($number_of_extrafields > 0) foreach($extrafields as $row) $ssql .= "page_".$row['field_name'].", "; // Extra fields
$ssql.="page_title,
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
		page_alias)
		VALUES
		(".(int)$page_state.",
		0,
		'".sed_sql_prep($newpagecat)."',
			'".sed_sql_prep($newpagekey)."',";
			if($number_of_extrafields > 0) foreach($newpageextrafields as $newpageextrafield) $ssql.= "'".sed_sql_prep($newpageextrafield)."',"; // Extra fields
  	$ssql.="'".sed_sql_prep($newpagetitle)."',
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
			'".sed_sql_prep($newpagealias)."')";
  		$sql = sed_sql_query($ssql);

		$id = sed_sql_insertid();
		$r_url = (!$page_state) ? sed_url('page', "id=".$id, '', true) : sed_url('message', "msg=300", '', true);

		/* === Hook === */
		$extp = sed_getextplugins('page.add.add.done');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		sed_shield_update(30, "New page");
		header("Location: " . SED_ABSOLUTE_URL . $r_url);
		exit;
	}
}

switch($newpagefile)
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
$pageadd_form_file = <<<HTM
<select name="newpagefile">
<option value="0"$sel0>{$L['No']}</option>
<option value="1"$sel1>{$L['Yes']}</option>
<option value="2"$sel2>{$L['Members_only']}</option>
</select>
HTM;

$newpagecat = (empty($newpagecat)) ? $c : $newpagecat;
$pageadd_form_categories = sed_selectbox_categories($newpagecat, 'newpagecat');
$newpage_form_begin = sed_selectbox_date($sys['now_offset']+$usr['timezone']*3600, 'long', '_beg');
$newpage_form_expire = sed_selectbox_date($sys['now_offset']+$usr['timezone']*3600 + 31536000, 'long', '_exp');

$pfs = sed_build_pfs($usr['id'], 'newpage', 'newpagetext',$L['Mypfs']);
$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, 'newpage', 'newpagetext', $L['SFS']) : '';
$pfs_form_url_myfiles = (!$cfg['disable_pfs']) ? sed_build_pfs($usr['id'], "newpage", "newpageurl", $L['Mypfs']) : '';
$pfs_form_url_myfiles .= (sed_auth('pfs', 'a', 'A')) ? ' '.sed_build_pfs(0, 'newpage', 'newpageurl', $L['SFS']) : '';

$title_tags[] = array('{TITLE}', '{CATEGORY}');
$title_tags[] = array('%1$s', '%1$s');
$title_data = array($L['pagadd_subtitle'], $sed_cat[$c]['title']);
$out['subtitle'] = sed_title('title_page', $title_tags, $title_data);
$sys['sublocation'] = $sed_cat[$c]['title'];

/* === Hook === */
$extp = sed_getextplugins('page.add.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('page', 'add', $sed_cat[$newpagecat]['tpl']));
$t = new XTemplate($mskin);

if (!empty($error_string))
{
	$t->assign("PAGEADD_ERROR_BODY",$error_string);
	$t->parse("MAIN.PAGEADD_ERROR");
}

$pageadd_array = array(
	"PAGEADD_PAGETITLE" => $L['pagadd_title'],
	"PAGEADD_SUBTITLE" => $L['pagadd_subtitle'],
	"PAGEADD_ADMINEMAIL" => "mailto:".$cfg['adminemail'],
	"PAGEADD_FORM_SEND" => sed_url('page', "m=add&a=add"),
	"PAGEADD_FORM_CAT" => $pageadd_form_categories,
	"PAGEADD_FORM_KEY" => "<input type=\"text\" class=\"text\" name=\"newpagekey\" value=\"".htmlspecialchars($newpagekey)."\" size=\"16\" maxlength=\"16\" />",
	"PAGEADD_FORM_ALIAS" => "<input type=\"text\" class=\"text\" name=\"newpagealias\" value=\"".htmlspecialchars($newpagealias)."\" size=\"16\" maxlength=\"255\" />",
	"PAGEADD_FORM_TITLE" => "<input type=\"text\" class=\"text\" name=\"newpagetitle\" value=\"".htmlspecialchars($newpagetitle)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_DESC" => "<input type=\"text\" class=\"text\" name=\"newpagedesc\" value=\"".htmlspecialchars($newpagedesc)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_AUTHOR" => "<input type=\"text\" class=\"text\" name=\"newpageauthor\" value=\"".htmlspecialchars($newpageauthor)."\" size=\"16\" maxlength=\"24\" />",
	"PAGEADD_FORM_OWNER" => sed_build_user($usr['id'], htmlspecialchars($usr['name'])),
	"PAGEADD_FORM_OWNERID" => $usr['id'],
	"PAGEADD_FORM_BEGIN" => $newpage_form_begin,
	"PAGEADD_FORM_EXPIRE" => $newpage_form_expire,
	"PAGEADD_FORM_FILE" => $pageadd_form_file,
	"PAGEADD_FORM_URL" => "<input type=\"text\" class=\"text\" name=\"newpageurl\" value=\"".htmlspecialchars($newpageurl)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_SIZE" => "<input type=\"text\" class=\"text\" name=\"newpagesize\" value=\"".htmlspecialchars($newpagesize)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_TEXT" => "<textarea class=\"editor\" name=\"newpagetext\" rows=\"24\" cols=\"120\">".htmlspecialchars($newpagetext)."</textarea>",
	"PAGEADD_FORM_TEXTBOXER" => "<textarea class=\"editor\" name=\"newpagetext\" rows=\"24\" cols=\"120\">".htmlspecialchars($newpagetext)."</textarea>",
	"PAGEADD_FORM_MYPFS" => $pfs
);

// PFS tags
$tplskin = file_get_contents($mskin);
preg_match_all("#\{(PAGEADD_FORM_PFS_([^\}]*?)_USER)\}#", $tplskin, $match);
$numtags = count($match[0]);
for($i = 0; $i<$numtags; $i++)
{
	$tag = $match[1][$i];
	$field = strtolower($match[2][$i]);
	$pfs_js = (!$cfg['disable_pfs']) ? sed_build_pfs($usr['id'], "newpage", "newpage$field", $L['Mypfs']) : '';
	$pageadd_array[$tag] = $pfs_js;
}
unset($match);
preg_match_all("#\{(PAGEADD_FORM_PFS_([^\}]*?)_SITE)\}#", $tplskin, $match);
$numtags = count($match[0]);
for($i = 0; $i<$numtags; $i++)
{
	$tag = $match[1][$i];
	$field = strtolower($match[2][$i]);
	$pfs_js = (sed_auth('pfs', 'a', 'A')) ? ' '.sed_build_pfs(0, "newpage", "newpage$field", $L['SFS']) : '';
	$pageadd_array[$tag] = $pfs_js;
}

// Extra fields
if(count($extrafields)>0)
foreach($extrafields as $i=>$row)
{
	// _TITLE tag
	isset($L['page_'.$row['field_name'].'_title']) ? $t->assign('PAGEADD_FORM_'.strtoupper($row['field_name']).'_TITLE', $L['page_'.$row['field_name'].'_title']) : $t->assign('PAGEADD_FORM_'.strtoupper($row['field_name']).'_TITLE', $row['field_description']);
	$t1 = "PAGEADD_FORM_".strtoupper($row['field_name']);
	$t2 = $row['field_html'];
	switch($row['field_type']) {
	case "input":
		$t2 = str_replace('<input ','<input name="newpage'.$row['field_name'].'" ', $t2);
		break;
	case "textarea":
		$t2 = str_replace('<textarea ','<textarea name="newpage'.$row['field_name'].'" ', $t2);
		break;
	case "select":
		$t2 = str_replace('<select','<select name="newpage'.$row['field_name'].'"', $t2);
		$options = "";
		$opt_array = explode(",",$row['field_variants']);
		if(count($opt_array)!=0)
		{	foreach ($opt_array as $var) $options .= "<option value=\"$var\">$var</option>"; }
		$t2 = str_replace("</select>","$options</select>",$t2); break;
	case "checkbox":
		$t2 = str_replace('<input','<input name="newpage'.$row['field_name'].'"', $t2);
		break;
	}
	$pageadd_array[$t1] = $t2;
}
$t->assign($pageadd_array);

/* === Hook === */
$extp = sed_getextplugins('page.add.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($usr['isadmin'])
{
	if ($cfg['autovalidate']) $usr_can_publish = TRUE;
	$t->parse('MAIN.ADMIN');
}

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>