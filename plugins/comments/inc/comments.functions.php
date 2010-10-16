<?php
/**
 * Comments system for Cotonti
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2010
 * @license BSD
 */

// Requirements
cot_require('users');
cot_require_lang('comments', 'plug');
cot_require_rc('comments', true);

// Table name globals
$GLOBALS['db_com'] = (isset($GLOBALS['db_com'])) ? $GLOBALS['db_com'] : $GLOBALS['db_x'] . 'com';
$GLOBALS['db_com_settings'] = (isset($GLOBALS['db_com_settings'])) ? $GLOBALS['db_com_settings'] : $GLOBALS['db_x'] . 'com_settings';

/**
 * Returns number of comments for item
 *
 * @param string $area Site area
 * @param string $code Item code
 * @return int
 */
function cot_comments_count($area, $code)
{
	global $db, $db_com;
	static $com_cache = array();

	if (isset($com_cache[$area][$code]))
	{
		return $com_cache[$area][$code];
	}

	$sql = $db->query("SELECT COUNT(*) FROM $db_com WHERE com_area='$area' AND com_code='$code'");

	if ($row = $sql->fetch(PDO::FETCH_NUM))
	{
		$com_cache[$area][$code] = (int) $row[0];
		return (int) $row[0];
	}
	else
	{
		return 0;
	}
}

/**
 * Generates comments display for a given item
 *
 * @param string $area Module or plugin code
 * @param string $code Item identifier
 * @param string $cat Item category code (optional)
 * @return string Rendered HTML output for comments
 */
