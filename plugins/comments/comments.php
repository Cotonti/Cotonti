<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=comments.edit
File=comments
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Asmo, motor2hg, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') && defined('SED_PLUG') or die('Wrong URL');

require_once sed_incfile('config', 'comments', true);
require_once sed_incfile('functions', 'comments', true);
require_once sed_incfile('resources', 'comments', true);

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
		$extp = sed_getextplugins('comments.edit.update.first');
		foreach ($extp as $pl)
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
			$sql = sed_sql_query("UPDATE $db_com SET com_text = '".sed_sql_prep($comtext)."',
				com_html = '".sed_sql_prep($comhtml)."' WHERE com_id=$id AND com_code='$item'");

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
			$extp = sed_getextplugins('comments.edit.update.done');
			foreach ($extp as $pl)
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
	$extp = sed_getextplugins('comments.edit.tags');
	foreach ($extp as $pl)
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
	$extp = sed_getextplugins('comments.send.first');
	foreach ($extp as $pl)
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
		$sql = sed_sql_query("INSERT INTO $db_com (com_area, com_code, com_author, com_authorid, com_authorip, com_text,
				com_html, com_date)
			VALUES ('".sed_sql_prep($area)."', '".sed_sql_prep($item)."', '".sed_sql_prep($usr['name'])."', "
			.(int)$usr['id'].", '".$usr['ip']."', '".sed_sql_prep($rtext)."', '".sed_sql_prep($rhtml)."',"
			.(int)$sys['now_offset'].")");

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
		$extp = sed_getextplugins('comments.send.new');
		foreach ($extp as $pl)
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

		$sql = sed_sql_query("DELETE FROM $db_com WHERE com_id='$id'");

		sed_log('Deleted comment #'.$id.' in &quot;'.$item.'&quot;', 'adm');
	}
	sed_redirect($url.'#comments');
}
elseif ($a == 'enable' && $usr['isadmin'])
{
	$area = sed_import('area', 'P', 'ALP');
	$state = sed_import('state', 'P', 'INT');

}

if (sed_check_messages())
{
	$t->assign('COMMENTS_ERROR_BODY', sed_implode_messages());
	$t->parse('MAIN.COMMENTS_ERROR');
	sed_clear_messages();
}

?>