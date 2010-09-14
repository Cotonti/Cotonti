<?php
/**
 * Administration panel - Other Admin parts listing
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['auth_read']);

$t = new XTemplate(cot_skinfile('admin.other'));

$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);

$p = cot_import('p', 'G', 'ALP');

/* === Hook === */
foreach (cot_getextplugins('admin.other.first') as $pl)
{
	include $pl;
}
/* ===== */

if(!empty($p))
{
	if (file_exists(cot_langfile($p, 'plug')))
	{
		cot_require_lang($p, 'plug');
	}

	$extp = array();

	if(is_array($cot_plugins['tools']))
	{
		foreach($cot_plugins['tools'] as $k)
		{
			if($k['pl_code'] == $p)
			{
				$extp[] = $k;
			}
		}
	}

	if(count($extp) == 0)
	{
		cot_redirect(cot_url('message', 'msg=907', '', true));
	}

	$extplugin_info = $cfg['plugins_dir'].'/'.$p.'/'.$p.'.setup.php';

	if(file_exists($extplugin_info))
	{
		$info = cot_infoget($extplugin_info, 'COT_EXT');
	}
	else
	{
		cot_redirect(cot_url('message', 'msg=907', '', true));
	}

	$adminpath[] = array(cot_url('admin', 'm=other&p='.$p), htmlspecialchars($info['Name']));
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

	function cot_admin_other_cmp($pl_a, $pl_b)
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
			$target = $cot_plugins['admin'];
			$dir = $cfg['modules_dir'];
			$title = $L['Modules'];
		}
		else
		{
			$target = $cot_plugins['tools'];
			$dir = $cfg['plugins_dir'];
			$title = $L['Plugins'];
		}
		if (is_array($target))
		{
			usort($target, 'cot_admin_other_cmp');
			foreach ($target as $pl)
			{
				$extplugin_info = $dir .'/' . $pl['pl_code'] .'/' . $pl['pl_code'] . '.setup.php';

				if(file_exists($extplugin_info))
				{
					$info = cot_infoget($extplugin_info, 'COT_EXT');
				}
				else
				{
					include_once cot_langfile('message', 'core');
					$info['Name'] = $pl['pl_code'] . ' : '. $L['msg907_1'];
				}

				$t->assign(array(
					'ADMIN_OTHER_EXT_URL' => $type == 'plug' ? cot_url('admin', 'm=other&p=' . $pl['pl_code']) :
						cot_url('admin', 'm=' . $pl['pl_code']),
					'ADMIN_OTHER_EXT_NAME' => $info['Name']
				));
				$t->parse('MAIN.SECTION.ROW');
			}
		}
		else
		{
			$t->parse('MAIN.SECTION.EMPTY');
		}
		$t->assign('ADMIN_OTHER_SECTION', $title);
		$t->parse('MAIN.SECTION');
	}

	$t->assign(array(
		'ADMIN_OTHER_URL_CACHE' => cot_url('admin', 'm=cache'),
		'ADMIN_OTHER_URL_DISKCACHE' => cot_url('admin', 'm=cache&s=disk'),
		'ADMIN_OTHER_URL_BBCODE' => cot_url('admin', 'm=bbcode'),
		'ADMIN_OTHER_URL_URLS' => cot_url('admin', 'm=urls'),
		'ADMIN_OTHER_URL_BANLIST' => cot_url('admin', 'm=banlist'),
		'ADMIN_OTHER_URL_HITS' => cot_url('admin', 'm=hits'),
		'ADMIN_OTHER_URL_REFERS' => cot_url('admin', 'm=referers'),
		'ADMIN_OTHER_URL_LOG' => cot_url('admin', 'm=log'),
		'ADMIN_OTHER_URL_INFOS' => cot_url('admin', 'm=infos'),
		'ADMIN_OTHER_URL_RATINGS' => cot_url('admin', 'm=ratings')
	));

	/* === Hook === */
	foreach (cot_getextplugins('admin.other.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN');
	$adminmain = $t->text('MAIN');
}

if (COT_AJAX)
{
	echo $adminmain;
}

?>