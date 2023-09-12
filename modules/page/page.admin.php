<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */

/**
 * Pages manager & Queue of pages
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('page', 'any');
cot_block($usr['isadmin']);

$t = new XTemplate(cot_tplfile('page.admin', 'module', true));

require_once cot_incfile('page', 'module');

$adminpath[] = array(cot_url('admin', 'm=extensions'), Cot::$L['Extensions']);
$adminpath[] = array(cot_url('admin', 'm=extensions&a=details&mod='.$m), $cot_modules[$m]['title']);
$adminpath[] = array(cot_url('admin', 'm='.$m), Cot::$L['Administration']);
$adminhelp = Cot::$L['adm_help_page'];
$adminTitle = Cot::$L['Pages'];

$id = cot_import('id', 'G', 'INT');

list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['maxrowsperpage']);

$sorttype = cot_import('sorttype', 'R', 'ALP');
$sorttype = empty($sorttype) ? 'id' : $sorttype;
if ($sorttype != 'id' && !Cot::$db->fieldExists(Cot::$db->pages, "page_$sorttype")) {
	$sorttype = 'id';
}
$sqlsorttype = 'page_'.$sorttype;

$sort_type = cot_page_config_order(true);

$sortway = cot_import('sortway', 'R', 'ALP');
$sortway = empty($sortway) ? 'desc' : $sortway;
$sort_way = array(
	'asc' => Cot::$L['Ascending'],
	'desc' => Cot::$L['Descending']
);
$sqlsortway = $sortway;

$filter = cot_import('filter', 'R', 'ALP');
$filter = empty($filter) ? 'valqueue' : $filter;
$filter_type = array(
	'all' => Cot::$L['All'],
	'valqueue' => Cot::$L['adm_valqueue'],
	'validated' => Cot::$L['adm_validated'],
	'expired' => Cot::$L['adm_expired'],
	'drafts' => Cot::$L['page_drafts'],
);

$urlParams = ['m' => 'page'];
if ($sorttype != 'id') {
    $urlParams['sorttype'] = $sorttype;
}
if ($sortway != 'desc') {
    $urlParams['sortway'] = $sortway;
}
if ($filter != 'valqueue') {
    $urlParams['filter'] = $filter;
}

/**
 * Common UrlParams without pagination
 * @deprecated
 */
$common_params = http_build_query($urlParams, '', '&');

if ($pg > 1) {
    $urlParams['d'] = $durl;
}

if ($filter == 'all') {
	$sqlwhere = "1 ";
} elseif ($filter == 'valqueue') {
	$sqlwhere = "page_state=1";
} elseif ($filter == 'validated') {
	$sqlwhere = "page_state=0";
} elseif ($filter == 'drafts') {
	$sqlwhere = "page_state=2";
} elseif ($filter == 'expired') {
	$sqlwhere = "page_begin > {$sys['now']} OR (page_expire <> 0 AND page_expire < {$sys['now']})";
}

$catsub = cot_structure_children('page', '');
if (count($catsub) < count(Cot::$structure['page'])) {
	$sqlwhere .= " AND page_cat IN ('" . join("','", $catsub) . "')";
}

$backUrl = cot_import('back', 'G', 'HTM');
$backUrl = !empty($backUrl) ?
    base64_decode($backUrl) : cot_url('admin', $urlParams, '', true);

/* === Hook  === */
foreach (cot_getextplugins('page.admin.first') as $pl) {
	include $pl;
}
/* ===== */

