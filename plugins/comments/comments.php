<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package comments
 * @version 0.7.0
 * @author Neocrome, Asmo, motor2hg, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
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

$plugin_title = $L['plu_title'];

// Get area/item/cat by id
if ($id > 0)
{
	$res = $db->query("SELECT com_code, com_area FROM $db_com WHERE com_id = $id");
	if ($res->rowCount() == 1)
	{
		$row = $res->fetch();
		$area = $row['com_area'];
		$item = $row['com_code'];
	}
}

// Check if comments are enabled for specific category/item
cot_block(!empty($area) && !empty($item) && cot_comments_enabled($area, $cat, $item));

$url_area = $_SESSION['cot_com_back'][$area][$cat][$item][0];
$url_params = $_SESSION['cot_com_back'][$area][$cat][$item][1];
cot_block(!empty($url_area));

if ($m == 'edit' && $id > 0)
{
	if ($a == 'update' && $id > 0)
	{
		/* == Hook == */
		foreach (cot_getextplugins('comments.edit.update.first') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$sql1 = $db->query("SELECT * FROM $db_com WHERE com_id=? AND com_code=? LIMIT 1", array($id, $item));
		cot_die($sql1->rowCount() == 0);
		$row = $sql1->fetch();

		$time_limit = ($sys['now_offset'] < ($row['com_date'] + $cfg['plugin']['comments']['time'] * 60)) ? TRUE : FALSE;
		$usr['isowner'] = $time_limit
			&& ($usr['id'] > 0 && $row['com_authorid'] == $usr['id']
				|| $usr['id'] == 0 && $usr['ip'] == $row['com_authorip']);
		$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
		cot_block($usr['allow_write']);

		$comtext = cot_import('comtext', 'P', 'HTM');

		if (empty($comtext))
		{
			cot_error($L['plu_comtooshort'], 'comtext');
		}

		if (!cot_error_found())
		{
			$sql = $db->update($db_com, array('com_text' => $comtext), 'com_id=? AND com_code=?', array($id, $item));

			if ($cfg['plugin']['comments']['mail'])
			{
				$sql2 = $db->query("SELECT * FROM $db_users WHERE user_maingrp=5");

				$email_title = $L['plu_comlive'].$cfg['main_url'];
				$email_body  = $L['User']." ".$usr['name'].", ".$L['plu_comlive3'];
				$email_body .= $url . $sep . 'comments=1#c' . $id . "\n\n";

				while ($adm = $sql2->fetch())
				{
					cot_mail($adm['user_email'], $email_title, $email_body);
				}
				$sql2->closeCursor();
			}
			/* == Hook == */
			foreach (cot_getextplugins('comments.edit.update.done') as $pl)
			{
				include $pl;
			}
			/* ===== */

			$com_grp = ($usr['isadmin']) ? 'adm' : 'usr';
			cot_log('Edited comment #'.$id, $com_grp);
			cot_redirect(cot_url($url_area, $url_params, '#c'.$id, true));
		}
	}
	$t->assign(array(
		'COMMENTS_TITLE' => $plugin_title,
		'COMMENTS_TITLE_URL' => cot_url('plug', 'e=comments')
	));
	$t->parse('MAIN.COMMENTS_TITLE');

	$sql = $db->query("SELECT * FROM $db_com WHERE com_id=? AND com_code=? AND com_area=?", array($id, $item, $area));
	cot_die($sql->rowCount() != 1);
	$com = $sql->fetch();

	$com_limit = ($sys['now_offset'] < ($com['com_date'] + $cfg['plugin']['comments']['time'] * 60)) ? TRUE : FALSE;
	$usr['isowner'] = $com_limit
		&& ($usr['id'] > 0 && $com['com_authorid'] == $usr['id'] || $usr['id'] == 0 && $usr['ip'] == $com['com_authorip']);

	$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
	cot_block($usr['allow_write']);

	$t->assign(array(
		'COMMENTS_FORM_POST' => cot_url('plug', 'e=comments&m=edit&a=update&area='.$area.'&cat='.$cat.'&item='.$com['com_code'].'&id='.$com['com_id']),
		'COMMENTS_POSTER_TITLE' => $L['Poster'],
		'COMMENTS_POSTER' => $com['com_author'],
		'COMMENTS_IP_TITLE' => $L['Ip'],
		'COMMENTS_IP' => $com['com_authorip'],
		'COMMENTS_DATE_TITLE' => $L['Date'],
		'COMMENTS_DATE' => cot_date('datetime_medium', $com['com_date'] + $usr['timezone'] * 3600),
		'COMMENTS_DATE_STAMP' => $com['com_date'] + $usr['timezone'] * 3600,
		'COMMENTS_FORM_UPDATE_BUTTON' => $L['Update'],
		'COMMENTS_FORM_TEXT' => cot_textarea('comtext', $com['com_text'], 8, 64, '', 'input_textarea_minieditor')
	));

	/* == Hook == */
	foreach (cot_getextplugins('comments.edit.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.COMMENTS_FORM_EDIT');
}

if ($a == 'send' && $usr['auth_write'])
{
	cot_shield_protect();
	$rtext = cot_import('rtext', 'P', 'HTM');

	/* == Hook == */
	foreach (cot_getextplugins('comments.send.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (mb_strlen($rtext) < 2)
	{
		cot_error($L['com_commenttooshort'], 'rtext');
	}
	if ($cfg['plugin']['comments']['commentsize'] && mb_strlen($rtext) > $cfg['plugin']['comments']['commentsize'])
	{
		cot_error($L['com_commenttoolong'], 'rtext');
	}

	if (!cot_error_found())
	{
		$comarray = array(
			'com_area' => $area,
			'com_code' => $item,
			'com_author' => $usr['name'],
			'com_authorid' => (int)$usr['id'],
			'com_authorip' => $usr['ip'],
			'com_text' => $rtext,
			'com_date' => (int)$sys['now_offset']
		);
		$sql = $db->insert($db_com, $comarray);
		$id = $db->lastInsertId();

		if ($cfg['plugin']['comments']['mail'])
		{
			$sql = $db->query("SELECT * FROM $db_users WHERE user_maingrp=5");
			$email_title = $L['plu_comlive'] . $cfg['main_url'];
			$email_body  = $L['User'] .' ' . $usr['name'] . ', ' . $L['plu_comlive2'];
			$sep = (mb_strpos($url, '?') !== false) ? '&' : '?';
			$email_body .= $url . $sep . 'comments=1#c' . $id . "\n\n";
			while ($adm = $sql->fetch())
			{
				cot_mail($adm['user_email'], $email_title, $email_body);
			}
			$sql->closeCursor();
		}

		/* == Hook == */
		foreach (cot_getextplugins('comments.send.new') as $pl)
		{
			include $pl;
		}
		/* ===== */


		cot_message($L['com_commentadded']);

		cot_shield_update(20, 'New comment');
		cot_redirect(cot_url($url_area, $url_params, '#c'.$id, true));
	}
}
elseif ($a == 'delete' && $usr['isadmin'])
{
	cot_check_xg();
	$sql = $db->query("SELECT * FROM $db_com WHERE com_id=$id AND com_area='$area' LIMIT 1");

	if ($row = $sql->fetch())
	{
		$sql->closeCursor();
		$sql = $db->delete($db_com, "com_id=$id");

		/* == Hook == */
		foreach (cot_getextplugins('comments.delete') as $pl)
		{
			include $pl;
		}
		/* ===== */

		cot_log('Deleted comment #'.$id.' in &quot;'.$item.'&quot;', 'adm');
	}
	cot_redirect(cot_url($url_area, $url_params, '#comments', true));
}
elseif ($a == 'enable' && $usr['isadmin'])
{
	$area = cot_import('area', 'P', 'ALP');
	$state = cot_import('state', 'P', 'INT');

}

cot_display_messages($t);

?>