<?php

/* ====================
 * [BEGIN_COT_EXT]
 * Hooks=standalone
 * [END_COT_EXT]
 */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');
require_once cot_incfile('forms');

$m = cot_import('m', 'G', 'ALP');
$a = cot_import('a', 'G', 'ALP');
$id = (int) cot_import('id', 'G', 'INT');
$item = cot_import('item', 'G', 'TXT');
$cat = cot_import('cat', 'G', 'TXT');
$area = cot_import('area', 'G', 'ALP');

Cot::$out['subtitle'] = Cot::$L['comments_comments'];

// Get area/item/cat by id
if ($id > 0)
{
	$res = Cot::$db->query("SELECT com_code, com_area FROM $db_com WHERE com_id = $id");
	if ($res->rowCount() == 1)
	{
		$row = $res->fetch();
		$area = $row['com_area'];
		$item = $row['com_code'];
	}
}

// Check if comments are enabled for specific category/item
cot_block(!empty($area) && !empty($item) && cot_comments_enabled($area, $cat, $item));

$cot_com_back = cot_import('cb', 'P', 'TXT');
if (!empty($cot_com_back)) {
    $cot_com_back = unserialize(base64_decode($cot_com_back));

} else {
    $cot_com_back = [];
    $tmpCat = !empty($cat) ? $cat : '--';
    if (isset($_SESSION['cot_com_back'][$area][$tmpCat][$item])) {
        $cot_com_back = $_SESSION['cot_com_back'][$area][$tmpCat][$item];
    }
}
$url_area = isset($cot_com_back[0]) ? $cot_com_back[0] : '';
$url_params = isset($cot_com_back[1]) ? $cot_com_back[1] : [];
cot_block(!empty($url_area));

// Try to fetch $force_admin from session
if (
    isset($_SESSION['cot_comments_force_admin'][$area][$item])
    && $_SESSION['cot_comments_force_admin'][$area][$item]
    && \Cot::$usr['auth_read'] && \Cot::$usr['auth_write']
) {
    \Cot::$usr['isadmin'] = true;
}

$staticCacheIsEnabled = '';
if (!in_array($url_area, ['admin', 'index', 'login', 'message', 'plug'])) {
    $staticCacheIsEnabled = 'cache_' . $url_area;
}

