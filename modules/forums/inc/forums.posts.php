<?php

/**
 * Forums posts display.
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

$id = cot_import('id', 'G', 'INT'); // post id
$s = cot_import('s', 'G', 'TXT');   // section category code
$q = cot_import('q', 'G', 'INT');   // topic id
$p = cot_import('p', 'G', 'INT');   // post id
list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['forums']['maxpostsperpage']); // page
$quote = cot_import('quote', 'G', 'INT');

require_once cot_langfile('countries', 'core');
require_once cot_incfile('forms');

/* === Hook === */
foreach (cot_getextplugins('forums.posts.first') as $pl) {
	include $pl;
}
/* ===== */

$fp_posterid = null;

if ((!empty($n) && !empty($q)) || !empty($p) || !empty($id)) {
	if (!empty($q) && ($n == 'last' || ($n == 'unread' && Cot::$usr['id'] == 0))) {
		$sql_forums = Cot::$db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid FROM $db_forum_posts
			WHERE fp_topicid = $q ORDER by fp_id DESC LIMIT 1");

    } elseif ($n == 'unread' && !empty($q) && Cot::$usr['id'] > 0) {
		$sql_forums = Cot::$db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid
			FROM $db_forum_posts WHERE fp_topicid = $q AND fp_updated > " . Cot::$usr['lastvisit'] . " ORDER by fp_id ASC LIMIT 1");
		if ($sql_forums->rowCount() == 0) {
			$sql_forums = Cot::$db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid FROM $db_forum_posts
			WHERE fp_topicid = $q ORDER by fp_id DESC LIMIT 1");
		}
	} elseif (!empty($p) || !empty($id)) {
		$p = ($p > 0) ? $p : $id;
		$sql_forums = Cot::$db->query("SELECT fp_id, fp_topicid, fp_cat, fp_posterid, fp_creation FROM $db_forum_posts WHERE fp_id = ?", $p);
	}
	if (isset($sql_forums) && is_object($sql_forums) && $sql_forums->rowCount() > 0) {
		$row = $sql_forums->fetch();
		$p = $row['fp_id'];
		$q = $row['fp_topicid'];
		$s = $row['fp_cat'];
		$fp_posterid = $row['fp_posterid'];
	}
} elseif (!empty($q)) {
	$sql_forums = Cot::$db->query("SELECT ft_cat FROM $db_forum_topics WHERE ft_id = $q LIMIT 1");
	if ($row = $sql_forums->fetch()) {
		$s = $row['ft_cat'];
	}
}

(empty($s)) && cot_die(true, true);
isset(Cot::$structure['forums'][$s]) || cot_die(true, true);

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('forums', $s);
/* === Hook === */
foreach (cot_getextplugins('forums.posts.rights') as $pl) {
	include $pl;
}
/* ===== */
cot_block(Cot::$usr['auth_read']);

$sys['sublocation'] = Cot::$structure['forums'][$s]['title'];

