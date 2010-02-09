<?php
/**
 * Administration panel - Tools
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.tools.inc'));

$adminpath[] = array(sed_url('admin', 'm=tools'), $L['Tools']);
$adminhelp = $L['adm_help_tools'];

$p = sed_import('p', 'G', 'ALP');

/* === Hook === */
$extp = sed_getextplugins('admin.tools.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if(!empty($p))
{
	$path_lang_def = $cfg['plugins_dir']."/$p/lang/$p.en.lang.php";
	$path_lang_alt = $cfg['plugins_dir']."/$p/lang/$p.$lang.lang.php";

	if(@file_exists($path_lang_def))
	{
		require_once($path_lang_def);
	}
	if(@file_exists($path_lang_alt) && $lang!='en')
	{
		require_once($path_lang_alt);
	}

	$extp = array();

	if(is_array($sed_plugins['tools']))
	{
		foreach($sed_plugins['tools'] as $k)
		{
			if($k['pl_code'] == $p)
			{
				$extp[] = $k;
			}
		}
	}

	if(count($extp) == 0)
	{
		sed_redirect(sed_url('message', "msg=907", '', true));
	}

	$extplugin_info = $cfg['plugins_dir']."/".$p."/".$p.".setup.php";

	if(file_exists($extplugin_info))
	{
		$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');
	}
	else
	{
		sed_redirect(sed_url('message', "msg=907", '', true));
	}

	$adminpath[] = array(sed_url('admin', "m=tools&p=".$p), htmlspecialchars($info['Name']));
	$adminhelp = $L['Description']." : ".$info['Description']."<br />".$L['Version']." : ".$info['Version']."<br />".$L['Date']." : ".$info['Date']."<br />".$L['Author']." : ".$info['Author']."<br />".$L['Copyright']." : ".$info['Copyright']."<br />".$L['Notes']." : ".$info['Notes'];

	if(is_array($extp))
	{
		foreach($extp as $k => $pl)
		{
			include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
			$adminmain .= $plugin_body;
		}
	}

}
else
{
	$plugins = array();

	function cot_admin_tools_cmp($pl_a, $pl_b)
	{
		if($pl_a['pl_code'] == $pl_b['pl_code'])
		{
			return 0;
		}
		return ($pl_a['pl_code'] < $pl_b['pl_code']) ? -1 : 1;
	}

	/* === Hook === */

	if (is_array($sed_plugins['tools']))
	{
		$list_present = true;
		$plugins = $sed_plugins['tools'];
		usort($plugins, 'cot_admin_tools_cmp');
		foreach ($plugins as $pl)
		{
			$extplugin_info = $cfg['plugins_dir'] .'/' . $pl['pl_code'] .'/' . $pl['pl_code'] . '.setup.php';

			if(file_exists($extplugin_info))
			{
				$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');
			}
			else
			{
				include_once sed_langfile('message', 'module');
				$info['Name'] = $pl['pl_code'] . ' : '. $L['msg907_1'];
			}

			$plugin_icon = (empty($pl['pl_title'])) ? 'plugins' : $pl['pl_title'];

			$t->assign(array(
				"ADMIN_TOOLS_PLUG_URL" => sed_url('admin', 'm=tools&p=' . $pl['pl_code']),
				"ADMIN_TOOLS_PLUG_NAME" => $info['Name']
			));
			$t->parse("TOOLS.ROW");
		}
	}
	/* === Hook === */
	$extp = sed_getextplugins('admin.tools.tags');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse("TOOLS");
	$adminmain = $t->text("TOOLS");
}

?>