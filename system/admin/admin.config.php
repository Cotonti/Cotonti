<?php

/**
 * Administration panel - Configuration
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('configuration');

$adminsubtitle = $L['Configuration'];

$t = new XTemplate(cot_tplfile('admin.config', 'core'));

/* === Hook === */
foreach (cot_getextplugins('admin.config.first') as $pl)
{
	include $pl;
}
/* ===== */

switch ($n)
{
	case 'edit':
		$o = cot_import('o', 'G', 'ALP');
		$p = cot_import('p', 'G', 'ALP');
		$v = cot_import('v', 'G', 'ALP');
		$o = empty($o) ? 'core' : $o;
		$p = empty($p) ? 'global' : $p;

		$optionslist = cot_config_list($o, $p, '');
		cot_die(!sizeof($optionslist), true);

		if ($o != 'core' && file_exists(cot_langfile($p, $o)))
		{
			require cot_langfile($p, $o);
		}
		if ($o != 'core' && file_exists(cot_incfile($p, $o)))
		{
			require_once cot_incfile($p, $o);
		}

		/* === Hook  === */
		foreach (cot_getextplugins('admin.config.edit.first') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($a == 'update' && !empty($_POST))
		{
			$updated = cot_config_update_options($p, $optionslist, $o);
			$errors = cot_get_messages('', 'error');

			if ($o == 'module' || $o == 'plug')
			{
				$dir = $o == 'module' ? $cfg['modules_dir'] : $cfg['plugins_dir'];
				// Run configure extension part if present
				if (file_exists($dir . "/" . $p . "/setup/" . $p . ".configure.php"))
				{
					include $dir . "/" . $p . "/setup/" . $p . ".configure.php";
				}
			}
			/* === Hook  === */
			foreach (cot_getextplugins('admin.config.edit.update.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			$cache && $cache->clear();

			if ($updated)
			{
				$errors ? cot_message('adm_partially_updated', 'warning') : cot_message('Updated');
			}
			else
			{
				if (!$errors) cot_message('adm_already_updated');
			}
		}
		elseif ($a == 'reset' && !empty($v))
		{
			cot_config_reset($p, $v, $o, '');
			$optionslist = cot_config_list($o, $p, '');

			/* === Hook  === */
			foreach (cot_getextplugins('admin.config.edit.reset.done') as $pl)
			{
				include $pl;
			}
			/* ===== */
			$cache && $cache->clear();

			cot_redirect(cot_url('admin', array('m'=>'config', 'n'=>'edit', 'o'=>$o, 'p'=>$p), '', true));
		}


		if ($o == 'core')
		{
			$adminpath[] = array(cot_url('admin', 'm=config'), $L['Configuration']);
			$adminpath[] = array(cot_url('admin', 'm=config&n=edit&o=' . $o . '&p=' . $p), $L['core_' . $p]);
		}
		else
		{
			$adminpath[] = array(cot_url('admin', 'm=extensions'), $L['Extensions']);
			$plmod = $o == 'module' ? 'mod' : 'pl';
			$ext_info = cot_get_extensionparams($p, $o == 'module');
			$adminpath[] = array(cot_url('admin', "m=extensions&a=details&$plmod=$p"), $ext_info['name']);
			$adminpath[] = array(cot_url('admin', 'm=config&n=edit&o=' . $o . '&p=' . $p), $L['Configuration']);
		}

		/* === Hook  === */
		foreach (cot_getextplugins('admin.config.edit.main') as $pl)
		{
			include $pl;
		}
		/* ===== */

		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('admin.config.edit.loop');
		/* ===== */

		foreach ($optionslist as $key => $row)
		{
			list($title, $hint) = cot_config_titles($row['config_name'], $row['config_text']);

			if ($row['config_subcat'] == '__default' && $prev_subcat == '' && $row['config_type'] != COT_CONFIG_TYPE_SEPARATOR)
			{
				$t->assign('ADMIN_CONFIG_FIELDSET_TITLE', $L['cfg_struct_defaults']);
				$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW.ADMIN_CONFIG_FIELDSET_BEGIN');
			}
			if ($row['config_type'] == COT_CONFIG_TYPE_SEPARATOR)
			{
				$t->assign('ADMIN_CONFIG_FIELDSET_TITLE', $title);
				$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW.ADMIN_CONFIG_FIELDSET_BEGIN');
			}
			else
			{
				$t->assign(array(
					'ADMIN_CONFIG_ROW_CONFIG' => cot_config_input($row),
					'ADMIN_CONFIG_ROW_CONFIG_TITLE' => $title,
					'ADMIN_CONFIG_ROW_CONFIG_MORE_URL' =>
					cot_url('admin', "m=config&n=edit&o=$o&p=$p&a=reset&v=" . $row['config_name']),
					'ADMIN_CONFIG_ROW_CONFIG_MORE' => $hint
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
			$prev_subcat = $row['config_subcat'];
		}

		$t->assign(array(
			'ADMIN_CONFIG_FORM_URL' => cot_url('admin', 'm=config&n=edit&o=' . $o . '&p=' . $p . '&a=update')
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
			AND config_type != '" . COT_CONFIG_TYPE_HIDDEN . "'
			ORDER BY config_cat ASC
		");
		$jj = 0;
		while ($row = $sql->fetch())
		{
			$jj++;
			if ($L['core_' . $row['config_cat']])
			{
				$icofile = $cfg['system_dir'] . '/admin/img/cfg_' . $row['config_cat'] . '.png';
				$t->assign(array(
					'ADMIN_CONFIG_ROW_URL' => cot_url('admin', 'm=config&n=edit&o=core&p=' . $row['config_cat']),
					'ADMIN_CONFIG_ROW_ICO' => (file_exists($icofile)) ? $icofile : '',
					'ADMIN_CONFIG_ROW_NAME' => $L['core_' . $row['config_cat']],
					'ADMIN_CONFIG_ROW_DESC' => $L['core_' . $row['config_cat'] . '_desc'],
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
			AND config_type != '" . COT_CONFIG_TYPE_HIDDEN . "'
			ORDER BY config_cat ASC
		");
		$jj = 0;
		while ($row = $sql->fetch())
		{
			$jj++;
			$ext_info = cot_get_extensionparams($row['config_cat'], true);
			$t->assign(array(
				'ADMIN_CONFIG_ROW_URL' => cot_url('admin', 'm=config&n=edit&o=module&p=' . $row['config_cat']),
				'ADMIN_CONFIG_ROW_ICO' => $ext_info['icon'],
				'ADMIN_CONFIG_ROW_NAME' => $ext_info['name'],
				'ADMIN_CONFIG_ROW_DESC' => $ext_info['desc'],
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
			AND config_type != '" . COT_CONFIG_TYPE_HIDDEN . "'
			ORDER BY config_cat ASC
		");
		$jj = 0;
		while ($row = $sql->fetch())
		{
			$jj++;
			$ext_info = cot_get_extensionparams($row['config_cat'], false);
			$t->assign(array(
				'ADMIN_CONFIG_ROW_URL' => cot_url('admin', 'm=config&n=edit&o=plug&p=' . $row['config_cat']),
				'ADMIN_CONFIG_ROW_ICO' => $ext_info['icon'],
				'ADMIN_CONFIG_ROW_NAME' => $ext_info['name'],
				'ADMIN_CONFIG_ROW_DESC' => $ext_info['desc'],
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
