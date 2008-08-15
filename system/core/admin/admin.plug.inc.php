<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.plugins.inc.php
Version=125
Updated=2008-may-26
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array ("admin.php?m=plug", $L['Plugins']);

$pl = sed_import('pl','G','ALP');
$part = sed_import('part','G','ALP');

$status[0] = '<span style="color:#5882AC; font-weight:bold;">'.$L['adm_paused'].'</span>';
$status[1] = '<span style="color:#739E48; font-weight:bold;">'.$L['adm_running'].'</span>';
$status[2] = '<span style="color:#A78731; font-weight:bold;">'.$L['adm_partrunning'].'</span>';
$status[3] = '<span style="color:#AC5866; font-weight:bold;">'.$L['adm_notinstalled'].'</span>';
$found_txt[0] = '<span style="color:#AC5866; font-weight:bold;">'.$L['adm_missing'].'</span>';
$found_txt[1] = '<span style="color:#739E48; font-weight:bold;">'.$L['adm_present'].'</span>';
unset($disp_errors);

$adminmain .= "<ul><li><a href=\"admin.php?m=config&amp;n=edit&amp;o=core&amp;p=plug\">".$L['Configuration']." : <img src=\"system/img/admin/config.gif\" alt=\"\" /></a></li></ul>";

switch ($a)
	{
	/* =============== */
	case 'details' :
	/* =============== */

	$extplugin_info = "plugins/".$pl."/".$pl.".setup.php";

	if (file_exists($extplugin_info))
		{
		$extplugin_info = "plugins/".$pl."/".$pl.".setup.php";
		$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');
		$adminpath[] = array ('admin.php?m=plug&amp;a=details&amp;pl='.$pl, $info['Name']." ($pl)");

		$handle=opendir("plugins/".$pl);
		$setupfile = $pl.".setup.php";
		while ($f = readdir($handle))
			{
			if ($f != "." && $f != ".." && $f!=$setupfile && strtolower(substr($f, strrpos($f, '.')+1, 4))=='php')
				{ $parts[] = $f; }
			}
		closedir($handle);
		if (is_array($parts))
			{ sort($parts); }

		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_config WHERE config_owner='plug' AND config_cat='$pl'");
		$totalconfig = sed_sql_result($sql, 0, "COUNT(*)");

		$info['Config'] = ($totalconfig>0) ? "<a href=\"admin.php?m=config&amp;n=edit&amp;o=plug&amp;p=".$pl."\"><img src=\"system/img/admin/config.gif\" alt=\"\" /> (".$totalconfig.") ".$L['Edit']."</a>": $L['None'];

		$info['Auth_members'] = sed_auth_getvalue($info['Auth_members']);
		$info['Lock_members'] = sed_auth_getvalue($info['Lock_members']);
		$info['Auth_guests'] = sed_auth_getvalue($info['Auth_guests']);
		$info['Lock_guests'] = sed_auth_getvalue($info['Lock_guests']);

		$adminmain .= "<h4>".$L['Plugin']." '".$info['Name']."' :</h4>";
		$adminmain .= "<table class=\"cells\">";
		$adminmain .= "<tr><td>".$L['Code'].":</td><td>".$info['Code']."</td></tr>";
		$adminmain .= "<tr><td>".$L['Description'].":</td><td>".$info['Description']."</td></tr>";
		$adminmain .= "<tr><td>".$L['Version'].":</td><td>".$info['Version']."</td></tr>";
		$adminmain .= "<tr><td>".$L['Date'].":</td><td>".$info['Date']."</td></tr>";
		$adminmain .= "<tr><td>".$L['Configuration'].":</td><td>".$info['Config']."</td></tr>";
		$adminmain .= "<tr><td>".$L['Rights'].":</td><td><a href=\"admin.php?m=rightsbyitem&amp;ic=plug&amp;io=".$info['Code']."\"><img src=\"system/img/admin/rights2.gif\" alt=\"\" /></a></td></tr>";		
		$adminmain .= "<tr><td>".$L['adm_defauth_guests'].":</td><td>".sed_build_admrights($info['Auth_guests']);
		$adminmain .= " (".$info['Auth_guests'].")</td></tr>";
		$adminmain .= "<tr><td>".$L['adm_deflock_guests'].":</td><td>".sed_build_admrights($info['Lock_guests']);
		$adminmain .= " (".$info['Lock_guests'].")</td></tr>";
		$adminmain .= "<tr><td>".$L['adm_defauth_members'].":</td><td>".sed_build_admrights($info['Auth_members']);
		$adminmain .= " (".$info['Auth_members'].")</td></tr>";
		$adminmain .= "<tr><td>".$L['adm_deflock_members'].":</td><td>".sed_build_admrights($info['Lock_members']);
		$adminmain .= " (".$info['Lock_members'].")</td></tr>";
		$adminmain .= "<tr><td>".$L['Author'].":</td><td>".$info['Author']."</td></tr>";
		$adminmain .= "<tr><td>".$L['Copyright'].":</td><td>".$info['Copyright']."</td></tr>";
		$adminmain .= "<tr><td>".$L['Notes'].":</td><td>".sed_parse($info['Notes'], 1, 0, 0)."</td></tr>";
		$adminmain .= "</table>";

		$adminmain .= "<h4>".$L['Options']." :</h4>";
		$adminmain .= "<table class=\"cells\">";
		$adminmain .= "<tr><td><a href=\"admin.php?m=plug&amp;a=edit&amp;pl=".$info['Code']."&amp;b=install\">".$L['adm_opt_installall']."</a></td>";
		$adminmain .= "<td>".$L['adm_opt_installall_explain']."</td></tr>";
		$adminmain .= "<tr><td><a href=\"admin.php?m=plug&amp;a=edit&amp;pl=".$info['Code']."&amp;b=uninstall\">".$L['adm_opt_uninstallall']."</a></td>";
		$adminmain .= "<td>".$L['adm_opt_uninstallall_explain']."</td></tr>";
		$adminmain .= "<tr><td><a href=\"admin.php?m=plug&amp;a=edit&amp;pl=".$info['Code']."&amp;b=pause\">".$L['adm_opt_pauseall']."</a></td>";
		$adminmain .= "<td>".$L['adm_opt_pauseall_explain']."</td></tr>";
		$adminmain .= "<tr><td><a href=\"admin.php?m=plug&amp;a=edit&amp;pl=".$info['Code']."&amp;b=unpause\">".$L['adm_opt_unpauseall']."</a></td>";
		$adminmain .= "<td>".$L['adm_opt_unpauseall_explain']."</td></tr>";
		$adminmain .= "</table>";

		$adminmain .= "<h4>".$L['Parts']." :</h4>";
		$adminmain .= "<table class=\"cells\"><tr>";
		$adminmain .= "<td class=\"coltop\" colspan=\"2\">".$L['adm_part']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['File']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['Hooks']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['Order']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['Status']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['Action']."</td>";
		$adminmain .= "</tr>";

		while( list($i,$x) = each($parts) )
			{
			$extplugin_file = "plugins/".$pl."/".$x;
			$info_file = sed_infoget($extplugin_file, 'SED_EXTPLUGIN');

			if (!empty($info_file['Error']))
				{
				$adminmain .= "<tr>";
				$adminmain .= "<td colspan=\"3\">".$x."</td>";
				$adminmain .= "<td colspan=\"5\">".$info_file['Error']."</td>";
				$adminmain .= "</tr>";
				}
			else
				{
				$sql = sed_sql_query("SELECT pl_active, pl_id FROM $db_plugins WHERE pl_code='$pl' AND pl_part='".$info_file['Part']."' LIMIT 1");

				if ($row = sed_sql_fetcharray($sql))
					{ $info_file['Status'] = $row['pl_active']; }
				else
					{ $info_file['Status'] = 3; }

				$adminmain .= "<tr>";
				$adminmain .= "<td style=\"width:32px;\">#".($i+1)."</td>";
				$adminmain .= "<td>".$info_file['Part']."</td>";
				$adminmain .= "<td>".$info_file['File'].".php</td>";
				$adminmain .= "<td>".$info_file['Hooks']."</td>";
				$adminmain .= "<td style=\"text-align:center;\">".$info_file['Order']."</td>";
				$adminmain .= "<td style=\"text-align:center;\">".$status[$info_file['Status']]."</td>";
				$adminmain .= "<td style=\"text-align:center;\">";

				if ($info_file['Status']==3)
					{ $adminmain .= "-"; }
				elseif ($row['pl_active']==1)
					{ $adminmain .= "<a href=\"admin.php?m=plug&amp;a=edit&amp;pl=".$pl."&amp;b=pausepart&amp;part=".$row['pl_id']."\">Pause</a>"; }
				elseif ($row['pl_active']==0)
					{ $adminmain .= "<a href=\"admin.php?m=plug&amp;a=edit&amp;pl=".$pl."&amp;b=unpausepart&amp;part=".$row['pl_id']."\">Un-pause</a>"; }

				$adminmain .= "</td></tr>";
				$listtags .= "<tr><td style=\"width:32px;\">#".($i+1)."</td><td>".$info_file['Part']."</td><td>";

				if (empty($info_file['Tags']))
					{
					$listtags .= $L['None'];
					}
				else
					{
					$line = explode (":",$info_file['Tags']);
					$line[0] = trim($line[0]);
					$tags = explode (",",$line[1]);
					$listtags .= $line[0]." :<br />";
					foreach ($tags as $k => $v)
						{
						if (substr(trim($v),0,1)=='{')
							{
							$listtags .= $v." : ";
							$found = sed_stringinfile('skins/'.$cfg['defaultskin'].'/'.$line[0], trim($v));
							$listtags .= $found_txt[$found];
							$listtags .= "<br />";
							}
						else
							{
							$listtags .= $v."<br />";
							}
						}
					}

				$listtags .= "</td></tr>";
				$adminmain .= "</td></tr>";
				}

			}
		$adminmain .= "</table>";

		$adminmain .= "<h4>".$L['Tags']." :</h4>";
		$adminmain .= "<table class=\"cells\">";
		$adminmain .= "<tr><td class=\"coltop\" colspan=\"2\">".$L['Part']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['Files']." / ".$L['Tags']."</td>".$listtags."</table>";
		}
	else
		{
		sed_die();
		}

	break;

	/* =============== */
	case 'edit' :
	/* =============== */

	switch ($b)
		{
		case 'install' :

		$pl =(strtolower($pl)=='core') ? 'error' : $pl;
		$sql = sed_sql_query("DELETE FROM $db_plugins WHERE pl_code='$pl'");
		$adminmain .= "Deleting old installation of this plugin... ";
		$adminmain .= "Found:".sed_sql_affectedrows()."<br />";

		$sql = sed_sql_query("DELETE FROM $db_config WHERE config_owner='plug' and config_cat='$pl'");
		$adminmain .= "Deleting old configuration entries... ";
		$adminmain .= "Found:".sed_sql_affectedrows()."<br />";

		$extplugin_info = "plugins/".$pl."/".$pl.".setup.php";

		$adminmain .= "Looking for the setup file... ";

		if (file_exists($extplugin_info))
			{
			$adminmain .= "Found:1<br />";
			$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');

			$handle = opendir("plugins/".$pl);
			$setupfile = $pl.".setup.php";
			$adminmain .= "Looking for parts...<br />";
			while ($f = readdir($handle))
				{
				if ($f != "." && $f != ".." && $f!=$setupfile && strtolower(substr($f, strrpos($f, '.')+1, 4))=='php')
					{
					$adminmain .= "- Found:".$f."<br />";
					$parts[] = $f;
					}
				}
			closedir($handle);

			$adminmain .= "Installing the parts...<br />";
			while( list($i,$x) = each($parts) )
				{
				$adminmain .= "- Part ".$x." ...";
				$extplugin_file = "plugins/".$pl."/".$x;
				$info_part = sed_infoget($extplugin_file, 'SED_EXTPLUGIN');

				if (empty($info_part['Error']))
					{
				   $sql = sed_sql_query("INSERT into $db_plugins (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active ) VALUES ('".$info_part['Hooks']."', '".$info_part['Code']."', '".sed_sql_prep($info_part['Part'])."', '".sed_sql_prep($info['Name'])."', '".$info_part['File']."',  ".(int)$info_part['Order'].", 1)");

					$adminmain .= "Installed<br />";
					}
				else
					{
					$adminmain .= "Error !<br />";
					}
				}

			$info_cfg = sed_infoget($extplugin_info, 'SED_EXTPLUGIN_CONFIG');
			$adminmain .= "Looking for configuration entries in the setup file... ";

			if (empty($info_cfg['Error']))
				{
				$adminmain .= "Found at least 1<br/>";
				$j = 0;
				foreach($info_cfg as $i => $x)
					{
					$line = explode(":", $x);

					if (is_array($line) && !empty($line[1]) && !empty($i))
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

					   	$sql = sed_sql_query("INSERT into $db_config (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES ('plug', '".$pl."', ".$line[0].", '".$i."', ".(int)$line['Type'].", '".$line[3]."', '".$line[2]."', '".sed_sql_prep($line[4])."')");
						$adminmain .= "- Entry #$j $i (".$line[1].") Installed<br />";
					   	}
					}
				}
			else
				{
				$adminmain .= "None found<br />";
				}
			}
		else
			{
			$adminmain .= "Not found ! Installation failed !<br />";
			}

		$sql = sed_sql_query("DELETE FROM $db_auth WHERE auth_code='plug' and auth_option='$pl'");
		$adminmain .= "Deleting any old rights about this plugin... ";
		$adminmain .= "Found:".sed_sql_affectedrows()."<br />";

		$adminmain .= "Adding the rights for the groups of users...<br />";

		foreach($sed_groups as $k => $v)
			{
			$comment = ' (Plugin setup)';

			if ($v['id']==1 || $v['id']==2)
				{
				$ins_auth = sed_auth_getvalue($info['Auth_guests']);
				$ins_lock = sed_auth_getvalue($info['Lock_guests']);

				if ($ins_auth>128 || $ins_lock<128)
					{
					$ins_auth = ($ins_auth>127) ? $ins_auth-128 : $ins_auth;
					$ins_lock = 128;
					$comment = ' (System override, guests and inactive are not allowed to admin)';
					}
				}
			elseif ($v['id']==3)
				{
				$ins_auth = 0;
				$ins_lock = 255;
				$comment = ' (System override, Banned)';
				}
			elseif ($v['id']==5)
				{
				$ins_auth = 255;
				$ins_lock = 255;
				$comment = ' (System override, Administrators)';
				}
			else
				{
				$ins_auth = sed_auth_getvalue($info['Auth_members']);
				$ins_lock = sed_auth_getvalue($info['Lock_members']);
				}

			$sql = sed_sql_query("INSERT into $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid) VALUES (".(int)$v['id'].", 'plug', '$pl', ".(int)$ins_auth.", ".(int)$ins_lock.", ".(int)$usr['id'].")");
			$adminmain .= "Group #".$v['id'].", ".$sed_groups[$v['id']]['title']." : Auth=".sed_build_admrights($ins_auth)." / Lock=".sed_build_admrights($ins_lock).$comment."<br />";
			}
		$sql = sed_sql_query("UPDATE $db_users SET user_auth='' WHERE 1");
		$adminmain .= "Resetting the auth column for all the users... ";
		$adminmain .= "Found:".sed_sql_affectedrows()."<br />";

		$extplugin_install = "plugins/".$pl."/".$pl.".install.php";
		$adminmain .= "Looking for the optional PHP file : ".$extplugin_install."... ";
		if (file_exists($extplugin_install))
			{
			$adminmain .= "Found, executing...<br />";
			include_once($extplugin_install);
			}
		else
			{ $adminmain .= "Not found.<br />"; 	}			
	
		sed_auth_reorder();
		sed_cache_clearall();
		$adminmain .= "<p>".$edit_log."</p>";
		$adminmain .= "<a href=\"admin.php?m=plug&amp;a=details&amp;pl=".$pl."\">Click here to continue...</a>";		
		
		break;

		case 'uninstall' :
		$sql = sed_sql_query("DELETE FROM $db_plugins WHERE pl_code='$pl'");
		$adminmain .= "Deleting old installation of this plugin... ";
		$adminmain .= "Found:".sed_sql_affectedrows()."<br />";
		$sql = sed_sql_query("DELETE FROM $db_config WHERE config_owner='plug' AND config_cat='$pl'");
		$adminmain .= "Deleting old configuration entries... ";
		$adminmain .= "Found:".sed_sql_affectedrows()."<br />";
		$sql = sed_sql_query("DELETE FROM $db_auth WHERE auth_code='plug' and auth_option='$pl'");
		$adminmain .= "Deleting any old rights about this plugin... ";
		$adminmain .= "Found:".sed_sql_affectedrows()."<br />";
		$sql = sed_sql_query("UPDATE $db_users SET user_auth='' WHERE 1");
		$adminmain .= "Resetting the auth column for all the users... ";
		$adminmain .= "Found:".sed_sql_affectedrows()."<br />";
		sed_cache_clearall();

		$extplugin_uninstall = "plugins/".$pl."/".$pl.".uninstall.php";
		$adminmain .= "Looking for the optional PHP file : ".$extplugin_uninstall."... ";
		if (file_exists($extplugin_uninstall))
			{
			$adminmain .= "Found, executing...<br />";
			include_once($extplugin_uninstall);
			}
		else
			{ $adminmain .= "Not found.<br />"; 	}			
			
		$adminmain .= "<p>".$edit_log."</p>";
		$adminmain .= "<a href=\"admin.php?m=plug\">Click here to continue...</a>";			
		break;

		case 'pause' :
		$sql = sed_sql_query("UPDATE $db_plugins SET pl_active=0 WHERE pl_code='$pl'");
		sed_cache_clearall();
		header("Location: admin.php?m=plug&a=details&pl=".$pl);
		exit;
		break;

		case 'unpause' :
		$sql = sed_sql_query("UPDATE $db_plugins SET pl_active=1 WHERE pl_code='$pl'");
		sed_cache_clearall();
		header("Location: admin.php?m=plug&a=details&pl=".$pl);
		exit;
		break;

		case 'pausepart' :
		$sql = sed_sql_query("UPDATE $db_plugins SET pl_active=0 WHERE pl_code='$pl' AND pl_id='$part'");
		sed_cache_clearall();
		header("Location: admin.php?m=plug&a=details&pl=".$pl);
		exit;
		break;

		case 'unpausepart' :
		$sql = sed_sql_query("UPDATE $db_plugins SET pl_active=1 WHERE pl_code='$pl' AND pl_id='$part'");
		sed_cache_clearall();
		header("Location: admin.php?m=plug&a=details&pl=".$pl);
		exit;
		break;

		default:
		sed_die();
		break;
	}

	break;

	default:

	$disp_plugins = "<table class=\"cells\">";
	$disp_plugins .= "<tr>";
	$disp_plugins .= "<td class=\"coltop\">".$L['Plugins']."</td>";
	$disp_plugins .= "<td class=\"coltop\">".$L['Status']."</td>";
	$disp_plugins .= "</tr>";

	$sql = sed_sql_query("SELECT DISTINCT(config_cat), COUNT(*) FROM $db_config WHERE config_owner='plug' GROUP BY config_cat");
	while ($row = sed_sql_fetcharray($sql))
		{ $cfgentries[$row['config_cat']] = $row['COUNT(*)']; }

	$handle=opendir("plugins");
	while ($f = readdir($handle))
		{
		if (!is_file($f) && $f!='.' && $f!='..' && $f!='code')
			{ $extplugins[] = $f; }
		}
	closedir($handle);
	sort($extplugins);
	$cnt_extp = count($extplugins);
	$cnt_parts = 0;

	$plg_standalone = array();
	$sql3 = sed_sql_query("SELECT pl_code FROM $db_plugins WHERE pl_hook='standalone'");
	while ($row3 = sed_sql_fetcharray($sql3))
		{ $plg_standalone[$row3['pl_code']] = TRUE; }

	$plg_tools = array();
	$sql3 = sed_sql_query("SELECT pl_code FROM $db_plugins WHERE pl_hook='tools'");
	while ($row3 = sed_sql_fetcharray($sql3))
		{ $plg_tools[$row3['pl_code']] = TRUE; }

	$adminmain .= "<h4>".$L['Plugins']." (".$cnt_extp.") :</h4>";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr>";
	$adminmain .= "<td class=\"coltop\">".$L['Plugins']." ".$L['adm_clicktoedit']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Code']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Configuration']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Parts']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Status']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:80px;\">".$L['Rights']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:64px;\">".$L['Open']."</td>";
	$adminmain .= "</tr>";


	while( list($i,$x) = each($extplugins) )
		{
		$extplugin_info = "plugins/".$x."/".$x.".setup.php";
		if (file_exists($extplugin_info))
			{
			$info = sed_infoget($extplugin_info, 'SED_EXTPLUGIN');

			if (!empty($info['Error']))
				{
				$adminmain .= "<tr><td>".$x."</td><td colspan=\"7\">".$info['Error']."</td></tr>";
				}
			else
				{
				$sql1 = sed_sql_query("SELECT SUM(pl_active) FROM $db_plugins WHERE pl_code='$x'");
				$sql2 = sed_sql_query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code='$x'");
				$totalactive = sed_sql_result($sql1, 0, "SUM(pl_active)");
				$totalinstalled = sed_sql_result($sql2, 0, "COUNT(*)");
				$cnt_parts += $totalinstalled;

				if ($totalinstalled ==0)
					{
					$part_status = 3;
					$info['Partscount'] = '?';
					}
				else
					{
					$info['Partscount'] = $totalinstalled;
					if ($totalinstalled>$totalactive && $totalactive>0)
						{ $part_status = 2; }
					elseif ($totalactive==0)
						{ $part_status = 0; }
					else
						{ $part_status = 1; }
					}


				$adminmain .= "<tr><td><a href=\"admin.php?m=plug&amp;a=details&amp;pl=".$info['Code']."\">";
				$adminmain .= ($plg_tools[$info['Code']]) ? "<img src=\"system/img/admin/tools.gif\" alt=\"\" />" : "<img src=\"system/img/admin/plug.gif\" alt=\"\" />";

				$adminmain .= " ".$info['Name']."</a></td><td>".$x."</td>";

				$adminmain .= "<td style=\"text-align:center;\">";
				$adminmain .= ($cfgentries[$info['Code']]>0) ? "<a href=\"admin.php?m=config&amp;n=edit&amp;o=plug&amp;p=".$info['Code']."\"><img src=\"system/img/admin/config.gif\" alt=\"\" /></a>" : '&nbsp;';
				$adminmain .= "</td>";
				$adminmain .= "<td style=\"text-align:center;\">".$info['Partscount']."</td>";
				$adminmain .= "<td style=\"text-align:center;\">".$status[$part_status]."</td>";
				$adminmain .= "<td style=\"text-align:center;\"><a href=\"admin.php?m=rightsbyitem&amp;ic=plug&amp;io=".$info['Code']."\"><img src=\"system/img/admin/rights2.gif\" alt=\"\" /></a></td>";
				$adminmain .= "<td style=\"text-align:center;\">";
				
				if ($plg_tools[$info['Code']])
					{
					$adminmain .= "<a href=\"admin.php?m=tools&amp;p=".$info['Code']."\"><img src=\"system/img/admin/jumpto.gif\" alt=\"\" /></a>";
					}
				else
					{
					$adminmain .= ($plg_standalone[$info['Code']]) ? "<a href=\"plug.php?e=".$info['Code']."\"><img src=\"system/img/admin/jumpto.gif\" alt=\"\" /></a>" : '&nbsp;';
					}
				$adminmain .= "</td></tr>";
				}
			}
		else
			{
			$disp_errors .= "<tr><td>plugins/".$x."</td><td colspan=\"7\">Error: Setup file is missing !</td></tr>";
			}
		}
	$adminmain .= $disp_errors;
	$adminmain .= "</table>";

	if ($o=='code')
		{ $sql = sed_sql_query("SELECT * FROM $db_plugins ORDER BY pl_code ASC, pl_hook ASC, pl_order ASC"); }
	else
		{ $sql = sed_sql_query("SELECT * FROM $db_plugins ORDER BY pl_hook ASC, pl_code ASC, pl_order ASC"); }

	$adminmain .= "<h4>".$L['Hooks']." (".sed_sql_numrows($sql).") :</h4>";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td class=\"coltop\">".$L['Hooks']."</td><td class=\"coltop\">".$L['Plugin']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"text-align:center;\">".$L['Order']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"text-align:center;\">".$L['Active']."</td></tr>";

	while ($row = sed_sql_fetcharray($sql))
		{
		$adminmain .= "<tr>";
		$adminmain .= "<td>".$row['pl_hook']."</td><td>".$row['pl_code']."</td>";
		$adminmain .= "<td style=\"text-align:center;\">".$row['pl_order']."</td>";
		$adminmain .= "<td style=\"text-align:center;\">".$sed_yesno[$row['pl_active']]."</td>";
		$adminmain .= "</tr>";
		}

	$adminmain .= "</table>";

	break;
	}

?>
