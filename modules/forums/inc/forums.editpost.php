<?php
/**
 * Forums edit post.
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\exceptions\NotFoundHttpException;
use cot\modules\forums\inc\ForumsTopicsRepository;

defined('COT_CODE') or die('Wrong URL');

$s = cot_import('s', 'G', 'TXT'); // section cat
$q = cot_import('q', 'G', 'INT');  // Topic id
$p = cot_import('p', 'G', 'INT'); // Post id
[$pg, $d, $durl] = cot_import_pagenav('d', Cot::$cfg['forums']['maxpostsperpage']);

/* === Hook === */
foreach (cot_getextplugins('forums.editpost.first') as $pl) {
	include $pl;
}
/* ===== */

cot_blockguests();
cot_check_xg();

isset(Cot::$structure['forums'][$s]) || cot_die();

$sql_forums = Cot::$db->query(
    'SELECT * FROM ' . Cot::$db->forum_posts . ' WHERE fp_id = ? and fp_topicid = ? and fp_cat = ?',
	[$p, $q, $s]
);
if ($rowpost = $sql_forums->fetch()) {
	[Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']] = cot_auth('forums', $s);

	/* === Hook === */
	foreach (cot_getextplugins('forums.editpost.rights') as $pl) {
		include $pl;
	}
	/* ===== */

	if (
        !Cot::$usr['isadmin']
        && (
            $rowpost['fp_posterid'] != Cot::$usr['id']
            || (Cot::$cfg['forums']['edittimeout'] != '0' && Cot::$sys['now'] - $rowpost['fp_creation'] > Cot::$cfg['forums']['edittimeout'] * 3600)
        )
    ) {
		cot_log('Attempt to edit a post without rights', 'sec', 'forums', 'error');
		cot_die();
	}
	cot_block(Cot::$usr['auth_read']);
} else {
	cot_die();
}

$isFirstPost = $p == Cot::$db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = ? ORDER BY fp_id ASC LIMIT 1", [$q])->fetchColumn();

$topic = ForumsTopicsRepository::getInstance()->getById($q);
if ($topic === null) {
    throw new NotFoundHttpException();
}

if ($topic['ft_state'] && !Cot::$usr['isadmin']) {
    cot_die_message(603, true);
}

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.26
    $rowt = $topic;
}

