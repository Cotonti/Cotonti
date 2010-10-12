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
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

cot_require('comments', true);

$m = cot_import('m', 'G', 'ALP');
$a = cot_import('a', 'G', 'ALP');
$id = (int) cot_import('id', 'G', 'INT');
$item = cot_import('item', 'G', 'TXT');
$cat = cot_import('cat', 'G', 'TXT');
$area = cot_import('area', 'G', 'ALP');

$plugin_title = $L['plu_title'];

// Check if comments are enabled for specific category/item
cot_block(!empty($area) && !empty($item) && cot_comments_enabled($area, $cat, $item));

$url_area = $_SESSION['cot_com_back'][$area][$cat][$item][0];
$url_params = $_SESSION['cot_com_back'][$area][$cat][$item][1];
cot_block(!empty($url_area));

if ($m == 'edit' && $id > 0)
{
	if ($a == 'update' && $id > 0)
	{
		cot_check_xg();
		/* == Hook == */
		foreach (cot_getextplugins('comments.edit.update.first') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$sql1 = cot_db_query("SELECT * FROM $db_com WHERE com_id=$id AND com_code='$item' LIMIT 1");
		cot_die(cot_db_numrows($sql1) == 0);
		$row = cot_db_fetcharray($sql1);

		$time_limit = ($sys['now_offset'] < ($row['com_date'] + $cfg['plugin']['comments']['time'] * 60)) ? TRUE : FALSE;
		$usr['isowner'] = $time_limit
			&& ($usr['id'] > 0 && $row['com_authorid'] == $usr['id']
				|| $usr['id'] == 0 && $usr['ip'] == $row['com_authorip']);
		$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
		cot_block($usr['allow_write']);

		$comtext = cot_import('comtext', 'P', 'TXT');

		if (empty($comtext))
		{
			cot_error($L['plu_comtooshort'], 'comtext');
		}

		if (!$cot_error)
		{
			$sql = cot_db_update($db_com, array('com_text' => $comtext), "com_id=$id AND com_code='$item'");

			if ($cfg['plugin']['comments']['mail'])
			{
				$sql2 = cot_db_query("SELECT * FROM $db_users WHERE user_maingrp=5");

				$email_title = $L['plu_comlive'].$cfg['main_url'];
				$email_body  = $L['User']." ".$usr['name'].", ".$L['plu_comlive3'];
				$email_body .= $url . $sep . 'comments=1#c' . $id . "\n\n";

				while ($adm = cot_db_fetcharray($sql2))
				{
					cot_mail($adm['user_email'], $email_title, $email_body);
				}
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

	$sql = cot_db_query("SELECT * FROM $db_com WHERE com_id=$id AND com_code='$item' AND com_area='$area'");
	cot_die(cot_db_numrows($sql) != 1);
	$com = cot_db_fetcharray($sql);

	$com_limit = ($sys['now_offset'] < ($com['com_date'] + $cfg['plugin']['comments']['time'] * 60)) ? TRUE : FALSE;
	$usr['isowner'] = $com_limit
		&& ($usr['id'] > 0 && $com['com_authorid'] == $usr['id'] || $usr['id'] == 0 && $usr['ip'] == $com['com_authorip']);

	$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
	cot_block($usr['allow_write']);

	$com_date = @date($cfg['dateformat'], $com['com_date'] + $usr['timezone'] * 3600);

	$t->assign(array(
		'COMMENTS_FORM_POST' => cot_url('plug', 'e=comments&m=edit&a=update&area='.$area
			.'&item='.$com['com_code'].'&id='.$com['com_id']),
		'COMMENTS_POSTER_TITLE' => $L['Poster'],
		'COMMENTS_POSTER' => $com['com_author'],
		'COMMENTS_IP_TITLE' => $L['Ip'],
		'COMMENTS_IP' => $com['com_authorip'],
		'COMMENTS_DATE_TITLE' => $L['Date'],
		'COMMENTS_DATE' => $com_date,
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

	if (!$cot_error)
	{
		$comarray = array('area' => $area, 'code' => $item, 'author' => $usr['name'], 'authorid' => (int)$usr['id'], 'authorip' => $usr['ip'],
			'text' => $rtext, 'date' => (int)$sys['now_offset']);
		$sql = cot_db_insert($db_com, $comarray, 'com_');
		$id = cot_db_insertid();

		if ($cfg['plugin']['comments']['mail'])
		{
			$sql = cot_db_query("SELECT * FROM $db_users WHERE user_maingrp=5");
			$email_title = $L['plu_comlive'] . $cfg['main_url'];
			$email_body  = $L['User'] .' ' . $usr['name'] . ', ' . $L['plu_comlive2'];
			$sep = (mb_strpos($url, '?') !== false) ? '&' : '?';
			$email_body .= $url . $sep . 'comments=1#c' . $id . "\n\n";
			while ($adm = cot_db_fetcharray($sql))
			{
				cot_mail($adm['user_email'], $email_title, $email_body);
			}
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
	$sql = cot_db_query("SELECT * FROM $db_com WHERE com_id=$id AND com_area='$area' LIMIT 1");

	if ($row = cot_db_fetchassoc($sql))
	{
		$sql = cot_db_delete($db_com, "com_id='$id'");

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