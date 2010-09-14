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

defined('SED_CODE') && defined('SED_PLUG') or die('Wrong URL');

sed_require('comments', true);

$m = sed_import('m', 'G', 'ALP');
$a = sed_import('a', 'G', 'ALP');
$id = (int) sed_import('id', 'G', 'INT');
$item = sed_import('item', 'G', 'TXT');
$cat = sed_import('cat', 'G', 'TXT');
$area = sed_import('area', 'G', 'ALP');

$plugin_title = $L['plu_title'];

// Check if comments are enabled for specific category/item
sed_block(!empty($area) && !empty($item) && sed_comments_enabled($area, $cat, $item));

$url_area = $_SESSION['cot_com_back'][$area][$cat][$item][0];
$url_params = $_SESSION['cot_com_back'][$area][$cat][$item][1];
sed_block(!empty($url_area));

if ($m == 'edit' && $id > 0)
{
	if ($a == 'update' && $id > 0)
	{
		sed_check_xg();
		/* == Hook == */
		foreach (sed_getextplugins('comments.edit.update.first') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$sql1 = sed_sql_query("SELECT * FROM $db_com WHERE com_id=$id AND com_code='$item' LIMIT 1");
		sed_die(sed_sql_numrows($sql1) == 0);
		$row = sed_sql_fetcharray($sql1);

		$time_limit = ($sys['now_offset'] < ($row['com_date'] + $cfg['plugin']['comments']['time'] * 60)) ? TRUE : FALSE;
		$usr['isowner'] = $time_limit
			&& ($usr['id'] > 0 && $row['com_authorid'] == $usr['id']
				|| $usr['id'] == 0 && $usr['ip'] == $row['com_authorip']);
		$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
		sed_block($usr['allow_write']);

		$comtext = sed_import('comtext', 'P', 'TXT');

		if (empty($comtext))
		{
			sed_error($L['plu_comtooshort'], 'comtext');
		}

		if (!$cot_error)
		{
			$comhtml = $cfg['parser_cache'] ? sed_parse(htmlspecialchars($comtext), $cfg['parsebbcodecom'],
				$cfg['parsesmiliescom'], true) : '';
			$sql = sed_sql_update($db_com, array('com_text' => $comtext, 'com_html' => $comhtml), "com_id=$id AND com_code='$item'");

			if ($cfg['plugin']['comments']['mail'])
			{
				$sql2 = sed_sql_query("SELECT * FROM $db_users WHERE user_maingrp=5");

				$email_title = $L['plu_comlive'].$cfg['main_url'];
				$email_body  = $L['User']." ".$usr['name'].", ".$L['plu_comlive3'];
				$email_body .= $url . $sep . 'comments=1#c' . $id . "\n\n";

				while ($adm = sed_sql_fetcharray($sql2))
				{
					sed_mail($adm['user_email'], $email_title, $email_body);
				}
			}
			/* == Hook == */
			foreach (sed_getextplugins('comments.edit.update.done') as $pl)
			{
				include $pl;
			}
			/* ===== */

			$com_grp = ($usr['isadmin']) ? 'adm' : 'usr';
			sed_log('Edited comment #'.$id, $com_grp);
			sed_redirect($url.'#c'.$id);
		}
	}
	$t->assign(array(
		'COMMENTS_TITLE' => $plugin_title,
		'COMMENTS_TITLE_URL' => sed_url('plug', 'e=comments')
	));
	$t->parse('MAIN.COMMENTS_TITLE');

	$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_id=$id AND com_code='$item' AND com_area='$area'");
	sed_die(sed_sql_numrows($sql) != 1);
	$com = sed_sql_fetcharray($sql);

	$com_limit = ($sys['now_offset'] < ($com['com_date'] + $cfg['plugin']['comments']['time'] * 60)) ? TRUE : FALSE;
	$usr['isowner'] = $com_limit
		&& ($usr['id'] > 0 && $com['com_authorid'] == $usr['id'] || $usr['id'] == 0 && $usr['ip'] == $com['com_authorip']);

	$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
	sed_block($usr['allow_write']);

	$com_date = @date($cfg['dateformat'], $com['com_date'] + $usr['timezone'] * 3600);

	$t->assign(array(
		'COMMENTS_FORM_POST' => sed_url('plug', 'e=comments&m=edit&a=update&area='.$area
			.'&item='.$com['com_code'].'&id='.$com['com_id']),
		'COMMENTS_POSTER_TITLE' => $L['Poster'],
		'COMMENTS_POSTER' => $com['com_author'],
		'COMMENTS_IP_TITLE' => $L['Ip'],
		'COMMENTS_IP' => $com['com_authorip'],
		'COMMENTS_DATE_TITLE' => $L['Date'],
		'COMMENTS_DATE' => $com_date,
		'COMMENTS_FORM_UPDATE_BUTTON' => $L['Update'],
		'COMMENTS_FORM_TEXT' => sed_textarea('comtext', $com['com_text'], 8, 64, '', 'input_textarea_minieditor')
	));

	/* == Hook == */
	foreach (sed_getextplugins('comments.edit.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.COMMENTS_FORM_EDIT');
}

if ($a == 'send' && $usr['auth_write'])
{
	sed_shield_protect();
	$rtext = sed_import('rtext', 'P', 'HTM');

	/* == Hook == */
	foreach (sed_getextplugins('comments.send.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (mb_strlen($rtext) < 2)
	{
		sed_error($L['com_commenttooshort'], 'rtext');
	}
	if ($cfg['plugin']['comments']['commentsize'] && mb_strlen($rtext) > $cfg['plugin']['comments']['commentsize'])
	{
		sed_error($L['com_commenttoolong'], 'rtext');
	}

	if (!$cot_error)
	{
		$rhtml = $cfg['parser_cache'] ? sed_parse(htmlspecialchars($rtext), $cfg['parsebbcodecom'],
			$cfg['parsesmiliescom'], true) : '';

		$comarray += array('area' => $area, 'code' => $item, 'author' => $usr['name'], 'authorid' => (int)$usr['id'], 'authorip' => $usr['ip'],
			'text' => $rtext, 'html' => $rhtml, 'date' => (int)$sys['now_offset']);
		$sql = sed_sql_insert($db_com, $comarray, 'com_');
		$id = sed_sql_insertid();

		if ($cfg['plugin']['comments']['mail'])
		{
			$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_maingrp=5");
			$email_title = $L['plu_comlive'] . $cfg['main_url'];
			$email_body  = $L['User'] .' ' . $usr['name'] . ', ' . $L['plu_comlive2'];
			$sep = (mb_strpos($url, '?') !== false) ? '&' : '?';
			$email_body .= $url . $sep . 'comments=1#c' . $id . "\n\n";
			while ($adm = sed_sql_fetcharray($sql))
			{
				sed_mail($adm['user_email'], $email_title, $email_body);
			}
		}

		/* == Hook == */
		foreach (sed_getextplugins('comments.send.new') as $pl)
		{
			include $pl;
		}
		/* ===== */


		sed_message($L['com_commentadded']);

		sed_shield_update(20, 'New comment');
		sed_redirect($url.'#c'.$id);
	}
}
elseif ($a == 'delete' && $usr['isadmin'])
{
	sed_check_xg();
	$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_id=$id AND com_area='$area' LIMIT 1");

	if ($row = sed_sql_fetchassoc($sql))
	{
		if ($cfg['plugin']['comments']['trash_comment'])
		{
			sed_trash_put('comment', $L['Comment']." #".$id." (".$row['com_author'].")", $id, $row);
		}

		$sql = sed_sql_delete($db_com, "com_id='$id'");

		sed_log('Deleted comment #'.$id.' in &quot;'.$item.'&quot;', 'adm');
	}
	sed_redirect($url.'#comments');
}
elseif ($a == 'enable' && $usr['isadmin'])
{
	$area = sed_import('area', 'P', 'ALP');
	$state = sed_import('state', 'P', 'INT');

}

sed_display_messages($t);

?>