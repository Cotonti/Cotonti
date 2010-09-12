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
sed_require('users');
sed_require_lang('comments', 'plug');
sed_require_rc('comments', true);

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
function sed_comments_count($area, $code)
{
	global $db_com;
	static $cache = array();

	if (isset($cache[$area][$code]))
	{
		return $cache[$area][$code];
	}

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_com WHERE com_area='$area' AND com_code='$code'");

	if ($row = sed_sql_fetchrow($sql))
	{
		$cache[$area][$code] = (int) $row[0];
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
function sed_comments_display($area, $code, $cat = '')
{
	global $db_com, $db_users, $cfg, $usr, $L, $sys, $R, $z;

	// Check permissions and enablement
	list($auth_read, $auth_write, $auth_admin) = sed_auth('plug', 'comments');

	$enabled_row = sed_comments_enabled($area, $cat, $code, true);
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
	$d = sed_import($d_var, 'G', 'INT');
	$d = empty($d) ? 0 : (int) $d;

	if ($auth_write && $enabled)
	{
		sed_require_api('forms');
	}

	$t = new XTemplate(sed_skinfile('comments', true));

	/* == Hook == */
	foreach (sed_getextplugins('comments.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->assign(array(
		'COMMENTS_CODE' => $code,
		'COMMENTS_FORM_SEND' => sed_url('plug', "e=comments&a=send&area=$area&cat=$cat&item=$code"),
		'COMMENTS_FORM_AUTHOR' => $usr['name'],
		'COMMENTS_FORM_AUTHORID' => $usr['id'],
		'COMMENTS_FORM_TEXT' => $auth_write && $enabled ? sed_textarea('rtext', $rtext, 10, 120, '', 'input_textarea_minieditor')
			: '',
		'COMMENTS_DISPLAY' => $cfg['plugin']['comments']['expand_comments'] ? '' : 'none'
	));

	if ($auth_write && $enabled)
	{

		$allowed_time = sed_build_timegap($sys['now_offset'] - $cfg['plugin']['comedit']['time'] * 60,
			$sys['now_offset']);
		$com_hint = sprintf($L['plu_comhint'], $allowed_time);

		/* == Hook == */
		foreach (sed_getextplugins('comments.newcomment.tags') as $pl)
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

	$sql = sed_sql_query("SELECT c.*, u.* FROM $db_com AS c
		LEFT JOIN $db_users AS u ON u.user_id=c.com_authorid
		WHERE com_area='$area' AND com_code='$code' ORDER BY com_id ASC LIMIT $d, "
		.$cfg['plugin']['comments']['maxcommentsperpage']);

	if (sed_sql_numrows($sql) > 0 && $enabled)
	{
		$i = $d;

		/* === Hook - Part1 : Set === */
		$extp = sed_getextplugins('comments.loop');
		/* ===== */

		while ($row = sed_sql_fetcharray($sql))
		{
			$i++;
			$com_author = htmlspecialchars($row['com_author']);

			$com_admin = ($auth_admin) ? sed_rc('comments_code_admin', array(
					'ipsearch' => sed_build_ipsearch($row['com_authorip']),
					'delete_url' => sed_url('plug', 'e=comments&a=delete&id='.$row['com_id'].'&'.sed_xg())
				)) : '';
			$com_authorlink = sed_build_user($row['com_authorid'], $com_author);

			if ($cfg['parser_cache'])
			{
				if (empty($row['com_html']) && !empty($row['com_text']))
				{
					$row['com_html'] = sed_parse(htmlspecialchars($row['com_text']), $cfg['parsebbcodecom'],
						$cfg['parsesmiliescom'], true);
					sed_sql_update($db_com, array('com_html' => $row['com_html']), "com_id = ".$row['com_id']);
				}
				$com_text = $cfg['parsebbcodepages'] ? sed_post_parse($row['com_html'])
					: htmlspecialchars($row['com_text']);
			}
			else
			{
				$com_text = sed_parse(htmlspecialchars($row['com_text']), $cfg['parsebbcodecom'],
					$cfg['parsesmiliescom'], true);
				$com_text = sed_post_parse($com_text, 'pages');
			}

			$time_limit = ($sys['now_offset'] < ($row['com_date'] + $cfg['plugin']['comedit']['time'] * 60)) ? TRUE
				: FALSE;
			$usr['isowner_com'] = $time_limit && ($usr['id'] > 0 && $row['com_authorid'] == $usr['id']
				|| $usr['id'] == 0 && $usr['ip'] == $row['com_authorip']);
			$com_gup = $sys['now_offset'] - ($row['com_date'] + $cfg['plugin']['comedit']['time'] * 60);
			$allowed_time = ($usr['isowner_com'] && !$usr['isadmin']) ? ' - '
				. sed_build_timegap($sys['now_offset'] + $com_gup, $sys['now_offset']) . $L['plu_comgup'] : '';
			$com_edit = ($auth_admin || $usr['isowner_com']) ? sed_rc('comments_code_edit', array(
					'edit_url' => sed_url('plug', 'e=comedit&m=edit&amp;pid=' . $code . '&amp;cid=' . $row['com_id']),
					'allowed_time' => $allowed_time
				)) : '';
			
			$t->assign(array(
				'COMMENTS_ROW_ID' => $row['com_id'],
				'COMMENTS_ROW_ORDER' => $i,
				'COMMENTS_ROW_URL' => sed_url($link_area, $link_params, '#c'.$row['com_id']),
				'COMMENTS_ROW_AUTHOR' => $com_authorlink,
				'COMMENTS_ROW_AUTHORID' => $row['com_authorid'],
				'COMMENTS_ROW_AVATAR' => sed_build_userimage($row['user_avatar'], 'avatar'),
				'COMMENTS_ROW_TEXT' => $com_text,
				'COMMENTS_ROW_DATE' => @date($cfg['dateformat'], $row['com_date'] + $usr['timezone'] * 3600),
				'COMMENTS_ROW_ADMIN' => $com_admin,
				'COMMENTS_ROW_EDIT' => $com_edit
			));
			$t->assign(sed_generate_usertags($pag, 'COMMENTS_ROW_AUTHOR'), $com_author);

			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */

			$t->parse('COMMENTS.COMMENTS_ROW');
		}

		$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_com WHERE com_code='$code'"), 0, 0);
		$pagenav = sed_pagenav($link_area, $link_params, $d, $totalitems,
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
			'COMMENTS_PAGES_INFO' => sed_rc('comments_code_pages_info', array(
					'totalitems' => $totalitems,
					'onpage' => $i - $d
				)),
			'COMMENTS_PAGES_PAGESPREV' => $pagenav['prev'],
			'COMMENTS_PAGES_PAGNAV' => $pagenav['main'],
			'COMMENTS_PAGES_PAGESNEXT' => $pagenav['next']
		));
		$t->parse('COMMENTS.PAGNAVIGATOR');

	}
	elseif (!sed_sql_numrows($sql) && $enabled)
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

		$enablement_change = sed_radiobox((int) !$enabled, 'state', array(1, 0), array($L['Enable'], $L['Disable']));
		$L['comments'] = lcfirst($L['Comments']);
		$enablement_selection = sed_selectbox($enablement_type, 'area', array('item', 'cat', 'area'),
			array($L['for_this_item'], $L['for_this_category'], $L['for_this_area']), false);

		$t->assign(array(
			'COMMENTS_ENABLEMENT_ACTION' => sed_url('plug', "e=comments&a=enable&area=$area&cat=$cat&item=$code"),
			'COMMENTS_ENABLEMENT_STATE' => $enabled ? $L['Enabled'] : $L['Disabled'],
			'COMMENTS_ENABLEMENT_AREA' => $enablement_area,
			'COMMENTS_ENABLEMENT_CHANGE' => $enablement_change,
			'COMMENTS_ENABLEMENT_AREA_SELECTION' => $enablement_selection
		));
		$t->parse('COMMENTS.COMMENTS_ENABLEMENT');
	}

	if (sed_check_messages())
	{
		$t->assign('COMMENTS_ERROR_BODY', sed_implode_messages());
		$t->parse('COMMENTS.COMMENTS_ERROR');
		sed_clear_messages();
	}

	/* == Hook == */
	foreach (sed_getextplugins('comments.tags') as $pl)
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
function sed_comments_enabled($area, $cat = '', $code = '', $return_row = false)
{
	global $db_com_settings;
	// A static call cache saves us from duplicate queries
	static $cache = array();
	// FIXME row cache and cache per item
	if (isset($cache[$area][$cat]) && !$return_row)
	{
		return $cache[$area][$cat];
	}

	if (!sed_auth('plug', 'comments', 'R'))
	{
		$cache[$area][$cat] = false;
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
	$res = sed_sql_query("SELECT coms_enabled, coms_area, coms_cat, coms_code FROM $db_com_settings
		WHERE coms_area = '$area' AND coms_cat = '$cat' AND coms_code = '$code' $extra_where
		ORDER BY coms_code DESC, coms_cat DESC LIMIT 1");
	if ($row = sed_sql_fetchassoc($res))
	{
		$enabled &= (bool) $row['coms_enabled'];
	}
	sed_sql_freeresult($res);

	$cache[$area][$cat] = $enabled;
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
 * @param string $link_area Target URL area for sed_url()
 * @param string $link_params Target URL params for sed_url()
 * @param string $area Module or plugin code
 * @param string $code Item identifier
 * @param string $cat Item category code (optional)
 * @return string Rendered HTML output for comments
 */
function sed_comments_link($link_area, $link_params, $area, $code, $cat = '')
{
	global $R, $L, $db_com;

	if (!sed_comments_enabled($area, $cat, $code))
	{
		return '';
	}

	$res = sed_rc('comments_link', array(
		'url' => sed_url($link_area, $link_params, '#comments'),
		'count' => $cfg['plugin']['comments']['countcomments'] ? sed_comments_count($area, $code) : ''
	));
	return $res;
}

/**
 * New comments count for admin page
 *
 * @param string $timeback Datetime to count from
 * @return int
 */
function sed_comments_newcount($timeback)
{
	global $db_com;

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_com WHERE com_date>'$timeback'");
	$newcomments = sed_sql_result($sql, 0, 'COUNT(*)');
	return $newcomments;
}

/**
 * Removes comments associated with an item
 *
 * @param string $area Item area code
 * @param string $code Item identifier
 */
function sed_comments_remove($area, $code)
{
	global $db_com, $db_com_settings;
	sed_sql_delete($db_com, "com_area = '$area' AND com_code = '$code'");
	sed_sql_delete($db_com_settings, "com_area = '$area' AND com_code = '$code'");
}

?>