if ($a == 'update') {
	/* === Hook === */
	foreach (cot_getextplugins('forums.editpost.update.first') as $pl) {
		include $pl;
	}
	/* ===== */

	$rtopic['ft_title'] = cot_import('rtopictitle', 'P', 'TXT', 255);
	$rtopic['ft_desc'] = cot_import('rtopicdesc', 'P', 'TXT', 255);

	$rmsg = [];
	$rmsg['fp_text'] = cot_import('rmsgtext', 'P', 'HTM');
    if (empty($rmsg['fp_text'])) {
        $rmsg['fp_text'] = '';
    }
	$rmsg['fp_updater'] = (
        $rowpost['fp_posterid'] == Cot::$usr['id']
        && (Cot::$sys['now'] < $rowpost['fp_updated'] + 300)
        && empty($rowpost['fp_updater'])
    ) ? '' : Cot::$usr['name'];
	$rmsg['fp_updated'] = Cot::$sys['now'];

	if (isset($_POST['rtopictitle']) && mb_strlen($rtopic['ft_title']) < Cot::$cfg['forums']['mintitlelength']) {
		cot_error('forums_titletooshort', 'rtopictitle');
	}
	if (mb_strlen($rmsg['fp_text']) < Cot::$cfg['forums']['minpostlength']) {
		cot_error('forums_messagetooshort', 'rmsgtext');
	}

    if (!empty(Cot::$extrafields[Cot::$db->forum_topics])) {
        foreach (Cot::$extrafields[Cot::$db->forum_topics] as $extraField) {
            $rtopic['ft_' . $extraField['field_name']] = cot_import_extrafields('rtopic' . $extraField['field_name'], $extraField, 'P', '', 'forums_topic_');
        }
    }

    if (!empty(Cot::$extrafields[Cot::$db->forum_posts])) {
        foreach (Cot::$extrafields[Cot::$db->forum_posts] as $extraField) {
            $rmsg['fp_'.$extraField['field_name']] = cot_import_extrafields('rmsg'.$extraField['field_name'], $extraField, 'P', '', 'forums_post_');
        }
    }

    if (cot_error_found()) {
        cot_redirect(
            cot_url('forums', ['m' => 'editpost', 's' => 'general', 'q' => $q, 'p' => $p, 'd' => $d, 'x' => Cot::$sys['xk']], '', true)
        );
    }


    Cot::$db->update(Cot::$db->forum_posts, $rmsg, "fp_id=$p");

    if (
        !empty($rtopic['ft_title'])
        && Cot::$db->query("SELECT fp_id FROM " . Cot::$db->forum_posts . " WHERE fp_topicid = $q ORDER BY fp_id ASC LIMIT 1")
            ->fetchColumn() == $p
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

	/* === Hook === */
	foreach (cot_getextplugins('forums.editpost.update.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if (Cot::$cache) {
        if (Cot::$cfg['cache_forums']) {
            Cot::$cache->static->clearByUri(cot_url('forums'));
        }
        if (Cot::$cfg['cache_index']) {
            Cot::$cache->static->clear('index');
        }
	}

    cot_redirect(
        cot_url('forums', ['m' => 'posts', 'q' => $q , 'd' => $durl], '#' . $p, true)
    );
}
require_once cot_incfile('forms');

$crumbs = cot_forums_buildpath($s);
$crumbs[] = [
    cot_url('forums', "m=posts&p=" . $p, "#" . $p),
    (($topic['ft_mode'] == 1) ? '# ' : '') . $topic['ft_title']
];
$crumbs[] = [
    cot_url('forums', ['m' => 'editpost', 's' => $s, 'q' => $q, 'p' => $p, 'x' => Cot::$sys['xk']]),
    Cot::$L['Edit']
];
$toptitle = cot_breadcrumbs($crumbs, Cot::$cfg['homebreadcrumb']);
$toptitle .= Cot::$usr['isadmin'] ? Cot::$R['forums_code_admin_mark'] : '';

$sys['sublocation'] = Cot::$structure['forums'][$s]['title'];
$title_params = array(
	'FORUM' => Cot::$L['Forums'],
	'SECTION' => Cot::$structure['forums'][$s]['title'],
	'TOPIC' => $topic['ft_title'],
	'EDIT' => Cot::$L['Edit']
);
Cot::$out['subtitle'] = cot_title('{EDIT} - {TOPIC}', $title_params);
if (!isset(Cot::$out['head'])) {
    Cot::$out['head'] = '';
}
Cot::$out['head'] .= Cot::$R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('forums.editpost.main') as $pl) {
	include $pl;
}
/* ===== */

require_once Cot::$cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('forums', 'editpost', Cot::$structure['forums'][$s]['tpl']));
$t = new XTemplate($mskin);

cot_display_messages($t);

if (Cot::$db->query("SELECT fp_id FROM $db_forum_posts WHERE fp_topicid = $q ORDER BY fp_id ASC LIMIT 1")->fetchColumn() == $p) {
	$t->assign([
		'FORUMS_EDITPOST_FORM_TOPIC_TITTLE' => cot_inputbox(
            'text',
            'rtopictitle',
            $topic['ft_title'],
            ['maxlength' => 255]
        ),
		'FORUMS_EDITPOST_FORM_TOPIC_DESCRIPTION' => cot_inputbox(
            'text',
            'rtopicdesc',
            $topic['ft_desc'],
            ['maxlength' => 255]
        ),
	]);

    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        // @deprecated in 0.9.26
        $t->assign([
            'FORUMS_EDITPOST_TOPICTITTLE' => cot_inputbox('text', 'rtopictitle', $topic['ft_title'], array('maxlength' => 255)),
            'FORUMS_EDITPOST_TOPICDESCRIPTION' => cot_inputbox('text', 'rtopicdesc', $topic['ft_desc'], array('maxlength' => 255)),
        ]);
    }

    // Extra fields
    if (!empty(Cot::$extrafields[Cot::$db->forum_topics])) {
        foreach (Cot::$extrafields[Cot::$db->forum_topics] as $extraField) {
            $uname = strtoupper($extraField['field_name']);
            $fieldFormElement = cot_build_extrafields(
                'rtopic' . $extraField['field_name'],
                $extraField,
                $topic['ft_' . $extraField['field_name']] ?? null
            );
            $fieldTitle = cot_extrafield_title($extraField, 'forums_topic_');

            $t->assign([
                'FORUMS_EDITPOST_FORM_TOPIC_' . $uname => $fieldFormElement,
                'FORUMS_EDITPOST_FORM_TOPIC_' . $uname . '_TITLE' => $fieldTitle,
                'FORUMS_EDITPOST_FORM_TOPIC_EXTRA_FILED' => $fieldFormElement,
                'FORUMS_EDITPOST_FORM_TOPIC_EXTRA_FILED_TITLE' => $fieldTitle,
            ]);

            if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                // @deprecated in 0.9.26
                $t->assign([
                    'FORUMS_EDITPOST_TOPIC_' . $uname => $fieldFormElement,
                    'FORUMS_EDITPOST_TOPIC_' . $uname . '_TITLE' => $fieldTitle,
                    'FORUMS_EDITPOST_TOPIC_EXTRAFLD' => $fieldFormElement,
                    'FORUMS_EDITPOST_TOPIC_EXTRAFLD_TITLE' => $fieldTitle,
                ]);
            }

            $t->parse('MAIN.FORUMS_EDITPOST_FIRSTPOST.TOPIC_EXTRA_FILED');
        }
    }

	$t->parse('MAIN.FORUMS_EDITPOST_FIRSTPOST');
}