if ($a == 'newpost' && !empty($s) && !empty($q))
{
	cot_shield_protect();

	Cot::$db->query("SELECT ft_state FROM $db_forum_topics WHERE ft_id = $q")->fetchColumn() && cot_die();

	$sql_forums = Cot::$db->query("SELECT fp_id, fp_text, fp_posterid, fp_creation, fp_updated, fp_updater FROM $db_forum_posts
		WHERE fp_topicid = $q ORDER BY fp_creation DESC LIMIT 1");
	if ($row = $sql_forums->fetch())
	{
		if (Cot::$cfg['forums']['antibumpforums'] && ( (Cot::$usr['id'] == 0 && $row['fp_posterid'] == 0 &&
			$row['fp_posterip'] == Cot::$usr['ip']) || ($row['fp_posterid'] > 0 && $row['fp_posterid'] == Cot::$usr['id']) ))
		{
			cot_die();
		}
		$merge = (!Cot::$cfg['forums']['antibumpforums'] && Cot::$cfg['forums']['mergeforumposts'] && $row['fp_posterid'] == Cot::$usr['id']) ? true : false;
		$merge = ($merge && Cot::$cfg['forums']['mergetimeout'] > 0 && (($sys['now'] - $row['fp_updated']) > (Cot::$cfg['forums']['mergetimeout'] * 3600))) ? false : $merge;
	}
	else
	{
		cot_die();
	}
	$rmsg = array();
	$rmsg['fp_text'] = cot_import('rmsgtext', 'P', 'HTM');
	$rmsg['fp_updated'] = (int)$sys['now'];
	$rmsg['fp_posterip'] = Cot::$usr['ip'];

	if (mb_strlen($rmsg['fp_text']) < Cot::$cfg['forums']['minpostlength'])
	{
		cot_error('forums_messagetooshort', 'rmsgtext');
		cot_redirect(cot_url('forums', "m=posts&q=$q&n=last", '#bottom', true));
	}

	// Extra fields
	if(!empty(Cot::$extrafields[Cot::$db->forum_posts])) {
		foreach (Cot::$extrafields[Cot::$db->forum_posts] as $exfld) {
			$rmsg['fp_' . $exfld['field_name']] = cot_import_extrafields('rmsg' . $exfld['field_name'], $exfld, 'P', '',
                'forums_post_');
		}
	}

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.newpost.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!cot_error_found()) {
		if (!$merge) {
			$rmsg['fp_topicid'] = (int) $q;
			$rmsg['fp_cat'] = $s;
			$rmsg['fp_posterid'] = (int) Cot::$usr['id'];
			$rmsg['fp_postername'] = Cot::$usr['name'];
			$rmsg['fp_creation'] = (int) $sys['now'];
			$rmsg['fp_updater'] = 0;

			Cot::$db->insert($db_forum_posts, $rmsg);
			$p = Cot::$db->lastInsertId();

            cot_forums_resyncTopic($q, Cot::$usr['id']);
            cot_forums_updateStructureCounters($s);
		} else {
			$p = (int) $row['fp_id'];

			$gap_base = empty($row['fp_updated']) ? $row['fp_creation'] : $row['fp_updated'];
			$updated = sprintf(Cot::$L['forums_mergetime'], cot_build_timegap($gap_base, Cot::$sys['now']));

			$rmsg['fp_text'] = $row['fp_text'] . cot_rc('forums_code_update', array('updated' => $updated)) . $rmsg['fp_text'];
			$rmsg['fp_updater'] = ($row['fp_posterid'] == Cot::$usr['id'] && ($sys['now'] < $row['fp_updated'] + 300) && empty($row['fp_updater']) ) ? '' : Cot::$usr['name'];

			Cot::$db->update($db_forum_posts, $rmsg, 'fp_id=' . $row['fp_id']);
			Cot::$db->update($db_forum_topics, array('ft_updated' => $sys['now']), "ft_id = $q");

            cot_forums_resyncTopic($q, false);
            cot_forums_updateStructureCounters($s);
		}

		cot_extrafield_movefiles();

		/* === Hook === */
		foreach (cot_getextplugins('forums.posts.newpost.done') as $pl) {
			include $pl;
		}
		/* ===== */

		if (\Cot::$cache) {
            if (\Cot::$cfg['cache_forums']) {
                //\Cot::$cache->page->clearByUri(cot_url('forums', ['m' => 'posts', 'q' => $q]));
                \Cot::$cache->static->clearByUri(cot_url('forums'));
            }
            if (\Cot::$cfg['cache_index']) {
                \Cot::$cache->static->clear('index');
            }
		}

		cot_shield_update(30, "New post");
		cot_redirect(
            cot_url('forums', ['m' => 'posts', 'q' => $q, 'n' => 'last'], '#bottom', true)
        );
	}
} elseif (
    $a == 'delete'
    && Cot::$usr['id'] > 0
    && !empty($s)
    && !empty($q)
    && !empty($p)
    && (
        Cot::$usr['isadmin']
        || (
            $fp_posterid == Cot::$usr['id']
            && (
                Cot::$cfg['forums']['edittimeout'] == '0'
                || Cot::$sys['now'] - $row['fp_creation'] < Cot::$cfg['forums']['edittimeout'] * 3600
            )
        )
    )
) {
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.delete.first') as $pl) {
		include $pl;
	}
	/* ===== */

	$row = Cot::$db->query("SELECT * FROM $db_forum_posts WHERE fp_id = ? AND fp_topicid = ? AND fp_cat = ? LIMIT 1",
		array($p, $q, $s))->fetch();
	is_array($row) || cot_die();

	// If the post is first in the topic, then delete entire topic or show an error
	$first_id = Cot::$db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = ? LIMIT 1", array($q))->fetchColumn();
	if ($p == $first_id)
	{
		if (Cot::$usr['isadmin'])
		{
			// Redirect to topic removal confirmation
			cot_redirect(str_replace('&amp;', '&', cot_confirm_url(cot_url('forums', 'm=topics&a=delete&s='.$s.'&q='.$q.'&x='.$sys['xk'], '', true), 'forums', 'forums_confirm_delete_topic')));
		}
		else
		{
			// Users can't delete topics
			cot_die();
		}
	}

	foreach($cot_extrafields[$db_forum_posts] as $exfld) {
		cot_extrafield_unlinkfiles($row['fp_'.$exfld['field_name']], $exfld);
	}

	Cot::$db->delete($db_forum_posts, 'fp_id = ? AND fp_topicid = ? AND fp_cat = ?', array($p, $q, $s));

    cot_forums_resyncTopic($q, $fp_posterid);

	cot_log("Deleted post #" . $p, 'forums', 'delete post', 'done');

	/* === Hook === */
	foreach (cot_getextplugins('forums.posts.delete.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if (
        Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->forum_posts . ' WHERE fp_topicid = ?', $q)
            ->fetchColumn() == 0
    ) {
        // There is no posts left, delete topic
        $sqlTopic = Cot::$db->query('SELECT * FROM ' . Cot::$db->forum_topics . ' WHERE ft_id = ?', $q);
		if ($row = $sqlTopic->fetch()) {
            cot_forums_prunetopics('single', $s, $q);

			/* === Hook === */
			foreach (cot_getextplugins('forums.posts.emptytopicdel') as $pl) {
				include $pl;
			}
			/* ===== */

			cot_log('Delete topic #' . $q . " (no post left)", 'forums', 'delete topic', 'done');
		}

        cot_forums_updateStructureCounters($s);
		cot_redirect(cot_url('forums', ['m' => 'topics', 's' => $s], '', true));
	} else {
        // There's at least 1 post left
        cot_forums_updateStructureCounters($s);
        cot_redirect(
            cot_url(
                'forums',
                ['m' => 'posts', 'q' => $q, 'd' => $durl,],
                '#' . $row['fp_id'],
                true
            )
        );
	}
}

