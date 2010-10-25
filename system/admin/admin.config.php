<?php
/**
 * Administration panel - Configuration
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

cot_require_api('configuration');

$t = new XTemplate(cot_skinfile('admin.config'));

$adminpath[] = array(cot_url('admin', 'm=config'), $L['Configuration']);

/* === Hook === */
foreach (cot_getextplugins('admin.config.first') as $pl)
{
	include $pl;
}
/* ===== */

switch($n)
{
	case 'edit':
		$o = cot_import('o', 'G', 'ALP');
		$p = cot_import('p', 'G', 'ALP');
		$v = cot_import('v', 'G', 'ALP');
		$o = empty($o) ? 'core' : $o;
		$p = empty($p) ? 'global' : $p;
		
		if ($a == 'update')
		{
			
			$sql = $db->query("SELECT config_name FROM $db_config
				WHERE config_owner='$o' AND config_cat='$p'");
			while ($row = $sql->fetch())
			{
				$cfg_value = trim(cot_import($row['config_name'], 'P', 'NOC'));
				if ($o == 'core' && $p == 'users'
					&& ($cfg_name == 'av_maxsize' || $cfg_name == 'sig_maxsize' || $cfg_name == 'ph_maxsize'))
				{
					$cfg_value = min($cfg_value, cot_get_uploadmax() * 1024);
				}
				$db->update($db_config, array('config_value' => $cfg_value),
					"config_name = :n AND config_owner = :o AND config_cat = :p", array(':n' => $row['config_name'], ':o' => $o, ':p' => $p));
			}
			$sql->closeCursor();
			$cache && $cache->clear();
			cot_message('Updated');
		}
		elseif ($a == 'reset' && !empty($v))
		{
			$db->query("UPDATE $db_config
				SET config_value=config_default WHERE config_name='$v' AND config_owner='$o'");
			$cache && $cache->clear();
		}
		
		$sql = $db->query("SELECT * FROM $db_config
			WHERE config_owner='$o' AND config_cat='$p' ORDER BY config_cat ASC, config_order ASC, config_name ASC");
		cot_die($sql->rowCount() == 0);
		
		if ($o == 'core')
		{
			$adminpath[] = array(cot_url('admin', 'm=config&n=edit&o='.$o.'&p='.$p), $L['core_'.$p]);
		}
		else
		{
			$plmod = $o == 'module' ? 'mod' : 'pl';
			$plmod_title = $o == 'module' ? $L['Module'] : $L['Plugin'];
			$adminpath[] = array(cot_url('admin', "m=extensions&a=details&$plmod=$p"), $plmod_title.' ('.$o.':'.$p.')');
			$adminpath[] = array(cot_url('admin', 'm=config&n=edit&o='.$o.'&p='.$p), $L['Edit']);
		}
		
		if ($o != 'core' && file_exists(cot_langfile($p, $o)))
		{
			cot_require_lang($p, $o);
		}
		
		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('admin.config.edit.loop');
		/* ===== */
		while ($row = $sql->fetch())
		{
			$config_owner = $row['config_owner'];
			$config_cat = $row['config_cat'];
			$config_name = $row['config_name'];
			$config_value = $row['config_value'];
			$config_default = $row['config_default'];
			$config_type = $row['config_type'];
			$config_title = $L['cfg_'.$config_name][0];
			$config_text = htmlspecialchars($row['config_text']);
			$config_more = $L['cfg_'.$config_name][1];
			$if_config_more = (!empty($config_more)) ? true : false;
					
			if ($config_type == COT_CONFIG_TYPE_STRING)
			{
				$config_input = cot_inputbox('text', $config_name, $config_value);
			}
			elseif ($config_type == COT_CONFIG_TYPE_SELECT)
			{
				if (!empty($row['config_variants']))
				{
					$cfg_params = explode(',', $row['config_variants']);
					$cfg_params_titles = (isset($L['cfg_'.$config_name.'_params'])
						&& is_array($L['cfg_'.$config_name.'_params']))
							? $L['cfg_'.$config_name.'_params'] : $cfg_params;
				}
				$config_input = (is_array($cfg_params))
					? cot_selectbox($config_value, $config_name, $cfg_params, $cfg_params_titles, false)
					: cot_inputbox('text', $config_name, $config_value);
			}
			elseif ($config_type == COT_CONFIG_TYPE_RADIO)
			{
				$config_input = cot_radiobox($config_value, $config_name, array(1, 0), array($L['Yes'], $L['No']), '', ' ');
			}
			elseif ($config_type == COT_CONFIG_TYPE_CALLBACK)
			{
				// TODO implement callback config type
//				$varname = 'cot_select_'.$config_name;
//				reset($$varname);
//				$vararray = array();
//				foreach ($$varname as $key => $value)
//				{
//					$vararray[$value[1]] = $value[0];
//				}
//				$config_input = cot_selectbox($config_value, $config_name, array_keys($vararray),
//				 array_values($vararray), false);
			}
			elseif ($config_type == COT_CONFIG_TYPE_HIDDEN)
			{
				continue;
			}
			else
			{
				$config_input = cot_textarea($config_name, $config_value, 8, 56);
			}
			
			$t->assign(array(
				'ADMIN_CONFIG_ROW_CONFIG' => $config_input,
				'ADMIN_CONFIG_ROW_CONFIG_TITLE' => (empty($L['cfg_'.$row['config_name']][0]) && !empty($config_text))
					? $config_text : $config_title,
				'ADMIN_CONFIG_ROW_CONFIG_MORE_URL' =>
					cot_url('admin', "m=config&n=edit&o=$o&p=$p&a=reset&v=$config_name"),
				'ADMIN_CONFIG_ROW_CONFIG_MORE' => $config_more
			));
			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */
			$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW');
		}
		
		$t->assign(array(
			'ADMIN_CONFIG_FORM_URL' => cot_url('admin', 'm=config&n=edit&o='.$o.'&p='.$p.'&a=update')
		));
		/* === Hook  === */
		foreach (cot_getextplugins('admin.config.edit.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */
		$t->parse('MAIN.EDIT');
		break;
	
	default:
		$sql = $db->query("SELECT DISTINCT(config_cat) FROM $db_config
			WHERE config_owner='core' ORDER BY config_cat ASC");
		while ($row = $sql->fetch())
		{
			if($L['core_'.$row['config_cat']])
			{
				$t->assign(array(
					'ADMIN_CONFIG_ROW_URL' => cot_url('admin', 'm=config&n=edit&o=core&p='.$row['config_cat']),
					'ADMIN_CONFIG_ROW_NAME' => $L['core_'.$row['config_cat']]
				));
				$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL.ADMIN_CONFIG_ROW');
			}
		}
		$sql->closeCursor();
		$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL');
		$sql = $db->query("SELECT DISTINCT(config_cat) FROM $db_config
			WHERE config_owner='module' ORDER BY config_cat ASC");
		while ($row = $sql->fetch())
		{
			$t->assign(array(
				'ADMIN_CONFIG_ROW_URL' => cot_url('admin', 'm=config&n=edit&o=module&p='.$row['config_cat']),
				'ADMIN_CONFIG_ROW_NAME' => $row['config_cat']
			));
			$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL.ADMIN_CONFIG_ROW');
		}
		$sql->closeCursor();
		$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL');
		$sql = $db->query("SELECT DISTINCT(config_cat) FROM $db_config
			WHERE config_owner='plug' ORDER BY config_cat ASC");
		while ($row = $sql->fetch())
		{
			$t->assign(array(
				'ADMIN_CONFIG_ROW_URL' => cot_url('admin', 'm=config&n=edit&o=plug&p='.$row['config_cat']),
				'ADMIN_CONFIG_ROW_NAME' => $row['config_cat']
			));
			$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL.ADMIN_CONFIG_ROW');
		}
		$sql->closeCursor();
		$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL');
		/* === Hook  === */
		foreach (cot_getextplugins('admin.config.default.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */
		$t->parse('MAIN.DEFAULT');
		break;
}

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.config.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (COT_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}
?>