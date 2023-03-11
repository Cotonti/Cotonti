<?php

/**
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('forums', 'any');
/* === Hook === */
foreach (cot_getextplugins('forums.sections.rights') as $pl) {
	include $pl;
}
/* ===== */
cot_block(cot::$usr['auth_read']);

$s = cot_import('s','G','TXT');
$c = cot_import('c','G','TXT');

cot::$sys['sublocation'] = cot::$L['Home'];

/* === Hook === */
foreach (cot_getextplugins('forums.sections.first') as $pl) {
	include $pl;
}
/* ===== */

if ($n == 'markall' && cot::$usr['id'] > 0) {
	cot::$db->update($db_users, array('user_lastvisit' => cot::$sys['now']), "user_id=".cot::$usr['id']);
	cot::$usr['lastvisit'] = cot::$sys['now'];
}

if (empty($cot_sections_act)) {
    $cot_sections_act = array();
	$timeback = cot::$sys['now'] - 604800; // 7 days
	$sqltmp = cot::$db->query("SELECT fp_cat, COUNT(*) FROM $db_forum_posts WHERE fp_creation > $timeback GROUP BY fp_cat");
	while ($tmprow = $sqltmp->fetch()) {
		$cot_sections_act[$tmprow['fp_cat']] = $tmprow['COUNT(*)'];
	}
	$sqltmp->closeCursor();
	cot::$cache && cot::$cache->db->store('cot_sections_act', $cot_sections_act, 'system', 7200);
}

$cat_top = array();
$sql_forums = cot::$db->query("SELECT * FROM $db_forum_stats ORDER by fs_cat DESC");
foreach ($sql_forums->fetchAll() as $row) {
	if (
        !empty($row['fs_cat']) &&
        !$row['fs_lt_id'] &&
        count(explode('.', cot::$structure['forums'][$row['fs_cat']]['rpath'])) > 1 &&
        cot::$structure['forums'][$row['fs_cat']]['count'] > 0
    ) {
		cot_forums_sectionsetlast($row['fs_cat']);
	}
	$cat_top[$row['fs_cat']] = $row;
	$cat_top[$row['fs_cat']]['topiccount'] = $cat_top[$row['fs_cat']]['fs_topiccount'];
	$cat_top[$row['fs_cat']]['postcount'] = $cat_top[$row['fs_cat']]['fs_postcount'];
    $cat_top[$row['fs_cat']]['viewcount'] = $cat_top[$row['fs_cat']]['fs_viewcount'];
}

$fstlvl = array();
$nxtlvl = array();
$cot_act = array();
foreach (cot::$structure['forums'] as $i => $x) {
	$parents = explode('.', $x['path']);
	$depth = count($parents);
    $cot_act[$parents[0]] = 0;
	if (cot_auth('forums', $i, 'R')) {
		if ($depth < 2) {
			$fstlvl[$i] = $i;
		} elseif($depth < 4) {
			$nxtlvl[$parents[$depth-2]][$i] = $i;
		}
		$depmax = ($depth < 4) ? ($depth - 1) : 3;
		for ($ii = 0; $ii < $depmax; $ii++) {
            if (isset($cat_top[$i]['fs_lt_date'])) {
                if (!isset($cat_top[$parents[$ii]]['fs_lt_date']) || ($cat_top[$i]['fs_lt_date'] > $cat_top[$parents[$ii]]['fs_lt_date'])) {
                    $cat_top[$parents[$ii]]['fs_lt_id'] = $cat_top[$i]['fs_lt_id'];
                    $cat_top[$parents[$ii]]['fs_lt_title'] = $cat_top[$i]['fs_lt_title'];
                    $cat_top[$parents[$ii]]['fs_lt_date'] = $cat_top[$i]['fs_lt_date'];
                    $cat_top[$parents[$ii]]['fs_lt_posterid'] = $cat_top[$i]['fs_lt_posterid'];
                    $cat_top[$parents[$ii]]['fs_lt_postername'] = $cat_top[$i]['fs_lt_postername'];
                }

                if (!isset($cat_top[$parents[$ii]]['topiccount'])) {
                    $cat_top[$parents[$ii]]['topiccount'] = 0;
                }
                $cat_top[$parents[$ii]]['topiccount'] += $cat_top[$i]['fs_topiccount'];

                if (!isset($cat_top[$parents[$ii]]['postcount'])) {
                    $cat_top[$parents[$ii]]['postcount'] = 0;
                }
                $cat_top[$parents[$ii]]['postcount'] += $cat_top[$i]['fs_postcount'];

                if (!isset($cat_top[$parents[$ii]]['viewcount'])) {
                    $cat_top[$parents[$ii]]['viewcount'] = 0;
                }
                $cat_top[$parents[$ii]]['viewcount'] += $cat_top[$i]['fs_viewcount'];
            }
		}

		if (isset($cot_sections_act[$i])) {
            $cot_act[$parents[0]] += $cot_sections_act[$i];
        }
	}
}

$secact_max = count($cot_act) > 0 ? (max($cot_act)) : 0;

cot::$out['subtitle'] = cot::$L['Forums'];

