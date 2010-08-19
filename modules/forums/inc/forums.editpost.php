<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * @package forums
 * @version 0.0.3
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$id = sed_import('id','G','INT');
$s = sed_import('s','G','INT');
$q = sed_import('q','G','INT');
$p = sed_import('p','G','INT');
$d = sed_import('d','G','INT');
$o = sed_import('o','G','ALP');
$w = sed_import('w','G','ALP',4);
$quote = sed_import('quote','G','INT');

/* === Hook === */
$extp = sed_getextplugins('forums.editpost.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

sed_blockguests();
sed_check_xg();

require_once sed_incfile('functions', 'polls');

$sql = sed_sql_query("SELECT * FROM $db_forum_posts WHERE fp_id='$p' and fp_topicid='$q' and fp_sectionid='$s' LIMIT 1");

if ($row = sed_sql_fetcharray($sql))
{
	$fp_text = $row['fp_text'];
	$fp_posterid = $row['fp_posterid'];
	$fp_postername = $row['fp_postername'];
	$fp_sectionid = $row['fp_sectionid'];
	$fp_topicid = $row['fp_topicid'];
	$fp_updated = $row['fp_updated'];
	$fp_updater = $row['fp_updater'];

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', $s);
	
	/* === Hook === */
	$extp = sed_getextplugins('forums.editpost.rights');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!$usr['isadmin'] && $fp_posterid!=$usr['id'])
	{
		sed_log("Attempt to edit a post without rights", 'sec');
		sed_die();
	}
	sed_block($usr['auth_read']);
}
else
{ sed_die(); }

$sql = sed_sql_query("SELECT fs_state, fs_title, fs_category, fs_allowbbcodes, fs_allowsmilies, fs_masterid, fs_mastername FROM $db_forum_sections WHERE fs_id='$s' LIMIT 1");

if ($row = sed_sql_fetcharray($sql))
{
	if ($row['fs_state'])
	{
		sed_redirect(sed_url('message', "msg=602", '', true));
	}

	$fs_title = $row['fs_title'];
	$fs_category = $row['fs_category'];
	$fs_allowbbcodes = $row['fs_allowbbcodes'];
	$fs_allowsmilies = $row['fs_allowsmilies'];
	$fs_masterid = $row['fs_masterid'];
	$fs_mastername = $row['fs_mastername'];
}
else
{ sed_die(); }

$sql = sed_sql_query("SELECT ft_state, ft_mode, ft_title, ft_desc FROM $db_forum_topics WHERE ft_id='$q' LIMIT 1");

if ($row = sed_sql_fetcharray($sql))
{
	if ($row['ft_state'] && !$usr['isadmin'])
	{
		sed_redirect(sed_url('message', "msg=603", '', true));
	}
	$ft_title = $row['ft_title'];
	$ft_desc = $row['ft_desc'];
	$ft_fulltitle = ($row['ft_mode']==1) ? "# ".$ft_title : $ft_title;
	$sys['sublocation'] = 'q'.$q;
}
else
{ sed_die(); }

if ($a=='update')
{
	/* === Hook === */
	$extp = sed_getextplugins('forums.editpost.update.first');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rtext = sed_import('rtext','P','HTM');
	$rtopictitle = sed_import('rtopictitle','P','TXT', 255);
	$rtopicdesc = sed_import('rtopicdesc','P','TXT', 255);
	$rupdater = ($fp_posterid == $usr['id'] && ($sys['now_offset'] < $fp_updated + 300) && empty($fp_updater) ) ? '' : $usr['name'];

	if(!empty($rtext))
	{
		if($cfg['parser_cache'])
		{
			$rhtml = sed_sql_prep(sed_parse(htmlspecialchars($rtext), $cfg['parsebbcodeforums'] && $fs_allowbbcodes, $cfg['parsesmiliesforums'] && $fs_allowsmilies, 1));
		}
		else
		{
			$rhtml = '';
		}
		$rtext = sed_sql_prep($rtext);
		$sql = sed_sql_query("UPDATE $db_forum_posts SET fp_text='$rtext', fp_html = '$rhtml', fp_updated='".$sys['now_offset']."', fp_updater='".sed_sql_prep($rupdater)."' WHERE fp_id='$p'");
	}

	$is_first_post = false;
	if (!empty($rtopictitle))
	{
		$sql = sed_sql_query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id ASC LIMIT 1");
		if ($row = sed_sql_fetcharray($sql))
		{
			$fp_idp = $row['fp_id'];
			if ($fp_idp==$p)
			{
				if (mb_substr($rtopictitle, 0 ,1)=="#")
				{ $rtopictitle = str_replace('#', '', $rtopictitle); }
				$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_title='".sed_sql_prep($rtopictitle)."', ft_desc='".sed_sql_prep($rtopicdesc)."' WHERE ft_id='$q'");
				$is_first_post = true;
				$poll_id = sed_import('poll_id','P','TXT');
				if($poll_id && !$cfg['disable_polls'])
				{
					sed_poll_check();
					if (!$cot_error)
					{
						$number = sed_poll_save();
					}
				}
			}
		}
	}

	if (!empty($rtopictitle) && !empty($rtext))
	{
		$rtopicpreview = mb_substr(htmlspecialchars($rtext), 0, 128);
		$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_preview='".sed_sql_prep($rtopicpreview)."' WHERE ft_id='$q'");
	}

	/* === Hook === */
	$extp = sed_getextplugins('forums.editpost.update.done');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	sed_forum_sectionsetlast($fp_sectionid);

	if ($cot_cache)
	{
		if ($cfg['cache_forums'])
		{
			$cot_cache->page->clear('forums');
		}
		if ($cfg['cache_index'])
		{
			$cot_cache->page->clear('index');
		}
	}

	sed_redirect(sed_url('forums', "m=posts&p=".$p, '#'.$p, true));
}

