<?php
/**
 * Administration panel - Configuration
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

require_once cot_incfile('configuration');

$t = new XTemplate(cot_tplfile('admin.config', 'core'));

$sub = cot_import('sub', 'G', 'TXT');
if (empty($sub))
{
	$where_cat = "AND (config_subcat = '' OR config_subcat = '__default')";
	$sub_param = array();
}
else
{
	$where_cat = "AND config_subcat = ?";
	$sub_param = array($sub);
}

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

		/* === Hook  === */
		foreach (cot_getextplugins('admin.config.edit.first') as $pl)
		{
			include $pl;
		}
		/* ===== */

		// For a subcat, load default category config
		if (!empty($sub) && $sub != '__default')
		{
			$default_set = array();
			try
			{
				// Attempt to fetch the entire rowset indexed by config_name
				$sql = $db->query("SELECT * FROM $db_config
					WHERE config_owner = ? AND config_cat = ? AND config_subcat = ?
					ORDER BY config_subcat ASC, config_order ASC, config_name ASC", array($o, $p, '__default'));
				$rs = $sql->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rs as $row)
				{
					$default_set[$row['config_name']] = $row;
				}
				unset($rs);
			}
			catch (PDOException $excpt)
			{
				// $default_set = array();
			}
		}

		if ($a == 'update' && !empty($_POST))
		{
			// Update only those options which have been changed
			$overriden = array();
			$sql = $db->query("SELECT * FROM $db_config
				WHERE config_owner = ? AND config_cat= ? $where_cat",
				array_merge(array($o, $p), $sub_param));
			while ($row = $sql->fetch())
			{
				if (sizeof($cot_import_filters[$row['config_name']]))
				{
					$cfg_value = cot_import($row['config_name'], 'P', $row['config_name']);
				}
				else
				{
					$cfg_value = trim(cot_import($row['config_name'], 'P', 'NOC'));
				}

				if ($o == 'core' && $p == 'users'
					&& ($cfg_name == 'av_maxsize' || $cfg_name == 'sig_maxsize' || $cfg_name == 'ph_maxsize'))
				{
					$cfg_value = min($cfg_value, cot_get_uploadmax() * 1024);
				}
				if ($cfg_value != $row['config_value'])
				{
					$db->update($db_config, array('config_value' => $cfg_value),
						"config_name = ? AND config_owner = ? AND config_cat = ? $where_cat",
						array_merge(array($row['config_name'], $o, $p), $sub_param));
					$overriden[] = $row['config_name'];
				}
			}
			$sql->closeCursor();
			if (!empty($sub))
			{
				// Compare to default, override if not modified in self and differs from default
				foreach ($default_set as $key => $row)
				{
					$cfg_value = trim(cot_import($key, 'P', 'NOC'));
					if (!in_array($key, $overriden) && $cfg_value != $row['config_value'])
					{
						$row['config_subcat'] = $sub;
						$row['config_value'] = $cfg_value;
						$db->insert($db_config, $row);
					}
				}
			}

			// Run configure extension part if present
			if ($o == 'module' && file_exists($cfg['modules_dir'] . "/$p/setup/$p.configure.php"))
			{
				include $cfg['modules_dir'] . "/$p/setup/$p.configure.php";
			}
			elseif ($o == 'plug' && file_exists($cfg['plugins_dir'] . "/$p/setup/$p.configure.php"))
			{
				include $cfg['plugins_dir'] . "/$p/setup/$p.configure.php";
			}

			/* === Hook  === */
			foreach (cot_getextplugins('admin.config.edit.update.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			$cache && $cache->clear();
			cot_message('Updated');
		}
		elseif ($a == 'reset' && !empty($v))
		{
			$update = true;
			if (!empty($sub))
			{
				// Check if overriden
				$sql = $db->query("SELECT COUNT(*) FROM $db_config
					WHERE config_name = ? AND config_owner = ? AND config_cat = ? $where_cat",
					array_merge(array($v, $o, $p), $sub_param));
				$count = $sql->fetchColumn();
				if ($count == 0 && $default_set[$v]['config_value'] != $default_set[$v]['config_default'])
				{
					// Reset this particular option to config_default
					$row = $default_set[$v];
					$row['config_subcat'] = $sub;
					$row['config_value'] = $row['config_default'];
					$db->insert($db_config, $row);
					$update = false;
				}
				elseif ($count > 0 && $default_set[$v]['config_value'] == $default_set[$v]['config_default'])
				{
					// Just remove
					$db->delete($db_config, "config_name = ? AND config_owner = ? AND config_cat = ?
						AND config_subcat = ?",
						array($v, $o, $p, $sub));
					$update = false;
				}
			}

			if ($update && $db->query("SELECT COUNT(*) FROM $db_config WHERE config_name = ? AND config_owner = ? AND config_cat = ? $where_cat", array_merge(array($v, $o, $p), $sub_param))->fetchColumn() == 1)
			{
				$db->query("UPDATE $db_config SET config_value = config_default
					WHERE config_name = ? AND config_owner = ? AND config_cat = ? $where_cat",
					array_merge(array($v, $o, $p), $sub_param));
			}
			/* === Hook  === */
			foreach (cot_getextplugins('admin.config.edit.reset.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			$cache && $cache->clear();
		}

		$rowset = array();
		try
		{
			// Attempt to fetch the entire rowset indexed by config_name
			$sql = $db->query("SELECT * FROM $db_config
				WHERE config_owner = ? AND config_cat = ? $where_cat
				ORDER BY config_subcat ASC, config_order ASC, config_name ASC", array_merge(array($o, $p), $sub_param));
			$rs = $sql->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rs as $row)
			{
				$rowset[$row['config_name']] = $row;
			}
			unset($rs);
		}
		catch (PDOException $excpt)
		{
			if (empty($sub))
			{
				// No items found and not in a subcategory
				cot_die();
			}
		}
		if (!empty($sub))
		{
			// Load missing options from __default config
			foreach ($default_set as $key => $row)
			{
				if (!isset($rowset[$key]))
				{
					$row['config_subcat'] = $sub;
					$rowset[$key] = $row;
				}
			}
		}

		if ($o == 'core')
		{
			$adminpath[] = array(cot_url('admin', 'm=config'), $L['Configuration']);
			$adminpath[] = array(cot_url('admin', 'm=config&n=edit&o='.$o.'&p='.$p.'&sub='.$sub), $L['core_'.$p]);
		}
		else
		{
			$adminpath[] = array(cot_url('admin', 'm=extensions'), $L['Extensions']);
			$plmod = $o == 'module' ? 'mod' : 'pl';
			$ext_info = cot_get_extensionparams($p, $o == 'module');
			$adminpath[] = array(cot_url('admin', "m=extensions&a=details&$plmod=$p"), $ext_info['name']);
			if (!empty($sub))
			{
				$adminpath[] = array(cot_url('admin', 'm=structure&n='.$p), $L['Structure']);
				$adminpath[] = array(cot_url('admin', 'm=structure&n='.$p.'&al='.$sub), $structure[$p][$sub]['title']);
			}
			$adminpath[] = array(cot_url('admin', 'm=config&n=edit&o='.$o.'&p='.$p.'&sub='.$sub), $L['Configuration']);
		}

		if ($o != 'core' && file_exists(cot_langfile($p, $o)))
		{
			require_once cot_langfile($p, $o);
		}

		/* === Hook  === */
		foreach (cot_getextplugins('admin.config.edit.main') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$inside_fieldset = false;
		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('admin.config.edit.loop');
		/* ===== */
		foreach ($rowset as $key => $row)
		{
			$config_owner = $o;
			$config_cat = $p;
			$config_subcat = $row['config_subcat'];
			$config_name = $row['config_name'];

			if (!is_array($L['cfg_'.$config_name]))
			{
				$L['cfg_'.$config_name] = array($L['cfg_'.$config_name]);
			}

			$config_value = $row['config_value'];
			$config_default = $row['config_default'];
			$config_type = $row['config_type'];
			$config_title = $L['cfg_'.$config_name][0];
			$config_text = htmlspecialchars($row['config_text']);
			$config_more = $L['cfg_'.$config_name][1];
			$if_config_more = (!empty($config_more)) ? true : false;


			if ($config_subcat == '__default' && $prev_subcat == '' && $config_type != COT_CONFIG_TYPE_SEPARATOR)
			{
				if ($inside_fieldset)
				{
					// Close previous fieldset
					$t->parse('MAIN.EDIT.ADMIN_CONFIG_FIELDSET_END');
				}
				$inside_fieldset = true;
				$t->assign('ADMIN_CONFIG_FIELDSET_TITLE', $L['cfg_struct_defaults']);
				$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW.ADMIN_CONFIG_FIELDSET_BEGIN');
			}

			if ($config_type == COT_CONFIG_TYPE_STRING)
			{
				$config_input = cot_inputbox('text', $config_name, $config_value);
			}
			elseif ($config_type == COT_CONFIG_TYPE_SELECT)
			{
				if (!empty($row['config_variants']))
				{
					$cfg_params = explode(',', $row['config_variants']);
					// $cfg_params_titles = (isset($L['cfg_'.$config_name.'_params'])
					// 	&& is_array($L['cfg_'.$config_name.'_params']))
					// 		? $L['cfg_'.$config_name.'_params'] : $cfg_params;
					$cfg_params_titles = cot_admin_config_get_titles($config_name, $cfg_params);
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
				// Preload module/plugin functions
				if (file_exists(cot_incfile($config_cat, $config_owner)))
				{
					require_once cot_incfile($config_cat, $config_owner);
				}
				if ((preg_match('#^(\w+)\((.*?)\)$#', $row['config_variants'], $mt) && function_exists($mt[1])))
				{
					$callback_params = preg_split('#\s*,\s*#', $mt[2]);
					if (count($callback_params) > 0 && !empty($callback_params[0]))
					{
						for ($i = 0; $i < count($callback_params); $i++)
						{
							$callback_params[$i] = str_replace("'", '', $callback_params[$i]);
							$callback_params[$i] = str_replace('"', '', $callback_params[$i]);
						}
						$cfg_params = call_user_func_array($mt[1], $callback_params);
					}
					else
					{
						$cfg_params = call_user_func($mt[1]);
					}
					$cfg_params_titles = cot_admin_config_get_titles($config_name, $cfg_params);
					$config_input = cot_selectbox($config_value, $config_name, $cfg_params, $cfg_params_titles, false);
				}
				else
				{
					$config_input = '';
				}
			}
			elseif ($config_type == COT_CONFIG_TYPE_HIDDEN)
			{
				continue;
			}
			elseif ($config_type == COT_CONFIG_TYPE_SEPARATOR)
			{
				if ($inside_fieldset)
				{
					// Close previous fieldset
					$t->parse('MAIN.EDIT.ADMIN_CONFIG_FIELDSET_END');
				}
				$inside_fieldset = true;
			}
			elseif ($config_type == COT_CONFIG_TYPE_RANGE)
			{
				$range_params = preg_split('#\s*,\s*#', $row['config_variants']);
				$cfg_params = count($range_params) == 3 ? range($range_params[0], $range_params[1], $range_params[2])
					: range($range_params[0], $range_params[1]);
				$config_input = cot_selectbox($config_value, $config_name, $cfg_params, $cfg_params, false);
			}
			elseif ($config_type == COT_CONFIG_TYPE_CUSTOM)
			{
				// Preload module/plugin functions
				if (file_exists(cot_incfile($config_cat, $config_owner)))
				{
					require_once cot_incfile($config_cat, $config_owner);
				}
				if ((preg_match('#^(\w+)\((.*?)\)$#', $row['config_variants'], $mt) && function_exists($mt[1])))
				{
					$callback_params = preg_split('#\s*,\s*#', $mt[2]);
					if (count($callback_params) > 0 && !empty($callback_params[0]))
					{
						for ($i = 0; $i < count($callback_params); $i++)
						{
						$callback_params[$i] = str_replace("'", '', $callback_params[$i]);
						$callback_params[$i] = str_replace('"', '', $callback_params[$i]);
						}
						$config_input = call_user_func_array($mt[1], array_merge(array($config_name, $config_value)),$callback_params);
					}
						else
						{
						$config_input = call_user_func_array($mt[1], array($config_name, $config_value));
						}
				}
				else
				{
					$config_input = '';
				}
			}
			else
			{
				$config_input = cot_textarea($config_name, $config_value, 8, 56);
			}

			if ($config_type == COT_CONFIG_TYPE_SEPARATOR)
			{
				$cfg_title = is_array($L['cfg_' . $row['config_name']]) ? $L['cfg_' . $row['config_name']][0] : $L['cfg_' . $row['config_name']];
				$t->assign('ADMIN_CONFIG_FIELDSET_TITLE', $cfg_title);
				$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW.ADMIN_CONFIG_FIELDSET_BEGIN');
			}
			else
			{
				$t->assign(array(
					'ADMIN_CONFIG_ROW_CONFIG' => $config_input,
					'ADMIN_CONFIG_ROW_CONFIG_TITLE' => (empty($L['cfg_'.$row['config_name']][0]) && !empty($config_text))
						? $config_text : $config_title,
					'ADMIN_CONFIG_ROW_CONFIG_MORE_URL' =>
						cot_url('admin', "m=config&n=edit&o=$o&p=$p&a=reset&v=$config_name&sub=$sub"),
					'ADMIN_CONFIG_ROW_CONFIG_MORE' => $config_more
				));
				/* === Hook - Part2 : Include === */
				foreach ($extp as $pl)
				{
					include $pl;
				}
				/* ===== */
				$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW.ADMIN_CONFIG_ROW_OPTION');
			}
			$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW');

			$prev_subcat = $config_subcat;
		}

		if ($inside_fieldset)
		{
			// Close the last fieldset
			$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW.ADMIN_CONFIG_FIELDSET_END');
			$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW');
		}

		$t->assign(array(
			'ADMIN_CONFIG_FORM_URL' => cot_url('admin', 'm=config&n=edit&o='.$o.'&p='.$p.'&sub='.$sub.'&a=update')
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
		$adminpath[] = array(cot_url('admin', 'm=config'), $L['Configuration']);
		$sql = $db->query("
			SELECT DISTINCT(config_cat) FROM $db_config
			WHERE config_owner='core'
			AND config_type != '".COT_CONFIG_TYPE_HIDDEN."'
			ORDER BY config_cat ASC
		");
		$jj = 0;
		while ($row = $sql->fetch())
		{
			$jj++;
			if($L['core_'.$row['config_cat']])
			{
				$icofile = $cfg['system_dir'] . '/admin/img/cfg_' . $row['config_cat'] . '.png';
				$t->assign(array(
					'ADMIN_CONFIG_ROW_URL' => cot_url('admin', 'm=config&n=edit&o=core&p='.$row['config_cat']),
					'ADMIN_CONFIG_ROW_ICO' => (file_exists($icofile)) ? $icofile : '',
					'ADMIN_CONFIG_ROW_NAME' => $L['core_'.$row['config_cat']],
					'ADMIN_CONFIG_ROW_NUM' => $jj,
					'ADMIN_CONFIG_ROW_ODDEVEN' => cot_build_oddeven($jj)
				));
				$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL.ADMIN_CONFIG_ROW');
			}
		}
		$sql->closeCursor();
		$t->assign('ADMIN_CONFIG_COL_CAPTION', $L['Core']);
		$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL');
		$sql = $db->query("
			SELECT DISTINCT(config_cat) FROM $db_config
			WHERE config_owner = 'module'
			AND config_type != '".COT_CONFIG_TYPE_HIDDEN."'
			ORDER BY config_cat ASC
		");
		$jj = 0;
		while ($row = $sql->fetch())
		{
			$jj++;
			$ext_info = cot_get_extensionparams($row['config_cat'], true);
			$t->assign(array(
				'ADMIN_CONFIG_ROW_URL' => cot_url('admin', 'm=config&n=edit&o=module&p='.$row['config_cat']),
				'ADMIN_CONFIG_ROW_ICO' => $ext_info['icon'],
				'ADMIN_CONFIG_ROW_NAME' => $ext_info['name'],
				'ADMIN_CONFIG_ROW_NUM' => $jj,
				'ADMIN_CONFIG_ROW_ODDEVEN' => cot_build_oddeven($jj)
			));
			$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL.ADMIN_CONFIG_ROW');
		}
		$sql->closeCursor();
		$t->assign('ADMIN_CONFIG_COL_CAPTION', $L['Modules']);
		$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL');
		$sql = $db->query("
			SELECT DISTINCT(c.config_cat), r.ct_title FROM $db_config AS c
				LEFT JOIN $db_core AS r ON c.config_cat = r.ct_code
			WHERE config_owner = 'plug'
			AND config_type != '".COT_CONFIG_TYPE_HIDDEN."'
			ORDER BY config_cat ASC
		");
		$jj = 0;
		while ($row = $sql->fetch())
		{
			$jj++;
			$ext_info = cot_get_extensionparams($row['config_cat'], false);
			$t->assign(array(
				'ADMIN_CONFIG_ROW_URL' => cot_url('admin', 'm=config&n=edit&o=plug&p='.$row['config_cat']),
				'ADMIN_CONFIG_ROW_ICO' => $ext_info['icon'],
				'ADMIN_CONFIG_ROW_NAME' => $ext_info['name'],
				'ADMIN_CONFIG_ROW_NUM' => $jj,
				'ADMIN_CONFIG_ROW_ODDEVEN' => cot_build_oddeven($jj)
			));
			$t->parse('MAIN.DEFAULT.ADMIN_CONFIG_COL.ADMIN_CONFIG_ROW');
		}
		$sql->closeCursor();
		$t->assign('ADMIN_CONFIG_COL_CAPTION', $L['Plugins']);
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
$adminmain = $t->text('MAIN');

/**
 * Helper function that generates selection titles.
 * @param  string $config_name Current config name
 * @param  array  $cfg_params  Array of config params
 * @return array               Selection titles
 */
function cot_admin_config_get_titles($config_name, $cfg_params)
{
	global $L;
	if (isset($L['cfg_'.$config_name.'_params'])
		&& is_array($L['cfg_'.$config_name.'_params']))
	{
		$lang_params_keys = array_keys($L['cfg_'.$config_name.'_params']);
		if (is_numeric($lang_params_keys[0]))
		{
			// Numeric array, simply use it
			$cfg_params_titles = $L['cfg_'.$config_name.'_params'];
		}
		else
		{
			// Associative, match entries
			$cfg_params_titles = array();
			foreach ($cfg_params as $val)
			{
				if (isset($L['cfg_'.$config_name.'_params'][$val]))
				{
					$cfg_params_titles[] = $L['cfg_'.$config_name.'_params'][$val];
				}
				else
				{
					$cfg_params_titles[] = $val;
				}
			}
		}
	}
	else
	{
		$cfg_params_titles = $cfg_params;
	}
	return $cfg_params_titles;
}

?>