$sql_forums = Cot::$db->query("SELECT * FROM $db_forum_topics WHERE ft_id= $q");
if ($rowt = $sql_forums->fetch())
{
	if ($rowt['ft_mode'] == 1 && !(Cot::$usr['isadmin'] || $rowt['ft_firstposterid'] == Cot::$usr['id']))
	{
		cot_die();
	}
}
else
{
	cot_die(true, true);
}

$sql_forums = Cot::$db->query("UPDATE $db_forum_topics SET ft_viewcount=ft_viewcount+1 WHERE ft_id = $q");
$sql_forums = Cot::$db->query("UPDATE $db_forum_stats SET fs_viewcount=fs_viewcount+1 WHERE fs_cat = " . Cot::$db->quote($s));

$where['topicid'] = "fp_topicid = $q";
$order = 'fp_id ASC';
$join_columns = '';
$join_condition = '';

if (!empty($p))
{
	$p_id = $p;
	$postsbefore = Cot::$db->query("SELECT COUNT(*) FROM $db_forum_posts AS p $join_condition WHERE " . implode(' AND ', $where) . " AND fp_id < $p_id")->fetchColumn();
	$d = Cot::$cfg['forums']['maxpostsperpage'] * floor($postsbefore / Cot::$cfg['forums']['maxpostsperpage']);
	$durl = Cot::$cfg['easypagenav'] ? floor($d / Cot::$cfg['forums']['maxpostsperpage']) + 1 : $d;
}