/* === Hook === */
foreach (cot_getextplugins('forums.sections.main') as $pl) {
	include $pl;
}
/* ===== */

require_once cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate(cot_tplfile('forums.sections'));

$url_markall = cot_url('forums', "n=markall");
$title[] = array(cot_url('forums'), cot::$L['Forums']);
$t->assign(array(
	'FORUMS_RSS' => cot_url('rss', 'm=forums'),
	'FORUMS_SECTIONS_PAGETITLE' => cot_breadcrumbs($title, cot::$cfg['homebreadcrumb']),
	'FORUMS_SECTIONS_MARKALL' =>  (cot::$usr['id'] > 0) ? cot_rc_link($url_markall, cot::$L['forums_markallasread']) : '',
	'FORUMS_SECTIONS_MARKALL_URL' => (cot::$usr['id'] > 0) ? $url_markall : ''
));


$xx = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.sections.loop');
/* ===== */

/* === Hook - Part1 : Set === */
$extps = cot_getextplugins('forums.sections.loop.sections');
/* ===== */

/* === Hook - Part1 : Set === */
$extpss = cot_getextplugins('forums.sections.loop.subsections');
/* ===== */

foreach ($fstlvl as $x) {
	if (isset($nxtlvl[$x]) && is_array($nxtlvl[$x])) {
		$yy = 0;
		foreach ($nxtlvl[$x] as $y) {
			if (isset($nxtlvl[$y]) && is_array($nxtlvl[$y]) && cot::$cfg['forums']['cat_' . $y]['defstate']) {
				$zz = 0;
				foreach ($nxtlvl[$y] as $z) {
					$zz++;
					$t->assign(cot_generate_sectiontags($z, 'FORUMS_SECTIONS_ROW_', $cat_top[$z]));
					$t->assign(array(
						'FORUMS_SECTIONS_ROW_ODDEVEN' => cot_build_oddeven($zz),
						'FORUMS_SECTIONS_ROW_NUM' => $zz,
					));
					/* === Hook - Part2 : Include === */
					foreach ($extpss as $pl) {
						include $pl;
					}
					/* ===== */
					$t->parse('MAIN.FORUMS_SECTIONS.CAT.SECTION.SUBSECTION');
				}
			}
			$yy++;
            $stat = isset($cat_top[$y]) ? $cat_top[$y] : null;
			$t->assign(cot_generate_sectiontags($y, 'FORUMS_SECTIONS_ROW_', $stat));

			$secact_num = 0;
			if ($secact_max) {
				$secact_num = isset($cot_sections_act[$y]) ? round(6.25 * $cot_sections_act[$y] / $secact_max) : 0;
				$secact_num = ($secact_num > 5) ? 5 : $secact_num;
				$secact_num = (!$secact_num && !empty($cot_sections_act[$y]) && $cot_sections_act[$y] > 1) ?
                    1 : $secact_num;
			}

			$t->assign(array(
				'FORUMS_SECTIONS_ROW_SUBITEMS' => (isset($nxtlvl[$y]) && cot::$cfg['forums']['cat_' . $y]['defstate']) ? 1 : 0,
				'FORUMS_SECTIONS_ROW_ACTIVITY' => cot_rc('forums_icon_section_activity', array('secact_num'=>$secact_num)),
				'FORUMS_SECTIONS_ROW_ACTIVITYVALUE' => $secact_num,
				'FORUMS_SECTIONS_ROW_ODDEVEN' => cot_build_oddeven($yy),
				'FORUMS_SECTIONS_ROW_NUM' => $yy,
			));
			/* === Hook - Part2 : Include === */
			foreach ($extps as $pl)
			{
				include $pl;
			}
			/* ===== */
			$t->parse('MAIN.FORUMS_SECTIONS.CAT.SECTION');
		}
	}
	$xx++;

	$fold = !cot::$cfg['forums']['cat_' . $x]['defstate'];
	if ($c) {
		$fold = (int) ($c == 'fold' ? true : ($c == 'unfold' ? false : ($c == $x ? false : true)));
	}

    $stat = isset($cat_top[$x]) ? $cat_top[$x] : null;
	$t->assign(cot_generate_sectiontags($x, 'FORUMS_SECTIONS_ROW_', $stat));
	$t->assign(array(
		'FORUMS_SECTIONS_ROW_FOLD' => $fold,
		'FORUMS_SECTIONS_ROW_SUBITEMS' => (isset($nxtlvl[$x]) && is_array($nxtlvl[$x])) ? 1 : 0,
		'FORUMS_SECTIONS_ROW_ODDEVEN' => cot_build_oddeven($xx),
		'FORUMS_SECTIONS_ROW_NUM' => $xx,
	));
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl) {
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.FORUMS_SECTIONS.CAT');
}
$t->parse('MAIN.FORUMS_SECTIONS');

/* === Hook === */
foreach (cot_getextplugins('forums.sections.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once cot::$cfg['system_dir'] . '/footer.php';

if (cot::$cache && cot::$usr['id'] === 0 && cot::$cfg['cache_forums']) {
	cot::$cache->page->write();
}
