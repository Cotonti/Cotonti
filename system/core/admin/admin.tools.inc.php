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

$t = new XTemplate(sed_skinfile('admin.tools.inc', false, true));

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

	if(is_array($sed_plugins))
	{
		foreach($sed_plugins as $i => $k)
		{
			if($k['pl_hook'] == 'tools' && $k['pl_code'] == $p)
			{
				$extp[$i] = $k;
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

	function cmp($a, $b, $k = 1)
	{
		if($a[$k] == $b[$k])
		{
			return 0;
		}
		return($a[$k] < $b[$k]) ? -1 : 1;
	}

	/* === Hook === */
	$extp = sed_getextplugins('tools');

	$list_present = is_array($extp);
	if($list_present)
	{
		foreach($extp as $k => $pl)
		{
			$plugins[] = array($pl['pl_code'], $pl['pl_title']);
		}

		usort($plugins, "cmp");

		while(list($i, $x) = each($plugins))
		{
			$extplugin_info = $cfg['plugins_dir']."/".$x[0]."/".$x[0].".setup.php";

			if(file_exists($extplugin_info))
			{
				$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');
			}
			else
			{
				include_once($cfg['system_dir'].'/lang/en/message.lang.php');
				if($lang!='en')
				{
					include_once($cfg['system_dir'].'/lang/$lang/message.lang.php');
				}
				$info['Name'] = $x[0]." : ".$L['msg907_1'];
			}

			$plugin_icon = (empty($x[1])) ? 'plugins' : $x[1];

			$t -> assign(array(
				"ADMIN_TOOLS_PLUG_URL" => sed_url('admin', "m=tools&p=".$x[0]),
				"ADMIN_TOOLS_PLUG_NAME" => $info['Name']
			));
			$t -> parse("TOOLS.ROW");
		}
	}
	/* === Hook === */
	$extp = sed_getextplugins('admin.tools.tags');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t -> parse("TOOLS");
	$adminmain = $t -> text("TOOLS");
}

?>