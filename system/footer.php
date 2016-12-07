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

if (!COT_AJAX)
{
	$mtpl_type = defined('COT_ADMIN') || defined('COT_MESSAGE') && $_SESSION['s_run_admin'] && cot_auth('admin', 'any', 'R') ? 'core' : 'module';
	if (cot::$cfg['enablecustomhf'])
	{
		$mtpl_base = (defined('COT_PLUG') && !empty($e)) ? array('footer', $e) : array('footer', cot::$env['location']);
	}
	else
	{
		$mtpl_base = 'footer';
	}
	$t = new XTemplate(cot_tplfile($mtpl_base, $mtpl_type));

    /* === Hook === */
    foreach (cot_getextplugins('footer.main') as $pl)
    {
        include $pl;
    }
    /* ===== */

	$t->assign(array(
		'FOOTER_COPYRIGHT'  => cot::$out['copyright'],
		'FOOTER_LOGSTATUS'  => cot::$out['logstatus'],
		'FOOTER_PMREMINDER' => cot::$out['pmreminder'],
		'FOOTER_ADMINPANEL' => cot::$out['adminpanel']
	));

	/* === Hook === */
	foreach (cot_getextplugins('footer.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	// Attach rich text editors if any
	if ($cot_textarea_count > 0)
	{
		if (is_array($cot_plugins['editor']))
		{
			$parser = !empty($sys['parser']) ? $sys['parser'] : cot::$cfg['parser'];
			$editor = cot::$cfg['plugin'][$parser]['editor'];
			foreach ($cot_plugins['editor'] as $k)
			{
				if ($k['pl_code'] == $editor && cot_auth('plug', $k['pl_code'], 'R'))
				{
					include cot::$cfg['plugins_dir'] . '/' . $k['pl_file'];
					break;
				}
			}
		}
	}

    cot::$out['footer_rc'] .= Resources::renderFooter();

	$t->assign('FOOTER_RC', cot::$out['footer_rc']);

	if (cot::$usr['id'] > 0)
	{
		$t->parse('FOOTER.USER');
	}
	else
	{
		$t->parse('FOOTER.GUEST');
	}

	if (cot::$cfg['debug_mode'])
	{
		$cot_hooks_fired[] = 'footer.last';
		$cot_hooks_fired[] = 'output';
        cot::$out['hooks'] = '<ol>';
		foreach ($cot_hooks_fired as $hook)
		{
            cot::$out['hooks'] .= '<li>'.$hook.'</li>';
		}
        cot::$out['hooks'] .= '</ol>';
		$t->assign('FOOTER_HOOKS', cot::$out['hooks']);
	}

	// Creation time statistics
	$i = explode(' ', microtime());
    cot::$sys['endtime'] = $i[1] + $i[0];
    cot::$sys['creationtime'] = round((cot::$sys['endtime'] - cot::$sys['starttime']), 3);

	$out['creationtime'] = (!cot::$cfg['disablesysinfos']) ? cot::$L['foo_created'].' '.cot_declension(cot::$sys['creationtime'],
            $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
	$out['sqlstatistics'] = (cot::$cfg['showsqlstats']) ? cot::$L['foo_sqltotal'].': '.cot_declension(round(cot::$db->timeCount, 3),
            $Ls['Seconds'], $onlyword = false, $canfrac = true).' - '.cot::$L['foo_sqlqueries'].': '.cot::$db->count.
            ' - '.cot::$L['foo_sqlaverage'].': '.cot_declension(round((cot::$db->timeCount / cot::$db->count), 5),
            $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
	$out['bottomline'] = cot::$cfg['bottomline'];
	$out['bottomline'] .= (cot::$cfg['keepcrbottom']) ? $out['copyright'] : '';

	// Development mode SQL query timings
	if (cot::$cfg['devmode'] && cot_auth('admin', 'a', 'A'))
	{
        cot::$out['devmode'] = "<h4>Dev-mode :</h4><table><tr><td><em>SQL query</em></td><td><em>Duration</em></td><td><em>Timeline</em></td><td><em>Execution stack<br />(file[line]: function)</em></td><td><em>Query</em></td></tr>";
        cot::$out['devmode'] .= "<tr><td colspan=\"2\">BEGIN</td>";
        cot::$out['devmode'] .= "<td style=\"text-align:right;\">0.000 ms</td><td>&nbsp;</td></tr>";
		if(is_array(cot::$sys['devmode']['queries']))
		{
			foreach (cot::$sys['devmode']['queries'] as $k => $i)
			{
                cot::$out['devmode'] .= "<tr><td>#".$i[0]." &nbsp;</td>";
                cot::$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f", round($i[1] * 1000, 3))." ms</td>";
                cot::$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f",
                        round(cot::$sys['devmode']['timeline'][$k] * 1000, 3))." ms</td>";
                cot::$out['devmode'] .= "<td style=\"text-align:left;\">".nl2br(htmlspecialchars($i[3]))."</td>";
                cot::$out['devmode'] .= "<td style=\"text-align:left;\">".htmlspecialchars($i[2])."</td></tr>";
			}
		}
        cot::$out['devmode'] .= "<tr><td colspan=\"2\">END</td>";
        cot::$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f", cot::$sys['creationtime']).
            " ms</td><td>&nbsp;</td></tr>";
        cot::$out['devmode'] .= "</table><br />Total:".round(cot::$db->timeCount, 4)."s - Queries:".cot::$db->count.
            " - Average:".round((cot::$db->timeCount / cot::$db->count), 5)."s/q";
	}

	$t->assign(array(
		'FOOTER_BOTTOMLINE' => cot::$out['bottomline'],
		'FOOTER_CREATIONTIME' => cot::$out['creationtime'],
		'FOOTER_SQLSTATISTICS' => cot::$out['sqlstatistics'],
		'FOOTER_DEVMODE' => cot::$out['devmode']
	));

	$t->parse('FOOTER');
	$t->out('FOOTER');
}

/* === Hook === */
foreach (cot_getextplugins('footer.last') as $pl)
{
	include $pl;
}
/* ===== */
