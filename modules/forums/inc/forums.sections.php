<?php

/**
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('forums', 'any');
/* === Hook === */
foreach (cot_getextplugins('forums.sections.rights') as $pl) {
	include $pl;
}
/* ===== */
cot_block(Cot::$usr['auth_read']);

$s = cot_import('s','G','TXT');
$c = cot_import('c','G','TXT');

Cot::$sys['sublocation'] = Cot::$L['Home'];

/* === Hook === */
foreach (cot_getextplugins('forums.sections.first') as $pl) {
	include $pl;
}
/* ===== */

if ($n == 'markall' && Cot::$usr['id'] > 0) {
	Cot::$db->update($db_users, array('user_lastvisit' => Cot::$sys['now']), "user_id=".Cot::$usr['id']);
	Cot::$usr['lastvisit'] = Cot::$sys['now'];
}

if (empty($cot_sections_act)) {
    $cot_sections_act = [];
	$timeback = Cot::$sys['now'] - 604800; // 7 days
	$sqltmp = Cot::$db->query(
        'SELECT fp_cat, COUNT(*) FROM ' . Cot::$db->forum_posts . " WHERE fp_creation > $timeback GROUP BY fp_cat"
    );
	while ($tmprow = $sqltmp->fetch()) {
		$cot_sections_act[$tmprow['fp_cat']] = $tmprow['COUNT(*)'];
	}
	$sqltmp->closeCursor();
    if (!empty(Cot::$cache)) {
        // Two hours. Because when cot_forum_stats.fs_viewcount is updated, the cache is not reset.
        // Otherwise, caching makes no sense
        Cot::$cache->db->store('cot_sections_act', $cot_sections_act, 'system', 7200);
    }
}

$cat_top = [];
$sqlForums = Cot::$db->query('SELECT * FROM ' . Cot::$db->forum_stats . ' ORDER by fs_cat DESC');
while ($row = $sqlForums->fetch()) {
	if (
        !empty($row['fs_cat'])
        && !$row['fs_lt_id']
        && count(explode('.', Cot::$structure['forums'][$row['fs_cat']]['rpath'])) > 1
        && Cot::$structure['forums'][$row['fs_cat']]['count'] > 0
    ) {
        cot_forums_updateStructureCounters($row['fs_cat']);
	}
	$cat_top[$row['fs_cat']] = $row;
	$cat_top[$row['fs_cat']]['topiccount'] = $cat_top[$row['fs_cat']]['fs_topiccount'];
	$cat_top[$row['fs_cat']]['postcount'] = $cat_top[$row['fs_cat']]['fs_postcount'];
    $cat_top[$row['fs_cat']]['viewcount'] = $cat_top[$row['fs_cat']]['fs_viewcount'];
}
$sqlForums->closeCursor();

$fstlvl = $nxtlvl = $cot_act = [];
foreach (Cot::$structure['forums'] as $i => $x) {
	$parents = explode('.', $x['path']);
	$depth = count($parents);
    if (!isset($cot_act[$parents[0]])) {
        $cot_act[$parents[0]] = 0;
    }
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

Cot::$out['subtitle'] = Cot::$L['Forums'];

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate(cot_tplfile('forums.sections'));

/* === Hook === */
foreach (cot_getextplugins('forums.sections.main') as $pl) {
	include $pl;
}
/* ===== */

$url_markall = cot_url('forums', "n=markall");
$title[] = array(cot_url('forums'), Cot::$L['Forums']);
$t->assign(array(
	'FORUMS_RSS' => cot_url('rss', 'm=forums'),
	'FORUMS_SECTIONS_PAGETITLE' => cot_breadcrumbs($title, Cot::$cfg['homebreadcrumb']),
	'FORUMS_SECTIONS_MARKALL' =>  (Cot::$usr['id'] > 0) ? cot_rc_link($url_markall, Cot::$L['forums_markallasread']) : '',
	'FORUMS_SECTIONS_MARKALL_URL' => (Cot::$usr['id'] > 0) ? $url_markall : ''
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
			if (isset($nxtlvl[$y]) && is_array($nxtlvl[$y]) && Cot::$cfg['forums']['cat_' . $y]['defstate']) {
				$zz = 0;
				foreach ($nxtlvl[$y] as $z) {
					$zz++;
                    $t->assign(cot_generate_sectiontags(
                        $z,
                        'FORUMS_SECTIONS_ROW_',
                        isset($cat_top[$z]) ? $cat_top[$z] : null)
                    );
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
				'FORUMS_SECTIONS_ROW_SUBITEMS' => (isset($nxtlvl[$y]) && Cot::$cfg['forums']['cat_' . $y]['defstate']) ? 1 : 0,
				'FORUMS_SECTIONS_ROW_ACTIVITY' => cot_rc('forums_icon_section_activity', array('secact_num'=>$secact_num)),
				'FORUMS_SECTIONS_ROW_ACTIVITYVALUE' => $secact_num,
				'FORUMS_SECTIONS_ROW_ODDEVEN' => cot_build_oddeven($yy),
				'FORUMS_SECTIONS_ROW_NUM' => $yy,
			));
			/* === Hook - Part2 : Include === */
			foreach ($extps as $pl) {
				include $pl;
			}
			/* ===== */
			$t->parse('MAIN.FORUMS_SECTIONS.CAT.SECTION');
		}
	}
	$xx++;

	$fold = !Cot::$cfg['forums']['cat_' . $x]['defstate'];
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

require_once Cot::$cfg['system_dir'] . '/footer.php';

if (Cot::$cache && Cot::$usr['id'] === 0 && Cot::$cfg['cache_forums']) {
	Cot::$cache->static->write();
}
