<?php
/**
 * Comments system for Cotonti
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
global $R, $L;
require_once cot_incfile('users', 'module');
require_once cot_langfile('comments', 'plug');
require_once cot_incfile('comments', 'plug', 'resources');

// Table name globals
global $db_com, $db_x;
$db_com = (isset($db_com)) ? $db_com : $db_x . 'com';

/**
 * Returns number of comments for item
 *
 * @param string $ext_name Target extension name
 * @param string $code Item code
 * @return int
 */
function cot_comments_count($ext_name, $code)
{
	global $db, $db_com;
	static $com_cache = array();

	if (isset($com_cache[$ext_name][$code]))
	{
		return $com_cache[$ext_name][$code];
	}

	$sql = $db->query("SELECT COUNT(*) FROM $db_com WHERE com_area = ? AND com_code = ?", array($ext_name, $code));

	if ($sql->rowCount() == 1)
	{
		$cnt = (int) $sql->fetchColumn();
		$com_cache[$ext_name][$code] = $cnt;
		return $cnt;
	}
	else
	{
		return 0;
	}
}

/**
 * Generates comments display for a given item
 *
 * @param string $ext_name Module or plugin code
 * @param string $code Item identifier
 * @param string $cat Item category code (optional)
 * @param bool $force_admin Enforces user to be administrator of comments for this item.
 *	E.g. to moderate his wall even if he is not a moderator
 * @return string Rendered HTML output for comments
 */
