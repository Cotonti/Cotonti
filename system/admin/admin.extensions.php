<?php
/**
 * Extension administration
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('auth');

$t = new XTemplate(cot_tplfile('admin.extensions', 'core'));

$adminpath[] = array (cot_url('admin', 'm=extensions'), $L['Extensions']);

$pl = cot_import('pl', 'G', 'ALP');
$mod = cot_import('mod', 'G', 'ALP');
$part = cot_import('part', 'G', 'ALP');

if (empty($mod))
{
	if (empty($pl))
	{
		if (!empty($a) && $a != 'hooks')
		{
			cot_die();
		}
	}
	else
	{
		$is_module = false;
		$code = $pl;
		$arg = 'pl';
		$dir = $cfg['plugins_dir'];
		$type = 'plug';
	}
}
else
{
	$is_module = true;
	$code = $mod;
	$arg = 'mod';
	$dir = $cfg['modules_dir'];
	$type = 'module';
}

$status[0] = $R['admin_code_paused'];
$status[1] = $R['admin_code_running'];
$status[2] = $R['admin_code_partrunning'];
$status[3] = $R['admin_code_notinstalled'];
$status[4] = $R['admin_code_missing'];
$found_txt[0] = $R['admin_code_missing'];
$found_txt[1] = $R['admin_code_present'];
unset($disp_errors);

/* === Hook === */
foreach (cot_getextplugins('admin.extensions.first') as $pl)
{
	include $pl;
}
/* ===== */

