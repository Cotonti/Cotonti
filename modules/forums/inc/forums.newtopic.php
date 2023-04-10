<?php

/**
 * Forums posts display.
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$s = cot_import('s','G','TXT'); // section cat

cot_blockguests();
cot_die(empty($s));

/* === Hook === */
foreach (cot_getextplugins('forums.newtopic.first') as $pl) {
	include $pl;
}
/* ===== */

isset(Cot::$structure['forums'][$s]) || cot_die();

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('forums', $s);
/* === Hook === */
foreach (cot_getextplugins('forums.newtopic.rights') as $pl) {
	include $pl;
}
/* ===== */
cot_block(Cot::$usr['auth_write']);

if (Cot::$structure['forums'][$s]['locked']) {
	cot_die_message(602, true);
}

$rtopic = [
    'ft_title' => '',
    'ft_desc' => '',
    'ft_mode' => '',
];
$rmsg = [
    'fp_text' => '',
];

if ($a == 'newtopic') {
	cot_shield_protect();

	/* === Hook === */
	foreach (cot_getextplugins('forums.newtopic.newtopic.first') as $pl) {
		include $pl;
	}
	/* ===== */

	$rmsg['fp_text'] = cot_import('rmsgtext','P','HTM');

	$rtopic['ft_title'] = cot_import('rtopictitle','P','TXT', 255);
	$rtopic['ft_desc'] = cot_import('rtopicdesc','P','TXT', 255);
	$rtopic['ft_mode'] = (int) (cot_import('rtopicmode','P','BOL') && Cot::$cfg['forums']['cat_' . $s]['allowprvtopics']) ? 1 : 0;
    $rtopic['ft_preview'] = !empty($rmsg['fp_text']) ? cot_string_truncate($rmsg['fp_text'], 120) : '';
    // If preview string is still too long, let's strip tags and try again
    if (mb_strlen($rtopic['ft_preview']) > 128) {
        $rtopic['ft_preview'] = cot_string_truncate(strip_tags($rmsg['fp_text']), 120, false);
    }

	if (mb_strlen($rtopic['ft_title']) < Cot::$cfg['forums']['mintitlelength']) {
		cot_error('forums_titletooshort', 'rtopictitle');
	}
	if (mb_strlen($rmsg['fp_text']) < Cot::$cfg['forums']['minpostlength']) {
		cot_error('forums_messagetooshort', 'rmsgtext');
	}
	if (!strpos(Cot::$structure['forums'][$s]['path'], '.')) {
		// Attempting to create a topic in a root category
		include cot_langfile('message', 'core');
		cot_error(Cot::$L['msg602_body']);
	}

	if (!empty(Cot::$extrafields[Cot::$db->forum_topics])) {
		foreach (Cot::$extrafields[Cot::$db->forum_topics] as $exfld) {
			$rtopic['ft_' . $exfld['field_name']] = cot_import_extrafields('rtopic' . $exfld['field_name'], $exfld, 'P', '', 'forums_topic_');
		}
	}

	if (!empty(Cot::$extrafields[Cot::$db->forum_posts])) {
		foreach (Cot::$extrafields[Cot::$db->forum_posts] as $exfld) {
			$rmsg['fp_' . $exfld['field_name']] = cot_import_extrafields('rmsg' . $exfld['field_name'], $exfld, 'P', '', 'forums_post_');
		}
	}

	if (!cot_error_found()) {
		if (mb_substr($rtopic['ft_title'], 0 ,1) == "#") {
			$rtopic['ft_title'] = str_replace('#', '', $rtopic['ft_title']);
		}

		$rtopic['ft_state'] = 0;
		$rtopic['ft_sticky'] = 0;
		$rtopic['ft_cat'] = $s;
		$rtopic['ft_creationdate'] = (int) Cot::$sys['now'];
		$rtopic['ft_updated'] = (int) Cot::$sys['now'];
		$rtopic['ft_postcount'] = 1;
		$rtopic['ft_viewcount'] = 0;
		$rtopic['ft_firstposterid'] = (int) Cot::$usr['id'];
		$rtopic['ft_firstpostername'] = Cot::$usr['name'];
		$rtopic['ft_lastposterid'] = (int) Cot::$usr['id'];
		$rtopic['ft_lastpostername'] = Cot::$usr['name'];

		Cot::$db->insert(Cot::$db->forum_topics, $rtopic);

		$q = Cot::$db->lastInsertId();

		$rmsg['fp_cat'] = $s;
		$rmsg['fp_topicid'] = (int) $q;
		$rmsg['fp_posterid'] = (int) Cot::$usr['id'];
		$rmsg['fp_postername'] = Cot::$usr['name'];
		$rmsg['fp_creation'] = (int) Cot::$sys['now'];
		$rmsg['fp_updated'] = (int) Cot::$sys['now'];
		$rmsg['fp_posterip'] = Cot::$usr['ip'];

		Cot::$db->insert(Cot::$db->forum_posts, $rmsg);

		$p = Cot::$db->lastInsertId();

		if (Cot::$cfg['forums']['cat_' . $s]['autoprune'] > 0) {
			cot_forums_prunetopics('updated', $s, Cot::$cfg['forums']['cat_' . $s]['autoprune']);
		}

		cot_extrafield_movefiles();

        cot_forums_updateUserPostCount(Cot::$usr['id']);
        cot_forums_updateStructureCounters($s);

		/* === Hook === */
		foreach (cot_getextplugins('forums.newtopic.newtopic.done') as $pl) {
			include $pl;
		}
		/* ===== */

		cot_shield_update(45, "New topic");
		cot_redirect(cot_url('forums', "m=posts&q=$q&n=last", '#bottom', true));
	}
}