$sql = sed_sql_query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id ASC LIMIT 1");

$is_first_post = false;
if ($row = sed_sql_fetcharray($sql))
{
	$fp_idp = $row['fp_id'];
	if ($fp_idp==$p)
	{
	 	$edittopictitle = "<input type=\"text\" class=\"text\" name=\"rtopictitle\" value=\"".htmlspecialchars($ft_title)."\" size=\"56\" maxlength=\"255\" />";
	 	$topicdescription ="<input type=\"text\" class=\"text\" name=\"rtopicdesc\" value=\"".htmlspecialchars($ft_desc)."\" size=\"56\" maxlength=\"255\" />";
	 	$is_first_post = true;
	}
}

// FIXME PFS dependency
//$pfs = sed_build_pfs($usr['id'], 'editpost', 'rtext', $L['Mypfs']);
//$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, "editpost", "rtext", $L['SFS']) : '';
$morejavascript .= sed_build_addtxt('editpost', 'rtext');
$post_main = '<textarea class="editor" name="rtext" rows="20" cols="56">'.htmlspecialchars($fp_text).'</textarea>';

$master = ($fs_masterid>0) ? array($fs_masterid, $fs_mastername) : false;

$toptitle = sed_build_forums($s, $fs_title, $fs_category, true, $master)." ".$cfg['separator']." <a href=\"".sed_url('forums', "m=posts&p=".$p, "#".$p)."\">".htmlspecialchars($ft_fulltitle)."</a> ";
$toptitle .= $cfg['separator']." <a href=\"".sed_url('forums', "m=editpost&s=$s&q=".$q."&p=".$p."&".sed_xg())."\">".$L['Edit']."</a>";
$toptitle .= ($usr['isadmin']) ? " *" : '';

$sys['sublocation'] = $fs_title;
$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => $fs_title,
	'EDIT' => $L['Edit']
);
$out['subtitle'] = sed_title('title_forum_editpost', $title_params);
$out['head'] .= $R['code_noindex'];

sed_online_update();

/* === Hook === */
$extp = sed_getextplugins('forums.editpost.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('forums', 'editpost', $fs_category, $fp_sectionid));
$t = new XTemplate($mskin);

if (sed_check_messages())
{
	$t->assign('FORUMS_POSTS_EDITPOST_ERROR_BODY', sed_implode_messages());
	$t->parse('MAIN.FORUMS_EDITPOST_ERROR');
	sed_clear_messages();
}
if ($is_first_post)
{
$t->assign(array(
	"FORUMS_EDITPOST_TOPICTITTLE" => $edittopictitle,
	"FORUMS_EDITPOST_TOPICDESCRIPTION" => $topicdescription,
));
	$t->parse("MAIN.FORUMS_EDITPOST_FIRSTPOST");
}

if ($is_first_post && $usr['isadmin'] && !$cfg['disable_polls'] && sed_poll_edit_form($q, $t, 'MAIN.POLL', 'forum'))
{
    	$t->parse("MAIN.POLL");
}

$t->assign(array(
	"FORUMS_EDITPOST_PAGETITLE" => $toptitle,
	"FORUMS_EDITPOST_SUBTITLE" => "#".$fp_posterid." ".$fp_postername." - ".date($cfg['dateformat'], $fp_updated + $usr['timezone'] * 3600),
	"FORUMS_EDITPOST_SEND" => sed_url('forums', "m=editpost&a=update&s=".$s."&q=".$q."&p=".$p."&".sed_xg()),
	"FORUMS_EDITPOST_TEXT" => $post_main."<br />".$pfs,
	"FORUMS_EDITPOST_TEXTONLY" => $post_main,
	"FORUMS_EDITPOST_TEXTBOXER" => $post_main."<br />".$pfs,
	"FORUMS_EDITPOST_MYPFS" => $pfs
));

/* === Hook === */
$extp = sed_getextplugins('forums.editpost.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>
