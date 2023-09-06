<?php

/**
 * Forums posts display.
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

$s = cot_import('s', 'G', 'TXT'); // section cat
$q = cot_import('q', 'G', 'INT');  // topic id
$p = cot_import('p', 'G', 'INT'); // post id
list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['forums']['maxpostsperpage']);

/* === Hook === */
foreach (cot_getextplugins('forums.editpost.first') as $pl)
{
	include $pl;
}
/* ===== */

cot_blockguests();
cot_check_xg();

isset(Cot::$structure['forums'][$s]) || cot_die();

$sql_forums = Cot::$db->query("SELECT * FROM $db_forum_posts WHERE fp_id = ? and fp_topicid = ? and fp_cat = ?",
	array($p, $q, $s));
if ($rowpost = $sql_forums->fetch()) {
	list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('forums', $s);

	/* === Hook === */
	foreach (cot_getextplugins('forums.editpost.rights') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!Cot::$usr['isadmin'] && ($rowpost['fp_posterid'] != Cot::$usr['id'] ||
			(Cot::$cfg['forums']['edittimeout'] != '0' && Cot::$sys['now'] - $rowpost['fp_creation'] > Cot::$cfg['forums']['edittimeout'] * 3600)))
	{
		cot_log('Attempt to edit a post without rights', 'sec', 'forums', 'error');
		cot_die();
	}
	cot_block(Cot::$usr['auth_read']);
} else {
	cot_die();
}

$is_first_post = $p == Cot::$db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = ? ORDER BY fp_id ASC LIMIT 1", array($q))->fetchColumn();

$sql_forums = Cot::$db->query("SELECT ft_state, ft_mode, ft_title, ft_desc FROM $db_forum_topics WHERE ft_id = $q LIMIT 1");

if ($rowt = $sql_forums->fetch()) {
	if ($rowt['ft_state'] && !Cot::$usr['isadmin']) {
		cot_die_message(603, true);
	}
} else {
	cot_die(true, true);
}

