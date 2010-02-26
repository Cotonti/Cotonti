<?php
/**
 * Comments system for Cotonti
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2010
 * @license BSD
 */

// TODO I messed up this code, please see if I did huge mistakes and inform me (oc)
function sed_build_comments($code, $url, $display = true)
{
	global $db_com, $db_users, $db_pages, $db_polls, $db_structure, $cfg, $usr, $L, $sys, $R;

	list($usr['auth_read_com'], $usr['auth_write_com'], $usr['isadmin_com']) = sed_auth('plug', 'comments');
	sed_block($usr['auth_read_com']);

	if (!$usr['auth_read_com']) return (array('', ''));

	$sep = (mb_strpos($url, '?') !== false) ? '&amp;' : '?';

	$ina = sed_import('ina', 'G', 'ALP');
	$ind = sed_import('ind', 'G', 'INT');

	$d = sed_import('dcm', 'G', 'INT');
	$d = empty($d) ? 0 : (int) $d;

	if ($ina == 'send' && $usr['auth_write_com'] && $display)
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

		$error_string .= (mb_strlen($rtext) < 2) ? $L['com_commenttooshort']."<br />" : '';
		$error_string .= ($cfg['plugin']['comments']['commentsize'] && mb_strlen($rtext) > $cfg['plugin']['comments']['commentsize']) ? $L['com_commenttoolong'].'<br />' : '';

		if (empty($error_string))
		{
			$rhtml = $cfg['parser_cache'] ? sed_parse(htmlspecialchars($rtext), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true) : '';
			$sql = sed_sql_query("INSERT INTO $db_com (com_code, com_author, com_authorid, com_authorip, com_text,
				com_html, com_date) VALUES ('".sed_sql_prep($code)."', '".sed_sql_prep($usr['name'])."', "
				.(int)$usr['id'].", '".$usr['ip']."', '".sed_sql_prep($rtext)."', '".sed_sql_prep($rhtml)."', ".(int)$sys['now_offset'].")");

			$id = sed_sql_insertid();

			$type = mb_substr($code, 0, 1);
			$item_id = mb_substr($code, 1, 10);
			if ($type == 'p')
			{
				$sql = sed_sql_query("UPDATE $db_pages SET page_comcount='".sed_get_comcount($code)."' WHERE page_id='".$item_id."'");
			}
			elseif ($type == 'v')
			{
				$sql = sed_sql_query("UPDATE $db_polls SET poll_comcount='".sed_get_comcount($code)."' WHERE poll_id='".$item_id."'");
			}

			if (empty($error_string) && $cfg['plugin']['comments']['mail'])
			{
				$newcomm = sed_sql_insertid($sql);

				$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_maingrp=5");

				$email_title = $L['plu_comlive'] . $cfg['main_url'];
				$email_body  = $L['User'] .' ' . $usr['name'] . ', ' . $L['plu_comlive2'];
				$email_url = str_replace('&amp;amp;', '&', $url);
				$email_url = str_replace('&amp;', '&', $email_url);
				$sep = (mb_strpos($email_url, '?') !== false) ? '&' : '?';
				$email_body .= $cfg['mainurl'] . '/' . $email_url . $sep . 'comments=1#c' . $newcomm . "\n\n";

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

			sed_shield_update(20, 'New comment');
			sed_redirect(str_replace('&amp;', '&', $url).'#c'.$id);
		}
	}

	if ($ina == 'delete' && $usr['isadmin_com'])
	{
		sed_check_xg();
		$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_id='$ind' LIMIT 1");

		if ($row = sed_sql_fetchassoc($sql))
		{
			if ($cfg['plugin']['comments']['trash_comment'])
			{
				sed_trash_put('comment', $L['Comment']." #".$ind." (".$row['com_author'].")", $ind, $row);
			}

			$sql = sed_sql_query("DELETE FROM $db_com WHERE com_id='$ind'");

			if (mb_substr($row['com_code'], 0, 1) == 'p')
			{
				$page_id = mb_substr($row['com_code'], 1, 10);
				$sql = sed_sql_query("UPDATE $db_pages SET page_comcount=".sed_get_comcount($row['com_code'])." WHERE page_id=".$page_id);
			}

			sed_log('Deleted comment #'.$ind.' in &quot;'.$code.'&quot;', 'adm');
		}

		sed_redirect(str_replace('&amp;', '&', $url).'#comments');
	}

	$error_string .= ($ina == 'added') ? $L['com_commentadded'].'<br />' : '';

	$t = new XTemplate(sed_skinfile('comments', true));

	/* == Hook == */
	$extp = sed_getextplugins('comments.main');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql = sed_sql_query("SELECT c.*, u.user_avatar FROM $db_com AS c
		LEFT JOIN $db_users AS u ON u.user_id=c.com_authorid
		WHERE com_code='$code' ORDER BY com_id ASC LIMIT $d, ".$cfg['plugin']['comments']['maxcommentsperpage']);

	if (!empty($error_string))
	{
		$t->assign('COMMENTS_ERROR_BODY', $error_string);
		$t->parse('COMMENTS.COMMENTS_ERROR');
	}

	if ($usr['auth_write_com'] && $display)
	{
		$pfs = ($usr['id'] > 0) ? sed_build_pfs($usr['id'], 'newcomment', 'rtext', $L['Mypfs']) : '';
		$pfs .= (sed_auth('pfs', 'a', 'A')) ? ' &nbsp; '.sed_build_pfs(0, 'newcomment', 'rtext', $L['SFS']) : '';
		$post_main = '<textarea class="minieditor" name="rtext" rows="10" cols="120">'.$rtext.'</textarea><br />'.$pfs; // TODO - to resorses
	}

	$t->assign(array(
		'COMMENTS_CODE' => $code,
		'COMMENTS_FORM_SEND' => $url.$sep.'ina=send',
		'COMMENTS_FORM_AUTHOR' => $usr['name'],
		"COMMENTS_FORM_AUTHORID" => $usr['id'],
		'COMMENTS_FORM_TEXT' => $post_main,
		'COMMENTS_FORM_TEXTBOXER' => $post_main,
		'COMMENTS_FORM_MYPFS' => $pfs,
		'COMMENTS_DISPLAY' => $cfg['plugin']['comments']['expand_comments'] ? '' : 'none'
	));

	if ($usr['auth_write_com'] && $display)
	{

		$allowed_time = sed_build_timegap($sys['now_offset'] - $cfg['plugin']['comedit']['time'] * 60, $sys['now_offset']);
		$com_hint = sprintf($L['plu_comhint'], $allowed_time);

		/* == Hook == */
		$extp = sed_getextplugins('comments.newcomment.tags');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->assign('COMMENTS_FORM_HINT', $com_hint);
		$t->parse('COMMENTS.COMMENTS_NEWCOMMENT');
	}
	elseif (!$display)
	{
		$t->assign('COMMENTS_CLOSED', $L['com_closed']);
		$t->parse('COMMENTS.COMMENTS_CLOSED');
	}


	if (sed_sql_numrows($sql) > 0)
	{
		$i = $d;

		/* === Hook - Part1 : Set === */
		$extp = sed_getextplugins('comments.loop');
		/* ===== */

		while ($row = sed_sql_fetcharray($sql))
		{
			$i++;
			$com_author = htmlspecialchars($row['com_author']);

			$com_admin = ($usr['isadmin_com']) ? $L['Ip'].':'.sed_build_ipsearch($row['com_authorip']).' &nbsp;'.$L['Delete'].':[<a href="'.$url.$sep.'ina=delete&amp;ind='.$row['com_id'].'&amp;'.sed_xg().'">x</a>]' : ''; // TODO - to resorses
			$com_authorlink = sed_build_user($row['com_authorid'], $com_author);

			if ($cfg['parser_cache'])
			{
				if (empty($row['com_html']) && !empty($row['com_text']))
				{
					$row['com_html'] = sed_parse(htmlspecialchars($row['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
					sed_sql_query("UPDATE $db_com SET com_html = '".sed_sql_prep($row['com_html'])."' WHERE com_id = ".$row['com_id']);
				}
				$com_text = $cfg['parsebbcodepages'] ? sed_post_parse($row['com_html']) : htmlspecialchars($row['com_text']);
			}
			else
			{
				$com_text = sed_parse(htmlspecialchars($row['com_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true);
				$com_text = sed_post_parse($com_text, 'pages');
			}

			$time_limit = ($sys['now_offset'] < ($row['com_date'] + $cfg['plugin']['comedit']['time'] * 60)) ? TRUE : FALSE;
			$usr['isowner_com'] = $time_limit && ($usr['id'] > 0 && $row['com_authorid'] == $usr['id'] || $usr['id'] == 0 && $usr['ip'] == $row['com_authorip']);
			$com_gup = $sys['now_offset'] - ($row['com_date'] + $cfg['plugin']['comedit']['time'] * 60);
			$allowed_time = ($usr['isowner_com'] && !$usr['isadmin']) ? ' - ' . sed_build_timegap($sys['now_offset'] + $com_gup, $sys['now_offset']) . $L['plu_comgup'] : '';
			$com_edit = ($usr['isadmin_com'] || $usr['isowner_com']) ? '<a href="'.sed_url('plug', 'e=comedit&m=edit&amp;pid=' . $code . '&amp;cid=' . $row['com_id']).'">'. $L['Edit'] . '</a>' . $allowed_time : '';

			$t-> assign(array(
				'COMMENTS_ROW_ID' => $row['com_id'],
				'COMMENTS_ROW_ORDER' => $i,
				'COMMENTS_ROW_URL' => $url.'#c'.$row['com_id'],
				'COMMENTS_ROW_AUTHOR' => $com_authorlink,
				'COMMENTS_ROW_AUTHORID' => $row['com_authorid'],
				'COMMENTS_ROW_AVATAR' => sed_build_userimage($row['user_avatar'], 'avatar'),
				'COMMENTS_ROW_TEXT' => $com_text,
				'COMMENTS_ROW_DATE' => @date($cfg['dateformat'], $row['com_date'] + $usr['timezone'] * 3600),
				'COMMENTS_ROW_ADMIN' => $com_admin,
				'COMMENTS_ROW_EDIT' => $com_edit
			));

			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			$t->parse('COMMENTS.COMMENTS_ROW');
		}

		$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_com WHERE com_code='$code'"), 0, 0);
		// TODO use sed_pagenav()
		$pagnav = sed_pagination($url, $d, $totalitems, $cfg['plugin']['comments']['maxcommentsperpage'], 'dcm');
		list($pagination_prev, $pagination_next) = sed_pagination_pn($url, $d, $totalitems, $cfg['plugin']['comments']['maxcommentsperpage'], TRUE, 'dcm');
		if (!$cfg['plugin']['comments']['expand_comments'])
		{
			// A dirty fix for pagination anchors
			$pagnav = preg_replace('/href="(.+?)"/', 'href="$1#comments"', $pagnav);
			$pagination_prev = preg_replace('/href="(.+?)"/', 'href="$1#comments"', $pagination_prev);
			$pagination_next = preg_replace('/href="(.+?)"/', 'href="$1#comments"', $pagination_next);
		}
		$t->assign(array(
			'COMMENTS_PAGES_INFO' => $L['Total'].' : '.$totalitems.', '.$L['comm_on_page'].': '.($i - $d),
			'COMMENTS_PAGES_PAGESPREV' => $pagination_prev,
			'COMMENTS_PAGES_PAGNAV' => $pagnav,
			'COMMENTS_PAGES_PAGESNEXT' => $pagination_next
		));
		$t->parse('COMMENTS.PAGNAVIGATOR');

	}
	elseif (!sed_sql_numrows($sql) && $display)
	{
		$t-> assign(array(
			'COMMENTS_EMPTYTEXT' => $L['com_nocommentsyet'],
		));
		$t->parse('COMMENTS.COMMENTS_EMPTY');
	}

	/* == Hook == */
	$extp = sed_getextplugins('comments.tags');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('COMMENTS');
	$res_display = $t->text('COMMENTS');

	$res = '<a href="'.$url.'#comments" class="comments_link" alt="'.$L['Comments'].'">'.$R['icon_comments']; // TODO - to resorses
	if ($cfg['plugin']['comments']['countcomments'])
	{
		if (isset($totalitems)) $nbcomment = $totalitems;
		else $nbcomment = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_com where com_code='$code'"), 0, 0);
		$res .= ' ('.$nbcomment.')'; // TODO - to resorses
	}
	$res .= '</a>'; // TODO - to resorses

	return(array($res, $res_display, $nbcomment));
}

/**
 * Returns number of comments for item
 *
 * @param string $code Item code
 * @return int
 */
function sed_get_comcount($code)
{
	global $db_com;

	$sql = sed_sql_query("SELECT DISTINCT com_code, COUNT(*) FROM $db_com WHERE com_code='$code' GROUP BY com_code");

	if ($row = sed_sql_fetcharray($sql))
	{
		return (int) $row['COUNT(*)'];
	}
	else
	{
		return 0;
	}
}

?>