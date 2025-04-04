<?php
/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\plugins\comments\inc\CommentsControlService;

defined('COT_CODE') or die('Wrong URL');

// Requirements
global $R, $L;
require_once cot_incfile('users', 'module');
require_once cot_langfile('comments', 'plug');
require_once cot_incfile('comments', 'plug', 'resources');
require_once cot_incfile('forms');

// Table names
Cot::$db->registerTable('com');
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
 */
function cot_comments_display($ext_name, $code, $cat = '', $force_admin = false)
{
    // $L, $R - for hooks include
	global $cfg, $usr, $L, $R, $env, $pg, $cache;

	// Check permissions and enablement
	list($auth_read, $auth_write, $auth_admin) = cot_auth('plug', 'comments');

	if ($auth_read && $auth_write && $force_admin) {
		$auth_admin = true;
		$_SESSION['cot_comments_force_admin'][$ext_name][$code] = true;
	}

	$enabled = cot_comments_enabled($ext_name, $cat, $code);

	if (!$auth_read || !$enabled && !$auth_admin) {
		return '';
	}

    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        /**
         * @deprecated $comments_join_columns in 0.9.25
         * @deprecated $comments_join_tables in 0.9.25
         * @deprecated $comments_join_where in 0.9.25
         */
        $comments_join_columns = $comments_join_tables = $comments_join_where = '';
    }

    $queryColumns = [];
    $queryJoinTables = [];
    $queryWhere = [];
    $queryParams = [];
    $queryOrder = [];

	// Get the URL and parameters
	$link_area = Cot::$env['ext'];
	$link_params = $_GET;
    unset($link_params['e']);
	if (defined('COT_PLUG')) {
		$link_area = 'plug';
		$link_params['e'] = Cot::$env['ext'];
	}
	if (isset($_GET['rwr'])) {
		unset($link_params['rwr'], $link_params['e']);
	}

    $tmpCat = $cat ?: '--';
	if (!COT_AJAX) {
		$cot_com_back = [$link_area, $link_params];
		$_SESSION['cot_com_back'][$ext_name][$tmpCat][$code] = $cot_com_back;
	}

	$d_var = 'dcm';
	list($pg, $d, $durl) = cot_import_pagenav($d_var, $cfg['plugin']['comments']['maxcommentsperpage']);
	$d = empty($d) ? 0 : (int) $d;

	if ($auth_write && $enabled) {
		require_once cot_incfile('forms');
	}

	$t = new XTemplate(cot_tplfile('comments', 'plug'));

	/* == Hook == */
	foreach (cot_getextplugins('comments.main') as $pl) {
		include $pl;
	}
	/* ===== */

    $editor = (Cot::$cfg['plugin']['comments']['markup']) ? 'input_textarea_minieditor' : '';

    // This parameter is needed only for guests and when the static cache is enabled.
    // Otherwise use session.
    $hiddenParam = '';
    if (
        Cot::$usr['id'] === 0
        && Cot::$cache
        && Cot::$cache->static->isEnabled()
    ) {
        if (!empty($cot_com_back)) {
            $hiddenParam .= cot_inputbox('hidden', 'cb', base64_encode(serialize($cot_com_back)));
        }
    }

	$t->assign([
		'COMMENTS_CODE' => $code,
        'COMMENTS_IS_AJAX' => COT_AJAX,
		'COMMENTS_FORM_SEND' => cot_url(
            'plug',
            ['e' => 'comments', 'a' => 'send', 'area' => $ext_name, 'cat' => $cat, 'item' => $code]
        ),

        // Use it if you are using custom textarea without {COMMENTS_FORM_TEXT} tag
        'COMMENTS_FORM_HIDDEN' => $hiddenParam,

        'COMMENTS_FORM_AUTHOR' => (Cot::$usr['id'] > 0) ? Cot::$usr['name'] : cot_inputbox('text', 'rname'),
		'COMMENTS_FORM_AUTHORID' => Cot::$usr['id'],
		'COMMENTS_FORM_TEXT' => $auth_write && $enabled
            ? cot_textarea('rtext', '', null, null, '', $editor) . $hiddenParam
            : '',
		'COMMENTS_DISPLAY' => Cot::$cfg['plugin']['comments']['expand_comments'] ? '' : 'none',
	]);

	if ($auth_write && $enabled) {
		// Extra fields
		if (!empty(Cot::$extrafields[Cot::$db->com])) {
			foreach (Cot::$extrafields[Cot::$db->com] as $exfld) {
				$uname = strtoupper($exfld['field_name']);
				$exfld_val = cot_build_extrafields('rcomments' . $exfld['field_name'], $exfld, '');
                $exfld_title = cot_extrafield_title($exfld, 'comments_');

				$t->assign([
					'COMMENTS_FORM_' . $uname => $exfld_val,
					'COMMENTS_FORM_' . $uname . '_TITLE' => $exfld_title,
					'COMMENTS_FORM_EXTRAFLD' => $exfld_val,
					'COMMENTS_FORM_EXTRAFLD_TITLE' => $exfld_title
				]);
				$t->parse('COMMENTS.COMMENTS_NEWCOMMENT.EXTRAFLD');
			}
		}

		$allowed_time = cot_build_timegap(
            Cot::$sys['now'] - Cot::$cfg['plugin']['comments']['time'] * 60,
            Cot::$sys['now']
        );
		$com_hint = cot_rc('com_edithint', ['time' => $allowed_time]);

		/* == Hook == */
		foreach (cot_getextplugins('comments.newcomment.tags') as $pl) {
			include $pl;
		}
		/* ===== */

        if (Cot::$usr['id'] === 0) {
            $t->parse('COMMENTS.COMMENTS_NEWCOMMENT.GUEST');

            // Don't cache page with messages and alerts.
            // Note messages can be empty at this stage
            if (cot_check_messages() && Cot::$cache) {
                $cache->static->disable();
            }
        }

		cot_display_messages($t, 'COMMENTS.COMMENTS_NEWCOMMENT');
		$t->assign('COMMENTS_FORM_HINT', $com_hint);
		$t->parse('COMMENTS.COMMENTS_NEWCOMMENT');
	} else {
		$warning = $enabled ? Cot::$L['com_regonly'] : Cot::$L['com_closed'];
		$t->assign('COMMENTS_CLOSED', $warning);
		$t->parse('COMMENTS.COMMENTS_CLOSED');
	}

    if ($enabled) {
        $queryWhere['area'] = 'com_area = :itemArea';
        $queryWhere['code'] = 'com_code = :itemCode';
        $queryParams['itemArea'] = $ext_name;
        $queryParams['itemCode'] = $code;

        $order = Cot::$cfg['plugin']['comments']['order'] == 'Chronological' ? 'ASC' : 'DESC';
        $queryOrder[] = "com_id $order";

        /* == Hook == */
        foreach (cot_getextplugins('comments.query') as $pl) {
            include $pl;
        }
        /* ===== */

        $sqlColumns = !empty($queryColumns) ? ', ' . implode(', ', $queryColumns) : '';
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            $sqlColumns .= $comments_join_columns;
        }

        $sqlWhere = !empty($queryWhere) ? "\nWHERE (" . implode(') AND (', $queryWhere) . ')' : '';
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            $sqlWhere .= $comments_join_where;
        }

        $sqlOrder = !empty($queryOrder) ? "\nORDER BY " . implode(', ', $queryOrder) : '';

        $sqlJoinTables = !empty($queryJoinTables) ? "\n" . implode("\n", $queryJoinTables) : '';
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            $sqlJoinTables .= $comments_join_tables;
        }

        $sql = "SELECT c.*, u.* $sqlColumns "
            . 'FROM ' . Cot::$db->com . ' AS c '
            . 'LEFT JOIN ' . Cot::$db->users . ' AS u ON u.user_id = c.com_authorid '
            . $sqlJoinTables . $sqlWhere . $sqlOrder
            . ' LIMIT ' . (int) Cot::$cfg['plugin']['comments']['maxcommentsperpage'] . ' OFFSET ' . (int) $d;

        $commentsList = Cot::$db->query($sql, $queryParams)->fetchAll();

        /* == Hook == */
        foreach (cot_getextplugins('comments.query.done') as $pl) {
            include $pl;
        }
        /* ===== */

        if (!empty($commentsList)) {
            $i = $d;
            $kk = 0;
            $totalItems = cot_comments_count($ext_name, $code);

            /* === Hook - Part1 : Set === */
            $extp = cot_getextplugins('comments.loop');
            /* ===== */

            foreach ($commentsList as $row) {
                $i++;
                $kk++;
                $com_admin = $auth_admin ?
                    cot_rc(
                        'comments_code_admin',
                        [
                            'ipsearch' => cot_build_ipsearch($row['com_authorip']),
                            'delete_url' => cot_confirm_url(
                                cot_url('comments', ['a' => 'delete', 'cat' => $cat, 'id' => $row['com_id'], 'x' => Cot::$sys['xk']]),
                                'comments',
                                'comments_confirm_delete'
                            ),
                        ]
                    )
                    : '';

                $row['user_id'] = (int) $row['user_id'];

                $com_text = cot_parse($row['com_text'], Cot::$cfg['plugin']['comments']['markup']);

                $time_limit = Cot::$sys['now'] < ($row['com_date'] + Cot::$cfg['plugin']['comments']['time'] * 60);
                $usr['isowner_com'] =
                    $time_limit
                    && (
                        (Cot::$usr['id'] > 0 && $row['com_authorid'] == Cot::$usr['id'])
                        || (
                            Cot::$usr['id'] == 0
                            && !empty($_SESSION['cot_comments_edit'][$row['com_id']])
                            && Cot::$usr['ip'] == $row['com_authorip']
                        )
                    );
                $com_gup = Cot::$sys['now'] - ($row['com_date'] + Cot::$cfg['plugin']['comments']['time'] * 60);
                $allowed_time = (Cot::$usr['isowner_com'] && !Cot::$usr['isadmin'])
                    ? ' - ' . cot_build_timegap(Cot::$sys['now'] + $com_gup, Cot::$sys['now']) . Cot::$L['plu_comgup']
                    : '';
                $com_edit = ($auth_admin || Cot::$usr['isowner_com'])
                    ? cot_rc(
                        'comments_code_edit',
                        [
                            'edit_url' => cot_url(
                                'plug',
                                ['e' => 'comments', 'm' => 'edit', 'cat' => $cat, 'id' => $row['com_id']]
                            ),
                            'allowed_time' => $allowed_time,
                        ]
                    )
                    : '';

                if ($row['com_area'] == 'page') {
                    if (Cot::$usr['id'] == 0 && Cot::$usr['isowner_com'] && Cot::$cfg['cache_page']) {
                        Cot::$cfg['cache_page'] = Cot::$cfg['cache_index'] = false;
                    }
                }

                if (!empty($row['user_id']) && !empty($row['user_name'])) {
                    $comAuthor = cot_build_user($row['user_id'], $row['user_name']);
                } elseif ($row['com_authorid'] == 0 && !empty($row['com_author'])) {
                    // Comment from guest
                    $comAuthor = htmlspecialchars($row['com_author']);
                } else {
                    $comAuthor = Cot::$L['Deleted'];
                }

                $t->assign([
                    'COMMENTS_ROW_ID' => $row['com_id'],
                    'COMMENTS_ROW_ORDER' => Cot::$cfg['plugin']['comments']['order'] == 'Recent' ? $totalItems - $i + 1 : $i,
                    'COMMENTS_ROW_URL' => cot_url($link_area, $link_params, '#com' . $row['com_id']),
                    'COMMENTS_ROW_AUTHOR' => $comAuthor,
                    // User can be deleted. So $row['user_id'] should be used here
                    'COMMENTS_ROW_AUTHORID' => !empty($row['user_id']) ? $row['user_id'] : 0,
                    'COMMENTS_ROW_TEXT' => $com_text,
                    'COMMENTS_ROW_DATE' => cot_date('datetime_medium', $row['com_date']),
                    'COMMENTS_ROW_DATE_STAMP' => $row['com_date'],
                    'COMMENTS_ROW_ADMIN' => $com_admin,
                    'COMMENTS_ROW_EDIT' => $com_edit,
                    'COMMENTS_ROW_ODDEVEN' => cot_build_oddeven($kk),
                    'COMMENTS_ROW_NUM' => $kk,
                ]);

                // Extrafields
                if (!empty(Cot::$extrafields[Cot::$db->com])) {
                    foreach (Cot::$extrafields[Cot::$db->com] as $exfld) {
                        $tag = mb_strtoupper($exfld['field_name']);
                        $exfld_title = cot_extrafield_title($exfld, 'comments_');

                        $t->assign([
                            'COMMENTS_ROW_' . $tag . '_TITLE' => $exfld_title,
                            'COMMENTS_ROW_' . $tag => cot_build_extrafields_data('comments', $exfld, $row['com_' . $exfld['field_name']]),
                            'COMMENTS_ROW_' . $tag . '_VALUE' => $row['com_' . $exfld['field_name']]
                        ]);
                    }
                }

                $t->assign(cot_generate_usertags($row, 'COMMENTS_ROW_AUTHOR_', htmlspecialchars($row['com_author'])));

                /* === Hook - Part2 : Include === */
                foreach ($extp as $pl) {
                    include $pl;
                }
                /* ===== */

                $t->parse('COMMENTS.COMMENTS_ROW');
            }

            $pagenav = cot_pagenav(
                $link_area,
                $link_params,
                $d,
                $totalItems,
                Cot::$cfg['plugin']['comments']['maxcommentsperpage'],
                $d_var,
                '#comments',
                Cot::$cfg['jquery'] && Cot::$cfg['turnajax'],
                'comments',
                'plug',
                "r=comments&area=$ext_name&cat=$cat&item=$code"
            );

            $t->assign([
                'COMMENTS_PAGES_INFO' => cot_rc(
                    'comments_code_pages_info',
                    ['totalitems' => $totalItems, 'onpage' => $i - $d]
                ),
            ]);

            $t->assign(cot_generatePaginationTags($pagenav, 'COMMENTS_'));

            if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                /** @deprecated in 0.9.25 */
                $t->assign([
                    'COMMENTS_PAGES_INFO' => cot_rc(
                        'comments_code_pages_info',
                        ['totalitems' => $totalItems, 'onpage' => $i - $d]
                    ),
                    'COMMENTS_PAGES_TOTALITEMS' => $totalItems,
                    'COMMENTS_PAGES_PAGESPREV' => $pagenav['prev'],
                    'COMMENTS_PAGES_PAGNAV' => $pagenav['main'],
                    'COMMENTS_PAGES_PAGESNEXT' => $pagenav['next']
                ]);
                $t->parse('COMMENTS.PAGNAVIGATOR');
            }
        } else {
            $t->assign([
                'COMMENTS_EMPTYTEXT' => Cot::$L['com_nocommentsyet'],
            ]);
            $t->parse('COMMENTS.COMMENTS_EMPTY');
        }
    }

	/* == Hook == */
	foreach (cot_getextplugins('comments.tags') as $pl) {
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
function cot_comments_link($link_area, $link_params, $ext_name, $code, $cat = '', $row = [])
{
	global $cfg, $db, $R, $L, $db_com;

	if (!cot_comments_enabled($ext_name, $cat, $code)) {
		return '';
	}

	$res = cot_rc(
        'comments_link',
        [
            'url' => cot_url($link_area, $link_params, '#comments'),
            'count' => $cfg['plugin']['comments']['countcomments'] ? cot_comments_count($ext_name, $code, $row) : '',
	    ]
    );
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

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.26
    /**
     * Removes comments associated with an item
     * @param string $area Item area code
     * @param string $code Item identifier
     * @deprecated
     * @see CommentsControlService::deleteBySourceId()
     */
    function cot_comments_remove($area, $code)
    {
        CommentsControlService::getInstance()->deleteBySourceId((string) $area, (string) $code);
    }
}
