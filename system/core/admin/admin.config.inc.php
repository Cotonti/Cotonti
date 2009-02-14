<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.config.inc.php
Version=101
Updated=2006-mar-15
Type=Core.admin
Author=Neocrome
Description=Configuration
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=config'), $L['Configuration']);

$sed_select_charset = sed_loadcharsets();
$sed_select_doctypeid = sed_loaddoctypes();

switch ($n)
{
	case 'edit':

		$o = sed_import('o','G','ALP');
		$p = sed_import('p','G','ALP');
		$v = sed_import('v','G','TXT');
		$o = (empty($o)) ? 'core' : $o;
		$p = (empty($o)) ? 'global' : $p;

		if ($a=='update' && !empty($n))
		{
			if ($o=='core')
			{
				reset($cfgmap);
				foreach ($cfgmap as $k => $line)
				{
					if ($line[0]==$p)
					{
						$cfg_name = $line[2];
						$cfg_value = trim(sed_import($cfg_name, 'P', 'NOC'));
						if ('users' == $p && ('av_maxsize' == $cfg_name || 'sig_maxsize' == $cfg_name || 'ph_maxsize' == $cfg_name))
						{
							$cfg_value = min($cfg_value, sed_get_uploadmax() * 1024);
						}
						$sql = sed_sql_query("UPDATE $db_config SET config_value='".sed_sql_prep($cfg_value)."' WHERE config_name='".$cfg_name."' AND config_owner='core'");
					}
				}
			}
			else
			{
				$sql = sed_sql_query("SELECT config_owner, config_name FROM $db_config WHERE config_owner='$o' AND config_cat='$p'");
				while ($row = sed_sql_fetcharray($sql))
				{
					$cfg_value = trim(sed_import($row['config_name'], 'P', 'NOC'));
					$sql1 = sed_sql_query("UPDATE $db_config SET config_value='".sed_sql_prep($cfg_value)."' WHERE config_name='".$row['config_name']."' AND config_owner='$o' AND config_cat='$p'");
				}
			}
			header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', "m=config", '', true));
			exit;
		}

		elseif ($a=='reset' && $o=='core' && !empty($v))

		{
			foreach($cfgmap as $i => $line)
			{
				if ($v==$line[2])
				{ $sql = sed_sql_query("UPDATE $db_config SET config_value='".sed_sql_prep($line[4])."' WHERE config_name='$v' AND config_owner='$o'"); }
			}
		}

		$sql = sed_sql_query("SELECT * FROM $db_config WHERE config_owner='$o' AND config_cat='$p' ORDER BY config_cat ASC, config_order ASC, config_name ASC");
		sed_die(sed_sql_numrows($sql)==0);

		foreach ($cfgmap as $k => $line)
		{ $cfg_params[$line[2]] = $line[5]; }

		if ($o=='core')
		{ $adminpath[] = array (sed_url('admin', 'm=config&n=edit&o='.$o.'&p='.$p), $L["core_".$p]); }
		else
		{
			$adminpath[] = array (sed_url('admin', 'm=config&n=edit&o='.$o.'&p='.$p), $L['Plugin'].' ('.$o.':'.$p.')');
		}

		$adminmain .= "<form id=\"saveconfig\" action=\"".sed_url('admin', "m=config&n=edit&o=".$o."&p=".$p."&a=update")."\" method=\"post\">";
		$adminmain .= "<table class=\"cells\">";
		$adminmain .= "<tr><td  class=\"coltop\" colspan=\"2\">".$L['Configuration']."</td><td class=\"coltop\">".$L['Reset']."</td></tr>";

		if ($o=='plug')
		{
			$path_lang_def	= $cfg['plugins_dir']."/$p/lang/$p.en.lang.php";
			$path_lang_alt	= $cfg['plugins_dir']."/$p/lang/$p.$lang.lang.php";
			if (file_exists($path_lang_alt))
			{ require_once($path_lang_alt); }
			elseif (file_exists($path_lang_def))
			{ require_once($path_lang_def); }
		}


		while ($row = sed_sql_fetcharray($sql))
		{
			$config_owner = $row['config_owner'];
			$config_cat = $row['config_cat'];
			$config_name = $row['config_name'];
			$config_value = sed_cc($row['config_value']);
			$config_default = $row['config_default'];
			$config_type = $row['config_type'];
			$config_title = $L['cfg_'.$row['config_name']][0];
			$config_text = sed_cc($row['config_text']);
			$config_more = $L['cfg_'.$row['config_name']][1];
			$config_more = (!empty($config_more)) ? '<div class="adminconfigmore">'.$config_more.'</div>' : '';
			$config_title = (empty($L['cfg_'.$row['config_name']][0]) && !empty($config_text)) ? $config_text : $config_title;

			$adminmain .= "<tr><td style=\"width:25%;\">".$config_title." : </td><td style=\"width:68%;\">";

			if ($config_type == 1)
			{ $adminmain .= "<input type=\"text\" class=\"text\" name=\"$config_name\" value=\"$config_value\" size=\"32\" maxlength=\"255\" />"; }
			elseif ($config_type == 2)
			{
				if ($o=='plug' && !empty($row['config_default']))
				{
					$cfg_params[$config_name] = explode(",", $row['config_default']);
				}

				if (is_array($cfg_params[$config_name]))
				{
					reset($cfg_params[$config_name]);
					$adminmain .= "<select name=\"$config_name\" size=\"1\">";
					while( list($i,$x) = each($cfg_params[$config_name]) )
					{
						$x = trim($x);
						$selected = ($x == $config_value) ? "selected=\"selected\"" : '';
						$adminmain .= "<option value=\"".$x."\" $selected>".$x."</option>";
					}
					$adminmain .= "</select>";
				}
				elseif ($cfg_params[$config_name]=="userlevels")
				{
					$adminmain .= sed_selectboxlevels(0, 99, $config_value, $config_name);
				}
				else
				{
					$adminmain .= "<input type=\"text\" class=\"text\" name=\"$config_name\" value=\"$config_value\" size=\"8\" maxlength=\"11\" />";
				}
			}
			elseif ($config_type == 3)
			{
				if ($config_value == 1)
				{ $adminmain .= "<input type=\"radio\" class=\"radio\" name=\"$config_name\" value=\"1\" checked=\"checked\" />".$L['Yes']."&nbsp;&nbsp;<input type=\"radio\" class=\"radio\" name=\"$config_name\" value=\"0\" />".$L['No']; 	}
				else
				{ $adminmain .= "<input type=\"radio\" class=\"radio\" name=\"$config_name\" value=\"1\" />".$L['Yes']."&nbsp;&nbsp;<input type=\"radio\" class=\"radio\" name=\"$config_name\" value=\"0\" checked=\"checked\" />".$L['No']; }
			}
			elseif ($config_type == 4)
			{
				$varname = "sed_select_".$config_name;
				$adminmain .= "<select name=\"".$config_name."\" size=\"1\">";
				reset($$varname);
				while ( list($i,$x) = each($$varname) )
				{
					$selected = ($config_value==$x[0]) ? "selected=\"selected\"" : '';
					$adminmain .= "<option value=\"".$x[0]."\" $selected>".$x[1]."</option>";
				}
				$adminmain .= "</select>";
			}
			else
			{
				$adminmain .= "<textarea name=\"$config_name\" rows=\"8\" cols=\"56\">".$config_value."</textarea>";
			}
			$adminmain .= " ".$config_more."</td>";
			$adminmain .= "<td style=\"text-align:center; width:7%;\">";
			$adminmain .= ($o=='core') ? "[<a href=\"".sed_url('admin', "m=config&n=edit&o=".$o."&p=".$p."&a=reset&v=".$config_name)."\">R</a>]" : '&nbsp;';
			$adminmain .= "</td>";
			$adminmain .= "</tr>";
		}
		$adminmain .= "<tr><td colspan=\"3\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
		$adminmain .= "</table></form>";

		break;

	default:

		$adminmain = "<h4>".$L['Core']." :</h4><ul>";

		$sql = sed_sql_query("SELECT DISTINCT(config_cat) FROM $db_config WHERE config_owner='core' ORDER BY config_cat ASC");

		while ($row = sed_sql_fetcharray($sql))
		{
			$code = "core_".$row['config_cat'];
			$adminmain .= "<li><a href=\"".sed_url('admin', "m=config&n=edit&o=core&p=".$row['config_cat'])."\">".$L[$code]."</a></li>";
		}


		$adminmain .= "</ul>";

		$adminmain .= "<h4>".$L['Plugins']." :</h4><ul>";

		$sql = sed_sql_query("SELECT DISTINCT(config_cat) FROM $db_config WHERE config_owner='plug' ORDER BY config_cat ASC");

		while ($row = sed_sql_fetcharray($sql))
		{
			$adminmain .= "<li><a href=\"".sed_url('admin', "m=config&n=edit&o=plug&p=".$row['config_cat'])."\">".$row['config_cat']."</a></li>";
		}

		$adminmain .= "</ul>";

		break;
}

?>