function cot_comments_display($area, $code, $cat = '')
{
	global $db, $db_com, $db_users, $cfg, $usr, $L, $sys, $R, $z;

	// Check permissions and enablement
	list($auth_read, $auth_write, $auth_admin) = cot_auth('plug', 'comments');

	$enabled_row = cot_comments_enabled($area, $cat, $code, true);
	$enabled = $enabled_row['coms_enabled'];

	if (!$auth_read || !$enabled && !$auth_admin)
	{
		return '';
	}

	// Get the URL and parameters
	$link_area = $z;
	$link_params = $_SERVER['QUERY_STRING'];

	$_SESSION['cot_com_back'][$area][$cat][$code] = array($link_area, $link_params);

	$d_var = 'dcm';
	$d = cot_import($d_var, 'G', 'INT');
	$d = empty($d) ? 0 : (int) $d;

	if ($auth_write && $enabled)
	{
		cot_require_api('forms');
	}

	$t = new XTemplate(cot_skinfile('comments', true));

	/* == Hook == */
	foreach (cot_getextplugins('comments.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->assign(array(
		'COMMENTS_CODE' => $code,
		'COMMENTS_FORM_SEND' => cot_url('plug', "e=comments&a=send&area=$area&cat=$cat&item=$code"),
		'COMMENTS_FORM_AUTHOR' => $usr['name'],
		'COMMENTS_FORM_AUTHORID' => $usr['id'],
		'COMMENTS_FORM_TEXT' => $auth_write && $enabled ? cot_textarea('rtext', $rtext, 10, 120, '', 'input_textarea_minieditor')
			: '',
		'COMMENTS_DISPLAY' => $cfg['plugin']['comments']['expand_comments'] ? '' : 'none'
	));

	if ($auth_write && $enabled)
	{

		$allowed_time = cot_build_timegap($sys['now_offset'] - $cfg['plugin']['comedit']['time'] * 60,
			$sys['now_offset']);
		$com_hint = sprintf($L['plu_comhint'], $allowed_time);

		/* == Hook == */
		foreach (cot_getextplugins('comments.newcomment.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->assign('COMMENTS_FORM_HINT', $com_hint);
		$t->parse('COMMENTS.COMMENTS_NEWCOMMENT');
	}
	else
	{
		$t->assign('COMMENTS_CLOSED', $L['com_closed']);
		$t->parse('COMMENTS.COMMENTS_CLOSED');
	}

	$sql = $db->query("SELECT c.*, u.* FROM $db_com AS c
		LEFT JOIN $db_users AS u ON u.user_id=c.com_authorid
		WHERE com_area='$area' AND com_code='$code' ORDER BY com_id ASC LIMIT $d, "
		.$cfg['plugin']['comments']['maxcommentsperpage']);

	if ($sql->rowCount() > 0 && $enabled)
	{
		$i = $d;

		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('comments.loop');
		/* ===== */

		while ($row = $sql->fetch())
		{
			$i++;
			$com_author = htmlspecialchars($row['com_author']);

			$com_admin = ($auth_admin) ? cot_rc('comments_code_admin', array(
					'ipsearch' => cot_build_ipsearch($row['com_authorip']),
					'delete_url' => cot_url('plug', 'e=comments&a=delete&cat='.$cat.'&id='.$row['com_id'].'&'.cot_xg())
				)) : '';
			$com_authorlink = cot_build_user($row['com_authorid'], $com_author);

			$com_text = cot_parse($row['com_text'], $cfg['plugin']['comments']['markup']);

			$time_limit = ($sys['now_offset'] < ($row['com_date'] + $cfg['plugin']['comedit']['time'] * 60)) ? TRUE
				: FALSE;
			$usr['isowner_com'] = $time_limit && ($usr['id'] > 0 && $row['com_authorid'] == $usr['id']
				|| $usr['id'] == 0 && $usr['ip'] == $row['com_authorip']);
			$com_gup = $sys['now_offset'] - ($row['com_date'] + $cfg['plugin']['comedit']['time'] * 60);
			$allowed_time = ($usr['isowner_com'] && !$usr['isadmin']) ? ' - '
				. cot_build_timegap($sys['now_offset'] + $com_gup, $sys['now_offset']) . $L['plu_comgup'] : '';
			$com_edit = ($auth_admin || $usr['isowner_com']) ? cot_rc('comments_code_edit', array(
					'edit_url' => cot_url('plug', 'e=comments&m=edit&cat='.$cat.'&id='.$row['com_id']),
					'allowed_time' => $allowed_time
				)) : '';
			
			$t->assign(array(
				'COMMENTS_ROW_ID' => $row['com_id'],
				'COMMENTS_ROW_ORDER' => $i,
				'COMMENTS_ROW_URL' => cot_url($link_area, $link_params, '#c'.$row['com_id']),
				'COMMENTS_ROW_AUTHOR' => $com_authorlink,
				'COMMENTS_ROW_AUTHORID' => $row['com_authorid'],
				'COMMENTS_ROW_AVATAR' => cot_build_userimage($row['user_avatar'], 'avatar'),
				'COMMENTS_ROW_TEXT' => $com_text,
				'COMMENTS_ROW_DATE' => @date($cfg['dateformat'], $row['com_date'] + $usr['timezone'] * 3600),
				'COMMENTS_ROW_ADMIN' => $com_admin,
				'COMMENTS_ROW_EDIT' => $com_edit
			));
			$t->assign(cot_generate_usertags($pag, 'COMMENTS_ROW_AUTHOR'), $com_author);

			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			$t->parse('COMMENTS.COMMENTS_ROW');
		}

		$totalitems = $db->query("SELECT COUNT(*) FROM $db_com WHERE com_code='$code'")->fetchColumn();
		$pagenav = cot_pagenav($link_area, $link_params, $d, $totalitems,
			$cfg['plugin']['comments']['maxcommentsperpage'], $d_var, '#comments',
			$cfg['jquery'] && $cfg['ajax_enabled'], 'comments', 'plug', "e=comments&area=$area&cat=$cat&item=$code");
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

	if ($auth_admin)
	{
		// Enable/disable comments for this item/category/module

		if (!empty($enabled_row['coms_code']))
		{
			$enablement_area = $L['for_this_item'];
			$enablement_type = 'item';
		}
		elseif (!empty($enabled_row['coms_cat']))
		{
			$enablement_area = $L['for_this_category'];
			$enablement_type = 'cat';
		}
		else
		{
			$enablement_area = $L['for_this_area'];
			$enablement_type = 'area';
		}

		$enablement_change = cot_radiobox((int) !$enabled, 'state', array(1, 0), array($L['Enable'], $L['Disable']));
		$enablement_selection = cot_selectbox($enablement_type, 'area', array('item', 'cat', 'area'),
			array($L['for_this_item'], $L['for_this_category'], $L['for_this_area']), false);

		$t->assign(array(
			'COMMENTS_ENABLEMENT_ACTION' => cot_url('plug', "e=comments&a=enable&area=$area&cat=$cat&item=$code"),
			'COMMENTS_ENABLEMENT_STATE' => $enabled ? $L['Enabled'] : $L['Disabled'],
			'COMMENTS_ENABLEMENT_AREA' => $enablement_area,
			'COMMENTS_ENABLEMENT_CHANGE' => $enablement_change,
			'COMMENTS_ENABLEMENT_AREA_SELECTION' => $enablement_selection
		));
		$t->parse('COMMENTS.COMMENTS_ENABLEMENT');
	}

	cot_display_messages($t);

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
 * Checks if comments are enabled for specific area and category
 *
 * @param string $area Area name
 * @param string $cat Category name or empty if checking the entire area
 * @param string $code Item code
 * @param bool $return_row If true returns the matching row instead of bool result, used to detect the enablement type
 * @return bool
 */
function cot_comments_enabled($area, $cat = '', $code = '', $return_row = false)
{
	global $db, $db_com_settings;
	// A static call cache saves us from duplicate queries
	static $com_cache = array();
	// FIXME row cache and cache per item
	if (isset($com_cache[$area][$cat]) && !$return_row)
	{
		return $com_cache[$area][$cat];
	}

	if (!cot_auth('plug', 'comments', 'R'))
	{
		$com_cache[$area][$cat] = false;
		return false;
	}

	$enabled = true;
	if (!empty($cat) && !empty($code))
	{
		$extra_where = "OR coms_area = '$area' AND coms_cat = '$cat' AND coms_code = ''
			OR coms_area = '$area' AND coms_cat = '' AND coms_code = ''";
	}
	elseif (!empty($cat) || !empty($code))
	{
		$extra_where = "OR coms_area = '$area' AND coms_cat = '' AND coms_code = ''";
	}
	$res = $db->query("SELECT coms_enabled, coms_area, coms_cat, coms_code FROM $db_com_settings
		WHERE coms_area = '$area' AND coms_cat = '$cat' AND coms_code = '$code' $extra_where
		ORDER BY coms_code DESC, coms_cat DESC LIMIT 1");
	if ($row = $res->fetch())
	{
		$enabled &= (bool) $row['coms_enabled'];
	}
	$res->closeCursor();

	$com_cache[$area][$cat] = $enabled;
	if ($return_row)
	{
		if (!$row)
		{
			$row['coms_enabled'] = $enabled;
			$row['coms_area'] = $area;
		}
		return $row;
	}
	else
	{
		return $enabled;
	}
}

/**
 * Generates comments display for a given item
 *
 * @param string $link_area Target URL area for cot_url()
 * @param string $link_params Target URL params for cot_url()
 * @param string $area Module or plugin code
 * @param string $code Item identifier
 * @param string $cat Item category code (optional)
 * @return string Rendered HTML output for comments
 */
function cot_comments_link($link_area, $link_params, $area, $code, $cat = '')
{
	global $cfg, $db, $R, $L, $db_com;

	if (!cot_comments_enabled($area, $cat, $code))
	{
		return '';
	}

	$res = cot_rc('comments_link', array(
		'url' => cot_url($link_area, $link_params, '#comments'),
		'count' => $cfg['plugin']['comments']['countcomments'] ? cot_comments_count($area, $code) : ''
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

	$sql = $db->query("SELECT COUNT(*) FROM $db_com WHERE com_date>'$timeback'");
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
	global $db, $db_com, $db_com_settings;
	$db->delete($db_com, "com_area = '$area' AND com_code = '$code'");
	$db->delete($db_com_settings, "coms_area = '$area' AND coms_code = '$code'");
}

?>
