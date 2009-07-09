<?php
/**
 * Administration panel
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

$t = new XTemplate(sed_skinfile('admin.plug.inc', false, true));

$adminpath[] = array (sed_url('admin', 'm=plug'), $L['Plugins']);

$pl = sed_import('pl', 'G', 'ALP');
$part = sed_import('part', 'G', 'ALP');
$ko = sed_import('ko', 'G', 'BOL');
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

$status[0] = '<span style="color:#5882AC;font-weight:bold;">'.$L['adm_paused'].'</span>';
$status[1] = '<span style="color:#739E48;font-weight:bold;">'.$L['adm_running'].'</span>';
$status[2] = '<span style="color:#A78731;font-weight:bold;">'.$L['adm_partrunning'].'</span>';
$status[3] = '<span style="color:#AC5866;font-weight:bold;">'.$L['adm_notinstalled'].'</span>';
$found_txt[0] = '<span style="color:#AC5866;font-weight:bold;">'.$L['adm_missing'].'</span>';
$found_txt[1] = '<span style="color:#739E48;font-weight:bold;">'.$L['adm_present'].'</span>';
unset($disp_errors);

/* === Hook === */
$extp = sed_getextplugins('admin.plug.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

switch($a)
{
	/* =============== */
	case 'details':
	/* =============== */
		$extplugin_info = $cfg['plugins_dir']."/".$pl."/".$pl.".setup.php";
		switch($b)
		{
			case 'pause':
				$sql = sed_sql_query("UPDATE $db_plugins SET pl_active=0 WHERE pl_code='$pl'");
				sed_cache_clearall();
				$adminwarnings = $L['adm_paused'];
			break;
			case 'unpause':
				$sql = sed_sql_query("UPDATE $db_plugins SET pl_active=1 WHERE pl_code='$pl'");
				sed_cache_clearall();
				$adminwarnings = $L['adm_running'];
			break;
			case 'pausepart':
				$sql = sed_sql_query("UPDATE $db_plugins SET pl_active=0 WHERE pl_code='$pl' AND pl_id='$part'");
				sed_cache_clearall();
				$adminwarnings = $L['adm_partstopped'];
			break;
			case 'unpausepart':
				$sql = sed_sql_query("UPDATE $db_plugins SET pl_active=1 WHERE pl_code='$pl' AND pl_id='$part'");
				sed_cache_clearall();
				$adminwarnings = $L['adm_partrunning'];
			break;
		}
		if(file_exists($extplugin_info))
		{
			$extplugin_info = $cfg['plugins_dir']."/".$pl."/".$pl.".setup.php";
			$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');
			$adminpath[] = array(sed_url('admin', 'm=plug&a=details&pl='.$pl), $info['Name']." ($pl)");

			$handle = opendir($cfg['plugins_dir']."/".$pl);
			$setupfile = $pl.'.setup.php';
			while($f = readdir($handle))
			{
				if($f != "." && $f != ".." && $f != $setupfile && mb_strtolower(mb_substr($f, mb_strrpos($f, '.') + 1, 4)) == 'php')
				{
					$parts[] = $f;
				}
			}
			closedir($handle);
			if(is_array($parts))
			{
				sort($parts);
			}

			$sql = sed_sql_query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code='$pl' ");
			$isinstalled = sed_sql_result($sql, 0, "COUNT(*)");

			$sql = sed_sql_query("SELECT COUNT(*) FROM $db_config WHERE config_owner='plug' AND config_cat='$pl'");
			$totalconfig = sed_sql_result($sql, 0, "COUNT(*)");

			$info['Auth_members'] = sed_auth_getvalue($info['Auth_members']);
			$info['Lock_members'] = sed_auth_getvalue($info['Lock_members']);
			$info['Auth_guests'] = sed_auth_getvalue($info['Auth_guests']);
			$info['Lock_guests'] = sed_auth_getvalue($info['Lock_guests']);

			if(is_array($parts))
			{
				/* === Hook - Part1 : Set === */
				$extp = sed_getextplugins('admin.plug.details.part.loop');
				/* ===== */
				while(list($i, $x) = each($parts))
				{
					$extplugin_file = $cfg['plugins_dir']."/".$pl."/".$x;
					$info_file = sed_infoget($extplugin_file, 'SED_EXTPLUGIN');
					$inf_fil_err = false;

					if(!empty($info_file['Error']))
					{
						$t -> assign(array(
							"ADMIN_PLUG_DETAILS_ROW_X" => $x,
							"ADMIN_PLUG_DETAILS_ROW_ERROR" => $info_file['Error']
						));
						$t -> parse("PLUG.DETAILS.ROW_ERROR_PART");
					}
					else
					{
						$sql = sed_sql_query("SELECT pl_active, pl_id FROM $db_plugins WHERE pl_code='$pl' AND pl_part='".$info_file['Part']."' LIMIT 1");

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
							$t -> assign(array(
								"ADMIN_PLUG_DETAILS_ROW_I_1" => $i+1,
								"ADMIN_PLUG_DETAILS_ROW_PART" => $info_file['Part']
							));
							$t -> parse("PLUG.DETAILS.ROW_ERROR_TAGS");
						}
						else
						{
							$line = explode(":", $info_file['Tags']);
							$line[0] = trim($line[0]);
							$tags = explode(",", $line[1]);
							$listtags = $line[0]." :<br />";
							foreach($tags as $k => $v)
							{
								if(mb_substr(trim($v), 0, 1) == '{')
								{
									$listtags .= $v." : ";
									$found = sed_stringinfile('skins/'.$cfg['defaultskin'].'/'.$line[0], trim($v));
									$listtags .= $found_txt[$found]."<br />";
								}
								else
								{
									$listtags .= $v."<br />";
								}
							}

							$t -> assign(array(
								"ADMIN_PLUG_DETAILS_ROW_I_1" => $i+1,
								"ADMIN_PLUG_DETAILS_ROW_PART" => $info_file['Part'],
								"ADMIN_PLUG_DETAILS_ROW_FILE" => $line[0]." :<br />",
								"ADMIN_PLUG_DETAILS_ROW_LISTTAGS" => $listtags,
								//"ADMIN_PLUG_DETAILS_ROW_TAGS_ODDEVEN" => sed_build_oddeven($ii)
							));
							$t -> parse("PLUG.DETAILS.ROW_TAGS");
						}

						$t -> assign(array(
							"ADMIN_PLUG_DETAILS_ROW_I_1" => $i+1,
							"ADMIN_PLUG_DETAILS_ROW_PART" => $info_file['Part'],
							"ADMIN_PLUG_DETAILS_ROW_FILE" => $info_file['File'],
							"ADMIN_PLUG_DETAILS_ROW_HOOKS" => $info_file['Hooks'],
							"ADMIN_PLUG_DETAILS_ROW_ORDER" => $info_file['Order'],
							"ADMIN_PLUG_DETAILS_ROW_STATUS" => $status[$info_file['Status']],
							"ADMIN_PLUG_DETAILS_ROW_PAUSEPART_URL" => sed_url('admin', "m=plug&a=details&pl=".$pl."&b=pausepart&part=".$row['pl_id']),
							"ADMIN_PLUG_DETAILS_ROW_PAUSEPART_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=details&ajax=1&pl='.$pl.'&b=pausepart&part='.$row['pl_id'])."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
							"ADMIN_PLUG_DETAILS_ROW_UNPAUSEPART_URL" => sed_url('admin', "m=plug&a=details&pl=".$pl."&b=unpausepart&part=".$row['pl_id']),
							"ADMIN_PLUG_DETAILS_ROW_UNPAUSEPART_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=details&ajax=1&pl='.$pl.'&b=unpausepart&part='.$row['pl_id'])."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
							//"ADMIN_PLUG_DETAILS_ROW_PART_ODDEVEN" => sed_build_oddeven($ii)
						));
						/* === Hook - Part2 : Include === */
						if(is_array($extp))
						{
							foreach($extp as $k => $pl)
							{
								include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
							}
						}
						/* ===== */
						$t -> parse("PLUG.DETAILS.ROW_PART");
					}
				}
			}

			$t -> assign(array(
				"ADMIN_PLUG_NAME" => $info['Name'],
				"ADMIN_PLUG_CODE" => $info['Code'],
				"ADMIN_PLUG_DESCRIPTION" => $info['Description'],
				"ADMIN_PLUG_VERSION" => $info['Version'],
				"ADMIN_PLUG_DATE" => $info['Date'],
				"ADMIN_PLUG_CONFIG_URL" => sed_url('admin', "m=config&n=edit&o=plug&p=".$pl),
				"ADMIN_PLUG_TOTALCONFIG" => $totalconfig,
				"ADMIN_PLUG_RIGHTS" => sed_url('admin', "m=rightsbyitem&ic=plug&io=".$info['Code']),
				"ADMIN_PLUG_ADMRIGHTS_AUTH_GUESTS" => sed_build_admrights($info['Auth_guests']),
				"ADMIN_PLUG_AUTH_GUESTS" => $info['Auth_guests'],
				"ADMIN_PLUG_ADMRIGHTS_LOCK_GUESTS" => sed_build_admrights($info['Lock_guests']),
				"ADMIN_PLUG_LOCK_GUESTS" => $info['Lock_guests'],
				"ADMIN_PLUG_ADMRIGHTS_AUTH_MEMBERS" => sed_build_admrights($info['Auth_members']),
				"ADMIN_PLUG_AUTH_MEMBERS" => $info['Auth_members'],
				"ADMIN_PLUG_ADMRIGHTS_LOCK_MEMBERS" => sed_build_admrights($info['Lock_members']),
				"ADMIN_PLUG_LOCK_MEMBERS" => $info['Lock_members'],
				"ADMIN_PLUG_AUTHOR" => $info['Author'],
				"ADMIN_PLUG_COPYRIGHT" => $info['Copyright'],
				"ADMIN_PLUG_NOTES" => sed_parse($info['Notes'], 1, 0, 0),
				"ADMIN_PLUG_INSTALL_URL" => sed_url('admin', "m=plug&a=edit&pl=".$info['Code']."&b=install"),
				"ADMIN_PLUG_INSTALL_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=edit&ajax=1&pl='.$info['Code'].'&b=install')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
				"ADMIN_PLUG_INSTALL_KO_URL" => sed_url('admin', "m=plug&a=edit&pl=".$info['Code']."&b=install&ko=1"),
				"ADMIN_PLUG_INSTALL_KO_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=edit&ajax=1&pl='.$info['Code'].'&b=install&ko=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
				"ADMIN_PLUG_UNINSTALL" => sed_url('admin', "m=plug&a=edit&pl=".$info['Code']."&b=uninstall"),
				"ADMIN_PLUG_UNINSTALL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=edit&ajax=1&pl='.$info['Code'].'&b=uninstall')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
				"ADMIN_PLUG_UNINSTALL_KO_URL" => sed_url('admin', "m=plug&a=edit&pl=".$info['Code']."&b=uninstall&ko=1"),
				"ADMIN_PLUG_UNINSTALL_KO_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=edit&ajax=1&pl='.$info['Code'].'&b=uninstall&ko=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
				"ADMIN_PLUG_PAUSE_URL" => sed_url('admin', "m=plug&a=details&pl=".$info['Code']."&b=pause"),
				"ADMIN_PLUG_PAUSE_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=details&ajax=1&pl='.$info['Code'].'&b=pause')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
				"ADMIN_PLUG_UNPAUSE_URL" => sed_url('admin', "m=plug&a=details&pl=".$info['Code']."&b=unpause"),
				"ADMIN_PLUG_UNPAUSE_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=details&ajax=1&pl='.$info['Code'].'&b=unpause')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : ""
			));
			/* === Hook  === */
			$extp = sed_getextplugins('admin.plug.details');
			if(is_array($extp))
			{
				foreach($extp as $k => $pl)
				{
					include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
				}
			}
			/* ===== */
			$t -> parse("PLUG.DETAILS");
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
				$pl = (mb_strtolower($pl) == 'core') ? 'error' : $pl;
				$sql = sed_sql_query("DELETE FROM $db_plugins WHERE pl_code='$pl'");
				$show_sql_affectedrows1 = sed_sql_affectedrows();

				if(!$ko)
				{
					$sql = sed_sql_query("DELETE FROM $db_config WHERE config_owner='plug' and config_cat='$pl'");
					$show_sql_affectedrows2 = sed_sql_affectedrows();
				}

				$extplugin_info = $cfg['plugins_dir']."/".$pl."/".$pl.".setup.php";

				if(file_exists($extplugin_info))
				{
					$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');
					$adminpath[] = array(sed_url('admin', 'm=plug&a=details&pl='.$pl), $info['Name']." ($pl)");

					$handle = opendir($cfg['plugins_dir']."/".$pl);
					$setupfile = $pl.".setup.php";
					while($f = readdir($handle))
					{
						if($f != "." && $f != ".." && $f != $setupfile && mb_strtolower(mb_substr($f, mb_strrpos($f, '.') + 1, 4)) == 'php')
						{
							$parts[] = $f;

							$t -> assign(array(
								"ADMIN_PLUG_EDIT_INSTALL_ROW_PARTS_FOUND_F" => $f
							));
							$t -> parse("PLUG.EDIT.INSTALL.ROW_PARTS_FOUND");
						}
					}
					closedir($handle);

					if(count($parts) > 0)
					{
						while(list($i, $x) = each($parts))
						{
							$extplugin_file = $cfg['plugins_dir']."/".$pl."/".$x;
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

							$t -> assign(array(
								"ADMIN_PLUG_EDIT_INSTALL_ROW_PARTS_INSTALLING_X" => $x,
								"ADMIN_PLUG_EDIT_INSTALL_ROW_PARTS_INSTALLING_MSG" => $msg
							));
							$t -> parse("PLUG.EDIT.INSTALL.ROW_PARTS_INSTALLING");
						}
					}

					$info_cfg = sed_infoget($extplugin_info, 'SED_EXTPLUGIN_CONFIG');

					if(empty($info_cfg['Error']))
					{
						$j = 0;
						foreach($info_cfg as $i => $x)
						{
							$line = explode(":", $x);

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
									$sql = sed_sql_query("INSERT into $db_config (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES ('plug', '".$pl."', ".$line[0].", '".$i."', ".(int)$line['Type'].", '".$line[3]."', '".$line[2]."', '".sed_sql_prep($line[4])."')");
								}
								elseif ($ko)
								{
									$sqltmp = sed_sql_query("SELECT COUNT(*) FROM $db_config WHERE config_owner='plug' AND config_cat='$pl' AND config_name='".$line[0]."' ");
									$if = sed_sql_result($sqltmp, 0, "COUNT(*)");

									$sql = (!$if) ? sed_sql_query("INSERT into $db_config (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES ('plug', '".$pl."', ".$line[0].", '".$i."', ".(int)$line['Type'].", '".$line[3]."', '".$line[2]."', '".sed_sql_prep($line[4])."')") : '';
								}

								$t -> assign(array(
									"ADMIN_PLUG_EDIT_INSTALL_ROW_PARTS_CFG_J" => $j,
									"ADMIN_PLUG_EDIT_INSTALL_ROW_PARTS_CFG_I" => $i,
									"ADMIN_PLUG_EDIT_INSTALL_ROW_PARTS_CFG_LINE" => $line[1]
								));
								$t -> parse("PLUG.EDIT.INSTALL.ROW_PARTS_CFG.ROW_PARTS_CFG_ENTRY");
							}
							$totalconfig++;
						}

						$t -> assign(array(
							"ADMIN_PLUG_EDIT_INSTALL_ROW_PARTS_CFG_TOTALCONFIG" => $totalconfig
						));
						$t -> parse("PLUG.EDIT.INSTALL.ROW_PARTS_CFG");
					}
					else
					{
						$t -> parse("PLUG.EDIT.INSTALL.ROW_PARTS_CFG_ERROR");
					}
				}

				if(!$ko)
				{
					$sql = sed_sql_query("DELETE FROM $db_auth WHERE auth_code='plug' and auth_option='$pl'");
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
						$sql = sed_sql_query("INSERT into $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid) VALUES (".(int)$v['id'].", 'plug', '$pl', ".(int)$ins_auth.", ".(int)$ins_lock.", ".(int)$usr['id'].")");
					}
					elseif(!$ko)
					{
						$sqltmp = sed_sql_query("SELECT COUNT(*) FROM $db_auth WHERE auth_code='plug' AND auth_groupid='".(int)$v['id']."' AND auth_option='$pl' ");
						$if = sed_sql_result($sqltmp, 0, "COUNT(*)");

						$sql = (!$if) ? sed_sql_query("INSERT into $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid) VALUES (".(int)$v['id'].", 'plug', '$pl', ".(int)$ins_auth.", ".(int)$ins_lock.", ".(int)$usr['id'].")") : '';
					}

					$t -> assign(array(
						"ADMIN_PLUG_EDIT_INSTALL_ROW_RIGHTS_ID" => $v['id'],
						"ADMIN_PLUG_EDIT_INSTALL_ROW_RIGHTS_TITLE" => $sed_groups[$v['id']]['title'],
						"ADMIN_PLUG_EDIT_INSTALL_ROW_RIGHTS_AUTH" => sed_build_admrights($ins_auth),
						"ADMIN_PLUG_EDIT_INSTALL_ROW_RIGHTS_LOCK" => sed_build_admrights($ins_lock),
						"ADMIN_PLUG_EDIT_INSTALL_ROW_RIGHTS_COMMENT" => $comment
					));
					$t -> parse("PLUG.EDIT.INSTALL.ROW_RIGHTS");
				}
				$sql = sed_sql_query("UPDATE $db_users SET user_auth='' WHERE 1");
				$show_sql_affectedrows4 = sed_sql_affectedrows();

				$extplugin_install = $cfg['plugins_dir']."/".$pl."/".$pl.".install.php";
				$action = 'install';
				include_once($extplugin_info);

				sed_auth_reorder();
				sed_cache_clearall();

				$t -> assign(array(
					"ADMIN_PLUG_EDIT_AFFECTEDROWS1" => $show_sql_affectedrows1,
					"ADMIN_PLUG_EDIT_AFFECTEDROWS2" => $show_sql_affectedrows2,
					"ADMIN_PLUG_EDIT_AFFECTEDROWS3" => $show_sql_affectedrows3,
					"ADMIN_PLUG_EDIT_AFFECTEDROWS4" => $show_sql_affectedrows4,
					"ADMIN_PLUG_EDIT_EXTPLUGIN_INFO" => include_once($extplugin_info),
					"ADMIN_PLUG_EDIT_LOG" => $edit_log,
					"ADMIN_PLUG_EDIT_CONTINUE_URL" => sed_url('admin', "m=plug&a=details&pl=".$pl),
					"ADMIN_PLUG_EDIT_CONTINUE_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=details&ajax=1&pl='.$pl)."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : ""
				));
				/* === Hook  === */
				$extp = sed_getextplugins('admin.plug.install.tags');
				if(is_array($extp))
				{
					foreach($extp as $k => $pl)
					{
						include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
					}
				}
				/* ===== */
				$t -> parse("PLUG.EDIT.INSTALL");
				$t -> parse("PLUG.EDIT");
			break;
			case 'uninstall':
				$extplugin_info = $cfg['plugins_dir']."/".$pl."/".$pl.".setup.php";
				$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');
				$adminpath[] = array(sed_url('admin', 'm=plug&a=details&pl='.$pl), $info['Name']." ($pl)");
				$sql = sed_sql_query("DELETE FROM $db_plugins WHERE pl_code='$pl'");
				$show_sql_affectedrows1 = sed_sql_affectedrows();

				if(!$ko)
				{
					$sql = sed_sql_query("DELETE FROM $db_config WHERE config_owner='plug' AND config_cat='$pl'");
					$show_sql_affectedrows2 = sed_sql_affectedrows();
					$sql = sed_sql_query("DELETE FROM $db_auth WHERE auth_code='plug' and auth_option='$pl'");
					$show_sql_affectedrows3 = sed_sql_affectedrows();
				}

				$sql = sed_sql_query("UPDATE $db_users SET user_auth='' WHERE 1");
				$show_sql_affectedrows4 = sed_sql_affectedrows();
				sed_cache_clearall();

				$extplugin_uninstall = $cfg['plugins_dir']."/".$pl."/".$pl.".uninstall.php";
				$action = 'uninstall';
				include_once($extplugin_info);

				$t -> assign(array(
					"ADMIN_PLUG_EDIT_AFFECTEDROWS1" => $show_sql_affectedrows1,
					"ADMIN_PLUG_EDIT_AFFECTEDROWS2" => $show_sql_affectedrows2,
					"ADMIN_PLUG_EDIT_AFFECTEDROWS3" => $show_sql_affectedrows3,
					"ADMIN_PLUG_EDIT_AFFECTEDROWS4" => $show_sql_affectedrows4,
					"ADMIN_PLUG_EDIT_EXTPLUGIN_INFO" => include_once($extplugin_info),
					"ADMIN_PLUG_EDIT_LOG" => $edit_log,
					"ADMIN_PLUG_EDIT_CONTINUE_URL" => sed_url('admin', "m=plug&a=details&pl=".$pl),
					"ADMIN_PLUG_EDIT_CONTINUE_URL_AJAX" => ($cfg['jquery']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=plug&a=details&ajax=1&pl='.$pl)."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : ""
				));
				/* === Hook  === */
				$extp = sed_getextplugins('admin.plug.uninstall.tags');
				if(is_array($extp))
				{
					foreach($extp as $k => $pl)
					{
						include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
					}
				}
				/* ===== */
				$t -> parse("PLUG.EDIT.UNINSTALL");
				$t -> parse("PLUG.EDIT");
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
		$extp = sed_getextplugins('admin.plug.list.loop');
		/* ===== */
		while(list($i, $x) = each($extplugins))
		{
			$extplugin_info = $cfg['plugins_dir']."/".$x."/".$x.".setup.php";
			if(file_exists($extplugin_info))
			{
				$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');

				if(!empty($info['Error']))
				{
					$t -> assign(array(
						"ADMIN_PLUG_X_ERR" => $x,
						"ADMIN_PLUG_ERROR_MSG" => $info['Error']
					));
					$t -> parse("PLUG.DEFAULT.ROW.ROW_ERROR_PLUG");
					$t -> parse("PLUG.DEFAULT.ROW");
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

					$t -> assign(array(
						"ADMIN_PLUG_DETAILS_URL" => sed_url('admin', "m=plug&a=details&pl=".$info['Code']),
						"ADMIN_PLUG_NAME" => $info['Name'],
						"ADMIN_PLUG_CODE_X" => $x,
						"ADMIN_PLUG_EDIT_URL" => sed_url('admin', "m=config&n=edit&o=plug&p=".$info['Code']),
						"ADMIN_PLUG_PARTSCOUNT" => $info['Partscount'],
						"ADMIN_PLUG_STATUS" => $status[$part_status],
						"ADMIN_PLUG_RIGHTS_URL" => sed_url('admin', "m=rightsbyitem&ic=plug&io=".$info['Code']),
						"ADMIN_PLUG_JUMPTO_URL_TOOLS" => sed_url('admin', "m=tools&p=".$info['Code']),
						"ADMIN_PLUG_JUMPTO_URL" => sed_url('plug', "e=".$info['Code']),
						"ADMIN_PLUG_ODDEVEN" => sed_build_oddeven($ii)
					));
					/* === Hook - Part2 : Include === */
					if(is_array($extp))
					{
						foreach($extp as $k => $pl)
						{
							include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
						}
					}
					/* ===== */
					$t -> parse("PLUG.DEFAULT.ROW");
				}
			}
			else
			{
				$t -> assign(array(
					"ADMIN_PLUG_X" => $x
				));
				$t -> parse("PLUG.DEFAULT.ROW_ERROR");
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
			$t -> assign(array(
				"ADMIN_PLUG_HOOK" => $row['pl_hook'],
				"ADMIN_PLUG_CODE" => $row['pl_code'],
				"ADMIN_PLUG_ORDER" => $row['pl_order'],
				"ADMIN_PLUG_ACTIVE" => $sed_yesno[$row['pl_active']]
			));
			$t -> parse("PLUG.DEFAULT.HOOKS");
		}

		$t -> assign(array(
			"ADMIN_PLUG_CNT_EXTP" => $cnt_extp,
			"ADMIN_PLUG_CNT_HOOK" => sed_sql_numrows($sql)
		));
		$t -> parse("PLUG.DEFAULT");
	break;
}

$if_conf_url = (!empty($pl) && $b == 'install' && $totalconfig > 0) ? true : false;
$is_adminwarnings = isset($adminwarnings);

$t -> assign(array(
	"ADMIN_PLUG_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_PLUG_CONFIG_URL" => sed_url('admin', "m=config&n=edit&o=plug&p=".$pl),
	"ADMIN_PLUG_ADMINWARNINGS" => $adminwarnings
));

/* === Hook  === */
$extp = sed_getextplugins('admin.plug.tags');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */
$t -> parse("PLUG");
$adminmain = $t -> text("PLUG");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>