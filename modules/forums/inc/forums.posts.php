<?php

/**
 * Forums posts display.
 *
 * @package forums
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

$id = cot_import('id','G','INT');
$s = cot_import('s','G','ALP');
$q = cot_import('q','G','INT');
$p = cot_import('p','G','INT');
$d = cot_import('d','G','INT');
$d = ((int)$d > 0) ? (int)$d : 0;
$quote = cot_import('quote','G','INT');
$unread_done = FALSE;
$fp_num = 0;

require_once cot_langfile('countries', 'core');

unset ($notlastpage);

/* === Hook === */
foreach (cot_getextplugins('forums.posts.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($n=='last' && !empty($q))
{
	$sql = $db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid
	FROM $db_forum_posts
	WHERE fp_topicid='$q'
	ORDER by fp_id DESC LIMIT 1");
	if ($row = $sql->fetch())
	{
		$p = $row['fp_id'];
		$q = $row['fp_topicid'];
		$s = $row['fp_cat'];
		$fp_posterid = $row['fp_posterid'];
	}
}
elseif ($n=='unread' && !empty($q) && $usr['id']>0)
{
	$sql = $db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid
	FROM $db_forum_posts
	WHERE fp_topicid='$q' AND fp_updated > ". $usr['lastvisit']."
		ORDER by fp_id ASC LIMIT 1");
	if ($row = $sql->fetch())
	{
		$p = $row['fp_id'];
		$q = $row['fp_topicid'];
		$s = $row['fp_cat'];
		$fp_posterid = $row['fp_posterid'];
	}
}
elseif (!empty($p))
{
	$sql = $db->query("SELECT fp_topicid, fp_cat, fp_posterid
	FROM $db_forum_posts WHERE fp_id='$p' LIMIT 1");
	if ($row = $sql->fetch())
	{
		$q = $row['fp_topicid'];
		$s = $row['fp_cat'];
		$fp_posterid = $row['fp_posterid'];
	}
	else
	{
		cot_die();
	}
}
elseif (!empty($id))
{
	$sql = $db->query("SELECT fp_topicid, fp_cat, fp_posterid FROM $db_forum_posts WHERE fp_id='$id' LIMIT 1");
	if ($row = $sql->fetch())
	{
		$p = $id;
		$q = $row['fp_topicid'];
		$s = $row['fp_cat'];
		$fp_posterid = $row['fp_posterid'];
	}
	else
	{
		cot_die();
	}
}
elseif (!empty($q))
{
	$sql = $db->query("SELECT ft_cat FROM $db_forum_topics WHERE ft_id='$q' LIMIT 1");
	if ($row = $sql->fetch())
	{
		$s = $row['ft_cat'];
	}
	else
	{
		cot_die();
	}
}

$sql = $db->query("SELECT * FROM $db_forum_sections WHERE fs_id='$s' LIMIT 1");

if ($row = $sql->fetch())
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

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', $s);

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.rights') as $pl)
	{
		include $pl;
	}
	/* ===== */

	cot_block($usr['auth_read']);

	if ($fs_state)
	{
		$env['status'] = '403 Forbidden';
		cot_redirect(cot_url('message', "msg=602", '', true));
	}
}
else
{ 
	cot_die();
}

$sys['sublocation'] = $fs_title;

$cat = $cot_forums_str[$s];

if ($a=='newpost')
{
	cot_shield_protect();

	$sql = $db->query("SELECT ft_state, ft_lastposterid, ft_updated FROM $db_forum_topics WHERE ft_id='$q'");

	if ($row = $sql->fetch())
	{
		if ($row['ft_state'])
		{
			cot_die();
		}
		$merge = (!$cfg['forums']['antibumpforums'] && $cfg['forums']['mergeforumposts'] && $row['ft_lastposterid']==$usr['id']) ? true : false;
		if ($merge && $cfg['forums']['mergetimeout']>0 && ( ($sys['now_offset']-$row['ft_updated'])>($cfg['forums']['mergetimeout']*3600) ) )
		{
			$merge = false;
		}
	}

	$sql = $db->query("SELECT fp_posterid, fp_posterip FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id DESC LIMIT 1");

	if ($row = $sql->fetch())
	{
		if ($cfg['forums']['antibumpforums'] && ( ($usr['id']==0 && $row['fp_posterid']==0 && $row['fp_posterip']==$usr['ip']) || ($row['fp_posterid']>0 && $row['fp_posterid']==$usr['id']) ))
		{
			cot_die();
		}
	}
	else
	{
		cot_die();
	}

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.newpost.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$newmsg = cot_import('newmsg','P','HTM');

	if (!$cot_error && !empty($newmsg) && !empty($s) && !empty($q))
	{

		if (!$merge)
		{
			$db->insert($db_forum_posts, array(
				'fp_topicid' => (int)$q,
				'fp_cat' => (int)$s,
				'fp_posterid' => (int)$usr['id'],
				'fp_postername' => $usr['name'],
				'fp_creation' => (int)$sys['now_offset'],
				'fp_updated' => (int)$sys['now_offset'],
				'fp_updater' => 0,
				'fp_text' => $newmsg,
				'fp_posterip' => $usr['ip']
			));

			$p = $db->lastInsertId();

			$sql = $db->query("UPDATE $db_forum_topics SET
			ft_postcount=ft_postcount+1,
			ft_updated='".$sys['now_offset']."',
			ft_lastposterid='".$usr['id']."',
			ft_lastpostername='".$db->prep($usr['name'])."'
			WHERE ft_id='$q'");

			$sql = $db->query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount+1 WHERE fs_id='$s'");
			$sql = ($fs_masterid>0) ? $db->query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount+1 WHERE fs_id='$fs_masterid'") : '';


			if ($fs_countposts)
			{
				$sql = $db->query("UPDATE $db_users SET user_postcount=user_postcount+1 WHERE user_id='".$usr['id']."'");
			}

			/* === Hook === */
			foreach (cot_getextplugins('forums.posts.newpost.done') as $pl)
			{
				include $pl;
			}
			/* ===== */

			cot_forum_sectionsetlast($s);

			if ($cache)
			{
				if ($cfg['cache_forums'])
				{
					$cache->page->clear('forums');
				}
				if ($cfg['cache_index'])
				{
					$cache->page->clear('index');
				}
			}

			cot_shield_update(30, "New post");
			cot_redirect(cot_url('forums', "m=posts&q=".$q."&n=last", '#bottom', true));
		}
		else
		{
			$sql = $db->query("SELECT fp_id, fp_text, fp_posterid, fp_creation, fp_updated, fp_updater FROM $db_forum_posts WHERE fp_topicid='".$q."' ORDER BY fp_creation DESC LIMIT 1");
			$row = $sql->fetch();

			$p = (int) $row['fp_id'];

			$gap_base = empty($row['fp_updated']) ? $row['fp_creation'] : $row['fp_updated'];
			$updated = sprintf($L['forums_mergetime'], cot_build_timegap($gap_base, $sys['now_offset']));

			$newmsg = $db->prep($row['fp_text']).cot_rc('forums_code_update', array('updated' => $updated)).$db->prep($newmsg);
			
			$rupdater = ($row['fp_posterid'] == $usr['id'] && ($sys['now_offset'] < $row['fp_updated'] + 300) && empty($row['fp_updater']) ) ? '' : $usr['name'];

			$sql = $db->query("UPDATE $db_forum_posts SET fp_updated='".$sys['now_offset']."', fp_updater='".$db->prep($rupdater)."', fp_text='".$newmsg."', fp_posterip='".$usr['ip']."' WHERE fp_id='".$row['fp_id']."' LIMIT 1");
			$sql = $db->query("UPDATE $db_forum_topics SET ft_updated='".$sys['now_offset']."' WHERE ft_id='$q'");

			/* === Hook === */
			foreach (cot_getextplugins('forums.posts.newpost.done') as $pl)
			{
				include $pl;
			}
			/* ===== */

			cot_forum_sectionsetlast($s);

			if ($cache)
			{
				if ($cfg['cache_forums'])
				{
					$cache->page->clear('forums');
				}
				if ($cfg['cache_index'])
				{
					$cache->page->clear('index');
				}
			}

			cot_shield_update(30, "New post");
			cot_redirect(cot_url('forums', "m=posts&q=".$q."&n=last", '#bottom', true));
		}
	}
}

elseif ($a=='delete' && $usr['id']>0 && !empty($s) && !empty($q) && !empty($p) && ($usr['isadmin'] || $fp_posterid==$usr['id']))
{
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.delete.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql2 = $db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id ASC LIMIT 2");

	while ($row2 = $sql2->fetch())
	{
		$post12[] = $row2['fp_id'];
	}
	if ($post12[0]==$p && $post12[1]>0)
	{
		cot_die();
	}

	$sql = $db->query("SELECT * FROM $db_forum_posts WHERE fp_id='$p' AND fp_topicid='$q' AND fp_cat='$s'");

	if ($row = $sql->fetch())
	{

	}
	else
	{
		cot_die();
	}

	$sql = $db->query("DELETE FROM $db_forum_posts WHERE fp_id='$p' AND fp_topicid='$q' AND fp_cat='$s'");

	if ($fs_countposts)
	{
		$sql = $db->query("UPDATE $db_users SET user_postcount=user_postcount-1 WHERE user_id='".$fp_posterid."' AND user_postcount>0");
	}

	cot_log("Deleted post #".$p, 'for');

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.delete.done') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($cache)
	{
		if ($cfg['cache_forums'])
		{
			$cache->page->clear('forums');
		}
		if ($cfg['cache_index'])
		{
			$cache->page->clear('index');
		}
	}

	$sql = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$q'");

	if ($sql->fetchColumn()==0)
	{
		// No posts left in this topic
		$sql = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id='$q'");

		if ($row = $sql->fetch())
		{
			$sql = $db->query("DELETE FROM $db_forum_topics WHERE ft_movedto='$q'");
			$sql = $db->query("DELETE FROM $db_forum_topics WHERE ft_id='$q'");

			$sql = $db->query("UPDATE $db_forum_sections SET
			fs_topiccount=fs_topiccount-1,
			fs_postcount=fs_postcount-1
			WHERE fs_id='$s'");

			if ($fs_masterid>0)
			{
				$sql = $db->query("UPDATE $db_forum_sections SET
				fs_topiccount=fs_topiccount-1,
				fs_postcount=fs_postcount-1
				WHERE fs_id='$fs_masterid'");
			}

			/* === Hook === */
			foreach (cot_getextplugins('forums.posts.emptytopicdel') as $pl)
			{
				include $pl;
			}
			/* ===== */

			cot_log("Delete topic #".$q." (no post left)",'for');
			cot_forum_sectionsetlast($s);
		}
		cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
	}
	else
	{
		// There's at least 1 post left, let's resync
		$sql = $db->query("SELECT fp_id, fp_posterid, fp_postername, fp_updated
		FROM $db_forum_posts
		WHERE fp_topicid='$q' AND fp_cat='$s'
		ORDER BY fp_id DESC LIMIT 1");

		if ($row = $sql->fetch())
		{
			$sql = $db->query("UPDATE $db_forum_topics SET
			ft_postcount=ft_postcount-1,
			ft_lastposterid='".(int)$row['fp_posterid']."',
			ft_lastpostername='".$db->prep($row['fp_postername'])."',
			ft_updated='".(int)$row['fp_updated']."'
			WHERE ft_id='$q'");

			$sql = $db->query("UPDATE $db_forum_sections SET
			fs_postcount=fs_postcount-1
			WHERE fs_id='$s'");

			if ($fs_masterid>0)
			{
				$sql = $db->query("UPDATE $db_forum_sections SET
				fs_postcount=fs_postcount-1
				WHERE fs_id='$fs_masterid'");
			}

			cot_forum_sectionsetlast($s);

			$sql = $db->query("SELECT fp_id FROM $db_forum_posts
			WHERE fp_topicid='$q' AND fp_cat='$s' AND fp_id<$p
			ORDER BY fp_id DESC LIMIT 1");

			if ($row = $sql->fetch())
			{
				cot_redirect(cot_url('forums', "m=posts&p=".$row['fp_id'], '#'.$row['fp_id'], true));
			}
		}
	}
}

$sql = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id='$q'");

if ($row = $sql->fetch())
{
	$ft_title = $row['ft_title'];
	$ft_desc = $row['ft_desc'];
	$ft_mode = $row['ft_mode'];
	$ft_state = $row['ft_state'];
	$ft_firstposterid = $row['ft_firstposterid'];

	if ($ft_mode==1 && !($usr['isadmin'] || $ft_firstposterid==$usr['id']))
	{
		cot_die();
	}
}
else
{ 
	cot_die();
}

$sql = $db->query("UPDATE $db_forum_topics SET ft_viewcount=ft_viewcount+1 WHERE ft_id='$q'");
$sql = $db->query("UPDATE $db_forum_sections SET fs_viewcount=fs_viewcount+1 WHERE fs_id='$s'");
$sql = ($fs_masterid>0) ? $db->query("UPDATE $db_forum_sections SET fs_viewcount=fs_viewcount+1 WHERE fs_id='$fs_masterid'") : '';
$sql = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$q'");
$totalposts = $sql->fetchColumn();

if (!empty($p))
{
	$sql = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid = $q and fp_id < $p");
	$postsbefore = $sql->fetchColumn();
	$d = $cfg['forums']['maxpostsperpage'] * floor($postsbefore / $cfg['forums']['maxpostsperpage']);
}


if ($usr['id']>0)
{
	$morejavascript .= cot_rc('forums_code_addtxt', array('c1' => 'newpost', 'c2' => 'newmsg'));
}


if (!empty($id))
{
	$sql = $db->query("SELECT p.*, u.*
	FROM $db_forum_posts AS p LEFT JOIN $db_users AS u ON u.user_id=p.fp_posterid
	WHERE fp_topicid='$q' AND fp_id='$id' ");
}
else
{
	$sql = $db->query("SELECT p.*, u.*
	FROM $db_forum_posts AS p LEFT JOIN $db_users AS u ON u.user_id=p.fp_posterid
	WHERE fp_topicid='$q'
	ORDER BY fp_id LIMIT $d, ".$cfg['forums']['maxpostsperpage']);
}

$title_params = array(
	'FORUM' => $L['Forums'],
	'SECTION' => $fs_title,
	'TITLE' => $ft_title
);
$out['subtitle'] = cot_title('title_forum_posts', $title_params);
$out['desc'] = htmlspecialchars(strip_tags($ft_desc));

/* === Hook === */
foreach (cot_getextplugins('forums.posts.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_skinfile(array('forums', 'posts', $fs_category, $s));
$t = new XTemplate($mskin);

$nbpages = ceil($totalposts / $cfg['forums']['maxpostsperpage']);
$curpage = $d / $cfg['forums']['maxpostsperpage'];
$notlastpage = (($d + $cfg['forums']['maxpostsperpage'])<$totalposts) ? TRUE : FALSE;

$pagenav = cot_pagenav('forums', "m=posts&q=$q", $d, $totalposts, $cfg['forums']['maxpostsperpage']);

cot_require_api('forms');

$jumpbox[cot_url('forums')] = $L['Forums'];
foreach($structure['forums'] as $key => $val)
{
	if (cot_auth('forums', $key, 'R'))
	{
		if ($val['tpath'] != $s)
		{
			$movebox[$key] = $val['tpath'];
		}
		$jumpbox[cot_url('forums', "m=topics&s=".$key, '', true)] = $val['tpath'];
	}
}
$jumpbox = cot_selectbox($s, 'jumpbox', array_keys($jumpbox), array_values($jumpbox), false, 'onchange="redirect(this)"');

if ($usr['isadmin'])
{
	$postsoptions = cot_rc('forums_adminoptions', array(
		'move_url' => cot_url('forums', 'm=topics&a=move&s='.$s.'&q='.$q.'&x='.$sys['xk']),
		'bump_url' => cot_url('forums', 'm=topics&a=bump&s='.$s.'&q='.$q.'&x='.$sys['xk']),
		'lock_url' => cot_url('forums', 'm=topics&a=lock&s='.$s.'&q='.$q.'&x='.$sys['xk']),
		'sticky_url' => cot_url('forums', 'm=topics&a=sticky&s='.$s.'&q='.$q.'&x='.$sys['xk']),
		'announce_url' => cot_url('forums', 'm=topics&a=announcement&s='.$s.'&q='.$q.'&x='.$sys['xk']),
		'private_url' => cot_url('forums', 'm=topics&a=private&s='.$s.'&q='.$q.'&x='.$sys['xk']),
		'clear_url' => cot_url('forums', 'm=topics&a=clear&s='.$s.'&q='.$q.'&x='.$sys['xk']),
		'delete_url' => cot_url('forums', 'm=topics&a=delete&s='.$s.'&q='.$q.'&x='.$sys['xk']),
		'movebox_select' => cot_selectbox('', 'ns', array_keys($movebox), array_values($movebox), false),
		'movebox_keep' => cot_checkbox('0', 'ghost')
	));
}

$ft_title = ($ft_mode == 1) ? "# ".htmlspecialchars($ft_title) : htmlspecialchars($ft_title);

$toptitle = cot_build_forumpath($s);
$toppath  = $toptitle;
$toptitle .= ' ' . $cfg['separator'] . ' ' . $ft_title;
$toptitle .= ($usr['isadmin']) ? $R['forums_code_admin_mark'] : '';

$t->assign(array(
	"FORUMS_POSTS_ID" => $q,
	"FORUMS_POSTS_RSS" => cot_url('rss', "c=topics&id=$q"),
	"FORUMS_POSTS_PAGETITLE" => $toptitle,
	"FORUMS_POSTS_TOPICDESC" => htmlspecialchars($ft_desc),
	"FORUMS_POSTS_SHORTTITLE" => $ft_title,
	"FORUMS_POSTS_PATH" => $toppath,
	"FORUMS_POSTS_OPTIONS" => $postsoptions,
	"FORUMS_POSTS_PAGES" => $pagenav['main'],
	"FORUMS_POSTS_PAGEPREV" => $pagenav['prev'],
	"FORUMS_POSTS_PAGENEXT" => $pagenav['next'],
	"FORUMS_POSTS_JUMPBOX" => $jumpbox,
));

$totalposts = $sql->rowCount();
$fp_num=0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.posts.loop');
/* ===== */

while ($row = $sql->fetch())
{
	$row['fp_created'] = @date($cfg['dateformat'], $row['fp_creation'] + $usr['timezone'] * 3600);
	$row['fp_updated_ago'] = cot_build_timegap($row['fp_updated'], $sys['now_offset']);
	$row['fp_updated'] = @date($cfg['dateformat'], $row['fp_updated'] + $usr['timezone'] * 3600);
	$row['user_text'] = ($fs_allowusertext) ? $row['user_text'] : '';
	$lastposterid = $row['fp_posterid'];
	$lastposterip = $row['fp_posterip'];
	$fp_num++;
	$i = empty($id) ? $d + $fp_num : $id;

	$rowquote  = ($usr['id']>0) ? cot_rc('forums_rowquote', array('url' => cot_url('forums', "m=posts&s=".$s."&q=".$q."&quote=".$row['fp_id']."&n=last", "#np"))) : '';
	$rowedit   = (($usr['isadmin'] || $row['fp_posterid']==$usr['id']) && $usr['id']>0) ? cot_rc('forums_rowedit', array('url' => cot_url('forums', "m=editpost&s=".$s."&q=".$q."&p=".$row['fp_id']."&".cot_xg()))) : '';
	$rowdelete = ($usr['id']>0 && ($usr['isadmin'] || $row['fp_posterid']==$usr['id']) && !($post12[0]==$row['fp_id'] && $post12[1]>0)) ? cot_rc('forums_rowdelete', array('url' => cot_url('forums', "m=posts&a=delete&".cot_xg()."&s=".$s."&q=".$q."&p=".$row['fp_id']))) : '';
	$rowdelete .= ($fp_num==$totalposts) ? $R['forums_code_bottom'] : '';
	$adminoptions = cot_rc('forums_code_post_adminoptions', array(
		'quote' => $rowquote,
		'edit' => $rowedit,
		'delete' => $rowdelete
	));

	if ($usr['id']>0 && $n=='unread' && !$unread_done && $row['fp_creation']>$usr['lastvisit'])
	{
		$unread_done = TRUE;
		$adminoptions .= $R['forums_code_unread'];
	}

	$row['fp_posterip'] = ($usr['isadmin']) ? cot_build_ipsearch($row['fp_posterip']) : '';

	if (!empty($row['fp_updater']))
	{
		$row['fp_updatedby'] = sprintf($L['forums_updatedby'], htmlspecialchars($row['fp_updater']), $row['fp_updated'], $row['fp_updated_ago']);
	}

	$t->assign(cot_generate_usertags($row, "FORUMS_POSTS_ROW_USER"));
	$t-> assign(array(
		"FORUMS_POSTS_ROW_ID" => $row['fp_id'],
		"FORUMS_POSTS_ROW_POSTID" => 'post_'.$row['fp_id'],
		"FORUMS_POSTS_ROW_IDURL" => cot_url('forums', "m=posts&id=".$row['fp_id']),
		"FORUMS_POSTS_ROW_URL" => cot_url('forums', "m=posts&p=".$row['fp_id'], "#".$row['fp_id']),
		"FORUMS_POSTS_ROW_CREATION" => $row['fp_created'],
		"FORUMS_POSTS_ROW_UPDATED" => $row['fp_updated'],
		"FORUMS_POSTS_ROW_UPDATER" => htmlspecialchars($row['fp_updater']),
		"FORUMS_POSTS_ROW_UPDATEDBY" => $row['fp_updatedby'],
		"FORUMS_POSTS_ROW_TEXT" => cot_parse($row['fp_text'], ($cfg['forums']['markup'] && $fs_allowbbcodes)),
		"FORUMS_POSTS_ROW_ANCHORLINK" => cot_rc('forums_code_post_anchor', array('id' => $row['fp_id'])),
		"FORUMS_POSTS_ROW_POSTERNAME" => cot_build_user($row['fp_posterid'], htmlspecialchars($row['fp_postername'])),
		"FORUMS_POSTS_ROW_POSTERID" => $row['fp_posterid'],
		"FORUMS_POSTS_ROW_POSTERIP" => $row['fp_posterip'],
		"FORUMS_POSTS_ROW_DELETE" => $rowdelete,
		"FORUMS_POSTS_ROW_EDIT" => $rowedit,
		"FORUMS_POSTS_ROW_QUOTE" => $rowquote,
		"FORUMS_POSTS_ROW_ADMIN" => $adminoptions,
		"FORUMS_POSTS_ROW_ODDEVEN" => cot_build_oddeven($fp_num),
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

$allowreplybox = (!$cfg['forums']['antibumpforums']) ? TRUE : FALSE;
$allowreplybox = ($cfg['forums']['antibumpforums'] && $lastposterid>0 && $lastposterid==$usr['id'] && $usr['auth_write']) ? FALSE : TRUE;

// Nested quote stripper by Spartan
function cot_stripquote($string)
{
	global $sys, $R;
	$starttime = $sys['now'];
	$startindex = mb_stripos($string, $R['forums_code_quote_begin']);
	while ($startindex>=0)
	{
		if (($sys['now']-$starttime)>2000)
		{
			break;
		}
		$stopindex = mb_strpos($string, $R['forums_code_quote_close']);
		if ($stopindex>0)
		{
			if (($sys['now']-$starttime)>3000)
			{
				break;
			}
			$fragment = mb_substr($string,$startindex,($stopindex-$startindex+8));
			$string = str_ireplace($fragment,'',$string);
			$stopindex = mb_stripos($string, $R['forums_code_quote_close']);
		} else
		{
			break;
		}
		$string = trim($string);
		$startindex = mb_stripos($string, $R['forums_code_quote_begin']);
	}
	return($string);
}

if (!$notlastpage && !$ft_state && $usr['id']>0 && $allowreplybox && $usr['auth_write'])
{
	if ($quote>0)
	{
		$sql4 = $db->query("SELECT fp_id, fp_text, fp_postername, fp_posterid FROM $db_forum_posts WHERE fp_topicid='$q' AND fp_cat='$s' AND fp_id='$quote' LIMIT 1");

		if ($row4 = $sql4->fetch())
		{
			$newmsg = cot_rc('forums_code_quote', array(
				'url' => cot_url('forums', 'm=posts&p=' . $row4['fp_id'] . '#' . $row4['fp_id']),
				'id' => $row4['fp_id'],
				'postername' => $row4['fp_postername'],
				'text' => cot_stripquote($row4['fp_text'])
			));
		}
	}

	cot_require_api('forms');
	$post_mark = $R['forums_code_newpost_mark'];

	$t->assign(array(
		"FORUMS_POSTS_NEWPOST_SEND" => cot_url('forums', "m=posts&a=newpost&s=".$s."&q=".$q),
		"FORUMS_POSTS_NEWPOST_TEXT" => $post_mark . cot_textarea('newmsg', $newmsg, 16, 56, '', 'input_textarea_editor')
	));

	cot_display_messages($t);

	/* === Hook  === */
	foreach (cot_getextplugins('forums.posts.newpost.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse("MAIN.FORUMS_POSTS_NEWPOST");
}

elseif ($ft_state)
{
	$t->assign("FORUMS_POSTS_TOPICLOCKED_BODY", $L['forums_topiclocked']);
	$t->parse("MAIN.FORUMS_POSTS_TOPICLOCKED");
}

elseif(!$allowreplybox && !$notlastpage && !$ft_state && $usr['id']>0)
{
	$t->assign("FORUMS_POSTS_ANTIBUMP_BODY", $L['forums_antibump']);
	$t->parse("MAIN.FORUMS_POSTS_ANTIBUMP");
}

if ($ft_mode==1)
{
	$t->parse("MAIN.FORUMS_POSTS_TOPICPRIVATE");
}

/* === Hook  === */
foreach (cot_getextplugins('forums.posts.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_forums'])
{
	$cache->page->write();
}

?>
