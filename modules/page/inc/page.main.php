<?php
/**
 * Page display.
 *
 * @package page
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['auth_read']);

$id = sed_import('id', 'G', 'INT');
$al = sed_import('al', 'G', 'ALP');
$r = sed_import('r', 'G', 'ALP');
$c = sed_import('c', 'G', 'TXT');
$pg = sed_import('pg', 'G', 'INT');

/* === Hook === */
$extp = sed_getextplugins('page.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$where = (!empty($al)) ? "page_alias='".$al."'" : "page_id='".$id."'";
$sql = sed_sql_query("SELECT p.*, u.* FROM $db_pages AS p
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		WHERE $where LIMIT 1");
	
sed_die(sed_sql_numrows($sql) == 0);
$pag = sed_sql_fetcharray($sql);

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin'], $usr['auth_download']) = sed_auth('page', $pag['page_cat'], 'RWA1');
sed_block($usr['auth_read']);

$al = empty($pag['page_alias']) ? '' : $pag['page_alias'];
$id = (int) $pag['page_id'];
$cat = $sed_cat[$pag['page_cat']];

$sys['sublocation'] = $pag['page_title'];
sed_online_update();

$pag['page_begin_noformat'] = $pag['page_begin'];
$pag['page_tab'] = (empty($pg)) ? 0 : $pg;
$pag['page_pageurl'] = (empty($al)) ? sed_url('page', 'id='.$id) : sed_url('page', 'al='.$al);

if ($pag['page_state'] == 1 && !$usr['isadmin'] && $usr['id'] != $pag['page_ownerid'])
{
	sed_log("Attempt to directly access an un-validated page", 'sec'); // TODO i18n
	sed_redirect(sed_url('message', "msg=930", '', true));
}
if (mb_substr($pag['page_text'], 0, 6) == 'redir:')
{
	$redir = trim(str_replace('redir:', '', $pag['page_text']));
	$sql = sed_sql_query("UPDATE $db_pages SET page_filecount=page_filecount+1 WHERE page_id='".$id."'");
	header('Location: ' . (preg_match('#^(http|ftp)s?://#', $redir) ? '' : SED_ABSOLUTE_URL) . $redir);
	exit;
}
elseif (mb_substr($pag['page_text'], 0, 8) == 'include:')
{
	$pag['page_text'] = sed_readraw('datas/html/'.trim(mb_substr($pag['page_text'], 8, 255)));
}
if ($pag['page_file'] && $sys['now_offset'] > $pag['page_begin_noformat'] && $a == 'dl' && (($pag['page_file'] == 2 && $usr['auth_download']) || $pag['page_file'] == 1))
{
	/* === Hook === */
	$extp = sed_getextplugins('page.download.first');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	// Hotlinking protection
	if ($_SESSION['dl'] != $id
		&& $_SESSION['cat'] != $pag['page_cat'])
	{
		sed_redirect($pag['page_pageurl']);
	}

	unset($_SESSION['dl']);

	$file_size = @filesize($row['page_url']);
	if (!$usr['isadmin'] || $cfg['count_admin'])
	{
		$pag['page_filecount']++;
		$sql = sed_sql_query("UPDATE $db_pages SET page_filecount=page_filecount+1 WHERE page_id=".(int)$id);
	}
	$redir = (preg_match('#^(http|ftp)s?://#', $pag['page_url']) ? '' : SED_ABSOLUTE_URL) . $pag['page_url'];
	header('Location: ' . $redir);
	echo sed_rc('page_code_redir');
	exit;
}
if (!$usr['isadmin'] || $cfg['count_admin'])
{
	$pag['page_count']++;
	$sql = (!$cfg['disablehitstats']) ? sed_sql_query("UPDATE $db_pages SET page_count='".$pag['page_count']."' WHERE page_id='".$id."'") : '';
}

$catpath = sed_build_catpath($pag['page_cat']);
$pag['page_fulltitle'] = $catpath . ' ' . $cfg['separator'] . ' ' . htmlspecialchars($pag['page_title']);
$pag['page_fulltitle'] .= ($pag['page_totaltabs'] > 1 && !empty($pag['page_tabtitle'][$pag['page_tab'] - 1])) ? " (".$pag['page_tabtitle'][$pag['page_tab'] - 1].")" : '';// page_totaltabs - Not found befor this line bur after .... see


$ratings = ($cat['ratings']) ? true : false;
list($ratings_link, $ratings_display) = sed_build_ratings('p'.$id, $pag['page_pageurl'], $ratings);

$title_params = array(
	'TITLE' => $pag['page_title'],
	'CATEGORY' => $cat['title']
);
$out['desc'] = htmlspecialchars(strip_tags($pag['page_desc']));
$out['subtitle'] = sed_title('title_page', $title_params);

/* === Hook === */
$extp = sed_getextplugins('page.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($pag['page_file'])
{
	unset($_SESSION['dl']);
	$_SESSION['dl'] = $id;
}

require_once $cfg['system_dir'] . '/header.php';
require_once sed_incfile('functions', 'users');

$mskin = sed_skinfile(array('page', $cat['tpl']));
$t = new XTemplate($mskin);

$t->assign(array(
	"PAGE_ID" => $pag['page_id'],
	"PAGE_STATE" => $pag['page_state'],
	"PAGE_TITLE" => $pag['page_fulltitle'],
	"PAGE_SHORTTITLE" => $pag['page_title'],
	"PAGE_CAT" => $pag['page_cat'],
	"PAGE_CATTITLE" => $cat['title'],
	"PAGE_CATPATH" => $catpath,
	"PAGE_CATDESC" => $cat['desc'],
	"PAGE_CATICON" => $cat['icon'],
	"PAGE_KEY" => $pag['page_key'],
	"PAGE_DESC" => $pag['page_desc'],
	"PAGE_AUTHOR" => $pag['page_author'],
	"PAGE_OWNER" => sed_build_user($pag['page_ownerid'], htmlspecialchars($pag['user_name'])),
	"PAGE_DATE" => @date($cfg['dateformat'], $pag['page_date'] + $usr['timezone'] * 3600),
	"PAGE_BEGIN" => @date($cfg['dateformat'], $pag['page_begin'] + $usr['timezone'] * 3600),
	"PAGE_EXPIRE" => @date($cfg['dateformat'], $pag['page_expire'] + $usr['timezone'] * 3600),
	"PAGE_ALIAS" => $pag['page_alias'],
	"RAGE_NOTAVAILIBLE" => ($pag['page_begin_noformat'] > $sys['now_offset']) ? $L['pag_notavailable'].sed_build_timegap($sys['now_offset'], $pag['page_begin_noformat']) : '',
	"PAGE_RATINGS" => $ratings_link,
	"PAGE_RATINGS_DISPLAY" => $ratings_display
));
$t->assign(sed_generate_usertags($pag, "PAGE_ROW_OWNER_"));

// Extra fields for pages
foreach ($sed_extrafields['pages'] as $i => $row)
{
	$uname = strtoupper($row['field_name']);
	$t->assign('PAGE_'.$uname, sed_build_extrafields_data('page', $row['field_type'], $row['field_name'], $pag['page_'.$row['field_name']]));
	$t->assign('PAGE_'.$uname.'_TITLE', isset($L['page_'.$row['field_name'].'_title']) ?  $L['page_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Extra fields for structure
foreach ($sed_extrafields['structure'] as $i => $row)
{
	$uname = strtoupper($row['field_name']);
	$t->assign('PAGE_CAT_'.$uname, sed_build_extrafields_data('structure', $row['field_type'], $row['field_name'], $cat[$row['field_name']]));
	$t->assign('PAGE_CAT_'.$uname.'_TITLE', isset($L['structure_'.$row['field_name'].'_title']) ?  $L['structure_'.$row['field_name'].'_title'] : $row['field_description']);
}

if ($usr['isadmin'] || $usr['id'] == $pag['page_ownerid'])
{
	$t->assign("PAGE_ADMIN_EDIT", "<a href=\"".sed_url('page', 'm=edit&id='.$id.'&r=list')."\">".$L['Edit'].'</a>');
}

if ($usr['isadmin'])
{

	if ($pag['page_state'] == 1)
	{
		$validation = sed_rc_link(sed_url('admin', 'm=page&a=validate&id='.$id.'&'.sed_xg()), $L['Validate']);
	}
	else
	{
		$validation = sed_rc_link(sed_url('admin', 'm=page&a=unvalidate&id='.$id.'&'.sed_xg()), $L['Putinvalidationqueue']);
	}

	$t->assign(array(
		"PAGE_ADMIN_COUNT" => $pag['page_count'],
		"PAGE_ADMIN_UNVALIDATE" => $validation
	));
}

switch($pag['page_type'])
{
	case '1':
		$t->assign("PAGE_TEXT", $pag['page_text']);
	break;

	case '2':
		if ($cfg['allowphp_pages'] && $cfg['allowphp_override'])
		{
			ob_start();
			eval($pag['page_text']);
			$t->assign("PAGE_TEXT", ob_get_clean());
		}
		else
		{
			$t->assign("PAGE_TEXT", "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\"."); // TODO - i18n
		}
	break;

	default:
		if ($cfg['parser_cache'])
		{
			if (empty($pag['page_html']) && !empty($pag['page_text']))
			{
				$pag['page_html'] = sed_parse(htmlspecialchars($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], true);
				sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($pag['page_html'])."' WHERE page_id = " . $id);
			}
			$html = $cfg['parsebbcodepages'] ? sed_post_parse($pag['page_html']) : htmlspecialchars($pag['page_text']);
			$t->assign('PAGE_TEXT', $html);
		}
		else
		{
			$text = sed_parse(htmlspecialchars($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], true);
			$text = sed_post_parse($text, 'pages');
			$t->assign('PAGE_TEXT', $text);
		}
	break;
}

$pag['page_file'] = intval($pag['page_file']);
if ($pag['page_file'] > 0)
{
	if ($sys['now_offset'] > $pag['page_begin_noformat'])
	{
		if (!empty($pag['page_url']))
		{
			$dotpos = mb_strrpos($pag['page_url'], ".") + 1;
			$type = mb_strtolower(mb_substr($pag['page_url'], $dotpos, 5));
			$pag['page_fileicon'] = sed_rc('page_icon_file_path');
			if (!file_exists($pag['page_fileicon']))
			{
				$pag['page_fileicon'] = sed_rc('page_icon_file_default');
			}
			$pag['page_fileicon'] = sed_rc('page_icon_file', array('icon' => $pag['page_fileicon']));
		}
		else
		{
			$pag['page_fileicon'] = '';
		}

		$t->assign(array(
			"PAGE_FILE_SIZE" => $pag['page_size'],
			"PAGE_FILE_COUNT" => $pag['page_filecount'],
			"PAGE_FILE_ICON" => $pag['page_fileicon'],
			"PAGE_FILE_NAME" => basename($pag['page_url'])
		));

		if (($pag['page_file'] === 2 && $usr['id'] == 0) || ($pag['page_file'] === 2 && !$usr['auth_download']))
		{
			$t->assign('PAGE_FILETITLE', $L['Members_download']);
		}
		else
		{
			$t->assign(array(
				'PAGE_FILETITLE' => $pag['page_title'],
				'PAGE_FILE_URL' => sed_url('page', "id=".$id."&a=dl")
			));
		}
	}
}

// Multi tabs
$pag['page_tabs'] = explode('[newpage]', $t->vars['PAGE_TEXT'], 99);
$pag['page_totaltabs'] = count($pag['page_tabs']);

if ($pag['page_totaltabs'] > 1)
{
	if (empty($pag['page_tabs'][0]))
	{
		$remove = array_shift($pag['page_tabs']);
		$pag['page_totaltabs']--;
	}
	$max_tab = $pag['page_totaltabs'] - 1;
	$pag['page_tab'] = ($pag['page_tab'] > $max_tab) ? 0 : $pag['page_tab'];
	$pag['page_tabtitles'] = array();

	for ($i = 0; $i < $pag['page_totaltabs']; $i++)
	{
		if (mb_strpos($pag['page_tabs'][$i], '<br />') === 0)
		{
			$pag['page_tabs'][$i] = mb_substr($pag['page_tabs'][$i], 6);
		}

		$p1 = mb_strpos($pag['page_tabs'][$i], '[title]');
		$p2 = mb_strpos($pag['page_tabs'][$i], '[/title]');

		if ($p2 > $p1 && $p1 < 4)
		{
			$pag['page_tabtitle'][$i] = mb_substr($pag['page_tabs'][$i], $p1 + 7, ($p2 - $p1) - 7);
			if ($i == $pag['page_tab'])
			{
				$pag['page_tabs'][$i] = trim(str_replace('[title]'.$pag['page_tabtitle'][$i].'[/title]', '', $pag['page_tabs'][$i]));
			}
		}
		else
		{
			$pag['page_tabtitle'][$i] = $i == 1 ? $pag['page_title'] : $L['Page'] . ' ' . ($i + 1);
		}
		$tab_url = empty($al) ? sed_url('page', 'id='.$id.'&pg='.$i) : sed_url('page', 'al='.$al.'&pg='.$i);
		$pag['page_tabtitles'][] .= sed_rc_link(sed_url($tab_url), ($i+1).'. '.$pag['page_tabtitle'][$i],
			array('class' => 'page_tabtitle'));
		$pn = sed_pagenav('page', (empty($al) ? 'id='.$id : 'al='.$al), $pag['page_tab'], $pag['page_totaltabs'], 1, 'pg');
		$pag['page_tabnav'] = $pn['main'];
		$pag['page_tabs'][$i] = str_replace('[newpage]', '', $pag['page_tabs'][$i]);
		$pag['page_tabs'][$i] = preg_replace('#^(<br />)+#', '', $pag['page_tabs'][$i]);
		$pag['page_tabs'][$i] = trim($pag['page_tabs'][$i]);
	}

	$pag['page_tabtitles'] = implode('<br />', $pag['page_tabtitles']);
	$pag['page_text'] = $pag['page_tabs'][$pag['page_tab']];

	$t->assign(array(
		'PAGE_MULTI_TABNAV' => $pag['page_tabnav'],
		'PAGE_MULTI_TABTITLES' => $pag['page_tabtitles'],
		'PAGE_MULTI_CURTAB' => $pag['page_tab'] + 1,
		'PAGE_MULTI_MAXTAB' => $pag['page_totaltabs'],
		'PAGE_TEXT' => $pag['page_text']
	));
	$t->parse('MAIN.PAGE_MULTI');
}

/* === Hook === */
$extp = sed_getextplugins('page.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */
if ($usr['isadmin'])
{
	$t->parse('MAIN.PAGE_ADMIN');
}
if (($pag['page_file'] === 2 && $usr['id'] == 0) || ($pag['page_file'] === 2 && !$usr['auth_download']))
{
	$t->parse('MAIN.PAGE_FILE.MEMBERSONLY');
}
else
{
	$t->parse('MAIN.PAGE_FILE.DOWNLOAD');
}
if (!empty($pag['page_url']))
{
	$t->parse('MAIN.PAGE_FILE');
}
$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';

if ($cot_cache && $usr['id'] === 0 && $cfg['cache_page'])
{
	$cot_cache->page->write();
}
?>