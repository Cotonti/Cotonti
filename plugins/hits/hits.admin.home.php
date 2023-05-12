<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home.mainpanel
[END_COT_EXT]
==================== */

/**
 * Hits
 *
 * @package Hits
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $Ls;

require_once cot_langfile('hits', 'plug');
require_once cot_incfile('hits', 'plug');

$timeback_interval = isset(Cot::$cfg['plugin']['hits']['timeback']) ? Cot::$cfg['plugin']['hits']['timeback'] : 7;
$timeback_interval_str = cot_declension($timeback_interval, $Ls['Days']);

$tt = new XTemplate(cot_tplfile('hits.admin.home', 'plug', true));
//Show hit stats
// INFO: `disablehitstats` var not actually defined in setup file now, but may be used (had been set) by another extension
if (!isset(Cot::$cfg['plugin']['hits']['disablehitstats']) || !Cot::$cfg['plugin']['hits']['disablehitstats']) {
	$hitsPerDay = [];

    $defaultTimeZone = !empty(Cot::$cfg['defaulttimezone']) ? Cot::$cfg['defaulttimezone'] : 'UTC';
    $timeZone = new \DateTimeZone($defaultTimeZone);
    $startDate = new \DateTimeImmutable('-' . $timeback_interval . ' days', $timeZone);
    $start = $startDate->format('Y-m-d');

	$sql = Cot::$db->query(
        'SELECT * FROM ' . Cot::$db->stats . " WHERE stat_name LIKE '20%' AND stat_name >= '{$start}' " .
        'ORDER BY stat_name DESC LIMIT ' . $timeback_interval
    );
	while ($row = $sql->fetch()) {
        $hitsPerDay[$row['stat_name']] = $row['stat_value'];
	}
	$sql->closeCursor();

    $hits_d_max = !empty($hitsPerDay) ? max($hitsPerDay) : 0;
    $date = new \DateTime('now', $timeZone);
    for ($i = 0; $i < $timeback_interval; $i++) {
        $dateString = $date->format('Y-m-d');
        $hits = isset($hitsPerDay[$dateString]) ? (int) $hitsPerDay[$dateString] : 0;
        $percentbar = $hits_d_max > 0 ? floor(($hits / $hits_d_max) * 100) : 0;
        $tt->assign(array(
            'ADMIN_HOME_DAY' => cot_date('d D', $date->getTimestamp(), false),
            'ADMIN_HOME_HITS' => $hits,
            'ADMIN_HOME_PERCENTBAR' => $percentbar
        ));
        $tt->parse('MAIN.STAT.ADMIN_HOME_ROW');

        $date->modify('-1 day');
    }

    $tt->assign([
        'ADMIN_HOME_MORE_HITS_URL' => cot_url('admin', ['m' => 'other', 'p' => 'hits']),
        'HITS_STAT_HEADER' => cot_rc(Cot::$L['hits_hits'], ['days' => $timeback_interval_str]),
    ]);
	$tt->parse('MAIN.STAT');
}

//Show activity stats
if (!Cot::$cfg['plugin']['hits']['disableactivitystats']) {
	$timeback = Cot::$sys['now'] - ($timeback_interval * 86400);

	$newpages = 0;
	if (cot_module_active('page')) {
		require_once cot_incfile('page', 'module');
		$sql = Cot::$db->query("SELECT COUNT(*) FROM $db_users WHERE user_regdate > $timeback");
		$newusers = $sql->fetchColumn();

		$sql = Cot::$db->query("SELECT COUNT(*) FROM $db_pages WHERE page_date > $timeback");
		$newpages = $sql->fetchColumn();
	}

	$newtopics = 0;
	$newposts = 0;
	if (cot_module_active('forums')) {
		require_once cot_incfile('forums', 'module');

		$sql = Cot::$db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_creationdate > $timeback");
		$newtopics = $sql->fetchColumn();

		$sql = Cot::$db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_updated > $timeback");
		$newposts = $sql->fetchColumn();
	}

	$newcomments = 0;
	if (function_exists('cot_get_newcomments')) {
		$newcomments = cot_get_newcomments($timeback);
	}

	$newpms = 0;
	if (cot_module_active('pm')) {
		require_once cot_incfile('pm', 'module');
		$sql = Cot::$db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_date > $timeback");
		$newpms = $sql->fetchColumn();
	}

	$tt->assign(array(
		'ADMIN_HOME_NEWUSERS_URL' => cot_url('users', 'f=all&s=regdate&w=desc'),
		'ADMIN_HOME_NEWUSERS' => $newusers,
		'ADMIN_HOME_NEWPAGES_URL' => cot_url('admin', 'm=page'),
		'ADMIN_HOME_NEWPAGES' => $newpages,
		'ADMIN_HOME_NEWTOPICS_URL' => cot_url('forums'),
		'ADMIN_HOME_NEWTOPICS' => $newtopics,
		'ADMIN_HOME_NEWPOSTS_URL' => cot_url('forums'),
		'ADMIN_HOME_NEWPOSTS' => $newposts,
		'ADMIN_HOME_NEWCOMMENTS_URL' => cot_url('admin', 'm=comments'),
		'ADMIN_HOME_NEWCOMMENTS' => $newcomments,
		'ADMIN_HOME_NEWPMS' => $newpms
	));
	$tt->assign('ACTIVITY_STAT_HEADER', cot_rc(Cot::$L['hits_activity'], "days=$timeback_interval_str") );
	$tt->parse('MAIN.ACTIVITY');
}

$tt->parse('MAIN');

$line = $tt->text('MAIN');
