<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=forums.php
Version=125
Updated=2008-feb-27
Type=Core
Author=Neocrome
Description=Forums
[END_SED]
==================== */

defined('SED_CODE') or die('Wrong URL');

$id = sed_import('id','G','INT');
$s = sed_import('s','G','INT');
$q = sed_import('q','G','INT');
$p = sed_import('p','G','INT');
$d = sed_import('d','G','INT');
$o = sed_import('o','G','ALP');
$w = sed_import('w','G','ALP',4);
$poll = sed_import('poll','G','INT');

sed_blockguests();
sed_die(empty($s));

/* === Hook === */
$extp = sed_getextplugins('forums.newtopic.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once sed_incfile('functions', 'polls');

$sql = sed_sql_query("SELECT * FROM $db_forum_sections WHERE fs_id='$s'");

if ($row = sed_sql_fetcharray($sql))
{
	$fs_state = $row['fs_state'];
	$fs_minlevel = $row['fs_minlevel'];
	$fs_title = $row['fs_title'];
	$fs_category = $row['fs_category'];
	$fs_desc = $row['fs_desc'];
	$fs_autoprune = $row['fs_autoprune'];
	$fs_allowusertext = $row['fs_allowusertext'];
	$fs_allowbbcodes = $row['fs_allowbbcodes'];
	$fs_allowsmilies = $row['fs_allowsmilies'];
	$fs_allowprvtopics = $row['fs_allowprvtopics'];
	$fs_allowpolls = $row['fs_allowpolls'];
	$fs_countposts = $row['fs_countposts'];
	$fs_masterid = $row['fs_masterid'];
	$fs_mastername = $row['fs_mastername'];

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', $s);
	/* === Hook === */
	$extp = sed_getextplugins('forums.newtopic.rights');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	sed_block($usr['auth_write']);
}
else
{ sed_die(); }

if ($fs_state)
{
	sed_redirect(sed_url('message', "msg=602", '', true));
}

if (!$fs_allowpolls & $poll)
{
	sed_redirect(sed_url('message', "msg=930", '', true));
}

if ($a=='newtopic')
{
	sed_shield_protect();

	/* === Hook === */
	$extp = sed_getextplugins('forums.newtopic.newtopic.first');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$newtopictitle = sed_import('newtopictitle','P','TXT', 255);
	$newtopicdesc = sed_import('newtopicdesc','P','TXT', 255);
	$newprvtopic = sed_import('newprvtopic','P','BOL');
	$newmsg = sed_import('newmsg','P','HTM');
	$newtopicpreview = mb_substr(htmlspecialchars($newmsg), 0, 128);
	$newprvtopic = (!$fs_allowprvtopics) ? 0 : $newprvtopic;


	$error_string .= ( strlen($newtopictitle) < 2) ? $L["for_titletooshort"]."<br />" : '';
	$error_string .= ( strlen($newmsg) < 5) ? $L["for_messagetooshort"]."<br />" : '';
	if($poll){
	sed_poll_check();
	}


	if (empty($error_string))
	{
		if (mb_substr($newtopictitle, 0 ,1)=="#")
		{ $newtopictitle = str_replace('#', '', $newtopictitle); }


		$sql = sed_sql_query("INSERT into $db_forum_topics
		(ft_state,
		ft_mode,
		ft_sticky,
		ft_sectionid,
		ft_title,
		ft_desc,
		ft_preview,
		ft_creationdate,
		ft_updated,
		ft_postcount,
		ft_viewcount,
		ft_firstposterid,
		ft_firstpostername,
		ft_lastposterid,
		ft_lastpostername)
		VALUES
		(0,
			".(int)$newprvtopic.",
			0,
			".(int)$s.",
			'".sed_sql_prep($newtopictitle)."',
			'".sed_sql_prep($newtopicdesc)."',
			'".sed_sql_prep($newtopicpreview)."',
			".(int)$sys['now_offset'].",
			".(int)$sys['now_offset'].",
			1,
			0,
			".(int)$usr['id'].",
			'".sed_sql_prep($usr['name'])."',
			".(int)$usr['id'].",
			'".sed_sql_prep($usr['name'])."')");

		$q = sed_sql_insertid();
        if ($poll) sed_poll_save('forum', $q);

		if($cfg['parser_cache'])
		{
			$rhtml = sed_sql_prep(sed_parse(htmlspecialchars($newmsg), $cfg['parsebbcodeforums'] && $fs_allowbbcodes, $cfg['parsesmiliesforums'] && $fs_allowsmilies, 1));
		}
		else
		{
			$rhtml = '';
		}

		$sql = sed_sql_query("INSERT into $db_forum_posts
		(fp_topicid,
		fp_sectionid,
		fp_posterid,
		fp_postername,
		fp_creation,
		fp_updated,
		fp_text,
		fp_html,
		fp_posterip)
		VALUES
		(".(int)$q.",
			".(int)$s.",
			".(int)$usr['id'].",
			'".sed_sql_prep($usr['name'])."',
			".(int)$sys['now_offset'].",
			".(int)$sys['now_offset'].",
			'".sed_sql_prep($newmsg)."',
		'$rhtml',
		'".$usr['ip']."')");

		$sql = sed_sql_query("SELECT fp_id FROM $db_forum_posts WHERE 1 ORDER BY fp_id DESC LIMIT 1");
		$row = sed_sql_fetcharray($sql);
		$p = $row['fp_id'];

		$sql = sed_sql_query("UPDATE $db_forum_sections SET
		fs_postcount=fs_postcount+1,
		fs_topiccount=fs_topiccount+1
		WHERE fs_id='$s'");

		if ($fs_masterid>0)
		{ $sql = sed_sql_query("UPDATE $db_forum_sections SET
		fs_postcount=fs_postcount+1,
		fs_topiccount=fs_topiccount+1
		WHERE fs_id='$fs_masterid'"); }
		
		if ($fs_autoprune>0)
		{ sed_forum_prunetopics('updated', $s, $fs_autoprune); }

		if ($fs_countposts)
		{ $sql = sed_sql_query("UPDATE $db_users SET
		user_postcount=user_postcount+1
		WHERE user_id='".$usr['id']."'"); }

		if (!$newprvtopic)
		{ sed_forum_sectionsetlast($s); }

		/* === Hook === */
		$extp = sed_getextplugins('forums.newtopic.newtopic.done');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

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

		sed_shield_update(45, "New topic");
		sed_redirect(sed_url('forums', "m=posts&q=$q&n=last", '#bottom', true));
	}
}

// FIXME PFS dependency
//$pfs = sed_build_pfs($usr['id'], 'newtopic', 'newmsg', $L['Mypfs']);
//$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, 'newtopic', 'newmsg', $L['SFS']) : '';
$morejavascript .= sed_build_addtxt('newtopic', 'newmsg');
$post_main = '<textarea class="editor" name="newmsg" rows="16" cols="56">'.htmlspecialchars($newmsg).'</textarea>';

$newtopicurl = ($poll) ? sed_url('forums', "m=newtopic&a=newtopic&s=".$s."&poll=1") : sed_url('forums', "m=newtopic&a=newtopic&s=".$s);

$master = ($fs_masterid>0) ? array($fs_masterid, $fs_mastername) : false;


$toptitle = sed_build_forums($s, $fs_title, $fs_category, true, $master)." ".$cfg['separator']." <a href=\"".sed_url('forums', "m=newtopic&s=".$s)."\">".$L['for_newtopic']."</a>";
$toptitle .= ($usr['isadmin']) ? " *" : '';

$sys['sublocation'] = $fs_title;
$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => $fs_title,
	'NEWTOPIC' => $L['for_newtopic']
);
$out['subtitle'] = sed_title('title_forum_newtopic', $title_params);
$out['head'] .= $R['code_noindex'];

sed_online_update();

/* === Hook === */
$extp = sed_getextplugins('forums.newtopic.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('forums', 'newtopic', $fs_category, $s));
$t = new XTemplate($mskin);

if (!empty($error_string))
{
	$t->assign("FORUMS_NEWTOPIC_ERROR_BODY",$error_string);
	$t->parse("MAIN.FORUMS_NEWTOPIC_ERROR");
}

$t->assign(array(

	"FORUMS_NEWTOPIC_PAGETITLE" => $toptitle ,
	"FORUMS_NEWTOPIC_SUBTITLE" => htmlspecialchars($fs_desc),
	"FORUMS_NEWTOPIC_SEND" => $newtopicurl,
	"FORUMS_NEWTOPIC_TITLE" => "<input type=\"text\" class=\"text\" name=\"newtopictitle\" value=\"".htmlspecialchars($newtopictitle)."\" size=\"56\" maxlength=\"255\" />",
	"FORUMS_NEWTOPIC_DESC" => "<input type=\"text\" class=\"text\" name=\"newtopicdesc\" value=\"".htmlspecialchars($newtopicdesc)."\" size=\"56\" maxlength=\"255\" />",
	"FORUMS_NEWTOPIC_TEXT" => $post_main."<br />".$pfs."<br />&nbsp;<br />",
	"FORUMS_NEWTOPIC_TEXTONLY" => $post_main,
	"FORUMS_NEWTOPIC_TEXTBOXER" => $post_main."<br />".$pfs."<br />&nbsp;<br />",
	"FORUMS_NEWTOPIC_MYPFS" => $pfs,
));

if ($fs_allowprvtopics)
{
	$checked = ($newprvtopic) ? "checked=\"checked\"" : '';
	$prvtopic = "<input type=\"checkbox\" class=\"checkbox\" name=\"newprvtopic\" $checked />";

	$t->assign(array(
		"FORUMS_NEWTOPIC_ISPRIVATE" => $prvtopic
	));
	$t->parse("MAIN.PRIVATE");
}


if ($fs_allowpolls && $poll)
	{

    sed_poll_edit_form("new", $t, "MAIN.POLL");
	$t->parse("MAIN.POLL");

	}

/* === Hook === */
$extp = sed_getextplugins('forums.newtopic.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>
