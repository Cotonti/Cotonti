<?php
/**
 * Extension administration
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\extensions\ExtensionsControlService;
use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsService;

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * @var array $cot_plugins
 */

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('admin', 'a');
cot_block(Cot::$usr['isadmin']);

require_once cot_incfile('auth');

$t = new XTemplate(cot_tplfile('admin.extensions', 'core'));

$adminPath[] = array (cot_url('admin', 'm=extensions'), Cot::$L['Extensions']);
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
		$type = ExtensionsDictionary::TYPE_PLUGIN;
	}

} else {
	$is_module = true;
	$code = $mod;
	$arg = 'mod';
	$dir = Cot::$cfg['modules_dir'];
	$type = ExtensionsDictionary::TYPE_MODULE;
}

$status = [
    0 => $R['admin_code_paused'] ?? '',
    1 => $R['admin_code_running'] ?? '',
    2 => $R['admin_code_partrunning'] ?? '',
    3 => $R['admin_code_notinstalled'] ?? '',
    4 => $R['admin_code_missing'] ?? ''
];

$found_txt = [
    0 => $R['admin_code_missing'] ?? '',
    1 => $R['admin_code_present'] ?? ''
];

unset($disp_errors);

$extensionsService = ExtensionsService::getInstance();

/* === Hook === */
foreach (cot_getextplugins('admin.extensions.first') as $pl) {
	include $pl;
}
/* ===== */

