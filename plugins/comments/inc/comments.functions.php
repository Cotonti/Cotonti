<?php
/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
global $R, $L;
require_once cot_incfile('users', 'module');
require_once cot_langfile('comments', 'plug');
require_once cot_incfile('comments', 'plug', 'resources');
require_once cot_incfile('forms');

// Table names
cot::$db->registerTable('com');
cot_extrafields_register_table('com');

/**
 * Returns number of comments for item
 *
 * @param string $ext_name Target extension name
 * @param string $code Item code
 * @param array $row Database row entry (optional)
 * @return int
 * @global CotDB $db
 */
function cot_comments_count($ext_name, $code, $row = array())
{
	global $db, $db_com;
	static $com_cache = array();

	if (isset($com_cache[$ext_name][$code]))
	{
		return $com_cache[$ext_name][$code];
	}

	$cnt = 0;
	if (isset($row['com_count']))
	{
		$cnt = (int) $row['com_count'];
		$com_cache[$ext_name][$code] = $cnt;
	}
	else
	{
		$comments_join_columns = '';
		$comments_join_tables = '';
		$comments_join_where = '';
		/* == Hook == */
		foreach (cot_getextplugins('comments.count.query') as $pl)
		{
			include $pl;
		}
		/* ===== */
		$sql = $db->query("SELECT COUNT(*) $comments_join_columns
			FROM $db_com $comments_join_tables
			WHERE com_area = ? AND com_code = ? $comments_join_where",
			array($ext_name, $code));
		if ($sql->rowCount() == 1)
		{
			$cnt = (int) $sql->fetchColumn();
			$com_cache[$ext_name][$code] = $cnt;
		}
	}

	return $cnt;
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
 * @global CotDB $db
 */
function cot_comments_display($ext_name, $code, $cat = '', $force_admin = false)
{
	global $db, $db_com, $db_users, $cfg, $usr, $L, $sys, $R, $env, $pg, $cot_extrafields, $cache, $structure;

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

    $comments_join_columns = $comments_join_tables = $comments_join_where = '';

	// Get the URL and parameters
	$link_area = $env['ext'];
	$link_params = $_GET;
	if (defined('COT_PLUG'))
	{
		$link_area = 'plug';
		$link_params['e'] = $env['ext'];
	}
	if (isset($_GET['rwr']))
	{
		unset($link_params['rwr'], $link_params['e']);
	}

    $cot_com_back = array($link_area, $link_params);
	$_SESSION['cot_com_back'][$ext_name][$cat][$code] = $cot_com_back;

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
    $editor = (cot::$cfg['plugin']['comments']['markup']) ? 'input_textarea_minieditor' : '';
	$t->assign(array(
		'COMMENTS_CODE' => $code,
		'COMMENTS_FORM_SEND' => cot_url('plug', "e=comments&a=send&area=$ext_name&cat=$cat&item=$code"),
		'COMMENTS_FORM_AUTHOR' => ($usr['id'] > 0) ? $usr['name'] : cot_inputbox('text', 'rname'),
		'COMMENTS_FORM_AUTHORID' => $usr['id'],
		'COMMENTS_FORM_TEXT' => $auth_write && $enabled ? cot_textarea('rtext', $rtext, 7, 120, '', $editor).
            cot_inputbox('hidden', 'cb', base64_encode(serialize($cot_com_back))) : '',
		'COMMENTS_DISPLAY' => $cfg['plugin']['comments']['expand_comments'] ? '' : 'none'
	));

	if ($auth_write && $enabled)
	{
		// Extra fields
		if(!empty(cot::$extrafields[cot::$db->com])) {
			foreach (cot::$extrafields[cot::$db->com] as $exfld) {
				$uname = strtoupper($exfld['field_name']);
				$exfld_val = cot_build_extrafields('rcomments' . $exfld['field_name'], $exfld, $rcomments[$exfld['field_name']]);
                $exfld_title = cot_extrafield_title($exfld, 'comments_');

				$t->assign(array(
					'COMMENTS_FORM_' . $uname => $exfld_val,
					'COMMENTS_FORM_' . $uname . '_TITLE' => $exfld_title,
					'COMMENTS_FORM_EXTRAFLD' => $exfld_val,
					'COMMENTS_FORM_EXTRAFLD_TITLE' => $exfld_title
				));
				$t->parse('COMMENTS.COMMENTS_NEWCOMMENT.EXTRAFLD');
			}
		}

		$allowed_time = cot_build_timegap($sys['now'] - $cfg['plugin']['comments']['time'] * 60,
			$sys['now']);
		$com_hint = cot_rc('com_edithint', array('time' => $allowed_time));

		/* == Hook == */
		foreach (cot_getextplugins('comments.newcomment.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$usr['id'] == 0 && $t->parse('COMMENTS.COMMENTS_NEWCOMMENT.GUEST');
		if ($usr['id'] == 0 && cot_check_messages() && $cache)
		{
			if($ext_name == 'page' && $cfg['cache_page'])
			{
				$cache->page->clear('page/' . str_replace('.', '/', $structure['page'][$cat]['path']));
				$cfg['cache_page'] = false;
			}
		}
		cot_display_messages($t, 'COMMENTS.COMMENTS_NEWCOMMENT');
		$t->assign('COMMENTS_FORM_HINT', $com_hint);
		$t->parse('COMMENTS.COMMENTS_NEWCOMMENT');
	}
	else
	{
		$warning = $enabled ? $L['com_regonly'] : $L['com_closed'];
		$t->assign('COMMENTS_CLOSED', $warning);
		$t->parse('COMMENTS.COMMENTS_CLOSED');
	}

	$order = $cfg['plugin']['comments']['order'] == 'Chronological' ? 'ASC' : 'DESC';
	$comments_order = "com_id $order";

	/* == Hook == */
	foreach (cot_getextplugins('comments.query') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql = cot::$db->query("SELECT c.*, u.* $comments_join_columns
		FROM ".cot::$db->com." AS c LEFT JOIN ".cot::$db->users." AS u ON u.user_id = c.com_authorid $comments_join_tables
		WHERE com_area = ? AND com_code = ? $comments_join_where ORDER BY $comments_order LIMIT ?, ?",
		array($ext_name, $code, (int) $d, (int) $cfg['plugin']['comments']['maxcommentsperpage']));
	if ($sql->rowCount() > 0 && $enabled)
	{
		$i = $d;
		$kk = 0;
		$totalitems = cot_comments_count($ext_name, $code);

		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('comments.loop');
		/* ===== */

		foreach ($sql->fetchAll() as $row)
		{
			$i++;
			$kk++;
			$com_admin = ($auth_admin) ? cot_rc('comments_code_admin', array(
					'ipsearch' => cot_build_ipsearch($row['com_authorip']),
					'delete_url' => cot_confirm_url(cot_url('plug', 'e=comments&a=delete&cat='.$cat.
                        '&id='.$row['com_id'].'&'.cot_xg()), 'comments', 'comments_confirm_delete')
				)) : '';

            $row['user_id'] = (int)$row['user_id'];

			$com_text = cot_parse($row['com_text'], cot::$cfg['plugin']['comments']['markup']);

			$time_limit = (cot::$sys['now'] < ($row['com_date'] + cot::$cfg['plugin']['comments']['time'] * 60)) ? TRUE : FALSE;
            $usr['isowner_com'] = $time_limit && ( (cot::$usr['id'] > 0 && $row['com_authorid'] == cot::$usr['id'] )
                || (cot::$usr['id'] == 0 && !empty($_SESSION['cot_comments_edit'][$row['com_id']]) && cot::$usr['ip'] == $row['com_authorip']) );
			$com_gup = cot::$sys['now'] - ($row['com_date'] + cot::$cfg['plugin']['comments']['time'] * 60);
			$allowed_time = (cot::$usr['isowner_com'] && !cot::$usr['isadmin']) ? ' - '
				. cot_build_timegap(cot::$sys['now'] + $com_gup, cot::$sys['now']) . cot::$L['plu_comgup'] : '';
			$com_edit = ($auth_admin || cot::$usr['isowner_com']) ? cot_rc('comments_code_edit', array(
					'edit_url' => cot_url('plug', 'e=comments&m=edit&cat='.$cat.'&id='.$row['com_id']),
					'allowed_time' => $allowed_time
				)) : '';

            if($row['com_area'] == 'page') {
                if(cot::$usr['id'] == 0 && cot::$usr['isowner_com'] && cot::$cfg['cache_page']) {
                    cot::$cfg['cache_page'] = cot::$cfg['cache_index'] = false;
                }
            }

            $t->assign(array(
				'COMMENTS_ROW_ID' => $row['com_id'],
				'COMMENTS_ROW_ORDER' => cot::$cfg['plugin']['comments']['order'] == 'Recent' ? $totalitems - $i + 1 : $i,
				'COMMENTS_ROW_URL' => cot_url($link_area, $link_params, '#c'.$row['com_id']),
				'COMMENTS_ROW_AUTHOR' => cot_build_user($row['user_id'], htmlspecialchars($row['com_author'])),
				// User can be deleted. So $row['user_id'] should be used here
				'COMMENTS_ROW_AUTHORID' => $row['user_id'],
				'COMMENTS_ROW_TEXT' => $com_text,
				'COMMENTS_ROW_DATE' => cot_date('datetime_medium', $row['com_date']),
				'COMMENTS_ROW_DATE_STAMP' => $row['com_date'],
				'COMMENTS_ROW_ADMIN' => $com_admin,
				'COMMENTS_ROW_EDIT' => $com_edit,
				'COMMENTS_ROW_ODDEVEN' => cot_build_oddeven($kk),
				'COMMENTS_ROW_NUM' => $kk
			));

			// Extrafields
            if(!empty(cot::$extrafields[cot::$db->com])) {
                foreach (cot::$extrafields[cot::$db->com] as $exfld) {
					$tag = mb_strtoupper($exfld['field_name']);
                    $exfld_title = cot_extrafield_title($exfld, 'comments_');

					$t->assign(array(
						'COMMENTS_ROW_' . $tag . '_TITLE' => $exfld_title,
						'COMMENTS_ROW_' . $tag => cot_build_extrafields_data('comments', $exfld, $row['com_'.$exfld['field_name']]),
						'COMMENTS_ROW_' . $tag . '_VALUE' => $row['com_'.$exfld['field_name']]
					));
				}
			}

			$t->assign(cot_generate_usertags($row, 'COMMENTS_ROW_AUTHOR_', htmlspecialchars($row['com_author'])));

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
		$t->assign(array(
			'COMMENTS_PAGES_INFO' => cot_rc('comments_code_pages_info', array(
					'totalitems' => $totalitems,
					'onpage' => $i - $d
				)),
			'COMMENTS_PAGES_TOTALITEMS' => $totalitems,
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
	if (isset($cfg[$ext_name]['cat_'.$cat]['enable_comments'])
		|| isset($cfg[$ext_name]['enable_comments'])
		|| isset($cfg['plugin'][$ext_name]['enable_comments'])
		|| isset($cfg[$ext_name]['cat___default']['enable_comments']))
	{
		if (isset($cot_modules[$ext_name]))
		{
			if (isset($cfg[$ext_name]['cat_'.$cat]['enable_comments']))
			{
				return $cfg[$ext_name]['cat_'.$cat]['enable_comments'];
			}
			else
			{
				return isset($cfg[$ext_name]['cat___default']['enable_comments'])
					? $cfg[$ext_name]['cat___default']['enable_comments']
					: $cfg[$ext_name]['enable_comments'];
			}
		}
		else
		{
			return (bool) $cfg['plugin'][$ext_name]['enable_comments'];
		}
	}
	return true;
}

/**
 * Generates comments display for a given item
 *
 * @param string $link_area Target URL area for cot_url()
 * @param string $link_params Target URL params for cot_url()
 * @param string $ext_name Module or plugin code
 * @param string $code Item identifier
 * @param string $cat Item category code (optional)
 * @param array $row Database row entry (optional)
 * @return string Rendered HTML output for comments
 * @see cot_comments_count()
 * @global CotDB $db
 */
function cot_comments_link($link_area, $link_params, $ext_name, $code, $cat = '', $row = array())
{
	global $cfg, $db, $R, $L, $db_com;

	if (!cot_comments_enabled($ext_name, $cat, $code))
	{
		return '';
	}

	$res = cot_rc('comments_link', array(
		'url' => cot_url($link_area, $link_params, '#comments'),
		'count' => $cfg['plugin']['comments']['countcomments'] ? cot_comments_count($ext_name, $code, $row) : ''
	));
	return $res;
}

/**
 * New comments count for admin page
 *
 * @param string $timeback Datetime to count from
 * @return int
 * @global CotDB $db
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
 * @global CotDB $db
 */
function cot_comments_remove($area, $code)
{
	global $db, $db_com;

	$db->delete($db_com, 'com_area = ? AND com_code = ?', array($area, $code));
}
