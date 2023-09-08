<?php
/**
 * Extension administration
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('admin', 'a');
cot_block(Cot::$usr['isadmin']);

require_once cot_incfile('auth');

$t = new XTemplate(cot_tplfile('admin.extensions', 'core'));

$adminpath[] = array (cot_url('admin', 'm=extensions'), Cot::$L['Extensions']);
$adminTitle = Cot::$L['Extensions'];

$pl = cot_import('pl', 'G', 'ALP');
$mod = cot_import('mod', 'G', 'ALP');
$part = cot_import('part', 'G', 'TXT');
$sort = cot_import('sort', 'G', 'ALP');

if (empty($mod)) {
	if (empty($pl)) {
		if (!empty($a) && $a != 'hooks') {
			cot_die();
		}

	} else {
		$is_module = false;
		$code = $pl;
		$arg = 'pl';
		$dir = Cot::$cfg['plugins_dir'];
		$type = 'plug';
	}

} else {
	$is_module = true;
	$code = $mod;
	$arg = 'mod';
	$dir = Cot::$cfg['modules_dir'];
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
foreach (cot_getextplugins('admin.extensions.first') as $pl) {
	include $pl;
}
/* ===== */

switch($a) {
	/* =============== */
	case 'details':
	/* =============== */
		$ext_info = $dir . '/' . $code . '/' . $code . '.setup.php';
        $info = false;
		$exists = file_exists($ext_info);
		if ($exists) {
			$old_ext_format = false;
			$info = cot_infoget($ext_info, 'COT_EXT');
			if (!$info && cot_plugin_active('genoa')) {
				// Try to load old format info
				$info = cot_infoget($ext_info, 'SED_EXTPLUGIN');
				$old_ext_format = true;
				cot_message('ext_old_format', 'warning');
			}
		}

        if ($info == false) {
            // Failed to load info block
            // Lets use default data
            $info = array(
                'Code' => $code,
                'Name' => $code,
                'Description' => '',
                'Category' => '',
                'Version' => '',
                'Date' => '',
                'Author' => '',
                'Copyright' => '',
                'Notes' => '',
                'Auth_members' => '',
                'Lock_members' => '',
                'Auth_guests' => '',
                'Lock_guests' => '',
                'Requires_modules' => '',
                'Requires_plugins' => '',
                'Recommends_modules' => '',
                'Recommends_plugins' => ''
            );
        }

		switch($b) {
			case 'install':
				$installed_modules = $db->query("SELECT ct_code FROM $db_core WHERE ct_plug = 0")->fetchAll(PDO::FETCH_COLUMN);
				$installed_plugins = $db->query("SELECT ct_code FROM $db_core WHERE ct_plug = 1")->fetchAll(PDO::FETCH_COLUMN);
				$dependencies_satisfied = cot_extension_dependencies_statisfied($code, $is_module, $installed_modules, $installed_plugins);
				if ($dependencies_satisfied) {
					$result = cot_extension_install($code, $is_module);
				}
			break;

			case 'update':
				$result = cot_extension_install($code, $is_module, true, true);
				break;

			case 'uninstall':
				/* === Hook  === */
				foreach (cot_getextplugins('admin.extensions.uninstall.first') as $pl) {
					include $pl;
				}
				/* ===== */

				if (cot_check_xg(false)) {
					// Check if there are extensions installed depending on this one
					$dependencies_satisfied = true;
					$res = $db->query("SELECT ct_code, ct_plug FROM $db_core ORDER BY ct_plug, ct_code");
					foreach ($res->fetchAll() as $row)
					{
						$ext = $row['ct_code'];
						$dir_ext = $row['ct_plug'] ? Cot::$cfg['plugins_dir'] : Cot::$cfg['modules_dir'];
						$dep_ext_info = $dir_ext . '/' . $ext . '/' . $ext . '.setup.php';
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
									'type' => $row['ct_plug'] ? Cot::$L['Plugin'] : Cot::$L['Module'],
									'name' => $dep_info['Name']
								)));
								$dependencies_satisfied = false;
							}
						}
					}

					if ($dependencies_satisfied) {
						$result = cot_extension_uninstall($code, $is_module);
					}
					$adminpath[] = Cot::$L['adm_opt_uninstall'];

				} else {
					$url = cot_url('admin', "m=extensions&a=details&$arg=$code&b=uninstall&x={$sys['xk']}");
					cot_message(cot_rc('ext_uninstall_confirm', array('url' => $url)), 'error');
					cot_redirect(cot_url('admin', "m=extensions&a=details&$arg=$code", '', true));
				}
				break;

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

		if (!empty($b)) {
			Cot::$db->update(Cot::$db->users, array('user_auth' => ''), "user_auth != ''");
			if (Cot::$cache) {
                Cot::$cache->clear();
			}
			cot_redirect(cot_url('admin', "m=extensions&a=details&$arg=$code", '', true));
		}

        $registeredParts = [];
        $parts = [];
		if ($exists) {
			// Collect all parts from extension directory
			$handle = opendir($dir . '/' . $code);
			while ($f = readdir($handle)) {
				if (
                    preg_match("#^$code(\.([\w\.]+))?.php$#", $f, $mt)
                    && (!isset($mt[2]) || !in_array($mt[2], $cot_ext_ignore_parts))
                ) {
					$parts[] = $f;
				}
			}
			closedir($handle);

			// ...And from DB
			$registeredParts = Cot::$db->query('SELECT * FROM ' . Cot::$db->plugins . ' WHERE pl_code = '.
                Cot::$db->quote($code))->fetchAll();
			if (empty($registeredParts)) {
                $registeredParts = array();
            }

			foreach ($registeredParts as $reg_data) {
				if ($reg_data['pl_code'] == $code) {
					$f = str_replace($code . '/', '', $reg_data['pl_file']);
					if (!in_array($f, $parts)) {
                        $parts[] = $f;
                    }
				}
			}

			$info['Auth_members'] = cot_auth_getvalue($info['Auth_members']);
			$info['Lock_members'] = cot_auth_getvalue($info['Lock_members']);
			$info['Auth_guests'] = cot_auth_getvalue($info['Auth_guests']);
			$info['Lock_guests'] = cot_auth_getvalue($info['Lock_guests']);

		} else {
			$row = Cot::$db->query('SELECT * FROM ' . Cot::$db->core . ' WHERE ct_code = '.
                Cot::$db->quote($code))->fetch();
			if ($row) {
                $info['Name'] = $row['ct_title'];
                $info['Version'] = $row['ct_version'];
            }
		}

		$ext_info = cot_get_extensionparams($code, $is_module);
		$adminpath[] = [cot_url('admin', ['m' => 'extensions', 'a' => 'details', $arg => $code]), $ext_info['name']];

		$isinstalled = cot_extension_installed($code);

		$sql = Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->config .
            " WHERE config_owner='$type' AND config_cat='$code' AND config_type != " . COT_CONFIG_TYPE_HIDDEN);
		$totalconfig = $sql->fetchColumn();

		if (count($parts) > 0) {
			sort($parts);
			/* === Hook - Part1 : Set === */
			$extp = cot_getextplugins('admin.extensions.details.part.loop');
			/* ===== */

			foreach ($parts as $i => $x) {
				$extplugin_file = $dir . '/' . $code . '/' . $x;
				$info_file = array();
				$Hooks = array();
				if (file_exists($extplugin_file)) {
					$info_file = cot_infoget($extplugin_file, 'COT_EXT');
					$Hooks = explode(',', str_replace(' ', '', $info_file['Hooks']));
				}
				$info_part = preg_match("#^$code\.([\w\.]+).php$#", $x, $mt) ? $mt[1] : 'main';

				$info_file['Status'] = 3;
				foreach ($registeredParts  as $reg_data) {
					if ($reg_data['pl_part'] == $info_part) {
						$info_file['Status'] = $reg_data['pl_active'];
						break;
					}
				}

				// check for not registered Hooks
				$not_registred = array();
				if ($info_file['Status'] == 1) {
					foreach ($Hooks as $h) {
						$regsistred_by_hook = $cot_plugins[$h];
						if (is_array($regsistred_by_hook) && sizeof($regsistred_by_hook)) {
							$found = false;
							foreach ($regsistred_by_hook as $reg_data) {
								if ($reg_data['pl_file'] == $code . '/' . $x) {
									$found = true;
									break;
								}
							}
							if (!$found) {
								array_push($not_registred, $h);
							}
						} else {
							array_push($not_registred, $h);
						}
					}
				}

				$deleted = array();

				// check for deleted Hooks
				if (file_exists($extplugin_file)) {
					foreach ($registeredParts as $reg_data) {
						if ($reg_data['pl_file'] == $code . '/' . $x) {
							if (!in_array($reg_data['pl_hook'], $Hooks)) {
                                array_push($deleted, $reg_data['pl_hook']);
                            }
						}
					}
				}


				if ($isinstalled && (!file_exists($extplugin_file)) || sizeof($deleted) > 0 || sizeof($not_registred) > 0) {
					$info_file['Error'] = Cot::$L['adm_hook_changed'];
					if (sizeof($not_registred)) {
						$info_file['Error'] .= cot_rc('adm_hook_notregistered', array('hooks' => implode(', ', $not_registred)));
					}
					if (sizeof($deleted)) {
						$info_file['Error'] .= cot_rc('adm_hook_notfound', array('hooks' => implode(', ', $deleted)));
					}
					if (!file_exists($extplugin_file)) {
						$info_file['Error'] .= cot_rc('adm_hook_filenotfound', array('file' => $extplugin_file));
					}
					$info_file['Error'] .= Cot::$L['adm_hook_updatenote'];
				}

				if (!empty($info_file['Error'])) {
					$t->assign(array(
						'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
						'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part,
						'ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS' => !empty($info_file['Hooks']) ?
                            implode('<br />',explode(',',$info_file['Hooks'])) : '',
						'ADMIN_EXTENSIONS_DETAILS_ROW_FILE' => $x,
						'ADMIN_EXTENSIONS_DETAILS_ROW_ERROR' => $info_file['Error']
					));
					$t->parse('MAIN.DETAILS.ROW_ERROR_PART');

				} else {
					if (empty($info_file['Tags'])) {
						$t->assign(array(
							'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
							'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part
						));
						$t->parse('MAIN.DETAILS.ROW_ERROR_TAGS');

					} else {
						$taggroups = explode(';', $info_file['Tags']);
						foreach ($taggroups as $taggroup) {
							$line = explode(':', $taggroup);
							$line[0] = trim($line[0]);
							$tplbase = explode('.', preg_replace('#\.tpl$#i', '', $line[0]));
							// Detect template container type
							if (in_array($tplbase[0], array('admin', 'users'))) {
								$tpltype = 'core';
							} elseif (file_exists(Cot::$cfg['plugins_dir'] . '/' . $tplbase[0])) {
								$tpltype = 'plug';
							} else {
								$tpltype = 'module';
							}
							$tags = explode(',', $line[1]);
							$tpl_file = cot_tplfile($tplbase, $tpltype);
							$listtags = $tpl_file.' :<br />';
							if (Cot::$cfg['xtpl_cache']) {
							    // clears cache if exists
								$cache_file = str_replace(array('./', '/'), '_', $tpl_file);
								$cache_path = Cot::$cfg['cache_dir'] . '/templates/' .
                                    pathinfo($cache_file, PATHINFO_FILENAME );
								$cache_files_ext = array('.tpl','.idx','.tags');
								foreach ($cache_files_ext as $ext) {
									if (file_exists($cache_path.$ext)) {
                                        unlink($cache_path.$ext);
                                    }
								}
							}
							$tpl_check = new XTemplate($tpl_file);
							$tpl_tags = $tpl_check->getTags();
							unset($tpl_tags[array_search('PHP', $tpl_tags)]);
							foreach($tags as $k => $v) {
								if(mb_substr(trim($v), 0, 1) == '{') {
									$tag = str_replace(array('{','}'),'',$v);
									$found = in_array($tag, $tpl_tags);
									$listtags .= $v.' : ';
									$listtags .= $found_txt[$found].'<br />';
								} else {
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
                    $info_hooks = !empty($info_file['Hooks']) ?
                        implode('<br />',explode(',',$info_file['Hooks'])) : '';
					$t->assign(array(
						'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
						'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part,
						'ADMIN_EXTENSIONS_DETAILS_ROW_FILE' => $x,
						'ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS' => $info_hooks,
						'ADMIN_EXTENSIONS_DETAILS_ROW_ORDER' => $info_order,
						'ADMIN_EXTENSIONS_DETAILS_ROW_STATUS' => $status[$info_file['Status']],
						//'ADMIN_EXTENSIONS_DETAILS_ROW_PART_ODDEVEN' => cot_build_oddeven($ii)
					));

					if ($info_file['Status'] == 3) {
						$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_NOTINSTALLED');

					} elseif ($info_file['Status'] == 1) {
						$t->assign('ADMIN_EXTENSIONS_DETAILS_ROW_PAUSEPART_URL',
							cot_url('admin', "m=extensions&a=details&$arg=$code&b=pausepart&part=".$info_part));
						$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_PAUSE');

					} elseif($info_file['Status'] == 0) {
						$t->assign('ADMIN_EXTENSIONS_DETAILS_ROW_UNPAUSEPART_URL',
							cot_url('admin', "m=extensions&a=details&$arg=$code&b=unpausepart&part=".$info_part));
						$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_UNPAUSE');
					}

					/* === Hook - Part2 : Include === */
					foreach ($extp as $pl) {
						include $pl;
					}
					/* ===== */
					$t->parse('MAIN.DETAILS.ROW_PART');
				}
			}
		}

		Cot::$L['info_name'] = Cot::$L['info_desc'] = Cot::$L['info_notes'] = '';
		if (file_exists(cot_langfile($code, $type))) {
			include cot_langfile($code, $type);
		}

		// Search admin parts, standalone parts, struct
        $standalone = null;
		if( $db->query("SELECT pl_code FROM $db_plugins WHERE (pl_hook='standalone' OR pl_hook='module') AND pl_code='$code' LIMIT 1")->rowCount() > 0)
		{
			$standalone = ($type == 'module') ? cot_url($code) : cot_url('plug', 'e=' . $code);
		}

		$tool_hook = $type == 'plug' ? 'tools' : 'admin';
        $tools = null;
		if ($db->query("SELECT pl_code FROM $db_plugins WHERE pl_hook='$tool_hook' AND pl_code='$code' AND pl_active = 1 LIMIT 1")->rowCount() > 0)
		{
			$tools = $type == 'plug' ? cot_url('admin', "m=other&p=$code") : cot_url('admin', "m=$code");
		}

        $struct = null;
		if ($db->query("SELECT pl_code FROM $db_plugins WHERE pl_hook='admin.structure.first' AND pl_code='$code' LIMIT 1")->rowCount() > 0)
		{
			$struct = cot_url('admin', "m=structure&n=$code");
		}

		$installed_ver = $db->query("SELECT ct_version FROM $db_core WHERE ct_code = '$code'")->fetchColumn();

        $params = cot_get_extensionparams($code, $type == COT_EXT_TYPE_MODULE);

		// Universal tags
		$t->assign(array(
			'ADMIN_EXTENSIONS_NAME' => htmlspecialchars($params['name']),
			'ADMIN_EXTENSIONS_TYPE' => $type == COT_EXT_TYPE_MODULE ? Cot::$L['Module'] : Cot::$L['Plugin'],
			'ADMIN_EXTENSIONS_CODE' => $code,
            'ADMIN_EXTENSIONS_ICON' => $params['icon'],
			'ADMIN_EXTENSIONS_DESCRIPTION' => $params['desc'],
			'ADMIN_EXTENSIONS_NOTES' => $params['notes'],
			'ADMIN_EXTENSIONS_VERSION' => $info['Version'],
			'ADMIN_EXTENSIONS_VERSION_INSTALLED' => $installed_ver,
			'ADMIN_EXTENSIONS_VERSION_COMPARE' => version_compare($info['Version'], $installed_ver),
			'ADMIN_EXTENSIONS_DATE' => $info['Date'],
			'ADMIN_EXTENSIONS_CONFIG_URL' => cot_url('admin', "m=config&n=edit&o=$type&p=$code"),
			'ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS' => $tools,
			'ADMIN_EXTENSIONS_JUMPTO_URL' => $standalone,
			'ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT' => $struct,
			'ADMIN_EXTENSIONS_TOTALCONFIG' => $totalconfig,
			'ADMIN_EXTENSIONS_INSTALL_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=install"),
			'ADMIN_EXTENSIONS_UPDATE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=update"),
			'ADMIN_EXTENSIONS_UNINSTALL_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=uninstall"),
			'ADMIN_EXTENSIONS_UNINSTALL_CONFIRM_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=uninstall&x={$sys['xk']}"),
			'ADMIN_EXTENSIONS_PAUSE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=pause"),
			'ADMIN_EXTENSIONS_UNPAUSE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=unpause"),

            // @deprecated For backward compatibility. Will be removed in future releases
            'ADMIN_EXTENSIONS_ICO' => $params['legacyIcon'],
		));

		if ($exists) {
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
			));

			// Check and display dependencies
			$dependencies_satisfied = true;
			foreach (['Requires_modules', 'Requires_plugins', 'Recommends_modules', 'Recommends_plugins'] as $dep_type) {
				if (!empty($info[$dep_type])) {
					$dep_obligatory = strpos($dep_type, 'Requires') === 0;
					$dep_module = strpos($dep_type, 'modules') !== false;
					$arg = $dep_module ? 'mod' : 'pl';
					$dir = $dep_module ? Cot::$cfg['modules_dir'] : Cot::$cfg['plugins_dir'];

					foreach (explode(',', $info[$dep_type]) as $ext) {
						$ext = trim($ext);
						$dep_installed = cot_extension_installed($ext);
						if ($dep_obligatory) {
							$dep_class = $dep_installed ? 'highlight_green' : 'highlight_red';
							$dependencies_satisfied &= $dep_installed;
						} else {
							$dep_class = '';
						}

						$dep_ext_info = $dir . '/' . $ext . '/' . $ext . '.setup.php';
						if (file_exists($dep_ext_info)) {
							$dep_info = cot_infoget($dep_ext_info, 'COT_EXT');
							if (!$dep_info && cot_plugin_active('genoa')) {
								// Try to load old format info
								$dep_info = cot_infoget($dep_ext_info, 'SED_EXTPLUGIN');
							}
						} else {
							$dep_info = array(
								'Name' => $ext
							);
						}

                        $dependencyUrl = '';
                        if (
                            ($dep_module && file_exists(Cot::$cfg['modules_dir'] . '/' . $ext))
                            || (!$dep_module && file_exists(Cot::$cfg['plugins_dir'] . '/' . $ext))
                        ) {
                            $dependencyUrl = cot_url('admin', ['m' => 'extensions', 'a' => 'details', $arg => $ext]);
                        }

						$t->assign(array(
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_CODE' => $ext,
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_NAME' => $dep_info['Name'],
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_URL' => $dependencyUrl,
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_TYPE' => $dep_module ? Cot::$L['Module'] : Cot::$L['Plugin'],
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_CLASS' => $dep_class
						));
						$t->parse('MAIN.DETAILS.DEPENDENCIES.DEPENDENCIES_ROW');
					}
					$t->assign(array(
						'ADMIN_EXTENSIONS_DEPENDENCIES_TITLE' => Cot::$L['ext_' . strtolower($dep_type)]
					));
					$t->parse('MAIN.DETAILS.DEPENDENCIES');
				}
			}
		}

		/* === Hook  === */
		foreach (cot_getextplugins('admin.extensions.details') as $pl) {
			include $pl;
		}
		/* ===== */

		$t->parse('MAIN.DETAILS');
	break;

	/* =============== */
	case 'hooks':
	/* =============== */
		$adminpath[] = array(cot_url('admin', 'm=extensions&a=hooks'), Cot::$L['Hooks']);

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
		// Params to show only installed extensions
		$only_installed = cot_import('inst', 'G', 'BOL');
		if (Cot::$cfg['default_show_installed'])
		{
			if (is_null($only_installed))
			{
				$only_installed = true;
			}
			$only_installed_urlp = $only_installed ? '' : '&inst=0';
			$only_installed_toggle = $only_installed ? '&inst=0' : '';
		}
		else
		{
			$only_installed_urlp = $only_installed ? '&inst=1' : '';
			$only_installed_toggle = $only_installed ? '' : '&inst=1';
		}
		$sort_urlp = $sort == 'cat' ? '&sort=cat' : '';

		// Filter/sort tags
		$t->assign(array(
			'ADMIN_EXTENSIONS_HOOKS_URL' => cot_url('admin', 'm=extensions&a=hooks'),
			'ADMIN_EXTENSIONS_SORT_ALP_URL' => cot_url('admin', 'm=extensions'.$only_installed_urlp),
			'ADMIN_EXTENSIONS_SORT_ALP_SEL' => $sort != 'cat',
			'ADMIN_EXTENSIONS_SORT_CAT_URL' => cot_url('admin', 'm=extensions&sort=cat'.$only_installed_urlp),
			'ADMIN_EXTENSIONS_SORT_CAT_SEL' => $sort == 'cat',
			'ADMIN_EXTENSIONS_ALL_EXTENSIONS_URL' => cot_url('admin', 'm=extensions'.$sort_urlp),
			'ADMIN_EXTENSIONS_ONLY_INSTALLED_URL' => cot_url('admin', 'm=extensions'.$sort_urlp.$only_installed_toggle),
			'ADMIN_EXTENSIONS_ONLY_INSTALLED_SEL' => $only_installed
		));

		// Prefetch common data to save SQL queries
		$totalconfigs = array();
		foreach ($db->query("SELECT COUNT(*) AS cnt, config_owner, config_cat
			FROM $db_config WHERE config_type != " . COT_CONFIG_TYPE_HIDDEN . "
			GROUP BY config_owner, config_cat")->fetchAll() as $row)
		{
			$totalconfigs[$row['config_owner']][$row['config_cat']] = (int)$row['cnt'];
		}

		$totalactives = array();
		$totalinstalleds = array();
		foreach ($db->query("SELECT SUM(pl_active) AS sum, COUNT(*) AS cnt, pl_code FROM $db_plugins GROUP BY pl_code")->fetchAll() as $row)
		{
			$totalactives[$row['pl_code']] = (int)$row['sum'];
			$totalinstalleds[$row['pl_code']] = (int)$row['cnt'];
		}

		$installed_vers = array();
		foreach($db->query("SELECT ct_version, ct_code FROM $db_core")->fetchAll() as $row)
		{
			$installed_vers[$row['ct_code']] = $row['ct_version'];
		}

		foreach (array('module', 'plug') as $type) {
			$sql = $db->query("SELECT DISTINCT(config_cat), COUNT(*) FROM $db_config
			    WHERE config_owner='$type' GROUP BY config_cat");
			while ($row = $sql->fetch(PDO::FETCH_NUM)) {
				$cfgentries[$row[0]] = $row[1];
			}
			$sql->closeCursor();

			$dir = $type == 'module' ? Cot::$cfg['modules_dir'] : Cot::$cfg['plugins_dir'];
			$extensions = cot_extension_list_info($dir);
			$ctplug = $type == 'module' ? '0' : '1';

			if ($only_installed)
			{
				// Filter only installed exts
				$tmp = array();
				$installed_exts = $db->query("SELECT ct_code FROM $db_core WHERE ct_plug = $ctplug")->fetchAll(PDO::FETCH_COLUMN);
				foreach ($extensions as $key => $val)
				{
					if (in_array($key, $installed_exts))
					{
						$tmp[$key] = $val;
					}
				}
				$extensions = $tmp;
			}

			// Find missing extensions
			$extlist = count($extensions) > 0 ? "ct_code NOT IN('" . implode("','", array_keys($extensions)) . "')" : '1';
			$sql = $db->query("SELECT * FROM $db_core WHERE $extlist AND ct_plug = $ctplug");
			foreach ($sql->fetchAll() as $row)
			{
				if ($type ==  'module' && in_array($row['ct_code'], array('admin', 'message', 'users')))
				{
					continue;
				}
				$extensions[$row['ct_code']] = array(
					'Code' => $row['ct_code'],
					'Name' => $row['ct_title'],
					'Version' => $row['ct_version'],
					'Category' => 'misc-ext',
					'NotFound' => true
				);
			}

			if ($type == 'plug' && $sort == 'cat')
			{
				uasort($extensions, 'cot_extension_catcmp');
			}
			else
			{
				ksort($extensions);
			}

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
			while ($row3 = $sql3->fetch()) {
				$struct[$row3['pl_code']] = TRUE;
			}
			$sql3->closeCursor();

			$prev_cat = '';

			/* === Hook - Part1 : Set === */
			$extp = cot_getextplugins("admin.extensions.$type.list.loop");
			/* ===== */

            $i = 1;
			foreach ($extensions as $code => $info) {
				if ($sort == 'cat' && $type == 'plug' && $prev_cat != $info['Category']) {
					// Render category heading
					$t->assign(
                        'ADMIN_EXTENSIONS_CAT_TITLE',
                        isset(Cot::$L['ext_cat_' . $info['Category']]) ?
                            Cot::$L['ext_cat_' . $info['Category']] : $info['Category']
                    );
					$t->parse('MAIN.DEFAULT.SECTION.ROW.ROW_CAT');
					// Assign a new one
					$prev_cat = $info['Category'];
				}

				$exists = !isset($info['NotFound']);

				if (!empty($info['Error'])) {
					$t->assign(array(
						'ADMIN_EXTENSIONS_X_ERR' => $code,
						'ADMIN_EXTENSIONS_ERROR_MSG' => $info['Error']
					));
					$t->parse('MAIN.DEFAULT.ROW.ROW_ERROR_EXT');
					$t->parse('MAIN.DEFAULT.ROW');

				} else {
					$totalactive = isset($totalactives[$code]) ? $totalactives[$code] : 0;
					$totalinstalled = isset($totalinstalleds[$code]) ? $totalinstalleds[$code] : 0;

					$cnt_parts += $totalinstalled;

					if (!isset($installed_vers[$code])) {
						$part_status = 3;
						$info['Partscount'] = '?';

					} else {
						$info['Partscount'] = $totalinstalled;
						if (!$exists) {
							$part_status = 4;
						} elseif ($totalinstalled > $totalactive && $totalactive > 0) {
							$part_status = 2;
						} elseif ($totalactive == 0 && $totalinstalled > 0) {
							$part_status = 0;
						} else {
							$part_status = 1;
						}
					}

					$totalconfig = !empty($totalconfigs[$type][$code]) ? $totalconfigs[$type][$code] : 0;

					$ifthistools = isset($tools[$code]) && $tools[$code];
					$ent_code = isset($cfgentries[$code]) ? $cfgentries[$code] : 0;
					$if_plg_standalone = isset($standalone[$code]) && $standalone[$code];
					$ifstruct = isset($struct[$code]) && $struct[$code];

					if ($type == COT_EXT_TYPE_MODULE) {
						$jump_url = cot_url($code);
						$arg = 'mod';

					} else {
						$jump_url = cot_url('plug', 'e=' . $code);
						$arg = 'pl';
					}

					$installed_ver = isset($installed_vers[$code]) ? $installed_vers[$code] : '';

                    $params = cot_get_extensionparams($code, $type == COT_EXT_TYPE_MODULE);

					Cot::$L['info_name'] = Cot::$L['info_desc'] = Cot::$L['info_notes'] = '';
					if (file_exists(cot_langfile($code, $type))) {
						include cot_langfile($code, $type);
					}

					$t->assign([
						'ADMIN_EXTENSIONS_DETAILS_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code"),
						'ADMIN_EXTENSIONS_NAME' => htmlspecialchars($params['name']),
						'ADMIN_EXTENSIONS_TYPE' => $type == COT_EXT_TYPE_MODULE ? Cot::$L['Module'] : Cot::$L['Plugin'],
						'ADMIN_EXTENSIONS_CODE_X' => $code,
						'ADMIN_EXTENSIONS_NOTES' => $params['notes'],
						'ADMIN_EXTENSIONS_DESCRIPTION' => $params['desc'],
                        'ADMIN_EXTENSIONS_ICON' => $params['icon'],
						'ADMIN_EXTENSIONS_EDIT_URL' => cot_url('admin', "m=config&n=edit&o=$type&p=$code"),
						'ADMIN_EXTENSIONS_TOTALCONFIG' => $totalconfig,
						'ADMIN_EXTENSIONS_PARTSCOUNT' => $info['Partscount'],
						'ADMIN_EXTENSIONS_STATUS' => $status[$part_status],
						'ADMIN_EXTENSIONS_VERSION' => $info['Version'],
						'ADMIN_EXTENSIONS_VERSION_INSTALLED' => $installed_ver,
						'ADMIN_EXTENSIONS_VERSION_COMPARE' => version_compare($info['Version'], $installed_ver),
						'ADMIN_EXTENSIONS_RIGHTS_URL' => $type == COT_EXT_TYPE_MODULE ?
                            cot_url('admin', "m=rightsbyitem&ic=$code&io=a") :
                            cot_url('admin', "m=rightsbyitem&ic=$type&io=$code"),
						'ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS' => $type == COT_EXT_TYPE_PLUGIN ?
                            cot_url('admin', "m=other&p=$code") :
                            cot_url('admin', "m=$code"),
						'ADMIN_EXTENSIONS_JUMPTO_URL' => $jump_url,
						'ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT' => cot_url('admin', "m=structure&n=$code"),
						'ADMIN_EXTENSIONS_ODDEVEN' => cot_build_oddeven($i),

                         // @deprecated For backward compatibility. Will be removed in future releases
                        'ADMIN_EXTENSIONS_ICO' => $params['legacyIcon'],
					]);
					/* === Hook - Part2 : Include === */
					foreach ($extp as $pl) {
						include $pl;
					}
					/* ===== */
					$t->parse('MAIN.DEFAULT.SECTION.ROW');
				}

				$i++;
			}
			$t->assign(array(
				'ADMIN_EXTENSIONS_SECTION_TITLE' => $type == 'module' ? Cot::$L['Modules'] : Cot::$L['Plugins'],
				'ADMIN_EXTENSIONS_CNT_EXTP' => $cnt_extp
			));
			$t->parse('MAIN.DEFAULT.SECTION');
		}

		$t->parse('MAIN.DEFAULT');
	break;
}
/*
if (!empty($code) && $b == 'install' && $totalconfig > 0)
{
	$t->assign('ADMIN_EXTENSIONS_CONFIG_URL', cot_url('admin', 'm=config&n=edit&o=plug&p='.$code));
	$t->parse('MAIN.CONFIG_URL');
}
*/
cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.extensions.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');