function cot_comments_display($ext_name, $code, $cat = '', $force_admin = false)
{
	global $db, $db_com, $db_users, $cfg, $usr, $L, $sys, $R, $env, $pg;

	// Check permissions and enablement
	list($auth_read, $auth_write, $auth_admin) = cot_auth('plug', 'comments');

	if ($auth_read && $auth_write && $force_admin)
	{
		$auth_admin = true;
		$_SESSION['cot_comments_force_admin'][$ext_name][$code] = true;
	}

	$enabled = cot_comments_enabled($ext_name, $cat, $code);

	if (!$auth_read || !$enabled && !$auth_admin)
	{
		return '';
	}

	// Get the URL and parameters
	$link_area = $env['ext'];
	$link_params = $_SERVER['QUERY_STRING'];

	$_SESSION['cot_com_back'][$ext_name][$cat][$code] = array($link_area, $link_params);

	$d_var = 'dcm';
	list($pg, $d, $durl) = cot_import_pagenav($d_var, $cfg['plugin']['comments']['maxcommentsperpage']);
	$d = empty($d) ? 0 : (int) $d;

	if ($auth_write && $enabled)
	{
		require_once cot_incfile('forms');
	}

	$t = new XTemplate(cot_tplfile('comments', 'plug'));

	/* == Hook == */
	foreach (cot_getextplugins('comments.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->assign(array(
		'COMMENTS_CODE' => $code,
		'COMMENTS_FORM_SEND' => cot_url('plug', "e=comments&a=send&area=$ext_name&cat=$cat&item=$code"),
		'COMMENTS_FORM_AUTHOR' => ($usr['id'] > 0) ? $usr['name'] : cot_inputbox('text', 'rname'),
		'COMMENTS_FORM_AUTHORID' => $usr['id'],
		'COMMENTS_FORM_TEXT' => $auth_write && $enabled ? cot_textarea('rtext', $rtext, 10, 120, '', 'input_textarea_minieditor')
			: '',
		'COMMENTS_DISPLAY' => $cfg['plugin']['comments']['expand_comments'] ? '' : 'none'
	));

	if ($auth_write && $enabled)
	{
		
		$allowed_time = cot_build_timegap($sys['now_offset'] - $cfg['plugin']['comments']['time'] * 60,
			$sys['now_offset']);
		$com_hint = cot_rc('com_edithint', array('time' => $allowed_time));
		
		/* == Hook == */
		foreach (cot_getextplugins('comments.newcomment.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$usr['id'] == 0 && $t->parse('COMMENTS.COMMENTS_NEWCOMMENT.GUEST');
		cot_display_messages($t, 'COMMENTS.COMMENTS_NEWCOMMENT');
		$t->assign('COMMENTS_FORM_HINT', $com_hint);
		$t->parse('COMMENTS.COMMENTS_NEWCOMMENT');
	}
	else
	{
		$t->assign('COMMENTS_CLOSED', $L['com_closed']);
		$t->parse('COMMENTS.COMMENTS_CLOSED');
	}

	$order = $cfg['plugin']['comments']['order'] == 'Chronological' ? 'ASC' : 'DESC';

	$sql = $db->query("SELECT c.*, u.*
		FROM $db_com AS c LEFT JOIN $db_users AS u ON u.user_id = c.com_authorid
		WHERE com_area = ? AND com_code = ? ORDER BY com_id $order LIMIT ?, ?",
		array($ext_name, $code, (int) $d, (int) $cfg['plugin']['comments']['maxcommentsperpage']));
	if ($sql->rowCount() > 0 && $enabled)
	{
		$i = $d;
		$kk = 0;
		$totalitems = $db->query("SELECT COUNT(*) FROM $db_com WHERE com_code = ?", array($code))->fetchColumn();
		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('comments.loop');
		/* ===== */
		
		foreach ($sql->fetchAll() as $row)
		{
			$i++;
			$kk++;
			$com_admin = ($auth_admin) ? cot_rc('comments_code_admin', array(
					'ipsearch' => cot_build_ipsearch($row['com_authorip']),
					'delete_url' => cot_url('plug', 'e=comments&a=delete&cat='.$cat.'&id='.$row['com_id'].'&'.cot_xg())
				)) : '';

			$com_text = cot_parse($row['com_text'], $cfg['plugin']['comments']['markup']);

			$time_limit = ($sys['now_offset'] < ($row['com_date'] + $cfg['plugin']['comments']['time'] * 60)) ? TRUE
				: FALSE;
			$usr['isowner_com'] = $time_limit && ($usr['id'] > 0 && $row['com_authorid'] == $usr['id']
				|| $usr['id'] == 0 && $usr['ip'] == $row['com_authorip']);
			$com_gup = $sys['now_offset'] - ($row['com_date'] + $cfg['plugin']['comments']['time'] * 60);
			$allowed_time = ($usr['isowner_com'] && !$usr['isadmin']) ? ' - '
				. cot_build_timegap($sys['now_offset'] + $com_gup, $sys['now_offset']) . $L['plu_comgup'] : '';
			$com_edit = ($auth_admin || $usr['isowner_com']) ? cot_rc('comments_code_edit', array(
					'edit_url' => cot_url('plug', 'e=comments&m=edit&cat='.$cat.'&id='.$row['com_id']),
					'allowed_time' => $allowed_time
				)) : '';
			
			$t->assign(array(
				'COMMENTS_ROW_ID' => $row['com_id'],
				'COMMENTS_ROW_ORDER' => $cfg['plugin']['comments']['order'] == 'Recent' ? $totalitems - $i + 1 : $i,
				'COMMENTS_ROW_URL' => cot_url($link_area, $link_params, '#c'.$row['com_id']),
				'COMMENTS_ROW_AUTHOR' => cot_build_user($row['com_authorid'], htmlspecialchars($row['com_author'])),
				'COMMENTS_ROW_AUTHORID' => $row['com_authorid'],
				'COMMENTS_ROW_TEXT' => $com_text,
				'COMMENTS_ROW_DATE' => cot_date('datetime_medium', $row['com_date'] + $usr['timezone'] * 3600),
				'COMMENTS_ROW_DATE_STAMP' => $row['com_date'] + $usr['timezone'] * 3600,
				'COMMENTS_ROW_ADMIN' => $com_admin,
				'COMMENTS_ROW_EDIT' => $com_edit,
				'COMMENTS_ROW_ODDEVEN' => cot_build_oddeven($kk),
				'COMMENTS_ROW_NUM' => $kk
			));
			$t->assign(cot_generate_usertags($row, 'COMMENTS_ROW_AUTHOR_'), htmlspecialchars($row['com_author']));

			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			$t->parse('COMMENTS.COMMENTS_ROW');
		}

		$pagenav = cot_pagenav($link_area, $link_params, $d, $totalitems,
			$cfg['plugin']['comments']['maxcommentsperpage'], $d_var, '#comments',
			$cfg['jquery'] && $cfg['ajax_enabled'], 'comments', 'plug', "e=comments&area=$ext_name&cat=$cat&item=$code");
		if (!$cfg['plugin']['comments']['expand_comments'])
		{
			// A dirty fix for pagination anchors
			$pagenav['main'] = preg_replace('/href="(.+?)"/', 'href="$1#comments"', $pagenav['main']);
			$pagenav['prev'] = preg_replace('/href="(.+?)"/', 'href="$1#comments"', $pagenav['prev']);
			$pagenav['next'] = preg_replace('/href="(.+?)"/', 'href="$1#comments"', $pagenav['next']);
		}
		$t->assign(array(
			'COMMENTS_PAGES_INFO' => cot_rc('comments_code_pages_info', array(
					'totalitems' => $totalitems,
					'onpage' => $i - $d
				)),
			'COMMENTS_PAGES_PAGESPREV' => $pagenav['prev'],
			'COMMENTS_PAGES_PAGNAV' => $pagenav['main'],
			'COMMENTS_PAGES_PAGESNEXT' => $pagenav['next']
		));
		$t->parse('COMMENTS.PAGNAVIGATOR');

	}
	elseif (!$sql->rowCount() && $enabled)
	{
		$t->assign(array(
			'COMMENTS_EMPTYTEXT' => $L['com_nocommentsyet'],
		));
		$t->parse('COMMENTS.COMMENTS_EMPTY');
	}


	/* == Hook == */
	foreach (cot_getextplugins('comments.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('COMMENTS');
	$res_display = $t->text('COMMENTS');

	return $res_display;
}

/**
 * Checks if comments are enabled for specific extension and category
 *
 * @param string $ext_name Extension name
 * @param string $cat Category name or empty if checking the entire area
 * @param string $item Item code, not yet supported
 * @return bool
 */
function cot_comments_enabled($ext_name, $cat = '', $item = '')
{
	global $cfg, $cot_modules;
	
	if (isset($cot_modules[$ext_name]))
	{
		return (bool) (isset($cfg[$ext_name][$cat]['enable_comments']) ? $cfg[$ext_name][$cat]['enable_comments']
			: $cfg[$ext_name]['enable_comments']);
	}
	else
	{
		return (bool) $cfg['plugin'][$ext_name]['enable_comments'];
	}
}

/**
 * Generates comments display for a given item
 *
 * @param string $link_area Target URL area for cot_url()
 * @param string $link_params Target URL params for cot_url()
 * @param string $ext_name Module or plugin code
 * @param string $code Item identifier
 * @param string $cat Item category code (optional)
 * @return string Rendered HTML output for comments
 * @see cot_comments_count()
 */
function cot_comments_link($link_area, $link_params, $ext_name, $code, $cat = '')
{
	global $cfg, $db, $R, $L, $db_com;

	if (!cot_comments_enabled($ext_name, $cat, $code))
	{
		return '';
	}

	$res = cot_rc('comments_link', array(
		'url' => cot_url($link_area, $link_params, '#comments'),
		'count' => $cfg['plugin']['comments']['countcomments'] ? cot_comments_count($ext_name, $code) : ''
	));
	return $res;
}

/**
 * New comments count for admin page
 *
 * @param string $timeback Datetime to count from
 * @return int
 */
function cot_comments_newcount($timeback)
{
	global $db, $db_com;

	$sql = $db->query("SELECT COUNT(*) FROM $db_com WHERE com_date > ?", array($timeback));
	$newcomments = $sql->fetchColumn();
	return $newcomments;
}

/**
 * Removes comments associated with an item
 *
 * @param string $area Item area code
 * @param string $code Item identifier
 */
function cot_comments_remove($area, $code)
{
	global $db, $db_com;

	$db->delete($db_com, 'com_area = ? AND com_code = ?', array($area, $code));
}

?>
