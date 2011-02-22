<?php
/**
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/* === Hook === */
foreach (cot_getextplugins('footer.first') as $pl)
{
	include $pl;
}
/* ===== */

$i = explode(' ', microtime());
$sys['endtime'] = $i[1] + $i[0];
$sys['creationtime'] = round(($sys['endtime'] - $sys['starttime']), 3);

$out['creationtime'] = (!$cfg['disablesysinfos']) ? $L['foo_created'].' '.cot_declension($sys['creationtime'], $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
$out['sqlstatistics'] = ($cfg['showsqlstats']) ? $L['foo_sqltotal'].': '.cot_declension(round($db->tcount, 3), $Ls['Seconds'], $onlyword = false, $canfrac = true).' - '.$L['foo_sqlqueries'].': '.$db->count. ' - '.$L['foo_sqlaverage'].': '.cot_declension(round(($db->tcount / $db->count), 5), $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
$out['bottomline'] = $cfg['bottomline'];
$out['bottomline'] .= ($cfg['keepcrbottom']) ? $out['copyright'] : '';

if ($cfg['devmode'] && cot_auth('admin', 'a', 'A'))
{
	$out['devmode'] = "<h4>Dev-mode :</h4><table><tr><td><em>SQL query</em></td><td><em>Duration</em></td><td><em>Timeline</em></td><td><em>Query</em></td></tr>";
	$out['devmode'] .= "<tr><td colspan=\"2\">BEGIN</td>";
	$out['devmode'] .= "<td style=\"text-align:right;\">0.000 ms</td><td>&nbsp;</td></tr>";
	if(is_array($sys['devmode']['queries']))
	{
		foreach ($sys['devmode']['queries'] as $k => $i)
		{
			$out['devmode'] .= "<tr><td>#".$i[0]." &nbsp;</td>";
			$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f", round($i[1] * 1000, 3))." ms</td>";
			$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f", round($sys['devmode']['timeline'][$k] * 1000, 3))." ms</td>";
			$out['devmode'] .= "<td style=\"text-align:left;\">".htmlspecialchars($i[2])."</td></tr>";
		}
	}
	$out['devmode'] .= "<tr><td colspan=\"2\">END</td>";
	$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f", $sys['creationtime'])." ms</td><td>&nbsp;</td></tr>";
	$out['devmode'] .= "</table><br />Total:".round($db->tcount, 4)."s - Queries:".$db->count. " - Average:".round(($db->tcount / $db->count), 5)."s/q";
}

/*
========= DEBUG:START =========
if (is_array($sys['auth_log']))
{
	$out['devauth'] .= "AUTHLOG: ".implode(', ',$sys['auth_log']);
}
$txt_r = ($usr['auth_read']) ? '1' : '0';
$txt_w = ($usr['auth_write']) ? '1' : '0';
$txt_a = ($usr['isadmin']) ? '1' : '0';
$out['devauth'] .= " &nbsp; AUTH_FINAL_RWA:".$txt_r.$txt_w.$txt_a;
$out['devmode']	 .= $out['devauth'];
========= DEBUG:END =========
*/

if (!COT_AJAX)
{
	/* === Hook === */
	foreach (cot_getextplugins('footer.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$mtpl_type = defined('COT_ADMIN') || defined('COT_MESSAGE') && $_SESSION['s_run_admin'] && cot_auth('admin', 'any', 'R') ? 'core' : 'module';
	if ($cfg['enablecustomhf'])
	{
		$mtpl_base = (defined('COT_PLUG') && !empty($e)) ? array('footer', $e) : array('footer', $env['location']);
	}
	else
	{
		$mtpl_base = 'footer';
	}
	$t = new XTemplate(cot_tplfile($mtpl_base, $mtpl_type));

	$t->assign(array(
		'FOOTER_BOTTOMLINE' => $out['bottomline'],
		'FOOTER_CREATIONTIME' => $out['creationtime'],
		'FOOTER_COPYRIGHT' => $out['copyright'],
		'FOOTER_SQLSTATISTICS' => $out['sqlstatistics'],
		'FOOTER_LOGSTATUS' => $out['logstatus'],
		'FOOTER_PMREMINDER' => $out['pmreminder'],
		'FOOTER_ADMINPANEL' => $out['adminpanel'],
		'FOOTER_DEVMODE' => $out['devmode'],
		'FOOTER_JS' => $out['footer_js']
	));

	/* === Hook === */
	foreach (cot_getextplugins('footer.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($usr['id'] > 0)
	{
		$t->parse('FOOTER.USER');
	}
	else
	{
		$t->parse('FOOTER.GUEST');
	}

	$t->parse('FOOTER');
	$t->out('FOOTER');
}

/* === Hook === */
foreach (cot_getextplugins('footer.last') as $pl)
{
	include $pl;
}
/* ===== */

?>