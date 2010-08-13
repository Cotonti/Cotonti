<?php

/**
 * Forums posts display.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
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
$unread_done = FALSE;
$fp_num = 0;

if (!$cfg['disable_polls']) require_once sed_incfile('functions', 'polls');
require_once sed_langfile('countries', 'core');

unset ($notlastpage);

/* === Hook === */
$extp = sed_getextplugins('forums.posts.first');
foreach ($extp as $pl)
{
	include $pl;
}
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
	WHERE fp_topicid='$q' AND fp_updated > ". $usr['lastvisit']."
		ORDER by fp_id ASC LIMIT 1");
	if ($row = sed_sql_fetcharray($sql))
	{
		$p = $row['fp_id'];
		$q = $row['fp_topicid'];
		$s = $row['fp_sectionid'];
		$fp_posterid = $row['fp_posterid'];
	}
}
elseif (!empty($p))
{
	$sql = sed_sql_query("SELECT fp_topicid, fp_sectionid, fp_posterid
	FROM $db_forum_posts WHERE fp_id='$p' LIMIT 1");
	if ($row = sed_sql_fetcharray($sql))
	{
		$q = $row['fp_topicid'];
		$s = $row['fp_sectionid'];
		$fp_posterid = $row['fp_posterid'];
	}
	else
	{ sed_die(); }
}
elseif (!empty($id))
{
	$sql = sed_sql_query("SELECT fp_topicid, fp_sectionid, fp_posterid FROM $db_forum_posts WHERE fp_id='$id' LIMIT 1");
	if ($row = sed_sql_fetcharray($sql))
	{
		$p = $id;
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
	$fs_masterid = $row['fs_masterid'];
	$fs_mastername = $row['fs_mastername'];

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', $s);
	
	/* === Hook === */
	$extp = sed_getextplugins('forums.posts.rights');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	
	sed_block($usr['auth_read']);

	if ($fs_state)
	{
		sed_redirect(sed_url('message', "msg=602", '', true));
	}
}
else
{ sed_die(); }

$sys['sublocation'] = $fs_title;
sed_online_update();

$cat = $sed_forums_str[$s];

if ($a=='newpost')
{
	sed_shield_protect();

	$sql = sed_sql_query("SELECT ft_state, ft_lastposterid, ft_updated FROM $db_forum_topics WHERE ft_id='$q'");

	if ($row = sed_sql_fetcharray($sql))
	{
		if ($row['ft_state'])
		{ sed_die(); }
		$merge = (!$cfg['antibumpforums'] && $cfg['mergeforumposts'] && $row['ft_lastposterid']==$usr['id']) ? true : false;
		if ($merge && $cfg['mergetimeout']>0 && ( ($sys['now_offset']-$row['ft_updated'])>($cfg['mergetimeout']*3600) ) )
			{ $merge = false; }
	}

	$sql = sed_sql_query("SELECT fp_posterid, fp_posterip FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id DESC LIMIT 1");

	if ($row = sed_sql_fetcharray($sql))
	{
		if ($cfg['antibumpforums'] && ( ($usr['id']==0 && $row['fp_posterid']==0 && $row['fp_posterip']==$usr['ip']) || ($row['fp_posterid']>0 && $row['fp_posterid']==$usr['id']) ))
		{
			sed_die();
		}
	}
	else
	{
		sed_die();
	}

	/* === Hook === */
	$extp = sed_getextplugins('forums.posts.newpost.first');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$newmsg = sed_import('newmsg','P','HTM');

	if (!$cot_error && !empty($newmsg) && !empty($s) && !empty($q))
	{

		if (!$merge)
		{
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

			$p = sed_sql_insertid();

			$sql = sed_sql_query("UPDATE $db_forum_topics SET
			ft_postcount=ft_postcount+1,
			ft_updated='".$sys['now_offset']."',
			ft_lastposterid='".$usr['id']."',
			ft_lastpostername='".sed_sql_prep($usr['name'])."'
			WHERE ft_id='$q'");

			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount+1 WHERE fs_id='$s'");
			$sql = ($fs_masterid>0) ? sed_sql_query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount+1 WHERE fs_id='$fs_masterid'") : '';


			if ($fs_countposts)
			{ $sql = sed_sql_query("UPDATE $db_users SET user_postcount=user_postcount+1 WHERE user_id='".$usr['id']."'"); }

			/* === Hook === */
			$extp = sed_getextplugins('forums.posts.newpost.done');
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			sed_forum_sectionsetlast($s);

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

			sed_shield_update(30, "New post");
			sed_redirect(sed_url('forums', "m=posts&q=".$q."&n=last", '#bottom', true));
		}
		else
		{
			if($cfg['parser_cache'])
			{
				$rhtml = sed_sql_prep(sed_parse(htmlspecialchars($newmsg), $cfg['parsebbcodeforums'] && $fs_allowbbcodes, $cfg['parsesmiliesforums'] && $fs_allowsmilies, 1));
			}
			else
			{
				$rhtml = '';
			}

			$sql = sed_sql_query("SELECT fp_id, fp_text, fp_html, fp_posterid, fp_creation, fp_updated, fp_updater FROM $db_forum_posts WHERE fp_topicid='".$q."' ORDER BY fp_creation DESC LIMIT 1");
			$row = sed_sql_fetcharray($sql);

			$p = (int) $row['fp_id'];

			$gap_base = empty($row['fp_updated']) ? $row['fp_creation'] : $row['fp_updated'];
			$updated = sprintf($L['for_mergetime'], sed_build_timegap($gap_base, $sys['now_offset']));

			$newmsg = sed_sql_prep($row['fp_text'])."\n\n[b]".$updated."[/b]\n\n".sed_sql_prep($newmsg);
			$newhtml = ($cfg['parser_cache']) ? sed_sql_prep($row['fp_html'])."<br /><br /><b>".$updated."</b><br /><br />".$rhtml : '';

			$rupdater = ($row['fp_posterid'] == $usr['id'] && ($sys['now_offset'] < $row['fp_updated'] + 300) && empty($row['fp_updater']) ) ? '' : $usr['name'];

			$sql = sed_sql_query("UPDATE $db_forum_posts SET fp_updated='".$sys['now_offset']."', fp_updater='".sed_sql_prep($rupdater)."', fp_text='".$newmsg."', fp_html='".$newhtml."', fp_posterip='".$usr['ip']."' WHERE fp_id='".$row['fp_id']."' LIMIT 1");
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_updated='".$sys['now_offset']."' WHERE ft_id='$q'");

			/* === Hook === */
			$extp = sed_getextplugins('forums.posts.newpost.done');
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			sed_forum_sectionsetlast($s);

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

			sed_shield_update(30, "New post");
			sed_redirect(sed_url('forums', "m=posts&q=".$q."&n=last", '#bottom', true));
		}
	}
}

elseif ($a=='delete' && $usr['id']>0 && !empty($s) && !empty($q) && !empty($p) && ($usr['isadmin'] || $fp_posterid==$usr['id']))
{
	sed_check_xg();

	/* === Hook === */
	$extp = sed_getextplugins('forums.posts.delete.first');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql2 = sed_sql_query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id ASC LIMIT 2");

	while ($row2 = sed_sql_fetcharray($sql2))
	{
		$post12[] = $row2['fp_id'];
	}
	if ($post12[0]==$p && $post12[1]>0)
	{
		sed_die();
	}

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

			if (!$cfg['disable_polls'])
			{
				sed_poll_delete($q, 'forum');
			}

			$sql = sed_sql_query("UPDATE $db_forum_sections SET
			fs_topiccount=fs_topiccount-1,
			fs_topiccount_pruned=fs_topiccount_pruned+1,
			fs_postcount=fs_postcount-1,
			fs_postcount_pruned=fs_postcount_pruned+1
			WHERE fs_id='$s'");

			if ($fs_masterid>0)
			{
				$sql = sed_sql_query("UPDATE $db_forum_sections SET
				fs_topiccount=fs_topiccount-1,
				fs_topiccount_pruned=fs_topiccount_pruned+1,
				fs_postcount=fs_postcount-1,
				fs_postcount_pruned=fs_postcount_pruned+1
				WHERE fs_id='$fs_masterid'");
			}

			/* === Hook === */
			$extp = sed_getextplugins('forums.posts.emptytopicdel');
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			sed_log("Delete topic #".$q." (no post left)",'for');
			sed_forum_sectionsetlast($s);
		}
		sed_redirect(sed_url('forums', "m=topics&s=".$s, '', true));
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

			if ($fs_masterid>0)
			{
				$sql = sed_sql_query("UPDATE $db_forum_sections SET
				fs_postcount=fs_postcount-1,
				fs_postcount_pruned=fs_postcount_pruned+1
				WHERE fs_id='$fs_masterid'");
			}

			sed_forum_sectionsetlast($s);

			$sql = sed_sql_query("SELECT fp_id FROM $db_forum_posts
			WHERE fp_topicid='$q' AND fp_sectionid='$s' AND fp_id<$p
			ORDER BY fp_id DESC LIMIT 1");

			if ($row = sed_sql_fetcharray($sql))
			{
				sed_redirect(sed_url('forums', "m=posts&p=".$row['fp_id'], '#'.$row['fp_id'], true));
			}
		}
	}
}

//$sql = sed_sql_query("SELECT ft_id, ft_title, ft_desc, ft_mode, ft_state, ft_poll, ft_firstposterid FROM $db_forum_topics WHERE ft_id='$q'");
$sql = sed_sql_query("SELECT t.*, p.* FROM $db_forum_topics AS t LEFT JOIN $db_polls AS p ON t.ft_id=p.poll_code WHERE t.ft_id='$q' AND (p.poll_type='forum' OR p.poll_id IS NULL)");

if ($row = sed_sql_fetcharray($sql))
{
	$ft_title = $row['ft_title'];
	$ft_desc = $row['ft_desc'];
	$ft_mode = $row['ft_mode'];
	$ft_state = $row['ft_state'];
	$ft_poll = $row;
    $ft_poll_id = $row['poll_id'];
	$ft_firstposterid = $row['ft_firstposterid'];

	if ($ft_mode==1 && !($usr['isadmin'] || $ft_firstposterid==$usr['id']))
	{ sed_die(); }
}
else
{ sed_die(); }

$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_viewcount=ft_viewcount+1 WHERE ft_id='$q'");
$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_viewcount=fs_viewcount+1 WHERE fs_id='$s'");
$sql = ($fs_masterid>0) ? sed_sql_query("UPDATE $db_forum_sections SET fs_viewcount=fs_viewcount+1 WHERE fs_id='$fs_masterid'") : '';
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$q'");
$totalposts = sed_sql_result($sql,0,"COUNT(*)");

if (!empty($p))
{
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid = $q and fp_id < $p");
	$postsbefore = sed_sql_result($sql, 0, 0);
	$d = $cfg['maxpostsperpage'] * floor($postsbefore / $cfg['maxpostsperpage']);
}

if (empty($d))
{ $d = '0'; }

if ($usr['id']>0)
{ $morejavascript .= sed_build_addtxt('newpost', 'newmsg'); }

$user_extrafields = "";
//Extra fields for users
foreach($sed_extrafields['users'] as $i => $row)
{
	$user_extrafields .= "u.user_{$row['field_name']}, ";
}


if (!empty($id))
{
	$sql = sed_sql_query("SELECT p.*, u.user_text, u.user_maingrp, u.user_avatar, u.user_photo, u.user_signature,
	$user_extrafields
	u.user_country, u.user_occupation, u.user_location, u.user_website, u.user_email, u.user_hideemail, u.user_gender, u.user_birthdate,
	u.user_postcount
	FROM $db_forum_posts AS p LEFT JOIN $db_users AS u ON u.user_id=p.fp_posterid
	WHERE fp_topicid='$q' AND fp_id='$id' ");
}
else
{
	$sql = sed_sql_query("SELECT p.*, u.user_text, u.user_maingrp, u.user_avatar, u.user_photo, u.user_signature,
	$user_extrafields
	u.user_country, u.user_occupation, u.user_location, u.user_website, u.user_email, u.user_hideemail, u.user_gender, u.user_birthdate,
	u.user_postcount
	FROM $db_forum_posts AS p LEFT JOIN $db_users AS u ON u.user_id=p.fp_posterid
	WHERE fp_topicid='$q'
	ORDER BY fp_id LIMIT $d, ".$cfg['maxpostsperpage']);
}

$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => $fs_title,
	'TITLE' => $ft_title
);
$out['subtitle'] = sed_title('title_forum_posts', $title_params);
$out['desc'] = htmlspecialchars(strip_tags($ft_desc));

/* === Hook === */
$extp = sed_getextplugins('forums.posts.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('forums', 'posts', $fs_category, $s));
$t = new XTemplate($mskin);

if (!$cfg['disable_polls'] && $ft_poll_id)
{
	sed_poll_vote();
	$poll_form=sed_poll_form($ft_poll, sed_url('forums', 'm=posts&q='.$q), '', 'forum');
	$t->assign(array(
		"POLLS_TITLE" => sed_parse(htmlspecialchars($poll_form['poll_text']), 1, 1, 1),
		"POLLS_FORM" => $poll_form['poll_block'],
	));

	$t->parse("MAIN.POLLS_VIEW");

	if ($alreadyvoted)
	{ $extra = ($votecasted) ? $L['polls_votecasted'] : $L['polls_alreadyvoted']; }
	else
	{ $extra = $L['polls_notyetvoted']; }

	$t->assign(array(
		"POLLS_EXTRATEXT" => $extra,
	));

	$t->parse("MAIN.POLLS_EXTRA");


}

$nbpages = ceil($totalposts / $cfg['maxpostsperpage']);
$curpage = $d / $cfg['maxpostsperpage'];
$notlastpage = (($d + $cfg['maxpostsperpage'])<$totalposts) ? TRUE : FALSE;

$pagenav = sed_pagenav('forums', "m=posts&q=$q", $d, $totalposts, $cfg['maxpostsperpage']);

$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category, s.fs_masterid, s.fs_mastername, s.fs_allowpolls FROM $db_forum_sections AS s LEFT JOIN
$db_forum_structure AS n ON n.fn_code=s.fs_category
ORDER by fn_path ASC, fs_masterid, fs_order ASC");

$movebox = "<input type=\"submit\" class=\"submit\" value=\"".$L['Move']."\" /><select name=\"ns\" size=\"1\">";
$jumpbox .= "<select name=\"jumpbox\" size=\"1\" onchange=\"redirect(this)\">";
$jumpbox .= "<option value=\"".sed_url('forums')."\">".$L['Forums']."</option>";

while ($row1 = sed_sql_fetcharray($sql1))
{
	if (sed_auth('forums', $row1['fs_id'], 'R'))
	{

		if ( ($ft_poll_id>0 && $row1['fs_allowpolls']) || ($ft_poll_id==0) )
		{

			$master = ($row1['fs_masterid'] > 0) ? array($row1['fs_masterid'], $row1['fs_mastername']) : false;

			$cfs = sed_build_forums($row1['fs_id'], $row1['fs_title'], $row1['fs_category'], FALSE, $master);

			if ($row1['fs_id'] != $s && $usr['isadmin'])
			{ $movebox .= "<option value=\"".$row1['fs_id']."\">".$cfs."</option>"; }
			$selected = ($row1['fs_id']==$s) ? "selected=\"selected\"" : '';
			$jumpbox .= "<option $selected value=\"".sed_url('forums', "m=topics&s=".$row1['fs_id'])."\">".$cfs."</option>";

		}

	}
}

$movebox .= "</select> ".$L['for_keepmovedlink']." <input type=\"checkbox\" class=\"checkbox\" name=\"ghost\" />";
$jumpbox .= "</select>";

if ($usr['isadmin'])
{
	$adminoptions = "<form id=\"movetopic\" action=\"".sed_url('forums', "m=topics&a=move&".sed_xg()."&s=".$s."&q=".$q)."\" method=\"post\">";
	$adminoptions .= $L['Topicoptions']." : <a href=\"".sed_url('forums', "m=topics&a=bump&".sed_xg()."&q=".$q."&s=".$s)."\">".$L['Bump'];
	$adminoptions .= "</a> &nbsp; <a href=\"".sed_url('forums', "m=topics&a=lock&".sed_xg()."&q=".$q."&s=".$s)."\">".$L['Lock'];
	$adminoptions .= "</a> &nbsp; <a href=\"".sed_url('forums', "m=topics&a=sticky&".sed_xg()."&q=".$q."&s=".$s)."\">".$L['Makesticky'];
	$adminoptions .= "</a> &nbsp; <a href=\"".sed_url('forums', "m=topics&a=announcement&".sed_xg()."&q=".$q."&s=".$s)."\">".$L['Announcement'];
	$adminoptions .= "</a> &nbsp; <a href=\"".sed_url('forums', "m=topics&a=private&".sed_xg()."&q=".$q."&s=".$s)."\">".$L['Private']." (#)";
	$adminoptions .= "</a> &nbsp; <a href=\"".sed_url('forums', "m=topics&a=clear&".sed_xg()."&q=".$q."&s=".$s)."\">".$L['Default'];
	$adminoptions .= "</a> &nbsp; &nbsp; ".$movebox." &nbsp; &nbsp; ".$L['Delete'].":[<a href=\"".sed_url('forums', "m=topics&a=delete&".sed_xg()."&s=".$s."&q=".$q)."\">x</a>]</form>";
}
else
{ $adminoptions = "&nbsp;"; }

if ($ft_poll_id>0)
{ $ft_title = $L['Poll'].": ".$ft_title; }

$ft_title = ($ft_mode==1) ? "# ".htmlspecialchars($ft_title) : htmlspecialchars($ft_title);

$master = ($fs_masterid > 0) ? array($fs_masterid, $fs_mastername) : false;

$toptitle = sed_build_forums($s, $fs_title, $fs_category, true, $master);
$toppath  = $toptitle;
$toptitle .= ' ' . $cfg['separator'] . ' ' . $ft_title;
$toptitle .= ($usr['isadmin']) ? " *" : '';

$t->assign(array(
	"FORUMS_POSTS_ID" => $q,
	"FORUMS_POSTS_RSS" => sed_url('rss', "c=topics&id=$q"),
	"FORUMS_POSTS_PAGETITLE" => $toptitle,
	"FORUMS_POSTS_TOPICDESC" => htmlspecialchars($ft_desc),
    "FORUMS_POSTS_SHORTTITLE" => $ft_title,
    "FORUMS_POSTS_PATH" => $toppath,
	"FORUMS_POSTS_SUBTITLE" => $adminoptions,
	"FORUMS_POSTS_PAGES" => $pagenav['main'],
	"FORUMS_POSTS_PAGEPREV" => $pagenav['prev'],
	"FORUMS_POSTS_PAGENEXT" => $pagenav['next'],
	"FORUMS_POSTS_POLL" => $poll_result,
	"FORUMS_POSTS_JUMPBOX" => $jumpbox,
));

$totalposts = sed_sql_numrows($sql);
$fp_num=0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('forums.posts.loop');
/* ===== */

while ($row = sed_sql_fetcharray($sql))
{
	$row['fp_text'] = htmlspecialchars($row['fp_text']);
	$row['fp_created'] = @date($cfg['dateformat'], $row['fp_creation'] + $usr['timezone'] * 3600);
	$row['fp_updated_ago'] = sed_build_timegap($row['fp_updated'], $sys['now_offset']);
	$row['fp_updated'] = @date($cfg['dateformat'], $row['fp_updated'] + $usr['timezone'] * 3600);
	$row['user_text'] = ($fs_allowusertext) ? $row['user_text'] : '';
	$lastposterid = $row['fp_posterid'];
	$lastposterip = $row['fp_posterip'];
	$fp_num++;
	$i = empty($id) ? $d + $fp_num : $id;

	$rowquote  = ($usr['id']>0) ? sed_rc('frm_rowquote', array('url' => sed_url('forums', "m=posts&s=".$s."&q=".$q."&quote=".$row['fp_id']."&n=last", "#np"))) : '';
	$rowedit   = (($usr['isadmin'] || $row['fp_posterid']==$usr['id']) && $usr['id']>0) ? sed_rc('frm_rowedit', array('url' => sed_url('forums', "m=editpost&s=".$s."&q=".$q."&p=".$row['fp_id']."&".sed_xg()))) : '';
	$rowdelete = ($usr['id']>0 && ($usr['isadmin'] || $row['fp_posterid']==$usr['id']) && !($post12[0]==$row['fp_id'] && $post12[1]>0)) ? sed_rc('frm_rowdelete', array('url' => sed_url('forums', "m=posts&a=delete&".sed_xg()."&s=".$s."&q=".$q."&p=".$row['fp_id']))) : '';
	$rowdelete .= ($fp_num==$totalposts) ? "<a name=\"bottom\" id=\"bottom\"></a>" : '';
    $adminoptions = $rowquote.' &nbsp; '.$rowedit.' &nbsp; '.$rowdelete;

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

	if (!empty($row['fp_updater']))
	{ $row['fp_updatedby'] = sprintf($L['for_updatedby'], htmlspecialchars($row['fp_updater']), $row['fp_updated'], $row['fp_updated_ago']); }

	$t->assign(sed_generate_usertags($row, "FORUMS_POSTS_ROW_")); // Lieve only best variant(some tags use has different key mask
	$t->assign(sed_generate_usertags($row, "FORUMS_POSTS_ROW_USER"));
	$t-> assign(array(
		"FORUMS_POSTS_ROW_ID" => $row['fp_id'],
		"FORUMS_POSTS_ROW_POSTID" => 'post_'.$row['fp_id'],
		"FORUMS_POSTS_ROW_IDURL" => sed_url('forums', "m=posts&id=".$row['fp_id']),
		"FORUMS_POSTS_ROW_URL" => sed_url('forums', "m=posts&p=".$row['fp_id'], "#".$row['fp_id']),
		"FORUMS_POSTS_ROW_CREATION" => $row['fp_created'],
		"FORUMS_POSTS_ROW_UPDATED" => $row['fp_updated'],
		"FORUMS_POSTS_ROW_UPDATER" => htmlspecialchars($row['fp_updater']),
		"FORUMS_POSTS_ROW_UPDATEDBY" => $row['fp_updatedby'],
		"FORUMS_POSTS_ROW_TEXT" => $row['fp_text'],
		"FORUMS_POSTS_ROW_ANCHORLINK" => "<a name=\"post{$row['fp_id']}\" id=\"post{$row['fp_id']}\"></a>",
		"FORUMS_POSTS_ROW_POSTERNAME" => sed_build_user($row['fp_posterid'], htmlspecialchars($row['fp_postername'])),
		"FORUMS_POSTS_ROW_POSTERID" => $row['fp_posterid'],
		"FORUMS_POSTS_ROW_POSTERIP" => $row['fp_posterip'],
        "FORUMS_POSTS_ROW_DELETE" => $rowdelete,
        "FORUMS_POSTS_ROW_EDIT" => $rowedit,
        "FORUMS_POSTS_ROW_QUOTE" => $rowquote,
		"FORUMS_POSTS_ROW_ADMIN" => $adminoptions,
		"FORUMS_POSTS_ROW_ODDEVEN" => sed_build_oddeven($fp_num),
        "FORUMS_POSTS_ROW_NUM" => $fp_num,
		"FORUMS_POSTS_ROW_ORDER" => $i,
		"FORUMS_POSTS_ROW" => $row,
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse("MAIN.FORUMS_POSTS_ROW");
}

$allowreplybox = (!$cfg['antibumpforums']) ? TRUE : FALSE;
$allowreplybox = ($cfg['antibumpforums'] && $lastposterid>0 && $lastposterid==$usr['id'] && $usr['auth_write']) ? FALSE : TRUE;

// Nested quote stripper by Spartan
function sed_stripquote($string) {
	global $sys;
	$starttime = $sys['now'];
	$startindex = mb_stripos($string,'[quote');
	while ($startindex>=0) {
		if (($sys['now']-$starttime)>2000) { break; }
		$stopindex = mb_strpos($string,'[/quote]');
		if ($stopindex>0) {
			if (($sys['now']-$starttime)>3000) { break; }
			$fragment = mb_substr($string,$startindex,($stopindex-$startindex+8));
			$string = str_ireplace($fragment,'',$string);
			$stopindex = mb_stripos($string,'[/quote]');
		} else { break; }
		$string = trim($string);
		$startindex = mb_stripos($string,'[quote');
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

    // FIXME PFS dependency
	//$pfs = ($usr['id']>0) ? sed_build_pfs($usr['id'], "newpost", "newmsg", $L['Mypfs']) : '';
	//$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, "newpost", "newmsg", $L['SFS']) : '';

	$post_mark = "<a name=\"np\" id=\"np\"></a>";
	$post_main = $post_mark.'<textarea class="editor" name="newmsg" rows="16" cols="56">'.htmlspecialchars($newmsg).'</textarea>';

	$t->assign(array(
		"FORUMS_POSTS_NEWPOST_SEND" => sed_url('forums', "m=posts&a=newpost&s=".$s."&q=".$q),
		"FORUMS_POSTS_NEWPOST_TEXT" => $post_main."<br />".$pfs,
		"FORUMS_POSTS_NEWPOST_TEXTONLY" => $post_main,
		"FORUMS_POSTS_NEWPOST_TEXTBOXER" => $post_main."<br />".$pfs,
		"FORUMS_POSTS_NEWPOST_MYPFS" => $pfs
	));

	if (sed_check_messages())
	{
		$t->assign('FORUMS_POSTS_NEWPOST_ERROR_MSG', sed_implode_messages());
		$t->parse('MAIN.FORUMS_POSTS_NEWPOST.FORUMS_POSTS_NEWPOST_ERROR');
		sed_clear_messages();
	}

	/* === Hook  === */
	$extp = sed_getextplugins('forums.posts.newpost.tags');
	foreach ($extp as $pl)
	{
		include $pl;
	}
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
{
	$t->parse("MAIN.FORUMS_POSTS_TOPICPRIVATE");
}

/* === Hook  === */
$extp = sed_getextplugins('forums.posts.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

if ($cot_cache && $usr['id'] === 0 && $cfg['cache_forums'])
{
	$cot_cache->page->write();
}

?>