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

$t = new XTemplate(sed_skinfile('admin.tools'));

$adminpath[] = array(sed_url('admin', 'm=tools'), $L['Tools']);
$adminhelp = $L['adm_help_tools'];

$p = sed_import('p', 'G', 'ALP');

/* === Hook === */
foreach (sed_getextplugins('admin.tools.first') as $pl)
{
	include $pl;
}
/* ===== */

if(!empty($p))
{
	if (file_exists(sed_langfile($p, 'plug')))
	{
		sed_require_lang($p, 'plug');
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
		sed_redirect(sed_url('message', 'msg=907', '', true));
	}

	$extplugin_info = $cfg['plugins_dir'].'/'.$p.'/'.$p.'.setup.php';

	if(file_exists($extplugin_info))
	{
		$info = sed_infoget($extplugin_info, 'COT_EXT');
	}
	else
	{
		sed_redirect(sed_url('message', 'msg=907', '', true));
	}

	$adminpath[] = array(sed_url('admin', 'm=tools&p='.$p), htmlspecialchars($info['Name']));
	// $adminhelp = $L['Description'].' : '.$info['Description'].'<br />'.$L['Version'].' : '.$info['Version'].'<br />'.$L['Date'].' : '.$info['Date'].'<br />'.$L['Author'].' : '.$info['Author'].'<br />'.$L['Copyright'].' : '.$info['Copyright'].'<br />'.$L['Notes'].' : '.$info['Notes'];

	if(is_array($extp))
	{
		foreach($extp as $k => $pl)
		{
			include_once $pl['pl_file'];
			$adminmain .= $plugin_body;
		}
	}

}
else
{
	$target = array();

	function cot_admin_tools_cmp($pl_a, $pl_b)
	{
		if($pl_a['pl_code'] == $pl_b['pl_code'])
		{
			return 0;
		}
		return ($pl_a['pl_code'] < $pl_b['pl_code']) ? -1 : 1;
	}

	foreach (array('module', 'plug') as $type)
	{
		if ($type == 'module')
		{
			$target = $sed_plugins['admin'];
			$dir = $cfg['modules_dir'];
			$title = $L['Modules'];
		}
		else
		{
			$target = $sed_plugins['tools'];
			$dir = $cfg['plugins_dir'];
			$title = $L['Plugins'];
		}
		if (is_array($target))
		{
			usort($target, 'cot_admin_tools_cmp');
			foreach ($target as $pl)
			{
				$extplugin_info = $dir .'/' . $pl['pl_code'] .'/' . $pl['pl_code'] . '.setup.php';

				if(file_exists($extplugin_info))
				{
					$info = sed_infoget($extplugin_info, 'COT_EXT');
				}
				else
				{
					include_once sed_langfile('message', 'core');
					$info['Name'] = $pl['pl_code'] . ' : '. $L['msg907_1'];
				}

				$t->assign(array(
					'ADMIN_TOOLS_EXT_URL' => $type == 'plug' ? sed_url('admin', 'm=tools&p=' . $pl['pl_code']) :
						sed_url('admin', 'm=' . $pl['pl_code']),
					'ADMIN_TOOLS_EXT_NAME' => $info['Name']
				));
				$t->parse('MAIN.SECTION.ROW');
			}
		}
		else
		{
			$t->parse('MAIN.SECTION.EMPTY');
		}
		$t->assign('ADMIN_TOOLS_SECTION', $title);
		$t->parse('MAIN.SECTION');
	}
	/* === Hook === */
	foreach (sed_getextplugins('admin.tools.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN');
	$adminmain = $t->text('MAIN');
}

?>