if ($m == 'edit' && $id > 0) {
	if ($a == 'update' && $id > 0) {
		/* == Hook == */
		foreach (cot_getextplugins('comments.edit.update.first') as $pl) {
			include $pl;
		}
		/* ===== */

		$sql1 = Cot::$db->query("SELECT * FROM $db_com WHERE com_id=? AND com_code=? LIMIT 1", array($id, $item));
		cot_die($sql1->rowCount() == 0);
		$row = $sql1->fetch();

		$time_limit = ($sys['now'] < ($row['com_date'] + $cfg['plugin']['comments']['time'] * 60)) ? TRUE : FALSE;
		$usr['isowner'] = $time_limit
			&& ($usr['id'] > 0 && $row['com_authorid'] == $usr['id']
			|| $usr['id'] == 0 && $usr['ip'] == $row['com_authorip']);
		$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
		cot_block($usr['allow_write']);

		$comarray['com_text'] = cot_import('comtext', 'P', 'HTM');

		if (mb_strlen($comarray['com_text']) < $cfg['plugin']['comments']['minsize']) {
			cot_error($L['com_commenttooshort'], 'comtext');
		}

        if (!empty(Cot::$extrafields[Cot::$db->com])) {
            foreach (Cot::$extrafields[Cot::$db->com] as $exfld) {
                $comarray['com_' . $exfld['field_name']] = cot_import_extrafields('rcomments' . $exfld['field_name'],
                    $exfld, 'P', '', 'comments_');
            }
        }

		if (!cot_error_found()) {
			$sql = \Cot::$db->update(\Cot::$db->com, $comarray, 'com_id = ? AND com_code = ?', [$id, $item]);

			cot_extrafield_movefiles();

            if (\Cot::$cache) {
                if ($staticCacheIsEnabled === '' || !empty(\Cot::$cfg[$staticCacheIsEnabled])) {
                    \Cot::$cache->static->clearByUri(cot_url($url_area, $url_params));

                }
                if (\Cot::$cfg['cache_index']) {
                    \Cot::$cache->static->clear('index');
                }
            }

			if (\Cot::$cfg['plugin']['comments']['mail']) {
				$sql2 = \Cot::$db->query(
                    'SELECT * FROM ' . \Cot::$db->users . ' WHERE user_maingrp = ' . COT_GROUP_SUPERADMINS
                );

				$email_title = $L['plu_comlive'];
				$email_body = $L['User'] . ' ' . preg_replace('#[^\w\p{L}]#u', '', $usr['name']) . ' ' . $L['plu_comlive3'];
				$email_body .= COT_ABSOLUTE_URL . cot_url($url_area, $url_params, '#com' . $id, true) . "\n\n";

				while ($adm = $sql2->fetch()) {
					cot_mail($adm['user_email'], $email_title, $email_body);
				}
				$sql2->closeCursor();
			}

            /* == Hook == */
			foreach (cot_getextplugins('comments.edit.update.done') as $pl) {
				include $pl;
			}
			/* ===== */

			//$com_grp = ($usr['isadmin']) ? 'adm' : 'users';//TODO backward compatibility need ?!
			cot_log('Edited comment #' . $id, 'comments', 'edit', 'done');
			cot_redirect(cot_url($url_area, $url_params, '#com' . $id, true));
		}
	}
	$t->assign(array(
		'COMMENTS_TITLE' => Cot::$L['plu_title'],
		'COMMENTS_TITLE_URL' => cot_url('plug', 'e=comments')
	));
	$t->parse('MAIN.COMMENTS_TITLE');

	$sql = Cot::$db->query("SELECT * FROM $db_com WHERE com_id=? AND com_code=? AND com_area=?", array($id, $item, $area));
	cot_die($sql->rowCount() != 1);
	$com = $sql->fetch();

	$com_limit = ($sys['now'] < ($com['com_date'] + $cfg['plugin']['comments']['time'] * 60)) ? TRUE : FALSE;
	$usr['isowner'] = $com_limit
		&& ($usr['id'] > 0 && $com['com_authorid'] == $usr['id'] || $usr['id'] == 0
		&& isset($_SESSION['cot_comments_edit'][$id]));

	$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
	cot_block($usr['allow_write']);

    $editor = ($cfg['plugin']['comments']['markup']) ? 'input_textarea_minieditor' : '';
	$t->assign(array(
		'COMMENTS_FORM_POST' => cot_url('plug', 'e=comments&m=edit&a=update&area=' . $area . '&cat=' . $cat . '&item=' . $com['com_code'] . '&id=' . $com['com_id']),
		'COMMENTS_POSTER_TITLE' => $L['Poster'],
		'COMMENTS_POSTER' => $com['com_author'],
		'COMMENTS_IP_TITLE' => $L['Ip'],
		'COMMENTS_IP' => $com['com_authorip'],
		'COMMENTS_DATE_TITLE' => $L['Date'],
		'COMMENTS_DATE' => cot_date('datetime_medium', $com['com_date']),
		'COMMENTS_DATE_STAMP' => $com['com_date'],
		'COMMENTS_FORM_UPDATE_BUTTON' => $L['Update'],
		'COMMENTS_FORM_TEXT' => cot_textarea('comtext', $com['com_text'], 8, 64, '', $editor)
	));

	// Extra fields
    if (!empty(Cot::$extrafields[Cot::$db->com])) {
        foreach (Cot::$extrafields[Cot::$db->com] as $exfld) {
            $uname = strtoupper($exfld['field_name']);
            $exfld_val = cot_build_extrafields('rcomments' . $exfld['field_name'], $exfld, $com[$exfld['field_name']]);
            $exfld_title = cot_extrafield_title($exfld, 'comments_');

            $t->assign(array(
                'COMMENTS_FORM_' . $uname => $exfld_val,
                'COMMENTS_FORM_' . $uname . '_TITLE' => $exfld_title,
                'COMMENTS_FORM_EXTRAFLD' => $exfld_val,
                'COMMENTS_FORM_EXTRAFLD_TITLE' => $exfld_title
            ));
            $t->parse('COMMENTS.COMMENTS_FORM_EDIT.EXTRAFLD');
        }
    }

	/* == Hook == */
	foreach (cot_getextplugins('comments.edit.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.COMMENTS_FORM_EDIT');
}

if ($a == 'send' && \Cot::$usr['auth_write']) {
	cot_shield_protect();
	$rtext = cot_import('rtext', 'P', 'HTM');
	$rname = cot_import('rname', 'P', 'TXT');
	$comarray = [];

	// Extra fields
    if (!empty(Cot::$extrafields[Cot::$db->com])) {
        foreach (Cot::$extrafields[Cot::$db->com] as $exfld) {
            $comarray['com_' . $exfld['field_name']] = cot_import_extrafields('rcomments' . $exfld['field_name'], $exfld,
                'P', '', 'comments_');
        }
    }

	/* == Hook == */
	foreach (cot_getextplugins('comments.send.first') as $pl) {
		include $pl;
	}
	/* ===== */

	if (empty($rname) && \Cot::$usr['id'] == 0) {
		cot_error(\Cot::$L['com_authortooshort'], 'rname');
	}
	if (mb_strlen($rtext) < \Cot::$cfg['plugin']['comments']['minsize']) {
		cot_error(\Cot::$L['com_commenttooshort'], 'rtext');
	}
	if (
        \Cot::$cfg['plugin']['comments']['commentsize']
        && mb_strlen($rtext) > \Cot::$cfg['plugin']['comments']['commentsize']
    ) {
		cot_error(\Cot::$L['com_commenttoolong'], 'rtext');
	}

	if (!cot_error_found()) {
		$comarray['com_area'] = $area;
		$comarray['com_code'] = $item;
		$comarray['com_author'] = (\Cot::$usr['id'] == 0) ? $rname : \Cot::$usr['name'];
		$comarray['com_authorid'] = (int) \Cot::$usr['id'];
		$comarray['com_authorip'] = \Cot::$usr['ip'];
		$comarray['com_text'] = $rtext;
		$comarray['com_date'] = (int) \Cot::$sys['now'];

		$sql = \Cot::$db->insert($db_com, $comarray);
		$id = \Cot::$db->lastInsertId();


        if (\Cot::$cache) {
            if ($staticCacheIsEnabled === '' || !empty(\Cot::$cfg[$staticCacheIsEnabled])) {
                \Cot::$cache->static->clearByUri(cot_url($url_area, $url_params));

            }
            if (\Cot::$cfg['cache_index']) {
                \Cot::$cache->static->clear('index');
            }
        }

		cot_extrafield_movefiles();

		$_SESSION['cot_comments_edit'][$id] = \Cot::$sys['now'];

		if (\Cot::$cfg['plugin']['comments']['mail']) {
            $sql = \Cot::$db->query(
                'SELECT * FROM ' . \Cot::$db->users . ' WHERE user_maingrp = ' . COT_GROUP_SUPERADMINS
            );

			$email_title = $L['plu_comlive'];
			$email_body = $L['User'] . ' ' . preg_replace('#[^\w\p{L}]#u', '', ($usr['id'] == 0 ? $rname : $usr['name'])) . ' ' . $L['plu_comlive2'];
			$email_body .= COT_ABSOLUTE_URL . cot_url($url_area, $url_params, '#com' . $id, true) . "\n\n";
			while ($adm = $sql->fetch()) {
				cot_mail($adm['user_email'], $email_title, $email_body);
			}
			$sql->closeCursor();
		}

		/* == Hook == */
		foreach (cot_getextplugins('comments.send.new') as $pl) {
			include $pl;
		}
		/* ===== */

		cot_message($L['com_commentadded']);

		cot_shield_update(20, 'New comment');

		cot_redirect(cot_url($url_area, $url_params, '#com' . $id, true));
	}

    // Clear static page cache to show alerts
	if (\Cot::$usr['id'] === 0 && \Cot::$cache) {
        if ($staticCacheIsEnabled === '' || !empty(\Cot::$cfg[$staticCacheIsEnabled])) {
            \Cot::$cache->static->clearByUri(cot_url($url_area, $url_params));

        }
	}
	cot_redirect(cot_url($url_area, $url_params, '#comments', true));

} elseif ($a == 'delete' && \Cot::$usr['isadmin']) {
	cot_check_xg();
	$sql = \Cot::$db->query(
        'SELECT * FROM ' . \Cot::$db->com . ' WHERE com_id = :id AND com_area = :area LIMIT 1',
        ['id' => $id, 'area' => $area]
    );

	if ($row = $sql->fetch()) {
		$sql->closeCursor();
		$sql = \Cot::$db->delete(\Cot::$db->com, 'com_id = ?', $id);

		foreach (Cot::$extrafields[$db_com] as $exfld) {
			cot_extrafield_unlinkfiles($row['com_' . $exfld['field_name']], $exfld);
		}

        if (\Cot::$cache) {
            if ($staticCacheIsEnabled === '' || !empty(\Cot::$cfg[$staticCacheIsEnabled])) {
                \Cot::$cache->static->clearByUri(cot_url($url_area, $url_params));

            }
            if (\Cot::$cfg['cache_index']) {
                \Cot::$cache->static->clear('index');
            }
        }

		/* == Hook == */
		foreach (cot_getextplugins('comments.delete') as $pl) {
			include $pl;
		}
		/* ===== */

		cot_log(
            'Deleted comment #' . $id . ' in &quot;' . $item . '&quot;', 'comments', 'delete',
            'done'
        );
	}
	cot_redirect(cot_url($url_area, $url_params, '#comments', true));

} elseif ($a == 'enable' && \Cot::$usr['isadmin']) {
	$area = cot_import('area', 'P', 'ALP');
	$state = cot_import('state', 'P', 'INT');
}

cot_display_messages($t);