if ($a == 'update') {
	/* === Hook === */
	foreach (cot_getextplugins('forums.editpost.update.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rtopic['ft_title'] = cot_import('rtopictitle', 'P', 'TXT', 255);
	$rtopic['ft_desc'] = cot_import('rtopicdesc', 'P', 'TXT', 255);

	$rmsg = array();
	$rmsg['fp_text'] = cot_import('rmsgtext', 'P', 'HTM');
	$rmsg['fp_updater'] = ($rowpost['fp_posterid'] == Cot::$usr['id'] && (Cot::$sys['now'] < $rowpost['fp_updated'] + 300)
        && empty($rowpost['fp_updater']) ) ? '' : Cot::$usr['name'];
	$rmsg['fp_updated'] = Cot::$sys['now'];

	if (isset($_POST['rtopictitle']) && mb_strlen($rtopic['ft_title']) < Cot::$cfg['forums']['mintitlelength']) {
		cot_error('forums_titletooshort', 'rtopictitle');
	}
	if (mb_strlen($rmsg['fp_text']) < Cot::$cfg['forums']['minpostlength']) {
		cot_error('forums_messagetooshort', 'rmsgtext');
	}

    if (!empty(Cot::$extrafields[Cot::$db->forum_topics])) {
        foreach (Cot::$extrafields[Cot::$db->forum_topics] as $exfld) {
            $rtopic['ft_' . $exfld['field_name']] = cot_import_extrafields('rtopic' . $exfld['field_name'], $exfld, 'P', '', 'forums_topic_');
        }
    }

    if (!empty(Cot::$extrafields[Cot::$db->forum_posts])) {
        foreach (Cot::$extrafields[Cot::$db->forum_posts] as $exfld) {
            $rmsg['fp_'.$exfld['field_name']] = cot_import_extrafields('rmsg'.$exfld['field_name'], $exfld, 'P', '', 'forums_post_');
        }
    }
	if (!cot_error_found()) {
        Cot::$db->update(Cot::$db->forum_posts, $rmsg, "fp_id=$p");

		if (
            !empty($rtopic['ft_title']) &&
            Cot::$db->query("SELECT fp_id FROM ".Cot::$db->forum_posts." WHERE fp_topicid = $q ORDER BY fp_id ASC LIMIT 1")->fetchColumn() == $p
        ) {
			if (mb_substr($rtopic['ft_title'], 0, 1) == "#") {
				$rtopic['ft_title'] = str_replace('#', '', $rtopic['ft_title']);
			}
            $rtopic['ft_preview'] = !empty($rmsg['fp_text']) ? cot_string_truncate($rmsg['fp_text'], 120) : '';
            // If preview string is still too long, let's strip tags and try again
            if (mb_strlen($rtopic['ft_preview']) > 128) {
                $rtopic['ft_preview'] = cot_string_truncate(strip_tags($rmsg['fp_text']), 120, false);
            }
            Cot::$db->update(Cot::$db->forum_topics, $rtopic, "ft_id = $q");
		}

		cot_extrafield_movefiles();
	}

	/* === Hook === */
	foreach (cot_getextplugins('forums.editpost.update.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if (\Cot::$cache) {
        if (\Cot::$cfg['cache_forums']) {
            \Cot::$cache->static->clearByUri(cot_url('forums'));
        }
        if (\Cot::$cfg['cache_index']) {
            \Cot::$cache->static->clear('index');
        }
	}

    cot_redirect(
        cot_url('forums', ['m' => 'posts', 'q' => $q , 'd' => $durl], '#' . $p, true)
    );
}
require_once cot_incfile('forms');

$crumbs = cot_forums_buildpath($s);
$crumbs[] = array(cot_url('forums', "m=posts&p=" . $p, "#" . $p), (($rowt['ft_mode'] == 1) ? '# ' : '') . htmlspecialchars($rowt['ft_title']));
$crumbs[] = array(cot_url('forums', "m=editpost&s=$s&q=" . $q . "&p=" . $p . "&" . cot_xg()), Cot::$L['Edit']);
$toptitle = cot_breadcrumbs($crumbs, Cot::$cfg['homebreadcrumb']);
$toptitle .= Cot::$usr['isadmin'] ? Cot::$R['forums_code_admin_mark'] : '';

$sys['sublocation'] = Cot::$structure['forums'][$s]['title'];
$title_params = array(
	'FORUM' => Cot::$L['Forums'],
	'SECTION' => Cot::$structure['forums'][$s]['title'],
	'TOPIC' => $rowt['ft_title'],
	'EDIT' => Cot::$L['Edit']
);
Cot::$out['subtitle'] = cot_title('{EDIT} - {TOPIC}', $title_params);
if(!isset(Cot::$out['head'])) Cot::$out['head'] = '';
Cot::$out['head'] .= Cot::$R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('forums.editpost.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once Cot::$cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('forums', 'editpost', Cot::$structure['forums'][$s]['tpl']));
$t = new XTemplate($mskin);

cot_display_messages($t);

if (Cot::$db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = $q ORDER BY fp_id ASC LIMIT 1")->fetchColumn() == $p) {
	$t->assign(array(
		'FORUMS_EDITPOST_TOPICTITTLE' => cot_inputbox('text', 'rtopictitle', $rowt['ft_title'], array('size' => 56, 'maxlength' => 255)),
		'FORUMS_EDITPOST_TOPICDESCRIPTION' => cot_inputbox('text', 'rtopicdesc', $rowt['ft_desc'], array('size' => 56, 'maxlength' => 255)),
	));

	// Extra fields
    if(!empty(Cot::$extrafields[Cot::$db->forum_topics])) {
        foreach (Cot::$extrafields[Cot::$db->forum_topics] as $exfld) {
            $uname = strtoupper($exfld['field_name']);
            $exfld_val = cot_build_extrafields('rtopic' . $exfld['field_name'], $exfld, $rowt['ft_' . $exfld['field_name']]);
            $exfld_title = cot_extrafield_title($exfld, 'forums_topic_');

            $t->assign(array(
                'FORUMS_EDITPOST_TOPIC_' . $uname => $exfld_val,
                'FORUMS_EDITPOST_TOPIC_' . $uname . '_TITLE' => $exfld_title,
                'FORUMS_EDITPOST_TOPIC_EXTRAFLD' => $exfld_val,
                'FORUMS_EDITPOST_TOPIC_EXTRAFLD_TITLE' => $exfld_title
            ));
            $t->parse('MAIN.FORUMS_EDITPOST_FIRSTPOST.TOPIC_EXTRAFLD');
        }
    }

	$t->parse('MAIN.FORUMS_EDITPOST_FIRSTPOST');
}


$t->assign(array(
	'FORUMS_EDITPOST_PAGETITLE' => $toptitle,
	'FORUMS_EDITPOST_SUBTITLE' => Cot::$L['forums_postedby'] . ": <a href=\"users.php?m=details&id=" . $rowpost['fp_posterid'] . "\">" . $rowpost['fp_postername'] . "</a> @ " . cot_date('datetime_medium', $rowpost['fp_updated']),
	'FORUMS_EDITPOST_UPDATED' => cot_date('datetime_medium', $rowpost['fp_updated']),
	'FORUMS_EDITPOST_UPDATED_STAMP' => $rowpost['fp_updated'],
	'FORUMS_EDITPOST_SEND' => cot_url('forums', "m=editpost&a=update&s=" . $s . "&q=" . $q . "&p=" . $p . '&d=' . $durl . "&" . cot_xg()),
	'FORUMS_EDITPOST_TEXT' => cot_textarea('rmsgtext', $rowpost['fp_text'], 20, 56, '', 'input_textarea_'.$minimaxieditor),
	'FORUMS_EDITPOST_EDITTIMEOUT' => cot_build_timegap(0, Cot::$cfg['forums']['edittimeout'] * 3600)
));

// Extra fields
if(!empty(Cot::$extrafields[Cot::$db->forum_posts])) {
    foreach (Cot::$extrafields[Cot::$db->forum_posts] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $exfld_val = cot_build_extrafields('rmsg' . $exfld['field_name'], $exfld,
            $rowpost['fp_' . $exfld['field_name']]);
        $exfld_title = cot_extrafield_title($exfld, 'forums_post_');

        $t->assign(array(
            'FORUMS_EDITPOST_' . $uname => $exfld_val,
            'FORUMS_EDITPOST_' . $uname . '_TITLE' => $exfld_title,
            'FORUMS_EDITPOST_EXTRAFLD' => $exfld_val,
            'FORUMS_EDITPOST_EXTRAFLD_TITLE' => $exfld_title
        ));
        $t->parse('MAIN.EXTRAFLD');
    }
}

/* === Hook === */
foreach (cot_getextplugins('forums.editpost.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