if ($a == 'validate') {
	cot_check_xg();

	/* === Hook  === */
	foreach (cot_getextplugins('page.admin.validate') as $pl) {
		include $pl;
	}
	/* ===== */

    $row = \Cot::$db->query(
        'SELECT page_id, page_alias, page_cat, page_begin, page_state FROM ' . \Cot::$db->pages
        . ' WHERE page_id = ?',
        $id
    )->fetch();
	if ($row) {
        if ($row['page_state'] == COT_PAGE_STATE_PUBLISHED) {
            cot_message('#' . $id . ' - ' . \Cot::$L['adm_already_updated']);
            cot_redirect($backUrl);
        }

		$usr['isadmin_local'] = cot_auth('page', $row['page_cat'], 'A');
		cot_block($usr['isadmin_local']);
        $data = ['page_state' => COT_PAGE_STATE_PUBLISHED];
		if ($row['page_begin'] < Cot::$sys['now']) {
            $data['page_begin'] = Cot::$sys['now'];
		}
		$sql_page = Cot::$db->update(Cot::$db->pages, $data, "page_id = $id");

		/* === Hook  === */
		foreach (cot_getextplugins('page.admin.validate.done') as $pl) {
			include $pl;
		}
		/* ===== */

		cot_log(
            \Cot::$L['Page'].' #' . $id . ' - ' . \Cot::$L['adm_queue_validated'],
            'page',
            'validate',
            'done'
        );

		if (\Cot::$cache) {
            \Cot::$cache->db->remove('structure', 'system');
			if (\Cot::$cfg['cache_page']) {
                \Cot::$cache->static->clearByUri(cot_page_url($row));
                \Cot::$cache->static->clearByUri(cot_url('page', ['c' => $row['page_cat']]));
			}
			if (\Cot::$cfg['cache_index']) {
                \Cot::$cache->static->clear('index');
			}
		}
		cot_message('#' . $id . ' - ' . \Cot::$L['adm_queue_validated']);

	} else {
        cot_error('#' . $id . ' - ' . \Cot::$L['nf']);
	}

    cot_redirect($backUrl);

} elseif ($a == 'unvalidate') {
	cot_check_xg();

	/* === Hook  === */
	foreach (cot_getextplugins('page.admin.unvalidate') as $pl) {
		include $pl;
	}
	/* ===== */

    $row = Cot::$db->query(
        'SELECT page_id, page_alias, page_cat, page_state FROM ' . Cot::$db->pages . ' WHERE page_id = ?',
        $id
    )->fetch();
    if ($row) {
        if ($row['page_state'] == COT_PAGE_STATE_PENDING) {
            cot_message('#' . $id . ' - ' . Cot::$L['adm_already_updated']);
            cot_redirect($backUrl);
        }

		\Cot::$usr['isadmin_local'] = cot_auth('page', $row['page_cat'], 'A');
		cot_block($usr['isadmin_local']);

		$sql_page = \Cot::$db->update(
            \Cot::$db->pages,
            ['page_state' => COT_PAGE_STATE_PENDING],
            'page_id = ?',
            $id
        );

		cot_log(Cot::$L['Page'] . ' #' . $id . ' - ' . \Cot::$L['adm_queue_unvalidated'], 'page', 'unvalidated', 'done');

		if (\Cot::$cache) {
            \Cot::$cache->db->remove('structure', 'system');
			if (Cot::$cfg['cache_page']) {
                \Cot::$cache->static->clearByUri(cot_page_url($row));
                \Cot::$cache->static->clearByUri(cot_url('page', ['c' => $row['page_cat']]));
			}
			if (\Cot::$cfg['cache_index']) {
                \Cot::$cache->static->clear('index');
			}
		}

		cot_message('#' . $id . ' - ' . \Cot::$L['adm_queue_unvalidated']);

    } else {
        cot_error('#' . $id . ' - ' . \Cot::$L['nf']);
	}

    cot_redirect($backUrl);

} elseif ($a == 'delete') {
	cot_check_xg();

	/* === Hook  === */
	foreach (cot_getextplugins('page.admin.delete') as $pl) {
		include $pl;
	}
	/* ===== */

    $result = cot_page_delete($id);
    if ($result) {
        /* === Hook === */
		foreach (cot_getextplugins('page.admin.delete.done') as $pl) {
			include $pl;
		}
		/* ===== */

        cot_message('#' . $id . ' - ' . Cot::$L['adm_queue_deleted']);

    } else {
        cot_error('#' . $id . ' - ' . Cot::$L['adm_failed']);
    }

    cot_redirect(cot_url('admin', $urlParams, '', true));

} elseif ($a == 'update_checked') {
	$paction = cot_import('paction', 'P', 'TXT');
	$s = cot_import('s', 'P', 'ARR');

	if ($paction == 'validate' && is_array($s)) {
		cot_check_xp();

		$perelik = '';
		$notfoundet = '';
		foreach ($s as $i => $k) {
			if ($s[$i] == '1' || $s[$i] == 'on') {
				/* === Hook  === */
				foreach (cot_getextplugins('page.admin.checked_validate') as $pl) {
					include $pl;
				}
				/* ===== */

				$sql_page = \Cot::$db->query('SELECT * FROM ' . \Cot::$db->pages . ' WHERE page_id = ?', $i);
				if ($row = $sql_page->fetch()) {
					$id = $row['page_id'];
					$usr['isadmin_local'] = cot_auth('page', $row['page_cat'], 'A');
					cot_block($usr['isadmin_local']);

					$sql_page = \Cot::$db->update(
                        \Cot::$db->pages,
                        ['page_state' => COT_PAGE_STATE_PUBLISHED],
                        'page_id= ?',
                        $id
                    );

					cot_log(
                        \Cot::$L['Page'] . ' #' . $id . ' - ' . \Cot::$L['adm_queue_validated'],
                        'page',
                        'validate',
                        'done'
                    );

					if (\Cot::$cache && \Cot::$cfg['cache_page']) {
                        \Cot::$cache->static->clearByUri(cot_page_url($row));
                        \Cot::$cache->static->clearByUri(cot_url('page', ['c' => $row['page_cat']]));
					}

					$perelik .= '#' . $id.', ';
				} else {
					$notfoundet .= '#' . $id . ' - ' . \Cot::$L['Error'] . '<br  />';
				}
			}
		}

        if (\Cot::$cache) {
            Cot::$cache->db->remove('structure', 'system');
            if (Cot::$cfg['cache_index']) {
                Cot::$cache->static->clear('index');
            }
        }

        if (!empty($notfoundet)) {
            cot_error($notfoundet);
        }

		if (!empty($perelik)) {
			cot_message($perelik . ' - ' . Cot::$L['adm_queue_validated']);
		}

        cot_redirect(cot_url('admin', $urlParams, '', true));

	} elseif ($paction == 'delete' && is_array($s)) {
		cot_check_xp();

		$perelik = '';
		$notfoundet = '';
		foreach ($s as $i => $k) {
			if ($s[$i] == '1' || $s[$i] == 'on') {
				/* === Hook  === */
				foreach (cot_getextplugins('page.admin.checked_delete') as $pl) {
					include $pl;
				}
				/* ===== */

                $result = cot_page_delete($id);
                if ($result) {
                    /* === Hook === */
                    foreach (cot_getextplugins('page.admin.delete.done') as $pl) {
                        include $pl;
                    }
                    /* ===== */
                    $perelik .= '#' . $id . ', ';

                } else {
                    $notfoundet .= '#'. $id . ' - ' . Cot::$L['Error'].'<br  />';
                }
			}
		}

        if (!empty($notfoundet)) {
            cot_error($notfoundet);
        }

        if (!empty($perelik)) {
            cot_message($perelik . ' - ' . Cot::$L['adm_queue_deleted']);
        }

        cot_redirect(cot_url('admin', $urlParams, '', true));
	}
}