switch($a) {
	/* =============== */
	case 'details':
	/* =============== */
        $extDir = $dir . '/' . $code;
        if (!file_exists($extDir)) {
            cot_die_message(404);
        }
		$ext_info = $extDir . '/' . $code . '.setup.php';
        $info = false;
		$exists = file_exists($ext_info);
		if ($exists) {
			$old_ext_format = false;
			$info = cot_infoget($ext_info, 'COT_EXT');
			if (empty($info) && function_exists('cot_plugin_active') && cot_plugin_active('genoa')) {
				// Try to load old format info
				$info = cot_infoget($ext_info, 'SED_EXTPLUGIN');
				$old_ext_format = true;
				cot_message('ext_old_format', 'warning');
			}
		}

        if (empty($info)) {
            // Failed to load info block
            // Lets use default data
            $info = [
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
            ];
        }

        $extensionControlService = ExtensionsControlService::getInstance();

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
							if (empty($dep_info) && function_exists('cot_plugin_active') && cot_plugin_active('genoa'))
							{
								// Try to load old format info
								$dep_info = cot_infoget($dep_ext_info, 'SED_EXTPLUGIN');
							}
							$dep_field = $is_module ? 'Requires_modules' : 'Requires_plugins';
							$dep_field_value = isset($dep_info[$dep_field]) ? $dep_info[$dep_field] : '';
							
							if (!empty($dep_field_value)) {
                                $dep_requires = explode(',', $dep_field_value);
                                if (in_array($code, $dep_requires, true)) {
                                    cot_error(cot_rc('ext_dependency_uninstall_error', [
                                        'type' => $row['ct_plug'] ? Cot::$L['Plugin'] : Cot::$L['Module'],
                                        'name' => $dep_info['Name']
                                    ]));
                                    $dependencies_satisfied = false;
                                }
                            }
						}
					}

					if ($dependencies_satisfied) {
						$result = cot_extension_uninstall($code, $is_module);
					}
					$adminPath[] = Cot::$L['adm_opt_uninstall'];

				} else {
					$url = cot_url('admin', "m=extensions&a=details&$arg=$code&b=uninstall&x={$sys['xk']}");
					cot_message(cot_rc('ext_uninstall_confirm', ['url' => $url]), 'error');
					cot_redirect(cot_url('admin', "m=extensions&a=details&$arg=$code", '', true));
				}
				break;

			case 'pause':
                $extensionControlService->pause($code);
				cot_message('adm_paused');
				break;

			case 'unpause':
                $extensionControlService->resume($code);
				cot_message('adm_running');
				break;

			case 'pausepart':
                $extensionControlService->pause($code, $part);
				cot_message('adm_partstopped');
				break;

			case 'unpausepart':
                $extensionControlService->resume($code, $part);
				cot_message('adm_partrunning');
				break;
		}

		if (!empty($b)) {
			Cot::$db->update(Cot::$db->users, ['user_auth' => ''], "user_auth != ''");
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
            // There may be multiple entries with the same pl_part. One for each hook (for parts with multiple hooks).
            // But they all contain information about a same extension file. The table is not normalized.
            $registeredParts = [];
            $query = Cot::$db->query(
                'SELECT * FROM ' . Cot::$db->plugins . ' WHERE pl_code = :code',
                ['code' => $code]
            );
            while ($row = $query->fetch()) {
                if (!isset($registeredParts[$row['pl_part']])) {
                    $registeredParts[$row['pl_part']] = [
                        'hooks' => []
                    ];
                }
                
                if (!isset($registeredParts[$row['pl_part']]['hooks'])) {
                    $registeredParts[$row['pl_part']]['hooks'] = [];
                }
                
                $registeredParts[$row['pl_part']]['hooks'][$row['pl_hook']] = [
                    'hook' => $row['pl_hook'],
                    'order' => (int) $row['pl_order'],
                ];
                
                // Remove hook and order from the row before merging
                $rowData = $row;
                unset($rowData['pl_hook'], $rowData['pl_order']);
                
                // Merge the remaining data
                foreach ($rowData as $k => $v) {
                    $registeredParts[$row['pl_part']][$k] = $v;
                }
            }
            $query->closeCursor();

			foreach ($registeredParts as $reg_data) {
				if (isset($reg_data['pl_code']) && $reg_data['pl_code'] == $code && isset($reg_data['pl_file'])) {
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
		$adminPath[] = [cot_url('admin', ['m' => 'extensions', 'a' => 'details', $arg => $code]), $ext_info['name']];

        $isInstalled = $extensionsService->isInstalled($code, $type, true);

		$sql = Cot::$db->query(
            'SELECT COUNT(*) FROM ' . Cot::$db->config . ' WHERE config_owner = :type AND config_cat = :code '
            . 'AND config_type NOT IN (' . COT_CONFIG_TYPE_HIDDEN . ', ' . COT_CONFIG_TYPE_SEPARATOR . ') '
            . "AND (config_subcat IN ('', '__default') OR config_subcat IS NULL)",
            ['type' => $type, 'code' => $code]
        );
		$totalConfig = $sql->fetchColumn();

		if (count($parts) > 0) {
			sort($parts);

			/* === Hook - Part1 : Set === */
			$extp = cot_getextplugins('admin.extensions.details.part.loop');
			/* ===== */

			foreach ($parts as $i => $x) {
				$extplugin_file = $dir . '/' . $code . '/' . $x;
				$extensionPart = [];
				if (file_exists($extplugin_file)) {
					$extensionPart = cot_infoget($extplugin_file, 'COT_EXT');
                    
                    // Process hooks and orders safely
                    $hooksText = isset($extensionPart['Hooks']) && !empty($extensionPart['Hooks']) ? $extensionPart['Hooks'] : '';
                    $hooks = !empty($hooksText) ? explode(',', $hooksText) : [];
                    
                    $orderText = isset($extensionPart['Order']) && !empty($extensionPart['Order']) ? $extensionPart['Order'] : '';
                    $hookOrders = !empty($orderText) ? explode(',', $orderText) : [];
                    
                    $hookOrders = array_map(function($el) { 
                        return (int) trim($el); 
                    }, $hookOrders);
                    
                    $extensionPart['Hooks'] = [];
                    
                    foreach ($hooks as $key => $hook) {
                        $hook = trim($hook);
                        if (empty($hook)) {
                            continue;
                        }
                        $order = isset($hookOrders[$key]) ? (int) $hookOrders[$key] : COT_PLUGIN_DEFAULT_ORDER;
                        $extensionPart['Hooks'][$hook] = [
                            'hook' => $hook,
                            'order' => $order,
                        ];
                    }
                }
                
                if (!isset($extensionPart['Hooks']) || !is_array($extensionPart['Hooks'])) {
                    $extensionPart['Hooks'] = [];
                }

				$info_part = preg_match("#^$code\.([\w\.]+).php$#", $x, $mt) ? $mt[1] : 'main';

				$extensionPart['Status'] = 3;
                $extensionPart['installedOrder'] = null;
                if (isset($registeredParts[$info_part]) && isset($registeredParts[$info_part]['pl_active'])) {
                    $extensionPart['Status'] = $registeredParts[$info_part]['pl_active'];
                }

				// check for not registered Hooks
				$not_registred = [];
				if (isset($extensionPart['Status']) && $extensionPart['Status'] == 1) {
					foreach ($extensionPart['Hooks'] as $hook) {
						$regsistred_by_hook = isset($cot_plugins[$hook['hook']]) ? $cot_plugins[$hook['hook']] : null;
						if (is_array($regsistred_by_hook) && count($regsistred_by_hook) > 0) {
							$found = false;
							foreach ($regsistred_by_hook as $reg_data) {
								if (isset($reg_data['pl_file']) && $reg_data['pl_file'] == $code . '/' . $x) {
									$found = true;
									break;
								}
							}
							if (!$found) {
								$not_registred[] = $hook['hook'];
							}
						} else {
							$not_registred[] = $hook['hook'];
						}
					}
				}

				$deleted = [];
				// check for deleted Hooks
				if (file_exists($extplugin_file)) {
					foreach ($registeredParts as $part_key => $reg_data) {
                        if (!isset($reg_data['pl_file']) || $reg_data['pl_file'] !== $code . '/' . $x) {
                            continue;
                        }
                        
                        if (isset($reg_data['hooks']) && is_array($reg_data['hooks'])) {
                            foreach ($reg_data['hooks'] as $hookKey => $registeredHook) {
                                if (!isset($extensionPart['Hooks'][$hookKey])) {
                                    $deleted[] = $hookKey;
                                }
                            }
                        }
					}
				}

				$hasError = $isInstalled && (
                    !file_exists($extplugin_file) || 
                    count($deleted) > 0 || 
                    count($not_registred) > 0
                );
                
                if ($hasError) {
					$extensionPart['Error'] = isset(Cot::$L['adm_hook_changed']) ? Cot::$L['adm_hook_changed'] : 'Hook configuration has changed';
					
					if (count($not_registred) > 0) {
						$extensionPart['Error'] .= cot_rc('adm_hook_notregistered', ['hooks' => implode(', ', $not_registred)]);
					}
					
					if (count($deleted) > 0) {
						$extensionPart['Error'] .= cot_rc(
                            'adm_hook_notfound',
                            ['hooks' => implode(', ', $deleted)]
                        );
					}
					
					if (!file_exists($extplugin_file)) {
						$extensionPart['Error'] .= cot_rc('adm_hook_filenotfound', ['file' => $extplugin_file]);
					}
					
					$extensionPart['Error'] .= isset(Cot::$L['adm_hook_updatenote']) ? Cot::$L['adm_hook_updatenote'] : '';
				}

                // Prepare hook lists for display
                $hooks = '';
                if (!empty($extensionPart['Hooks'])) {
                    $hookList = [];
                    foreach ($extensionPart['Hooks'] as $h) {
                        if (isset($h['hook'])) {
                            $hookList[] = $h['hook'];
                        }
                    }
                    $hooks = implode('<br />', $hookList);
                }
                
                $installedHooks = '';
                if (isset($registeredParts[$info_part]) && isset($registeredParts[$info_part]['hooks']) && is_array($registeredParts[$info_part]['hooks'])) {
                    $hookList = [];
                    foreach ($registeredParts[$info_part]['hooks'] as $h) {
                        if (isset($h['hook'])) {
                            $hookList[] = $h['hook'];
                        }
                    }
                    $installedHooks = implode('<br />', $hookList);
                }

				if (isset($extensionPart['Error'])) {
					$t->assign([
						'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
						'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part,
                        'ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS' => $hooks,
                        'ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS_INSTALLED' => $installedHooks,
						'ADMIN_EXTENSIONS_DETAILS_ROW_FILE' => $x,
						'ADMIN_EXTENSIONS_DETAILS_ROW_ERROR' => $extensionPart['Error'],
					]);
					$t->parse('MAIN.DETAILS.ROW_ERROR_PART');

				} else {
					if (empty($extensionPart['Tags'])) {
						$t->assign([
							'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
							'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part,
						]);
						$t->parse('MAIN.DETAILS.ROW_ERROR_TAGS');

					} else {
						$taggroups = explode(';', $extensionPart['Tags']);
						foreach ($taggroups as $taggroup) {
							$line = explode(':', $taggroup);
							$line[0] = isset($line[0]) ? trim($line[0]) : '';
							$tplbase = explode('.', preg_replace('#\.tpl$#i', '', $line[0]));
							
							// Detect template container type
							$tpltype = 'module';
							if (isset($tplbase[0])) {
							    if (in_array($tplbase[0], ['admin', 'users'])) {
								    $tpltype = 'core';
							    } elseif (file_exists(Cot::$cfg['plugins_dir'] . '/' . $tplbase[0])) {
								    $tpltype = 'plug';
							    }
							}
							
							$tags = isset($line[1]) ? explode(',', $line[1]) : [];
							$tpl_file = cot_tplfile($tplbase, $tpltype);
							$listtags = $tpl_file . ' :<br />';
							
							if (isset(Cot::$cfg['xtpl_cache']) && Cot::$cfg['xtpl_cache']) {
							    // clears cache if exists
								$cache_file = str_replace(['./', '/'], '_', $tpl_file ?? '');
								$cache_path = Cot::$cfg['cache_dir'] . '/templates/' .
                                    pathinfo($cache_file, PATHINFO_FILENAME);
								$cache_files_ext = ['.tpl','.idx','.tags'];
								
								foreach ($cache_files_ext as $ext) {
									if (file_exists($cache_path . $ext)) {
                                        unlink($cache_path . $ext);
                                    }
								}
							}
							
							$tpl_check = new XTemplate($tpl_file);
							$tpl_tags = $tpl_check->getTags();
							
							$phpIndex = array_search('PHP', $tpl_tags);
							if ($phpIndex !== false) {
							    unset($tpl_tags[$phpIndex]);
							}
							
							foreach ($tags as $k => $v) {
								if (mb_substr(trim($v), 0, 1) == '{') {
									$tag = str_replace(['{','}'], '', $v);
									$found = in_array($tag, $tpl_tags);
									$listtags .= $v.' : ';
									$listtags .= isset($found_txt[$found ? 1 : 0]) ? $found_txt[$found ? 1 : 0] : '';
									$listtags .= '<br />';
								} else {
									$listtags .= $v.'<br />';
								}
							}

							$t->assign([
								'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i + 1,
								'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part,
								'ADMIN_EXTENSIONS_DETAILS_ROW_FILE' => $line[0].' :<br />',
								'ADMIN_EXTENSIONS_DETAILS_ROW_LISTTAGS' => $listtags,
							]);
							$t->parse('MAIN.DETAILS.ROW_TAGS');
						}
					}

                    // Process orders safely
                    $order = [];
                    $installedOrder = [];
                    
                    // Extract orders from hooks
                    if (!empty($extensionPart['Hooks'])) {
                        foreach ($extensionPart['Hooks'] as $hook) {
                            if (isset($hook['order'])) {
                                $order[] = $hook['order'];
                            }
                        }
                    }
                    
                    // Extract installed orders
                    if (isset($registeredParts[$info_part]) && isset($registeredParts[$info_part]['hooks'])) {
                        foreach ($registeredParts[$info_part]['hooks'] as $hook) {
                            if (isset($hook['order'])) {
                                $installedOrder[] = $hook['order'];
                            }
                        }
                    }
                    
                    // Simplify single value arrays
                    $orderFormatted = '';
                    $installedOrderFormatted = '';
                    
                    if (!empty($order)) {
                        if (count($order) === 1) {
                            $orderFormatted = (string)$order[0];
                        } else {
                            $minOrder = min($order);
                            $maxOrder = max($order);
                            
                            if ($minOrder === $maxOrder) {
                                $orderFormatted = (string)$minOrder;
                            } else {
                                $orderFormatted = implode(', ', $order);
                            }
                        }
                    }
                    
                    if (!empty($installedOrder)) {
                        if (count($installedOrder) === 1) {
                            $installedOrderFormatted = (string)$installedOrder[0];
                        } else {
                            $minOrder = min($installedOrder);
                            $maxOrder = max($installedOrder);
                            
                            if ($minOrder === $maxOrder) {
                                $installedOrderFormatted = (string)$minOrder;
                            } else {
                                $installedOrderFormatted = implode(', ', $installedOrder);
                            }
                        }
                    }

					$t->assign([
						'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i + 1,
						'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_part,
						'ADMIN_EXTENSIONS_DETAILS_ROW_FILE' => $x,
						'ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS' => $hooks,
                        'ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS_INSTALLED' => $installedHooks,
						'ADMIN_EXTENSIONS_DETAILS_ROW_ORDER' => $orderFormatted,
                        'ADMIN_EXTENSIONS_DETAILS_ROW_ORDER_INSTALLED' => $installedOrderFormatted,
						'ADMIN_EXTENSIONS_DETAILS_ROW_STATUS' => isset($status[$extensionPart['Status']]) ? $status[$extensionPart['Status']] : '',
					]);

					if ($extensionPart['Status'] == 3) {
						$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_NOTINSTALLED');

					} elseif ($extensionPart['Status'] == 1) {
						$t->assign('ADMIN_EXTENSIONS_DETAILS_ROW_PAUSEPART_URL',
							cot_url('admin', "m=extensions&a=details&$arg=$code&b=pausepart&part=".$info_part));
						$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_PAUSE');

					} elseif($extensionPart['Status'] == 0) {
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

        $structurePartUrl = null;
        $extensionsWithStructure = [];
        
        if (function_exists('cot_getExtensionsWithStructure')) {
            $extensionsWithStructure = cot_getExtensionsWithStructure();
        }
        
		if (is_array($extensionsWithStructure) && in_array($code, $extensionsWithStructure, true)) {
            $structurePartUrl = cot_url('admin', ['m' => 'structure', 'n' => $code]);
		}

		$installed_ver = '';
		$sql = Cot::$db->query(
            'SELECT ct_version FROM ' . Cot::$db->core . ' WHERE ct_code = :code',
            ['code' => $code]
        );
		
		if ($sql && $row = $sql->fetchColumn()) {
		    $installed_ver = $row;
		}

        $params = cot_get_extensionparams($code, $type === ExtensionsDictionary::TYPE_MODULE);

        $extensionCategoryTitle = '';
        if (!empty($info['Category'])) {
            $extensionCategoryTitle = isset(Cot::$L['ext_cat_' . $info['Category']]) ? 
                Cot::$L['ext_cat_' . $info['Category']] : 
                $info['Category'];
        }

        // Ensure values are set and not null
        $infoVersion = isset($info['Version']) ? $info['Version'] : '';
        $infoDate = isset($info['Date']) ? $info['Date'] : '';
        
        // Safe version_compare
        $versionCompare = 0;
        if (!empty($infoVersion) && !empty($installed_ver)) {
            $versionCompare = version_compare($infoVersion, $installed_ver);
        }

		// Universal tags
		$t->assign([
			'ADMIN_EXTENSIONS_NAME' => htmlspecialchars($params['name'] ?? ''),
			'ADMIN_EXTENSIONS_TYPE' => $type === ExtensionsDictionary::TYPE_MODULE
                ? (isset(Cot::$L['Module']) ? Cot::$L['Module'] : 'Module')
                : (isset(Cot::$L['Plugin']) ? Cot::$L['Plugin'] : 'Plugin'),
			'ADMIN_EXTENSIONS_CODE' => $code,
            'ADMIN_EXTENSIONS_ICON' => isset($params['icon']) ? $params['icon'] : '',
			'ADMIN_EXTENSIONS_DESCRIPTION' => isset($params['desc']) ? $params['desc'] : '',
			'ADMIN_EXTENSIONS_NOTES' => isset($params['notes']) ? $params['notes'] : '',
            'ADMIN_EXTENSIONS_CATEGORY' => isset($info['Category']) ? $info['Category'] : '',
            'ADMIN_EXTENSIONS_CATEGORY_TITLE' => $extensionCategoryTitle,
			'ADMIN_EXTENSIONS_VERSION' => $infoVersion,
			'ADMIN_EXTENSIONS_VERSION_INSTALLED' => $installed_ver,
			'ADMIN_EXTENSIONS_VERSION_COMPARE' => $versionCompare,
			'ADMIN_EXTENSIONS_DATE' => $infoDate,
			'ADMIN_EXTENSIONS_CONFIG_URL' => cot_url(
                'admin',
                ['m' => 'config', 'n' => 'edit', 'o' => $type, 'p' => $code]
            ),
            'ADMIN_EXTENSIONS_ADMIN_URL' => $extensionsService->getAdminPageUrl($code, $type),
            'ADMIN_EXTENSIONS_JUMPTO_URL' => $extensionsService->getPublicPageUrl($code, $type),
            'ADMIN_EXTENSIONS_STRUCTURE_URL' => $structurePartUrl,
			'ADMIN_EXTENSIONS_TOTALCONFIG' => $totalConfig,
			'ADMIN_EXTENSIONS_INSTALL_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=install"),
			'ADMIN_EXTENSIONS_UPDATE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=update"),
			'ADMIN_EXTENSIONS_UNINSTALL_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=uninstall"),
			'ADMIN_EXTENSIONS_UNINSTALL_CONFIRM_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=uninstall&x={$sys['xk']}"),
			'ADMIN_EXTENSIONS_PAUSE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=pause"),
			'ADMIN_EXTENSIONS_UNPAUSE_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code&b=unpause"),
		]);
		
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            $t->assign([
                // @deprecated For backward compatibility. Will be removed in future releases
                'ADMIN_EXTENSIONS_ICO' => isset($params['legacyIcon']) ? $params['legacyIcon'] : '',

                /** @deprecated in 0.9.26 */
                'ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS' => $extensionsService->getAdminPageUrl($code, $type),
                'ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT' => $structurePartUrl,
            ]);
        }

		if ($exists) {
			// Tags for existing exts
			$t->assign([
				'ADMIN_EXTENSIONS_RIGHTS_URL' => $extensionsService->getRightsUrl($code, $type),
				'ADMIN_EXTENSIONS_ADMRIGHTS_AUTH_GUESTS' => cot_auth_getmask($info['Auth_guests'] ?? ''),
				'ADMIN_EXTENSIONS_AUTH_GUESTS' => isset($info['Auth_guests']) ? $info['Auth_guests'] : '',
				'ADMIN_EXTENSIONS_ADMRIGHTS_LOCK_GUESTS' => cot_auth_getmask($info['Lock_guests'] ?? ''),
				'ADMIN_EXTENSIONS_LOCK_GUESTS' => isset($info['Lock_guests']) ? $info['Lock_guests'] : '',
				'ADMIN_EXTENSIONS_ADMRIGHTS_AUTH_MEMBERS' => cot_auth_getmask($info['Auth_members'] ?? ''),
				'ADMIN_EXTENSIONS_AUTH_MEMBERS' => isset($info['Auth_members']) ? $info['Auth_members'] : '',
				'ADMIN_EXTENSIONS_ADMRIGHTS_LOCK_MEMBERS' => cot_auth_getmask($info['Lock_members'] ?? ''),
				'ADMIN_EXTENSIONS_LOCK_MEMBERS' => isset($info['Lock_members']) ? $info['Lock_members'] : '',
				'ADMIN_EXTENSIONS_AUTHOR' => isset($info['Author']) ? $info['Author'] : '',
				'ADMIN_EXTENSIONS_COPYRIGHT' => isset($info['Copyright']) ? $info['Copyright'] : '',
			]);

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
						if (empty($ext)) continue;
						
						$dep_installed = $extensionsService->isInstalled($ext, null, true);
						
						$dep_class = '';
						if ($dep_obligatory) {
							$dep_class = $dep_installed ? 'highlight_green' : 'highlight_red';
							$dependencies_satisfied = $dependencies_satisfied && $dep_installed;
						}

						$dep_ext_info = $dir . '/' . $ext . '/' . $ext . '.setup.php';
						$dep_info = ['Name' => $ext];
						
						if (file_exists($dep_ext_info)) {
							$loadedInfo = cot_infoget($dep_ext_info, 'COT_EXT');
							if (!$loadedInfo && function_exists('cot_plugin_active') && cot_plugin_active('genoa')) {
								// Try to load old format info
								$loadedInfo = cot_infoget($dep_ext_info, 'SED_EXTPLUGIN');
							}
							
							if (is_array($loadedInfo)) {
                                $dep_info = $loadedInfo;
                            }
						}

                        $dependencyUrl = '';
                        if (
                            ($dep_module && file_exists(Cot::$cfg['modules_dir'] . '/' . $ext))
                            || (!$dep_module && file_exists(Cot::$cfg['plugins_dir'] . '/' . $ext))
                        ) {
                            $dependencyUrl = cot_url('admin', ['m' => 'extensions', 'a' => 'details', $arg => $ext]);
                        }

						$t->assign([
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_CODE' => $ext,
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_NAME' => isset($dep_info['Name']) ? $dep_info['Name'] : $ext,
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_URL' => $dependencyUrl,
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_TYPE' => $dep_module ? 
                                (isset(Cot::$L['Module']) ? Cot::$L['Module'] : 'Module') : 
                                (isset(Cot::$L['Plugin']) ? Cot::$L['Plugin'] : 'Plugin'),
							'ADMIN_EXTENSIONS_DEPENDENCIES_ROW_CLASS' => $dep_class,
						]);
						$t->parse('MAIN.DETAILS.DEPENDENCIES.DEPENDENCIES_ROW');
					}
					
					$dep_title = isset(Cot::$L['ext_' . strtolower($dep_type)]) ? 
                        Cot::$L['ext_' . strtolower($dep_type)] : 
                        ucfirst(str_replace('_', ' ', $dep_type));
                        
					$t->assign([
						'ADMIN_EXTENSIONS_DEPENDENCIES_TITLE' => $dep_title
					]);
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
		$adminPath[] = [cot_url('admin', 'm=extensions&a=hooks'), Cot::$L['Hooks'] ?? 'Hooks'];

		$sql = $db->query("SELECT * FROM $db_plugins ORDER BY pl_hook ASC, pl_code ASC, pl_order ASC");

		while($row = $sql->fetch()) {
			$t->assign([
				'ADMIN_EXTENSIONS_HOOK' => $row['pl_hook'],
				'ADMIN_EXTENSIONS_CODE' => $row['pl_code'],
				'ADMIN_EXTENSIONS_ORDER' => $row['pl_order'],
				'ADMIN_EXTENSIONS_ACTIVE' => isset($cot_yesno[$row['pl_active']]) ? $cot_yesno[$row['pl_active']] : $row['pl_active']
			]);
			$t->parse('MAIN.HOOKS.HOOKS_ROW');
		}
		$sql->closeCursor();

		$t->assign([
			'ADMIN_EXTENSIONS_CNT_HOOK' => $sql->rowCount()
		]);
		$t->parse('MAIN.HOOKS');
	break;

	/* =============== */
	default:
	/* =============== */
		// Params to show only installed extensions
		$only_installed = cot_import('inst', 'G', 'BOL');
		if (isset(Cot::$cfg['default_show_installed']) && Cot::$cfg['default_show_installed']) {
			if (is_null($only_installed)) {
				$only_installed = true;
			}
			$only_installed_urlp = $only_installed ? '' : '&inst=0';
			$only_installed_toggle = $only_installed ? '&inst=0' : '';
		} else {
			$only_installed_urlp = $only_installed ? '&inst=1' : '';
			$only_installed_toggle = $only_installed ? '' : '&inst=1';
		}
		$sort_urlp = $sort == 'cat' ? '&sort=cat' : '';

		// Filter/sort tags
		$t->assign([
			'ADMIN_EXTENSIONS_HOOKS_URL' => cot_url('admin', 'm=extensions&a=hooks'),
			'ADMIN_EXTENSIONS_SORT_ALP_URL' => cot_url('admin', 'm=extensions'.$only_installed_urlp),
			'ADMIN_EXTENSIONS_SORT_ALP_SEL' => $sort != 'cat',
			'ADMIN_EXTENSIONS_SORT_CAT_URL' => cot_url('admin', 'm=extensions&sort=cat'.$only_installed_urlp),
			'ADMIN_EXTENSIONS_SORT_CAT_SEL' => $sort == 'cat',
			'ADMIN_EXTENSIONS_ALL_EXTENSIONS_URL' => cot_url('admin', 'm=extensions'.$sort_urlp),
			'ADMIN_EXTENSIONS_ONLY_INSTALLED_URL' => cot_url('admin', 'm=extensions'.$sort_urlp.$only_installed_toggle),
			'ADMIN_EXTENSIONS_ONLY_INSTALLED_SEL' => $only_installed,
		]);

		// Prefetch common data to save SQL queries
		$totalConfigs = [];
        $sql = Cot::$db->query(
            'SELECT COUNT(*) AS cnt, config_owner, config_cat FROM ' . Cot::$db->config
            . ' WHERE config_type NOT IN (' . COT_CONFIG_TYPE_HIDDEN . ', ' . COT_CONFIG_TYPE_SEPARATOR . ') '
            . "AND (config_subcat IN ('', '__default') OR config_subcat IS NULL) "
            . 'GROUP BY config_owner, config_cat');

        while ($row = $sql->fetch())    {
            if (!isset($totalConfigs[$row['config_owner']])) {
                $totalConfigs[$row['config_owner']] = [];
            }
            $totalConfigs[$row['config_owner']][$row['config_cat']] = (int) $row['cnt'];
        }
        $sql->closeCursor();

		$totalactives = [];
		$totalinstalleds = [];
		$query = $db->query("SELECT SUM(pl_active) AS sum, COUNT(*) AS cnt, pl_code FROM $db_plugins GROUP BY pl_code");
		while ($row = $query->fetch()) {
		    $totalactives[$row['pl_code']] = (int) $row['sum'];
		    $totalinstalleds[$row['pl_code']] = (int) $row['cnt'];
        }
        $query->closeCursor();

		$installed_vers = [];
		$query = $db->query("SELECT ct_version, ct_code FROM $db_core");
		while ($row = $query->fetch()) {
		    $installed_vers[$row['ct_code']] = $row['ct_version'];
        }
        $query->closeCursor();

        $extensionsWithStructure = [];
        if (function_exists('cot_getExtensionsWithStructure')) {
            $extensionsWithStructure = cot_getExtensionsWithStructure();
        }

		$cfgentries = [];
		foreach (['module', 'plug'] as $type) {
			$sql = $db->query("SELECT DISTINCT(config_cat), COUNT(*) FROM $db_config
			    WHERE config_owner='$type' GROUP BY config_cat");
			while ($row = $sql->fetch(PDO::FETCH_NUM)) {
				$cfgentries[$row[0]] = $row[1];
			}
			$sql->closeCursor();

			$dir = $type == 'module' ? Cot::$cfg['modules_dir'] : Cot::$cfg['plugins_dir'];
			$extensions = [];
			
			if (function_exists('cot_extension_list_info')) {
			    $extensions = cot_extension_list_info($dir);
			}
			
			$ctplug = $type == 'module' ? '0' : '1';

			if ($only_installed) {
				// Filter only installed exts
				$tmp = [];
				$installed_exts = [];
				$query = $db->query("SELECT ct_code FROM $db_core WHERE ct_plug = $ctplug");
				while ($row = $query->fetch()) {
				    $installed_exts[] = $row['ct_code'];
				}
				$query->closeCursor();
				
				foreach ($extensions as $key => $val) {
					if (in_array($key, $installed_exts)) {
						$tmp[$key] = $val;
					}
				}
				$extensions = $tmp;
			}

			// Find missing extensions
			$extlist = '1';
			if (count($extensions) > 0) {
			    $extKeys = array_keys($extensions);
			    $extKeysQuoted = array_map(function($key) use ($db) {
			        return $db->quote($key);
			    }, $extKeys);
			    
			    $extlist = "ct_code NOT IN(" . implode(',', $extKeysQuoted) . ")";
			}
			
			$sql = $db->query("SELECT * FROM $db_core WHERE $extlist AND ct_plug = $ctplug");
			while ($row = $sql->fetch()) {
			    if ($type === 'module' && in_array($row['ct_code'], ['admin', 'message', 'users'])) {
			        continue;
			    }
			    
			    $extensions[$row['ct_code']] = [
				    'Code' => $row['ct_code'],
				    'Name' => $row['ct_title'],
				    'Version' => $row['ct_version'],
				    'Category' => 'misc-ext',
				    'NotFound' => true,
			    ];
			}
			$sql->closeCursor();

			if ($type == 'plug' && $sort == 'cat') {
				if (function_exists('cot_extension_catcmp')) {
				    uasort($extensions, 'cot_extension_catcmp');
				} else {
				    // Fallback sort if function doesn't exist
				    ksort($extensions);
				}
			} else {
				ksort($extensions);
			}

			$cnt_extp = count($extensions);
			$cnt_parts = 0;

			$prev_cat = '';

			/* === Hook - Part1 : Set === */
			$extp = cot_getextplugins("admin.extensions.$type.list.loop");
			/* ===== */

            $i = 1;
			foreach ($extensions as $code => $info) {
				if (
                    $sort === 'cat'
                    && $type === ExtensionsDictionary::TYPE_PLUGIN
                    && isset($info['Category'])
                    && $prev_cat !== $info['Category']
                ) {
				    $catTitle = isset($info['Category']) ? $info['Category'] : '';
				    if (!empty($catTitle) && isset(Cot::$L['ext_cat_' . $catTitle])) {
				        $catTitle = Cot::$L['ext_cat_' . $catTitle];
				    }
				    
					// Render category heading
					$t->assign('ADMIN_EXTENSIONS_CAT_TITLE', $catTitle);
					$t->parse('MAIN.DEFAULT.SECTION.ROW.ROW_CAT');
					// Assign a new one
					$prev_cat = $info['Category'];
				}

				$exists = !isset($info['NotFound']);
                $isInstalled = $extensionsService->isInstalled($code, $type, true);

				if (!empty($info['Error'])) {
					$t->assign([
						'ADMIN_EXTENSIONS_X_ERR' => $code,
						'ADMIN_EXTENSIONS_ERROR_MSG' => $info['Error'],
					]);
					$t->parse('MAIN.DEFAULT.ROW.ROW_ERROR_EXT');
					$t->parse('MAIN.DEFAULT.ROW');

				} else {
					$totalactive = isset($totalactives[$code]) ? $totalactives[$code] : 0;
					$totalinstalled = isset($totalinstalleds[$code]) ? $totalinstalleds[$code] : 0;

					$cnt_parts += $totalinstalled;

					$part_status = 3;
					$partscount = '?';
					
					if (isset($installed_vers[$code])) {
						$partscount = $totalinstalled;
						
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

					$totalConfig = 0;
					if (isset($totalConfigs[$type]) && isset($totalConfigs[$type][$code])) {
					    $totalConfig = $totalConfigs[$type][$code];
					}

					$ent_code = isset($cfgentries[$code]) ? $cfgentries[$code] : 0;

					$hasStructure = is_array($extensionsWithStructure) && in_array($code, $extensionsWithStructure, true);

					if ($type === ExtensionsDictionary::TYPE_MODULE) {
						$arg = 'mod';
					} else {
						$arg = 'pl';
					}

					$installed_ver = isset($installed_vers[$code]) ? $installed_vers[$code] : '';

                    $params = cot_get_extensionparams($code, $type === ExtensionsDictionary::TYPE_MODULE);

                    $rightsUrl = null;

					Cot::$L['info_name'] = Cot::$L['info_desc'] = Cot::$L['info_notes'] = '';
                    $langFile = cot_langfile($code, $type);
					if (!empty($langFile) && file_exists($langFile)) {
						include $langFile;
					}

					$extAssign = [
						'ADMIN_EXTENSIONS_DETAILS_URL' => cot_url('admin', "m=extensions&a=details&$arg=$code"),
						'ADMIN_EXTENSIONS_NAME' => htmlspecialchars($params['name'] ?? ''),
						'ADMIN_EXTENSIONS_TYPE' => $type === ExtensionsDictionary::TYPE_MODULE
                            ? (isset(Cot::$L['Module']) ? Cot::$L['Module'] : 'Module')
                            : (isset(Cot::$L['Plugin']) ? Cot::$L['Plugin'] : 'Plugin'),
						'ADMIN_EXTENSIONS_CODE_X' => $code,
						'ADMIN_EXTENSIONS_NOTES' => isset($params['notes']) ? $params['notes'] : '',
						'ADMIN_EXTENSIONS_DESCRIPTION' => isset($params['desc']) ? $params['desc'] : '',
                        'ADMIN_EXTENSIONS_ICON' => isset($params['icon']) ? $params['icon'] : '',
						'ADMIN_EXTENSIONS_EDIT_URL' => cot_url('admin', "m=config&n=edit&o=$type&p=$code"),
						'ADMIN_EXTENSIONS_TOTALCONFIG' => $totalConfig,
						'ADMIN_EXTENSIONS_PARTSCOUNT' => $partscount,
						'ADMIN_EXTENSIONS_STATUS' => isset($status[$part_status]) ? $status[$part_status] : '',
						'ADMIN_EXTENSIONS_VERSION' => isset($info['Version']) ? $info['Version'] : '',
						'ADMIN_EXTENSIONS_VERSION_INSTALLED' => $installed_ver,
						'ADMIN_EXTENSIONS_VERSION_COMPARE' => !empty($installed_ver) && isset($info['Version']) ? 
						    version_compare($info['Version'], $installed_ver) : 0,
						'ADMIN_EXTENSIONS_RIGHTS_URL' => $extensionsService->getRightsUrl($code, $type),
						'ADMIN_EXTENSIONS_ADMIN_URL' => $extensionsService->getAdminPageUrl($code, $type),
						'ADMIN_EXTENSIONS_JUMPTO_URL' => $extensionsService->getPublicPageUrl($code, $type),
						'ADMIN_EXTENSIONS_STRUCTURE_URL' => $hasStructure
                            ? cot_url('admin', ['m' => 'structure', 'n' => $code])
                            : '',
					];
					
					$t->assign($extAssign);
					
                    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                        $legacyAssign = [
                            // @deprecated For backward compatibility. Will be removed in future releases
                            'ADMIN_EXTENSIONS_ICO' => isset($params['legacyIcon']) ? $params['legacyIcon'] : '',

                            /** @deprecated in 0.9.26 */
                            'ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS' => $extensionsService->getAdminPageUrl($code, $type),
                            'ADMIN_EXTENSIONS_JUMPTO_URL_STRUCT' => $hasStructure
                                ? cot_url('admin', ['m' => 'structure', 'n' => $code])
                                : '',
                        ];
                        
                        $t->assign($legacyAssign);
                    }

					/* === Hook - Part2 : Include === */
					foreach ($extp as $pl) {
						include $pl;
					}
					/* ===== */
					$t->parse('MAIN.DEFAULT.SECTION.ROW');
				}

				$i++;
			}
			
			$sectionTitle = '';
			if ($type == 'module') {
			    $sectionTitle = isset(Cot::$L['Modules']) ? Cot::$L['Modules'] : 'Modules';
			} else {
			    $sectionTitle = isset(Cot::$L['Plugins']) ? Cot::$L['Plugins'] : 'Plugins';
			}
			
			$t->assign([
				'ADMIN_EXTENSIONS_SECTION_TITLE' => $sectionTitle,
				'ADMIN_EXTENSIONS_CNT_EXTP' => $cnt_extp
			]);
			$t->parse('MAIN.DEFAULT.SECTION');
		}

		$t->parse('MAIN.DEFAULT');
	break;
}

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.extensions.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminMain = $t->text('MAIN');