switch($a)
{
	/* =============== */
	case 'details':
	/* =============== */
		$ext_info = $dir . '/' . $code . '/' . $code . '.setup.php';
		switch($b)
		{
			case 'pause':
				cot_extension_pause($code);
				cot_message('adm_paused');
			break;
			case 'unpause':
				cot_extension_resume($code);
				cot_message('adm_running');
			break;
			case 'pausepart':
				cot_plugin_pause($code, $part);
				cot_message('adm_partstopped');
			break;
			case 'unpausepart':
				cot_plugin_resume($code, $part);
				cot_message('adm_partrunning');
			break;
		}
		if (!empty($b))
		{
			$db->update($db_users, array('user_auth' => ''));
			if ($cache)
			{
				$cache->clear();
				cot_rc_consolidate();
			}
		}
		$exists = file_exists($ext_info);
		
		if ($exists)
		{
			$old_ext_format = false;
			$info = cot_infoget($ext_info, 'COT_EXT');
			if (!$info && cot_plugin_active('genoa'))
			{
				// Try to load old format info
				$info = cot_infoget($ext_info, 'SED_EXTPLUGIN');
				$old_ext_format = true;
				cot_message('ext_old_format', 'warning');
			}
			
			$parts = array();
			$handle = opendir($dir . '/' . $code);
			while($f = readdir($handle))
			{
				if (preg_match("#^$code(\.([\w\.]+))?.php$#", $f, $mt)
					&& !in_array($mt[2], $cot_ext_ignore_parts))
				{
					$parts[] = $f;
				}
			}
			closedir($handle);
			
			$info['Auth_members'] = cot_auth_getvalue($info['Auth_members']);
			$info['Lock_members'] = cot_auth_getvalue($info['Lock_members']);
			$info['Auth_guests'] = cot_auth_getvalue($info['Auth_guests']);
			$info['Lock_guests'] = cot_auth_getvalue($info['Lock_guests']);
		}
		else
		{
			$row = $db->query("SELECT * FROM $db_core WHERE ct_code = '$code'")->fetch();
			$info['Name'] = $row['ct_title'];
			$info['Version'] = $row['ct_version'];
		}
		
		$adminpath[] = array(cot_url('admin', "m=extensions&a=details&$arg=$code"), $info['Name']);

		$isinstalled = cot_extension_installed($code);

		$sql = $db->query("SELECT COUNT(*) FROM $db_config WHERE config_owner='$type' AND config_cat='$code'");
		$totalconfig = $sql->fetchColumn();

		if (count($parts) > 0)
		{
			sort($parts);
			/* === Hook - Part1 : Set === */
			$extp = cot_getextplugins('admin.extensions.details.part.loop');
			/* ===== */
			foreach ($parts as $i => $x)
			{
				$extplugin_file = $dir . '/' . $code . '/' . $x;
				$info_file = cot_infoget($extplugin_file, 'COT_EXT');
				if (!$info_file && cot_plugin_active('genoa'))
				{
					// Try to load old format info
					$info_file = cot_infoget($extplugin_file, 'SED_EXTPLUGIN');
				}
				$info_part = preg_match("#^$code\.([\w\.]+).php$#", $x, $mt) ? $mt[1] : 'main';

				if(!empty($info_file['Error']))
				{
					$t->assign(array(
						'ADMIN_EXTENSIONS_DETAILS_ROW_X' => $x,
						'ADMIN_EXTENSIONS_DETAILS_ROW_ERROR' => $info_file['Error']
					));
					$t->parse('MAIN.DETAILS.ROW_ERROR_PART');
				}
				else
				{
					$sql = $db->query("SELECT pl_active, pl_id FROM $db_plugins
						WHERE pl_code='$code' AND pl_part='".$info_part."' LIMIT 1");

					if($row = $sql->fetch())
					{
						$info_file['Status'] = $row['pl_active'];
					}
					else
					{
						$info_file['Status'] = 3;
					}

					if(empty($info_file['Tags']))
					{
						$t->assign(array(
							'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
							'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part
						));
						$t->parse('MAIN.DETAILS.ROW_ERROR_TAGS');
					}
					else
					{
						$taggroups = explode(';', $info_file['Tags']);
						foreach ($taggroups as $taggroup)
						{
							$line = explode(':', $taggroup);
							$line[0] = trim($line[0]);
							$tplbase = explode('.', preg_replace('#\.tpl$#i', '', $line[0]));
							// Detect template container type
							if (in_array($tplbase[0], array('admin', 'users')))
							{
								$tpltype = 'core';
							}
							elseif (file_exists($cfg['plugins_dir'] . '/' . $tplbase[0]))
							{
								$tpltype = 'plug';
							}
							else
							{
								$tpltype = 'module';
							}
							$tags = explode(',', $line[1]);
							$listtags = $line[0].' :<br />';
							foreach($tags as $k => $v)
							{
								if(mb_substr(trim($v), 0, 1) == '{')
								{
									$listtags .= $v.' : ';
									$found = cot_stringinfile(cot_tplfile($tplbase, $tpltype), trim($v));
									$listtags .= $found_txt[$found].'<br />';
								}
								else
								{
									$listtags .= $v.'<br />';
								}
							}

							$t->assign(array(
								'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
								'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part,
								'ADMIN_EXTENSIONS_DETAILS_ROW_FILE' => $line[0].' :<br />',
								'ADMIN_EXTENSIONS_DETAILS_ROW_LISTTAGS' => $listtags,
								//'ADMIN_EXTENSIONS_DETAILS_ROW_TAGS_ODDEVEN' => cot_build_oddeven($ii)
							));
							$t->parse('MAIN.DETAILS.ROW_TAGS');
						}
					}

					$info_order = empty($info_file['Order']) ? COT_PLUGIN_DEFAULT_ORDER : $info_file['Order'];
					$t->assign(array(
						'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
						'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part,
						'ADMIN_EXTENSIONS_DETAILS_ROW_FILE' => $x,
						'ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS' => implode('<br />',explode(',',$info_file['Hooks'])),
						'ADMIN_EXTENSIONS_DETAILS_ROW_ORDER' => $info_order,
						'ADMIN_EXTENSIONS_DETAILS_ROW_STATUS' => $status[$info_file['Status']],
						//'ADMIN_EXTENSIONS_DETAILS_ROW_PART_ODDEVEN' => cot_build_oddeven($ii)
					));

					if ($info_file['Status'] == 3)
					{
						$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_NOTINSTALLED');
					}
					if ($info_file['Status'] != 3 && $row['pl_active'] == 1)
					{
						$t->assign('ADMIN_EXTENSIONS_DETAILS_ROW_PAUSEPART_URL',
							cot_url('admin', "m=extensions&a=details&$arg=$code&b=pausepart&part=".$row['pl_id']));
						$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_PAUSE');
					}
					if ($info_file['Status'] != 3 && $row['pl_active'] == 0)
					{
						$t->assign('ADMIN_EXTENSIONS_DETAILS_ROW_UNPAUSEPART_URL',
							cot_url('admin', "m=extensions&a=details&$arg=$code&b=unpausepart&part=".$row['pl_id']));
						$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_UNPAUSE');
					}

					/* === Hook - Part2 : Include === */
					foreach ($extp as $pl)
					{
						include $pl;
					}
					/* ===== */
					$t->parse('MAIN.DETAILS.ROW_PART');
				}
			}
		}

		$L['info_desc'] = '';
		if (file_exists(cot_langfile($code, $type)))
		{
			include cot_langfile($code, $type);
		}
		$icofile = (($type == 'module') ? $cfg['modules_dir'] : $cfg['plugins_dir']) . '/' . $code . '/' . $code . '.png';
		
		// Search admin parts, standalone parts, struct
		if( $db->query("SELECT pl_code FROM $db_plugins WHERE (pl_hook='standalone' OR pl_hook='module') AND pl_code='$code' LIMIT 1")->rowCount() > 0)
		{
			$standalone = ($type == 'module') ? cot_url($code) : cot_url('plug', 'e=' . $code);
		}

		$tool_hook = $type == 'plug' ? 'tools' : 'admin';
		if($db->query("SELECT pl_code FROM $db_plugins WHERE pl_hook='$tool_hook' AND pl_code='$code' LIMIT 1")->rowCount() > 0)
		{
			$tools = $type == 'plug' ? cot_url('admin', "m=other&p=$code") : cot_url('admin', "m=$code");
		}

		if($db->query("SELECT pl_code FROM $db_plugins WHERE pl_hook='admin.structure.first' AND pl_code='$code' LIMIT 1")->rowCount() > 0)
		{
			$struct = cot_url('admin', "m=structure&n=$code");
		}
		
		// Universal tags
		$t->assign(array(
			'ADMIN_EXTENSIONS_NAME' => $info['Name'],
			'ADMIN_EXTENSIONS_TYPE' => $type == 'module' ? $L['Module'] : $L['Plugin'],
			'ADMIN_EXTENSIONS_CODE' => $code,
			'ADMIN_EXTENSIONS_ICO' => (file_exists($icofile)) ? $icofile : '',
			'ADMIN_EXTENSIONS_DESCRIPTION' => empty($L['info_desc']) ? $info['Description'] : $L['info_desc'],
			'ADMIN_EXTENSIONS_VERSION' => $info['Version'],
			'ADMIN_EXTENSIONS_DATE' => $info['Date'],
			'ADMIN_EXTENSIONS_CONFIG_URL' => cot_url('admin', "m=config&n=edit&o=$type&p=$code"),
			'ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS' => $tools,
			'ADMIN_EXTENSIONS_JUMPTO_URL' => $standalone,
			'ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT' => $struct,
			'ADMIN_EXTENSIONS_TOTALCONFIG' => $totalconfig,
			'ADMIN_EXTENSIONS_INSTALL_URL' => cot_url('admin', "m=extensions&a=edit&$arg=$code&b=install"),
			'ADMIN_EXTENSIONS_UPDATE_URL' => cot_url('admin', "m=extensions&a=edit&$arg=$code&b=update"),
			'ADMIN_EXTENSIONS_UNINSTALL_URL' => cot_url('admin', "m=extensions&a=edit&$arg=$code&b=uninstall"),
			'ADMIN_EXTENSIONS_UNINSTALL_CONFIRM_URL' => cot_url('admin', "m=extensions&a=edit&$arg=$code&b=uninstall&x={$sys['xk']}"),
			'ADMIN_EXTENSIONS_PAUSE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=pause"),
			'ADMIN_EXTENSIONS_UNPAUSE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=unpause")
		));

		if ($exists)
		{
			// Tags for existing exts
			$t->assign(array(
				'ADMIN_EXTENSIONS_RIGHTS' => $type == 'module' ? cot_url('admin', "m=rightsbyitem&ic=$code&io=a")
					: cot_url('admin', "m=rightsbyitem&ic=$type&io=$code"),
				'ADMIN_EXTENSIONS_ADMRIGHTS_AUTH_GUESTS' => cot_auth_getmask($info['Auth_guests']),
				'ADMIN_EXTENSIONS_AUTH_GUESTS' => $info['Auth_guests'],
				'ADMIN_EXTENSIONS_ADMRIGHTS_LOCK_GUESTS' => cot_auth_getmask($info['Lock_guests']),
				'ADMIN_EXTENSIONS_LOCK_GUESTS' => $info['Lock_guests'],
				'ADMIN_EXTENSIONS_ADMRIGHTS_AUTH_MEMBERS' => cot_auth_getmask($info['Auth_members']),
				'ADMIN_EXTENSIONS_AUTH_MEMBERS' => $info['Auth_members'],
				'ADMIN_EXTENSIONS_ADMRIGHTS_LOCK_MEMBERS' => cot_auth_getmask($info['Lock_members']),
				'ADMIN_EXTENSIONS_LOCK_MEMBERS' => $info['Lock_members'],
				'ADMIN_EXTENSIONS_AUTHOR' => $info['Author'],
				'ADMIN_EXTENSIONS_COPYRIGHT' => $info['Copyright'],
				'ADMIN_EXTENSIONS_NOTES' => cot_parse($info['Notes']),
			));
			
			// Check and display dependencies
			$dependencies_satisfied = true;
			foreach (array('Requires_modules', 'Requires_plugins', 'Recommends_modules', 'Recommends_plugins') as $dep_type)
			{
				if (!empty($info[$dep_type]))
				{
					$dep_obligatory = strpos($dep_type, 'Requires') === 0;
					$dep_module = strpos($dep_type, 'modules') !== false;
					$arg = $dep_module ? 'mod' : 'pl';
					$dir = $dep_module ? $cfg['modules_dir'] : $cfg['plugins_dir'];
					
					foreach (explode(',', $info[$dep_type]) as $ext)
					{
						$dep_installed = cot_extension_installed($ext);
						if ($dep_obligatory)
						{
							$dep_class = $dep_installed ? 'highlight_green' : 'highlight_red';
							$dependencies_satisfied &= $dep_installed;
						}
						else
						{
							$dep_class = '';
						}
						
						$dep_ext_info = $dir . '/' . $ext . '/' . $ext . '.setup.php';
						if (file_exists($dep_ext_info))
						{
							$dep_info = cot_infoget($dep_ext_info, 'COT_EXT');
							if (!$dep_info && cot_plugin_active('genoa'))
							{
								// Try to load old format info
								$dep_info = cot_infoget($dep_ext_info, 'SED_EXTPLUGIN');
							}
						}
						else
						{
							$dep_info = array(
								'Name' => $ext
							);
						}
						$t->assign(array(
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_CODE' => $ext,
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_NAME' => $dep_info['Name'],
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_URL' => cot_url('admin', "m=extensions&a=details&$arg=$ext"),
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_TYPE' => $dep_module ? $L['Module'] : $L['Plugin'],
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_CLASS' => $dep_class
						));
						$t->parse('MAIN.DETAILS.DEPENDENCIES.DEPENDENCIES_ROW');
					}
					$t->assign(array(
						'ADMIN_EXTENSIONS_DEPENDENCIES_TITLE' => $L['ext_' . strtolower($dep_type)]
					));
					$t->parse('MAIN.DETAILS.DEPENDENCIES');
				}
			}
		}
		/* === Hook  === */
		foreach (cot_getextplugins('admin.extensions.details') as $pl)
		{
			include $pl;
		}
		/* ===== */
		$t->parse('MAIN.DETAILS');
	break;
	/* =============== */
	case 'edit':
	/* =============== */
		$dir = $is_module ? $cfg['modules_dir'] : $cfg['plugins_dir'];
		$ext_info = $dir . '/' . $code . '/' . $code . '.setup.php';
		$arg = $is_module ? 'mod' : 'pl';
		$exists = file_exists($ext_info);
		if ($exists)
		{
			$info = cot_infoget($ext_info, 'COT_EXT');
			if (!$info && cot_plugin_active('genoa'))
			{
				// Try to load old format info
				$info = cot_infoget($ext_info, 'SED_EXTPLUGIN');
			}
		}
		else
		{
			$info = array(
				'Name' => $code
			);
		}
		$adminpath[] = array(cot_url('admin', "m=extensions&a=details&$arg=$code"), $info['Name']);
		switch($b)
		{
			case 'install':
				$installed_modules = $db->query("SELECT ct_code FROM $db_core WHERE ct_plug = 0")->fetchAll(PDO::FETCH_COLUMN);
				$installed_plugins = $db->query("SELECT ct_code FROM $db_core WHERE ct_plug = 1")->fetchAll(PDO::FETCH_COLUMN);
				$dependencies_satisfied = cot_extension_dependencies_statisfied($code, $is_module, $installed_modules, $installed_plugins);
				if ($dependencies_satisfied)
				{
					$result = cot_extension_install($code, $is_module);
				}
				$adminpath[] = $L['adm_opt_install'];
				$t->assign(array(
					'ADMIN_EXTENSIONS_EDIT_TITLE' => cot_rc('ext_installing', array(
							'type' => $is_module ? $L['Module'] : $L['Plugin'],
							'name' => $code
						)),
					'ADMIN_EXTENSIONS_EDIT_RESULT' => $dependencies_satisfied && $result && !cot_error_found() ? 'success' : 'error',
					'ADMIN_EXTENSIONS_EDIT_LOG' => cot_implode_messages(),
					'ADMIN_EXTENSIONS_EDIT_CONTINUE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code")
				));
				/* === Hook  === */
				foreach (cot_getextplugins('admin.extensions.install.tags') as $pl)
				{
					include $pl;
				}
				/* ===== */
			break;
			case 'update':
				$result = cot_extension_install($code, $is_module, true, true);
				$adminpath[] = $L['adm_opt_update'];
				$t->assign(array(
					'ADMIN_EXTENSIONS_EDIT_TITLE' => cot_rc('ext_updating', array(
							'type' => $is_module ? $L['Module'] : $L['Plugin'],
							'name' => $code
						)),
					'ADMIN_EXTENSIONS_EDIT_RESULT' => $result && !cot_error_found() ? 'success' : 'error',
					'ADMIN_EXTENSIONS_EDIT_LOG' => cot_implode_messages(),
					'ADMIN_EXTENSIONS_EDIT_CONTINUE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code")
				));
				/* === Hook  === */
				foreach (cot_getextplugins('admin.extensions.update.tags') as $pl)
				{
					include $pl;
				}
				/* ===== */
				break;
			case 'uninstall':
				/* === Hook  === */
				foreach (cot_getextplugins('admin.extensions.uninstall.first') as $pl)
				{
					include $pl;
				}
				/* ===== */
				if (cot_check_xg(false))
				{
					// Check if there are extensions installed depending on this one
					$dependencies_satisfied = true;
					$res = $db->query("SELECT ct_code, ct_plug FROM $db_core ORDER BY ct_plug, ct_code");
					foreach ($res->fetchAll() as $row)
					{
						$ext = $row['ct_code'];
						$dir = $row['ct_plug'] ? $cfg['plugins_dir'] : $cfg['modules_dir'];
						$dep_ext_info = $dir . '/' . $ext . '/' . $ext . '.setup.php';
						if (file_exists($dep_ext_info))
						{
							$dep_info = cot_infoget($dep_ext_info, 'COT_EXT');
							if (!$dep_info && cot_plugin_active('genoa'))
							{
								// Try to load old format info
								$dep_info = cot_infoget($dep_ext_info, 'SED_EXTPLUGIN');
							}
							$dep_field = $is_module ? 'Requires_modules' : 'Requires_plugins';
							if (in_array($code, explode(',', $dep_info[$dep_field])))
							{
								cot_error(cot_rc('ext_dependency_uninstall_error', array(
									'type' => $row['ct_plug'] ? $L['Plugin'] : $L['Module'],
									'name' => $dep_info['Name']
								)));
								$dependencies_satisfied = false;
							}
						}
					}
					
					if ($dependencies_satisfied)
					{
						$result = cot_extension_uninstall($code, $is_module);
					}
					$adminpath[] = $L['adm_opt_uninstall'];
					$t->assign(array(
						'ADMIN_EXTENSIONS_EDIT_TITLE' => cot_rc('ext_uninstalling', array(
								'type' => $is_module ? $L['Module'] : $L['Plugin'],
								'name' => $code
							)),
						'ADMIN_EXTENSIONS_EDIT_RESULT' => $dependencies_satisfied && $result && !cot_error_found() ? 'success' : 'error',
						'ADMIN_EXTENSIONS_EDIT_LOG' => cot_implode_messages(),
						'ADMIN_EXTENSIONS_EDIT_CONTINUE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code")
					));
					/* === Hook  === */
					foreach (cot_getextplugins('admin.extensions.uninstall.tags') as $pl)
					{
						include $pl;
					}
					/* ===== */
				}
				else
				{
					$url = cot_url('admin', "m=extensions&a=edit&$arg=$code&b=uninstall&x={$sys['xk']}");
					cot_message(cot_rc('ext_uninstall_confirm', array('url' => $url)), 'error');
					cot_redirect(cot_url('admin', "m=extensions&a=details&$arg=$code", '', true));
				}
			break;
			default:
				cot_die();
			break;
		}
		if ($cache)
		{
			cot_rc_consolidate();
		}
		cot_clear_messages();
		$t->parse('MAIN.EDIT');
	break;
	/* =============== */
	case 'hooks':
	/* =============== */
		$adminpath[] = array(cot_url('admin', 'm=extensions&a=hooks'), $L['Hooks']);

		$sql = $db->query("SELECT * FROM $db_plugins ORDER BY pl_hook ASC, pl_code ASC, pl_order ASC");

		while($row = $sql->fetch())
		{
			$t->assign(array(
				'ADMIN_EXTENSIONS_HOOK' => $row['pl_hook'],
				'ADMIN_EXTENSIONS_CODE' => $row['pl_code'],
				'ADMIN_EXTENSIONS_ORDER' => $row['pl_order'],
				'ADMIN_EXTENSIONS_ACTIVE' => $cot_yesno[$row['pl_active']]
			));
			$t->parse('MAIN.HOOKS.HOOKS_ROW');
		}
		$sql->closeCursor();

		$t->assign(array(
			'ADMIN_EXTENSIONS_CNT_HOOK' => $sql->rowCount()
		));
		$t->parse('MAIN.HOOKS');
	break;
	/* =============== */
	default:
	/* =============== */
		foreach (array('module', 'plug') as $type)
		{
			$sql = $db->query("SELECT DISTINCT(config_cat), COUNT(*) FROM $db_config
			WHERE config_owner='$type' GROUP BY config_cat");
			while ($row = $sql->fetch(PDO::FETCH_NUM))
			{
				$cfgentries[$row['config_cat']] = $row[0];
			}
			$sql->closeCursor();

			$dir = $type == 'module' ? $cfg['modules_dir'] : $cfg['plugins_dir'];
			$extensions = array();
			$handle = opendir($dir);
			while ($f = readdir($handle))
			{
				if (is_dir($dir . '/' . $f) && $f[0] != '.')
				{
					// Check for extension .php files, otherwise it's inconsistent
					$is_ext = false;
					$dp = opendir($dir . '/' . $f);
					while ($pf = readdir($dp))
					{
						if (preg_match("#^$f(\.([\w\.]+))?.php$#", $pf))
						{
							$is_ext = true;
							break;
						}
					}
					closedir($dp);
					if ($is_ext)
					{
						$extensions[] = $f;
					}
				}
			}
			closedir($handle);
			
			// Find missing extensions
			$extlist = count($extensions) > 0 ? "ct_code NOT IN('" . implode("','", $extensions) . "')" : '1';
			$ctplug = $type == 'module' ? '0' : '1';
			$sql = $db->query("SELECT ct_code FROM $db_core WHERE $extlist AND ct_plug = $ctplug");
			foreach ($sql->fetchAll() as $row)
			{
				if ($type ==  'module' && in_array($row['ct_code'], array('admin', 'message', 'users')))
				{
					continue;
				}
				$extensions[] = $row['ct_code'];
			}
			
			sort($extensions);
			$cnt_extp = count($extensions);
			$cnt_parts = 0;

			$standalone = array();
			$sql3 = $db->query("SELECT pl_code FROM $db_plugins WHERE pl_hook='standalone' OR pl_hook='module'");
			while ($row3 = $sql3->fetch())
			{
				$standalone[$row3['pl_code']] = TRUE;
			}
			$sql3->closeCursor();

			$tools = array();
			$tool_hook = $type == 'plug' ? 'tools' : 'admin';
			$sql3 = $db->query("SELECT pl_code FROM $db_plugins WHERE pl_hook='$tool_hook'");
			while ($row3 = $sql3->fetch())
			{
				$tools[$row3['pl_code']] = TRUE;
			}
			$sql3->closeCursor();

			$struct = array();
			$sql3 = $db->query("SELECT pl_code FROM $db_plugins WHERE pl_hook='admin.structure.first'");
			while ($row3 = $sql3->fetch())
			{
				$struct[$row3['pl_code']] = TRUE;
			}

			$sql3->closeCursor();


			/* === Hook - Part1 : Set === */
			$extp = cot_getextplugins("admin.extensions.$type.list.loop");
			/* ===== */
			foreach ($extensions as $i => $x)
			{
				$ext_info = $dir . '/' . $x . '/' . $x . '.setup.php';
				$exists = file_exists($ext_info);
				
				if ($exists)
				{
					$info = cot_infoget($ext_info, 'COT_EXT');
					if (!$info && cot_plugin_active('genoa'))
					{
						// Try to load old format info
						$info = cot_infoget($ext_info, 'SED_EXTPLUGIN');
					}
				}
				else
				{
					$info = array(
						'Name' => $x
					);
				}

					if (!empty($info['Error']))
				{
					$t->assign(array(
						'ADMIN_EXTENSIONS_X_ERR' => $x,
						'ADMIN_EXTENSIONS_ERROR_MSG' => $info['Error']
					));
					$t->parse('MAIN.DEFAULT.ROW.ROW_ERROR_EXT');
					$t->parse('MAIN.DEFAULT.ROW');
				}
				else
				{
					$totalactive = $db->query("SELECT SUM(pl_active) FROM $db_plugins WHERE pl_code='$x'")->fetchColumn();
					$totalinstalled = $db->query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code='$x'")->fetchColumn();

					$cnt_parts += $totalinstalled;

					if (!cot_extension_installed($x))
					{
						$part_status = 3;
						$info['Partscount'] = '?';
					}
					else
					{
						$info['Partscount'] = $totalinstalled;
						if (!$exists)
						{
							$part_status = 4;
						}
						elseif ($totalinstalled > $totalactive && $totalactive > 0)
						{
							$part_status = 2;
						}
						elseif ($totalactive == 0 && $totalinstalled > 0)
						{
							$part_status = 0;
						}
						else
						{
							$part_status = 1;
						}
					}
					$totalconfig = $db->query("SELECT COUNT(*) FROM $db_config WHERE config_owner='$type' AND config_cat='$x'")->fetchColumn();

					$ifthistools = $tools[$x];
					$ent_code = $cfgentries[$x];
					$if_plg_standalone = $standalone[$x];
					$ifstruct = $struct[$x];

					if ($type == 'module')
					{
						$jump_url = cot_url($x);
						$arg = 'mod';
					}
					else
					{
						$jump_url = cot_url('plug', 'e=' . $x);
						$arg = 'pl';
					}
					$icofile = (($type == 'module') ? $cfg['modules_dir'] : $cfg['plugins_dir']) . '/' . $x . '/' . $x . '.png';
					
					$t->assign(array(
						'ADMIN_EXTENSIONS_DETAILS_URL' => cot_url('admin', "m=extensions&a=details&$arg=$x"),
						'ADMIN_EXTENSIONS_NAME' => $info['Name'],
						'ADMIN_EXTENSIONS_TYPE' => $type == 'module' ? $L['Module'] : $L['Plugin'],
						'ADMIN_EXTENSIONS_CODE_X' => $x,
						'ADMIN_EXTENSIONS_ICO' => (file_exists($icofile)) ? $icofile : '',
						'ADMIN_EXTENSIONS_EDIT_URL' => cot_url('admin', "m=config&n=edit&o=$type&p=$x"),
						'ADMIN_EXTENSIONS_TOTALCONFIG' => $totalconfig,
						'ADMIN_EXTENSIONS_PARTSCOUNT' => $info['Partscount'],
						'ADMIN_EXTENSIONS_STATUS' => $status[$part_status],
						'ADMIN_EXTENSIONS_VERSION' => $info['Version'],
						'ADMIN_EXTENSIONS_RIGHTS_URL' => $type == 'module' ? cot_url('admin', "m=rightsbyitem&ic=$x&io=a") : cot_url('admin',
								"m=rightsbyitem&ic=$type&io=$x"),
						'ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS' => $type == 'plug' ? cot_url('admin', "m=other&p=$x") : cot_url('admin', "m=$x"),
						'ADMIN_EXTENSIONS_JUMPTO_URL' => $jump_url,
						'ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT' => cot_url('admin', "m=structure&n=$x"),
						'ADMIN_EXTENSIONS_ODDEVEN' => cot_build_oddeven($i)
					));
					/* === Hook - Part2 : Include === */
					foreach ($extp as $pl)
					{
						include $pl;
					}
					/* ===== */
					$t->parse('MAIN.DEFAULT.SECTION.ROW');
				}
			}
			$t->assign(array(
				'ADMIN_EXTENSIONS_SECTION_TITLE' => $type == 'module' ? $L['Modules'] : $L['Plugins'],
				'ADMIN_EXTENSIONS_CNT_EXTP' => $cnt_extp
			));
			$t->parse('MAIN.DEFAULT.SECTION');
		}

		$t->assign(array(
			'ADMIN_EXTENSIONS_HOOKS_URL' => cot_url('admin', 'm=extensions&a=hooks')
		));

		$t->parse('MAIN.DEFAULT');
	break;
}

if (!empty($code) && $b == 'install' && $totalconfig > 0)
{
	$t->assign('ADMIN_EXTENSIONS_CONFIG_URL', cot_url('admin', 'm=config&n=edit&o=plug&p='.$code));
	$t->parse('MAIN.CONFIG_URL');
}

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.extensions.tags') as $pl)
{
	include $pl;
}
/* ===== */
$t->parse('MAIN');
$adminmain = $t->text('MAIN');

?>