$t->assign([
    'FORUMS_EDITPOST_TITLE' => Cot::$L['forums_editPost'],
    'FORUMS_EDITPOST_BREADCRUMBS' => $toptitle,
	'FORUMS_EDITPOST_SUBTITLE' => Cot::$L['forums_postedby'] . ": <a href=\"users.php?m=details&id=" . $rowpost['fp_posterid'] . "\">" . $rowpost['fp_postername'] . "</a> @ " . cot_date('datetime_medium', $rowpost['fp_updated']),
	'FORUMS_EDITPOST_UPDATED' => cot_date('datetime_medium', $rowpost['fp_updated']),
	'FORUMS_EDITPOST_UPDATED_STAMP' => $rowpost['fp_updated'],
	'FORUMS_EDITPOST_FORM_ACTION' => cot_url('forums', "m=editpost&a=update&s=" . $s . "&q=" . $q . "&p=" . $p . '&d=' . $durl . "&" . cot_xg()),
	'FORUMS_EDITPOST_FORM_TEXT' => cot_textarea('rmsgtext', $rowpost['fp_text'], 20, 56, '', 'input_textarea_'.$minimaxieditor),
	'FORUMS_EDITPOST_EDIT_TIMEOUT' => Cot::$cfg['forums']['edittimeout'] > 0
        ? cot_build_timegap(0, Cot::$cfg['forums']['edittimeout'] * 3600)
        : '',
]);

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.26
    $t->assign([
        'FORUMS_EDITPOST_PAGETITLE' => $toptitle,
        'FORUMS_EDITPOST_SEND' => cot_url('forums', "m=editpost&a=update&s=" . $s . "&q=" . $q . "&p=" . $p . '&d=' . $durl . "&" . cot_xg()),
        'FORUMS_EDITPOST_TEXT' => cot_textarea('rmsgtext', $rowpost['fp_text'], 20, 56, '', 'input_textarea_'.$minimaxieditor),
        'FORUMS_EDITPOST_EDITTIMEOUT' => Cot::$cfg['forums']['edittimeout'] > 0
            ? cot_build_timegap(0, Cot::$cfg['forums']['edittimeout'] * 3600)
            : '',
    ]);
}

// Extra fields
if (!empty(Cot::$extrafields[Cot::$db->forum_posts])) {
    foreach (Cot::$extrafields[Cot::$db->forum_posts] as $extraField) {
        $uname = strtoupper($extraField['field_name']);
        $fieldFormElement = cot_build_extrafields('rmsg' . $extraField['field_name'], $extraField,
            $rowpost['fp_' . $extraField['field_name']]);
        $fieldTitle = cot_extrafield_title($extraField, 'forums_post_');

        $t->assign([
            'FORUMS_EDITPOST_FORM_' . $uname => $fieldFormElement,
            'FORUMS_EDITPOST_FORM_' . $uname . '_TITLE' => $fieldTitle,
            'FORUMS_EDITPOST_FORM_EXTRA_FILED' => $fieldFormElement,
            'FORUMS_EDITPOST_FORM_EXTRA_FILED_TITLE' => $fieldTitle,
        ]);

        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            // @deprecated in 0.9.26
            $t->assign([
                'FORUMS_EDITPOST_' . $uname => $fieldFormElement,
                'FORUMS_EDITPOST_' . $uname . '_TITLE' => $fieldTitle,
                'FORUMS_EDITPOST_EXTRAFLD' => $fieldFormElement,
                'FORUMS_EDITPOST_EXTRAFLD_TITLE' => $fieldTitle,
            ]);
        }
        $t->parse('MAIN.EXTRA_FILED');
    }
}

/* === Hook === */
foreach (cot_getextplugins('forums.editpost.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
