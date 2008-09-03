<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=page.inc.php
Version=122
Updated=2007-oct-10
Type=Core
Author=Neocrome
Description=Pages
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['auth_read']);

$id = sed_import('id','G','INT');
$al = sed_import('al','G','ALP');
$r = sed_import('r','G','ALP');
$c = sed_import('c','G','TXT');
$pg = sed_import('pg','G','INT');
$comments = sed_import('comments','G','BOL');
$ratings = sed_import('ratings','G','BOL');

/* === Hook === */
$extp = sed_getextplugins('page.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if (!empty($al))
{ $sql = sed_sql_query("SELECT p.*, u.user_name, u.user_avatar FROM $db_pages AS p
LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
WHERE page_alias='$al' LIMIT 1"); }
else
{ $sql = sed_sql_query("SELECT p.*, u.user_name, u.user_avatar FROM $db_pages AS p
LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
WHERE page_id='$id'"); }

sed_die(sed_sql_numrows($sql)==0);
$pag = sed_sql_fetcharray($sql);

$pag['page_date'] = @date($cfg['dateformat'], $pag['page_date'] + $usr['timezone'] * 3600);
$pag['page_begin'] = @date($cfg['dateformat'], $pag['page_begin'] + $usr['timezone'] * 3600);
$pag['page_expire'] = @date($cfg['dateformat'], $pag['page_expire'] + $usr['timezone'] * 3600);
$pag['page_tab'] = (empty($pg)) ? 1 : $pg;
$pag['page_pageurl'] = (empty($pag['page_alias'])) ? "page.php?id=".$pag['page_id'] : "page.php?al=".$pag['page_alias'];

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', $pag['page_cat']);
sed_block($usr['auth_read']);

if ($pag['page_state']==1 && !$usr['isadmin'])
{
	sed_log("Attempt to directly access an un-validated page", 'sec');
	header("Location: " . SED_ABSOLUTE_URL . "message.php?msg=930");
	exit;
}

if (mb_substr($pag['page_text'], 0, 6)=='redir:')
{
	$redir = str_replace('redir:', '', trim($pag['page_text']));
	$sql = sed_sql_query("UPDATE $db_pages SET page_filecount=page_filecount+1 WHERE page_id='".$pag['page_id']."'");
	header("Location: " . SED_ABSOLUTE_URL . "".$redir);
	exit;
}
elseif (mb_substr($pag['page_text'], 0, 8)=='include:')
{
	$pag['page_text'] = sed_readraw('datas/html/'.trim(mb_substr($pag['page_text'], 8, 255)));
}

if($pag['page_file'] && $a=='dl')
{
	$file_size = @filesize($row['page_url']);
	$pag['page_filecount']++;
	$sql = sed_sql_query("UPDATE $db_pages SET page_filecount=page_filecount+1 WHERE page_id='".$pag['page_id']."'");
	header("Location: ".$pag['page_url']);
	echo("<script type='text/javascript'>location.href='".$pag['page_url']."';</script>Redirecting...");
	exit;
}

if(!$usr['isadmin'] || $cfg['count_admin'])
{
	$pag['page_count']++;
	$sql = sed_sql_query("UPDATE $db_pages SET page_count='".$pag['page_count']."' WHERE page_id='".$pag['page_id']."'");
}

$pag['page_tabs'] = explode('[newpage]', $pag['page_text'], 99);
$pag['page_totaltabs'] = count($pag['page_tabs']);

if ($pag['page_totaltabs']>1)
{
	if (empty($pag['page_tabs'][0]))
	{
		$remove = array_shift($pag['page_tabs']);
		$pag['page_totaltabs']--;
	}
	$pag['page_tab'] = ($pag['page_tab']>$pag['page_totaltabs']) ? 1 : $pag['page_tab'];
	$pag['page_tabtitles'] = array();

	for ($i = 0; $i < $pag['page_totaltabs']; $i++)
	{
		$p1 = mb_strpos($pag['page_tabs'][$i], '[title]');
		$p2 = mb_strpos($pag['page_tabs'][$i], '[/title]');

		if ($p2>$p1 && $p1<4)
		{
			$pag['page_tabtitle'][$i] = substr ($pag['page_tabs'][$i], $p1+7, ($p2-$p1)-7);
			if ($i+1==$pag['page_tab'])
			{
				$pag['page_tabs'][$i] = trim(str_replace('[title]'.$pag['page_tabtitle'][$i].'[/title]', '', $pag['page_tabs'][$i]));
			}
		}
		else
		{ $pag['page_tabtitle'][$i] = ''; }

		$pag['page_tabtitles'][] .= "<a href=\"".$pag['page_pageurl']."&amp;pg=".($i+1)."\">".($i+1).". ".$pag['page_tabtitle'][$i]."</a>";
		$pag['page_tabnav'] .= ($i+1==$pag['page_tab']) ? '&gt; ' : '[';
		$pag['page_tabnav'] .= "<a href=\"".$pag['page_pageurl']."&amp;pg=".($i+1)."\">".($i+1)."</a>";
		$pag['page_tabnav'] .= ($i+1==$pag['page_tab']) ? ' &lt; ' : '] ';
		$pag['page_tabs'][$i] = trim(str_replace('[newpage]', '', $pag['page_tabs'][$i]));
	}

	$pag['page_tabtitles'] = implode('<br />', $pag['page_tabtitles']);
	$pag['page_text'] = $pag['page_tabs'][$pag['page_tab']-1];
}


$catpath = sed_build_catpath($pag['page_cat'], "<a href=\"list.php?c=%1\$s\">%2\$s</a>");
$pag['page_fulltitle'] = $catpath." ".$cfg['separator']." <a href=\"".$pag['page_pageurl']."\">".$pag['page_title']."</a>";
$pag['page_fulltitle'] .= ($pag['page_totaltabs']>1 && !empty($pag['page_tabtitle'][$pag['page_tab']-1])) ? " (".$pag['page_tabtitle'][$pag['page_tab']-1].")" : '';

$item_code = 'p'.$pag['page_id'];

list($comments_link, $comments_display, $comments_count) = sed_build_comments($item_code, $pag['page_pageurl'], $comments);
list($ratings_link, $ratings_display) = sed_build_ratings($item_code, $pag['page_pageurl'], $ratings);

$sys['sublocation'] = $sed_cat[$c]['title'];
$out['subtitle'] = $pag['page_title'];

/* === Hook === */
$extp = sed_getextplugins('page.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('page', $sed_cat[$pag['page_cat']]['tpl']));
$t = new XTemplate($mskin);

$t->assign(array(
	"PAGE_ID" => $pag['page_id'],
	"PAGE_STATE" => $pag['page_state'],
	"PAGE_EXECUTE" => $pag['page_execute'],
	"PAGE_TITLE" => $pag['page_fulltitle'],
	"PAGE_SHORTTITLE" => $pag['page_title'],
	"PAGE_CAT" => $pag['page_cat'],
	"PAGE_CATTITLE" => $sed_cat[$pag['page_cat']]['title'],
	"PAGE_CATPATH" => $catpath,
	"PAGE_CATDESC" => $sed_cat[$pag['page_cat']]['desc'],
	"PAGE_CATICON" => $sed_cat[$pag['page_cat']]['icon'],
	"PAGE_KEY" => $pag['page_key'],
	"PAGE_EXTRA1" => $pag['page_extra1'],
	"PAGE_EXTRA2" => $pag['page_extra2'],
	"PAGE_EXTRA3" => $pag['page_extra3'],
	"PAGE_EXTRA4" => $pag['page_extra4'],
	"PAGE_EXTRA5" => $pag['page_extra5'],
	"PAGE_DESC" => $pag['page_desc'],
	"PAGE_AUTHOR" => $pag['page_author'],
	"PAGE_OWNER" => sed_build_user($pag['page_ownerid'], sed_cc($pag['user_name'])),
	"PAGE_AVATAR" => sed_build_userimage($pag['user_avatar']),
	"PAGE_DATE" => $pag['page_date'],
	"PAGE_BEGIN" => $pag['page_begin'],
	"PAGE_EXPIRE" => $pag['page_expire'],
	"PAGE_COMMENTS" => $comments_link,
	"PAGE_COMMENTS_DISPLAY" => $comments_display,
	"PAGE_COMMENTS_COUNT" => $comments_count,
	"PAGE_RATINGS" => $ratings_link,
	"PAGE_RATINGS_DISPLAY" => $ratings_display
));

if($pag['page_totaltabs']>1)
{
	$t->assign(array(
			"PAGE_MULTI_TABNAV" => $pag['page_tabnav'],
			"PAGE_MULTI_TABTITLES" => $pag['page_tabtitles'],
			"PAGE_MULTI_CURTAB" => $pag['page_tab'],
			"PAGE_MULTI_MAXTAB" => $pag['page_totaltabs']
	));
	$t->parse("MAIN.PAGE_MULTI");
}

if ($usr['isadmin'])
{
	$t-> assign(array(
			"PAGE_ADMIN_COUNT" => $pag['page_count'],
			"PAGE_ADMIN_UNVALIDATE" => "<a href=\"admin.php?m=page&amp;s=queue&amp;a=unvalidate&amp;id=".$pag['page_id']."&amp;".sed_xg()."\">".$L['Putinvalidationqueue']."</a>",
			"PAGE_ADMIN_EDIT" => "<a href=\"page.php?m=edit&amp;id=".$pag['page_id']."&amp;r=list\">".$L['Edit']."</a>"
			));

			$t->parse("MAIN.PAGE_ADMIN");
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
			$t->assign("PAGE_TEXT", "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\".");
		}
	break;

	default:
		if($cfg['parser_cache'])
		{
			if(empty($pag['page_html']) && !empty($pag['page_text']))
			{
				$pag['page_html'] = sed_parse(sed_cc($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
				sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($pag['page_html'])."' WHERE page_id = " . $pag['page_id']);
			}
			$html = $cfg['parsebbcodepages'] ? sed_post_parse($pag['page_html']) : sed_cc($pag['page_text']);
			$t->assign('PAGE_TEXT', $html);
		}
		else
		{
			$text = sed_parse(sed_cc($pag['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
			$text = sed_post_parse($text, 'pages');
			$t->assign('PAGE_TEXT', $text);
		}
	break;
}

$pag['page_file'] = intval($pag['page_file']);
if($pag['page_file'] > 0)
{
	if (!empty($pag['page_url']))
	{
		$dotpos = mb_strrpos($pag['page_url'],".")+1;
		$pag['page_fileicon'] = "images/pfs/".mb_strtolower(mb_substr($pag['page_url'], $dotpos, 5)).".gif";
		if (!file_exists($pag['page_fileicon']))
		{ $pag['page_fileicon'] = "images/admin/page.gif"; }
		$pag['page_fileicon'] = "<img src=\"".$pag['page_fileicon']."\" alt=\"\">";
	}
	else
	{ $pag['page_fileicon'] = ''; }

	$t->assign(array(
			"PAGE_FILE_SIZE" => $pag['page_size'],
			"PAGE_FILE_COUNT" => $pag['page_filecount'],
			"PAGE_FILE_ICON" => $pag['page_fileicon'],
			"PAGE_FILE_NAME" => basename($pag['page_url'])
	));
	if($pag['page_file'] === 2 && $usr['id'] == 0)
	{
		$t->assign('PAGE_SHORTTITLE', $L['Members_download']);
	}
	else
	{
		$t->assign('PAGE_FILE_URL', 'page.php?id='.$pag['page_id'].'&amp;a=dl');
	}
	$t->parse("MAIN.PAGE_FILE");
	$t->assign('PAGE_SHORTTITLE', $pag['page_title']);
}

/* === Hook === */
$extp = sed_getextplugins('page.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>