$toptitle = cot_breadcrumbs(cot_forums_buildpath($s), Cot::$cfg['homebreadcrumb']);
$toptitle .= (Cot::$usr['isadmin']) ? Cot::$R['forums_code_admin_mark'] : '';

Cot::$sys['sublocation'] = Cot::$structure['forums'][$s]['title'];
Cot::$out['subtitle'] = Cot::$L['forums_newtopic'];
if (!isset(Cot::$out['head'])) {
    Cot::$out['head'] = '';
}
Cot::$out['head'] .= Cot::$R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('forums.newtopic.main') as $pl) {
	include $pl;
}
/* ===== */
require_once cot_incfile('forms');
require_once Cot::$cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('forums', 'newtopic', Cot::$structure['forums'][$s]['tpl']));
$t = new XTemplate($mskin);

$t->assign(array(
	'FORUMS_NEWTOPIC_PAGETITLE' => $toptitle ,
	'FORUMS_NEWTOPIC_SUBTITLE' => htmlspecialchars(cot_parse_autourls(Cot::$structure['forums'][$s]['desc'])),
	'FORUMS_NEWTOPIC_SEND' => cot_url('forums', "m=newtopic&a=newtopic&s=".$s),
	'FORUMS_NEWTOPIC_TITLE' => cot_inputbox('text', 'rtopictitle', $rtopic['ft_title'], array('size' => 56, 'maxlength' => 255)),
	'FORUMS_NEWTOPIC_DESC' => cot_inputbox('text', 'rtopicdesc', $rtopic['ft_desc'], array('size' => 56, 'maxlength' => 255)),
	'FORUMS_NEWTOPIC_TEXT' => cot_textarea('rmsgtext', $rmsg['fp_text'], 20, 56, '', 'input_textarea_'.$minimaxieditor),
	'FORUMS_NEWTOPIC_EDITTIMEOUT' => cot_build_timegap(0, Cot::$cfg['forums']['edittimeout'] * 3600)
));

// Extra fields
if (!empty(Cot::$extrafields[Cot::$db->forum_posts])) {
    foreach (Cot::$extrafields[Cot::$db->forum_posts] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $exfld_val = cot_build_extrafields('rmsg' . $exfld['field_name'], $exfld, $rmsg['fp_' . $exfld['field_name']]);
        $exfld_title = cot_extrafield_title($exfld, 'forums_post_');

        $t->assign(array(
            'FORUMS_NEWTOPIC_' . $uname => $exfld_val,
            'FORUMS_NEWTOPIC_' . $uname . '_TITLE' => $exfld_title,
            'FORUMS_NEWTOPIC_EXTRAFLD' => $exfld_val,
            'FORUMS_NEWTOPIC_EXTRAFLD_TITLE' => $exfld_title
        ));
        $t->parse('MAIN.EXTRAFLD');
    }
}

// Extra fields
if (!empty(Cot::$extrafields[Cot::$db->forum_topics])) {
    foreach (Cot::$extrafields[Cot::$db->forum_topics] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $exfld_val = cot_build_extrafields('rtopic' . $exfld['field_name'], $exfld, $rtopic['ft_' . $exfld['field_name']]);
        $exfld_title = cot_extrafield_title($exfld, 'forums_topic_');
        
        $t->assign(array(
            'FORUMS_NEWTOPIC_TOPIC_' . $uname => $exfld_val,
            'FORUMS_NEWTOPIC_TOPIC_' . $uname . '_TITLE' => $exfld_title,
            'FORUMS_NEWTOPIC_TOPIC_EXTRAFLD' => $exfld_val,
            'FORUMS_NEWTOPIC_TOPIC_EXTRAFLD_TITLE' => $exfld_title
        ));
        $t->parse('MAIN.TOPIC_EXTRAFLD');
    }
}

if (Cot::$cfg['forums']['cat_' . $s]['allowprvtopics']) {
	$t->assign('FORUMS_NEWTOPIC_ISPRIVATE', cot_checkbox($rtopic['ft_mode'], 'rtopicmode'));
	$t->parse('MAIN.PRIVATE');
}

/* === Hook === */
foreach (cot_getextplugins('forums.newtopic.tags') as $pl) {
	include $pl;
}
/* ===== */

cot_display_messages($t);

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
