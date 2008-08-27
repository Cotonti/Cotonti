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

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$id = sed_import('id','G','INT');
$s = sed_import('s','G','INT');
$q = sed_import('q','G','INT');
$p = sed_import('p','G','INT');
$d = sed_import('d','G','INT');
$o = sed_import('o','G','ALP');
$w = sed_import('w','G','ALP',4);
$quote = sed_import('quote','G','INT');
$poll = sed_import('poll','G','INT');
$vote = sed_import('vote','G','INT');
$unread_done = FALSE;
$fp_num = 0;
unset ($notlastpage);

/* === Hook === */
$extp = sed_getextplugins('forums.posts.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($n=='last' && !empty($q))
{
	$sql = sed_sql_query("SELECT fp_id, fp_topicid, fp_sectionid, fp_posterid
	FROM $db_forum_posts
	WHERE fp_topicid='$q'
	ORDER by fp_id DESC LIMIT 1");
	if ($row = sed_sql_fetcharray($sql))
	{
		$p = $row['fp_id'];
		$q = $row['fp_topicid'];
		$s = $row['fp_sectionid'];
		$fp_posterid = $row['fp_posterid'];
	}
}
elseif ($n=='unread' && !empty($q) && $usr['id']>0)
{
	$sql = sed_sql_query("SELECT fp_id, fp_topicid, fp_sectionid, fp_posterid
	FROM $db_forum_posts
	WHERE fp_topicid='$q' AND fp_creation>'".$usr['lastvisit']."' AND fp_posterid!='".$usr['id']."'
		ORDER by fp_id ASC LIMIT 1");
	if ($row = sed_sql_fetcharray($sql))
	{
		$p = $row['fp_id'];
		$q = $row['fp_topicid'];
		$s = $row['fp_sectionid'];
		$fp_posterid = $row['fp_posterid'];
	}
}

if (!empty($p))
{
	$sql = sed_sql_query("SELECT fp_topicid, fp_sectionid, fp_posterid FROM $db_forum_posts WHERE fp_id='$p' LIMIT 1");
	if ($row = sed_sql_fetcharray($sql))
	{
		$q = $row['fp_topicid'];
		$s = $row['fp_sectionid'];
		$fp_posterid = $row['fp_posterid'];
	}
	else
	{ sed_die(); }
}
elseif (!empty($q))
{
	$sql = sed_sql_query("SELECT ft_sectionid FROM $db_forum_topics WHERE ft_id='$q' LIMIT 1");
	if ($row = sed_sql_fetcharray($sql))
	{ $s = $row['ft_sectionid']; }
	else
	{ sed_die(); }
}

$sql = sed_sql_query("SELECT * FROM $db_forum_sections WHERE fs_id='$s' LIMIT 1");

if ($row = sed_sql_fetcharray($sql))
{
	$fs_title = $row['fs_title'];
	$fs_category = $row['fs_category'];
	$fs_state = $row['fs_state'];
	$fs_allowusertext = $row['fs_allowusertext'];
	$fs_allowbbcodes = $row['fs_allowbbcodes'];
	$fs_allowsmilies = $row['fs_allowsmilies'];
	$fs_countposts = $row['fs_countposts'];

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', $s);
	sed_block($usr['auth_read']);

	if ($fs_state)
	{
		header("Location: " . SED_ABSOLUTE_URL . "message.php?msg=602");
		exit;
	}
}
else
{ sed_die(); }

$sql2 = sed_sql_query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id ASC LIMIT 2");

while ($row2 = sed_sql_fetcharray($sql2))
{ $post12[] = $row2['fp_id']; }

if ($a=='newpost')
{
	sed_shield_protect();

	$sql = sed_sql_query("SELECT ft_state FROM $db_forum_topics WHERE ft_id='$q'");

	if ($row = sed_sql_fetcharray($sql))
	{
		if ($row['ft_state'])
		{ sed_die(); }
	}
	else
	{ sed_die(); }

	$sql = sed_sql_query("SELECT fp_posterid, fp_posterip FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id DESC LIMIT 1");

	if ($row = sed_sql_fetcharray($sql))
	{
		if ($cfg['antibumpforums'] && ( ($usr['id']==0 && $row['fp_posterid']==0 && $row['fp_posterip']==$usr['ip']) || ($row['fp_posterid']>0 && $row['fp_posterid']==$usr['id']) ))
		{ sed_die(); }
	}
	else
	{ sed_die(); }

	/* === Hook === */
	$extp = sed_getextplugins('forums.posts.newpost.first');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$newmsg = sed_import('newmsg','P','HTM');

	if (empty($error_string) && !empty($newmsg) && !empty($s) && !empty($q))
	{
		if($cfg['parser_cache'])
		{
			$rhtml = sed_sql_prep(sed_parse(sed_cc($newmsg), $cfg['parsebbcodeforums'] && $fs_allowbbcodes, $cfg['parsesmiliesforums'] && $fs_allowsmilies, 1));
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
		fp_updater,
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
			0,
			'".sed_sql_prep($newmsg)."',
			'$rhtml',
			'".$usr['ip']."')");

		$sql = sed_sql_query("UPDATE $db_forum_topics SET
		ft_postcount=ft_postcount+1,
		ft_updated='".$sys['now_offset']."',
			ft_lastposterid='".$usr['id']."',
			ft_lastpostername='".sed_sql_prep($usr['name'])."'
		WHERE ft_id='$q'");

		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount+1 WHERE fs_id='$s'");

		if ($fs_countposts)
		{ $sql = sed_sql_query("UPDATE $db_users SET user_postcount=user_postcount+1 WHERE user_id='".$usr['id']."'"); }

		/* === Hook === */
		$extp = sed_getextplugins('forums.posts.newpost.done');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		sed_forum_sectionsetlast($s);
		sed_shield_update(30, "New post");
		header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=posts&q=$q&n=last#bottom");
		exit;
	}
}

elseif ($a=='delete' && $usr['id']>0 && !empty($s) && !empty($q) && !empty($p) && ($usr['isadmin'] || $fp_posterid==$usr['id']))
{
	sed_check_xg();

	/* === Hook === */
	$extp = sed_getextplugins('forums.posts.delete.first');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	if ($post12[0]==$p && $post12[1]>0)
	{ sed_die(); }

	$sql = sed_sql_query("SELECT * FROM $db_forum_posts WHERE fp_id='$p' AND fp_topicid='$q' AND fp_sectionid='$s'");

	if ($row = sed_sql_fetchassoc($sql))
	{
		if ($cfg['trash_forum'])
		{ sed_trash_put('forumpost', $L['Post']." #".$p." from topic #".$q, "p".$p."-q".$q, $row); }
	}
	else
	{ sed_die(); }

	$sql = sed_sql_query("DELETE FROM $db_forum_posts WHERE fp_id='$p' AND fp_topicid='$q' AND fp_sectionid='$s'");

	if ($fs_countposts)
	{ $sql = sed_sql_query("UPDATE $db_users SET user_postcount=user_postcount-1 WHERE user_id='".$fp_posterid."' AND user_postcount>0"); }

	sed_log("Deleted post #".$p, 'for');

	/* === Hook === */
	$extp = sed_getextplugins('forums.posts.delete.done');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$q'");

	if (sed_sql_result($sql, 0, "COUNT(*)")==0)
	{
		// No posts left in this topic
		$sql = sed_sql_query("SELECT * FROM $db_forum_topics WHERE ft_id='$q'");

		if ($row = sed_sql_fetchassoc($sql))
		{
			if ($cfg['trash_forum'])
			{ sed_trash_put('forumtopic', $L['Topic']." #".$q." (no post left)", "q".$q, $row); }
			$sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_movedto='$q'");
			$sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_id='$q'");

			$sql = sed_sql_query("UPDATE $db_forum_sections SET
			fs_topiccount=fs_topiccount-1,
			fs_topiccount_pruned=fs_topiccount_pruned+1,
			fs_postcount=fs_postcount-1,
			fs_postcount_pruned=fs_postcount_pruned+1
			WHERE fs_id='$s'");

			/* === Hook === */
			$extp = sed_getextplugins('forums.posts.emptytopicdel');
			if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
			/* ===== */

			sed_log("Delete topic #".$q." (no post left)",'for');
			sed_forum_sectionsetlast($s);
		}
		header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=topics&s=".$s);
		exit;
	}
	else
	{
		// There's at least 1 post left, let's resync
		$sql = sed_sql_query("SELECT fp_id, fp_posterid, fp_postername, fp_updated
		FROM $db_forum_posts
		WHERE fp_topicid='$q' AND fp_sectionid='$s'
		ORDER BY fp_id DESC LIMIT 1");

		if ($row = sed_sql_fetcharray($sql))
		{
			$sql = sed_sql_query("UPDATE $db_forum_topics SET
			ft_postcount=ft_postcount-1,
			ft_lastposterid='".(int)$row['fp_posterid']."',
				ft_lastpostername='".sed_sql_prep($row['fp_postername'])."',
				ft_updated='".(int)$row['fp_updated']."'
			WHERE ft_id='$q'");

			$sql = sed_sql_query("UPDATE $db_forum_sections SET
			fs_postcount=fs_postcount-1,
			fs_postcount_pruned=fs_postcount_pruned+1
			WHERE fs_id='$s'");

			sed_forum_sectionsetlast($s);

			$sql = sed_sql_query("SELECT fp_id FROM $db_forum_posts
			WHERE fp_topicid='$q' AND fp_sectionid='$s' AND fp_id<$p
			ORDER BY fp_id DESC LIMIT 1");

			if ($row = sed_sql_fetcharray($sql))
			{
				header("Location: " . SED_ABSOLUTE_URL . "forums.php?m=posts&p=".$row['fp_id']."#".$row['fp_id']);
				exit;
			}
		}
	}
}

$sql = sed_sql_query("SELECT ft_title, ft_desc, ft_mode, ft_state, ft_poll, ft_firstposterid FROM $db_forum_topics WHERE ft_id='$q'");

if ($row = sed_sql_fetcharray($sql))
{
	$ft_title = $row['ft_title'];
	$ft_desc = $row['ft_desc'];
	$ft_mode = $row['ft_mode'];
	$ft_state = $row['ft_state'];
	$ft_poll = $row['ft_poll'];
	$ft_firstposterid = $row['ft_firstposterid'];

	if ($ft_mode==1 && !($usr['isadmin'] || $ft_firstposterid==$usr['id']))
	{ sed_die(); }
}
else
{ sed_die(); }

$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_viewcount=ft_viewcount+1 WHERE ft_id='$q'");
$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_viewcount=fs_viewcount+1 WHERE fs_id='$s'");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$q'");
$totalposts = sed_sql_result($sql,0,"COUNT(*)");

if (!empty($p))
{
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$q' and fp_id<'$p'");
	$postsbefore = sed_sql_result($sql,0,"COUNT(*)");
	$d = $cfg['maxtopicsperpage'] * floor($postsbefore / $cfg['maxtopicsperpage']);
}

if (empty($d))
{ $d = '0'; }

if ($usr['id']>0)
{ $morejavascript .= sed_build_addtxt('newpost', 'newmsg'); }

$sql = sed_sql_query("SELECT p.*, u.user_text, u.user_maingrp, u.user_avatar, u.user_photo, u.user_signature,
u.user_extra1, u.user_extra2, u.user_extra3, u.user_extra4, u.user_extra5, u.user_extra6, u.user_extra7, u.user_extra8, u.user_extra9,
u.user_country, u.user_occupation, u.user_location, u.user_website, u.user_email, u.user_hideemail, u.user_gender, u.user_birthdate,
u.user_jrnpagescount, u.user_jrnupdated, u.user_gallerycount, u.user_postcount
FROM $db_forum_posts AS p LEFT JOIN $db_users AS u ON u.user_id=p.fp_posterid
WHERE fp_topicid='$q'
ORDER BY fp_id LIMIT $d, ".$cfg['maxtopicsperpage']);

$sys['sublocation'] = $fs_title;
$out['subtitle'] = $L['Forums']." - ".sed_cc($ft_title);

/* === Hook === */
$extp = sed_getextplugins('forums.posts.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once("system/header.php");

$mskin = sed_skinfile(array('forums', 'posts', $fs_category, $s));
$t = new XTemplate($mskin);

if (!$cfg['disable_polls'] && $ft_poll>0)
{
	$sql5 = sed_sql_query("SELECT * FROM $db_polls WHERE poll_id='$ft_poll' AND poll_state='0' AND poll_type='1' LIMIT 1");

	sed_die(sed_sql_numrows($sql5)==0);

	if ($usr['id']>0)
	{ $sql7 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$ft_poll' AND (pv_userid='".$usr['id']."' OR pv_userip='".$usr['ip']."') LIMIT 1"); }
	else
	{ $sql7 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$ft_poll' AND pv_userip='".$usr['ip']."' LIMIT 1"); }

	$alreadyvoted = (sed_sql_numrows($sql7)>0) ? TRUE : FALSE;

	if ($a=='send' && !$alreadyvoted && !$ft_state)
	{
		sed_check_xg();
		$sql8 = sed_sql_query("UPDATE $db_polls_options SET po_count=po_count+1 WHERE po_pollid='$ft_poll' AND po_id='$vote'");
		if (sed_sql_affectedrows()==1)
		{
			$sql8 = sed_sql_query("INSERT INTO $db_polls_voters (pv_pollid, pv_userid, pv_userip) VALUES (".(int)$ft_poll.", ".(int)$usr['id'].", '".$usr['ip']."')");
			$votecasted = TRUE;
			$alreadyvoted = TRUE;
		}
	}

	$sql4 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$ft_poll'");
	$totalvotes = sed_sql_result($sql4,0,"SUM(po_count)");

	$row5 = sed_sql_fetcharray($sql5);
	$poll_state = $row5['poll_state'];

	$sql6 = sed_sql_query("SELECT po_id, po_text, po_count FROM $db_polls_options WHERE po_pollid='$ft_poll' ORDER by po_id ASC");
	$sql9 = sed_sql_query("SELECT MAX(po_count) FROM $db_polls_options WHERE po_pollid='$ft_poll'");

	$row9 = sed_sql_fetcharray($sql9);
	$coef = ($row9['MAX(po_count)']<1) ? 0 : ($totalvotes / $row9['MAX(po_count)'])*2.56;

	$poll_result = "<table>";
	$ii=1;
	while ($row6 = sed_sql_fetcharray($sql6))
	{
		$po_id = $row6['po_id'];
		$po_count = $row6['po_count'];
		$percent = @round(100 * ($po_count / $totalvotes),1);
		$percentbar = floor($percent * $coef);

		$poll_result .= "<tr><td>";

		if ($alreadyvoted || $ft_state)
		{ $poll_result .= $row6['po_text']; }
		else
		{ $poll_result .= "<a href=\"forums.php?m=posts&amp;q=$q&amp;a=send&amp;".sed_xg()."&amp;poll=".$ft_poll."&amp;vote=".$po_id."\">".$row6['po_text']."</a>"; }

		$poll_result .= "</td><td style=\"text-align:right;\">".$percent."%</td><td style=\"text-align:center; width:24px;\">(".$po_count.")</td>";
		$poll_result .= "<td style=\"text-align:left;\"><img src=\"skins/$skin/img/system/poll-bar1.gif\" height=\"12\" alt=\"\" />";
		$poll_result .= "<img src=\"skins/$skin/img/system/poll-bar2.gif\" width=\"$percentbar\" height=\"12\" alt=\"\" />";
		$poll_result .= "<img src=\"skins/$skin/img/system/poll-bar3.gif\" height=\"12\" alt=\"\" /></td></tr>";
	}
	$poll_result .= "</table><br />";

	if ($alreadyvoted)
	{ $poll_result .= ($votecasted) ? $L['polls_votecasted'] : $L['polls_alreadyvoted']; }
	else
	{ $poll_result .= $L['polls_notyetvoted']; }
}

$nbpages = ceil($totalposts / $cfg['maxtopicsperpage']);
$curpage = $d / $cfg['maxtopicsperpage'];
$notlastpage = (($d + $cfg['maxtopicsperpage'])<$totalposts) ? TRUE : FALSE;

$pages = sed_pagination("forums.php?m=posts&amp;q=$q", $d, $totalposts, $cfg['maxtopicsperpage']);
list($pages_prev, $pages_next) = sed_pagination_pn("forums.php?m=posts&amp;q=$q", $d, $totalposts, $cfg['maxtopicsperpage'], TRUE);

$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s LEFT JOIN
$db_forum_structure AS n ON n.fn_code=s.fs_category
ORDER by fn_path ASC, fs_order ASC");

$movebox = "<input type=\"submit\" class=\"submit\" value=\"".$L['Move']."\" /><select name=\"ns\" size=\"1\">";
$jumpbox .= "<select name=\"jumpbox\" size=\"1\" onchange=\"redirect(this)\">";
$jumpbox .= "<option value=\"forums.php\">".$L['Forums']."</option>";

while ($row1 = sed_sql_fetcharray($sql1))
{
	if (sed_auth('forums', $row1['fs_id'], 'R'))
	{
		$cfs = sed_build_forums($row1['fs_id'], $row1['fs_title'], $row1['fs_category'], FALSE);

		if ($row1['fs_id'] != $s && $usr['isadmin'])
		{ $movebox .= "<option value=\"".$row1['fs_id']."\">".$cfs."</option>"; }
		$selected = ($row1['fs_id']==$s) ? "selected=\"selected\"" : '';
		$jumpbox .= "<option $selected value=\"forums.php?m=topics&amp;s=".$row1['fs_id']."\">".$cfs."</option>";
	}
}

$movebox .= "</select> ".$L['Ghost']."<input type=\"checkbox\" class=\"checkbox\" name=\"ghost\" checked=\"checked\" />";
$jumpbox .= "</select>";

if ($usr['isadmin'])
{
	$adminoptions = "<form id=\"movetopic\" action=\"forums.php?m=topics&amp;a=move&amp;".sed_xg()."&amp;s=".$s."&amp;q=".$q."\" method=\"post\">";
	$adminoptions .= $L['Topicoptions']." : <a href=\"forums.php?m=topics&amp;a=bump&amp;".sed_xg()."&amp;q=".$q."&amp;s=".$s."\">".$L['Bump'];
	$adminoptions .= "</a> &nbsp; <a href=\"forums.php?m=topics&amp;a=lock&amp;".sed_xg()."&amp;q=".$q."&amp;s=".$s."\">".$L['Lock'];
	$adminoptions .= "</a> &nbsp; <a href=\"forums.php?m=topics&amp;a=sticky&amp;".sed_xg()."&amp;q=".$q."&amp;s=".$s."\">".$L['Makesticky'];
	$adminoptions .= "</a> &nbsp; <a href=\"forums.php?m=topics&amp;a=announcement&amp;".sed_xg()."&amp;q=".$q."&amp;s=".$s."\">".$L['Announcement'];
	$adminoptions .= "</a> &nbsp; <a href=\"forums.php?m=topics&amp;a=private&amp;".sed_xg()."&amp;q=".$q."&amp;s=".$s."\">".$L['Private']." (#)";
	$adminoptions .= "</a> &nbsp; <a href=\"forums.php?m=topics&amp;a=clear&amp;".sed_xg()."&amp;q=".$q."&amp;s=".$s."\">".$L['Default'];
	$adminoptions .= "</a> &nbsp; &nbsp; ".$movebox." &nbsp; &nbsp; ".$L['Delete'].":[<a href=\"forums.php?m=topics&amp;a=delete&amp;".sed_xg()."&amp;s=".$s."&amp;q=".$q."\">x</a>]</form>";
}
else
{ $adminoptions = "&nbsp;"; }

if ($ft_poll>0)
{ $ft_title = $L['Poll'].": ".$ft_title; }

$ft_title = ($ft_mode==1) ? "# ".sed_cc($ft_title) : sed_cc($ft_title);

$toptitle = "<a href=\"forums.php\">".$L['Forums']."</a> ".$cfg['separator']." ".sed_build_forums($s, $fs_title, $fs_category);
$toptitle .= " ".$cfg['separator']." <a href=\"forums.php?m=posts&amp;q=".$q."\">".$ft_title."</a>";
$toptitle .= ($usr['isadmin']) ? " *" : '';

$t->assign(array(
	"FORUMS_POSTS_PAGETITLE" => $toptitle,
	"FORUMS_POSTS_TOPICDESC" => sed_cc($ft_desc),
	"FORUMS_POSTS_SUBTITLE" => $adminoptions,
	"FORUMS_POSTS_PAGES" => $pages,
	"FORUMS_POSTS_PAGEPREV" => $pages_prev,
	"FORUMS_POSTS_PAGENEXT" => $pages_next,
	"FORUMS_POSTS_POLL" => $poll_result,
	"FORUMS_POSTS_JUMPBOX" => $jumpbox,
));

$totalposts = sed_sql_numrows($sql);

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('forums.posts.loop');
/* ===== */

while ($row = sed_sql_fetcharray($sql))
{
	$row['fp_text'] = sed_cc($row['fp_text']);
	$row['fp_created'] = @date($cfg['dateformat'], $row['fp_creation'] + $usr['timezone'] * 3600)." ".$usr['timetext'];
	$row['fp_updated_ago'] = sed_build_timegap($row['fp_updated'], $sys['now_offset']);
	$row['fp_updated'] = @date($cfg['dateformat'], $row['fp_updated'] + $usr['timezone'] * 3600)." ".$usr['timetext'];
	$row['user_text'] = ($fs_allowusertext) ? $row['user_text'] : '';
	$lastposterid = $row['fp_posterid'];
	$lastposterip = $row['fp_posterip'];
	$fp_num++;

	$adminoptions = ($usr['id']>0) ? "<a href=\"forums.php?m=posts&amp;s=".$s."&amp;q=".$q."&amp;quote=".$row['fp_id']."&amp;n=last#np\">".$L['Quote']."</a>" : "&nbsp;";
	$adminoptions .= (($usr['isadmin'] || $row['fp_posterid']==$usr['id']) && $usr['id']>0) ? " &nbsp; <a href=\"forums.php?m=editpost&amp;s=".$s."&amp;q=".$q."&amp;p=".$row['fp_id']."&amp;".sed_xg()."\">".$L['Edit']."</a>" : '';
	$adminoptions .= ($usr['id']>0 && ($usr['isadmin'] || $row['fp_posterid']==$usr['id']) && !($post12[0]==$row['fp_id'] && $post12[1]>0)) ? " &nbsp; ".$L['Delete'].":[<a href=\"forums.php?m=posts&amp;a=delete&amp;".sed_xg()."&amp;s=".$s."&amp;q=".$q."&amp;p=".$row['fp_id']."\">x</a>]" : '';
	$adminoptions .= ($fp_num==$totalposts) ? "<a name=\"bottom\" id=\"bottom\"></a>" : '';

	if ($usr['id']>0 && $n=='unread' && !$unread_done && $row['fp_creation']>$usr['lastvisit'])
	{
		$unread_done = TRUE;
		$adminoptions .= "<a name=\"unread\" id=\"unread\"></a>";
	}

	$row['fp_posterip'] = ($usr['isadmin']) ? sed_build_ipsearch($row['fp_posterip']) : '';
	if($cfg['parser_cache'])
	{
		if(empty($row['fp_html']) && !empty($row['fp_text']))
		{
			$row['fp_html'] = sed_parse($row['fp_text'], $cfg['parsebbcodeforums']  && $fs_allowbbcodes, $cfg['parsesmiliesforums']  && $fs_allowsmilies, 1);
			sed_sql_query("UPDATE $db_forum_posts SET fp_html = '".sed_sql_prep($row['fp_html'])."' WHERE fp_id = " . $row['fp_id']);
		}
		$row['fp_text'] = sed_post_parse($row['fp_html'], 'forums');
	}
	else
	{
		$row['fp_text'] = sed_parse($row['fp_text'], ($cfg['parsebbcodeforums'] && $fs_allowbbcodes), ($cfg['parsesmiliesforums'] && $fs_allowsmilies), 1);
		$row['fp_text'] = sed_post_parse($row['fp_text'], 'forums');
	}
	$row['fp_useronline'] = (sed_userisonline($row['fp_posterid'])) ? "1" : "0";

	if (!empty($row['fp_updater']))
	{ $row['fp_updatedby'] = sprintf($L['for_updatedby'], sed_cc($row['fp_updater']), $row['fp_updated'], $row['fp_updated_ago']); }

	if (!$cache[$row['fp_posterid']]['cached'])
	{
		$row['user_text'] = sed_parse($row['user_text'], $cfg['parsebbcodeusertext'], $cfg['parsesmiliesusertext'], 1);
		$row['user_age'] = ($row['user_birthdate']!=0) ? sed_build_age($row['user_birthdate']) : '';
		$cache[$row['fp_posterid']]['user_text'] = $row['user_text'];
		$cache[$row['fp_posterid']]['user_age']= $row['user_age'];
		$cache[$row['fp_posterid']]['cached'] = TRUE;
	}
	else
	{
		$row['user_text'] = $cache[$row['fp_posterid']]['user_text'];
		$row['user_journal'] = $cache[$row['fp_posterid']]['user_journal'];
		$row['user_age'] = $cache[$row['fp_posterid']]['user_age'];
	}

	$t-> assign(array(
		"FORUMS_POSTS_ROW_ID" => $row['fp_id'],
		"FORUMS_POSTS_ROW_IDURL" => "<a id=\"".$row['fp_id']."\" href=\"forums.php?m=posts&amp;p=".$row['fp_id']."#".$row['fp_id']."\">".$row['fp_id']."</a>",
		"FORUMS_POSTS_ROW_CREATION" => $row['fp_created'],
		"FORUMS_POSTS_ROW_UPDATED" => $row['fp_updated'],
		"FORUMS_POSTS_ROW_UPDATER" => sed_cc($row['fp_updater']),
		"FORUMS_POSTS_ROW_UPDATEDBY" => $row['fp_updatedby'],
		"FORUMS_POSTS_ROW_TEXT" => $row['fp_text'],
		"FORUMS_POSTS_ROW_POSTERNAME" => sed_build_user($row['fp_posterid'], sed_cc($row['fp_postername'])),
		"FORUMS_POSTS_ROW_POSTERID" => $row['fp_posterid'],
		"FORUMS_POSTS_ROW_MAINGRP" => sed_build_group($row['user_maingrp']),
		"FORUMS_POSTS_ROW_MAINGRPID" => $row['user_maingrp'],
		"FORUMS_POSTS_ROW_MAINGRPSTARS" => sed_build_stars($sed_groups[$row['user_maingrp']]['level']),
		"FORUMS_POSTS_ROW_MAINGRPICON" => sed_build_userimage($sed_groups[$row['user_maingrp']]['icon']),
		"FORUMS_POSTS_ROW_USERTEXT" => $row['user_text'],
		"FORUMS_POSTS_ROW_AVATAR" => sed_build_userimage($row['user_avatar']),
		"FORUMS_POSTS_ROW_PHOTO" => sed_build_userimage($row['user_photo']),
		"FORUMS_POSTS_ROW_SIGNATURE" => sed_build_userimage($row['user_signature']),
		"FORUMS_POSTS_ROW_GENDER" => $row['user_gender'] = ($row['user_gender']=='' || $row['user_gender']=='U') ? '' : $L["Gender_".$row['user_gender']],
		"FORUMS_POSTS_ROW_USEREXTRA1" => sed_cc($row['user_extra1']),
		"FORUMS_POSTS_ROW_USEREXTRA2" => sed_cc($row['user_extra2']),
		"FORUMS_POSTS_ROW_USEREXTRA3" => sed_cc($row['user_extra3']),
		"FORUMS_POSTS_ROW_USEREXTRA4" => sed_cc($row['user_extra4']),
		"FORUMS_POSTS_ROW_USEREXTRA5" => sed_cc($row['user_extra5']),
		"FORUMS_POSTS_ROW_USEREXTRA6" => sed_cc($row['user_extra6']),
		"FORUMS_POSTS_ROW_USEREXTRA7" => sed_cc($row['user_extra7']),
		"FORUMS_POSTS_ROW_USEREXTRA8" => sed_cc($row['user_extra8']),
		"FORUMS_POSTS_ROW_USEREXTRA9" => sed_cc($row['user_extra9']),
		"FORUMS_POSTS_ROW_POSTERIP" => $row['fp_posterip'],
		"FORUMS_POSTS_ROW_USERONLINE" => $row['fp_useronline'],
		"FORUMS_POSTS_ROW_ADMIN" => $adminoptions,
		"FORUMS_POSTS_ROW_COUNTRY" => $sed_countries[$row['user_country']],
		"FORUMS_POSTS_ROW_COUNTRYFLAG" => sed_build_flag($row['user_country']),
		"FORUMS_POSTS_ROW_WEBSITE" => sed_build_url($row['user_website'], 36),
		"FORUMS_POSTS_ROW_WEBSITERAW" => $row['user_website'],
		"FORUMS_POSTS_ROW_JOURNAL" => $row['user_journal'],
		"FORUMS_POSTS_ROW_EMAIL" => sed_build_email($row['user_email'], $row['user_hideemail']),
		"FORUMS_POSTS_ROW_LOCATION" => sed_cc($row['user_location']),
		"FORUMS_POSTS_ROW_OCCUPATION" => sed_cc($row['user_occupation']),
		"FORUMS_POSTS_ROW_AGE" => $row['user_age'],
		"FORUMS_POSTS_ROW_POSTCOUNT" => $row['user_postcount'],
		"FORUMS_POSTS_ROW_ODDEVEN" => sed_build_oddeven($fp_num),
		"FORUMS_POSTS_ROW" => $row,
	));

	/* === Hook - Part2 : Include === */
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t->parse("MAIN.FORUMS_POSTS_ROW");
}

$allowreplybox = (!$cfg['antibumpforums']) ? TRUE : FALSE;
$allowreplybox = ($cfg['antibumpforums'] && $lastposterid>0 && $lastposterid==$usr['id'] && $usr['auth_write']) ? FALSE : TRUE;

// Nested quote stripper by Spartan
function sed_stripquote($string) {
	global $sys;
	$starttime = $sys['now'];
	$startindex = stripos($string,'[quote');
	while ($startindex>=0) {
		if (($sys['now']-$starttime)>2000) { break; }
		$stopindex = strpos($string,'[/quote]');
		if ($stopindex>0) {
			if (($sys['now']-$starttime)>3000) { break; }
			$fragment = substr($string,$startindex,($stopindex-$startindex+8));
			$string = str_ireplace($fragment,'',$string);
			$stopindex = stripos($string,'[/quote]');
		} else { break; }
		$string = trim($string);
		$startindex = stripos($string,'[quote');
	}
	return($string);
}

if (!$notlastpage && !$ft_state && $usr['id']>0 && $allowreplybox && $usr['auth_write'])
{
	if ($quote>0)
	{
		$sql4 = sed_sql_query("SELECT fp_id, fp_text, fp_postername, fp_posterid FROM $db_forum_posts WHERE fp_topicid='$q' AND fp_sectionid='$s' AND fp_id='$quote' LIMIT 1");

		if ($row4 = sed_sql_fetcharray($sql4))
		{
			$newmsg = "[quote][url=forums.php?m=posts&p=".$row4['fp_id']."#".$row4['fp_id']."]#[/url] [b]".$row4['fp_postername']." :[/b]\n".sed_stripquote($row4['fp_text'])."\n[/quote]";
		}
	}

	$pfs = ($usr['id']>0) ? sed_build_pfs($usr['id'], "newpost", "newmsg", $L['Mypfs']) : '';
	$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, "newpost", "newmsg", $L['SFS']) : '';
	$smilies = ($cfg['parsesmiliesforums'] && $fs_allowsmilies) ? sed_build_smilies("newpost", "newmsg", $L['Smilies']) : '';
	$smilies_local = ($cfg['parsesmiliesforums'] && $fs_allowsmilies) ? sed_build_smilies_local(20) : '';
	$bbcodes = ($cfg['parsebbcodeforums'] && $fs_allowbbcodes) ? sed_build_bbcodes("newpost", "newmsg", $L['BBcodes']): '';
	$bbcodes_local = ($cfg['parsebbcodeforums'] && $fs_allowbbcodes) ? sed_build_bbcodes_local(99) : '';

	$post_mark = "<a name=\"np\" id=\"np\"></a>";
	$post_main = $post_mark.'<textarea class="editor" name="newmsg" rows="16" cols="56">'.sed_cc($newmsg).'</textarea>';

	$t->assign(array(
		"FORUMS_POSTS_NEWPOST_SEND" => "forums.php?m=posts&amp;a=newpost&amp;s=".$s."&amp;q=".$q,
		"FORUMS_POSTS_NEWPOST_TEXT" => $post_main."<br />".$bbcodes." ".$smilies." ".$pfs,
		"FORUMS_POSTS_NEWPOST_TEXTONLY" => $post_main,
		"FORUMS_POSTS_NEWPOST_TEXTBOXER" => $post_main."<br />".$bbcodes." ".$smilies." ".$pfs,
		"FORUMS_POSTS_NEWPOST_SMILIES" => $smilies,
		"FORUMS_POSTS_NEWPOST_BBCODES" => $bbcodes,
		"FORUMS_POSTS_NEWPOST_SMILIESLOCAL" => $smilies_local,
		"FORUMS_POSTS_NEWPOST_BBCODESLOCAL" => $bbcodes_local,
		"FORUMS_POSTS_NEWPOST_MYPFS" => $pfs
	));

	/* === Hook  === */
	$extp = sed_getextplugins('forums.posts.newpost.tags');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t->parse("MAIN.FORUMS_POSTS_NEWPOST");
}

elseif ($ft_state)
{
	$t->assign("FORUMS_POSTS_TOPICLOCKED_BODY", $L['Topiclocked']);
	$t->parse("MAIN.FORUMS_POSTS_TOPICLOCKED");
}

elseif(!$allowreplybox && !$notlastpage && !$ft_state && $usr['id']>0)
{
	$t->assign("FORUMS_POSTS_ANTIBUMP_BODY", $L['for_antibump']);
	$t->parse("MAIN.FORUMS_POSTS_ANTIBUMP");
}

if ($ft_mode==1)
{ $t->parse("MAIN.FORUMS_POSTS_TOPICPRIVATE"); }

/* === Hook  === */
$extp = sed_getextplugins('forums.posts.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once("system/footer.php");

?>
