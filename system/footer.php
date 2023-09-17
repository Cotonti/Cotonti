<?php
/**
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/* === Hook === */
foreach (cot_getextplugins('footer.first') as $pl)
{
	include $pl;
}
/* ===== */

if (!COT_AJAX) {
	$mtpl_type = (
        defined('COT_ADMIN')
        || (
            defined('COT_MESSAGE')
            && $_SESSION['s_run_admin']
            && cot_auth('admin', 'any', 'R')
        )
    ) ? 'core' : 'module';

    $mtpl_base = 'footer';
	if (Cot::$cfg['enablecustomhf']) {
        if (defined('COT_PLUG') && !empty($e)) {
            $mtpl_base = ['footer', $e];
        } elseif (!empty(Cot::$env['ext'])) {
            $mtpl_base = ['footer', Cot::$env['ext']];
        } elseif (!empty(Cot::$env['location'])) {
            $mtpl_base = ['footer', Cot::$env['location']];
        }
	}

	$t = new XTemplate(cot_tplfile($mtpl_base, $mtpl_type));

    /* === Hook === */
    foreach (cot_getextplugins('footer.main') as $pl) {
        include $pl;
    }
    /* ===== */

	$t->assign(array(
		'FOOTER_COPYRIGHT'  => Cot::$out['copyright'],
		'FOOTER_LOGSTATUS'  => Cot::$out['logstatus'],
		'FOOTER_PMREMINDER' => !empty(Cot::$out['pmreminder']) ? Cot::$out['pmreminder'] : '',
		'FOOTER_ADMINPANEL' => !empty(Cot::$out['adminpanel']) ? Cot::$out['adminpanel'] : ''
	));

	/* === Hook === */
	foreach (cot_getextplugins('footer.tags') as $pl) {
		include $pl;
	}
	/* ===== */

	// Attach rich text editors if any
	if (
        (!empty($cot_textarea_count) || !empty($cot_turnOnEditor))
        && !empty($cot_plugins['editor'])
        && is_array($cot_plugins['editor'])
    ) {
        $parser = !empty(Cot::$sys['parser']) ? Cot::$sys['parser'] : Cot::$cfg['parser'];
        if (!empty(Cot::$cfg['plugin'][$parser]['editor'])) {
            $editor = Cot::$cfg['plugin'][$parser]['editor'];
            foreach ($cot_plugins['editor'] as $k) {
                if ($k['pl_code'] == $editor && cot_auth('plug', $k['pl_code'], 'R')) {
                    $fileName = Cot::$cfg['plugins_dir'] . '/' . $k['pl_file'];
                    if (is_readable($fileName)) {
                        include $fileName;
                        break;
                    }
                }
            }
        }
	}

	if (empty(Cot::$out['footer_rc'])) {
        Cot::$out['footer_rc'] = '';
    }
    Cot::$out['footer_rc'] .= Resources::renderFooter();

	$t->assign('FOOTER_RC', Cot::$out['footer_rc']);

	if (Cot::$usr['id'] > 0) {
		$t->parse('FOOTER.USER');

	} else {
		$t->parse('FOOTER.GUEST');
	}

	if (Cot::$cfg['debug_mode']) {
		$cot_hooks_fired[] = 'footer.last';
		$cot_hooks_fired[] = 'output';
        Cot::$out['hooks'] = '<ol>';
		foreach ($cot_hooks_fired as $hook)
		{
            Cot::$out['hooks'] .= '<li>'.$hook.'</li>';
		}
        Cot::$out['hooks'] .= '</ol>';
		$t->assign('FOOTER_HOOKS', Cot::$out['hooks']);
	}

	// Creation time statistics
	$i = explode(' ', microtime());
    \Cot::$sys['endtime'] = bcadd($i[1], $i[0], 8);
    \Cot::$sys['creationtime'] = bcsub(\Cot::$sys['endtime'], \Cot::$sys['starttime'], 3);

	Cot::$out['creationtime'] = (!Cot::$cfg['disablesysinfos']) ? Cot::$L['foo_created'].' '.cot_declension(Cot::$sys['creationtime'],
            $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
	Cot::$out['sqlstatistics'] = (Cot::$cfg['showsqlstats']) ? Cot::$L['foo_sqltotal'].': '.cot_declension(round(Cot::$db->timeCount, 3),
            $Ls['Seconds'], $onlyword = false, $canfrac = true).' - '.Cot::$L['foo_sqlqueries'].': '.Cot::$db->count.
            ' - '.Cot::$L['foo_sqlaverage'].': '.cot_declension(round((Cot::$db->timeCount / Cot::$db->count), 5),
            $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
	Cot::$out['bottomline'] = Cot::$cfg['bottomline'];
	Cot::$out['bottomline'] .= (Cot::$cfg['keepcrbottom']) ? Cot::$out['copyright'] : '';

	// Development mode SQL query timings
	if (\Cot::$cfg['devmode'] && cot_auth('admin', 'a', 'A')) {
        Cot::$out['devmode'] = "<h4>Dev-mode :</h4><table><tr><td><em>SQL query</em></td><td><em>Duration</em></td><td><em>Timeline</em></td><td><em>Execution stack<br />(file[line]: function)</em></td><td><em>Query</em></td></tr>";
        Cot::$out['devmode'] .= "<tr><td colspan=\"2\">BEGIN</td>";
        Cot::$out['devmode'] .= "<td style=\"text-align:right;\">0.000 ms</td><td>&nbsp;</td></tr>";
        $tdStyle = 'vertical-align: top; padding: 0 10px 10px 0';
		if (is_array(Cot::$sys['devmode']['queries'])) {
			foreach (Cot::$sys['devmode']['queries'] as $k => $i) {
                Cot::$out['devmode'] .= "<tr><td style=\"vertical-align: top\">#".$i[0]." &nbsp;</td>";
                Cot::$out['devmode'] .= "<td style=\"text-align:right; {$tdStyle}\">".sprintf("%.3f", round($i[1] * 1000, 3))." ms</td>";
                Cot::$out['devmode'] .= "<td style=\"text-align:right; {$tdStyle}\">".sprintf("%.3f",
                        round(Cot::$sys['devmode']['timeline'][$k] * 1000, 3))." ms</td>";
                Cot::$out['devmode'] .= "<td style=\"text-align:left; {$tdStyle}\">".nl2br(htmlspecialchars($i[3]))."</td>";
                Cot::$out['devmode'] .= "<td style=\"text-align:left; {$tdStyle}\">".htmlspecialchars($i[2])."</td></tr>";
			}
		}
        Cot::$out['devmode'] .= "<tr><td colspan=\"2\">END</td>";
        Cot::$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f", Cot::$sys['creationtime']).
            " ms</td><td>&nbsp;</td></tr>";
        Cot::$out['devmode'] .= "</table><br />Total:".round(Cot::$db->timeCount, 4)."s - Queries:".Cot::$db->count.
            " - Average:".round((Cot::$db->timeCount / Cot::$db->count), 5)."s/q";
	}

	$t->assign([
		'FOOTER_BOTTOMLINE' => \Cot::$out['bottomline'],
		'FOOTER_CREATIONTIME' => \Cot::$out['creationtime'],
		'FOOTER_SQLSTATISTICS' => \Cot::$out['sqlstatistics'],
		'FOOTER_DEVMODE' => isset(\Cot::$out['devmode']) ? \Cot::$out['devmode'] : ''
	]);

	$t->parse('FOOTER');
	$t->out('FOOTER');
}

/* === Hook === */
foreach (cot_getextplugins('footer.last') as $pl) {
	include $pl;
}
/* ===== */
