<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=page.inc.php
Version=110
Updated=2006-jun-15
Type=Core
Author=Neocrome
Description=Pages
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$id = sed_import('id','G','INT');
$r = sed_import('r','G','ALP');
$c = sed_import('c','G','ALP');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['auth_write']);

/* === Hook === */
$extp = sed_getextplugins('page.add.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

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
	$newpageextra1 = sed_import('newpageextra1','P','TXT');
	$newpageextra2 = sed_import('newpageextra2','P','TXT');
	$newpageextra3 = sed_import('newpageextra3','P','TXT');
	$newpageextra4 = sed_import('newpageextra4','P','TXT');
	$newpageextra5 = sed_import('newpageextra5','P','TXT');
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

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $newpagecat);
	sed_block($usr['auth_write']);

	$error_string .= (empty($newpagecat)) ? $L['pag_catmissing']."<br />" : '';
	$error_string .= (mb_strlen($newpagetitle)<2) ? $L['pag_titletooshort']."<br />" : '';

	if (empty($error_string))
	{
		if (!empty($newpagealias))
		{
			$sql = sed_sql_query("SELECT page_id FROM $db_pages WHERE page_alias='".sed_sql_prep($newpagealias)."'");
			$newpagealias = (sed_sql_numrows($sql)>0) ? "alias".rand(1000,9999) : $newpagealias;
		}

		if($cfg['parser_cache'])
		{
			$newpagehtml = sed_parse(sed_cc($newpagetext), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
		}
		else
		{
			$newpagehtml = '';
		}

		$sql = sed_sql_query("INSERT into $db_pages
		(page_state,
		page_type,
		page_cat,
		page_key,
		page_extra1,
		page_extra2,
		page_extra3,
		page_extra4,
		page_extra5,
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
		page_alias)
		VALUES
		(1,
		0,
		'".sed_sql_prep($newpagecat)."',
			'".sed_sql_prep($newpagekey)."',
			'".sed_sql_prep($newpageextra1)."',
			'".sed_sql_prep($newpageextra2)."',
			'".sed_sql_prep($newpageextra3)."',
			'".sed_sql_prep($newpageextra4)."',
			'".sed_sql_prep($newpageextra5)."',
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
			'".sed_sql_prep($newpagealias)."')");

		/* === Hook === */
		$extp = sed_getextplugins('page.add.add.done');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		sed_shield_update(30, "New page");
		header("Location: " . SED_ABSOLUTE_URL . "message.php?msg=300");
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

$bbcodes = ($cfg['parsebbcodepages']) ? sed_build_bbcodes('newpage', 'newpagetext',$L['BBcodes']) : '';
$smilies = ($cfg['parsesmiliespages']) ? sed_build_smilies('newpage', 'newpagetext',$L['Smilies']) : '';
$pfs = sed_build_pfs($usr['id'], 'newpage', 'newpagetext',$L['Mypfs']);
$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, 'newpage', 'newpagetext', $L['SFS']) : '';
$pfs_form_url_myfiles = (!$cfg['disable_pfs']) ? sed_build_pfs($usr['id'], "newpage", "newpageurl", $L['Mypfs']) : '';
$pfs_form_url_myfiles .= (sed_auth('pfs', 'a', 'A')) ? ' '.sed_build_pfs(0, 'newpage', 'newpageurl', $L['SFS']) : '';

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

$t->assign(array(
	"PAGEADD_PAGETITLE" => $L['pagadd_title'],
	"PAGEADD_SUBTITLE" => $L['pagadd_subtitle'],
	"PAGEADD_ADMINEMAIL" => "mailto:".$cfg['adminemail'],
	"PAGEADD_FORM_SEND" => "page.php?m=add&amp;a=add",
	"PAGEADD_FORM_CAT" => $pageadd_form_categories,
	"PAGEADD_FORM_KEY" => "<input type=\"text\" class=\"text\" name=\"newpagekey\" value=\"".sed_cc($newpagekey)."\" size=\"16\" maxlength=\"16\" />",
	"PAGEADD_FORM_ALIAS" => "<input type=\"text\" class=\"text\" name=\"newpagealias\" value=\"".sed_cc($newpagealias)."\" size=\"16\" maxlength=\"255\" />",
	"PAGEADD_FORM_EXTRA1" => "<input type=\"text\" class=\"text\" name=\"newpageextra1\" value=\"".sed_cc($newpageextra1)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_EXTRA2" => "<input type=\"text\" class=\"text\" name=\"newpageextra2\" value=\"".sed_cc($newpageextra2)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_EXTRA3" => "<input type=\"text\" class=\"text\" name=\"newpageextra3\" value=\"".sed_cc($newpageextra3)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_EXTRA4" => "<input type=\"text\" class=\"text\" name=\"newpageextra4\" value=\"".sed_cc($newpageextra4)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_EXTRA5" => "<input type=\"text\" class=\"text\" name=\"newpageextra5\" value=\"".sed_cc($newpageextra5)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_TITLE" => "<input type=\"text\" class=\"text\" name=\"newpagetitle\" value=\"".sed_cc($newpagetitle)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_DESC" => "<input type=\"text\" class=\"text\" name=\"newpagedesc\" value=\"".sed_cc($newpagedesc)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_AUTHOR" => "<input type=\"text\" class=\"text\" name=\"newpageauthor\" value=\"".sed_cc($newpageauthor)."\" size=\"16\" maxlength=\"24\" />",
	"PAGEADD_FORM_OWNER" => sed_build_user($usr['id'], sed_cc($usr['name'])),
	"PAGEADD_FORM_OWNERID" => $usr['id'],
	"PAGEADD_FORM_BEGIN" => $newpage_form_begin,
	"PAGEADD_FORM_EXPIRE" => $newpage_form_expire,
	"PAGEADD_FORM_FILE" => $pageadd_form_file,
	"PAGEADD_FORM_URL" => "<input type=\"text\" class=\"text\" name=\"newpageurl\" value=\"".sed_cc($newpageurl)."\" size=\"56\" maxlength=\"255\" /> ".$pfs_form_url_myfiles,
	"PAGEADD_FORM_SIZE" => "<input type=\"text\" class=\"text\" name=\"newpagesize\" value=\"".sed_cc($newpagesize)."\" size=\"56\" maxlength=\"255\" />",
	"PAGEADD_FORM_TEXT" => "<textarea class=\"editor\" name=\"newpagetext\" rows=\"24\" cols=\"56\">".sed_cc($newpagetext)."</textarea><br />".$bbcodes." ".$smilies." ".$pfs,
	"PAGEADD_FORM_TEXTBOXER" => "<textarea class=\"editor\" name=\"newpagetext\" rows=\"24\" cols=\"56\">".sed_cc($newpagetext)."</textarea><br />".$bbcodes." ".$smilies." ".$pfs,
	"PAGEADD_FORM_BBCODES" => $bbcodes,
	"PAGEADD_FORM_SMILIES" => $smilies,
	"PAGEADD_FORM_MYPFS" => $pfs
));

/* === Hook === */
$extp = sed_getextplugins('page.add.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>