$totalitems = $db->query("SELECT COUNT(*) FROM $db_pages WHERE ".$sqlwhere)->fetchColumn();
$pagenav = cot_pagenav('admin', $common_params, $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql_page = $db->query("SELECT p.*, u.user_name
	FROM $db_pages as p
	LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
	WHERE $sqlwhere
		ORDER BY $sqlsorttype $sqlsortway
		LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('page.admin.loop');
/* ===== */
foreach ($sql_page->fetchAll() as $row) {
    $sub_count = 0;
    if (isset(Cot::$structure['page'][$row["page_cat"]])) {
        $sql_page_subcount = Cot::$db->query("SELECT SUM(structure_count) FROM $db_structure WHERE structure_path LIKE '" .
                Cot::$db->prep(Cot::$structure['page'][$row["page_cat"]]['rpath']) . "%' ");
        $sub_count = $sql_page_subcount->fetchColumn();
    }
	$row['page_file'] = intval($row['page_file']);
	$t->assign(cot_generate_pagetags($row, 'ADMIN_PAGE_', 200));
	$t->assign(array(
		'ADMIN_PAGE_ID_URL' => cot_url('page', 'c='.$row['page_cat'].'&id='.$row['page_id']),
		'ADMIN_PAGE_OWNER' => cot_build_user($row['page_ownerid'], $row['user_name']),
		'ADMIN_PAGE_FILE_BOOL' => $row['page_file'],
		'ADMIN_PAGE_URL_FOR_VALIDATED' => cot_confirm_url(cot_url('admin', $common_params.'&a=validate&id='.$row['page_id'].'&d='.$durl.'&'.cot_xg()), 'page', 'page_confirm_validate'),
		'ADMIN_PAGE_URL_FOR_UNVALIDATE' => cot_confirm_url(cot_url('admin', $common_params.'&a=unvalidate&id='.$row['page_id'].'&d='.$durl.'&'.cot_xg()), 'page', 'page_confirm_unvalidate'),
		'ADMIN_PAGE_URL_FOR_DELETED' => cot_confirm_url(cot_url('admin', $common_params.'&a=delete&id='.$row['page_id'].'&d='.$durl.'&'.cot_xg()), 'page', 'page_confirm_delete'),
		'ADMIN_PAGE_URL_FOR_EDIT' => cot_url('page', 'm=edit&id='.$row['page_id']),
		'ADMIN_PAGE_ODDEVEN' => cot_build_oddeven($ii),
		'ADMIN_PAGE_CAT_COUNT' => $sub_count
	));
	$t->assign(cot_generate_usertags($row['page_ownerid'], 'ADMIN_PAGE_OWNER_'), htmlspecialchars($row['user_name']));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl) {
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.PAGE_ROW');
	$ii++;
}

$is_row_empty = ($sql_page->rowCount() == 0) ? true : false ;

$totaldbpages = Cot::$db->countRows($db_pages);
$sql_page_queued = Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->pages . ' WHERE page_state=' .
    COT_PAGE_STATE_PENDING);
$sys['pagesqueued'] = $sql_page_queued->fetchColumn();

$t->assign(array(
	'ADMIN_PAGE_URL_CONFIG' => cot_url('admin', 'm=config&n=edit&o=module&p=page'),
	'ADMIN_PAGE_URL_ADD' => cot_url('page', 'm=add'),
	'ADMIN_PAGE_URL_EXTRAFIELDS' => cot_url('admin', 'm=extrafields&n='.$db_pages),
	'ADMIN_PAGE_URL_STRUCTURE' => cot_url('admin', 'm=structure&n=page'),
	'ADMIN_PAGE_FORM_URL' => cot_url('admin', $common_params.'&a=update_checked&d='.$durl),
	'ADMIN_PAGE_ORDER' => cot_selectbox($sorttype, 'sorttype', array_keys($sort_type), array_values($sort_type), false),
	'ADMIN_PAGE_WAY' => cot_selectbox($sortway, 'sortway', array_keys($sort_way), array_values($sort_way), false),
	'ADMIN_PAGE_FILTER' => cot_selectbox($filter, 'filter', array_keys($filter_type), array_values($filter_type), false),
	'ADMIN_PAGE_TOTALDBPAGES' => $totaldbpages,
	'ADMIN_PAGE_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_PAGE_PAGNAV' => $pagenav['main'],
	'ADMIN_PAGE_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_PAGE_TOTALITEMS' => $totalitems,
	'ADMIN_PAGE_ON_PAGE' => $ii
));

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('page.admin.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');