if (!empty($id))
{
	$where['id'] = "fp_id = $id";
}

/* === Hook === */
foreach (cot_getextplugins('forums.posts.query') as $pl)
{
	include $pl;
}
/* ===== */

$where = array_diff($where, array(''));
$totalposts = Cot::$db->query("SELECT COUNT(*) FROM $db_forum_posts AS p $join_condition WHERE " . implode(' AND ', $where))->fetchColumn();

// Disallow accessing non-existent pages
if (empty($id) && $totalposts > 0 && $d > $totalposts)
{
	cot_die_message(404);
}

$orderlimit = empty($id) ? " ORDER BY $order LIMIT $d, " . Cot::$cfg['forums']['maxpostsperpage'] : '';

$sql_forums = Cot::$db->query("SELECT p.*, u.* $join_columns
	FROM $db_forum_posts AS p LEFT JOIN $db_users AS u ON u.user_id=p.fp_posterid $join_condition
	WHERE " . implode(' AND ', $where) . $orderlimit);

$title_params = array(
	'FORUM' => Cot::$L['Forums'],
	'SECTION' => Cot::$structure['forums'][$s]['title'],
	'TITLE' => $rowt['ft_title']
);
$out['subtitle'] = cot_title(Cot::$cfg['forums']['title_posts'], $title_params);
$out['desc'] = htmlspecialchars(strip_tags($rowt['ft_desc']));
$topicurl_params = array(
	'm' => 'posts',
	'q' => $q
);

if ( ($durl > 1 && Cot::$cfg['easypagenav']) || ($durl > 0 && !Cot::$cfg['easypagenav']) )
{
	$topicurl_params['d'] = $durl;
}
$out['canonical_uri'] = cot_url('forums', $topicurl_params);

/* === Hook === */
foreach (cot_getextplugins('forums.posts.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once Cot::$cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('forums', 'posts', Cot::$structure['forums'][$s]['tpl']));
$t = new XTemplate($mskin);


/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.posts.loop');
/* ===== */
$fp_num = 0;
foreach ($sql_forums->fetchAll() as $row)
{
	$row['user_text'] = (Cot::$cfg['forums']['cat_' . $s]['allowusertext']) ? $row['user_text'] : '';
    $row['fp_updatedby'] = '';
	$fp_num++;

	$rowquote_url = (Cot::$usr['id'] > 0) ? cot_url('forums', 'm=posts&s=' . $s . '&q=' . $q . '&quote=' . $row['fp_id'] . '&d=' . $durl . '&n=last', '#np') : '';
	$rowquote = (Cot::$usr['id'] > 0) ? cot_rc('forums_rowquote', array('url' => $rowquote_url)) : '';
	$rowedit_url = ((Cot::$usr['isadmin'] || ($row['fp_posterid'] == Cot::$usr['id'] && (Cot::$cfg['forums']['edittimeout'] == '0' || $sys['now'] - $row['fp_creation'] < Cot::$cfg['forums']['edittimeout'] * 3600))) && Cot::$usr['id'] > 0) ? cot_url('forums', 'm=editpost&s=' . $s . '&q=' . $q . '&p=' . $row['fp_id'] . '&d=' . $durl . '&' . cot_xg()) : '';
	$rowedit = ((Cot::$usr['isadmin'] || ($row['fp_posterid'] == Cot::$usr['id'] && (Cot::$cfg['forums']['edittimeout'] == '0' || $sys['now'] - $row['fp_creation'] < Cot::$cfg['forums']['edittimeout'] * 3600))) && Cot::$usr['id'] > 0) ? cot_rc('forums_rowedit', array('url' => $rowedit_url)) : '';
	$rowdelete_url = (Cot::$usr['id'] > 0 && (Cot::$usr['isadmin'] || ($row['fp_posterid'] == Cot::$usr['id'] && (Cot::$cfg['forums']['edittimeout'] == '0' || $sys['now'] - $row['fp_creation'] < Cot::$cfg['forums']['edittimeout'] * 3600)))) ? cot_confirm_url(cot_url('forums', 'm=posts&a=delete&' . cot_xg() . '&s=' . $s . '&q=' . $q . '&p=' . $row['fp_id'] . '&d=' . $durl), 'forums', 'forums_confirm_delete_post') : '';
	$rowdelete = (Cot::$usr['id'] > 0 && (Cot::$usr['isadmin'] || ($row['fp_posterid'] == Cot::$usr['id'] && (Cot::$cfg['forums']['edittimeout'] == '0' || $sys['now'] - $row['fp_creation'] < Cot::$cfg['forums']['edittimeout'] * 3600)) && $fp_num > 1)) ? cot_rc('forums_rowdelete', array('url' => $rowdelete_url)) : '';

	if (!empty($row['fp_updater']))
	{
		$row['fp_updatedby'] = sprintf(Cot::$L['forums_updatedby'], htmlspecialchars($row['fp_updater']), cot_date('datetime_medium', $row['fp_updated']), cot_build_timegap($row['fp_updated'], $sys['now']));
	}

	$t->assign(cot_generate_usertags($row, 'FORUMS_POSTS_ROW_USER'));
	$t->assign(array(
		'FORUMS_POSTS_ROW_ID' => $row['fp_id'],
		'FORUMS_POSTS_ROW_POSTID' => 'post_' . $row['fp_id'],
		'FORUMS_POSTS_ROW_IDURL' => cot_url('forums', 'm=posts&id=' . $row['fp_id']),
		'FORUMS_POSTS_ROW_URL' => cot_url('forums', 'm=posts&q=' . $row['fp_topicid'] . '&d=' . $durl, "#" . $row['fp_id']),
		'FORUMS_POSTS_ROW_CREATION' => cot_date('datetime_medium', $row['fp_creation']),
		'FORUMS_POSTS_ROW_CREATION_STAMP' => $row['fp_creation'],
		'FORUMS_POSTS_ROW_UPDATED' => cot_date('datetime_medium', $row['fp_updated']),
		'FORUMS_POSTS_ROW_UPDATED_STAMP' => $row['fp_updated'],
		'FORUMS_POSTS_ROW_UPDATER' => htmlspecialchars($row['fp_updater']),
		'FORUMS_POSTS_ROW_UPDATEDBY' => $row['fp_updatedby'],
		'FORUMS_POSTS_ROW_TEXT' => cot_parse($row['fp_text'], (Cot::$cfg['forums']['markup'] && Cot::$cfg['forums']['cat_' . $s]['allowbbcodes'])),
		'FORUMS_POSTS_ROW_ANCHORLINK' => cot_rc('forums_code_post_anchor', array('id' => $row['fp_id'])),
		'FORUMS_POSTS_ROW_POSTERNAME' => cot_build_user($row['fp_posterid'], $row['fp_postername']),
		'FORUMS_POSTS_ROW_POSTERID' => $row['fp_posterid'],
		'FORUMS_POSTS_ROW_POSTERIP' => (Cot::$usr['isadmin']) ? cot_build_ipsearch($row['fp_posterip']) : '',
		'FORUMS_POSTS_ROW_DELETE' => $rowdelete,
		'FORUMS_POSTS_ROW_DELETE_URL' => $rowdelete_url,
		'FORUMS_POSTS_ROW_EDIT' => $rowedit,
		'FORUMS_POSTS_ROW_EDIT_URL' => $rowedit_url,
		'FORUMS_POSTS_ROW_QUOTE' => $rowquote,
		'FORUMS_POSTS_ROW_QUOTE_URL' => $rowquote_url,
		'FORUMS_POSTS_ROW_BOTTOM' => ((empty($id) ? $d + $fp_num : $id) == $totalposts) ? Cot::$R['forums_code_bottom'] :
			((Cot::$usr['id'] > 0 && $n == 'unread' && $row['fp_creation'] > Cot::$usr['lastvisit']) ? Cot::$R['forums_code_unread'] : ''),
		'FORUMS_POSTS_ROW_ODDEVEN' => cot_build_oddeven($fp_num),
		'FORUMS_POSTS_ROW_NUM' => $fp_num,
		'FORUMS_POSTS_ROW_ORDER' => empty($id) ? $d + $fp_num : $id
	));

    if(!empty(Cot::$extrafields[Cot::$db->forum_posts])) {
        foreach (Cot::$extrafields[Cot::$db->forum_posts] as $exfld) {
            $tag = mb_strtoupper($exfld['field_name']);
            $exfld_title = cot_extrafield_title($exfld, 'forums_post_');
            $t->assign(array(
                'FORUMS_POSTS_ROW_' . $tag . '_TITLE' => $exfld_title,
                'FORUMS_POSTS_ROW_' . $tag => cot_build_extrafields_data('forums', $exfld,
                    $row['fp_' . $exfld['field_name']],
                    (Cot::$cfg['forums']['markup'] && Cot::$cfg['forums']['cat_' . $s]['allowbbcodes'])),
                'FORUMS_POSTS_ROW_' . $tag . '_VALUE' => $row['fp_' . $exfld['field_name']]
            ));
        }
    }

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.FORUMS_POSTS_ROW');
}

$lastpage = (($d + Cot::$cfg['forums']['maxpostsperpage']) < $totalposts) ? FALSE : TRUE;
$pagenav = cot_pagenav('forums', "m=posts&q=$q", $d, $totalposts, Cot::$cfg['forums']['maxpostsperpage']);

$jumpbox[cot_url('forums')] = Cot::$L['Forums'];
foreach (Cot::$structure['forums'] as $key => $val)
{
	if (cot_auth('forums', $key, 'R') && strpos($val['path'], '.'))
	{
		($val['tpath'] == $s) || $movebox[$key] = $val['tpath'];
		$jumpbox[cot_url('forums', 'm=topics&s=' . $key, '', true)] = $val['tpath'];
	}
}

if (Cot::$usr['isadmin'])
{
	$t->assign(array(
		'FORUMS_POSTS_MOVE_URL' => cot_url('forums', 'm=topics&a=move&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_BUMP_URL' => cot_url('forums', 'm=topics&a=bump&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_LOCK_URL' => cot_url('forums', 'm=topics&a=lock&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_STICKY_URL' => cot_url('forums', 'm=topics&a=sticky&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_ANNOUNCE_URL' => cot_url('forums', 'm=topics&a=announcement&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_PRIVATE_URL' => cot_url('forums', 'm=topics&a=private&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_CLEAR_URL' => cot_url('forums', 'm=topics&a=clear&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']),
		'FORUMS_POSTS_DELETE_URL' => cot_confirm_url(cot_url('forums', 'm=topics&a=delete&s=' . $s . '&q=' . $q . '&x=' . $sys['xk']), 'forums', 'forums_confirm_delete_topic'),
		'FORUMS_POSTS_MOVEBOX_SELECT' => cot_selectbox('', 'ns', array_keys($movebox), array_values($movebox), false),
		'FORUMS_POSTS_MOVEBOX_KEEP' => cot_checkbox('0', 'ghost')
	));
	$t->parse('MAIN.FORUMS_POSTS_ADMIN');
}

$allowreplybox = (Cot::$cfg['forums']['antibumpforums'] && $row['fp_posterid'] > 0 && $row['fp_posterid'] == Cot::$usr['id'] && Cot::$usr['auth_write']) ? FALSE : TRUE;

if ((Cot::$cfg['forums']['enablereplyform'] || $lastpage) && !$rowt['ft_state'] && Cot::$usr['id'] > 0 && $allowreplybox && Cot::$usr['auth_write'])
{
    $rmsg = null;
	if ($quote > 0)
	{
		$sql_forums_quote = Cot::$db->query("SELECT fp_id, fp_text, fp_postername, fp_posterid, fp_creation FROM $db_forum_posts
			WHERE fp_topicid = ? AND fp_cat = ? AND fp_id = ? LIMIT 1",
			array($q, $s, $quote));
		if ($row4 = $sql_forums_quote->fetch())
		{
			$rmsg['fp_text'] = cot_rc('forums_code_quote', array(
				'url' => cot_url('forums', 'm=posts&q=' . $q . '&d=' . $durl, '#' . $row4['fp_id'], $forums_quote_htmlspecialchars_bypass),
				'id' => $row4['fp_id'],
				'date' => cot_date('datetime_medium', $row4['fp_creation']),
				'postername' => $row4['fp_postername'],
				'text' => $row4['fp_text']
			));
		}
	}

	// Extra fields
    if(!empty(Cot::$extrafields[Cot::$db->forum_posts])) {
        foreach (Cot::$extrafields[Cot::$db->forum_posts] as $exfld) {
            $uname = strtoupper($exfld['field_name']);
            $exfld_val = cot_build_extrafields('rmsg' . $exfld['field_name'], $exfld, $rmsg[$exfld['field_name']]);
            $exfld_title = cot_extrafield_title($exfld, 'forums_post_');

            $t->assign(array(
                'FORUMS_POSTS_NEWPOST_' . $uname => $exfld_val,
                'FORUMS_POSTS_NEWPOST_' . $uname . '_TITLE' => $exfld_title,
                'FORUMS_POSTS_NEWPOST_EXTRAFLD' => $exfld_val,
                'FORUMS_POSTS_NEWPOST_EXTRAFLD_TITLE' => $exfld_title
            ));
            $t->parse('MAIN.FORUMS_POSTS_NEWPOST.EXTRAFLD');
        }
    }

    $text = '';
    if (isset($rmsg['fp_text'])) $text = $rmsg['fp_text'];
	$t->assign(array(
		'FORUMS_POSTS_NEWPOST_SEND' => cot_url('forums', "m=posts&a=newpost&s=" . $s . "&q=" . $q),
		'FORUMS_POSTS_NEWPOST_TEXT' => Cot::$R['forums_code_newpost_mark'] . cot_textarea('rmsgtext', $text,
                16, 56, '', 'input_textarea_'.$minimaxieditor),
        'FORUMS_POSTS_NEWPOST_EDITTIMEOUT' => cot_build_timegap(0, Cot::$cfg['forums']['edittimeout'] * 3600)
	));

	cot_display_messages($t);

	/* === Hook  === */
	foreach (cot_getextplugins('forums.posts.newpost.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.FORUMS_POSTS_NEWPOST');

} elseif ($rowt['ft_state']) {
	$t->assign('FORUMS_POSTS_TOPICLOCKED_BODY', Cot::$L['forums_topiclocked']);
	$t->parse('MAIN.FORUMS_POSTS_TOPICLOCKED');

} elseif (!$allowreplybox && (Cot::$cfg['forums']['enablereplyform'] || $lastpage) && !$rowt['ft_state'] && Cot::$usr['id'] > 0) {
	$t->assign('FORUMS_POSTS_ANTIBUMP_BODY', Cot::$L['forums_antibump']);
	$t->parse('MAIN.FORUMS_POSTS_ANTIBUMP');
}

if ($rowt['ft_mode'] == 1) {
	$t->parse('MAIN.FORUMS_POSTS_TOPICPRIVATE');
}


$rowt['ft_title'] = (($rowt['ft_mode'] == 1) ? '# ' : '') . $rowt['ft_title'];

$crumbs = cot_forums_buildpath($s);
$toppath = cot_breadcrumbs($crumbs, Cot::$cfg['homebreadcrumb'], false);
$crumbs[] = $rowt['ft_title'];
$toptitle = cot_breadcrumbs($crumbs, Cot::$cfg['homebreadcrumb'], true);
$toptitle .= ( Cot::$usr['isadmin']) ? Cot::$R['forums_code_admin_mark'] : '';

$t->assign(array(
	'FORUMS_POSTS_ID' => $q,
	'FORUMS_POSTS_RSS' => cot_url('rss', "m=topics&c=$q"),
	'FORUMS_POSTS_PAGETITLE' => $toptitle,
	'FORUMS_POSTS_TOPICDESC' => htmlspecialchars($rowt['ft_desc']),
	'FORUMS_POSTS_SHORTTITLE' => htmlspecialchars($rowt['ft_title']),
	'FORUMS_POSTS_CATTITLE' => htmlspecialchars(Cot::$structure['forums'][$s]['title']),
	'FORUMS_POSTS_PATH' => $toppath,
	'FORUMS_POSTS_PAGES' => $pagenav['main'],
	'FORUMS_POSTS_PAGEPREV' => $pagenav['prev'],
	'FORUMS_POSTS_PAGENEXT' => $pagenav['next'],
	'FORUMS_POSTS_CURRENTPAGE' => $pagenav['current'],
	'FORUMS_POSTS_TOTALPAGES' => ceil($totalposts / Cot::$cfg['forums']['maxpostsperpage']),
	'FORUMS_POSTS_JUMPBOX' => cot_selectbox($s, 'jumpbox', array_keys($jumpbox), array_values($jumpbox), false, 'onchange="redirect(this)"')
));

// Topic icon
$rowt['ft_icon'] = 'posts';
$rowt['ft_postisnew'] = FALSE;
if ($rowt['ft_updated'] > Cot::$usr['lastvisit'] && Cot::$usr['id']>0)
{
	$rowt['ft_icon'] .= '_new';
	$rowt['ft_postisnew'] = TRUE;
}

if ($rowt['ft_postcount'] >= Cot::$cfg['forums']['hottopictrigger'] && !$rowt['ft_state'] && !$rowt['ft_sticky'])
{
	$rowt['ft_icon'] = ($rowt['ft_postisnew']) ? 'posts_new_hot' : 'posts_hot';
}
else
{
	$rowt['ft_icon'] .= ($rowt['ft_sticky']) ? '_sticky' : '';
	$rowt['ft_icon'] .=  ($rowt['ft_state']) ? '_locked' : '';
}

$rowt['ft_icon_type'] = $rowt['ft_icon'];
$rowt['ft_icon'] = cot_rc('forums_icon_topic', array('icon' => $rowt['ft_icon']));

$rowt['ft_icon_type_ex'] = $rowt['ft_icon_type'];
if (Cot::$usr['id'] > 0 && $rowt['ft_firstposterid'] == Cot::$usr['id']) {
	$rowt['ft_icon_type_ex'] .= '_posted';
}

$t->assign(array(
	'FORUMS_POSTS_ICON' => $rowt['ft_icon'],
	'FORUMS_POSTS_ICON_TYPE' => $rowt['ft_icon_type'],
	'FORUMS_POSTS_ICON_TYPE_EX' => $rowt['ft_icon_type_ex']
));


if(!empty(Cot::$extrafields[Cot::$db->forum_topics])) {
    foreach (Cot::$extrafields[Cot::$db->forum_topics] as $exfld) {
        $tag = mb_strtoupper($exfld['field_name']);
        $exfld_title = cot_extrafield_title($exfld, 'forums_topic_');
        $t->assign(array(
            'FORUMS_POSTS_TOPIC_' . $tag . '_TITLE' => $exfld_title,
            'FORUMS_POSTS_TOPIC_' . $tag => cot_build_extrafields_data('forums', $exfld, $rowt['ft_' . $exfld['field_name']],
                (Cot::$cfg['forums']['markup'] && Cot::$cfg['forums']['cat_' . $s]['allowbbcodes'])),
            'FORUMS_POSTS_TOPIC_' . $tag . '_VALUE' => $rowt['ft_' . $exfld['field_name']]
        ));
    }
}

/* === Hook  === */
foreach (cot_getextplugins('forums.posts.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';

if (Cot::$cache && Cot::$usr['id'] === 0 && Cot::$cfg['cache_forums']) {
    Cot::$cache->static->write();
}
