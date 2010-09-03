<?php
/**
 * Extension administration
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

sed_require_api('auth');

$t = new XTemplate(sed_skinfile('admin.extensions'));

$adminpath[] = array (sed_url('admin', 'm=extensions'), $L['Extensions']);

$pl = sed_import('pl', 'G', 'ALP');
$mod = sed_import('mod', 'G', 'ALP');
$part = sed_import('part', 'G', 'ALP');
$ko = sed_import('ko', 'G', 'BOL');

if (empty($mod))
{
	if (empty($pl))
	{
		sed_die();
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
$found_txt[0] = $R['admin_code_missing'];
$found_txt[1] = $R['admin_code_present'];
unset($disp_errors);

/* === Hook === */
foreach (sed_getextplugins('admin.extensions.first') as $pl)
{
	include $pl;
}
/* ===== */

switch($a)
{
	/* =============== */
	case 'details':
	/* =============== */
		$extplugin_info = $dir . '/' . $code . '/' . $code . '.setup.php';
		switch($b)
		{
			case 'pause':
				if ($is_module)
				{
					sed_module_pause($code);
				}
				sed_plugin_pause($code);
				$cot_cache && $cot_cache->db->remove('sed_plugins', 'system');
				sed_message('adm_paused');
			break;
			case 'unpause':
				if ($is_module)
				{
					sed_module_resume($code);
				}
				sed_plugin_resume($code);
				$cot_cache && $cot_cache->db->remove('sed_plugins', 'system');
				sed_message('adm_running');
			break;
			case 'pausepart':
				sed_plugin_pause($code, $part);
				$cot_cache && $cot_cache->db->remove('sed_plugins', 'system');
				sed_message('adm_partstopped');
			break;
			case 'unpausepart':
				sed_plugin_resume($code, $part);
				$cot_cache && $cot_cache->db->remove('sed_plugins', 'system');
				sed_message('adm_partrunning');
			break;
		}
		if(file_exists($extplugin_info))
		{
			$info = sed_infoget($extplugin_info, 'COT_EXT');
			$adminpath[] = array(sed_url('admin', "m=extensions&a=details&$arg=$code"), $info['Name']." ($code)");

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

			$isinstalled = $is_module ? sed_module_installed($code) : sed_plugin_installed($code);

			$sql = sed_sql_query("SELECT COUNT(*) FROM $db_config WHERE config_owner='$type' AND config_cat='$code'");
			$totalconfig = sed_sql_result($sql);

			$info['Auth_members'] = sed_auth_getvalue($info['Auth_members']);
			$info['Lock_members'] = sed_auth_getvalue($info['Lock_members']);
			$info['Auth_guests'] = sed_auth_getvalue($info['Auth_guests']);
			$info['Lock_guests'] = sed_auth_getvalue($info['Lock_guests']);

			if (count($parts) > 0)
			{
				sort($parts);
				/* === Hook - Part1 : Set === */
				$extp = sed_getextplugins('admin.extensions.details.part.loop');
				/* ===== */
				foreach ($parts as $i => $x)
				{
					$extplugin_file = $dir . '/' . $code . '/' . $x;
					$info_file = sed_infoget($extplugin_file, 'COT_EXT');
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
						$sql = sed_sql_query("SELECT pl_active, pl_id FROM $db_plugins
							WHERE pl_code='$code' AND pl_part='".$info_part."' LIMIT 1");

						if($row = sed_sql_fetcharray($sql))
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
								'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_file['Part']
							));
							$t->parse('MAIN.DETAILS.ROW_ERROR_TAGS');
						}
						else
						{
							$line = explode(':', $info_file['Tags']);
							$line[0] = trim($line[0]);
							$tags = explode(',', $line[1]);
							$listtags = $line[0].' :<br />';
							foreach($tags as $k => $v)
							{
								if(mb_substr(trim($v), 0, 1) == '{')
								{
									$listtags .= $v.' : ';
									$found = sed_stringinfile('./skins/'.$cfg['defaultskin'].'/'.$line[0], trim($v));
									$listtags .= $found_txt[$found].'<br />';
								}
								else
								{
									$listtags .= $v.'<br />';
								}
							}

							$t->assign(array(
								'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
								'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_file['Part'],
								'ADMIN_EXTENSIONS_DETAILS_ROW_FILE' => $line[0].' :<br />',
								'ADMIN_EXTENSIONS_DETAILS_ROW_LISTTAGS' => $listtags,
								//'ADMIN_EXTENSIONS_DETAILS_ROW_TAGS_ODDEVEN' => sed_build_oddeven($ii)
							));
							$t->parse('MAIN.DETAILS.ROW_TAGS');
						}

						$t->assign(array(
							'ADMIN_EXTENSIONS_DETAILS_ROW_I_1' => $i+1,
							'ADMIN_EXTENSIONS_DETAILS_ROW_PART' => $info_file['Part'],
							'ADMIN_EXTENSIONS_DETAILS_ROW_FILE' => $info_file['File'],
							'ADMIN_EXTENSIONS_DETAILS_ROW_HOOKS' => $info_file['Hooks'],
							'ADMIN_EXTENSIONS_DETAILS_ROW_ORDER' => $info_file['Order'],
							'ADMIN_EXTENSIONS_DETAILS_ROW_STATUS' => $status[$info_file['Status']],
							//'ADMIN_EXTENSIONS_DETAILS_ROW_PART_ODDEVEN' => sed_build_oddeven($ii)
						));

						if ($info_file['Status'] == 3)
						{
							$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_NOTINSTALLED');
						}
						if ($info_file['Status'] != 3 && $row['pl_active'] == 1)
						{
							$t->assign('ADMIN_EXTENSIONS_DETAILS_ROW_PAUSEPART_URL',
								sed_url('admin', "m=plug&a=details&$arg=$code&b=pausepart&part=".$row['pl_id']));
							$t->parse('MAIN.DETAILS.ROW_PART.ROW_PART_PAUSE');
						}
						if ($info_file['Status'] != 3 && $row['pl_active'] == 0)
						{
							$t->assign('ADMIN_EXTENSIONS_DETAILS_ROW_UNPAUSEPART_URL',
								sed_url('admin', "m=plug&a=details&$arg=$code&b=unpausepart&part=".$row['pl_id']));
							$t->parse('MAIN.DETAILS.ROW_PART.ROW_PARTUNPAUSE');
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

			$t->assign(array(
				'ADMIN_EXTENSIONS_NAME' => $info['Name'],
				'ADMIN_EXTENSIONS_CODE' => $info['Code'],
				'ADMIN_EXTENSIONS_DESCRIPTION' => $info['Description'],
				'ADMIN_EXTENSIONS_VERSION' => $info['Version'],
				'ADMIN_EXTENSIONS_DATE' => $info['Date'],
				'ADMIN_EXTENSIONS_CONFIG_URL' => sed_url('admin', "m=config&n=edit&o=$type&p=$code"),
				'ADMIN_EXTENSIONS_TOTALCONFIG' => $totalconfig,
				'ADMIN_EXTENSIONS_RIGHTS' => sed_url('admin', "m=rightsbyitem&ic=$type&io=$code"),
				'ADMIN_EXTENSIONS_ADMRIGHTS_AUTH_GUESTS' => sed_auth_getmask($info['Auth_guests']),
				'ADMIN_EXTENSIONS_AUTH_GUESTS' => $info['Auth_guests'],
				'ADMIN_EXTENSIONS_ADMRIGHTS_LOCK_GUESTS' => sed_auth_getmask($info['Lock_guests']),
				'ADMIN_EXTENSIONS_LOCK_GUESTS' => $info['Lock_guests'],
				'ADMIN_EXTENSIONS_ADMRIGHTS_AUTH_MEMBERS' => sed_auth_getmask($info['Auth_members']),
				'ADMIN_EXTENSIONS_AUTH_MEMBERS' => $info['Auth_members'],
				'ADMIN_EXTENSIONS_ADMRIGHTS_LOCK_MEMBERS' => sed_auth_getmask($info['Lock_members']),
				'ADMIN_EXTENSIONS_LOCK_MEMBERS' => $info['Lock_members'],
				'ADMIN_EXTENSIONS_AUTHOR' => $info['Author'],
				'ADMIN_EXTENSIONS_COPYRIGHT' => $info['Copyright'],
				'ADMIN_EXTENSIONS_NOTES' => sed_parse($info['Notes'], 1, 0, 0),
				'ADMIN_EXTENSIONS_INSTALL_URL' => sed_url('admin', "m=plug&a=edit&$arg=$code&b=install"),
				'ADMIN_EXTENSIONS_INSTALL_KO_URL' => sed_url('admin', "m=plug&a=edit&$arg=$code&b=install&ko=1"),
				'ADMIN_EXTENSIONS_UNINSTALL' => sed_url('admin', "m=plug&a=edit&$arg=$code&b=uninstall"),
				'ADMIN_EXTENSIONS_UNINSTALL_KO_URL' => sed_url('admin', "m=plug&a=edit&$arg=$code&b=uninstall&ko=1"),
				'ADMIN_EXTENSIONS_PAUSE_URL' => sed_url('admin', "m=plug&a=details&$arg=$code&b=pause"),
				'ADMIN_EXTENSIONS_UNPAUSE_URL' => sed_url('admin', "m=plug&a=details&$arg=$code&b=unpause")
			));
			/* === Hook  === */
			foreach (sed_getextplugins('admin.extensions.details') as $pl)
			{
				include $pl;
			}
			/* ===== */
			$t->parse('MAIN.DETAILS');
		}
		else
		{
			sed_die();
		}
	break;
	/* =============== */
	case 'edit':
	/* =============== */
		switch($b)
		{
			case 'install':
				$sql = sed_sql_query("DELETE FROM $db_plugins WHERE pl_code='$code'");
				$show_sql_affectedrows1 = sed_sql_affectedrows();

				if(!$ko)
				{
					$sql = sed_sql_query("DELETE FROM $db_config WHERE config_owner='plug' and config_cat='$code'");
					$show_sql_affectedrows2 = sed_sql_affectedrows();
				}

				$extplugin_info = $cfg['plugins_dir'].'/'.$code.'/'.$code.'.setup.php';

				if(file_exists($extplugin_info))
				{
					$extplugin_info_exists = TRUE;
					$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');
					$adminpath[] = array(sed_url('admin', 'm=plug&a=details&pl='.$code), $info['Name']." ($code)");

					$handle = opendir($cfg['plugins_dir'].'/'.$code);
					$setupfile = $code.'.setup.php';
					while($f = readdir($handle))
					{
						if($f != '.' && $f != '..' && $f != $setupfile && mb_strtolower(mb_substr($f, mb_strrpos($f, '.') + 1, 4)) == 'php')
						{
							$parts[] = $f;

							$t->assign(array(
								'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_FOUND_F' => $f
							));
							$t->parse('MAIN.EDIT.INSTALL.ROW_PARTS_FOUND');
						}
					}
					closedir($handle);

					if(count($parts) > 0)
					{
						while(list($i, $x) = each($parts))
						{
							$extplugin_file = $cfg['plugins_dir'].'/'.$code.'/'.$x;
							$info_part = sed_infoget($extplugin_file, 'SED_EXTPLUGIN');

							if(empty($info_part['Error']))
							{
								$sql = sed_sql_query("INSERT into $db_plugins (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active ) VALUES ('".$info_part['Hooks']."', '".$info_part['Code']."', '".sed_sql_prep($info_part['Part'])."', '".sed_sql_prep($info['Name'])."', '".$info_part['File']."',  ".(int)$info_part['Order'].", 1)");

								$msg = ($sql) ? $L['adm_installed'] : $L['Error'];
							}
							else
							{
								$msg = $L['Error'];
							}

							$t->assign(array(
								'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_INSTALLING_X' => $x,
								'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_INSTALLING_MSG' => $msg
							));
							$t->parse('MAIN.EDIT.INSTALL.ROW_PARTS_INSTALLING');
						}
					}

					$info_cfg = sed_infoget($extplugin_info, 'SED_EXTPLUGIN_CONFIG');

					if(empty($info_cfg['Error']))
					{
						$j = 0;
						foreach($info_cfg as $i => $x)
						{
							$line = explode(':', $x);

							if(is_array($line) && !empty($line[1]) && !empty($i))
							{
								$j++;
								switch($line[1])
								{
									case 'string':
										$line['Type'] = 1;
									break;
									case 'select':
										$line['Type'] = 2;
									break;
									case 'radio':
										$line['Type'] = 3;
									break;
									default:
										$line['Type'] = 0;
									break;
								}

								if(!$ko)
								{
									$sql = sed_sql_query("INSERT into $db_config (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_variants, config_text)
										VALUES ('plug', '".$code."', ".$line[0].", '".$i."', ".(int)$line['Type'].", '".sed_sql_prep($line[3])."', '".sed_sql_prep($line[3])."', '".sed_sql_prep($line[2])."', '".sed_sql_prep($line[4])."')");
								}
								elseif ($ko)
								{
									$sqltmp = sed_sql_query("SELECT COUNT(*) FROM $db_config WHERE config_owner='plug' AND config_cat='$code' AND config_name='".$line[0]."' ");
									$if = sed_sql_result($sqltmp, 0, "COUNT(*)");

									$sql = (!$if) ? sed_sql_query("INSERT into $db_config (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_variants, config_text)
										VALUES ('plug', '".$code."', ".$line[0].", '".$i."', ".(int)$line['Type'].", '".sed_sql_prep($line[3])."', '".sed_sql_prep($line[3])."', '".sed_sql_prep($line[2])."', '".sed_sql_prep($line[4])."')") : '';
								}

								$t->assign(array(
									'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_CFG_J' => $j,
									'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_CFG_I' => $i,
									'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_CFG_LINE' => $line[1]
								));
								$t->parse('MAIN.EDIT.INSTALL.ROW_PARTS_CFG.ROW_PARTS_CFG_ENTRY');
							}
							$totalconfig++;
						}

						$t->assign(array(
							'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_PARTS_CFG_TOTALCONFIG' => $totalconfig
						));
						$t->parse('MAIN.EDIT.INSTALL.ROW_PARTS_CFG');
					}
					else
					{
						$t->parse('MAIN.EDIT.INSTALL.ROW_PARTS_CFG_ERROR');
					}
				}

				if(!$ko)
				{
					$sql = sed_sql_query("DELETE FROM $db_auth WHERE auth_code='plug' and auth_option='$code'");
					$show_sql_affectedrows3 = sed_sql_affectedrows();
				}

				foreach($sed_groups as $k => $v)
				{
					$comment = $L['adm_plugsetup'];

					if($v['id'] == 1 || $v['id'] == 2)
					{
						$ins_auth = sed_auth_getvalue($info['Auth_guests']);
						$ins_lock = sed_auth_getvalue($info['Lock_guests']);

						if($ins_auth > 128 || $ins_lock < 128)
						{
							$ins_auth = ($ins_auth > 127) ? $ins_auth - 128 : $ins_auth;
							$ins_lock = 128;
							$comment = $L['adm_override_guests'];
						}
					}
					elseif($v['id'] == 3)
					{
						$ins_auth = 0;
						$ins_lock = 255;
						$comment = $L['adm_override_banned'];
					}
					elseif($v['id'] == 5)
					{
						$ins_auth = 255;
						$ins_lock = 255;
						$comment = $L['adm_override_admins'];
					}
					else
					{
						$ins_auth = sed_auth_getvalue($info['Auth_members']);
						$ins_lock = sed_auth_getvalue($info['Lock_members']);
					}

					if(!$ko)
					{
						$sql = sed_sql_query("INSERT into $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid) VALUES (".(int)$v['id'].", 'plug', '$code', ".(int)$ins_auth.", ".(int)$ins_lock.", ".(int)$usr['id'].")");
					}
					elseif(!$ko)
					{
						$sqltmp = sed_sql_query("SELECT COUNT(*) FROM $db_auth WHERE auth_code='plug' AND auth_groupid='".(int)$v['id']."' AND auth_option='$code' ");
						$if = sed_sql_result($sqltmp, 0, "COUNT(*)");

						$sql = (!$if) ? sed_sql_query("INSERT into $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid) VALUES (".(int)$v['id'].", 'plug', '$code', ".(int)$ins_auth.", ".(int)$ins_lock.", ".(int)$usr['id'].")") : '';
					}

					$t->assign(array(
						'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_ID' => $v['id'],
						'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_TITLE' => $sed_groups[$v['id']]['title'],
						'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_AUTH' => sed_auth_getmask($ins_auth),
						'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_LOCK' => sed_auth_getmask($ins_lock),
						'ADMIN_EXTENSIONS_EDIT_INSTALL_ROW_RIGHTS_COMMENT' => $comment
					));
					$t->parse('MAIN.EDIT.INSTALL.ROW_RIGHTS');
				}
				$sql = sed_sql_query("UPDATE $db_users SET user_auth='' WHERE 1");
				$show_sql_affectedrows4 = sed_sql_affectedrows();

				$extplugin_install = $cfg['plugins_dir']."/".$code."/".$code.".install.php";
				$action = 'install';
				include_once($extplugin_info);

				sed_auth_reorder();
				$cot_cache && $cot_cache->db->remove('sed_plugins', 'system');

				$t->assign(array(
					'ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS1' => $show_sql_affectedrows1,
					'ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS2' => $show_sql_affectedrows2,
					'ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS3' => $show_sql_affectedrows3,
					'ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS4' => $show_sql_affectedrows4,
					'ADMIN_EXTENSIONS_EDIT_EXTPLUGIN_INFO' => include_once($extplugin_info),
					'ADMIN_EXTENSIONS_EDIT_LOG' => $edit_log,
					'ADMIN_EXTENSIONS_EDIT_CONTINUE_URL' => sed_url('admin', "m=plug&a=details&pl=".$code)
				));
				/* === Hook  === */
				foreach (sed_getextplugins('admin.extensions.install.tags') as $pl)
				{
					include $pl;
				}
				/* ===== */
				$t->parse('MAIN.EDIT.INSTALL');
				$t->parse('MAIN.EDIT');
			break;
			case 'uninstall':
				$extplugin_info = $cfg['plugins_dir'].'/'.$code.'/'.$code.'.setup.php';
				$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');
				$adminpath[] = array(sed_url('admin', 'm=plug&a=details&pl='.$code), $info['Name']." ($code)");
				$sql = sed_sql_query("DELETE FROM $db_plugins WHERE pl_code='$code'");
				$show_sql_affectedrows1 = sed_sql_affectedrows();

				if(!$ko)
				{
					$sql = sed_sql_query("DELETE FROM $db_config WHERE config_owner='plug' AND config_cat='$code'");
					$show_sql_affectedrows2 = sed_sql_affectedrows();
					$sql = sed_sql_query("DELETE FROM $db_auth WHERE auth_code='plug' and auth_option='$code'");
					$show_sql_affectedrows3 = sed_sql_affectedrows();
				}

				$sql = sed_sql_query("UPDATE $db_users SET user_auth='' WHERE 1");
				$show_sql_affectedrows4 = sed_sql_affectedrows();
				$cot_cache && $cot_cache->db->remove('sed_plugins', 'system');

				$extplugin_uninstall = $cfg['plugins_dir'].'/'.$code.'/'.$code.'.uninstall.php';
				$action = 'uninstall';
				include_once($extplugin_info);

				$t->assign(array(
					'ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS1' => $show_sql_affectedrows1,
					'ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS2' => $show_sql_affectedrows2,
					'ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS3' => $show_sql_affectedrows3,
					'ADMIN_EXTENSIONS_EDIT_AFFECTEDROWS4' => $show_sql_affectedrows4,
					'ADMIN_EXTENSIONS_EDIT_EXTPLUGIN_INFO' => include_once($extplugin_info),
					'ADMIN_EXTENSIONS_EDIT_LOG' => $edit_log,
					'ADMIN_EXTENSIONS_EDIT_CONTINUE_URL' => sed_url('admin', 'm=plug&a=details&pl='.$code)
				));
				/* === Hook  === */
				foreach (sed_getextplugins('admin.extensions.uninstall.tags') as $pl)
				{
					include $pl;
				}
				/* ===== */
				$t->parse('MAIN.EDIT.UNINSTALL');
				$t->parse('MAIN.EDIT');
			break;
			default:
				sed_die();
			break;
		}
	break;
	default:
		$sql = sed_sql_query("SELECT DISTINCT(config_cat), COUNT(*) FROM $db_config WHERE config_owner='plug' GROUP BY config_cat");
		while($row = sed_sql_fetcharray($sql))
		{
			$cfgentries[$row['config_cat']] = $row['COUNT(*)'];
		}

		$handle = opendir($cfg['plugins_dir']);
		while($f = readdir($handle))
		{
			if(is_dir($cfg['plugins_dir'].'/'.$f) && $f[0] !='.' && $f != 'code')
			{
				// Check for plugin .php files, otherwise it's inconsistent
				$is_plug = false;
				$dp = opendir($cfg['plugins_dir'].'/'.$f);
				while($pf = readdir($dp))
				{
					if(preg_match('#^'.preg_quote($f).'.*\.php$#', $pf))
					{
						$is_plug = true;
						break;
					}
				}
				closedir($dp);
				if($is_plug)
				{
					$extplugins[] = $f;
				}
			}
		}
		closedir($handle);
		sort($extplugins);
		$cnt_extp = count($extplugins);
		$cnt_parts = 0;

		$plg_standalone = array();
		$sql3 = sed_sql_query("SELECT pl_code FROM $db_plugins WHERE pl_hook='standalone'");
		while($row3 = sed_sql_fetcharray($sql3))
		{
			$plg_standalone[$row3['pl_code']] = TRUE;
		}

		$plg_tools = array();
		$sql3 = sed_sql_query("SELECT pl_code FROM $db_plugins WHERE pl_hook='tools'");
		while($row3 = sed_sql_fetcharray($sql3))
		{
			$plg_tools[$row3['pl_code']] = TRUE;
		}
		/* === Hook - Part1 : Set === */
		$extp = sed_getextplugins('admin.extensions.list.loop');
		/* ===== */
		while(list($i, $x) = each($extplugins))
		{
			$extplugin_info = $cfg['plugins_dir'].'/'.$x.'/'.$x.'.setup.php';
			if(file_exists($extplugin_info))
			{
				$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');

				if(!empty($info['Error']))
				{
					$t->assign(array(
						'ADMIN_EXTENSIONS_X_ERR' => $x,
						'ADMIN_EXTENSIONS_ERROR_MSG' => $info['Error']
					));
					$t->parse('MAIN.DEFAULT.ROW.ROW_ERROR_PLUG');
					$t->parse('MAIN.DEFAULT.ROW');
				}
				else
				{
					$sql1 = sed_sql_query("SELECT SUM(pl_active) FROM $db_plugins WHERE pl_code='$x'");
					$sql2 = sed_sql_query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code='$x'");
					$totalactive = sed_sql_result($sql1, 0, "SUM(pl_active)");
					$totalinstalled = sed_sql_result($sql2, 0, "COUNT(*)");
					$cnt_parts += $totalinstalled;

					if($totalinstalled == 0)
					{
						$part_status = 3;
						$info['Partscount'] = '?';
					}
					else
					{
						$info['Partscount'] = $totalinstalled;
						if($totalinstalled > $totalactive && $totalactive > 0)
						{
							$part_status = 2;
						}
						elseif($totalactive == 0)
						{
							$part_status = 0;
						}
						else
						{
							$part_status = 1;
						}
					}

					$ifthistools = $plg_tools[$info['Code']];
					$ent_code = $cfgentries[$info['Code']];
					$if_plg_standalone = $plg_standalone[$info['Code']];

					$t->assign(array(
						'ADMIN_EXTENSIONS_DETAILS_URL' => sed_url('admin', 'm=plug&a=details&pl='.$info['Code']),
						'ADMIN_EXTENSIONS_NAME' => $info['Name'],
						'ADMIN_EXTENSIONS_CODE_X' => $x,
						'ADMIN_EXTENSIONS_EDIT_URL' => sed_url('admin', 'm=config&n=edit&o=plug&p='.$info['Code']),
						'ADMIN_EXTENSIONS_PARTSCOUNT' => $info['Partscount'],
						'ADMIN_EXTENSIONS_STATUS' => $status[$part_status],
						'ADMIN_EXTENSIONS_RIGHTS_URL' => sed_url('admin', 'm=rightsbyitem&ic=plug&io='.$info['Code']),
						'ADMIN_EXTENSIONS_JUMPTO_URL_TOOLS' => sed_url('admin', 'm=tools&p='.$info['Code']),
						'ADMIN_EXTENSIONS_JUMPTO_URL' => sed_url('plug', 'e='.$info['Code']),
						'ADMIN_EXTENSIONS_ODDEVEN' => sed_build_oddeven($ii)
					));
					/* === Hook - Part2 : Include === */
					foreach ($extp as $pl)
					{
						include $pl;
					}
					/* ===== */
					$t->parse('MAIN.DEFAULT.ROW');
				}
			}
			else
			{
				$t->assign(array(
					'ADMIN_EXTENSIONS_X' => $x
				));
				$t->parse('MAIN.DEFAULT.ROW_ERROR');
			}
		}

		if($o == 'code')
		{
			$sql = sed_sql_query("SELECT * FROM $db_plugins ORDER BY pl_code ASC, pl_hook ASC, pl_order ASC");
		}
		else
		{
			$sql = sed_sql_query("SELECT * FROM $db_plugins ORDER BY pl_hook ASC, pl_code ASC, pl_order ASC");
		}

		while($row = sed_sql_fetcharray($sql))
		{
			$t->assign(array(
				'ADMIN_EXTENSIONS_HOOK' => $row['pl_hook'],
				'ADMIN_EXTENSIONS_CODE' => $row['pl_code'],
				'ADMIN_EXTENSIONS_ORDER' => $row['pl_order'],
				'ADMIN_EXTENSIONS_ACTIVE' => $sed_yesno[$row['pl_active']]
			));
			$t->parse('MAIN.DEFAULT.HOOKS');
		}

		$t->assign(array(
			'ADMIN_EXTENSIONS_CNT_EXTP' => $cnt_extp,
			'ADMIN_EXTENSIONS_CNT_HOOK' => sed_sql_numrows($sql)
		));
		$t->parse('MAIN.DEFAULT');
	break;
}

if (!empty($code) && $b == 'install' && $totalconfig > 0)
{
	$t->assign('ADMIN_EXTENSIONS_CONFIG_URL', sed_url('admin', 'm=config&n=edit&o=plug&p='.$code));
	$t->parse('MAIN.CONFIG_URL');
}

if (sed_check_messages())
{
	$t->assign('ADMIN_EXTENSIONS_ADMINWARNINGS', sed_implode_messages());
	$t->parse('MAIN.MESSAGE');
}
sed_clear_messages();

/* === Hook  === */
foreach (sed_getextplugins('admin.extensions.tags') as $pl)
{
	include $pl;
}
/* ===== */
$t->parse('MAIN');
if (SED_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>