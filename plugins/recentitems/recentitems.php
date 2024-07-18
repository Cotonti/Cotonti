<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package RecentItems
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var array $Ls
 */

defined('COT_CODE') or die("Wrong URL.");

$days = cot_import('days', 'G', 'TXT');
$mode = cot_import('mode', 'G', 'TXT');

list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['plugin']['recentitems']['itemsperpage']);

$timeBack = null;
$periodToShow = '';
$pageTitleLimit = isset($pageTitleLimit) ? $pageTitleLimit : 0;  // Todo

$urlParams = [];
if (!empty($days)) {
    $urlParams['days'] = $days;
}
if (!empty($mode)) {
    $urlParams['mode'] = $mode;
}

$recentItemsModes = [];
if (cot_module_active('page')) {
    $recentItemsModes[] = 'pages';
}
if (cot_module_active('forums')) {
    $recentItemsModes[] = 'forums';
}

if (empty($days) || is_numeric($days)) {
    $days = (int) $days;

    // From user's last visit
    if ($days === -1) {
        if (Cot::$usr['id'] > 0 && Cot::$usr['lastvisit'] > 0) {
            $timeBack = Cot::$usr['lastvisit'];
            $periodToShow = Cot::$L['recentitems_fromlastvisit'];
        } else {
            $days = 1;
            $urlParams['days'] = $days;
        }
    }

    if ($days === 0) {
        // Today. From 00:00 in user timezone
        $timeZone = cot_getUserTimeZone();
        $date = new DateTime('today midnight', $timeZone);
        $timeBack = $date->getTimestamp();
        $periodToShow = Cot::$L['Today'];
    } elseif ($days > 0) {
        $timeBack = Cot::$sys['now'] - ($days * 86400);
        $periodToShow = cot_declension($days, $Ls['Days']);
    }

} else {
    if (preg_match('/^(?P<count>\d{1,2})(?P<unit>[YMDW])$/i', $days, $matches)) {
        $matches['unit'] = mb_strtoupper($matches['unit']);

        if ($matches['unit'] === 'D') {
            $redirectUrlParams = $urlParams;
            $redirectUrlParams['days'] = $matches['count'];
            if (!empty($d)) {
                $redirectUrlParams['d'] = $durl;
            }
            cot_redirect(cot_url('recentitems', $redirectUrlParams, '', true));
        }

        try {
            $startDateTime = new DateTime();
            $startDateTime->sub(new DateInterval('P' . $matches['count'] . $matches['unit']));
            $timeBack = $startDateTime->getTimestamp();
        } catch (Exception $e) {
            cot_die_message('404');
        }

        switch ($matches['unit']) {
            case 'W':
                $periodToShow = cot_declension($matches['count'], $Ls['Weeks']);
                break;
            case 'M':
                $periodToShow = cot_declension($matches['count'], $Ls['Months']);
                break;
            case 'Y':
                $periodToShow = cot_declension($matches['count'], $Ls['Years']);
                break;
        }
    } else {
        cot_die_message('404');
    }
}

require_once cot_incfile('recentitems', 'plug');

$totalrecent[] = 0;
if (
    Cot::$cfg['plugin']['recentitems']['newpages']
    && cot_module_active('page')
    && (empty($mode) || $mode === 'pages')
) {
	require_once cot_incfile('page', 'module');
	$res = cot_build_recentpages(
        'recentitems.pages',
        $timeBack,
        Cot::$cfg['plugin']['recentitems']['itemsperpage'],
        $d,
        $pageTitleLimit,
        Cot::$cfg['plugin']['recentitems']['newpagestext'],
        Cot::$cfg['plugin']['recentitems']['rightscan']
    );
	$t->assign('RECENT_PAGES', $res);
}

if (
    Cot::$cfg['plugin']['recentitems']['newforums']
    && cot_module_active('forums')
    && (empty($mode) || $mode == 'forums')
) {
	require_once cot_incfile('forums', 'module');

    $forumtitlelimit = isset($forumtitlelimit) ? $forumtitlelimit : 0;  // Todo

	$res = cot_build_recentforums(
        'recentitems.forums',
        $timeBack,
        Cot::$cfg['plugin']['recentitems']['itemsperpage'],
        $d,
        $forumtitlelimit,
        Cot::$cfg['plugin']['recentitems']['rightscan']
    );
	$t->assign('RECENT_FORUMS', $res);
}

$titleParams = [Cot::$L['recentitems_title']];
if ($mode === 'pages') {
    $titleParams[] = Cot::$L['Pages'];
} elseif ($mode === 'forums') {
    $titleParams[] = Cot::$L['Forums'];
}

if ($mode !== 'pages' || $mode !== 'forums') {
	/* === Hook === */
	foreach (cot_getextplugins('recentitems.tags') as $pl) {
		include $pl;
	}
	/* ===== */
}

if (!empty($mode) && !in_array($mode, $recentItemsModes)) {
    cot_die_message(404);
}

if ($periodToShow !== '') {
    $titleParams[] = $periodToShow;
}

$canonicalUrlParams = $urlParams;
if (!empty($d)) {
    $canonicalUrlParams['d'] = $durl;
}
Cot::$out['canonical_uri'] = cot_url('recentitems', $canonicalUrlParams);
Cot::$out['subtitle'] = implode(' - ', $titleParams);

$totalEntries = max($totalrecent);

$daysUrl = '';
$modeUrl = '';
if (!empty($days) && !empty($timeBack)) {
    $daysUrl = 'days=' . $days;
}
if (!empty($mode)) {
    $modeUrl = 'mode=' . $mode;
}

$pagination = cot_pagenav(
    'recentitems',
    $urlParams,
    $d,
    $totalEntries,
    Cot::$cfg['plugin']['recentitems']['itemsperpage']
);

$t->assign(cot_generatePaginationTags($pagination));

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.24
    $t->assign([
        'PAGE_PAGENAV' => $pagination['main'],
        'PAGE_PAGEPREV' => $pagination['prev'],
        'PAGE_PAGENEXT' => $pagination['next'],
    ]);
}
