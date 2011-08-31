<?php
/**
 * Administration panel - Home page for administrators
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$t = new XTemplate(cot_tplfile('admin.home', 'core'));

if (cot_module_active('page'))
{
	require_once cot_incfile('page', 'module');
	$pagesqueued = $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_state='1'");
	$pagesqueued = $pagesqueued->fetchColumn();
	$t->assign(array(
		'ADMIN_HOME_URL' => cot_url('admin', 'm=page'),
		'ADMIN_HOME_PAGESQUEUED' => $pagesqueued
	));
}

if (!function_exists('gd_info') && $cfg['th_amode'] != 'Disabled')
{
	$is_adminwarnings = true;
}

//Version Checking
if ($cfg['check_updates'] && $cache)
{
	$update_info = $cache->db->get('update_info');
	if (!$update_info)
	{
		if (ini_get('allow_url_fopen'))
		{
			$update_info = @file_get_contents('http://www.cotonti.com/update-check');
			if ($update_info)
			{
				$update_info = json_decode($update_info, TRUE);
				$cache->db->store('update_info', $update_info, COT_DEFAULT_REALM, 86400);
			}
		}
		elseif (function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'http://www.cotonti.com/update-check');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			$update_info = curl_exec($curl);
			if ($update_info)
			{
				$update_info = json_decode($update_info, TRUE);
				$cache->db->store('update_info', $update_info, COT_DEFAULT_REALM, 86400);
			}
			curl_close($curl);
		}
	}
	if ($update_info['update_ver'] > $cfg['version'])
	{
		$t->assign(array(
			'ADMIN_HOME_UPDATE_REVISION' => sprintf($L['home_update_revision'], $cfg['version'], htmlspecialchars($update_info['update_ver'])),
			'ADMIN_HOME_UPDATE_MESSAGE' => cot_parse($update_info['update_message']),
		));
		$t->parse('MAIN.UPDATE');
	}
}

$t->assign(array(
	'ADMIN_HOME_VERSION' => $cfg['version'],
	'ADMIN_HOME_DB_VERSION' => htmlspecialchars($db->query("SELECT upd_value FROM $db_updates WHERE upd_param = 'revision'")->fetchColumn())
));

/* === Hook === */
foreach (cot_getextplugins('admin.home', 'R') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

?>